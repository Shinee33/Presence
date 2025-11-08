<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index() 
    {
        // Pastikan login sebagai karyawan
        if (!Auth::guard('karyawan')->check()) {
            return redirect('/')->with('warning', 'Silakan login terlebih dahulu');
        }

        $user = Auth::guard('karyawan')->user();

        // Validasi data pegawai
        if (!$user || empty($user->nama_lengkap)) {
            Auth::guard('karyawan')->logout();
            return redirect('/')->with('warning', 'Session tidak valid, silakan login kembali');
        }

        $hariini   = date("Y-m-d");
        $bulanini  = (int) date("m");
        $tahunini  = (int) date("Y");
        $nama_lengkap = $user->nama_lengkap;

        // Presensi hari ini
        $presensihariini = DB::table('presensi')
            ->where('nama_lengkap', $nama_lengkap)
            ->where('tgl_presensi', $hariini)
            ->first();

        // Riwayat bulan ini
        $historibulanini = DB::table('presensi')
            ->where('nama_lengkap', $nama_lengkap)
            ->whereRaw('MONTH(tgl_presensi) = ?', [$bulanini])
            ->whereRaw('YEAR(tgl_presensi) = ?', [$tahunini])
            ->orderBy('tgl_presensi')
            ->get();

        // Rekap kehadiran bulan ini
        $attendanceCount = DB::table('presensi')
            ->where('nama_lengkap', $nama_lengkap)
            ->whereRaw('MONTH(tgl_presensi) = ?', [$bulanini])
            ->whereRaw('YEAR(tgl_presensi) = ?', [$tahunini])
            ->whereNotNull('jam_in')
            ->count();

        $lateCount = DB::table('presensi')
            ->where('nama_lengkap', $nama_lengkap)
            ->whereRaw('MONTH(tgl_presensi) = ?', [$bulanini])
            ->whereRaw('YEAR(tgl_presensi) = ?', [$tahunini])
            ->where('jam_in', '>', '08:00:00')
            ->count();

        // Data izin dan sakit
        $rekapizin = DB::table('pengajuan_izin')
            ->selectRaw('SUM(IF(status="I", 1, 0)) as jmlizin, SUM(IF(status="S", 1, 0)) as jmlsakit')
            ->where('nama_lengkap', $nama_lengkap)
            ->whereRaw('MONTH(tgl_izin) = ?', [$bulanini])
            ->whereRaw('YEAR(tgl_izin) = ?', [$tahunini])
            ->where('status_approved', 1)
            ->first();

        // Hitung jumlah alfa (hari kerja - hadir - izin - sakit)
        $totalHariKerja = $this->hitungHariKerjaBulanIni($bulanini, $tahunini);
        $jmlalpa = max(0, $totalHariKerja - $attendanceCount - ($rekapizin->jmlizin ?? 0) - ($rekapizin->jmlsakit ?? 0));

        $rekappresensi = [
            'jmlhadir'     => $attendanceCount,
            'jmlterlambat' => $lateCount,
            'jmlizin'      => $rekapizin->jmlizin ?? 0,
            'jmlsakit'     => $rekapizin->jmlsakit ?? 0,
            'jmlalpa'      => $jmlalpa
        ];

        $namabulan = ["", "Januari", "Februari", "Maret", "April", "Mei", "Juni", "Juli", "Agustus", "September", "Oktober", "November", "Desember"];

        return view('dashboard.dashboard', compact(
            'presensihariini',
            'historibulanini',
            'namabulan',
            'bulanini',
            'tahunini',
            'rekappresensi',
            'rekapizin'
        ));
        
    }

    private function hitungHariKerjaBulanIni($bulan, $tahun)
    {
        $totalHari = cal_days_in_month(CAL_GREGORIAN, $bulan, $tahun);
        $hariKerja = 0;
        for ($i = 1; $i <= $totalHari; $i++) {
            $hariKe = date('N', strtotime("$tahun-$bulan-$i")); // 1 = Senin, 7 = Minggu
            if ($hariKe < 6) { // Senin–Jumat
                $hariKerja++;
            }
        }
        return $hariKerja;
    }

    public function dashboardadmin() 
    {
        $hariini = date("Y-m-d");

        // Ambil data kehadiran dari tabel presensi
        $rekappresensi = DB::table('presensi')
            ->selectRaw('COUNT(user_id) as jmlhadir, SUM(IF(jam_in > "08:00", 1, 0)) as jmlterlambat')
            ->where('tgl_presensi', $hariini)
            ->first();

        // Ambil data izin dari tabel pengajuan_izin
        $rekapizin = DB::table('pengajuan_izin')
            ->selectRaw('SUM(IF(status="I", 1, 0)) as jmlizin, SUM(IF(status="S", 1, 0)) as jmlsakit')
            ->where('tgl_izin', $hariini)
            ->where('status_approved', 1)
            ->first();
            
        $jumlahkaryawan = DB::table('pegawai')->count();
        
        return view('dashboard.dashboardadmin', compact('rekappresensi', 'rekapizin', 'jumlahkaryawan'));
    }
}

// ALTERNATIF SOLUSI dengan try-catch:
class DashboardControllerAlternative extends Controller
{
    public function index() 
    {
        try {
            $hariini = date("Y-m-d");
            $bulanini = date("m") * 1;
            $tahunini = date("Y");
            
            // Check authentication with null coalescing
            $user = Auth::guard('karyawan')->user();
            $nama_lengkap = $user?->nama_lengkap ?? null;
            
            if (!$nama_lengkap) {
                return redirect('/login')->with('error', 'Silakan login terlebih dahulu');
            }
            
            $presensihariini = DB::table('presensi')
                ->where('nama_lengkap', $nama_lengkap)
                ->where('tgl_presensi', $hariini)
                ->first();
            
            $historibulanini = DB::table('presensi')
                ->where('nama_lengkap', $nama_lengkap)
                ->whereRaw('Month(tgl_presensi)="'. $bulanini . '"')
                ->whereRaw('YEAR(tgl_presensi)="'. $tahunini. '"')
                ->get();
            
            return view('dashboard', compact('presensihariini', 'historibulanini'));
            
        } catch (\Exception $e) {
            return redirect('/login')->with('error', 'Terjadi kesalahan, silakan login kembali');
        }
    }
}