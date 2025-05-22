<?php

namespace App\Filament\Resources;

use App\Filament\Resources\PagamentoResource\Pages;
use App\Filament\Resources\PagamentoResource\RelationManagers;
use App\Models\Enums\MetodoPagamentoEnum;
use App\Models\Enums\StatusPagamentoEnum;
use App\Models\HistoricoPagamento;
use App\Models\MetodoPagamento;
use App\Models\Pagamento;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class PagamentoResource extends Resource
{
    protected static ?string $model = HistoricoPagamento::class;

    protected static ?string $label = 'Pagamentos';
    protected static ?string $navigationLabel = 'Pagamentos';
    protected static ?string $navigationIcon = 'heroicon-o-document-currency-dollar';

    public static function canCreate(): bool { return false; }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('pagamento.uuid')
                    ->label('Código'),
                TextColumn::make('created_at')
                    ->datetime('d/m/Y \à\s H:i')
                    ->label('Data do Histórico'),
                TextColumn::make('pagamento.valor_pago')
                    ->money('BRL')
                    ->label("Valor Pago"),
                TextColumn::make('status.status')
                    ->badge()
                    ->colors([
                        'success' => static fn ($state): bool => $state == StatusPagamentoEnum::APROVADO,
                        'warning' => static fn ($state): bool => $state == StatusPagamentoEnum::EM_PROCESSAMENTO,
                        'danger' => static function ($state): bool {
                            return $state == StatusPagamentoEnum::CANCELADO or
                                $state == StatusPagamentoEnum::EXTORNADO;
                        }
                    ])
                    ->formatStateUsing(fn ($state, $record) => $state->toString())
                    ->label('Status'),
                TextColumn::make('pagamento.metodo.metodo')
                    ->badge()
                    ->colors([
                        'success' => static fn ($state): bool => $state == MetodoPagamentoEnum::BOLETO,
                        'primary' => static fn ($state): bool => $state == MetodoPagamentoEnum::PIX,
                        'danger' => static function ($state): bool {
                            return $state == MetodoPagamentoEnum::CARTAO_CREDITO or
                                $state == MetodoPagamentoEnum::CARTAO_DEBITO;
                        }
                    ])
                    ->formatStateUsing(fn ($state, $record) => $state->toString())
                    ->label('Metodo'),
                TextColumn::make('pagamento.inscricao.evento')
                    ->formatStateUsing(fn ($state, $record) => "#$state->id - $state->titulo")
                    ->label("Evento"),
                TextColumn::make('pagamento.inscricao.evento.dt_evento')
                    ->datetime('d/m/Y \à\s H:i')
                    ->label("Data do Evento"),
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
            'index' => Pages\ListPagamentos::route('/'),
        ];
    }
}
