<?php

namespace App\Filament\Resources\InscricaoResource\Actions;

use App\Models\Enums\MetodoPagamentoEnum;
use App\Models\Enums\PermissaoEnum;
use Closure;
use Filament\Actions\Concerns\CanCustomizeProcess;
use Filament\Tables\Actions\Action;
use Filament\Tables\Table;

use App\Models\Enums\StatusInscricaoEnum;
use App\Models\Enums\StatusPagamentoEnum;
use App\Models\HistoricoInscricao;
use App\Models\HistoricoPagamento;
use App\Models\Inscricao;
use App\Models\MetodoPagamento;
use App\Models\Pagamento;
use App\Models\StatusInscricao;
use App\Models\StatusPagamento;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Forms\Get;
use Illuminate\Support\HtmlString;

class PagarInscricaoAction extends Action
{
    use CanCustomizeProcess;

    protected ?Closure $mutateRecordDataUsing = null;

    public static function getDefaultName(): ?string
    {
        $user = auth()->user();
        $permissao = $user->permissao->role;

        if ($permissao == PermissaoEnum::ORGANIZADOR) {
            return "Registrar Pagamento";
        }

        return 'Pagar';
    }

    protected function setUp(): void
    {
        parent::setUp();

        $this->icon('heroicon-m-currency-dollar');

        $this->successNotificationTitle("Inscrição paga com sucesso!");

        // $this->requiresConfirmation()
        //     ->modalHeading('Cancelar esta inscrição para o evento?')
        //     ->modalDescription('Tem certeza, que deseja cancelar esta inscrição para o evento? (Esta ação é irrevesivel!)')
        //     ->modalSubmitActionLabel('Sim, cancelar!');

        $this->modalFooterActionsAlignment('right');
        $this->modalSubmitActionLabel("Realizar Pagamento");

        $this->form(function (Form $form, Inscricao $record) {
            $inscricao = $record;
            $evento = $inscricao->evento;

            $form->columns(2);

            return [
                TextInput::make('evento')
                    ->default($evento->display_name)
                    ->disabled(),
                TextInput::make('preco')
                    ->label("Preço")
                    ->prefix("R$")
                    ->default(number_format($evento->preco, 2, ','))
                    ->disabled(),
                Select::make('metodo_pagamento')
                    ->label("Metodo")
                    ->options(MetodoPagamentoEnum::class)
                    ->columnSpanFull()
                    ->reactive(),
                static::formPix(),
                static::formCartao()
            ];
        });

        $this->action(function () {
            $this->process(function (array $data, Inscricao $record, Table $table) {
                if ($record->status->status != StatusInscricaoEnum::ESPERANDO_PAGAMENTO) {
                    return;
                }

                $listStatusPagamento = StatusPagamento::get();
                $listMetodoPagamento = MetodoPagamento::get();

                $statusPagamentoEmProcessamento = $listStatusPagamento->filter(fn (StatusPagamento $status) => $status->status == StatusPagamentoEnum::EM_PROCESSAMENTO)->values()->first();
                $statusPagamentoAprovado = $listStatusPagamento->filter(fn (StatusPagamento $status) => $status->status == StatusPagamentoEnum::APROVADO)->values()->first();

                // Registra o pagamento
                $pagamento = Pagamento::create([
                    'valor_pago' => $record->evento->preco,
                    'metodo_id' => $listMetodoPagamento->filter(fn (MetodoPagamento $metodo) => $metodo->metodo == MetodoPagamentoEnum::BOLETO)->values()->first()->id,
                    'inscricao_id' => $record->id,
                    'status_pagamento_id' => $statusPagamentoEmProcessamento->id
                ]);

                $historicoPagamento = HistoricoPagamento::create([
                    'pagamento_id' => $pagamento->id,
                    'status_pagamento_id' => $statusPagamentoEmProcessamento->id
                ]);
                
                // Atualiza o pagamento
                sleep(10); // Mock de pagamento
                $pagamento->update([
                    'status_pagamento_id' => $statusPagamentoAprovado->id
                ]);

                $historicoPagamento = HistoricoPagamento::create([
                    'pagamento_id' => $pagamento->id,
                    'status_pagamento_id' => $statusPagamentoAprovado->id
                ]);

                // Atualiza inscrição
                $statusInscricaoId = StatusInscricao::where(
                    'status', StatusInscricaoEnum::INSCRITO
                )->first()->id;

                $record->update([
                    'status_inscricao_id' => $statusInscricaoId
                ]);

                $historicoInscricao = HistoricoInscricao::create([
                    'status_inscricao_id' => $statusInscricaoId,
                    'inscricao_id' => $record->id
                ]);

                $this->success();
            });
        });

        $this->visible(function (Inscricao $record) {
            $user = auth()->user();
            $permissao = $user->permissao->role;

            return ($permissao == PermissaoEnum::COMUM || $permissao == PermissaoEnum::ORGANIZADOR) &&
                $record->status->status == StatusInscricaoEnum::ESPERANDO_PAGAMENTO;
        });
    }
    
    private static function formPix(): Section {
        return Section::make()
            ->schema([
                Placeholder::make('qr_pix')
                    ->content(function ($record): HtmlString {
                        return new HtmlString("<img src= '" . url('images/qr_code_pix.png') . "')>");
                    })
            ])->visible(function (Get $get) {
                return $get("metodo_pagamento") == MetodoPagamentoEnum::PIX->name;
            });
    }

    private static function formCartao(): Section {
        $anoVencimentoPadrao = date_format(date_create('now'), 'Y');

        return Section::make()
            ->schema([
                TextInput::make('numero')
                    ->label("Número do Cartão")
                    ->length(16)
                    ->numeric()
                    ->required(),
                TextInput::make('titular')
                    ->label("Titular do Cartão")
                    ->required(),
                Select::make('mes_validade')
                    ->label("Mês de vencimento")
                    ->options(range(1, 12))
                    ->required(),
                TextInput::make('ano_validade')
                    ->label("Ano de vencimento")
                    ->numeric()
                    ->minLength(4)
                    ->minValue($anoVencimentoPadrao)
                    ->default($anoVencimentoPadrao)
                    ->required(),
                TextInput::make('cvc')
                    ->label("Código de INsergurança")
                    ->numeric()
                    ->minLength(3)
                    ->maxLength(4)
                    ->required(),
            ])->visible(function (Get $get) {
                return $get("metodo_pagamento") == MetodoPagamentoEnum::CARTAO_CREDITO->name ||
                    $get("metodo_pagamento") == MetodoPagamentoEnum::CARTAO_DEBITO->name;
            });
    }
}
