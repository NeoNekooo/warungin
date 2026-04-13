<?php
// app/Http/Controllers/AbsensiController.php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Absensi;
use App\Models\JadwalAbsensi;
use Illuminate\Support\Facades\Crypt;
use Carbon\Carbon;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Worksheet\Drawing;

class AbsensiController extends Controller
{
public function index()
{
    // sebelum menampilkan halaman scan, jalankan proses auto‑alpa jika hari
    // sudah melewati jam pulang sehingga karyawan yang tidak pernah melakukan
    // scan dianggap absen. logika ini mirip dengan command Artisan tetapi
    // memungkinkan pemutakhiran via hit halaman ketika scheduler belum
    // disiapkan.
    $this->runAutoAlpaIfNeeded();

    // Ambil data pertama. Jika find(1) gagal, kita ambil yang paling atas
    $jadwal = \App\Models\JadwalAbsensi::first();

    // 1. Inisialisasi default agar tidak error jika $jadwal NULL
    $isLibur = false;
    $hariKerja = [];
    $liburKhusus = [];

    // 2. Ambil nama hari dalam Bahasa Indonesia
    $daftarHari = [
        1 => 'Senin', 2 => 'Selasa', 3 => 'Rabu', 
        4 => 'Kamis', 5 => 'Jumat', 6 => 'Sabtu', 7 => 'Minggu'
    ];
    $hariIniKe = now()->format('N'); 
    $hariIni = $daftarHari[$hariIniKe];
    $tanggalSekarang = now()->toDateString();

    // 3. Hanya jalankan logika jika $jadwal ditemukan
    if ($jadwal) {
        // Decode hari kerja (karena data Anda ["Senin", "Selasa", ...])
        $hariKerja = json_decode($jadwal->hari_kerja, true) ?? [];
        
        // Bersihkan string tanggal libur (misal: "2024-01-01, 2024-01-02")
        $liburKhusus = $jadwal->tanggal_libur 
            ? array_map('trim', explode(',', $jadwal->tanggal_libur)) 
            : [];

        // Tentukan apakah hari ini libur
        // LIBUR jika: Hari ini tidak terdaftar di hari_kerja ATAU ada di daftar tanggal_libur
        $isLibur = !in_array($hariIni, $hariKerja) || in_array($tanggalSekarang, $liburKhusus);
    } else {
        // Jika jadwal kosong di DB, kita anggap libur demi keamanan
        $isLibur = true;
    }

    $daftar_absensi = \App\Models\User::where('role', 'kasir')
        ->with(['absensis' => function($query) {
            $query->whereDate('waktu_scan', now()->toDateString());
        }])->get();

    return view('absensi.index', compact('jadwal', 'isLibur', 'daftar_absensi'));
}

