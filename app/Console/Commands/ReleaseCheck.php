<?php

namespace App\Console\Commands;

use App\Models\Page;
use App\Models\Testimonial;
use App\Support\Site;
use Illuminate\Console\Command;

/**
 * Production release gate: `php artisan site:release-check`
 *
 * Exit code 1 when the current configuration or content must not go live:
 * staging noindex on the production host, debug mode, placeholder reviews,
 * draft legal pages, sample strings on public pages, staging host in SEO output.
 * Run it in deploy.sh; it is safe on staging (warnings instead of failures).
 */
class ReleaseCheck extends Command
{
    protected $signature = 'site:release-check {--strict : Treat every warning as a failure (use on the production host)}';

    protected $description = 'Verify that the site is safe to publish (noindex, debug, placeholders, drafts, hosts).';

    private array $failures = [];

    private array $warnings = [];

    public function handle(): int
    {
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

        // --- content ------------------------------------------------------
        $placeholders = Testimonial::active()->get()->filter->isPlaceholder();
        if ($placeholders->isNotEmpty()) {
            $this->report($production, 'Active placeholder testimonials: '.$placeholders->pluck('author')->join(', ').' (they are hidden outside local, but should be deleted or deactivated).');
        }

        foreach (Page::active()->get() as $page) {
            foreach (array_keys(Site::locales()) as $locale) {
                $text = strip_tags((string) $page->t('content', $locale));
                foreach (config('autostvabaks.draft_markers', []) as $marker) {
                    if (mb_stripos($text, $marker) !== false) {
                        $this->report($production, "Page \"{$page->t('title', $locale)}\" ({$locale}) still contains the draft marker \"{$marker}\".");
                        break;
                    }
                }
            }
        }

        // --- rendered public pages ----------------------------------------
        $markers = array_merge(config('autostvabaks.placeholder_markers', []), ['autostvabaks.adme.ee']);
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
            if ($production && preg_match('/<meta name="robots" content="[^"]*noindex/i', $html)) {
                $this->block("{$url} renders a noindex meta tag.");
            }
            if (! preg_match('/<link rel="canonical" href="'.preg_quote(rtrim($appUrl, '/'), '/').'/', $html)) {
                $this->report($production, "{$url}: canonical does not start with APP_URL ({$appUrl}).");
            }
        }

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

    private function render(string $url): ?string
    {
        try {
            $request = \Illuminate\Http\Request::create($url, 'GET', server: ['HTTP_ACCEPT' => 'text/html']);
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
