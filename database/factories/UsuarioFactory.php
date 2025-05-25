<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

use App\Models\Permissao;
use App\Models\Enums\PermissaoEnum;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Usuario>
 */
class UsuarioFactory extends Factory
{
    /**
     * The current password being used by the factory.
     */
    protected static ?string $senha;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {   
        $email = fake()->email();
        $senha = Str::random(12);

        print ("Usário criado $email => $senha\n");

        return [
            'uuid' => fake()->uuid(),
            'dt_nascimento' => fake()->date(),
            'name' => fake()->name(),
            'email' => $email,
            'email_verified_at' => now(),
            'password' => Hash::make($senha),
            'remember_token' => Str::random(10),
            'permissao_id' => Permissao::where('role', '=',
                array_column(
                    PermissaoEnum::cases(), 'name'
                )[random_int(0, 1)]
            )->first()->id,
        ];
    }

    /**
     * Indicate that the model's email address should be unverified.
     */
    public function unverified(): static
    {
        return $this->state(fn (array $attributes) => [
            'email_verified_at' => null,
        ]);
    }
}
