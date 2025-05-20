<?php

namespace App\Console\Commands;

use App\Models\Enums\PermissaoEnum;
use App\Models\Permissao;
use App\Models\Usuario;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Hash;

class MakeFilamentUser extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'make:filament-user';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Comando para criar usuario do filament';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $user = Usuario::create([
            'dt_nascimento' => '1890-01-01',
            'name' => $this->ask('nome'),
            'email' => $this->ask('email'),
            'password' => Hash::make($this->secret('senha')),
            'permissao_id' => Permissao::where('role', '=', PermissaoEnum::ADMINISTRADOR)->first()->id
        ]);


        $this->info("Usuario ($user->nome) criado com sucesso!");
    }
}
