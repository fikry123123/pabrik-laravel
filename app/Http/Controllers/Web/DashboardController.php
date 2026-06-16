<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\BahanMentah;
use App\Models\MasterProduk;
use App\Models\ProduksiWip;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(): View
    {
        $products = MasterProduk::with('reseps.bahanMentah')->get();

        $proyeksi = $products->map(fn($p) => [
            'nama_produk' => $p->nama_produk,
            'kapasitas'   => $p->kapasitasMaksimal(),
            'bom'         => $p->reseps->map(fn($r) => [
                'nama_bahan' => $r->bahanMentah->nama,
                'satuan'     => $r->bahanMentah->satuan,
                'qty_butuh'  => $r->qty_butuh,
            ]),
        ]);

        return view('dashboard.index', [
            'total_materials' => BahanMentah::count(),
            'total_wip'       => (int) ProduksiWip::sum('qty'),
            'proyeksi'        => $proyeksi,
        ]);
    }
}
