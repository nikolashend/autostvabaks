<?php

namespace App\Filament\Resources\LeadResource\Pages;

use App\Filament\Resources\LeadResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;

class ViewLead extends ViewRecord
{
    protected static string $resource = LeadResource::class;

    public function getTitle(): string
    {
        return 'Заявка — '.$this->getRecord()->reg_number;
    }

    protected function getHeaderActions(): array
    {
        return [
            Actions\Action::make('reply')
                ->label('Ответить клиенту')
                ->icon('heroicon-o-envelope')
                ->url(fn () => 'mailto:'.$this->getRecord()->email.'?subject='.rawurlencode('Autostvabaks — '.$this->getRecord()->reg_number)),
            Actions\DeleteAction::make(),
        ];
    }
}
