<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Evento extends Model
{
    protected $fillable = [
        'titulo',
        'descricao',
        'capacidade',
        'idade_min',
        'preco',
        'dt_evento'
    ];

    protected static function booted() {
        static::creating(function ($model) {
            if (auth()->check()) {
                $model->organizador_id = auth()->id();
            }
        });
    }


    public function organizador() {
        return $this->belongsTo(Usuario::class, 'organizador_id');
    }

    public function endereco() {
        return $this->belongsTo(Endereco::class, 'endereco_id');
    }
}
