<?php

namespace Database\Seeders;

use App\Models\Testimonial;
use Illuminate\Database\Seeder;

/**
 * Placeholders that only demonstrate the carousel. They are deliberately worded
 * as placeholders (no invented customers), seeded INACTIVE, and the public site
 * additionally refuses to render anything matching autostvabaks.placeholder_markers
 * outside the local environment. Replace them with real reviews in the admin panel.
 */
class TestimonialSeeder extends Seeder
{
    public function run(): void
    {
        if (Testimonial::query()->exists()) {
            return;
        }

        for ($i = 1; $i <= 3; $i++) {
            Testimonial::create([
                'author' => "Kliendi nimi {$i}",
                'location' => ['et' => 'Linn', 'ru' => 'Город', 'en' => 'City'],
                'title' => [
                    'et' => "Näidistagasiside {$i}",
                    'ru' => "Пример отзыва {$i}",
                    'en' => "Sample review {$i}",
                ],
                'text' => [
                    'et' => 'Siia tuleb päris kliendi tagasiside. Tekst lisatakse haldusliideses pärast esimesi tehinguid.',
                    'ru' => 'Здесь будет отзыв реального клиента. Текст добавляется в админ-панели после первых сделок.',
                    'en' => 'A real customer review will appear here. The text is added in the admin panel after the first deals.',
                ],
                'rating' => 5,
                'sort' => $i * 10,
                'is_active' => false,
            ]);
        }
    }
}
