<?php

namespace App\Filament\Resources;

use App\Filament\Resources\PageResource\Pages;
use App\Filament\Support\Translatable;
use App\Models\Page;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Forms\Set;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Support\Str;

/**
 * Content pages: Kasutustingimused / Privaatsuspoliitika today, information and
 * SEO pages later. A new page gets its own URL in every language and a footer
 * link automatically – no development needed.
 */
class PageResource extends Resource
{
    protected static ?string $model = Page::class;

    protected static ?string $navigationIcon = 'heroicon-o-document-text';

    protected static ?string $navigationGroup = 'Контент сайта';

    protected static ?string $navigationLabel = 'Страницы';

    protected static ?string $modelLabel = 'страница';

    protected static ?string $pluralModelLabel = 'Страницы';

    protected static ?int $navigationSort = 30;

    /** URL segments already used by the application itself. */
    private const RESERVED_SLUGS = ['admin', 'livewire', 'storage', 'up', 'paring', 'ru', 'en', 'et', 'assets'];

    public static function form(Form $form): Form
    {
        return $form->schema([
            Translatable::tabs(fn (string $locale, bool $isDefault) => [
                Forms\Components\TextInput::make("title.{$locale}")
                    ->label('Заголовок')
                    ->maxLength(160)
                    ->required()
                    ->live(onBlur: true)
                    ->afterStateUpdated(function (?string $state, Set $set, ?Page $record) use ($locale) {
                        // Suggest a URL only for a new page – changing a live URL breaks links and SEO.
                        if ($record === null && filled($state)) {
                            $set("slug_{$locale}", Str::slug($state, '-', $locale === 'ru' ? 'ru' : 'en'));
                        }
                    }),
                Forms\Components\TextInput::make("slug_{$locale}")
                    ->label('Адрес страницы (URL)')
                    ->prefix($isDefault ? '/' : "/{$locale}/")
                    ->required()
                    ->maxLength(120)
                    ->regex('/^[a-z0-9]+(?:-[a-z0-9]+)*$/')
                    ->notIn(self::RESERVED_SLUGS)
                    ->unique(ignoreRecord: true)
                    ->helperText('Только латинские буквы, цифры и дефис, например: auto-kiire-muuk')
                    ->validationMessages([
                        'regex' => 'Допустимы только строчные латинские буквы, цифры и дефис.',
                        'not_in' => 'Этот адрес занят системой, выберите другой.',
                        'unique' => 'Страница с таким адресом уже существует.',
                    ]),
                Forms\Components\Textarea::make("meta_description.{$locale}")
                    ->label('Описание для поисковых систем (meta description)')
                    ->rows(2)
                    ->maxLength(300)
                    ->helperText('Рекомендуется до 160 символов.'),
                Forms\Components\RichEditor::make("content.{$locale}")
                    ->label('Содержимое')
                    ->toolbarButtons(['h2', 'h3', 'bold', 'italic', 'link', 'bulletList', 'orderedList', 'blockquote', 'undo', 'redo'])
                    ->required($isDefault),
            ]),

            Forms\Components\Section::make('Публикация')
                ->columns(3)
                ->schema([
                    Forms\Components\Select::make('footer_group')
                        ->label('Ссылка в футере')
                        ->options(Page::FOOTER_GROUPS)
                        ->default('info')
                        ->required()
                        ->native(false),
                    Forms\Components\TextInput::make('sort')
                        ->label('Порядок')
                        ->numeric()
                        ->default(100)
                        ->minValue(0)
                        ->required(),
                    Forms\Components\Toggle::make('is_active')
                        ->label('Опубликована')
                        ->default(true)
                        ->inline(false),
                ]),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->defaultSort('sort')
            ->columns([
                Tables\Columns\TextColumn::make('title_et')
                    ->label('Страница')
                    ->state(fn (Page $record) => $record->t('title', 'et'))
                    ->weight('bold')
                    ->description(fn (Page $record) => '/'.$record->slug_et),
                Tables\Columns\TextColumn::make('footer_group')
                    ->label('Футер')
                    ->badge()
                    ->color('gray')
                    ->formatStateUsing(fn (string $state) => Page::FOOTER_GROUPS[$state] ?? $state),
                Tables\Columns\TextColumn::make('sort')->label('Порядок')->sortable(),
                Tables\Columns\ToggleColumn::make('is_active')->label('Опубликована'),
                Tables\Columns\TextColumn::make('updated_at')->label('Изменена')->dateTime('d.m.Y H:i')->sortable(),
            ])
            ->actions([
                Tables\Actions\Action::make('open')
                    ->label('Открыть')
                    ->icon('heroicon-o-arrow-top-right-on-square')
                    ->url(fn (Page $record) => $record->url('et'))
                    ->openUrlInNewTab()
                    ->visible(fn (Page $record) => $record->is_active),
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make()
                    // The form's consent checkbox and the footer link to these pages.
                    ->hidden(fn (Page $record) => $record->key !== null),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListPages::route('/'),
            'create' => Pages\CreatePage::route('/create'),
            'edit' => Pages\EditPage::route('/{record}/edit'),
        ];
    }
}
