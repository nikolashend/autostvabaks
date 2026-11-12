<?php

namespace Tests\Feature;

use App\Models\Page;
use App\Models\Testimonial;
use Database\Seeders\PageSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\URL;
use Livewire\Livewire;
use Tests\TestCase;

/**
 * Stage 4 – production release gates: production hero asset, legal operator facts,
 * seeder-owned drafts, release-check contradictions.
 */
class Stage4ReleaseTest extends TestCase
{
    use RefreshDatabase;

    private const PROD = 'https://autostvabaks.ee';

    protected function setUp(): void
    {
        parent::setUp();
        Livewire::flushState();
        $this->seed();
    }

    public function test_hero_uses_the_production_asset_set_and_never_the_template_files(): void
    {
        foreach (config('autostvabaks.release.hero_files') as $file) {
            $this->assertFileExists(public_path($file), $file);
        }
        foreach (config('autostvabaks.release.temporary_hero_files') as $file) {
            $this->assertFileDoesNotExist(public_path($file), $file);
        }
        [$w, $h] = getimagesize(public_path('assets/imgs/hero/hero-coast.jpg'));
        $this->assertSame([1920, 1080], [$w, $h]);
        [$w, $h] = getimagesize(public_path('assets/imgs/hero/hero-coast-mobile.jpg'));
        $this->assertSame([800, 450], [$w, $h]);

        foreach (['/', '/ru', '/en'] as $path) {
            $html = $this->get($path)->assertOk()->getContent();
            $this->assertStringContainsString('type="image/avif" srcset="'.url('assets/imgs/hero/hero-coast.avif'), $html);
            $this->assertStringContainsString('width="1920" height="1080"', $html);
            $this->assertStringContainsString('hero-coast-mobile.webp', $html);
            foreach (config('autostvabaks.release.temporary_hero_files') as $file) {
                $this->assertStringNotContainsString($file, $html);
            }
            // social / schema image stays the brand composition
            $this->assertStringNotContainsString('og:image" content="'.url('assets/imgs/hero/'), $html);
        }
    }

    public function test_legal_operator_facts_appear_in_footer_schema_and_legal_pages(): void
    {
        $this->assertSame('17341567', config('autostvabaks.facts.registry_code'));
        $this->assertSame('EE102908916', config('autostvabaks.facts.vat_number'));

        foreach (['/' => 'registrikood 17341567', '/ru' => 'рег. код 17341567', '/en' => 'registry code 17341567'] as $path => $line) {
            $html = $this->get($path)->assertOk()->getContent();
            $this->assertStringContainsString('Motion Motors OÜ ('.$line.')', $html);
            $this->assertStringContainsString('"vatID":"EE102908916"', $html);
            $this->assertStringContainsString('"propertyID":"Registrikood","value":"17341567"', $html);
        }

        foreach ([
            ['/kasutustingimused', 'KMKR nr EE102908916'],
            ['/privaatsuspoliitika', 'registrikood 17341567'],
            ['/ru/usloviya-ispolzovaniya', 'регистрационный код 17341567'],
            ['/en/privacy-policy', 'VAT no. EE102908916'],
        ] as [$path, $needle]) {
            $html = $this->get($path)->assertOk()->getContent();
            $this->assertStringContainsString('Motion Motors OÜ', $html);
            $this->assertStringContainsString($needle, $html);
            $this->assertStringContainsString('Meistri 20, Tallinn 13517', $html);
        }
    }

    public function test_page_seeder_refreshes_drafts_but_never_overwrites_approved_or_edited_texts(): void
    {
        $terms = Page::byKey('terms');
        $this->assertStringContainsString('Mustand', $terms->t('content', 'et'));

        // an admin edit that removed the marker is final: the seeder leaves it alone
        $terms->update(['content' => ['et' => '<p>Kinnitatud tekst.</p>', 'ru' => '<p>Утверждённый текст.</p>', 'en' => '<p>Approved text.</p>']]);
        (new PageSeeder)->run();
        $this->assertSame('<p>Kinnitatud tekst.</p>', Page::byKey('terms')->fresh()->t('content', 'et'));

        // a still-draft page is refreshed from the seeder (new facts flow in)
        $privacy = Page::byKey('privacy');
        $privacy->update(['content' => ['et' => '<p>Mustand vana</p>', 'ru' => '<p>Черновик старый</p>', 'en' => '<p>Draft old</p>']]);
        (new PageSeeder)->run();
        $this->assertStringContainsString('registrikood 17341567', Page::byKey('privacy')->fresh()->t('content', 'et'));
        $this->assertStringContainsString('Mustand', Page::byKey('privacy')->fresh()->t('content', 'et'));

        // approval: the seeder publishes the same texts without the marker
        config(['autostvabaks.release.legal_content_status' => 'approved']);
        (new PageSeeder)->run();
        foreach (['et', 'ru', 'en'] as $locale) {
            $text = strip_tags(Page::byKey('privacy')->fresh()->t('content', $locale));
            foreach (config('autostvabaks.draft_markers') as $marker) {
                $this->assertStringNotContainsString($marker, $text, $locale);
            }
        }
        $this->assertStringContainsString('EE102908916', Page::byKey('privacy')->fresh()->t('content', 'en'));
        // the approved terms edited by the admin were still not touched
        $this->assertSame('<p>Kinnitatud tekst.</p>', Page::byKey('terms')->fresh()->t('content', 'et'));
    }

    public function test_strict_release_check_passes_only_with_real_hero_and_approved_legal_texts(): void
    {
        config([
            'app.url' => self::PROD, 'app.env' => 'production', 'app.debug' => false,
            'autostvabaks.noindex' => false, 'autostvabaks.staging_auth' => '',
            'autostvabaks.release.hero_asset_status' => 'approved',
            'autostvabaks.release.legal_content_status' => 'approved',
        ]);
        URL::forceRootUrl(self::PROD);
        URL::forceScheme('https');
        Testimonial::query()->update(['is_active' => false]);
        (new PageSeeder)->run(); // approved → texts without the draft marker

        $this->artisan('site:release-check --strict')->expectsOutputToContain('Release check passed')->assertExitCode(0);

        // a temporary hero file reappearing on disk contradicts "approved"
        config(['autostvabaks.release.temporary_hero_files' => ['assets/imgs/brand/logo.svg']]);
        $this->artisan('site:release-check --strict')->expectsOutputToContain('still exists in public/')->assertExitCode(1);
        config(['autostvabaks.release.temporary_hero_files' => []]);

        // a legal page that lost the operator name
        Page::byKey('privacy')->update(['content' => ['et' => '<p>Tekst.</p>', 'ru' => '<p>Текст.</p>', 'en' => '<p>Text.</p>']]);
        $this->artisan('site:release-check --strict')->expectsOutputToContain('does not name the legal operator')->assertExitCode(1);

        // a legal page unlinked from the home page (inactive) is caught as well
        Page::byKey('privacy')->update(['is_active' => false]);
        $this->artisan('site:release-check --strict')->expectsOutputToContain('is missing or inactive')->assertExitCode(1);
    }
}
