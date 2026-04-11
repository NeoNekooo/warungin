<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Laporan Penjualan - Warungin</title>
    <style>
        body {
            font-family: 'Helvetica', 'Arial', sans-serif;
            color: #333;
            margin: 0;
            padding: 20px;
            line-height: 1.6;
        }
        .header {
            text-align: center;
            border-bottom: 2px solid #4f46e5;
            padding-bottom: 10px;
            margin-bottom: 25px;
        }
        .header h1 {
            margin: 0;
            color: #4f46e5;
            font-size: 24px;
            text-transform: uppercase;
        }
        .header p {
            margin: 5px 0 0;
            color: #666;
            font-size: 12px;
        }
        .info {
            margin-bottom: 20px;
            font-size: 12px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
        }
        th {
            background-color: #4f46e5;
            color: white;
            text-align: left;
            padding: 10px;
            font-size: 13px;
        }
        td {
            padding: 10px;
            border-bottom: 1px solid #eee;
            font-size: 12px;
        }
        tr:nth-child(even) {
            background-color: #f9fafb;
        }
        .text-right {
            text-align: right;
        }
        .footer {
            margin-top: 30px;
            text-align: center;
            font-size: 10px;
            color: #999;
            border-top: 1px solid #eee;
            padding-top: 10px;
        }
        .summary {
            margin-top: 20px;
            text-align: right;
        }
        .summary-box {
            display: inline-block;
            background: #f3f4f6;
            padding: 15px;
            border-radius: 8px;
            min-width: 200px;
        }
        .summary-item {
            display: flex;
            justify-content: space-between;
            margin-bottom: 5px;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>Laporan Penjualan</h1>
        <p>Warungin POS System - Sistem Manajemen Bisnis</p>
    </div>

    <div class="info">
        <strong>Dicetak pada:</strong> {{ date('d F Y, H:i') }}<br>
        <strong>Dicetak oleh:</strong> {{ auth()->user()->nama }} ({{ ucfirst(auth()->user()->role) }})
    </div>

    <table>
        <thead>
            <tr>
                <th>No</th>
                <th>Tanggal</th>
                <th class="text-right">Jumlah Transaksi</th>
                <th class="text-right">Total Pendapatan (Rp)</th>
            </tr>
        </thead>
        <tbody>
            @php $totalSemua = 0; $totalCount = 0; @endphp
            @foreach($rows as $index => $row)
            @php 
                $totalSemua += $row->total; 
                $totalCount += $row->count;
            @endphp
            <tr>
                <td>{{ $index + 1 }}</td>
                <td>{{ date('d M Y', strtotime($row->tanggal)) }}</td>
                <td class="text-right">{{ number_format($row->count, 0, ',', '.') }}</td>
                <td class="text-right">{{ number_format($row->total, 0, ',', '.') }}</td>
            </tr>
            @endforeach
        </tbody>
        <tfoot>
            <tr style="font-weight: bold; background-color: #f3f4f1;">
                <td colspan="2" style="border-top: 2px solid #4f46e5;">TOTAL KESELURUHAN</td>
                <td class="text-right" style="border-top: 2px solid #4f46e5;">{{ number_format($totalCount, 0, ',', '.') }}</td>
                <td class="text-right" style="border-top: 2px solid #4f46e5;">Rp {{ number_format($totalSemua, 0, ',', '.') }}</td>
            </tr>
        </tfoot>
    </table>

    <div class="footer">
        Dokumen ini dibuat otomatis oleh sistem Warungin. Terima kasih atas kepercayaan Anda.
    </div>
</body>
</html>
