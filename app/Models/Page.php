<?php

namespace App\Models;

use App\Models\Concerns\HasTranslations;
use App\Support\Site;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

/**
 * A content page with its own URL per language: legal pages today, SEO /
 * information pages later ("how to sell fast", "damaged cars", …) – those are
 * added from the admin panel without touching the code.
 */
class Page extends Model
{
    use HasTranslations;

    public const FOOTER_GROUPS = [
        'info' => 'Полезная информация',
        'legal' => 'Юридическая информация',
        'none' => 'Не показывать в футере',
    ];

    protected $fillable = [
        'key', 'slug_et', 'slug_ru', 'slug_en', 'title', 'content',
        'meta_description', 'footer_group', 'sort', 'is_active', 'is_indexable',
    ];

    protected function casts(): array
    {
        return [
            'title' => 'array',
            'content' => 'array',
            'meta_description' => 'array',
            'is_active' => 'boolean',
            'is_indexable' => 'boolean',
        ];
    }

    protected static function booted(): void
    {
        $flush = fn () => Cache::forget('site.footer_pages');
        static::saved($flush);
        static::deleted($flush);
    }

    public function scopeActive(Builder $query): Builder
    {
        return $query->where('is_active', true)->orderBy('sort')->orderBy('id');
    }

    /** Pages that may appear in the sitemap / be indexed: published AND marked indexable. */
    public function scopeIndexable(Builder $query): Builder
    {
        return $query->active()->where('is_indexable', true);
    }

    /** @return array<string, string> */
    public function alternates(): array
    {
        return Site::alternates(fn (string $locale) => $this->url($locale));
    }

    public function slug(?string $locale = null): string
    {
        $locale ??= app()->getLocale();

        return (string) ($this->getAttribute('slug_'.$locale) ?: $this->slug_et);
    }

    public function url(?string $locale = null): string
    {
        $locale ??= app()->getLocale();

        return Site::route('page', ['slug' => $this->slug($locale)], $locale);
    }

    public static function findBySlug(string $slug, string $locale): ?self
    {
        return static::query()
            ->where('is_active', true)
            ->where('slug_'.$locale, $slug)
            ->first();
    }

    public static function byKey(string $key): ?self
    {
        return static::query()->where('key', $key)->where('is_active', true)->first();
    }
}
