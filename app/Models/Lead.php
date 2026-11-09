<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class Lead extends Model
{
    public const LANGUAGES = [
        'et' => 'Eesti',
        'ru' => 'Русский',
        'en' => 'English',
    ];

    protected $fillable = [
        'reg_number', 'phone', 'email', 'desired_price', 'language', 'mileage',
        'info', 'photos', 'locale', 'ip', 'user_agent', 'referrer',
        'utm_source', 'utm_medium', 'utm_campaign', 'utm_content', 'utm_term', 'gclid',
        'mail_sent_at', 'mail_error',
    ];

    public const ATTRIBUTION_FIELDS = ['utm_source', 'utm_medium', 'utm_campaign', 'utm_content', 'utm_term', 'gclid'];

    protected function casts(): array
    {
        return [
            'photos' => 'array',
            'desired_price' => 'integer',
            'mileage' => 'integer',
            'mail_sent_at' => 'datetime',
        ];
    }

    protected static function booted(): void
    {
        // Photos live on the private disk – remove them together with the lead.
        static::deleted(function (Lead $lead) {
            Storage::disk('local')->deleteDirectory($lead->photoDirectory());
        });
    }

    public function photoDirectory(): string
    {
        return 'leads/'.$this->getKey();
    }

    public function languageLabel(): string
    {
        return self::LANGUAGES[$this->language] ?? (string) $this->language;
    }

    public function formattedPrice(): ?string
    {
        return $this->desired_price === null
            ? null
            : number_format($this->desired_price, 0, ',', ' ').' €';
    }

    public function formattedMileage(): ?string
    {
        return $this->mileage === null
            ? null
            : number_format($this->mileage, 0, ',', ' ').' km';
    }

    public function photoCount(): int
    {
        return count($this->photos ?? []);
    }
}
