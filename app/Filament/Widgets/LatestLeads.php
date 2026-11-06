<?php

namespace App\Filament\Widgets;

use App\Filament\Resources\LeadResource;
use App\Models\Lead;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget;

class LatestLeads extends TableWidget
{
    protected static ?int $sort = 2;

    protected int|string|array $columnSpan = 'full';

    protected static ?string $heading = 'Последние заявки';

    public function table(Table $table): Table
    {
        return $table
            ->query(Lead::query()->latest()->limit(8))
            ->paginated(false)
            ->columns([
                Tables\Columns\TextColumn::make('created_at')->label('Получена')->dateTime('d.m.Y H:i'),
                Tables\Columns\TextColumn::make('reg_number')->label('Рег. номер')->weight('bold'),
                Tables\Columns\TextColumn::make('phone')->label('Телефон'),
                Tables\Columns\TextColumn::make('desired_price')->label('Желаемая цена')
                    ->state(fn (Lead $record) => $record->formattedPrice() ?? '—'),
                Tables\Columns\TextColumn::make('language')->label('Язык')->badge()->color('gray')
                    ->formatStateUsing(fn (string $state) => strtoupper($state)),
            ])
            ->recordUrl(fn (Lead $record) => LeadResource::getUrl('view', ['record' => $record]))
            ->emptyStateHeading('Заявок пока нет');
    }
}
