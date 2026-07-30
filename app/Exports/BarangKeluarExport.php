<?php

namespace App\Exports;

class BarangKeluarExport
{
    protected $history;
    protected $summary;

    public function __construct($history, array $summary = [])
    {
        $this->history = $history;
        $this->summary = $summary;
    }

    public function array(): array
    {
        $rows = [];

        $rows[] = ['LAPORAN RIWAYAT BARANG KELUAR'];
        $rows[] = ['Dicetak pada: '.now()->format('d M Y H:i')];
        $rows[] = [];
        $rows[] = ['REKAPITULASI PRODUKSI'];
        $rows[] = ['Total barang keluar', $this->summary['total_barang_keluar'] ?? 0];
        $rows[] = ['Total unit produksi', $this->summary['total_unit_produksi'] ?? 0];
        $rows[] = ['Produk terbanyak', $this->summary['produk_terbanyak'] ?? '-'];
        $rows[] = ['Bahan baku terbanyak', $this->summary['bahan_terbanyak'] ?? '-'];
        $rows[] = ['Bahan yang perlu ditambahkan', $this->formatList($this->summary['perlu_ditambahkan'] ?? [])];
        $rows[] = [];
        $rows[] = ['GRAFIK RINGKASAN PRODUKSI'];
        $rows[] = ['Kategori', 'Nilai'];
        $rows[] = ['Barang keluar', $this->summary['total_barang_keluar'] ?? 0];
        $rows[] = ['Unit produksi', $this->summary['total_unit_produksi'] ?? 0];
        $rows[] = ['Produk terbanyak', $this->summary['produk_terbanyak'] === '-' ? 0 : 1];
        $rows[] = ['Bahan baku terbanyak', $this->summary['bahan_terbanyak'] === '-' ? 0 : 1];
        $rows[] = [];
        $rows[] = ['REKAP PRODUK PER BULAN'];

        $rekapBulanan = $this->summary['rekap_per_bulan'] ?? [];
        if (empty($rekapBulanan)) {
            $rows[] = ['Belum ada data'];
        } else {
            foreach ($rekapBulanan as $bulan => $items) {
                $rows[] = [$bulan, $this->formatRekapBulanan($items)];
            }
        }

        $rows[] = [];
        $rows[] = ['DETAIL DATA PRODUKSI'];
        $rows[] = ['No', 'Nama Barang', 'Jumlah (Qty)', 'Tanggal Keluar'];

        $no = 1;
        foreach ($this->history as $date => $items) {
            foreach ($items as $h) {
                $rows[] = [
                    $no++,
                    $h->nama_barang,
                    $h->qty,
                    $h->created_at->format('d/m/Y H:i'),
                ];
            }
        }

        return $rows;
    }

    protected function formatList(array $items): string
    {
        if (empty($items)) {
            return '-';
        }

        return collect($items)->map(function ($item) {
            if (is_array($item)) {
                return $item['nama'] ?? json_encode($item, JSON_UNESCAPED_SLASHES);
            }

            return (string) $item;
        })->implode(', ');
    }

    protected function formatRekapBulanan(array $items): string
    {
        return collect($items)->map(function ($item) {
            return ($item['nama_produk'] ?? '-') . ' (' . ($item['qty'] ?? 0) . ')';
        })->implode(' | ');
    }
}

if (interface_exists('Maatwebsite\\Excel\\Concerns\\FromArray')
    && interface_exists('Maatwebsite\\Excel\\Concerns\\ShouldAutoSize')
    && interface_exists('Maatwebsite\\Excel\\Concerns\\WithEvents')) {
    class BarangKeluarExcelExport extends BarangKeluarExport implements \Maatwebsite\Excel\Concerns\FromArray, \Maatwebsite\Excel\Concerns\ShouldAutoSize, \Maatwebsite\Excel\Concerns\WithEvents
    {
        public function registerEvents(): array
        {
            return [
                \Maatwebsite\Excel\Events\AfterSheet::class => function (\Maatwebsite\Excel\Events\AfterSheet $event) {
                    $sheet = $event->sheet->getDelegate();

                    $sheet->mergeCells('A1:D1');
                    $sheet->setCellValue('A1', 'LAPORAN RIWAYAT BARANG KELUAR');
                    $sheet->getStyle('A1')->getFont()->setBold(true)->setSize(16);
                    $sheet->getStyle('A1')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);

                    $sheet->mergeCells('A2:D2');
                    $sheet->setCellValue('A2', 'Dicetak pada: '.now()->format('d M Y H:i'));
                    $sheet->getStyle('A2')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);

                    $detailTitleRow = null;
                    for ($row = 1; $row <= $sheet->getHighestRow(); $row++) {
                        if ($sheet->getCell('A'.$row)->getValue() === 'DETAIL DATA PRODUKSI') {
                            $detailTitleRow = $row;
                            break;
                        }
                    }

                    $headerRow = $detailTitleRow ? $detailTitleRow + 1 : 14;
                    if ($detailTitleRow) {
                        $sheet->getStyle("A{$headerRow}:D{$headerRow}")->getFont()->setBold(true);
                        $sheet->getStyle("A{$headerRow}:D{$headerRow}")->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
                    }

                    $sheet->getColumnDimension('A')->setWidth(16);
                    $sheet->getColumnDimension('B')->setWidth(40);
                    $sheet->getColumnDimension('C')->setWidth(18);
                    $sheet->getColumnDimension('D')->setWidth(24);

                    $highestRow = $sheet->getHighestRow();
                    if ($detailTitleRow) {
                        $sheet->getStyle("A{$headerRow}:D{$highestRow}")->getBorders()->getAllBorders()->setBorderStyle(\PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN);
                        $sheet->getStyle("C".($headerRow + 1).":C{$highestRow}")->getFont()->getColor()->setRGB('008000');
                        $sheet->getStyle("A{$headerRow}:D{$highestRow}")->getAlignment()->setVertical(\PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER);
                    }
                },
            ];
        }
    }
}
