<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Endereco extends Model
{
    protected $fillable = [
        'id',
        'cep',
        'logradouro',
        'bairro',
        'cidade',
        'uf',
        'numero',
        'complemento'
    ];

    protected $hidden = [
        'id'
    ];

    protected static function booted() {
        static::creating(function ($model) {
            $model->uuid = Str::uuid();
        });
    }


    public function evento () {
        return $this->hasOne(Evento::class);
    }

    public function __toString(): String {
        $numero_complemento = "";
        if ($this->numero) {
            $numero_complemento .= "Nº $this->numero";
        }

        if ($this->complemento) {
            if ($numero_complemento) $numero_complemento .= " ";
            $numero_complemento .= "$this->complemento";
        }

        return "$this->logradouro, $this->bairro, $numero_complemento, $this->cidade/$this->uf";
    }

    public function getDisplayNameAttribute() {
        return (String) $this;
    }
}
