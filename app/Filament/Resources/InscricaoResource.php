<?php

namespace App\Filament\Resources;

use App\Filament\Resources\InscricaoResource\Actions\DeleteInscricaoAction;
use App\Filament\Resources\InscricaoResource\Actions\PagarInscricaoAction;
use App\Filament\Resources\InscricaoResource\Pages;
use Filament\Resources\Resource;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\DateTimePicker;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\SelectFilter;
use Illuminate\Database\Eloquent\Builder;

use App\Models\Enums\PermissaoEnum;
use App\Models\Enums\StatusInscricaoEnum;
use App\Models\Inscricao;
use App\Models\StatusInscricao;
use DateTime;

class InscricaoResource extends Resource
{
    protected static ?string $model = Inscricao::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    protected static ?string $label = 'Inscrições';
    protected static ?string $navigationLabel = 'Inscrições';

    public static function getRoutePrefix(): string
    {
        return "/inscricoes";
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('updated_at')
                    ->label('Data Inscrição')
                    ->datetime('d/m/Y \à\s H:i', 'america/recife'),
                TextColumn::make('evento.display_name')
                    ->label('Evento'),
                TextColumn::make('evento.dt_evento')
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
                TextColumn::make('inscrito.name')
                    ->label('Usuário')
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

                    $query->whereHas('evento', fn (Builder $query) => $query->whereDate('dt_evento', '=', $dtEvento));
                    return $query;
                }),
                SelectFilter::make('evento')
                    ->relationship('evento', 'titulo'),
                SelectFilter::make('status')
                    ->label('Situação')
                    ->relationship('status', 'id')
                    ->getOptionLabelFromRecordUsing(fn (StatusInscricao $record): string => $record->status->toString()),
                Filter::make('f_inscrito')->form([
                    TextInput::make('finpt_inscrito')->label("Nome do Inscrito")
                ])->query(function (Builder $query, array $data): Builder {
                    $nomeInscrito = $data['finpt_inscrito'];
                    if (!$nomeInscrito) return $query;

                    $query->whereRelation('inscrito', 'name', 'like', "%$nomeInscrito%");
                    return $query;
                })->visible(function () {
                    $user = auth()->user();
                    $permissao = $user->permissao->role;

                    return $permissao != PermissaoEnum::COMUM;
                })
            ])
            ->actions([
                DeleteInscricaoAction::make()
                    ->visible(function (Inscricao $record) {
                        $user = auth()->user();
                        $permissao = $user->permissao->role;

                        $isPodeCancelar = false;

                        $tempo_para_evento = $record->evento->dt_evento->diff(new DateTime('now'));
                        
                        $isPodeCancelar = $record->status->status != StatusInscricaoEnum::CANCELADO;

                        if ($permissao == PermissaoEnum::COMUM) {
                            $isPodeCancelar = $isPodeCancelar && (
                                $tempo_para_evento->d > 1 ||
                                ($tempo_para_evento->d == 1 && $tempo_para_evento->h >= 5)
                            );
                        }

                        return $isPodeCancelar;
                    }),
                PagarInscricaoAction::make()
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
            'index' => Pages\ListInscricoes::route('/'),
        ];
    }

    public static function getEloquentQuery(): Builder
    {
        $query = parent::getEloquentQuery();

        $user = auth()->user();
        $permissao = $user->permissao->role;

        switch ($permissao) {
            case PermissaoEnum::ORGANIZADOR:
                // Listando apenas inscrições de eventos criados pelo organizador
                $query->whereRelation('evento', 'organizador_id', '=', $user->id);
                break;

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
