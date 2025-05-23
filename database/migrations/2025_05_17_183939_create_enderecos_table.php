<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

use App\Models\Enums\EstadoEnum;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('enderecos', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->string('logradouro');
            $table->string('bairro');
            $table->string('cidade');
            $table->enum('uf', array(
                array_column(EstadoEnum::cases(), 'name')
            ));
            $table->string('cep', 11);
            $table->integer('numero')->nullable();
            $table->string('complemento')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('enderecos');
    }
};
