<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\BarangKeluar;
use App\Models\BahanMentah;
use App\Models\MasterProduk;
use App\Models\ProduksiWip;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class ProductionController extends Controller
{
    public function index(): View
    {
        return view('production.index', [
            'products'  => MasterProduk::with('reseps.bahanMentah')->get(),
            'wip_list'  => ProduksiWip::latest()->get(),
            'materials' => BahanMentah::all()->keyBy('id'),
        ]);
    }

    public function start(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'id_produk'      => 'required|exists:master_produk,id',
            'qty_produksi'   => 'required|integer|min:1',
        ]);

        $produk = MasterProduk::with('reseps.bahanMentah')->findOrFail($data['id_produk']);
        $qty    = (int) $data['qty_produksi'];

        if ($produk->kapasitasMaksimal() < $qty) {
            return back()->with('error', 'Stok bahan mentah tidak cukup!');
        }

        DB::transaction(function () use ($produk, $qty) {
            foreach ($produk->reseps as $resep) {
                $resep->bahanMentah->decrement('stok', $resep->qty_butuh * $qty);
            }
            ProduksiWip::create([
                'nama_produk' => $produk->nama_produk,
                'qty'         => $qty,
            ]);
        });

        return back()->with('success', "Berhasil memproses {$qty} unit {$produk->nama_produk}!");
    }

    public function complete(ProduksiWip $wip): RedirectResponse
    {
        DB::transaction(function () use ($wip) {
            BarangKeluar::create([
                'nama_barang' => $wip->nama_produk,
                'qty'         => $wip->qty,
            ]);
            $wip->delete();
        });

        return back()->with('success', 'Barang berhasil diselesaikan dan dikeluarkan!');
    }

    public function outbound(): View
    {
        $history = BarangKeluar::latest()->get()->groupBy(
            fn($item) => $item->created_at->format('Y-m-d')
        );

        return view('production.outbound', compact('history'));
    }
}
