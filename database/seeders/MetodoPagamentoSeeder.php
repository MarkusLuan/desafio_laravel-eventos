<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

use App\Models\MetodoPagamento;
use App\Models\Enums\MetodoPagamentoEnum;

class MetodoPagamentoSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        if (MetodoPagamento::exists()) return;

        foreach (MetodoPagamentoEnum::cases() as $metodo) {
            MetodoPagamento::insert([
                'metodo' => $metodo->name
            ]);
        }
    }
}
