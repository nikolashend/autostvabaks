<?php

namespace App\Filament\Resources;

use App\Filament\Resources\LeadResource\Pages;
use App\Models\Lead;
use Filament\Infolists\Components\Section;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Components\ViewEntry;
use Filament\Infolists\Infolist;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

/**
 * Incoming requests from the landing page. Read-only by design: no statuses or
 * CRM features in the first stage – the e-mail is the working copy, this is the archive.
 */
class LeadResource extends Resource
{
    protected static ?string $model = Lead::class;

    protected static ?string $navigationIcon = 'heroicon-o-inbox-arrow-down';

    protected static ?string $navigationLabel = 'Заявки';

    protected static ?string $modelLabel = 'заявка';

    protected static ?string $pluralModelLabel = 'Заявки';

    protected static ?int $navigationSort = 1;

    protected static ?string $recordTitleAttribute = 'reg_number';

    public static function canCreate(): bool
    {
        return false;
    }

    public static function canEdit(Model $record): bool
    {
        return false;
    }

    public static function getNavigationBadge(): ?string
    {
        $today = static::getModel()::query()->whereDate('created_at', today())->count();

        return $today > 0 ? (string) $today : null;
    }

    public static function getNavigationBadgeTooltip(): ?string
    {
        return 'Заявок сегодня';
    }

    public static function table(Table $table): Table
    {
        return $table
            ->defaultSort('created_at', 'desc')
            ->columns([
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Получена')
                    ->dateTime('d.m.Y H:i')
                    ->sortable(),
                Tables\Columns\TextColumn::make('reg_number')
                    ->label('Рег. номер')
                    ->weight('bold')
                    ->searchable()
                    ->copyable(),
                Tables\Columns\TextColumn::make('phone')
                    ->label('Телефон')
                    ->searchable()
                    ->url(fn (Lead $record) => 'tel:'.preg_replace('/[^\d+]/', '', $record->phone)),
                Tables\Columns\TextColumn::make('email')
                    ->label('E-mail')
                    ->searchable()
                    ->toggleable(),
                Tables\Columns\TextColumn::make('desired_price')
                    ->label('Желаемая цена')
                    ->state(fn (Lead $record) => $record->formattedPrice() ?? '—')
                    ->sortable(),
                Tables\Columns\TextColumn::make('mileage')
                    ->label('Пробег')
                    ->state(fn (Lead $record) => $record->formattedMileage() ?? '—')
                    ->sortable()
                    ->toggleable(),
                Tables\Columns\TextColumn::make('language')
                    ->label('Язык')
                    ->badge()
                    ->color('gray')
                    ->formatStateUsing(fn (string $state) => strtoupper($state)),
                Tables\Columns\TextColumn::make('photos_count')
                    ->label('Фото')
                    ->state(fn (Lead $record) => $record->photoCount() ?: '—')
                    ->alignCenter(),
                Tables\Columns\IconColumn::make('mail_sent_at')
                    ->label('Письмо')
                    ->state(fn (Lead $record) => $record->mail_sent_at !== null)
                    ->boolean()
                    ->tooltip(fn (Lead $record) => $record->mail_sent_at
                        ? 'Отправлено '.$record->mail_sent_at->format('d.m.Y H:i')
                        : ($record->mail_error ?: 'Письмо не отправлено'))
                    ->alignCenter(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('language')
                    ->label('Язык общения')
                    ->options(Lead::LANGUAGES),
                Tables\Filters\Filter::make('with_photos')
                    ->label('Только с фото')
                    ->query(fn (Builder $query) => $query->whereNotNull('photos')),
                Tables\Filters\Filter::make('mail_failed')
                    ->label('Письмо не отправлено')
                    ->query(fn (Builder $query) => $query->whereNull('mail_sent_at')),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\DeleteBulkAction::make(),
            ])
            ->emptyStateHeading('Заявок пока нет')
            ->emptyStateDescription('Здесь появятся заявки, отправленные через форму на сайте.');
    }

    public static function infolist(Infolist $infolist): Infolist
    {
        return $infolist->schema([
            Section::make('Автомобиль')
                ->columns(3)
                ->schema([
                    TextEntry::make('reg_number')->label('Регистрационный номер')->weight('bold')->copyable(),
                    TextEntry::make('desired_price')->label('Желаемая цена')
                        ->state(fn (Lead $record) => $record->formattedPrice() ?? 'не указана'),
                    TextEntry::make('mileage')->label('Пробег')
                        ->state(fn (Lead $record) => $record->formattedMileage() ?? 'не указан'),
                    TextEntry::make('info')->label('Дополнительная информация')
                        ->placeholder('—')
                        ->columnSpanFull()
                        // keep the client's line breaks; the text itself stays escaped
                        ->formatStateUsing(fn (string $state) => nl2br(e($state)))
                        ->html(),
                ]),

            Section::make('Контакты')
                ->columns(3)
                ->schema([
                    TextEntry::make('phone')->label('Телефон')->copyable()
                        ->url(fn (Lead $record) => 'tel:'.preg_replace('/[^\d+]/', '', $record->phone)),
                    TextEntry::make('email')->label('E-mail')->copyable()
                        ->url(fn (Lead $record) => 'mailto:'.$record->email),
                    TextEntry::make('language')->label('Язык общения')
                        ->state(fn (Lead $record) => $record->languageLabel()),
                ]),

            Section::make('Фотографии')
                ->schema([
                    ViewEntry::make('photos')->hiddenLabel()->view('filament.lead-photos'),
                ]),

            Section::make('Служебная информация')
                ->columns(3)
                ->collapsed()
                ->schema([
                    TextEntry::make('created_at')->label('Получена')->dateTime('d.m.Y H:i:s'),
                    TextEntry::make('locale')->label('Язык сайта')
                        ->formatStateUsing(fn (?string $state) => strtoupper((string) $state)),
                    TextEntry::make('mail_sent_at')->label('Письмо отправлено')
                        ->dateTime('d.m.Y H:i:s')->placeholder('не отправлено'),
                    TextEntry::make('mail_error')->label('Ошибка отправки письма')
                        ->visible(fn (Lead $record) => filled($record->mail_error))
                        ->color('danger')->columnSpanFull(),
                    TextEntry::make('ip')->label('IP-адрес')->placeholder('—'),
                    TextEntry::make('referrer')->label('Источник перехода')->placeholder('—'),
                    TextEntry::make('user_agent')->label('Браузер')->placeholder('—')->columnSpanFull(),
                    TextEntry::make('utm_source')->label('utm_source')->placeholder('—'),
                    TextEntry::make('utm_medium')->label('utm_medium')->placeholder('—'),
                    TextEntry::make('utm_campaign')->label('utm_campaign')->placeholder('—'),
                    TextEntry::make('utm_content')->label('utm_content')->placeholder('—'),
                    TextEntry::make('utm_term')->label('utm_term')->placeholder('—'),
                    TextEntry::make('gclid')->label('gclid')->placeholder('—'),
                ]),
        ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListLeads::route('/'),
            'view' => Pages\ViewLead::route('/{record}'),
        ];
    }
}
