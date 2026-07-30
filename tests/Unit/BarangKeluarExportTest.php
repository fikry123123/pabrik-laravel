<?php

namespace Tests\Unit;

use App\Exports\BarangKeluarExport;
use Tests\TestCase;

class BarangKeluarExportTest extends TestCase
{
    public function test_export_includes_rekapitulasi_section(): void
    {
        $history = [
            '2026-07-29' => [
                (object) [
                    'nama_barang' => 'Produk A',
                    'qty' => 5,
                    'created_at' => now(),
                ],
            ],
        ];

        $export = new BarangKeluarExport($history, [
            'total_barang_keluar' => 1,
            'total_unit_produksi' => 5,
            'produk_terbanyak' => 'Produk A',
            'bahan_terbanyak' => 'Bahan X',
            'perlu_ditambahkan' => ['Bahan X'],
            'rekap_per_bulan' => [
                '2026-07' => [
                    ['nama_produk' => 'Produk A', 'qty' => 5],
                ],
            ],
        ]);

        $rows = $export->array();
        $flatRows = collect($rows)->flatten(1)->filter(fn ($value) => is_string($value))->values()->all();

        $this->assertContains('REKAPITULASI PRODUKSI', $flatRows);
        $this->assertContains('GRAFIK RINGKASAN PRODUKSI', $flatRows);
    }
}
