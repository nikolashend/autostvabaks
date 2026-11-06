<?php

namespace App\Filament\Resources;

use App\Filament\Resources\FaqResource\Pages;
use App\Filament\Support\Translatable;
use App\Models\Faq;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

/** KKK – frequently asked questions block on the landing page. */
class FaqResource extends Resource
{
    protected static ?string $model = Faq::class;

    protected static ?string $navigationIcon = 'heroicon-o-question-mark-circle';

    protected static ?string $navigationGroup = 'Контент сайта';

    protected static ?string $navigationLabel = 'Вопросы и ответы (KKK)';

    protected static ?string $modelLabel = 'вопрос';

    protected static ?string $pluralModelLabel = 'Вопросы и ответы';

    protected static ?int $navigationSort = 20;

    public static function form(Form $form): Form
    {
        return $form->schema([
            Translatable::tabs(fn (string $locale, bool $isDefault) => [
                Forms\Components\TextInput::make("question.{$locale}")
                    ->label('Вопрос')
                    ->maxLength(255)
                    ->required($isDefault),
                Forms\Components\Textarea::make("answer.{$locale}")
                    ->label('Ответ')
                    ->rows(6)
                    ->maxLength(3000)
                    ->required($isDefault)
                    ->helperText($isDefault ? null : 'Если оставить пустым, на этом языке будет показан эстонский текст.'),
            ]),

            Forms\Components\Toggle::make('is_active')
                ->label('Показывать на сайте')
                ->default(true),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->reorderable('sort')
            ->defaultSort('sort')
            ->columns([
                Tables\Columns\TextColumn::make('question_et')
                    ->label('Вопрос (ET)')
                    ->state(fn (Faq $record) => $record->t('question', 'et'))
                    ->weight('bold')
                    ->wrap()
                    ->searchable(query: fn (Builder $query, string $search) => $query
                        ->where('question', 'like', "%{$search}%")),
                Tables\Columns\TextColumn::make('question_ru')
                    ->label('RU')
                    ->state(fn (Faq $record) => $record->question['ru'] ?? '')
                    ->wrap()
                    ->toggleable(),
                Tables\Columns\ToggleColumn::make('is_active')->label('На сайте'),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\DeleteBulkAction::make(),
            ])
            ->emptyStateHeading('Вопросов пока нет')
            ->emptyStateDescription('Пока нет ни одного вопроса, блок KKK на сайте скрыт.');
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()->orderBy('sort')->orderBy('id');
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ManageFaqs::route('/'),
        ];
    }
}
