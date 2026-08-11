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
        Schema::create('urun_resimler', function (Blueprint $table) {
            $table->id();
            $table->foreignId('urun_id')
                ->index()
                ->constrained('urunler')
                ->cascadeOnDelete();
            $table->string('yol');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('urun_resimler');
    }
};
