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
        Schema::create('status_pagamento', function (Blueprint $table) {
            $table->id();
            $table->enum('status', array(
                'APROVADO',
                'EM_PROCESSAMENTO',
                'EXTORNADO',
                'CANCELADO'
            ));
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('status_pagamento');
    }
};