    /**
     * Automatically mark kasir as alpa when they have no scan records by the
     * end of the day (past scheduled pulang). This is a lightweight in‑controller
     * version of the separate Artisan command so the behaviour works even if a
     * scheduler/cron isn't configured — it will run the next time someone hits
     * the scan page after jam pulang.
     */
    private function runAutoAlpaIfNeeded()
{
    $jadwal = JadwalAbsensi::first();
    if (!$jadwal) return;

    // Tambahan: Jangan jalankan auto-alpa kalau hari ini LIBUR
    $daftarHari = [1 => 'Senin', 2 => 'Selasa', 3 => 'Rabu', 4 => 'Kamis', 5 => 'Jumat', 6 => 'Sabtu', 7 => 'Minggu'];
    $hariIni = $daftarHari[now()->format('N')];
    $hariKerja = json_decode($jadwal->hari_kerja, true) ?? [];
    $tanggalSekarang = now()->toDateString();
    $liburKhusus = $jadwal->tanggal_libur ? array_map('trim', explode(',', $jadwal->tanggal_libur)) : [];

    $isLibur = !in_array($hariIni, $hariKerja) || in_array($tanggalSekarang, $liburKhusus);
    
    // Kalau libur, jangan paksa orang jadi Alpa
    if ($isLibur) return;

    $now = now();
    $jamPulang = Carbon::parse($jadwal->jam_pulang);

    // HANYA jalankan jika sudah melewati jam pulang
    if ($now->gt($jamPulang)) {
        $kasirs = User::where('role', 'kasir')->where('status', 1)->get();
        foreach ($kasirs as $k) {
            // Gunakan 'id' atau 'user_id' sesuai nama kolom primary key kamu
            $absen = Absensi::where('user_id', $k->user_id) 
                            ->whereDate('waktu_scan', $now->toDateString())
                            ->first();

            if (!$absen) {
                Absensi::create([
                    'user_id' => $k->user_id,
                    'waktu_scan' => $now,
                    'status' => 'alpa',
                    'keterangan' => 'Alpa: Tidak ada data masuk. (auto)'
                ]);
            }
        }
    }
}

public function prosesScan(Request $request)
{
    // Inisialisasi awal agar catch tidak bingung mencari variabel
    $jadwal = null;
    $user = null;

    try {
        $qr_data = trim($request->qr_data);
        $parts = explode('|', $qr_data);

        if (count($parts) !== 3) {
            return response()->json(['status' => 'error', 'message' => 'Format QR Tidak Dikenali']);
        }

        [$userId, $timestamp, $providedSignature] = $parts;

        // 1. Validasi Signature & User
        $expectedSignature = substr(hash_hmac('sha256', $userId . '|' . $timestamp, config('app.key')), 0, 8);
        if ($providedSignature !== $expectedSignature) {
            return response()->json(['status' => 'error', 'message' => 'Kode QR Tidak Sah']);
        }

        $user = User::where('user_id', $userId)->where('status', 1)->first();
        if (!$user) return response()->json(['status' => 'error', 'message' => 'User tidak aktif']);

        // 2. Ambil Jadwal (Gunakan logika pencarian yang sudah diperbaiki sebelumnya)
        $daftarHari = [
            'Sunday' => 'Minggu', 'Monday' => 'Senin', 'Tuesday' => 'Selasa',
            'Wednesday' => 'Rabu', 'Thursday' => 'Kamis', 'Friday' => 'Jumat', 'Saturday' => 'Sabtu'
        ];
        $namaHariIndo = $daftarHari[now()->format('l')];
        $jadwal = JadwalAbsensi::whereJsonContains('hari_kerja', $namaHariIndo)->first();

        if (!$jadwal) {
            return response()->json(['status' => 'error', 'message' => "Jadwal hari $namaHariIndo tidak ditemukan."]);
        }

        // 3. Setup Waktu & Peraturan
        $sekarang = now();
        $jamMasukJadwal = \Carbon\Carbon::parse($jadwal->jam_masuk);
        $jamPulangJadwal = \Carbon\Carbon::parse($jadwal->jam_pulang);

        $awalScanMasuk = (clone $jamMasukJadwal)->subMinutes(30);
        $batasTerlambat = (clone $jamMasukJadwal)->addMinutes(15);
        $batasPulang = (clone $jamPulangJadwal)->addMinutes(60);

        // Cek data absen masuk hari ini
        $absenExist = Absensi::where('user_id', $userId)
            ->whereDate('waktu_scan', now()->toDateString())
            ->first();

        // --- LOGIKA PULANG ---
        if ($sekarang->gte($jamPulangJadwal)) {
            // PERATURAN: Hanya pulang tanpa masuk = GAGAL/ALPA
            if (!$absenExist) {
                return response()->json([
                    'status' => 'error',
                    'message' => "Absensi {$user->nama} Gagal. Lakukan absensi pada saat jadwal masuk dan pulang."
                ]);
            }

            if ($absenExist->jam_pulang) {
                return response()->json(['status' => 'failed', 'message' => 'Sudah absen pulang hari ini.']);
            }

            // PERATURAN: Toleransi 60 menit pulang
            // --- DI DALAM LOGIKA PULANG ---
if ($sekarang->lte($batasPulang)) {
    $absenExist->update(['jam_pulang' => $sekarang->toTimeString()]);
    
    return response()->json([
        'status' => 'success',
        'nama' => $user->nama,
        'status_absen' => 'pulang',
        'pesan' => "Absensi {$user->nama} Berhasil.",
        // Tambahkan ini agar list terupdate otomatis:
        'waktu_masuk' => substr($absenExist->waktu_scan, 11, 5), 
        'waktu_pulang' => $sekarang->format('H:i')
    ]);
} else {
                return response()->json(['status' => 'error', 'message' => "Absensi {$user->nama} Gagal. Batas waktu pulang habis."]);
            }
        }

        // --- LOGIKA MASUK ---
        if ($absenExist) {
            return response()->json(['status' => 'failed', 'message' => 'Anda sudah melakukan absen masuk.']);
        }

        // Deklarasi variabel status secara eksplisit agar tidak kosong
        $statusFinal = null;

        // PERATURAN: 30 menit sebelum s/d Jam Masuk (Hadir)
        if ($sekarang->between($awalScanMasuk, $jamMasukJadwal)) {
            $statusFinal = 'hadir';
        } 
        // PERATURAN: Jam Masuk s/d 15 menit sesudah (Terlambat)
        elseif ($sekarang->between($jamMasukJadwal, $batasTerlambat)) {
            // late scan should always be marked as "terlambat";
            // previously there were reports of "sakit" being stored when
            // someone scanned during a late-entry schedule, so we correct it
            // here just in case the variable ever ends up with that value.
            $statusFinal = 'terlambat';
        } 
        // PERATURAN: Lebih dari 15 menit (GAGAL/ALPA) atau Terlalu Pagi
        else {
            // Do not create a record when the scan is completely outside of
            // acceptable windows. Earlier code mistakenly created a record
            // with "sakit" for some late‑entry configurations; we now simply
            // reject the scan and avoid saving anything.
            return response()->json([
                'status' => 'error',
                'message' => "Absensi {$user->nama} Gagal."
            ]);
        }

        // safety: if some strange condition accidentally gave us "sakit",
        // force it back to "terlambat". the only valid values coming from the
        // scan logic are hadir or terlambat, and we never persist sick here.
        if ($statusFinal === 'sakit') {
            $statusFinal = 'terlambat';
        }

        // Simpan data dengan statusFinal yang sudah pasti
        // --- DI BAGIAN PALING BAWAH (LOGIKA MASUK) ---
Absensi::create([
    'user_id' => $userId,
    'waktu_scan' => $sekarang,
    'status' => $statusFinal,
    'device_id_toko' => $request->ip()
]);

return response()->json([
    'status' => 'success',
    'nama' => $user->nama,
    'status_absen' => $statusFinal,
    'pesan' => "Absensi {$user->nama} Berhasil.",
    // Tambahkan ini agar list terupdate otomatis:
    'waktu_masuk' => $sekarang->format('H:i'),
    'waktu_pulang' => null
]);

    } catch (\Exception $e) {
        return response()->json(['status' => 'error', 'message' => 'Terjadi kesalahan sistem: ' . $e->getMessage()]);
    }
}

