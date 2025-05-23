<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('eventos', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->datetime('dt_evento');
            $table->datetime('dt_cancelamento')->nullable();
            $table->string('titulo', 100);
            $table->string('descricao', 400);
            $table->integer('capacidade');
            $table->integer('idade_min')->nullable();
            $table->float('preco');
            $table->integer('organizador_id')->unsigned();
            $table->integer('endereco_id')->unsigned();
            $table->timestamps();

            $table->foreign('organizador_id')
                ->references('id')
                ->on('usuarios');
            
            $table->foreign('endereco_id')
                ->references('id')
                ->on('enderecos');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('eventos');
    }
};
