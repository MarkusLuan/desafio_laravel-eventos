<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class HistoricoInscricao extends Model
{
    protected $table = "historico_inscricoes";

    function status () {
        return $this->belongsTo(StatusInscricao::class, 'status_inscricao_id');
    }

    function inscricao () {
        return $this->belongsTo(Inscricao::class, 'inscricao_id');
    }
}
