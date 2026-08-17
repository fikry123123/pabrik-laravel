<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('role_permissions', function (Blueprint $table) {
            $table->id();
            $table->string('role'); // admin, editor, reviewer
            $table->string('feature'); // bahan_baku, resep, produksi, barang_keluar
            $table->boolean('can_view')->default(true);
            $table->boolean('can_create')->default(false);
            $table->boolean('can_update')->default(false);
            $table->boolean('can_delete')->default(false);
            $table->timestamps();

            // Ensure unique combination of role + feature
            $table->unique(['role', 'feature']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('role_permissions');
    }
};
