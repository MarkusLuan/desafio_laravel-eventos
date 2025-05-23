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

class InscreverEventoAction extends Action
{
    use CanCustomizeProcess;

    protected ?Closure $mutateRecordDataUsing = null;

    public static function getDefaultName(): ?string
    {
        return 'inscrever';
    }

    protected function setUp(): void
    {
        parent::setUp();

        $this->icon('heroicon-m-arrow-right-end-on-rectangle');

        $this->successNotificationTitle("Inscrito com sucesso!");

        $this->action(function () {
            $this->process(function (array $data, Evento $record, Table $table) {
                $status_inscricao_id = StatusInscricao::where(
                    'status', StatusInscricaoEnum::ESPERANDO_PAGAMENTO
                )->first()->id;

                $inscricao = Inscricao::create(array(
                    'evento_id' => $record->id,
                    'status_inscricao_id' => $status_inscricao_id
                ));

                $historico = HistoricoInscricao::create(array(
                    'status_inscricao_id' => $status_inscricao_id,
                    'inscricao_id' => $inscricao->id
                ));
            });

            $this->success();
        });
    }
}
