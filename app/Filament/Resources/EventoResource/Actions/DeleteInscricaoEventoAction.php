<?php

namespace App\Filament\Resources\EventoResource\Actions;

use Closure;
use Filament\Actions\Concerns\CanCustomizeProcess;
use Filament\Tables\Actions\Action;
use Filament\Tables\Table;

use App\Models\Enums\StatusInscricaoEnum;
use App\Models\Enums\StatusPagamentoEnum;
use App\Models\Evento;
use App\Models\HistoricoInscricao;
use App\Models\HistoricoPagamento;
use App\Models\Inscricao;
use App\Models\Pagamento;
use App\Models\StatusInscricao;
use App\Models\StatusPagamento;

class DeleteInscricaoEventoAction extends Action
{
    use CanCustomizeProcess;

    protected ?Closure $mutateRecordDataUsing = null;

    public static function getDefaultName(): ?string
    {
        return 'Cancelar Inscrição';
    }

    protected function setUp(): void
    {
        parent::setUp();

        $this->icon('heroicon-m-arrow-left-start-on-rectangle');

        $this->successNotificationTitle("Inscrição cancelada com sucesso!");

        $this->requiresConfirmation()
            ->modalHeading('Cancelar a sua inscrição para o evento?')
            ->modalDescription('Tem certeza, que deseja cancelar a sua inscrição para o evento? (Esta ação é irrevesivel!)')
            ->modalSubmitActionLabel('Sim, cancelar!');

        $this->action(function () {
            $this->process(function (array $data, Evento $record, Table $table) {
                $statusInscricaoId = StatusInscricao::where(
                    'status', StatusInscricaoEnum::CANCELADO
                )->first()->id;

                $inscricao = Inscricao::where([
                    'usuario_id' => auth()->id(),
                    'evento_id' => $record->id,
                ])->orderByDesc('created_at')->first();

                if (!$inscricao or $inscricao->status_inscricao_id == $statusInscricaoId) {
                    return;
                }

                $inscricao->update([
                    'status_inscricao_id' => $statusInscricaoId
                ]);

                $historico = HistoricoInscricao::create([
                    'status_inscricao_id' => $statusInscricaoId,
                    'inscricao_id' => $inscricao->id
                ]);

                // Extornar pagamento
                $listStatusPagamento = StatusPagamento::get();
                $statusPagamentoCancelado = $listStatusPagamento->filter(fn (StatusPagamento $status) => $status->status == StatusPagamentoEnum::CANCELADO)->values()->first();
                $statusPagamentoExtornado = $listStatusPagamento->filter(fn (StatusPagamento $status) => $status->status == StatusPagamentoEnum::EXTORNADO)->values()->first();

                $pagamento = Pagamento::where([
                    'inscricao_id' => $inscricao->id,
                ])->first();

                if (!$pagamento) return;

                $statusPagamento = $statusPagamentoExtornado;
                if ($pagamento->status->status == StatusPagamentoEnum::EM_PROCESSAMENTO) $statusPagamento = $statusPagamentoCancelado;
                
                $pagamento->update([
                    'status_pagamento_id' => $statusPagamento->id
                ]);

                $historicoPagamento = HistoricoPagamento::create([
                    'pagamento_id' => $pagamento->id,
                    'status_pagamento_id' => $statusPagamento->id
                ]);

                $this->success();
            });
        });
    }
}
