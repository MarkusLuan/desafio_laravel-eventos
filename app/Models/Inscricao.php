<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Inscricao extends Model
{
    protected $table = "inscricoes";

    protected $fillable = [
        'evento_id',
        'status_inscricao_id',
    ];

    protected static function booted() {
        static::creating(function ($model) {
            if (auth()->check()) {
                $model->usuario_id = auth()->id();
                $model->uuid = Str::uuid();
            }
        });
    }

    function evento () {
        return $this->belongsTo(Evento::class, 'evento_id');
    }
    
    function inscrito () {
        return $this->belongsTo(Usuario::class, 'usuario_id');
    }

    function status () {
        return $this->belongsTo(StatusInscricao::class, 'status_inscricao_id');
    }
}
