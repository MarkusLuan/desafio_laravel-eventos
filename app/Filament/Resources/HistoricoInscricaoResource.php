<?php

namespace App\Filament\Resources;

use App\Filament\Resources\HistoricoInscricaoResource\Pages;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\DateTimePicker;
use Illuminate\Database\Eloquent\Builder;

use App\Models\Enums\StatusInscricaoEnum;
use App\Models\Enums\PermissaoEnum;
use App\Models\HistoricoInscricao;
use App\Models\StatusInscricao;

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
                    ->label('Data de Alteração')
                    ->datetime('d/m/Y \à\s H:i', 'america/recife'),
                TextColumn::make('inscricao.evento.display_name')
                    ->label('Evento'),
                TextColumn::make('inscricao.evento.dt_evento')
                    ->label('Data do Evento')
                    ->datetime('d/m/Y \à\s H:i', 'america/recife'),
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
                    ->label('Inscrito')
                    ->visible(function () {
                        $user = auth()->user();
                        $permissao = $user->permissao->role;

                        return $permissao != PermissaoEnum::COMUM;
                    }),
            ])
            ->filters([
                Filter::make('f_dt_evento')->form([
                    DateTimePicker::make('finpt_dt_evento')->label('Data do Evento')
                        ->displayFormat('d/m/Y')
                        ->firstDayOfWeek(1)
                        ->format('Y-m-d')
                        ->timezone('america/recife')
                        ->time(false)
                ])->query(function (Builder $query, array $data): Builder {
                    $dtEvento = $data['finpt_dt_evento'];
                    if (!$dtEvento) return $query;

                    $query->whereHas('inscricao.evento', fn (Builder $query) => $query->whereDate('dt_evento', '=',$dtEvento));
                    return $query;
                }),
                SelectFilter::make('evento')
                    ->relationship('inscricao.evento', 'titulo'),
                SelectFilter::make('status')
                    ->label('Situação')
                    ->relationship('status', 'id')
                    ->getOptionLabelFromRecordUsing(fn (StatusInscricao $record): string => $record->status->toString()),
                Filter::make('f_inscrito')->form([
                    TextInput::make('finpt_inscrito')->label("Nome do Inscrito")
                ])->query(function (Builder $query, array $data): Builder {
                    $nomeInscrito = $data['finpt_inscrito'];
                    if (!$nomeInscrito) return $query;

                    $query->whereRelation('inscricao.inscrito', 'name', 'like', "%$nomeInscrito%");
                    return $query;
                })->visible(function () {
                    $user = auth()->user();
                    $permissao = $user->permissao->role;

                    return $permissao != PermissaoEnum::COMUM;
                })
            ])
            ->actions([
            ])
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
            'index' => Pages\ListHistoricoInscricoes::route('/'),
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
                $query->joinRelationship('inscricao')
                    ->where([
                        'inscricoes.usuario_id' => $user->id
                    ]);
                break;
            case PermissaoEnum::ORGANIZADOR:
                // Listando apenas inscrições efetuadas em eventos do organizador
                $query->joinRelationship('inscricao')
                    ->joinRelationship('inscricao.evento')
                    ->where([
                        'eventos.organizador_id' => $user->id
                    ]);
                break;
        }

        return $query;
    }
}