    public function simpanAbsensi(Request $request)
    {
        Absensi::create([
            'user_id' => $request->user_id,
            'waktu_scan' => now(),
            'status' => $request->status, // hadir, izin, atau sakit
            'keterangan' => $request->keterangan,
            'device_id_toko' => $request->ip(), // Opsional: gunakan IP sebagai ID perangkat sementara
        ]);

        return response()->json(['message' => 'Absensi berhasil dicatat!']);
    }

public function laporan(Request $request)
{
    // pastikan data alpa otomatis dikunci saat laporan dibuka setelah jam pulang
    $this->runAutoAlpaIfNeeded();

    // Pastikan kolom 'hari_kerja' masuk ke dalam array kedua (values)
    $jadwal = \App\Models\JadwalAbsensi::firstOrCreate(
        ['id' => 1], // Cari ID 1
        [
            'jam_masuk' => '08:00',
            'jam_pulang' => '17:00',
            // PENTING: Berikan nilai default agar database tidak komplain
            'hari_kerja' => json_encode(['Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu']),
            'tanggal_libur' => ''
        ]
    );

    $query = \App\Models\Absensi::with(['user', 'admin']);

    // Filter berdasarkan Rentang Tanggal
    if ($request->filled('start_date')) {
        $query->whereDate('waktu_scan', '>=', $request->start_date);
    }
    if ($request->filled('end_date')) {
        $query->whereDate('waktu_scan', '<=', $request->end_date);
    }

    // Filter berdasarkan Status Kehadiran
    if ($request->filled('status')) {
        $query->where('status', $request->status);
    }

    $absensis = $query->latest('waktu_scan')->paginate(15)->withQueryString();
    $karyawans = \App\Models\User::where('role', 'kasir')->where('status', 1)->get();

    return view('absensi.laporan', compact('absensis', 'karyawans', 'jadwal'));
}

