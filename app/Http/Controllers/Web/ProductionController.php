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

    public function outbound(Request $request)
    {
        $history = BarangKeluar::latest()->get()->groupBy(
            fn($item) => $item->created_at->format('Y-m-d')
        );

        if ($request->query('export')) {
            $xlsxFilename = 'barang-keluar-'.now()->format('Ymd_His').'.xlsx';

            if (class_exists(\Maatwebsite\Excel\Facades\Excel::class) && class_exists(\App\Exports\BarangKeluarExport::class)) {
                return \Maatwebsite\Excel\Facades\Excel::download(new \App\Exports\BarangKeluarExport($history), $xlsxFilename);
            }

            // Fallback to HTML-based .xls export (styled) if maatwebsite/excel is not installed
            $filename = 'Laporan_Barang_Keluar_'.now()->format('Y-m-d_His').'.xls';
            $headers = [
                'Content-Type' => 'application/vnd.ms-excel; charset=UTF-8',
                'Content-Disposition' => "attachment; filename=\"{$filename}\"",
                'Pragma' => 'no-cache',
                'Expires' => '0',
            ];

            $html = view('production.export_xls', compact('history'))->render();
            // Prepend UTF-8 BOM so Excel recognizes encoding
            $content = "\xEF\xBB\xBF" . $html;

            return response($content, 200, $headers);
        }

        return view('production.outbound', compact('history'));
    }

    public function updateOutbound(Request $request, BarangKeluar $barangKeluar): RedirectResponse
    {
        $data = $request->validate([
            'nama_barang' => 'required|string|max:255',
            'qty'         => 'required|integer|min:1',
        ]);

        $barangKeluar->update($data);

        return back()->with('success', 'Data barang keluar berhasil diperbarui!');
    }

    public function destroyOutbound(BarangKeluar $barangKeluar): RedirectResponse
    {
        $barangKeluar->delete();

        return back()->with('success', 'Data barang keluar berhasil dihapus!');
    }
}
