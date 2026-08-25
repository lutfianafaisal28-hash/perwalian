<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Dosen;
use App\Models\Mahasiswa;
use App\Models\Perwalian;

// ============================================================
// CONTROLLER DASHBOARD ADMIN
// ============================================================
// Menyiapkan data statistik yang ditampilkan di halaman
// dashboard admin (admin/dashboard). Data dihitung langsung
// dari database lalu dikirim ke view.
// ============================================================
class DashboardController extends Controller
{
    public function index()
    {
        // ===== HITUNG DATA UNTUK KARTU STATISTIK =====
        // Mahasiswa::count() = jumlah semua baris di tabel mahasiswa
        $totalMahasiswa = Mahasiswa::count();
        $totalDosen = Dosen::count();
        $totalPerwalian = Perwalian::count();

        // whereDoesntHave('dosenWali') = mahasiswa yang TIDAK punya
        // relasi dosenWali sama sekali (belum ditentukan wali-nya)
        $belumWali = Mahasiswa::whereDoesntHave('dosenWali')->count();

        // ===== REKAP PERWALIAN PER BULAN (UNTUK GRAFIK) =====
        // Query agregat: kelompokkan perwalian berdasarkan bulan (YYYY-MM).
        // to_char adalah fungsi PostgreSQL untuk memformat tanggal.
        // Hasilnya misal: [{bulan: '2026-08', total: 12}, ...]
        $rekapPerBulan = Perwalian::selectRaw("to_char(tanggal, 'YYYY-MM') as bulan, count(*) as total")
            ->groupBy('bulan')
            ->orderBy('bulan')
            ->get();

        // compact() = cara singkat mengirim variabel ke view.
        // Sama seperti: return view('admin.dashboard', [
        //   'totalMahasiswa' => $totalMahasiswa, ... ]);
        return view('admin.dashboard', compact(
            'totalMahasiswa',
            'totalDosen',
            'totalPerwalian',
            'belumWali',
            'rekapPerBulan',
        ));
    }
}
