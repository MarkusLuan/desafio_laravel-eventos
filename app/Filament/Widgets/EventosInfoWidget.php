<?php

namespace App\Filament\Widgets;

use App\Models\Enums\PermissaoEnum;
use App\Models\Enums\StatusInscricaoEnum;
use App\Models\Enums\StatusPagamentoEnum;
use App\Models\Evento;
use App\Models\Inscricao;
use App\Models\Pagamento;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class EventosInfoWidget extends BaseWidget
{
    public static function canView(): bool
    {
        $user = auth()->user();
        $permissao = $user->permissao->role;

        return $permissao != PermissaoEnum::COMUM;
    }

    protected function getStats(): array
    {
        $user = auth()->user();
        $permissao = $user->permissao->role;

        $eventoWhere = [];

        if ($permissao == PermissaoEnum::ORGANIZADOR) {
            $eventoWhere = [
                'eventos.organizador_id' => Auth()->id()
            ];
        }

        $quantEventos = Evento::where([
            'dt_cancelamento' => null,
            ...$eventoWhere
        ])->count();

        $quantInscritos = Inscricao::whereRelation('evento', $eventoWhere)->whereRelation('status', [
            'status' => StatusInscricaoEnum::INSCRITO
        ])->count();

        $quantPreInscricoes = Inscricao::whereRelation('evento', $eventoWhere)->whereRelation('status', [
            'status' => StatusInscricaoEnum::ESPERANDO_PAGAMENTO
        ])->count();

        $valorRecebido = Pagamento::whereRelation('inscricao.evento', $eventoWhere)->whereRelation('status', [
            'status' => StatusPagamentoEnum::APROVADO
        ])->sum('valor_pago');
        
        $valorDevolvido = Pagamento::whereRelation('inscricao.evento', $eventoWhere)->whereRelation('status', [
            'status' => StatusPagamentoEnum::EXTORNADO
        ])->sum('valor_pago');

        $idEventoComMaisInscritos = Inscricao::whereRelation('evento', $eventoWhere)->whereRelation('status', [
            'status' => StatusInscricaoEnum::INSCRITO
        ])->max('evento_id');

        $eventoComMaisInscritos = null;
        if ($idEventoComMaisInscritos > 0) {
            $eventoComMaisInscritos = Evento::where([
                'id' => $idEventoComMaisInscritos,
                ...$eventoWhere
            ])->first()->display_name;
        }

        return [
            Stat::make('Eventos', $quantEventos),
            Stat::make('Evento Com mais Inscrito', $eventoComMaisInscritos),
            Stat::make('Pré-inscrições', $quantPreInscricoes),
            Stat::make('Total de Inscritos', $quantInscritos),
            Stat::make('Total de Recebido', 'R$ ' . number_format($valorRecebido, 2, ',', '.')),
            Stat::make('Total de Extornado', 'R$ ' . number_format($valorDevolvido, 2, ',', '.')),
        ];
    }
}
