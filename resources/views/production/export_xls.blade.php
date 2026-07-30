<!doctype html>
<html>
<head>
    <meta charset="UTF-8">
    <style>
        .title { font-family: 'Arial', sans-serif; font-size: 18pt; font-weight: bold; color: #1e40af; text-align: center; }
        .subtitle { text-align: center; margin-bottom: 20px; font-family: 'Arial', sans-serif; }
        .section { margin-top: 20px; font-family: 'Arial', sans-serif; }
        .section-title { font-weight: bold; font-size: 12pt; color: #1e3a8a; margin-bottom: 8px; }
        .table { border-collapse: collapse; width: 100%; font-family: 'Arial', sans-serif; margin-top: 8px; }
        .table th { background-color: #1e40af; color: #ffffff; padding: 12px; border: 1px solid #ffffff; text-transform: uppercase; font-size: 10pt; }
        .table td { padding: 10px; border: 1px solid #e2e8f0; font-size: 10pt; }
        .row-even { background-color: #f8fafc; }
        .qty-cell { font-weight: bold; color: #059669; text-align: center; }
        .date-cell { color: #64748b; font-style: italic; }
    </style>
</head>
<body>
    <div class="title">LAPORAN RIWAYAT BARANG KELUAR</div>
    <div class="subtitle">Dicetak pada: {{ now()->format('d M Y H:i') }}</div>

    <div class="section">
        <div class="section-title">REKAPITULASI PRODUKSI</div>
        <div><strong>Total barang keluar:</strong> {{ $summary['total_barang_keluar'] ?? 0 }}</div>
        <div><strong>Total unit produksi:</strong> {{ $summary['total_unit_produksi'] ?? 0 }}</div>
        <div><strong>Produk terbanyak:</strong> {{ $summary['produk_terbanyak'] ?? '-' }}</div>
        <div><strong>Bahan baku terbanyak:</strong> {{ $summary['bahan_terbanyak'] ?? '-' }}</div>
        <div><strong>Bahan yang perlu ditambahkan:</strong> {{ collect($summary['perlu_ditambahkan'] ?? [])->pluck('nama')->implode(', ') ?: '-' }}</div>
    </div>


    <div class="section">
        <div class="section-title">REKAP PRODUK PER BULAN</div>
        @foreach($summary['rekap_per_bulan'] ?? [] as $bulan => $items)
            <div><strong>{{ $bulan }}:</strong> {{ collect($items)->map(fn($item) => $item['nama_produk'].' ('.$item['qty'].')')->implode(' | ') }}</div>
        @endforeach
    </div>

    <div class="section">
        <div class="section-title">DETAIL DATA PRODUKSI</div>
        <table class="table">
            <thead>
                <tr>
                    <th style="width:6%">No</th>
                    <th style="width:60%">Nama Barang</th>
                    <th style="width:18%">Jumlah (Qty)</th>
                    <th style="width:16%">Tanggal Keluar</th>
                </tr>
            </thead>
            <tbody>
                @php $no = 1; @endphp
                @foreach($history as $date => $items)
                    @foreach($items as $row)
                        @php $class = ($no % 2 == 0) ? 'row-even' : ''; @endphp
                        <tr class="{{ $class }}">
                            <td style="text-align: center;">{{ $no++ }}</td>
                            <td>{{ strtoupper($row->nama_barang) }}</td>
                            <td class="qty-cell">{{ $row->qty }} Unit</td>
                            <td class="date-cell">{{ $row->created_at->format('d/m/Y H:i') }}</td>
                        </tr>
                    @endforeach
                @endforeach
            </tbody>
        </table>
    </div>
</body>
</html>
