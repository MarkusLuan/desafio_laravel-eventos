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
        Schema::create('inscricoes', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->default(str::uuid())->unique();
            $table->integer('evento_id')->unsigned();
            $table->integer('usuario_id')->unsigned();
            $table->integer('status_inscricao_id')->unsigned();
            $table->timestamps();

            $table->foreign('evento_id')
                ->references('id')
                ->on('eventos');
            
            $table->foreign('usuario_id')
                ->references('id')
                ->on('usuarios');
            
            $table->foreign('status_inscricao_id')
                ->references('id')
                ->on('status_inscricao');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('inscricoes');
    }
};
