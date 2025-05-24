<?php

namespace App\Filament\Resources;

use App\Filament\Resources\HistoricoInscricaoResource\Pages;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

use App\Models\Enums\StatusInscricaoEnum;
use App\Models\HistoricoInscricao;

class HistoricoInscricaoResource extends Resource
{
    protected static ?string $model = HistoricoInscricao::class;

    protected static ?string $navigationIcon = 'heroicon-o-archive-box';

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
                TextColumn::make('created_at')
                    ->datetime('d/m/Y \à\s H:i')
                    ->label('Data de Alteração'),
                TextColumn::make('inscricao.evento.display_name')
                    ->label('Evento'),
                TextColumn::make('status.status')
                    ->label('Situação')
                    ->badge()
                    ->colors([
                        'success' => static fn ($state): bool => $state == StatusInscricaoEnum::INSCRITO,
                        'warning' => static fn ($state): bool => $state == StatusInscricaoEnum::ESPERANDO_PAGAMENTO,
                        'danger' => static fn ($state): bool => $state == StatusInscricaoEnum::CANCELADO
                    ])
                    ->formatStateUsing(fn ($state, $record) => $state->toString()),
                TextColumn::make('inscricao.inscrito.name')
                    ->label('Inscrito'),
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
