<?php

namespace App\Filament\Widgets;

use App\Filament\Resources\EventoResource;
use App\Models\Enums\PermissaoEnum;
use App\Models\Enums\StatusInscricaoEnum;
use App\Models\Evento;
use App\Models\Inscricao;
use App\Models\Permissao;
use Illuminate\Database\Eloquent\Model;
use Saade\FilamentFullCalendar\Data\EventData;
use Saade\FilamentFullCalendar\Widgets\FullCalendarWidget;

class CalendarioEventosWidget extends FullCalendarWidget
{

    public Model|string|null $model = Evento::class;

    protected function headerActions(): array
    {
        return [];
    }

    protected function modalActions(): array
    {
        return [];
    }

    public function fetchEvents(array $info): array
    {
        $user = auth()->user();
        $permissao = $user->permissao->role;

        $eventosQuery = Evento::
            where('dt_evento', '>=', $info['start'])
            ->where('dt_evento', '<=', $info['end']);
        
        switch ($permissao) {
            case PermissaoEnum::ORGANIZADOR:
                $eventosQuery->where([
                    'eventos.organizador_id' => Auth()->id(),
                ]);
                break;
            case PermissaoEnum::COMUM:
                $eventosQuery->leftJoin('inscricoes', 'inscricoes.evento_id', '=', 'eventos.id')
                    ->where(function ($query) {
                        $query->where('inscricoes.usuario_id', Auth()->id())
                            ->whereNotNull('eventos.dt_cancelamento')
                            ->whereNull('inscricoes.id');
                    })
                    ->orWhereNull('eventos.dt_cancelamento')
                    ->distinct()
                    ->select('eventos.*');
                break;
        }

        return $eventosQuery->get()->map(function (Evento $evento) use ($permissao) {
            $cor = 'orange';

            if ($evento->dt_cancelamento) {
                $cor = 'red';
            } else if ($permissao == PermissaoEnum::COMUM) {
                $isInscrito = Inscricao::
                    whereRelation('status', [
                        'status' => StatusInscricaoEnum::INSCRITO
                    ])->where([
                        'usuario_id' => Auth()->id(),
                        'evento_id' => $evento->id
                    ])->exists();

                if ($isInscrito) {
                    $cor = 'green';
                }
            }

            return EventData::make()
                ->id($evento->uuid)
                ->title($evento->titulo)
                ->backgroundColor($cor)
                ->start($evento->dt_evento)
                ->end($evento->dt_evento)
                ->url(
                    url: EventoResource::getUrl(name: 'view', parameters: ['record' => $evento]),
                    shouldOpenUrlInNewTab: true
                );
        })->toArray();
    }
}
