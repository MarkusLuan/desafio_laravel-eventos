<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

use App\Models\Enums\PermissaoEnum;

class Permissao extends Model
{
    protected $table = "permissoes";

    function casts() {
        return [
            'role' => PermissaoEnum::class
        ];
    }
}
