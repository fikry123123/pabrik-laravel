<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\BahanMentah;
use App\Models\MasterProduk;
use App\Models\ProduksiWip;
use Illuminate\Http\JsonResponse;

class DashboardController extends Controller
{
    /**
     * GET /api/dashboard
     */
    public function index(): JsonResponse
    {
        $products = MasterProduk::with(['reseps.bahanMentah'])->get();

        $proyeksi = $products->map(fn($p) => [
            'id'           => $p->id,
            'nama_produk'  => $p->nama_produk,
            'kapasitas'    => $p->kapasitasMaksimal(),
        ]);

        return response()->json([
            'total_materials' => BahanMentah::count(),
            'total_wip'       => (int) ProduksiWip::sum('qty'),
            'proyeksi'        => $proyeksi,
        ]);
    }
}
