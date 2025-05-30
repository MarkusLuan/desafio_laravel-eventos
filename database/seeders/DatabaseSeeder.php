<?php

namespace Database\Seeders;

use App\Models\Endereco;
use App\Models\Usuario;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Usuario::factory(10)->create();

        // Usuario::factory()->create([
        //     'nome' => 'Test Usuario',
        //     'email' => 'test@example.com',
        // ]);

        $this->call([
            MetodoPagamentoSeeder::class,
            PermissoesSeeder::class,
            StatusInscricaoSeeder::class,
            StatusPagamentoSeeder::class,
        ]);
        
        Usuario::factory(10)->create();
        Endereco::factory(30)->create();
    }
}
