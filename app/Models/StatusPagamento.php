<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

use App\Models\Enums\StatusPagamentoEnum;

class StatusPagamento extends Model
{
    protected $table = "status_pagamento";

    function casts() {
        return [
            'status' => StatusPagamentoEnum::class
        ];
    }
}
