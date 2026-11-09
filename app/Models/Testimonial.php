<?php

namespace App\Models;

use App\Models\Concerns\HasTranslations;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class Testimonial extends Model
{
    use HasTranslations;

    protected $fillable = ['author', 'location', 'title', 'text', 'rating', 'sort', 'is_active'];

    protected function casts(): array
    {
        return [
            'location' => 'array',
            'title' => 'array',
            'text' => 'array',
            'rating' => 'integer',
            'is_active' => 'boolean',
        ];
    }

    public function scopeActive(Builder $query): Builder
    {
        return $query->where('is_active', true)->orderBy('sort')->orderBy('id');
    }

    /**
     * Reviews allowed on the public site: active ones that are not seeded
     * placeholders. Placeholders are filtered out everywhere except local
     * development, so sample reviews can never reach production.
     */
    public function scopePublished(Builder $query): Builder
    {
        $query->active();

        if (app()->environment('local')) {
            return $query;
        }

        foreach (config('autostvabaks.placeholder_markers', []) as $marker) {
            $query->where('author', 'not like', '%'.$marker.'%')
                ->where('title', 'not like', '%'.$marker.'%')
                ->where('text', 'not like', '%'.$marker.'%');
        }

        return $query;
    }

    public function isPlaceholder(): bool
    {
        $haystack = $this->author.' '.json_encode($this->title, JSON_UNESCAPED_UNICODE).' '.json_encode($this->text, JSON_UNESCAPED_UNICODE);

        foreach (config('autostvabaks.placeholder_markers', []) as $marker) {
            if (mb_stripos($haystack, $marker) !== false) {
                return true;
            }
        }

        return false;
    }
}
