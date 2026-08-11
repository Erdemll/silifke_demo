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
        Schema::create('yorumlar', function (Blueprint $table) {
            $table->id();
            $table->foreignId('urun_id')
                ->index()
                ->constrained('urunler')
                ->cascadeOnDelete();
            $table->foreignId('kullanici_id')
                ->index()
                ->constrained('kullanicilar')
                ->cascadeOnDelete();
            $table->text('metin');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('yorumlar');
    }
};
