<?php

namespace App\Console\Commands;

use App\Models\Page;
use App\Models\Testimonial;
use App\Support\Analytics;
use App\Support\Site;
use Illuminate\Console\Command;
use Illuminate\Http\Request;

/**
 * Production release gate: `php artisan site:release-check`
 *
 * Exit code 1 when the current configuration or content must not go live:
 * staging noindex on the production host, debug mode, placeholder reviews,
 * draft legal pages, sample strings / prohibited claims / staging host on
 * public pages, temporary hero or OG asset, unapproved legal texts, broken
 * sitemap entries. Run it in deploy.sh; on staging the content findings are
 * warnings, on the production host (or with --strict) they block.
 *
 * Human sign-offs are explicit statuses in .env (HERO_ASSET_STATUS,
 * LEGAL_CONTENT_STATUS) – nobody has to remember them at launch time.
 */
class ReleaseCheck extends Command
{
    protected $signature = 'site:release-check {--strict : Treat every warning as a failure (use on the production host)}';

    protected $description = 'Verify that the site is safe to publish (noindex, debug, placeholders, drafts, hosts, assets, legal, sitemap).';

    private array $failures = [];

    private array $warnings = [];

    public function handle(): int
    {
        // the command object is reused when artisan is invoked several times in one process (tests, deploy scripts)
        $this->failures = [];
        $this->warnings = [];

        $production = Site::isProductionHost() || $this->option('strict');
        $appUrl = (string) config('app.url');

        // --- environment ---------------------------------------------------
        if ($production && config('autostvabaks.noindex')) {
            $this->block('SITE_NOINDEX=true on the production host – search engines would be blocked.');
        }
        if ($production && config('app.debug')) {
            $this->block('APP_DEBUG=true on the production host.');
        }
        if ($production && config('app.env') !== 'production') {
            $this->block('APP_ENV is "'.config('app.env').'" on the production host.');
        }
        if ($production && config('autostvabaks.staging_auth')) {
            $this->block('STAGING_AUTH is set – the public site would ask for a password.');
        }
        if (! str_starts_with($appUrl, 'https://')) {
            $this->warn_('APP_URL is not https:// ('.$appUrl.').');
        }
        if (! $production && ! config('autostvabaks.noindex')) {
            $this->warn_('Non-production host without SITE_NOINDEX=true – make sure this host is not reachable by crawlers.');
        }

        // --- human sign-offs (Stage 3 gates) --------------------------------
        $hero = (string) config('autostvabaks.release.hero_asset_status');
        $temporaryHero = config('autostvabaks.release.temporary_hero_files', []);
        if ($hero !== 'approved') {
            $this->report($production, "HERO_ASSET_STATUS={$hero} – the hero visual is not signed off. Replace the asset (ADR-32), delete the temporary files and set HERO_ASSET_STATUS=approved.");
        }
        // "approved" is a statement about the asset, not a switch: it is contradicted by a temporary file on disk or a missing production file.
        foreach ($temporaryHero as $file) {
            if (is_file(public_path($file))) {
                $this->report($production || $hero === 'approved', "Temporary hero file {$file} still exists in public/ – delete it (the gate never trusts HERO_ASSET_STATUS while it is present).");
            }
        }
        foreach (config('autostvabaks.release.hero_files', []) as $file) {
            if (! is_file(public_path($file))) {
                $this->block("Production hero file {$file} is missing.");
            }
        }
        $legal = (string) config('autostvabaks.release.legal_content_status');
        if ($legal !== 'approved') {
            $this->report($production, "LEGAL_CONTENT_STATUS={$legal} – Terms / Privacy texts are not client-approved. After approval run `php artisan db:seed --class=PageSeeder --force` with LEGAL_CONTENT_STATUS=approved (ADR-33).");
        }

        // --- analytics / assets --------------------------------------------
        if (! Analytics::configured()) {
            $this->warn_('No analytics container configured (ANALYTICS_GTM_ID) – conversions will not be measured after launch.');
        } elseif ($production && ! Analytics::enabled()) {
            $this->warn_('ANALYTICS_ENABLED=false – the configured GTM container is not injected on this host.');
        }
        if (config('autostvabaks.minified_assets', true)) {
            foreach (['assets/css/autostvabaks.css', 'assets/js/autostvabaks.js'] as $source) {
                if (! Site::minifiedTwin($source)) {
                    $this->warn_("Minified twin of {$source} is missing or older than the source – run `node scripts/build-assets.cjs`.");
                }
            }
        }

        // --- content ------------------------------------------------------
        $placeholders = Testimonial::active()->get()->filter->isPlaceholder();
        if ($placeholders->isNotEmpty()) {
            $this->report($production, 'Active placeholder testimonials: '.$placeholders->pluck('author')->join(', ').' (they are hidden outside local, but should be deleted or deactivated).');
        }

        $placeholderMarkers = config('autostvabaks.placeholder_markers', []);
        foreach (Page::active()->get() as $page) {
            foreach (array_keys(Site::locales()) as $locale) {
                $text = strip_tags((string) $page->t('content', $locale));
                foreach (config('autostvabaks.draft_markers', []) as $marker) {
                    if (mb_stripos($text, $marker) !== false) {
                        $this->report($production, "Page \"{$page->t('title', $locale)}\" ({$locale}) still contains the draft marker \"{$marker}\".");
                        break;
                    }
                }
                foreach ($placeholderMarkers as $marker) {
                    if (mb_stripos($text, $marker) !== false) {
                        $this->report($production, "Page \"{$page->t('title', $locale)}\" ({$locale}) contains the placeholder \"{$marker}\".");
                        break;
                    }
                }
            }
        }
        $this->checkLegalPages($production);

        // --- rendered public pages ----------------------------------------
        // The staging host must never appear in production output; on staging itself it is expected.
        $markers = config('autostvabaks.placeholder_markers', []);
        if ($production) {
            $markers[] = 'autostvabaks.adme.ee';
        }
        $claims = config('autostvabaks.prohibited_claims', []);
        $temporaryHero = config('autostvabaks.release.temporary_hero_files', []);

        foreach (array_keys(Site::locales()) as $locale) {
            $url = Site::route('home', [], $locale);
            $html = $this->render($url);
            if ($html === null) {
                $this->warn_("Could not render {$url}.");

                continue;
            }
            foreach ($markers as $marker) {
                if (mb_stripos($html, $marker) !== false) {
                    $this->report($production, "{$url} contains \"{$marker}\".");
                }
            }
            $text = mb_strtolower(strip_tags($html));
            foreach ($claims as $claim) {
                if (mb_strpos($text, mb_strtolower($claim)) !== false) {
                    $this->report($production, "{$url} contains the prohibited claim fragment \"{$claim}\" (FACT_CLAIM_REGISTER §4).");
                }
            }
            if ($production && preg_match('/<meta name="robots" content="[^"]*noindex/i', $html)) {
                $this->block("{$url} renders a noindex meta tag.");
            }
            if (preg_match('/nosnippet|max-snippet|max-image-preview:none|noimageindex/i', $html)) {
                $this->report($production, "{$url} carries a snippet / preview restriction – remove it unless deliberate (Google AI / search controls).");
            }
            if (! preg_match('/<link rel="canonical" href="'.preg_quote(rtrim($appUrl, '/'), '/').'/', $html)) {
                $this->report($production, "{$url}: canonical does not start with APP_URL ({$appUrl}).");
            }
            // The temporary template photo must not be the social / schema image of the page.
            if (preg_match('/<meta property="og:image" content="([^"]+)"/', $html, $m)) {
                foreach ($temporaryHero as $file) {
                    if (str_contains($m[1], $file)) {
                        $this->block("{$url}: og:image points at the temporary hero asset ({$file}).");
                    }
                }
            }
            if (preg_match('/"primaryImageOfPage":"([^"]+)"/', $html, $m)) {
                foreach ($temporaryHero as $file) {
                    if (str_contains($m[1], $file)) {
                        $this->block("{$url}: schema primaryImageOfPage points at the temporary hero asset ({$file}).");
                    }
                }
            }
            foreach ($temporaryHero as $file) {
                if (str_contains($html, $file)) {
                    $this->report($production, "{$url} still references the temporary hero asset {$file}.");
                }
            }
        }

        // --- sitemap: every URL must be a live, self-canonical, indexable production route ----
        $this->checkSitemap($production, $appUrl);

        // --- output -------------------------------------------------------
        foreach ($this->warnings as $w) {
            $this->line('<comment>WARN</comment>  '.$w);
        }
        foreach ($this->failures as $f) {
            $this->line('<error>FAIL</error>  '.$f);
        }

        if ($this->failures) {
            $this->error(count($this->failures).' blocking problem(s) – do not publish.');

            return self::FAILURE;
        }

        $this->info('Release check passed'.($this->warnings ? ' with '.count($this->warnings).' warning(s)' : '').'.');

        return self::SUCCESS;
    }

