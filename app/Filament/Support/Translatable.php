<?php

namespace App\Filament\Support;

use App\Support\Site;
use Closure;
use Filament\Forms\Components\Tabs;
use Filament\Forms\Components\Tabs\Tab;

/**
 * Builds the "ET | RU | EN" tab set used by every translatable form in the admin panel.
 *
 * The callback receives the locale code and whether it is the default (required)
 * language, and returns the fields for that tab – typically bound to JSON paths
 * such as "title.et".
 */
class Translatable
{
    /** @param Closure(string $locale, bool $isDefault): array $fields */
    public static function tabs(Closure $fields): Tabs
    {
        $tabs = [];

        foreach (Site::locales() as $code => $locale) {
            $tabs[] = Tab::make($locale['short'].' · '.$locale['name'])
                ->schema($fields($code, $code === Site::defaultLocale()));
        }

        return Tabs::make('translations')->tabs($tabs)->columnSpanFull();
    }
}
