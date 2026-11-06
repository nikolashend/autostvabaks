<?php

namespace App\Filament\Resources\TestimonialResource\Pages;

use App\Filament\Resources\TestimonialResource;
use App\Models\Testimonial;
use Filament\Actions;
use Filament\Resources\Pages\ManageRecords;

class ManageTestimonials extends ManageRecords
{
    protected static string $resource = TestimonialResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()
                ->label('Добавить отзыв')
                ->mutateFormDataUsing(function (array $data): array {
                    // New reviews go to the end of the carousel.
                    $data['sort'] = (int) Testimonial::query()->max('sort') + 10;

                    return $data;
                }),
        ];
    }
}
