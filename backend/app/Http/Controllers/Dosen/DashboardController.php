<?php

namespace App\Http\Controllers\Dosen;

use App\Http\Controllers\Controller;
use App\Models\Dosen;
use App\Models\Mahasiswa;
use App\Models\Perwalian;
use Illuminate\Http\Request;

// ============================================================
// CONTROLLER DASHBOARD DOSEN
// ============================================================
// Menyiapkan data ringkasan untuk halaman dashboard dosen:
// jumlah mahasiswa bimbingan, total catatan perwalian, dan
// daftar bimbingan beserta jumlah catatannya.
// ============================================================
class DashboardController extends Controller
{
    public function index()
    {
        // Ambil data dosen dari user yang sedang login.
        // User relasi 'dosen' adalah one-to-one, jadi ambil yang pertama.
        $dosen = auth()->user()->dosen()->first();

        // Jumlah seluruh mahasiswa yang dibimbing dosen ini
        $jumlahBimbingan = $dosen->mahasiswaBimbingan()->count();

        // Ambil daftar bimbingan + preload relasi perwalian (catatannya)
        $bimbingan = $dosen->mahasiswaBimbingan()->with('perwalian')->get();

        // Total semua catatan perwalian dari SEMUA mahasiswa bimbingan.
        // sum(fn) = jumlahkan hasil closure untuk tiap mahasiswa.
        $totalCatatan = $bimbingan->sum(fn ($m) => $m->perwalian->count());

        return view('dosen.dashboard', compact('dosen', 'jumlahBimbingan', 'totalCatatan', 'bimbingan'));
    }
}
