<?php

namespace App\Filament\Resources;

use App\Filament\Resources\UsuarioResource\Pages;
use App\Filament\Resources\UsuarioResource\RelationManagers;
use App\Models\Enums\PermissaoEnum;
use App\Models\Usuario;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class UsuarioResource extends Resource
{
    protected static ?string $model = Usuario::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                //
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('id')
                    ->label('#'),
                TextColumn::make('name')
                    ->label('Nome'),
                TextColumn::make('email'),
                TextColumn::make('dt_nascimento')
                    ->datetime('d/m/Y')
                    ->label('Data de Nascimento'),
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
