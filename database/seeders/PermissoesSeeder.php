<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

use App\Models\Permissao;
use App\Models\Enums\PermissaoEnum;

class PermissoesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        if (Permissao::exists()) return;

        foreach (PermissaoEnum::cases() as $permissao) {
            Permissao::insert([
                "uuid" => Str::uuid(),
                'role' => $permissao->name
            ]);
        }
    }
}
