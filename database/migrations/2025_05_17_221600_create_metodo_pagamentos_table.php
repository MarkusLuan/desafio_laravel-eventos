<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

use App\Models\Enums\MetodoPagamentoEnum;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('metodo_pagamento', function (Blueprint $table) {
            $table->id();
            $table->enum('metodo', array(
                array_column(MetodoPagamentoEnum::cases(), 'name')
            ));
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('metodo_pagamentos');
    }
};
