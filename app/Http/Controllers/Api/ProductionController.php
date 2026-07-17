<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\BarangKeluar;
use App\Models\MasterProduk;
use App\Models\ProduksiWip;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ProductionController extends Controller
{
    /**
     * GET /api/wip
     * Daftar barang yang sedang diproses.
     */
    public function wip(): JsonResponse
    {
        return response()->json(ProduksiWip::latest()->get());
    }

    /**
     * POST /api/production/start
     * Body: { id_produk, qty }
     *
     * Kurangi stok bahan mentah lalu masukkan ke WIP.
     */
    public function start(Request $request): JsonResponse
    {
        $data = $request->validate([
            'id_produk' => 'required|exists:master_produk,id',
            'qty'       => 'required|integer|min:1',
        ]);

        $produk = MasterProduk::with('reseps.bahanMentah')->findOrFail($data['id_produk']);
        $qty    = (int) $data['qty'];

        // Cek kapasitas
        if ($produk->kapasitasMaksimal() < $qty) {
            return response()->json([
                'status'  => 'error',
                'message' => 'Stok bahan mentah tidak cukup untuk memproduksi sejumlah itu.',
            ], 422);
        }

        DB::transaction(function () use ($produk, $qty) {
            // Kurangi stok semua bahan yang diperlukan
            foreach ($produk->reseps as $resep) {
                $resep->bahanMentah->decrement('stok', $resep->qty_butuh * $qty);
            }

            // Masukkan ke WIP
            ProduksiWip::create([
                'nama_produk' => $produk->nama_produk,
                'qty'         => $qty,
            ]);
        });

        return response()->json(['status' => 'success']);
    }

    /**
     * POST /api/production/complete/{id}
     * Selesaikan WIP → pindah ke barang_keluar.
     */
    public function complete(ProduksiWip $wip): JsonResponse
    {
        DB::transaction(function () use ($wip) {
            BarangKeluar::create([
                'nama_barang' => $wip->nama_produk,
                'qty'         => $wip->qty,
            ]);

            $wip->delete();
        });

        return response()->json(['status' => 'success']);
    }

    /**
     * GET /api/outbound
     * Riwayat barang keluar.
     */
    public function outbound(): JsonResponse
    {
        return response()->json(BarangKeluar::latest()->get());
    }
}
