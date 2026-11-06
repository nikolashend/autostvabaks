<?php

namespace App\Filament\Resources;

use App\Filament\Resources\UserResource\Pages;
use App\Models\User;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Validation\Rules\Password;

/** Admin panel accounts. */
class UserResource extends Resource
{
    protected static ?string $model = User::class;

    protected static ?string $navigationIcon = 'heroicon-o-users';

    protected static ?string $navigationGroup = 'Система';

    protected static ?string $navigationLabel = 'Администраторы';

    protected static ?string $modelLabel = 'администратор';

    protected static ?string $pluralModelLabel = 'Администраторы';

    protected static ?int $navigationSort = 100;

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\TextInput::make('name')
                ->label('Имя')
                ->required()
                ->maxLength(255),
            Forms\Components\TextInput::make('email')
                ->label('E-mail (логин)')
                ->email()
                ->required()
                ->maxLength(255)
                ->unique(ignoreRecord: true),
            Forms\Components\TextInput::make('password')
                ->label(fn (string $operation) => $operation === 'create' ? 'Пароль' : 'Новый пароль')
                ->password()
                ->revealable()
                ->rule(Password::min(10))
                ->required(fn (string $operation) => $operation === 'create')
                // Empty on edit = keep the current password (the model hashes it on save).
                ->dehydrated(fn (?string $state) => filled($state))
                ->helperText(fn (string $operation) => $operation === 'create'
                    ? 'Не менее 10 символов.'
                    : 'Оставьте пустым, чтобы не менять пароль.'),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')->label('Имя')->weight('bold'),
                Tables\Columns\TextColumn::make('email')->label('E-mail'),
                Tables\Columns\TextColumn::make('created_at')->label('Создан')->dateTime('d.m.Y'),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make()
                    // Nobody can lock everyone out by deleting their own account.
                    ->hidden(fn (User $record) => $record->is(auth()->user())),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ManageUsers::route('/'),
        ];
    }
}
