<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\BahanMentah;
use App\Models\BarangKeluar;
use App\Models\MasterProduk;
use App\Models\ProduksiWip;
use Illuminate\Support\Facades\DB;
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

        $topProducts = BarangKeluar::select('nama_barang', DB::raw('SUM(qty) as total_qty'))
            ->groupBy('nama_barang')
            ->orderByDesc('total_qty')
            ->limit(5)
            ->get();

        $monthlyProduction = BarangKeluar::get(['created_at', 'qty'])
            ->groupBy(fn($item) => $item->created_at->format('Y-m'))
            ->map(fn($items, $month) => (object) [
                'month' => $month,
                'total_qty' => $items->sum('qty'),
            ])
            ->sortBy('month')
            ->values();

        $topProductCapacity = $products->map(fn($p) => [
            'nama_produk' => $p->nama_produk,
            'kapasitas' => $p->kapasitasMaksimal(),
        ])->sortByDesc('kapasitas')->take(5)->values();

        $materials = BahanMentah::orderBy('stok')->get();
        $lowStockThreshold = 15;
        $lowStockMaterials = $materials->where('stok', '<', $lowStockThreshold)->values();
        $lowStockCount = $lowStockMaterials->count();
        $maxMaterialStock = $materials->max('stok') ?: 1;

        return view('dashboard.index', [
            'total_materials'    => BahanMentah::count(),
            'total_wip'          => (int) ProduksiWip::sum('qty'),
            'total_production'   => (int) BarangKeluar::sum('qty'),
            'topProducts'        => $topProducts,
            'monthlyProduction'  => $monthlyProduction,
            'topProductCapacity' => $topProductCapacity,
            'proyeksi'           => $proyeksi,
            'materials'          => $materials,
            'lowStockMaterials'  => $lowStockMaterials,
            'lowStockCount'      => $lowStockCount,
            'lowStockThreshold'  => $lowStockThreshold,
            'maxMaterialStock'   => $maxMaterialStock,
        ]);
    }
}
