<?php

namespace App\Models\Enums;

use Illuminate\Support\Str;

enum MetodoPagamentoEnum {
    case BOLETO;
    case CARTAO_DEBITO;
    case CARTAO_CREDITO;
    case PIX;

    function to_string() {
        return Str::title(
            str_replace('_', ' ', $this->name)
        );
    }
}