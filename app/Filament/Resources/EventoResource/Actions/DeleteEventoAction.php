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
use DateTime;

class DeleteEventoAction extends Action
{
    use CanCustomizeProcess;

    protected ?Closure $mutateRecordDataUsing = null;

    public static function getDefaultName(): ?string
    {
        return 'Cancelar o Evento';
    }

    protected function setUp(): void
    {
        parent::setUp();

        $this->icon('heroicon-m-trash');

        $this->successNotificationTitle("Evento cancelado com sucesso!");

        $this->requiresConfirmation()
            ->modalHeading('Cancelar o Evento?')
            ->modalDescription('Tem certeza, que deseja cancelar o evento? Os usuários não poderam mais se inscrever e os pagamentos serão extornados! (Esta ação é irrevesivel!)')
            ->modalSubmitActionLabel('Sim, cancelar!');

        $this->action(function () {
            $this->process(function (array $data, Evento $record, Table $table) {
                $status_inscricao_id = StatusInscricao::where(
                    'status', StatusInscricaoEnum::CANCELADO
                )->first()->id;

                $inscricoes = Inscricao::where([
                    'evento_id' => $record->id,
                ])->where(
                    'status_inscricao_id', '!=', $status_inscricao_id
                )->orderByDesc('created_at')->get();

                foreach ($inscricoes as $inscricao) {
                    $inscricao->update([
                        'status_inscricao_id' => $status_inscricao_id
                    ]);

                    $historico = HistoricoInscricao::create([
                        'status_inscricao_id' => $status_inscricao_id,
                        'inscricao_id' => $inscricao->id
                    ]);
                }

                $record->update([
                    'dt_cancelamento' => new DateTime('now')
                ]);

                $this->success();
            });
        });
    }
}
