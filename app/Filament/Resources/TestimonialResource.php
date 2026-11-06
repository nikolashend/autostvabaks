<?php

namespace App\Filament\Resources;

use App\Filament\Resources\TestimonialResource\Pages;
use App\Filament\Support\Translatable;
use App\Models\Testimonial;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Str;

/** Customer reviews shown in the landing page carousel (entered manually, no Google Reviews API). */
class TestimonialResource extends Resource
{
    protected static ?string $model = Testimonial::class;

    protected static ?string $navigationIcon = 'heroicon-o-chat-bubble-left-right';

    protected static ?string $navigationGroup = 'Контент сайта';

    protected static ?string $navigationLabel = 'Отзывы';

    protected static ?string $modelLabel = 'отзыв';

    protected static ?string $pluralModelLabel = 'Отзывы';

    protected static ?int $navigationSort = 10;

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\Section::make()
                ->columns(4)
                ->schema([
                    Forms\Components\TextInput::make('author')
                        ->label('Имя клиента')
                        ->required()
                        ->maxLength(255)
                        ->columnSpan(2),
                    Forms\Components\Select::make('rating')
                        ->label('Оценка')
                        ->options([5 => '★★★★★', 4 => '★★★★', 3 => '★★★', 2 => '★★', 1 => '★'])
                        ->default(5)
                        ->required()
                        ->native(false),
                    Forms\Components\Toggle::make('is_active')
                        ->label('Показывать на сайте')
                        ->default(true)
                        ->inline(false),
                ]),

            Translatable::tabs(fn (string $locale, bool $isDefault) => [
                Forms\Components\TextInput::make("title.{$locale}")
                    ->label('Заголовок (необязательно)')
                    ->maxLength(120),
                Forms\Components\Textarea::make("text.{$locale}")
                    ->label('Текст отзыва')
                    ->rows(5)
                    ->maxLength(1500)
                    ->required($isDefault)
                    ->helperText($isDefault ? null : 'Если оставить пустым, на этом языке будет показан эстонский текст.'),
                Forms\Components\TextInput::make("location.{$locale}")
                    ->label('Город / подпись под именем (необязательно)')
                    ->maxLength(120),
            ]),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->reorderable('sort')
            ->defaultSort('sort')
            ->columns([
                Tables\Columns\TextColumn::make('author')->label('Клиент')->weight('bold')->searchable(),
                Tables\Columns\TextColumn::make('text_preview')
                    ->label('Отзыв')
                    ->state(fn (Testimonial $record) => Str::limit($record->t('text', 'et'), 110))
                    ->wrap(),
                Tables\Columns\TextColumn::make('rating')
                    ->label('Оценка')
                    ->formatStateUsing(fn (int $state) => str_repeat('★', $state))
                    ->sortable(),
                Tables\Columns\ToggleColumn::make('is_active')->label('На сайте'),
            ])
            ->filters([
                Tables\Filters\TernaryFilter::make('is_active')->label('Показывается на сайте'),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\DeleteBulkAction::make(),
            ])
            ->emptyStateHeading('Отзывов пока нет')
            ->emptyStateDescription('Пока нет ни одного отзыва, блок отзывов на сайте скрыт.');
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()->orderBy('sort')->orderBy('id');
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ManageTestimonials::route('/'),
        ];
    }
}
