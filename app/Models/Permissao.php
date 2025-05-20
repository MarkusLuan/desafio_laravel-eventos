<?php

namespace App\Models;

use App\Models\Enums\PermissaoEnum;
use Illuminate\Database\Eloquent\Model;

class Permissao extends Model
{
    protected $table = "permissoes";

    function casts() {
        return [
            'role' => PermissaoEnum::class
        ];
    }
}
