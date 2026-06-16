<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Resep extends Model
{
    protected $table = 'resep';

    protected $fillable = ['id_produk', 'id_bahan', 'qty_butuh'];

    // ─── Relasi ─────────────────────────────────────────────────────────────────

    public function produk(): BelongsTo
    {
        return $this->belongsTo(MasterProduk::class, 'id_produk');
    }

    public function bahanMentah(): BelongsTo
    {
        return $this->belongsTo(BahanMentah::class, 'id_bahan');
    }
}