    /**
     * Terms / Privacy: every locale resolves 200, carries the legal operator, is linked
     * from the footer and (terms) from the form consent line – TZ Stage 4 §15.
     */
    private function checkLegalPages(bool $production): void
    {
        $operator = Site::legalEntity();
        foreach (['terms', 'privacy'] as $key) {
            $page = Page::byKey($key);
            if (! $page || ! $page->is_active) {
                $this->report($production, "Legal page \"{$key}\" is missing or inactive.");

                continue;
            }
            foreach (array_keys(Site::locales()) as $locale) {
                $url = $page->url($locale);
                $html = $this->render($url);
                if ($html === null) {
                    $this->report($production, "Legal page {$url} does not return 200.");

                    continue;
                }
                // the footer names the operator on every page – the legal text itself must do so too
                if (! str_contains(strip_tags((string) $page->t('content', $locale)), $operator)) {
                    $this->report($production, "Legal page {$url} does not name the legal operator ({$operator}).");
                }
                $home = $this->render(Site::route('home', [], $locale)) ?? '';
                if (! str_contains($home, 'href="'.$url.'"')) {
                    $this->report($production, "Legal page {$url} is not linked from the {$locale} home page (footer / consent line).");
                }
            }
        }
    }

    private function checkSitemap(bool $production, string $appUrl): void
    {
        $xml = $this->render(route('sitemap'));
        if ($xml === null) {
            $this->report($production, 'sitemap.xml could not be rendered.');

            return;
        }

        preg_match_all('~<loc>([^<]+)</loc>~', $xml, $m);
        $urls = array_values(array_unique(array_map('html_entity_decode', $m[1])));

        if ($urls === []) {
            $this->report($production, 'sitemap.xml lists no URLs.');

            return;
        }

        foreach ($urls as $loc) {
            if (! str_starts_with($loc, rtrim($appUrl, '/'))) {
                $this->report($production, "sitemap: {$loc} is not on APP_URL.");

                continue;
            }
            if (preg_match('~/(admin|livewire|paring|up)(/|$)~', parse_url($loc, PHP_URL_PATH) ?? '')) {
                $this->report($production, "sitemap: {$loc} is a utility route and must not be listed.");

                continue;
            }
            $html = $this->render($loc);
            if ($html === null) {
                $this->report($production, "sitemap: {$loc} does not return 200.");

                continue;
            }
            if (preg_match('/<meta name="robots" content="[^"]*noindex/i', $html) && ! config('autostvabaks.noindex')) {
                $this->report($production, "sitemap: {$loc} is noindex but listed.");
            }
            if (! str_contains($html, '<link rel="canonical" href="'.$loc.'">')) {
                $this->report($production, "sitemap: {$loc} is not self-canonical.");
            }
        }
    }

    private function render(string $url): ?string
    {
        try {
            $server = ['HTTP_ACCEPT' => 'text/html'];

            // Staging is behind Basic Auth: pass the configured credentials to the internal request.
            $auth = (string) config('autostvabaks.staging_auth');
            if (str_contains($auth, ':')) {
                [$server['PHP_AUTH_USER'], $server['PHP_AUTH_PW']] = explode(':', $auth, 2);
            }

            $request = Request::create($url, 'GET', server: $server);
            $response = app()->handle($request);

            return $response->getStatusCode() === 200 ? (string) $response->getContent() : null;
        } catch (\Throwable) {
            return null;
        }
    }

    private function report(bool $blocking, string $message): void
    {
        $blocking ? $this->block($message) : $this->warn_($message);
    }

    private function block(string $message): void
    {
        $this->failures[] = $message;
    }

    private function warn_(string $message): void
    {
        $this->warnings[] = $message;
    }
}
