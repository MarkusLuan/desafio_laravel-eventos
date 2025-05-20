<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Inscricao extends Model
{
    protected $table = "inscricoes";

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
