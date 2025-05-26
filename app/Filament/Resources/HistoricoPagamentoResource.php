<?php

namespace App\Filament\Resources;

use App\Filament\Resources\HistoricoPagamentoResource\Pages;
use Filament\Resources\Resource;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

use App\Models\Enums\MetodoPagamentoEnum;
use App\Models\Enums\PermissaoEnum;
use App\Models\Enums\StatusPagamentoEnum;
use App\Models\HistoricoPagamento;

class HistoricoPagamentoResource extends Resource
{
    protected static ?string $model = HistoricoPagamento::class;

    protected static ?string $navigationIcon = 'heroicon-o-document-currency-dollar';

    public static function canCreate(): bool { return false; }

    public static function getRoutePrefix(): string
    {
        return "/historico/pagamentos";
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('pagamento.uuid')
                    ->label('Código da Transação'),
                TextColumn::make('created_at')
                    ->label('Data do Histórico')
                    ->datetime('d/m/Y \à\s H:i', 'america/recife'),
                TextColumn::make('pagamento.valor_pago')
                    ->label("Valor Pago")
                    ->money('BRL'),
                TextColumn::make('status.status')
                    ->label('Status')
                    ->badge()
                    ->colors([
                        'success' => static fn ($state): bool => $state == StatusPagamentoEnum::APROVADO,
                        'warning' => static fn ($state): bool => $state == StatusPagamentoEnum::EM_PROCESSAMENTO,
                        'danger' => static function ($state): bool {
                            return $state == StatusPagamentoEnum::CANCELADO or
                                $state == StatusPagamentoEnum::EXTORNADO;
                        }
                    ])
                    ->formatStateUsing(fn ($state, $record) => $state->toString()),
                TextColumn::make('pagamento.metodo.metodo')
                    ->label('Metodo')
                    ->badge()
                    ->colors([
                        'success' => static fn ($state): bool => $state == MetodoPagamentoEnum::BOLETO,
                        'primary' => static fn ($state): bool => $state == MetodoPagamentoEnum::PIX,
                        'danger' => static function ($state): bool {
                            return $state == MetodoPagamentoEnum::CARTAO_CREDITO or
                                $state == MetodoPagamentoEnum::CARTAO_DEBITO;
                        }
                    ])
                    ->formatStateUsing(fn ($state, $record) => $state->toString()),
                TextColumn::make('pagamento.inscricao.evento.display_name')
                    ->label("Evento"),
                TextColumn::make('pagamento.inscricao.evento.dt_evento')
                    ->label("Data do Evento")
                    ->datetime('d/m/Y \à\s H:i'),
            ])
            ->filters([
                //
            ])
            ->actions([
            ])
            ->bulkActions([
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListHistoricoPagamentos::route('/'),
        ];
    }

    public static function getEloquentQuery(): Builder
    {
        $query = parent::getEloquentQuery();

        $user = auth()->user();
        $permissao = $user->permissao->role;

        // Listando apenas pagamentos efetuadas pelo usuário logado - Usuario COMUM
        if ($permissao == PermissaoEnum::COMUM) {
            $query->joinRelationship('pagamento.inscricao')
            ->where([
                'inscricoes.usuario_id' => $user->id
            ]);
        }

        return $query;
    }
}
