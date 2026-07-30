<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BarangKeluar extends Model
{
    protected $table = 'barang_keluar';

    protected $fillable = ['nama_barang', 'qty', 'created_at'];

    protected $casts = [
        'created_at' => 'datetime',
    ];
}
