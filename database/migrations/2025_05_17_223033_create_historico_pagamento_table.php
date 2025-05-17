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
        Schema::create('historico_pagamento', function (Blueprint $table) {
            $table->id();
            $table->integer('status_pagamento_id')->unsigned();
            $table->integer('pagamento_id')->unsigned();
            $table->timestamps();

            $table->foreign('status_pagamento_id')
                ->references('id')
                ->on('status_pagamento');
            
            $table->foreign('pagamento_id')
                ->references('id')
                ->on('pagamentos');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('historico_pagamentos');
    }
};
