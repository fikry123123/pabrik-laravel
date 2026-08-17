<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // Default permissions setup
        $permissions = [
            // Admin: Full access to everything
            ['role' => 'admin', 'feature' => 'bahan_baku', 'can_view' => true, 'can_create' => true, 'can_update' => true, 'can_delete' => true],
            ['role' => 'admin', 'feature' => 'resep', 'can_view' => true, 'can_create' => true, 'can_update' => true, 'can_delete' => true],
            ['role' => 'admin', 'feature' => 'produksi', 'can_view' => true, 'can_create' => true, 'can_update' => true, 'can_delete' => true],
            ['role' => 'admin', 'feature' => 'barang_keluar', 'can_view' => true, 'can_create' => true, 'can_update' => true, 'can_delete' => true],

            // Editor: Can create, read, update but not delete
            ['role' => 'editor', 'feature' => 'bahan_baku', 'can_view' => true, 'can_create' => true, 'can_update' => true, 'can_delete' => false],
            ['role' => 'editor', 'feature' => 'resep', 'can_view' => true, 'can_create' => true, 'can_update' => true, 'can_delete' => false],
            ['role' => 'editor', 'feature' => 'produksi', 'can_view' => true, 'can_create' => true, 'can_update' => true, 'can_delete' => false],
            ['role' => 'editor', 'feature' => 'barang_keluar', 'can_view' => true, 'can_create' => false, 'can_update' => false, 'can_delete' => false],

            // Reviewer: Read-only access to all
            ['role' => 'reviewer', 'feature' => 'bahan_baku', 'can_view' => true, 'can_create' => false, 'can_update' => false, 'can_delete' => false],
            ['role' => 'reviewer', 'feature' => 'resep', 'can_view' => true, 'can_create' => false, 'can_update' => false, 'can_delete' => false],
            ['role' => 'reviewer', 'feature' => 'produksi', 'can_view' => true, 'can_create' => false, 'can_update' => false, 'can_delete' => false],
            ['role' => 'reviewer', 'feature' => 'barang_keluar', 'can_view' => true, 'can_create' => false, 'can_update' => false, 'can_delete' => false],
        ];

        DB::table('role_permissions')->insertOrIgnore($permissions);
    }

    public function down(): void
    {
        DB::table('role_permissions')->truncate();
    }
};
