<?php

namespace Tests\Feature;

use App\Mail\NewLeadMail;
use App\Models\Faq;
use App\Models\Lead;
use App\Models\Page;
use App\Models\Setting;
use App\Models\Testimonial;
use App\Support\HtmlSanitizer;
use Database\Seeders\DatabaseSeeder;
use Database\Seeders\FaqSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\URL;
use Livewire\Livewire;
use Tests\TestCase;

/**
 * Stage 3: content/SEO hardening, legal operator line, social image, analytics
 * vendor switch, CSP, CMS sanitiser, lead-mail size safety, FAQ content release,
 * crawler policy and the production release gates.
 */
class Stage3HardeningTest extends TestCase
{
    use RefreshDatabase;

    private const PROD = 'https://autostvabaks.ee';

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(DatabaseSeeder::class);
        // Livewire remembers "a component rendered" across tests in one process and would inject its
        // assets into the next public page; the public site never renders Livewire components.
        Livewire::flushState();
    }

    public function test_footer_names_the_legal_operator_in_every_locale(): void
    {
        foreach (['/' => 'Autostvabaks teenuse osutaja: Motion Motors OÜ', '/ru' => 'Услугу Autostvabaks оказывает Motion Motors OÜ', '/en' => 'The Autostvabaks service is provided by Motion Motors OÜ'] as $path => $line) {
            $html = $this->get($path)->assertOk()->getContent();
            $this->assertStringContainsString($line, $html, "operator line on {$path}");
            $this->assertStringContainsString('asv-footer__operator', $html);
        }
    }

    public function test_social_image_is_the_brand_composition_not_the_temporary_hero(): void
    {
        foreach (['/' => 'et', '/ru' => 'ru', '/en' => 'en'] as $path => $locale) {
            $html = $this->get($path)->assertOk()->getContent();
            $this->assertMatchesRegularExpression('~<meta property="og:image" content="[^"]+/assets/imgs/brand/og-'.$locale.'\.jpg">~', $html);
            $this->assertStringContainsString('<meta property="og:image:width" content="1200">', $html);
            $this->assertStringNotContainsString('og:image" content="'.url('assets/imgs/hero/hero.jpg'), $html);
            $this->assertStringContainsString('"primaryImageOfPage":"'.url('assets/imgs/brand/og-'.$locale.'.jpg').'"', $html);
            $this->assertFileExists(public_path('assets/imgs/brand/og-'.$locale.'.jpg'));
        }
    }

    public function test_final_copy_carries_the_intent_cluster_without_prohibited_claims(): void
    {
        $et = $this->get('/')->assertOk()->getContent();
        $this->assertStringContainsString('<title>Autode kokkuost üle Eesti – müü oma auto lihtsalt | Autostvabaks</title>', $et);
        $this->assertStringContainsString('<h1 id="hero-title">Müü oma auto kiirelt ja mugavalt</h1>', $et);
        $this->assertStringContainsString('Autode kokkuost üle Eesti', $et);
        $this->assertStringContainsString('Auto väljaost nelja sammuga', $et);
        $this->assertStringContainsString('asv-valuation__expectation', $et);
        $this->assertStringContainsString('Kas liisingus või laenuga autot saab Autostvabaksile müüa?', $et);
        // the "no obligation" statement stays unpublished until the client confirms it (fact register P8)
        $this->assertStringNotContainsString('ei kohusta', $et);

        $ru = $this->get('/ru')->assertOk()->getContent();
        $this->assertStringContainsString('<title>Выкуп авто по всей Эстонии – продать машину просто | Autostvabaks</title>', $ru);
        $this->assertStringContainsString('Выкуп автомобилей по всей Эстонии', $ru);

        $en = $this->get('/en')->assertOk()->getContent();
        $this->assertStringContainsString('<title>Car buyback across Estonia – sell your car the simple way | Autostvabaks</title>', $en);

        // title ≠ H1, one H1, no snippet restrictions, no duplicate titles across locales
        $titles = [];
        foreach (['/', '/ru', '/en'] as $path) {
            $html = $this->get($path)->getContent();
            preg_match('~<title>(.+?)</title>~', $html, $m);
            $titles[] = $m[1];
            $this->assertSame(1, substr_count($html, '<h1'), "one H1 on {$path}");
            $this->assertDoesNotMatchRegularExpression('/nosnippet|max-snippet|noimageindex/i', $html);
        }
        $this->assertCount(3, array_unique($titles));
    }

    public function test_faq_content_release_is_idempotent_and_updates_legacy_rows_in_place(): void
    {
        $this->assertSame(9, Faq::active()->count());

        // a pre-Stage-3 row (no key, old question) is adopted instead of duplicated
        $legacy = Faq::create(['question' => ['et' => 'Kuidas toimub arveldus?'], 'answer' => ['et' => 'vana'], 'sort' => 999, 'is_active' => true]);
        Faq::where('key', 'payment')->delete();

        (new FaqSeeder)->run();
        (new FaqSeeder)->run();

        $this->assertSame(9, Faq::active()->count());
        $this->assertSame('payment', $legacy->fresh()->key);
        $this->assertStringContainsString('Pangaülekandega', $legacy->fresh()->t('answer', 'et'));

        // schema and accordion share the same records; every answer is self-contained (names the service)
        $html = $this->get('/')->assertOk()->getContent();
        preg_match('~<script type="application/ld\+json">(.+?)</script>~s', $html, $m);
        $graph = collect(json_decode($m[1], true)['@graph'])->keyBy('@type');
        $this->assertCount(9, $graph['FAQPage']['mainEntity']);
        foreach ($graph['FAQPage']['mainEntity'] as $q) {
            $this->assertStringContainsString($q['name'], $html);
        }
    }

    public function test_gtm_is_injected_only_with_a_container_id_on_an_enabled_host(): void
    {
        $this->assertStringNotContainsString('googletagmanager', $this->get('/')->getContent());

        config(['autostvabaks.analytics.gtm_id' => 'gtm-test123']);
        // enabled = null → production host only; the test host is not production
        $this->assertStringNotContainsString('googletagmanager', $this->get('/')->getContent());

        config(['autostvabaks.analytics.enabled' => 'false']);
        $this->assertStringNotContainsString('googletagmanager', $this->get('/')->getContent());

        config(['autostvabaks.analytics.enabled' => 'true']);
        $html = $this->get('/')->assertOk()->getContent();
        $this->assertStringContainsString("'https://www.googletagmanager.com/gtm.js?id='+i+dl", $html);
        $this->assertStringContainsString("'dataLayer','GTM-TEST123')", $html);
        $this->assertStringContainsString('https://www.googletagmanager.com/ns.html?id=GTM-TEST123', $html);
        $this->assertSame(1, substr_count($html, 'gtm.js?id='), 'GTM bootstrap must not be duplicated');
        // the dataLayer exists before any vendor script, for the asvTrack contract
        $this->assertLessThan(strpos($html, 'gtm.js?id='), strpos($html, 'window.dataLayer = window.dataLayer || [];'));

        config(['autostvabaks.analytics.gtm_id' => 'not-a-container']);
        $this->assertStringNotContainsString('googletagmanager', $this->get('/')->getContent());
    }

    public function test_public_pages_get_a_nonce_based_csp_and_the_admin_does_not(): void
    {
        $response = $this->get('/')->assertOk();
        $html = $response->getContent();
        $policy = (string) $response->headers->get('Content-Security-Policy-Report-Only');

        $this->assertNotSame('', $policy);
        $response->assertHeaderMissing('Content-Security-Policy');
        preg_match_all('/<script[^>]*nonce="([^"]+)"/', $html, $m);
        $this->assertNotEmpty($m[1]);
        $this->assertCount(1, array_unique($m[1]), 'one nonce per request');
        $this->assertStringContainsString("'nonce-".$m[1][0]."'", $policy);
        $this->assertStringContainsString("'strict-dynamic'", $policy);
        $this->assertStringContainsString("object-src 'none'", $policy);
        // every executable script tag carries the nonce (JSON-LD does not execute)
        preg_match_all('/<script(?![^>]*type="application\/ld\+json")[^>]*>/', $html, $all);
        foreach ($all[0] as $tag) {
            $this->assertStringContainsString('nonce="', $tag, $tag);
        }

        // admin-authored head scripts receive the nonce too
        Setting::putMany(['head_scripts' => '<script>console.log("x")</script>']);
        $html = $this->get('/')->getContent();
        $this->assertStringContainsString('<script nonce="', $html);
        $this->assertStringNotContainsString('<script>console.log', $html);

        $this->get('/admin/login')->assertOk()->assertHeaderMissing('Content-Security-Policy-Report-Only')->assertHeaderMissing('Content-Security-Policy');

        config(['autostvabaks.csp' => 'enforce']);
        $this->get('/')->assertOk()->assertHeader('Content-Security-Policy')->assertHeaderMissing('Content-Security-Policy-Report-Only');

        config(['autostvabaks.csp' => 'off']);
        $this->get('/')->assertOk()->assertHeaderMissing('Content-Security-Policy')->assertHeaderMissing('Content-Security-Policy-Report-Only');
    }

    public function test_admin_rich_text_is_sanitised_on_save_and_on_render(): void
    {
        $payload = '<h2 onclick="alert(1)">Pealkiri</h2><p>Tekst <a href="javascript:alert(1)">halb</a> <a href="https://example.com/x" target="_blank" onmouseover="x()">hea</a> <strong>b</strong></p>'
            .'<script>alert(1)</script><iframe src="https://evil.example"></iframe><img src=x onerror="alert(1)">'
            .'<ul><li>üks</li><li>kaks</li></ul><div style="color:red">div tekst</div><blockquote>q</blockquote><a href="mailto:info@autostvabaks.ee">mail</a><!-- c -->';

        $page = Page::byKey('terms');
        $page->update(['content' => ['et' => $payload, 'ru' => $payload, 'en' => $payload]]);
        $stored = $page->fresh()->content['et'];

        foreach (['<script', '<iframe', '<img', 'onclick', 'onmouseover', 'onerror', 'javascript:', 'style=', '<div', '<!--'] as $bad) {
            $this->assertStringNotContainsString($bad, $stored, "stored content still contains {$bad}");
        }
        $this->assertStringContainsString('<h2>Pealkiri</h2>', $stored);
        $this->assertStringContainsString('<a>halb</a>', $stored);
        $this->assertStringContainsString('<a href="https://example.com/x" target="_blank" rel="noopener">hea</a>', $stored);
        $this->assertStringContainsString('<ul><li>üks</li><li>kaks</li></ul>', $stored);
        $this->assertStringContainsString('div tekst', $stored);
        $this->assertStringContainsString('<a href="mailto:info@autostvabaks.ee">mail</a>', $stored);
        $this->assertStringContainsString('<strong>b</strong>', $stored);

        // legacy rows written before the guard are cleaned when rendered
        Page::withoutEvents(fn () => $page->forceFill(['content' => ['et' => '<p>ok</p><script>alert(2)</script>']])->save());
        $html = $this->get('/'.$page->slug_et)->assertOk()->getContent();
        $this->assertStringContainsString('<p>ok</p>', $html);
        $this->assertStringNotContainsString('alert(2)', $html);

        $this->assertSame('', HtmlSanitizer::clean(null));
        $this->assertFalse(HtmlSanitizer::safeUrl("java\tscript:alert(1)"));
        $this->assertFalse(HtmlSanitizer::safeUrl('//evil.example'));
        $this->assertTrue(HtmlSanitizer::safeUrl('/kasutustingimused'));
    }

    public function test_lead_e_mail_keeps_within_the_attachment_cap_and_links_to_the_admin(): void
    {
        Mail::fake();
        Storage::fake('local');
        config(['autostvabaks.leads.mail_attachments_max_mb' => 0]); // every photo exceeds the budget

        $this->postJson('/paring', [
            'reg_number' => '999 CAP', 'phone' => '+372 5555 0000', 'email' => 'cap@example.com', 'terms' => '1', 'language' => 'et',
            'photos' => [UploadedFile::fake()->image('a.jpg', 800, 600), UploadedFile::fake()->image('b.jpg', 800, 600)],
        ])->assertOk()->assertJson(['ok' => true]);

        $lead = Lead::sole();
        $this->assertCount(2, $lead->photos);
        $this->assertNotNull($lead->mail_sent_at, 'the e-mail is still sent when photos do not fit');

        Mail::assertSent(NewLeadMail::class, function (NewLeadMail $mail) use ($lead) {
            $attachments = $mail->attachments();
            $html = $mail->render();

            return $attachments === []
                && $mail->skippedPhotos === 2
                && str_contains($html, 'доступны в админ-панели')
                && str_contains($html, url('/admin/leads/'.$lead->id))
                && ! str_contains($html, 'admin/lead-photo/');
        });

        // with a normal budget the small test photos are attached as before
        config(['autostvabaks.leads.mail_attachments_max_mb' => 10]);
        $mail = new NewLeadMail($lead);
        $this->assertCount(2, $mail->attachments());
        $this->assertSame(0, $mail->skippedPhotos);
    }

    public function test_production_robots_allows_oai_searchbot_and_keeps_admin_closed(): void
    {
        config(['app.url' => self::PROD, 'autostvabaks.noindex' => false]);
        URL::forceRootUrl(self::PROD);
        URL::forceScheme('https');

        $robots = $this->get(self::PROD.'/robots.txt')->assertOk()->getContent();
        $this->assertStringContainsString("User-agent: *\nDisallow: /admin", $robots);
        $this->assertStringContainsString("User-agent: OAI-SearchBot\nAllow: /\nDisallow: /admin", $robots);
        $this->assertStringNotContainsString('GPTBot', $robots);
        $this->assertStringNotContainsString("Disallow: /\n", $robots);
        $this->assertStringContainsString('Sitemap: '.self::PROD.'/sitemap.xml', $robots);
    }

    public function test_strict_release_check_blocks_the_temporary_hero_and_unapproved_legal_texts_only(): void
    {
        config([
            'app.url' => self::PROD, 'app.env' => 'production', 'app.debug' => false,
            'autostvabaks.noindex' => false, 'autostvabaks.staging_auth' => '',
            'autostvabaks.release.hero_asset_status' => 'temporary',
            'autostvabaks.release.legal_content_status' => 'draft',
        ]);
        URL::forceRootUrl(self::PROD);
        URL::forceScheme('https');
        Testimonial::query()->update(['is_active' => false]);
        // approved legal texts without draft markers, so only the explicit statuses remain
        foreach (Page::all() as $page) {
            $page->update(['content' => ['et' => '<p>Lõplik tekst. Teenuse osutaja: Motion Motors OÜ.</p>', 'ru' => '<p>Финальный текст. Оператор: Motion Motors OÜ.</p>', 'en' => '<p>Final text. Operator: Motion Motors OÜ.</p>']]);
        }

        $this->artisan('site:release-check --strict')
            ->expectsOutputToContain('HERO_ASSET_STATUS=temporary')
            ->expectsOutputToContain('LEGAL_CONTENT_STATUS=draft')
            ->assertExitCode(1);

        config(['autostvabaks.release.hero_asset_status' => 'approved', 'autostvabaks.release.legal_content_status' => 'approved']);

        $this->artisan('site:release-check --strict')
            ->expectsOutputToContain('Release check passed')
            ->assertExitCode(0);

        // a sitemap entry that is noindex must fail the gate
        Page::byKey('privacy')->update(['is_indexable' => false]);
        $this->artisan('site:release-check --strict')->assertExitCode(0); // not listed → fine
        Page::byKey('privacy')->update(['is_indexable' => true]);
        config(['autostvabaks.prohibited_claims' => ['autostvabaks']]); // provoke the claim scanner
        $this->artisan('site:release-check --strict')->expectsOutputToContain('prohibited claim fragment')->assertExitCode(1);
    }

    public function test_minified_assets_are_served_only_when_fresh(): void
    {
        $css = public_path('assets/css/autostvabaks.css');
        $min = public_path('assets/css/autostvabaks.min.css');
        $this->assertFileExists($min);
        $this->assertGreaterThanOrEqual(filemtime($css), filemtime($min), 'run node scripts/build-assets.cjs after editing the CSS');

        $html = $this->get('/')->assertOk()->getContent();
        $this->assertStringContainsString('/assets/css/autostvabaks.min.css?v=', $html);
        $this->assertStringContainsString('/assets/js/autostvabaks.min.js?v=', $html);

        config(['autostvabaks.minified_assets' => false]);
        $html = $this->get('/')->assertOk()->getContent();
        $this->assertStringContainsString('/assets/css/autostvabaks.css?v=', $html);
        $this->assertStringNotContainsString('autostvabaks.min.css', $html);
    }
}
