<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;

class BarangKeluarExport implements FromArray, ShouldAutoSize, WithEvents
{
    protected $history;

    public function __construct($history)
    {
        $this->history = $history;
    }

    public function array(): array
    {
        $rows = [];

        // We'll build a simple table starting a few rows down so we can add title above in AfterSheet
        // Header row for the table
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

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $event) {
                $sheet = $event->sheet->getDelegate();

                // Title
                $sheet->mergeCells('A1:D1');
                $sheet->setCellValue('A1', 'LAPORAN RIWAYAT BARANG KELUAR');
                $sheet->getStyle('A1')->getFont()->setBold(true)->setSize(16);
                $sheet->getStyle('A1')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);

                // Printed at
                $sheet->mergeCells('A2:D2');
                $sheet->setCellValue('A2', 'Dicetak pada: '.now()->format('d M Y H:i'));
                $sheet->getStyle('A2')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);

                // Table header style (row 4 since we have title rows and an empty row)
                $headerRow = 4;
                $sheet->getStyle("A{$headerRow}:D{$headerRow}")
                    ->getFont()->setBold(true);

                $sheet->getStyle("A{$headerRow}:D{$headerRow}")
                    ->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);

                // Shift data down by one row so header is at row 4
                $sheet->insertNewRowBefore(1, 1); // ensure there's space

                // Set column widths
                $sheet->getColumnDimension('A')->setWidth(6);
                $sheet->getColumnDimension('B')->setWidth(40);
                $sheet->getColumnDimension('C')->setWidth(15);
                $sheet->getColumnDimension('D')->setWidth(22);

                // Apply borders to the table range
                $highestRow = $sheet->getHighestRow();
                $sheet->getStyle("A{$headerRow}:D{$highestRow}")
                    ->getBorders()->getAllBorders()->setBorderStyle(\PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN);

                // Color Qty column green text
                $sheet->getStyle("C".($headerRow+1).":C{$highestRow}")
                    ->getFont()->getColor()->setRGB('008000');

                // Center vertical alignment for all
                $sheet->getStyle("A{$headerRow}:D{$highestRow}")
                    ->getAlignment()->setVertical(\PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER);
            },
        ];
    }
}
