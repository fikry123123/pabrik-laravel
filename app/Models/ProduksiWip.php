<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProduksiWip extends Model
{
    protected $table = 'produksi_wip';

    protected $fillable = ['nama_produk', 'qty'];
}
