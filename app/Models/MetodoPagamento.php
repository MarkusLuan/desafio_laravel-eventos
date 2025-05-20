<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

use App\Models\Enums\MetodoPagamentoEnum;

class MetodoPagamento extends Model
{
    protected $table = "metodo_pagamento";

    function casts() {
        return [
            'metodo' => MetodoPagamentoEnum::class
        ];
    }
}
