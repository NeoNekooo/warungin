<?php
namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\User;
use App\Models\Absensi;
use Carbon\Carbon;

class AutoAlpaAttendance extends Command
{
    // Ini adalah nama perintah yang akan dipanggil nanti
    protected $signature = 'absensi:auto-alpa';

    protected $description = 'Mengubah status kasir menjadi Alpa jika tidak absen masuk atau pulang';

    public function handle()
    {
        // Ambil semua user dengan role kasir yang aktif
        $kasirs = User::where('role', 'kasir')->where('status', 1)->get();

        foreach ($kasirs as $kasir) {
            $absen = Absensi::where('user_id', $kasir->user_id)
                            ->whereDate('waktu_scan', Carbon::today())
                            ->first();

            if (!$absen) {
                // Kasus: Tidak scan masuk sama sekali
                Absensi::create([
                    'user_id' => $kasir->user_id,
                    'waktu_scan' => now(), // Sebagai penanda tanggal saja
                    'status' => 'alpa',
                    'keterangan' => 'Alpa: Tidak ada data masuk.'
                ]);
            } elseif ($absen && !$absen->jam_pulang && $absen->status != 'alpa') {
                // Kasus: Scan masuk tapi tidak scan pulang
                $absen->update([
                    'status' => 'alpa',
                    'keterangan' => 'Alpa: Lupa scan pulang.'
                ]);
            }
        }

        $this->info('Proses auto-alpa selesai dijalankan.');
    }
}