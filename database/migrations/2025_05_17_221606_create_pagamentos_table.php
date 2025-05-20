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
        Schema::create('pagamentos', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->default(str::uuid())->unique();
            $table->float('valor_pago');
            $table->integer('metodo_id')->unsigned();
            $table->integer('inscricao_id')->unsigned();
            $table->integer('status_pagamento_id')->unsigned();
            $table->timestamps();

            $table->foreign('metodo_id')
                ->references('id')
                ->on('metodo_pagamento');
            
            $table->foreign('inscricao_id')
                ->references('id')
                ->on('inscricoes');
            
            $table->foreign('status_pagamento_id')
                ->references('id')
                ->on('status_pagamento');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pagamentos');
    }
};
