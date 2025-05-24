<?php

namespace App\Filament\Resources\EventoResource\Actions;

use Closure;
use Filament\Actions\Concerns\CanCustomizeProcess;
use Filament\Tables\Actions\Action;
use Filament\Tables\Table;

use App\Models\Enums\StatusInscricaoEnum;
use App\Models\Evento;
use App\Models\HistoricoInscricao;
use App\Models\Inscricao;
use App\Models\StatusInscricao;

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

        $this->action(function () {
            $this->process(function (array $data, Evento $record, Table $table) {
                $status_inscricao_id = StatusInscricao::where(
                    'status', StatusInscricaoEnum::CANCELADO
                )->first()->id;

                $inscricao = Inscricao::where([
                    'usuario_id' => auth()->id(),
                    'evento_id' => $record->id,
                ])->orderByDesc('created_at')->first();

                if (!$inscricao or $inscricao->status_inscricao_id == $status_inscricao_id) {
                    return;
                }

                $inscricao->update([
                    'status_inscricao_id' => $status_inscricao_id
                ]);

                $historico = HistoricoInscricao::create([
                    'status_inscricao_id' => $status_inscricao_id,
                    'inscricao_id' => $inscricao->id
                ]);

                $this->success();
            });
        });
    }
}