        public function storeJadwal(Request $request)
{
    // 1. Validasi Input Dasar
    $request->validate([
        'jam_masuk' => 'required',
        'jam_pulang' => 'required',
        'hari_kerja' => 'required|array'
    ]);

    // 2. Logika Kunci Jadwal (Locking System)
    $daftarHari = [
        'Sunday' => 'Minggu', 'Monday' => 'Senin', 'Tuesday' => 'Selasa',
        'Wednesday' => 'Rabu', 'Thursday' => 'Kamis', 'Friday' => 'Jumat', 'Saturday' => 'Sabtu'
    ];
    $hariIni = $daftarHari[now()->format('l')];

    // Cek apakah admin mencoba merubah jadwal yang termasuk hari ini
    if (in_array($hariIni, $request->hari_kerja)) {
        // Cek apakah sudah ada aktivitas absen masuk atau pulang hari ini
        $sudahAdaAktivitas = \App\Models\Absensi::whereDate('waktu_scan', now()->toDateString())->exists();

        if ($sudahAdaAktivitas) {
            return redirect()->back()
                ->withInput() // Agar data di form tidak hilang
                ->withErrors(['kunci' => "Jadwal hari $hariIni terkunci karena absensi sudah berjalan. Gunakan fitur Koreksi Manual untuk perubahan hari ini."]);
        }
    }

    // 3. Eksekusi jika lolos validasi
    try {
        JadwalAbsensi::updateOrCreate(
            ['id' => 1],
            [
                'jam_masuk' => $request->jam_masuk,
                'jam_pulang' => $request->jam_pulang,
                'hari_kerja' => json_encode($request->hari_kerja),
                'tanggal_libur' => $request->tanggal_libur 
            ]
        );

        return redirect()->back()->with('success', 'Pengaturan operasional toko berhasil diperbarui.');
        
    } catch (\Exception $e) {
        return redirect()->back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
    }
}

