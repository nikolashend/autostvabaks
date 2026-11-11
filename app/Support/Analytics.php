<?php

namespace App\Support;

/**
 * Analytics vendor switch (ADR-13 / Stage 3).
 *
 * The frontend contract is the dataLayer (window.asvTrack). Google Tag Manager
 * is injected only when a container ID is configured AND analytics are enabled
 * for the environment: by default on the production host only, so staging
 * never reports traffic unless ANALYTICS_ENABLED=true is set deliberately
 * (e.g. for GTM preview sessions).
 */
class Analytics
{
    public static function enabled(): bool
    {
        $flag = config('autostvabaks.analytics.enabled');

        if ($flag === null || $flag === '') {
            return Site::isProductionHost();
        }

        return filter_var($flag, FILTER_VALIDATE_BOOL);
    }

    /** Container ID to inject, or null when analytics are off / not configured. */
    public static function gtmId(): ?string
    {
        $id = strtoupper(trim((string) config('autostvabaks.analytics.gtm_id')));

        if (! preg_match('/^GTM-[A-Z0-9]{4,12}$/', $id)) {
            return null;
        }

        return static::enabled() ? $id : null;
    }

    public static function configured(): bool
    {
        return (bool) preg_match('/^GTM-[A-Z0-9]{4,12}$/', strtoupper(trim((string) config('autostvabaks.analytics.gtm_id'))));
    }
}
