<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('historico_inscricoes', function (Blueprint $table) {
            $table->id();
            $table->integer('status_inscricao_id')->unsigned();
            $table->integer('inscricao_id')->unsigned();
            $table->timestamps();

            $table->foreign('status_inscricao_id')
                ->references('id')
                ->on('status_inscricao');
            
            $table->foreign('inscricao_id')
                ->references('id')
                ->on('inscricoes');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('historico_inscricoes');
    }
};
