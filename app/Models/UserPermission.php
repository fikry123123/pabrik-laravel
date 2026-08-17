<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class UserPermission extends Model
{
    public const FEATURES = ['bahan_baku', 'resep', 'produksi', 'barang_keluar'];

    protected $fillable = ['user_id', 'feature', 'can_manage'];

    protected $casts = ['can_manage' => 'boolean'];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
