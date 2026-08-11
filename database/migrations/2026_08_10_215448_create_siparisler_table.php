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
        Schema::create('siparisler', function (Blueprint $table) {
            $table->id();
            $table->foreignId('urun_id')
                ->index()
                ->constrained('urunler')
                ->restrictOnDelete();
            $table->foreignId('kullanici_id')
                ->index()
                ->constrained('kullanicilar')
                ->restrictOnDelete();
            $table->decimal('fiyat', 10, 2);
            $table->string('kargo_kodu')->nullable()->unique();
            $table->timestamp('tarih')->useCurrent()->index();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('siparisler');
    }
};
