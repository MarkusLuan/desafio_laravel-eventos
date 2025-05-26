<?php

namespace App\Filament\Resources;

use App\Filament\Resources\InscricaoResource\Pages;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

use App\Models\Enums\PermissaoEnum;
use App\Models\Enums\StatusInscricaoEnum;
use App\Models\Inscricao;

class InscricaoResource extends Resource
{
    protected static ?string $model = Inscricao::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    protected static ?string $label = 'Inscrições';
    protected static ?string $navigationLabel = 'Inscrições';

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('updated_at')
                    ->label('Data Inscrição')
                    ->datetime('d/m/Y \à\s H:i', 'america/recife'),
                TextColumn::make('evento.display_name')
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
                TextColumn::make('inscrito.name')
                    ->label('Usuário')
                    ->visible(function () {
                        $user = auth()->user();
                        $permissao = $user->permissao->role;

                        return $permissao != PermissaoEnum::COMUM;
                    }),
            ])
            ->filters([
                //
            ])
            ->actions([])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([]),
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
            'index' => Pages\ListInscricoes::route('/'),
        ];
    }

    public static function getEloquentQuery(): Builder
    {
        $query = parent::getEloquentQuery();

        $user = auth()->user();
        $permissao = $user->permissao->role;

        switch ($permissao) {
            case PermissaoEnum::COMUM:
                // Listando apenas inscrições efetuadas pelo usuário logado
                $query->where([
                    'usuario_id' => $user->id
                ]);
                break;
        }

        return $query;
    }
}
