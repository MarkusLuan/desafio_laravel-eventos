<?php

namespace App\Models\Enums;

use Illuminate\Support\Str;

enum StatusInscricaoEnum {
    case INSCRITO;
    case ESPERANDO_PAGAMENTO;
    case CANCELADO;

    function to_string() {
        return Str::title(
            str_replace('_', ' ', $this->name)
        );
    }
}