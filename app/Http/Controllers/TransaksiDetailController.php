<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\Transaksi;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Worksheet\Drawing;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Border;

class TransaksiDetailController extends Controller
{
    public function __construct()
    {
        // Allow admin, kasir and owner to view transaksi detail
        $this->middleware(function ($request, $next) {
            $user = auth()->user();
            if (!$user) return abort(403);
            if (!in_array($user->role, ['admin','kasir','owner'])) return abort(403);
            return $next($request);
        });
    }
     public function index()
    {
        // join transaksi_detail with produk and transaksi header
        $details = DB::table('transaksi_detail as td')
            ->join('produk as p', 'td.produk_id', '=', 'p.produk_id')
            ->join('transaksi as t', 'td.transaksi_id', '=', 't.transaksi_id')
            ->select('td.*', 'p.nama_produk', 'p.kode_barcode', 't.tanggal', 't.kasir_id')
            ->orderBy('td.created_at', 'desc')
            ->paginate(30);

        return view('admin.transaksi_detail.index', compact('details'));
    }

    public function export()
    {
        $fileName = 'laporan-detail-transaksi-' . date('Y-m-d') . '.xlsx';
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Detail Transaksi');

        // --- 1. HEADER (Logo & Title) ---
        // Range tabel A sampai J
        $lastCol = 'J';

        // Logo (Ditaruh di Row 1, Column A/B tapi di-center dengan cara ditaruh di atas merge)
        $logoPath = public_path('assets/img/logo.png');
        if (file_exists($logoPath)) {
            $drawing = new Drawing();
            $drawing->setName('Logo Warungin');
            $drawing->setDescription('Logo');
            $drawing->setPath($logoPath);
            $drawing->setHeight(80);
            $drawing->setCoordinates('A1');
            // Menaruh logo agak ke tengah secara visual
            $drawing->setOffsetX(300); 
            $drawing->setOffsetY(10);
            $drawing->setWorksheet($sheet);
        }
        $sheet->getRowDimension(1)->setRowHeight(70); // Kasih ruang buat Logo

        // Title "WARUNGIN" (Merge A2:J2)
        $sheet->mergeCells("A2:{$lastCol}2");
        $sheet->setCellValue('A2', 'WARUNGIN');
        $sheet->getStyle('A2')->applyFromArray([
            'font' => [
                'bold' => true,
                'size' => 28,
                'color' => ['rgb' => '4F46E5']
            ],
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_CENTER,
                'vertical' => Alignment::VERTICAL_CENTER,
            ]
        ]);

        // Subtitle (Merge A3:J3)
        $sheet->mergeCells("A3:{$lastCol}3");
        $sheet->setCellValue('A3', 'LAPORAN DETAIL TRANSAKSI');
        $sheet->getStyle('A3')->applyFromArray([
            'font' => ['bold' => true, 'size' => 16],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER]
        ]);

        // Date (Merge A4:J4)
        $sheet->mergeCells("A4:{$lastCol}4");
        $sheet->setCellValue('A4', 'Tanggal Cetak: ' . date('d F Y H:i'));
        $sheet->getStyle('A4')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

        // Kasih jarak (Empty Row 5 & 6)

        // --- 2. TABLE HEADERS ---
        $startRow = 7;
        $columns = ['No', 'Tanggal', 'ID Transaksi', 'Produk', 'Barcode', 'Qty', 'Harga Satuan', 'Subtotal', 'Kasir', 'Pelanggan'];
        
        $colIdx = 'A';
        foreach ($columns as $column) {
            $cell = $colIdx . $startRow;
            $sheet->setCellValue($cell, $column);
            
            // Header Styling
            $sheet->getStyle($cell)->applyFromArray([
                'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
                'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
                'fill' => [
                    'fillType' => Fill::FILL_SOLID,
                    'startColor' => ['rgb' => '4F46E5'] // Indigo
                ],
                'borders' => [
                    'allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => '000000']]
                ]
            ]);
            $colIdx++;
        }

        // --- 3. DATA ROWS ---
        $details = DB::table('transaksi_detail as td')
            ->join('produk as p', 'td.produk_id', '=', 'p.produk_id')
            ->join('transaksi as t', 'td.transaksi_id', '=', 't.transaksi_id')
            ->leftJoin('users as u', 't.kasir_id', '=', 'u.user_id')
            ->leftJoin('pelanggans as pl', 't.pelanggan_id', '=', 'pl.id')
            ->select('td.*', 'p.nama_produk', 'p.kode_barcode', 't.tanggal', 'u.nama as nama_kasir', 'pl.nama_pelanggan')
            ->orderBy('td.created_at', 'desc')
            ->get();

        $currentRow = $startRow + 1;
        $no = 1;
        foreach ($details as $row) {
            $sheet->setCellValue('A' . $currentRow, $no++);
            $sheet->setCellValue('B' . $currentRow, \Carbon\Carbon::parse($row->tanggal)->format('d/m/Y H:i'));
            $sheet->setCellValue('C' . $currentRow, $row->transaksi_id);
            $sheet->setCellValue('D' . $currentRow, $row->nama_produk);
            $sheet->setCellValue('E' . $currentRow, $row->kode_barcode ?? '-');
            $sheet->setCellValue('F' . $currentRow, $row->jumlah);
            $sheet->setCellValue('G' . $currentRow, $row->harga_satuan);
            $sheet->getStyle('G' . $currentRow)->getNumberFormat()->setFormatCode('"Rp "#,##0');
            $sheet->setCellValue('H' . $currentRow, $row->subtotal);
            $sheet->getStyle('H' . $currentRow)->getNumberFormat()->setFormatCode('"Rp "#,##0');
            $sheet->setCellValue('I' . $currentRow, $row->nama_kasir ?? 'N/A');
            $sheet->setCellValue('J' . $currentRow, $row->nama_pelanggan ?? 'Umum');

            // Set Border for each data cell
            $sheet->getStyle("A$currentRow:J$currentRow")->applyFromArray([
                'borders' => [
                    'allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => '000000']]
                ],
                'alignment' => [
                    'vertical' => Alignment::VERTICAL_CENTER
                ]
            ]);

            $currentRow++;
        }

        // --- 4. FINAL TOUCHES ---
        // Page Setup for A4 Printing
        $sheet->getPageSetup()->setPaperSize(\PhpOffice\PhpSpreadsheet\Worksheet\PageSetup::PAPERSIZE_A4);
        $sheet->getPageSetup()->setOrientation(\PhpOffice\PhpSpreadsheet\Worksheet\PageSetup::ORIENTATION_LANDSCAPE);
        $sheet->getPageSetup()->setFitToWidth(1);
        $sheet->getPageSetup()->setFitToHeight(0);

        // Auto-Size columns
        foreach (range('A', 'J') as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }

        // Add Filter
        $sheet->setAutoFilter("A$startRow:J" . ($currentRow - 1));

        // Use output stream to download directly
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="' . $fileName . '"');
        header('Cache-Control: max-age=0');

        $writer = new Xlsx($spreadsheet);
        $writer->save('php://output');
        exit;
    }
}
