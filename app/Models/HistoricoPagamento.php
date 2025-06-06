<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class HistoricoPagamento extends Model
{
    protected $table = "historico_pagamento";

    protected $fillable = [
        'pagamento_id',
        'status_pagamento_id'
    ];

    function status () {
        return $this->belongsTo(StatusPagamento::class, 'status_pagamento_id');
    }

    function pagamento () {
        return $this->belongsTo(Pagamento::class, 'pagamento_id');
    }
}
