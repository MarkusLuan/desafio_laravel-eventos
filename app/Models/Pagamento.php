<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Pagamento extends Model
{
    protected $fillable = [
        'valor_pago',
        'metodo_id',
        'inscricao_id',
        'status_pagamento_id'
    ];

    protected static function booted() {
        static::creating(function ($model) {
            $model->uuid = Str::uuid();
        });
    }

    function metodo() {
        return $this->belongsTo(MetodoPagamento::class, 'metodo_id');
    }

    function inscricao() {
        return $this->belongsTo(Inscricao::class, 'inscricao_id');
    }

    function status() {
        return $this->belongsTo(StatusPagamento::class, 'status_pagamento_id');
    }

    public function getRouteKeyName(): string {
        return 'uuid';
    }
}
