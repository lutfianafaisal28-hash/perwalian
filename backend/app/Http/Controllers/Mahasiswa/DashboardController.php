<?php

namespace App\Http\Controllers\Mahasiswa;

use App\Http\Controllers\Controller;
use App\Models\Mahasiswa;
use App\Models\Perwalian;

// ============================================================
// CONTROLLER DASHBOARD MAHASISWA
// ============================================================
// Menyiapkan ringkasan untuk halaman dashboard mahasiswa:
// data diri + dosen wali, total perwalian, catatan terakhir,
// dan 5 riwayat terbaru.
// ============================================================
class DashboardController extends Controller
{
    public function index()
    {
        // Data mahasiswa dari user yang login, sekalian muat relasi
        // dosen walinya. with() = eager loading (hindari n+1 query).
        $mahasiswa = auth()->user()->mahasiswa()->with('dosenWali.dosen')->first();

        // Total catatan perwalian milik mahasiswa ini
        $totalPerwalian = Perwalian::where('mahasiswa_id', $mahasiswa->id)->count();

        // Catatan perwalian paling terakhir (untuk kartu info)
        $perwalianTerakhir = Perwalian::where('mahasiswa_id', $mahasiswa->id)
            ->latest('tanggal')
            ->first();

        // Riwayat 5 catatan terbaru (untuk daftar di dashboard)
        $riwayat = Perwalian::where('mahasiswa_id', $mahasiswa->id)
            ->latest('tanggal')
            ->take(5)
            ->get();

        return view('mahasiswa.dashboard', compact('mahasiswa', 'totalPerwalian', 'perwalianTerakhir', 'riwayat'));
    }
}
