<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

use App\Models\Enums\StatusInscricaoEnum;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('status_inscricao', function (Blueprint $table) {
            $table->id();
            $table->enum('status', array(
                array_column(StatusInscricaoEnum::cases(), 'name')
            ));
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('status_inscricao');
    }
};
