<?php

namespace App\Filament\Resources\PageResource\Pages;

use App\Filament\Resources\PageResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditPage extends EditRecord
{
    protected static string $resource = PageResource::class;

    public function getTitle(): string
    {
        return 'Редактирование: '.$this->getRecord()->t('title', 'et');
    }

    protected function getHeaderActions(): array
    {
        return [
            Actions\Action::make('open')
                ->label('Открыть на сайте')
                ->icon('heroicon-o-arrow-top-right-on-square')
                ->color('gray')
                ->url(fn () => $this->getRecord()->url('et'))
                ->openUrlInNewTab(),
            Actions\DeleteAction::make()
                ->hidden(fn () => $this->getRecord()->key !== null),
        ];
    }
}