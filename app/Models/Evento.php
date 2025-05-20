<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Evento extends Model
{
    public function organizador() {
        return $this->belongsTo(Usuario::class, 'organizador_id');
    }

    public function endereco() {
        return $this->belongsTo(Endereco::class, 'endereco_id');
    }
}
