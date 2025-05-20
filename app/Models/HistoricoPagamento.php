<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class HistoricoPagamento extends Model
{
    protected $table = "historico_pagamentos";

    function status () {
        return $this->belongsTo(StatusPagamento::class, 'status_id');
    }

    function inscricao () {
        return $this->belongsTo(Inscricao::class, 'inscricao_id');
    }
}
