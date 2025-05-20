<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

use App\Models\Enums\StatusInscricaoEnum;

class StatusInscricao extends Model
{
    protected $table = "status_inscricao";

    function casts() {
        return [
            'status' => StatusInscricaoEnum::class
        ];
    }
}
