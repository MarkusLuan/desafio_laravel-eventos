<?php

namespace App\Models\Enums;

use Illuminate\Support\Str;

enum PermissaoEnum {
    case COMUM;
    case ORGANIZADOR;
    case ADMINISTRADOR;

    function to_string() {
        return Str::title(
            str_replace('_', ' ', $this->name)
        );
    }
}