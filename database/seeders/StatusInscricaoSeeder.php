<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

use App\Models\StatusInscricao;
use App\Models\Enums\StatusInscricaoEnum;

class StatusInscricaoSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        if (StatusInscricao::exists()) return;

        foreach (StatusInscricaoEnum::cases() as $status) {
            StatusInscricao::insert([
                'status' => $status->name
            ]);
        }
    }
}
