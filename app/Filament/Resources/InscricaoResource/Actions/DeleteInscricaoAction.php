<?php

namespace App\Filament\Resources\InscricaoResource\Actions;

use Closure;
use Filament\Actions\Concerns\CanCustomizeProcess;
use Filament\Tables\Actions\Action;
use Filament\Tables\Table;

use App\Models\Enums\StatusInscricaoEnum;
use App\Models\HistoricoInscricao;
use App\Models\Inscricao;
use App\Models\StatusInscricao;

class DeleteInscricaoAction extends Action
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
            ->modalHeading('Cancelar esta inscrição para o evento?')
            ->modalDescription('Tem certeza, que deseja cancelar esta inscrição para o evento? (Esta ação é irrevesivel!)')
            ->modalSubmitActionLabel('Sim, cancelar!');

        $this->action(function () {
            $this->process(function (array $data, Inscricao $record, Table $table) {
                $status_inscricao_id = StatusInscricao::where(
                    'status', StatusInscricaoEnum::CANCELADO
                )->first()->id;

                $record->update([
                    'status_inscricao_id' => $status_inscricao_id
                ]);

                $historico = HistoricoInscricao::create([
                    'status_inscricao_id' => $status_inscricao_id,
                    'inscricao_id' => $record->id
                ]);

                $this->success();
            });
        });
    }
}
