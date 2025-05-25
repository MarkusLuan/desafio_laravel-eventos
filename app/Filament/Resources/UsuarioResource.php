<?php

namespace App\Filament\Resources;

use App\Filament\Resources\UsuarioResource\Pages;
use Filament\Forms\Components\Actions\Action;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Forms\Set;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

use Illuminate\Support\Str;

use App\Models\Enums\PermissaoEnum;
use App\Models\Permissao;
use App\Models\Usuario;
use Filament\Forms\Get;

class UsuarioResource extends Resource
{
    protected static ?string $model = Usuario::class;

    protected static ?string $navigationIcon = 'heroicon-o-user-group';

    public static function canAccess(): bool
    {
        $user = auth()->user();
        $permissao = $user->permissao->role;

        return $permissao == PermissaoEnum::ADMINISTRADOR;
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                TextInput::make('name')
                    ->label('Nome Completo')
                    ->minLength(3)
                    ->required(),
                TextInput::make('email')
                    ->email()
                    ->required(),
                DateTimePicker::make('dt_nascimento')->label('Data de Nascimento')
                    ->displayFormat('d/m/Y')
                    ->firstDayOfWeek(1)
                    ->format('Y-m-d')
                    ->maxDate(now())
                    ->time(false)
                    ->required(),
                Select::make('permissao_id')->label('Permissão')->relationship('permissao', 'id')
                    ->getOptionLabelFromRecordUsing(fn (Permissao $record): string => $record->role->toString())
                    ->required(),
                TextInput::make('password')
                    ->label('Senha')
                    ->password()
                    ->minLength(5)
                    ->maxLength(20)
                    ->autocomplete('new-password')
                    ->revealable(filament()->arePasswordsRevealable())
                    ->suffixActions([
                        Action::make('mk_pass') // Action para gerar uma senha aleatória - Caso o metódo do navegador falhe
                            ->label('Gerar Senha')
                            ->action (function (array $arguments, Set $set) {
                                $senha = Str::random(10) . random_int(0, 900) . array('!', '@', '*')[random_int(0, 2)];
                                $set('password', $senha);
                            })
                            ->icon('heroicon-o-arrow-path')
                    ])
                    ->required(),
            ])->columns(3);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->label('Nome'),
                TextColumn::make('email'),
                TextColumn::make('dt_nascimento')
                    ->datetime('d/m/Y')
                    ->label('Data de Nascimento'),
                TextColumn::make('idade'),
                TextColumn::make('permissao.role')
                    ->badge()
                    ->colors([
                        'danger' => static fn ($state): bool => $state == PermissaoEnum::ADMINISTRADOR,
                        'primary' => static fn ($state): bool => $state == PermissaoEnum::ORGANIZADOR,
                        'success' => static fn ($state): bool => $state == PermissaoEnum::COMUM
                    ])
                    ->formatStateUsing(fn ($state, $record) => $state->toString())
                    ->label('Permissão'),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListUsuarios::route('/'),
            'create' => Pages\CreateUsuario::route('/create'),
            'edit' => Pages\EditUsuario::route('/{record}/edit'),
        ];
    }
}
