<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class MasterProduk extends Model
{
    protected $table = 'master_produk';

    protected $fillable = ['nama_produk'];

    // ─── Relasi ─────────────────────────────────────────────────────────────────

    /**
     * Satu produk punya banyak baris resep (BOM).
     */
    public function reseps(): HasMany
    {
        return $this->hasMany(Resep::class, 'id_produk');
    }

    // ─── Helper ─────────────────────────────────────────────────────────────────

    /**
     * Hitung berapa unit maksimal yang bisa diproduksi
     * berdasarkan stok bahan mentah saat ini (bottleneck logic).
     */
    public function kapasitasMaksimal(): int
    {
        $reseps = $this->reseps()->with('bahanMentah')->get();

        if ($reseps->isEmpty()) {
            return 0;
        }

        $max = PHP_INT_MAX;
        foreach ($reseps as $resep) {
            $potensi = (int) floor($resep->bahanMentah->stok / $resep->qty_butuh);
            if ($potensi < $max) {
                $max = $potensi;
            }
        }

        return max(0, $max === PHP_INT_MAX ? 0 : $max);
    }
}
