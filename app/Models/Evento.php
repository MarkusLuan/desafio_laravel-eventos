<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Evento extends Model
{
    protected $fillable = [
        'titulo',
        'descricao',
        'capacidade',
        'idade_min',
        'preco',
        'dt_evento',
        'dt_cancelamento',
        'endereco_id'
    ];

    protected static function booted() {
        static::creating(function ($model) {
            if (auth()->check()) {
                $model->organizador_id = auth()->id();
                $model->uuid = Str::uuid();
            }
        });
    }


    public function organizador() {
        return $this->hasOne(Usuario::class);
    }

    public function endereco() {
        return $this->belongsTo(Endereco::class);
    }

    public function __toString()
    {
        return "#$this->id - $this->titulo";
    }

    public function getDisplayNameAttribute() {
        return (String) $this;
    }

    protected function casts () {
        return [
            'dt_evento' => 'datetime',
            'dt_cancelamento' => 'datetime'
        ];
    }
}
