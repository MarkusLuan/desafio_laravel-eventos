<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Pagamento extends Model
{
    function metodo() {
        return $this->belongsTo(MetodoPagamento::class, 'metodo_pagamento_id');
    }

    function inscricao() {
        return $this->belongsTo(Inscricao::class, 'inscricao_id');
    }

    function status() {
        return $this->belongsTo(StatusPagamento::class, 'status_pagamento_id');
    }
}
