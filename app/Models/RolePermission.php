<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class RolePermission extends Model
{
    protected $table = 'role_permissions';

    protected $fillable = ['role', 'feature', 'can_view', 'can_create', 'can_update', 'can_delete'];

    protected $casts = [
        'can_view'   => 'boolean',
        'can_create' => 'boolean',
        'can_update' => 'boolean',
        'can_delete' => 'boolean',
    ];

    // ─── Constants ───────────────────────────────────────────────────────────────

    public const ROLE_ADMIN    = 'admin';
    public const ROLE_EDITOR   = 'editor';
    public const ROLE_REVIEWER = 'reviewer';

    public const FEATURE_BAHAN_BAKU   = 'bahan_baku';
    public const FEATURE_RESEP         = 'resep';
    public const FEATURE_PRODUKSI      = 'produksi';
    public const FEATURE_BARANG_KELUAR = 'barang_keluar';

    public const FEATURES = [
        self::FEATURE_BAHAN_BAKU,
        self::FEATURE_RESEP,
        self::FEATURE_PRODUKSI,
        self::FEATURE_BARANG_KELUAR,
    ];

    public const ROLES = [
        self::ROLE_ADMIN,
        self::ROLE_EDITOR,
        self::ROLE_REVIEWER,
    ];
}
