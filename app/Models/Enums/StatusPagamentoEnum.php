<?php

namespace App\Models\Enums;

use Illuminate\Support\Str;

enum StatusPagamentoEnum {
    case APROVADO;
    case EM_PROCESSAMENTO;
    case EXTORNADO;
    case CANCELADO;
    case RECUSADO;

    function toString(): String {
        return Str::title(
            str_replace('_', ' ', $this->name)
        );
    }
}