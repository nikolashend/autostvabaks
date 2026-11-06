<?php

namespace App\Filament\Pages;

use App\Models\Setting;
use Filament\Actions\Action;
use Filament\Forms;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Pages\Page;

/**
 * Contacts and other values that change without a developer: phone, messengers,
 * e-mails, map address, analytics tags.
 *
 * @property Form $form
 */
class ManageSettings extends Page implements HasForms
{
    use InteractsWithForms;

    protected static ?string $navigationIcon = 'heroicon-o-cog-6-tooth';

    protected static ?string $navigationGroup = 'Система';

    protected static ?string $navigationLabel = 'Настройки';

    protected static ?string $title = 'Настройки сайта';

    protected static ?string $slug = 'settings';

    protected static ?int $navigationSort = 90;

    protected static string $view = 'filament.pages.manage-settings';

    public ?array $data = [];

    public function mount(): void
    {
        $this->form->fill(Setting::allValues());
    }

    public function form(Form $form): Form
    {
        return $form
            ->statePath('data')
            ->schema([
                Forms\Components\Section::make('Контакты на сайте')
                    ->description('Пустые поля на сайте не показываются: пока номер не выбран, телефон и кнопки мессенджеров просто скрыты.')
                    ->columns(2)
                    ->schema([
                        Forms\Components\TextInput::make('phone')
                            ->label('Телефон для клиентов')
                            ->placeholder('+372 5xxx xxxx')
                            ->tel()
                            ->maxLength(32),
                        Forms\Components\TextInput::make('public_email')
                            ->label('Публичный e-mail')
                            ->email()
                            ->maxLength(255),
                        Forms\Components\TextInput::make('whatsapp')
                            ->label('WhatsApp')
                            ->placeholder('+372 5xxx xxxx')
                            ->helperText('Номер в международном формате. На сайте появится кнопка быстрого перехода в WhatsApp.')
                            ->maxLength(32),
                        Forms\Components\TextInput::make('telegram')
                            ->label('Telegram')
                            ->placeholder('@username или +372 5xxx xxxx')
                            ->helperText('Имя пользователя, номер телефона или ссылка t.me/…')
                            ->maxLength(255),
                    ]),

                Forms\Components\Section::make('Адрес и карта')
                    ->columns(2)
                    ->schema([
                        Forms\Components\TextInput::make('address')
                            ->label('Адрес (как показывать на сайте)')
                            ->maxLength(255),
                        Forms\Components\TextInput::make('map_query')
                            ->label('Точка на Google Maps')
                            ->helperText('Адрес или название места – так, как вы искали бы его в Google Maps.')
                            ->maxLength(255),
                    ]),

                Forms\Components\Section::make('Заявки')
                    ->columns(2)
                    ->schema([
                        Forms\Components\TextInput::make('leads_email')
                            ->label('E-mail для заявок')
                            ->helperText('Сюда приходит письмо «Новая заявка — номер авто» с фотографиями. На сайте этот адрес не показывается.')
                            ->email()
                            ->required()
                            ->maxLength(255),
                    ]),

                Forms\Components\Section::make('Аналитика и реклама')
                    ->collapsed()
                    ->schema([
                        Forms\Components\Textarea::make('head_scripts')
                            ->label('Код для <head>')
                            ->helperText('Google Tag Manager, Google Ads, Analytics и т.п. Код вставляется на все страницы сайта как есть.')
                            ->rows(8)
                            ->extraInputAttributes(['style' => 'font-family: monospace; font-size: 13px']),
                    ]),
            ]);
    }

    protected function getFormActions(): array
    {
        return [
            Action::make('save')->label('Сохранить')->submit('save'),
        ];
    }

    public function save(): void
    {
        Setting::putMany($this->form->getState());

        Notification::make()->title('Настройки сохранены')->success()->send();
    }
}
