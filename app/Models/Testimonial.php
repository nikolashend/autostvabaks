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
}