    public function storeManual(Request $request)
{
    $request->validate([
        'user_id'    => 'required',
        'tanggal'    => 'required|date',
        'jam_manual' => 'required', // Format jam dari input
        'status'     => 'required|in:hadir,izin,sakit,terlambat',
        'keterangan' => 'required|string|max:255'
    ]);

    // Gabungkan tanggal dan jam menjadi format datetime yang valid
    $waktuFull = $request->tanggal . ' ' . $request->jam_manual;

    // Cari data berdasarkan user_id dan TANGGAL (tanpa jam)
    $absenExist = \App\Models\Absensi::where('user_id', $request->user_id)
        ->whereDate('waktu_scan', $request->tanggal)
        ->first();

    if ($absenExist) {
        // SKENARIO A: UPDATE (Jika sudah ada data hari itu)
        $absenExist->update([
            'waktu_scan'     => $waktuFull,
            'status'         => $request->status,
            'keterangan'     => $request->keterangan,
            'device_id_toko' => 'KOREKSI_ADMIN' // Penanda bahwa data diubah admin
        ]);
        $pesan = "Data absensi berhasil diperbarui (Koreksi).";
    } else {
        // SKENARIO B: INSERT (Jika belum ada data hari itu)
        \App\Models\Absensi::create([
            'user_id'        => $request->user_id,
            'waktu_scan'     => $waktuFull,
            'status'         => $request->status,
            'keterangan'     => $request->keterangan,
            'device_id_toko' => 'MANUAL_ADMIN' // Penanda bahwa diinput manual oleh admin
        ]);
        $pesan = "Data absensi manual berhasil ditambahkan.";
    }

    return redirect()->back()->with('success', $pesan);
}

public function exportExcel(Request $request)
{
    $fileName = 'laporan-absensi-' . date('Y-m-d') . '.xlsx';
    $spreadsheet = new Spreadsheet();
    $sheet = $spreadsheet->getActiveSheet();
    $sheet->setTitle('Laporan Absensi');

    // --- 1. HEADER (Logo & Title) ---
    $lastCol = 'I'; // A to I

    // Drawing Logo
    $logoPath = public_path('assets/img/logo.png');
    if (file_exists($logoPath)) {
        $drawing = new Drawing();
        $drawing->setName('Logo Warungin');
        $drawing->setDescription('Logo');
        $drawing->setPath($logoPath);
        $drawing->setHeight(70);
        $drawing->setCoordinates('A1');
        $drawing->setOffsetX(330); 
        $drawing->setOffsetY(5);
        $drawing->setWorksheet($sheet);
    }
    $sheet->getRowDimension(1)->setRowHeight(60);

    // Title "WARUNGIN" 
    $sheet->mergeCells("A2:{$lastCol}2");
    $sheet->setCellValue('A2', 'WARUNGIN');
    $sheet->getStyle('A2')->applyFromArray([
        'font' => ['bold' => true, 'size' => 24, 'color' => ['rgb' => '4F46E5']],
        'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER]
    ]);

    // Subtitle
    $sheet->mergeCells("A3:{$lastCol}3");
    $sheet->setCellValue('A3', 'LAPORAN ABSENSI KARYAWAN');
    $sheet->getStyle('A3')->applyFromArray([
        'font' => ['bold' => true, 'size' => 14],
        'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER]
    ]);

    // Metadata Filter
    $filterText = 'Periode: ' . ($request->start_date ?? 'Semua') . ' s/d ' . ($request->end_date ?? 'Semua');
    if ($request->filled('status')) {
        $filterText .= ' | Status: ' . strtoupper($request->status);
    }
    $sheet->mergeCells("A4:{$lastCol}4");
    $sheet->setCellValue('A4', $filterText);
    $sheet->getStyle('A4')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

    // --- 2. TABLE HEADERS ---
    $startRow = 6;
    $columns = ['No', 'Nama Karyawan', 'Role', 'Tanggal', 'Jam Masuk', 'Jam Pulang', 'Status', 'Keterangan', 'Petugas Scan'];
    
    $colIdx = 'A';
    foreach ($columns as $column) {
        $cell = $colIdx . $startRow;
        $sheet->setCellValue($cell, $column);
        $sheet->getStyle($cell)->applyFromArray([
            'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
            'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => '4F46E5']],
            'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN]]
        ]);
        $colIdx++;
    }

    // --- 3. DATA ROWS ---
    $query = \App\Models\Absensi::with(['user', 'admin']);
    if ($request->filled('start_date')) $query->whereDate('waktu_scan', '>=', $request->start_date);
    if ($request->filled('end_date')) $query->whereDate('waktu_scan', '<=', $request->end_date);
    if ($request->filled('status')) $query->where('status', $request->status);
    
    $data = $query->orderBy('waktu_scan', 'desc')->get();

    $currentRow = $startRow + 1;
    foreach ($data as $index => $row) {
        $sheet->setCellValue('A' . $currentRow, $index + 1);
        $sheet->setCellValue('B' . $currentRow, $row->user->nama ?? 'N/A');
        $sheet->setCellValue('C' . $currentRow, ucfirst($row->user->role ?? 'Staff'));
        $sheet->setCellValue('D' . $currentRow, Carbon::parse($row->waktu_scan)->format('d/m/Y'));
        $sheet->setCellValue('E' . $currentRow, Carbon::parse($row->waktu_scan)->format('H:i:s'));
        $sheet->setCellValue('F' . $currentRow, $row->jam_pulang ? Carbon::parse($row->jam_pulang)->format('H:i:s') : '-');
        $sheet->setCellValue('G' . $currentRow, strtoupper($row->status));
        $sheet->setCellValue('H' . $currentRow, $row->keterangan ?? 'N/A');
        $sheet->setCellValue('I' . $currentRow, $row->admin->nama ?? 'Sistem QR');

        // Styling Border & Alignment
        $sheet->getStyle("A$currentRow:I$currentRow")->applyFromArray([
            'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN]],
            'alignment' => ['vertical' => Alignment::VERTICAL_CENTER]
        ]);
        $sheet->getStyle("G$currentRow")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

        $currentRow++;
    }

    // Auto-Size & A4 Setup
    foreach (range('A', 'I') as $col) {
        $sheet->getColumnDimension($col)->setAutoSize(true);
    }
    
    header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
    header('Content-Disposition: attachment;filename="' . $fileName . '"');
    header('Cache-Control: max-age=0');

    $writer = new Xlsx($spreadsheet);
    $writer->save('php://output');
    exit;
}
}