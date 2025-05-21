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
        return "$this->logradouro, $this->bairro, $this->cidade/$this->uf";
    }
}
