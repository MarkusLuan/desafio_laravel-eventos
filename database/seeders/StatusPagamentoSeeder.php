<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

use App\Models\StatusPagamento;
use App\Models\Enums\StatusPagamentoEnum;

class StatusPagamentoSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        if (StatusPagamento::exists()) return;

        foreach (StatusPagamentoEnum::cases() as $status) {
            StatusPagamento::insert([
                'status' => $status->name
            ]);
        }
    }
}
