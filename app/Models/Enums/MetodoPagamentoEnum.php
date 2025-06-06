<?php

namespace App\Models\Enums;

use Illuminate\Support\Str;

enum MetodoPagamentoEnum {
    case BOLETO;
    case CARTAO_DEBITO;
    case CARTAO_CREDITO;
    case PIX;

    function toString(): String {
        return Str::title(
            str_replace('_', ' ', $this->name)
        );
    }
}