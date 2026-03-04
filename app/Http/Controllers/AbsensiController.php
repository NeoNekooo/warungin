<?php
// app/Http/Controllers/AbsensiController.php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Absensi;
use App\Models\JadwalAbsensi;
use Illuminate\Support\Facades\Crypt;
use Carbon\Carbon;

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
            $absen = Absensi::where('user_id', $k->id) 
                            ->whereDate('waktu_scan', $now->toDateString())
                            ->first();

            if (!$absen) {
                Absensi::create([
                    'user_id' => $k->id,
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

    // ... sisa logic filter dan pagination Anda ...
    $query = \App\Models\Absensi::with(['user', 'admin']);
    // ...
    
    $absensis = $query->latest('waktu_scan')->paginate(15);
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
}