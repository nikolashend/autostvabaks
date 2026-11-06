<?php

namespace App\Filament\Resources\FaqResource\Pages;

use App\Filament\Resources\FaqResource;
use App\Models\Faq;
use Filament\Actions;
use Filament\Resources\Pages\ManageRecords;

class ManageFaqs extends ManageRecords
{
    protected static string $resource = FaqResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()
                ->label('Добавить вопрос')
                ->mutateFormDataUsing(function (array $data): array {
                    // New questions go to the end of the list.
                    $data['sort'] = (int) Faq::query()->max('sort') + 10;

                    return $data;
                }),
        ];
    }
}
