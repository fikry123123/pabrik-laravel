<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class BahanMentah extends Model
{
    protected $table = 'bahan_mentah';

    protected $fillable = ['nama', 'stok', 'satuan', 'created_by', 'updated_by'];

    // ─── Relasi ─────────────────────────────────────────────────────────────────

    /**
     * Bahan mentah ini dipakai di banyak resep.
     */
    public function reseps(): HasMany
    {
        return $this->hasMany(Resep::class, 'id_bahan');
    }

    /**
     * User yang membuat bahan mentah ini.
     */
    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * User yang terakhir mengupdate bahan mentah ini.
     */
    public function updatedBy()
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    // ─── Helper ─────────────────────────────────────────────────────────────────

    /**
     * Kurangi stok. Pastikan tidak minus.
     */
    public function kurangiStok(float $qty): bool
    {
        if ($this->stok < $qty) {
            return false;
        }
        $this->decrement('stok', $qty);
        return true;
    }
}
