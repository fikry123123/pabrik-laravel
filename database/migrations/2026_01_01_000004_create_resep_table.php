<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('resep', function (Blueprint $table) {
            $table->id();
            $table->foreignId('id_produk')->constrained('master_produk')->onDelete('cascade');
            $table->foreignId('id_bahan')->constrained('bahan_mentah')->onDelete('cascade');
            $table->decimal('qty_butuh', 10, 2);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('resep');
    }
};
