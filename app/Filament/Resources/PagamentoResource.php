<?php

namespace App\Filament\Resources;

use App\Filament\Resources\PagamentoResource\Pages;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\Summarizers\Sum;
use Filament\Tables\Columns\Summarizers\Summarizer;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

use App\Models\Enums\MetodoPagamentoEnum;
use App\Models\Enums\PermissaoEnum;
use App\Models\Enums\StatusPagamentoEnum;
use App\Models\Pagamento;
use App\Models\StatusPagamento;

class PagamentoResource extends Resource
{
    protected static ?string $model = Pagamento::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('uuid')
                    ->label('Código da Transação'),
                TextColumn::make('updated_at')
                    ->label('Data do Pagamento')
                    ->datetime('d/m/Y \à\s H:i', 'america/recife'),
                TextColumn::make('valor_pago')
                    ->label("Valor Pago")
                    ->money('BRL')
                    ->summarize(
                        Summarizer::make()->using(function ($query) {
                            $statusPagamentoId = StatusPagamento::where([
                                'status' => StatusPagamentoEnum::APROVADO
                            ])->first()->id;

                            $query->where([
                                'status_pagamento_id' => $statusPagamentoId
                            ]);

                            return $query->sum('valor_pago');
                        })
                        ->label(function () {
                            $user = auth()->user();
                            $permissao = $user->permissao->role;
                            $texto = 'Valor Total';
                            
                            switch ($permissao) {
                                case PermissaoEnum::ORGANIZADOR:
                                    $texto .= ' Recebido';
                                    break;
                                    case PermissaoEnum::COMUM:
                                        $texto .= ' Pago';
                                        break;
                                    }
                                    
                                    return $texto;
                            }
                        )->money('BRL')
                    ),
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
                TextColumn::make('metodo.metodo')
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
                TextColumn::make('inscricao.evento.display_name')
                    ->label("Evento"),
                TextColumn::make('inscricao.evento.dt_evento')
                    ->label("Data do Evento")
                    ->datetime('d/m/Y \à\s H:i'),
                TextColumn::make('inscricao.inscrito.name')
                    ->label('Inscrito')
                    ->visible(function () {
                        $user = auth()->user();
                        $permissao = $user->permissao->role;

                        return $permissao != PermissaoEnum::COMUM;
                    })
            ])
            ->filters([
                //
            ])
            ->actions([
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([]),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListPagamentos::route('/')
        ];
    }

    public static function getEloquentQuery(): Builder
    {
        $query = parent::getEloquentQuery();

        $user = auth()->user();
        $permissao = $user->permissao->role;

        switch ($permissao) {
            case PermissaoEnum::COMUM:
                // Listando apenas pagamentos efetuadas pelo usuário logado - Usuario COMUM
                $query->joinRelationship('inscricao')
                ->where([
                    'inscricoes.usuario_id' => $user->id
                ]);

                break;
            case PermissaoEnum::ORGANIZADOR:
                // Listando apenas pagamentos efetuadas nos eventos do organizador
                $query->joinRelationship('inscricao.evento')
                ->where([
                    'eventos.organizador_id' => $user->id
                ]);
                
                break;
        }

        return $query;
    }
}
