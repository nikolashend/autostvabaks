<?php

namespace Tests\Feature;

use Database\Seeders\DatabaseSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * Stage 2 no-regression checks: approved brand assets, no legacy lime branding,
 * template debt removed from the public pages, new conversion components in every locale.
 */
class BrandFrontendTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(DatabaseSeeder::class);
    }

    public function test_approved_logo_and_favicon_are_served(): void
    {
        $html = $this->get('/')->assertOk()->getContent();

        $this->assertStringContainsString('assets/imgs/brand/logo-white.svg', $html, 'header/footer use the approved white wordmark');
        $this->assertStringContainsString('assets/imgs/brand/favicon.svg', $html);
        $this->assertStringNotContainsString('asv-logo__text', $html, 'text placeholder logo must be gone');

        foreach (['logo.svg', 'logo-white.svg', 'favicon.svg'] as $file) {
            $svg = file_get_contents(public_path('assets/imgs/brand/'.$file));
            $this->assertStringContainsString('#e6007e', strtolower($svg), "{$file} carries the approved magenta");
        }

        // inner pages (white header) use the graphite wordmark
        $this->get('/kasutustingimused')->assertOk()->assertSee('assets/imgs/brand/logo.svg', false);
    }

    public function test_public_frontend_has_no_lime_branding_or_template_css(): void
    {
        $css = strtolower(file_get_contents(public_path('assets/css/autostvabaks.css')));
        $this->assertStringNotContainsString('#70f46d', $css, 'prototype lime must not remain in the design system');
        $this->assertStringContainsString('--asv-brand-primary: #e6007e', $css);
        $this->assertStringContainsString('--asv-brand-dark: #141414', $css);

        $this->assertSame('#E6007E', config('autostvabaks.brand.primary'));
        $this->assertSame('APPLIED', config('autostvabaks.brand.assets_status'));

        $html = $this->get('/')->assertOk()->getContent();
        $this->assertStringNotContainsString('theme.css', $html, 'Carento theme.css is no longer loaded');
        $this->assertStringNotContainsString('fonts.googleapis.com', $html, 'fonts are self-hosted');
        $this->assertStringContainsString('assets/fonts/urbanist-latin.woff2', $html);
        $this->assertStringContainsString('content="#141414"', $html, 'theme-color follows the brand ink token');
    }

    public function test_conversion_components_render_in_every_locale(): void
    {
        $expected = [
            '/' => ['Mis mõjutab pakkumist?', 'Kas ostate ka liisingus või laenuga auto?', 'Valmis oma auto müüma?', 'Samm 1 / 2'],
            '/ru' => ['Что влияет на предложение?', 'Покупаете ли вы автомобиль в лизинге или кредите?', 'Готовы продать автомобиль?', 'Шаг 1 из 2'],
            '/en' => ['What affects the offer?', 'Do you buy a car that is leased or financed?', 'Ready to sell your car?', 'Step 1 of 2'],
        ];

        foreach ($expected as $path => $strings) {
            $html = $this->get($path)->assertOk()->getContent();
            foreach ($strings as $string) {
                $this->assertStringContainsString($string, $html, "{$string} missing on {$path}");
            }

            // structure every locale must share
            $this->assertStringContainsString('id="hindamine"', $html);
            $this->assertStringContainsString('id="vastused"', $html);
            $this->assertStringContainsString('class="asv-hero__facts"', $html);
            $this->assertStringContainsString('data-sticky-cta', $html, 'mobile sticky CTA present');
            $this->assertStringContainsString('data-lead-success', $html);
            $this->assertSame(1, substr_count($html, '<h1'), "exactly one H1 on {$path}");
            $this->assertStringNotContainsString('id="tagasiside"', $html, 'no review section without real reviews');
        }
    }

    public function test_copy_contains_no_prohibited_claims(): void
    {
        $prohibited = ['parim hind', 'parima hinna', 'лучшая цена', 'лучшую цену', 'best price', 'highest price',
            'garanteeritud', 'гарантируем', 'guaranteed', 'kiireim', 'самый быстрый', 'fastest', '#1', 'tasuta hindamine'];

        foreach (['/', '/ru', '/en'] as $path) {
            $html = mb_strtolower(strip_tags($this->get($path)->assertOk()->getContent()));
            foreach ($prohibited as $claim) {
                $this->assertStringNotContainsString(mb_strtolower($claim), $html, "prohibited claim \"{$claim}\" on {$path}");
            }
        }
    }
}
