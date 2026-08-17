<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, Notifiable;

    /**
     * Kolom yang bisa diisi massal.
     */
    protected $fillable = ['username', 'password', 'role'];

    /**
     * Kolom yang disembunyikan saat serialisasi.
     */
    protected $hidden = ['password', 'remember_token'];

    protected $casts = [
        'password' => 'hashed',
    ];

    // ─── Relasi ─────────────────────────────────────────────────────────────────

    /**
     * Bahan mentah yang dibuat oleh user ini.
     */
    public function createdMaterials(): HasMany
    {
        return $this->hasMany(BahanMentah::class, 'created_by');
    }

    /**
     * Bahan mentah yang diupdate oleh user ini.
     */
    public function updatedMaterials(): HasMany
    {
        return $this->hasMany(BahanMentah::class, 'updated_by');
    }

    public function permissions(): HasMany
    {
        return $this->hasMany(UserPermission::class);
    }

    // ─── Helper Role ────────────────────────────────────────────────────────────

    public function isAdmin(): bool
    {
        return $this->role === 'admin';
    }

    public function isEditor(): bool
    {
        return in_array($this->role, ['admin', 'editor']);
    }

    public function isReviewer(): bool
    {
        return $this->role === 'reviewer';
    }
}
