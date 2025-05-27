<?php

namespace Database\Factories;

use App\Models\Endereco;
use App\Models\Enums\EstadoEnum;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Endereco>
 */
class EnderecoFactory extends Factory
{
    protected $model = Endereco::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $ufCases = EstadoEnum::cases();

        return [
            'uuid' => fake()->uuid(),
            'logradouro' => fake()->streetAddress(),
            'bairro' => fake()->country(),
            'cidade' => fake()->city(),
            'uf' => $ufCases[random_int(0, count($ufCases)-1)],
            'cep' => random_int(50000000, 60000000),
            'numero' => random_int(10, 5000),
            'complemento' => fake()->name(),
        ];
    }
}
