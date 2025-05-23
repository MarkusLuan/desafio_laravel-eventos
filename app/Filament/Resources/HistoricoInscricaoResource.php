<?php

namespace App\Filament\Resources;

use App\Filament\Resources\HistoricoInscricaoResource\Pages;
use App\Filament\Resources\HistoricoInscricaoResource\RelationManagers;
use App\Models\HistoricoInscricao;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class HistoricoInscricaoResource extends Resource
{
    protected static ?string $model = HistoricoInscricao::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    protected static ?string $label = 'Histórico Inscrições';
    protected static ?string $navigationLabel = 'Histórico Inscrições';

    public static function canCreate(): bool { return false; }

    public static function getRoutePrefix(): string
    {
        return "/historico/inscricoes";
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                //
            ])
            ->filters([
                //
            ])
            ->actions([
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
            'index' => Pages\ListHistoricoInscricoes::route('/'),
        ];
    }
}
