<?php

namespace App\Models;

use App\Models\Enums\StatusInscricaoEnum;
use Illuminate\Database\Eloquent\Model;

class StatusInscricao extends Model
{
    protected $table = "status_inscricao";

        function casts() {
        return [
            'status' => StatusInscricaoEnum::class
        ];
    }
}
