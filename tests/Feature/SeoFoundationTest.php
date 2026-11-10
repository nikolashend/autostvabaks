<?php

namespace Tests\Feature;

use App\Models\Lead;
use App\Models\Page;
use App\Models\Setting;
use App\Models\Testimonial;
use Database\Seeders\DatabaseSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\URL;
use Tests\TestCase;

/**
 * Stage 1 no-regression checks: environment-aware SEO output, entity model,
 * indexation safety, contact conditionality, placeholder guard, headers.
 */
class SeoFoundationTest extends TestCase
{
    use RefreshDatabase;

    private const PROD = 'https://autostvabaks.ee';

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(DatabaseSeeder::class);
    }

    /** Production-like configuration without deploying anything. */
    private function simulateProduction(): void
    {
        config([
            'app.url' => self::PROD,
            'app.env' => 'production',
            'app.debug' => false,
            'autostvabaks.noindex' => false,
            'autostvabaks.staging_auth' => '',
        ]);
        URL::forceRootUrl(self::PROD);
        URL::forceScheme('https');
    }

    private function extractJsonLd(string $html): array
    {
        preg_match('~<script type="application/ld\+json">(.*?)</script>~s', $html, $m);
        $this->assertNotEmpty($m, 'JSON-LD block missing');
        $data = json_decode($m[1], true, 512, JSON_THROW_ON_ERROR);
        $this->assertSame('https://schema.org', $data['@context']);

        return collect($data['@graph'])->keyBy('@type')->all();
    }

    public function test_production_simulation_emits_only_the_production_host_and_no_noindex(): void
    {
        $this->simulateProduction();

        foreach (['/' => 'et', '/ru' => 'ru', '/en' => 'en'] as $path => $locale) {
            $response = $this->get(self::PROD.$path)->assertOk();
            $html = $response->getContent();

            $this->assertStringNotContainsString('autostvabaks.adme.ee', $html, "staging host leaked on {$path}");
            $this->assertStringNotContainsString('noindex', $html, "noindex leaked on {$path}");
            $response->assertHeaderMissing('X-Robots-Tag');

            $this->assertStringContainsString('<link rel="canonical" href="'.self::PROD.($locale === 'et' ? '' : '/'.$locale).'">', $html);
            $this->assertStringContainsString('<meta property="og:url" content="'.self::PROD.($locale === 'et' ? '' : '/'.$locale).'">', $html);
            $this->assertStringContainsString('<link rel="alternate" hreflang="x-default" href="'.self::PROD.'">', $html);
            foreach (['et' => '', 'ru' => '/ru', 'en' => '/en'] as $code => $suffix) {
                $this->assertStringContainsString('<link rel="alternate" hreflang="'.$code.'" href="'.self::PROD.$suffix.'">', $html);
            }
        }

        $this->get(self::PROD.'/robots.txt')->assertOk()->assertSee('Disallow: /admin')->assertSee('Sitemap: '.self::PROD.'/sitemap.xml', false)->assertDontSee('Disallow: /'."\n", false);
        $this->get(self::PROD.'/sitemap.xml')->assertOk()->assertSee(self::PROD.'/ru', false)->assertDontSee('adme.ee');
    }

    public function test_hreflang_is_reciprocal_for_home_and_legal_pages(): void
    {
        $page = Page::byKey('privacy');
        $groups = [
            ['/' , '/ru', '/en'],
            ['/'.$page->slug_et, '/ru/'.$page->slug_ru, '/en/'.$page->slug_en],
        ];

        foreach ($groups as $urls) {
            $expected = [];
            foreach (['et', 'ru', 'en'] as $i => $code) {
                $expected[$code] = url($urls[$i]);
            }
            foreach ($urls as $i => $path) {
                $html = $this->get($path)->assertOk()->getContent();
                $self = $expected[['et', 'ru', 'en'][$i]];
                $this->assertStringContainsString('<link rel="canonical" href="'.$self.'">', $html, "self-canonical on {$path}");
                foreach ($expected as $code => $href) {
                    $this->assertStringContainsString('<link rel="alternate" hreflang="'.$code.'" href="'.$href.'">', $html, "{$code} alternate on {$path}");
                }
                $this->assertStringContainsString('<link rel="alternate" hreflang="x-default" href="'.$expected['et'].'">', $html);
            }
        }
    }

    public function test_structured_data_describes_the_real_entities_without_invented_facts(): void
    {
        $html = $this->get('/')->assertOk()->getContent();
        $graph = $this->extractJsonLd($html);

        $this->assertArrayHasKey('Organization', $graph);
        $this->assertArrayHasKey('Brand', $graph);
        $this->assertArrayHasKey('Service', $graph);
        $this->assertArrayHasKey('WebSite', $graph);
        $this->assertArrayHasKey('WebPage', $graph);
        $this->assertArrayHasKey('FAQPage', $graph);
        $this->assertArrayNotHasKey('AutoDealer', $graph);
        $this->assertArrayNotHasKey('LocalBusiness', $graph);

        $this->assertSame('Motion Motors OÜ', $graph['Organization']['name']);
        $this->assertSame('Autostvabaks', $graph['Brand']['name']);
        $this->assertSame('Meistri 20', $graph['Organization']['address']['streetAddress']);
        $this->assertSame('Estonia', $graph['Service']['areaServed']['name']);
        $this->assertSame($graph['Organization']['@id'], $graph['Service']['provider']['@id']);

        // Phone is PENDING: no telephone anywhere, no ratings, no opening hours.
        $this->assertArrayNotHasKey('telephone', $graph['Organization']);
        $this->assertStringNotContainsString('aggregateRating', $html);
        $this->assertStringNotContainsString('openingHours', $html);

        // Visible FAQ and FAQ schema share one source.
        $this->assertCount(\App\Models\Faq::active()->count(), $graph['FAQPage']['mainEntity']);
    }

    public function test_phone_appears_in_schema_and_contacts_only_once_configured(): void
    {
        $html = $this->get('/')->assertOk()->getContent();
        $this->assertStringNotContainsString('href="tel:', $html);
        $this->assertStringNotContainsString('helista', $html, 'calling promised without a phone');
        $this->assertStringNotContainsString('Saada päring või kirjuta meile', '', 'sanity');
        $this->assertStringContainsString('Saada päring või kirjuta meile', $html);

        Setting::putMany(['phone' => '+372 5555 0000']);

        $html = $this->get('/')->assertOk()->getContent();
        $this->assertStringContainsString('href="tel:+37255550000"', $html);
        $this->assertStringContainsString('Kirjuta või helista', $html);
        $this->assertSame('+372 5555 0000', $this->extractJsonLd($html)['Organization']['telephone']);
    }

    public function test_placeholder_reviews_never_render_outside_local_even_if_activated(): void
    {
        Testimonial::query()->update(['is_active' => true]);
        $this->assertTrue(Testimonial::first()->isPlaceholder());

        $html = $this->get('/')->assertOk()->getContent();
        $this->assertStringNotContainsString('Näidistagasiside', $html);
        $this->assertStringNotContainsString('Kliendi nimi', $html);
        $this->assertStringNotContainsString('id="tagasiside"', $html, 'review section rendered without real reviews');
        $this->assertStringNotContainsString('swiper-bundle', $html, 'Swiper loaded for an empty carousel');

        Testimonial::create([
            'author' => 'Mari M.',
            'location' => ['et' => 'Tartu'],
            'title' => ['et' => 'Kiire ja mugav'],
            'text' => ['et' => 'Auto müüdud kahe päevaga.'],
            'rating' => 5,
            'sort' => 1,
            'is_active' => true,
        ]);

        $html = $this->get('/')->assertOk()->getContent();
        $this->assertStringContainsString('Mari M.', $html);
        $this->assertStringNotContainsString('Kliendi nimi', $html);
        $this->assertStringContainsString('asv-reviews--1', $html, 'a single review is laid out as a grid');
        $this->assertStringNotContainsString('swiper-bundle', $html, 'Swiper must not load for 1–3 reviews');

        // more than three reviews → carousel, and only then the Swiper assets
        foreach (['Jaan K.', 'Anna P.', 'Toomas L.'] as $i => $author) {
            Testimonial::create([
                'author' => $author,
                'text' => ['et' => 'Sujuv tehing.'],
                'rating' => 5,
                'sort' => $i + 2,
                'is_active' => true,
            ]);
        }
        $html = $this->get('/')->assertOk()->getContent();
        $this->assertStringContainsString('asv-reviews--slider', $html);
        $this->assertStringContainsString('swiper-bundle', $html);
    }

    public function test_admin_pages_can_be_published_without_being_indexed(): void
    {
        $page = Page::byKey('terms');
        $page->update(['is_indexable' => false]);

        $this->get('/sitemap.xml')->assertOk()->assertDontSee($page->slug_et)->assertSee(Page::byKey('privacy')->slug_et);
        $this->get('/'.$page->slug_et)->assertOk()->assertSee('<meta name="robots" content="noindex, nofollow">', false);
        $this->get('/'.Page::byKey('privacy')->slug_et)->assertOk()->assertDontSee('noindex');
    }

    public function test_security_and_robots_headers(): void
    {
        $this->get('/')->assertOk()
            ->assertHeader('X-Content-Type-Options', 'nosniff')
            ->assertHeader('Referrer-Policy', 'strict-origin-when-cross-origin')
            ->assertHeader('X-Frame-Options', 'SAMEORIGIN')
            ->assertHeaderMissing('X-Robots-Tag');

        $this->get('/admin/login')->assertOk()->assertHeader('X-Robots-Tag', 'noindex, nofollow');

        config(['autostvabaks.noindex' => true]);
        $this->get('/')->assertOk()->assertHeader('X-Robots-Tag', 'noindex, nofollow');
    }

    public function test_duplicate_submission_is_accepted_once_and_attribution_is_stored(): void
    {
        Mail::fake();
        $payload = [
            'reg_number' => '777XYZ', 'phone' => '+372 5555 7777', 'email' => 'dup@example.com',
            'terms' => '1', 'language' => 'et',
            'utm_source' => 'google', 'utm_medium' => 'cpc', 'utm_campaign' => 'buyback-et', 'gclid' => 'abc123',
        ];

        $this->postJson('/paring', $payload)->assertOk()->assertJson(['ok' => true]);
        $this->postJson('/paring', $payload)->assertOk()->assertJson(['ok' => true]);

        $this->assertSame(1, Lead::count());
        $lead = Lead::sole();
        $this->assertSame('google', $lead->utm_source);
        $this->assertSame('cpc', $lead->utm_medium);
        $this->assertSame('buyback-et', $lead->utm_campaign);
        $this->assertSame('abc123', $lead->gclid);
        $this->assertNull($lead->utm_term);
        Mail::assertSentCount(1);
    }

    public function test_release_check_blocks_a_production_build_with_staging_settings(): void
    {
        $this->simulateProduction();
        Testimonial::query()->update(['is_active' => false]);
        Page::query()->update(['content' => ['et' => '<p>ok</p>', 'ru' => '<p>ok</p>', 'en' => '<p>ok</p>']]);

        $this->artisan('site:release-check')->assertSuccessful();

        config(['autostvabaks.noindex' => true]);
        $this->artisan('site:release-check')
            ->expectsOutputToContain('SITE_NOINDEX=true on the production host')
            ->assertFailed();

        config(['autostvabaks.noindex' => false]);
        Testimonial::query()->update(['is_active' => true]);
        $this->artisan('site:release-check')
            ->expectsOutputToContain('placeholder testimonials')
            ->assertFailed();
    }
}
