<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\BarangKeluar;
use App\Models\BahanMentah;
use App\Models\MasterProduk;
use App\Models\ProduksiWip;use Carbon\Carbon;use Illuminate\Http\RedirectResponse;
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

        $summary = $this->buildExportSummary($history);
        $chartSvg = $this->buildChartSvg($summary);

        if ($request->query('export')) {
            $xlsxFilename = 'barang-keluar-'.now()->format('Ymd_His').'.xlsx';

            if (class_exists(\Maatwebsite\Excel\Facades\Excel::class) && class_exists(\App\Exports\BarangKeluarExcelExport::class)) {
                return \Maatwebsite\Excel\Facades\Excel::download(new \App\Exports\BarangKeluarExcelExport($history, $summary), $xlsxFilename);
            }

            // Fallback to HTML-based .xls export (styled) if maatwebsite/excel is not installed
            $filename = 'Laporan_Barang_Keluar_'.now()->format('Y-m-d_His').'.xls';
            $headers = [
                'Content-Type' => 'application/vnd.ms-excel; charset=UTF-8',
                'Content-Disposition' => "attachment; filename=\"{$filename}\"",
                'Pragma' => 'no-cache',
                'Expires' => '0',
            ];

            $html = view('production.export_xls', compact('history', 'summary', 'chartSvg'))->render();
            // Prepend UTF-8 BOM so Excel recognizes encoding
            $content = "\xEF\xBB\xBF" . $html;

            return response($content, 200, $headers);
        }

        return view('production.outbound', compact('history'));
    }

    protected function buildChartSvg(array $summary): string
    {
        $barangKeluar = max(0, (int) ($summary['total_barang_keluar'] ?? 0));
        $unitProduksi = max(0, (int) ($summary['total_unit_produksi'] ?? 0));
        $maxValue = max(1, $barangKeluar, $unitProduksi);
        $bar1 = max(20, (int) round(($barangKeluar / $maxValue) * 220));
        $bar2 = max(20, (int) round(($unitProduksi / $maxValue) * 220));
        $label1X = $bar1 + 130;
        $label2X = $bar2 + 130;

        return <<<SVG
<svg xmlns="http://www.w3.org/2000/svg" width="760" height="320" viewBox="0 0 760 320">
  <rect x="0" y="0" width="760" height="320" fill="#ffffff"/>
  <rect x="40" y="60" width="680" height="200" fill="#f8fafc" stroke="#dbe2ea" rx="8"/>
  <text x="60" y="92" font-family="Arial" font-size="18" font-weight="bold" fill="#111827">Grafik Ringkasan Produksi</text>
  <line x1="90" y1="220" x2="650" y2="220" stroke="#94a3b8" stroke-width="2"/>
  <rect x="120" y="160" width="{$bar1}" height="36" rx="6" fill="#2563eb"/>
  <rect x="120" y="110" width="{$bar2}" height="36" rx="6" fill="#16a34a"/>
  <text x="120" y="102" font-family="Arial" font-size="13" font-weight="bold" fill="#111827">Unit Produksi</text>
  <text x="120" y="152" font-family="Arial" font-size="13" font-weight="bold" fill="#111827">Barang Keluar</text>
  <text x="{$label1X}" y="183" font-family="Arial" font-size="12" fill="#111827">{$barangKeluar}</text>
  <text x="{$label2X}" y="133" font-family="Arial" font-size="12" fill="#111827">{$unitProduksi}</text>
</svg>
SVG;
    }

    protected function buildExportSummary($history): array
    {
        $items = collect($history)->flatten();
        $produkUsage = [];
        $bahanUsage = [];
        $monthlyUsage = [];

        foreach ($items as $item) {
            $namaProduk = trim((string) $item->nama_barang);
            $qty = (int) $item->qty;
            $produkUsage[$namaProduk] = ($produkUsage[$namaProduk] ?? 0) + $qty;

            $monthKey = $item->created_at->format('Y-m');
            $monthlyUsage[$monthKey][] = [
                'nama_produk' => $namaProduk,
                'qty' => $qty,
            ];

            $produk = MasterProduk::where('nama_produk', $namaProduk)->with('reseps.bahanMentah')->first();
            if ($produk) {
                foreach ($produk->reseps as $resep) {
                    if (! $resep->bahanMentah) {
                        continue;
                    }

                    $bahanUsage[$resep->bahanMentah->nama] = ($bahanUsage[$resep->bahanMentah->nama] ?? 0) + ($resep->qty_butuh * $qty);
                }
            }
        }

        $topProduk = collect($produkUsage)->sortDesc()->take(5);
        $topBahan = collect($bahanUsage)->sortDesc()->take(5);
        $perluDitambahkan = [];

        foreach (BahanMentah::where('stok', '<=', 10)->get() as $bahan) {
            $perluDitambahkan[] = [
                'nama' => $bahan->nama,
                'stok' => (float) $bahan->stok,
                'satuan' => $bahan->satuan,
            ];
        }

        $rekapPerBulan = [];
        foreach ($monthlyUsage as $month => $rows) {
            $rekapPerBulan[$month] = collect($rows)
                ->groupBy('nama_produk')
                ->map(function ($group, $produkNama) {
                    return [
                        'nama_produk' => $produkNama,
                        'qty' => $group->sum('qty'),
                    ];
                })
                ->sortByDesc('qty')
                ->values()
                ->all();
        }

        krsort($rekapPerBulan);

        return [
            'total_barang_keluar' => $items->count(),
            'total_unit_produksi' => (int) $items->sum('qty'),
            'produk_terbanyak' => $topProduk->keys()->first() ?? '-',
            'bahan_terbanyak' => $topBahan->keys()->first() ?? '-',
            'perlu_ditambahkan' => $perluDitambahkan,
            'rekap_per_bulan' => $rekapPerBulan,
        ];
    }

    public function updateOutbound(Request $request, BarangKeluar $barangKeluar): RedirectResponse
    {
        $data = $request->validate([
            'nama_barang' => 'required|string|max:255',
            'qty'         => 'required|integer|min:1',
            'created_at'  => 'required|date',
        ]);

        $barangKeluar->update([
            'nama_barang' => $data['nama_barang'],
            'qty'         => $data['qty'],
            'created_at'  => Carbon::parse($data['created_at']),
        ]);

        return back()->with('success', 'Data barang keluar berhasil diperbarui!');
    }

    public function destroyOutbound(BarangKeluar $barangKeluar): RedirectResponse
    {
        $barangKeluar->delete();

        return back()->with('success', 'Data barang keluar berhasil dihapus!');
    }
}
