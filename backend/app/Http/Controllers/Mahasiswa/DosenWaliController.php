<?php

namespace App\Http\Controllers\Mahasiswa;

use App\Http\Controllers\Controller;
use App\Models\Dosen;
use App\Models\Mahasiswa;

// ============================================================
// CONTROLLER LIHAT DOSEN WALI (Sisi Mahasiswa)
// ============================================================
// Mahasiswa melihat informasi dosen wali yang ditetapkan untuknya.
// Data dosen wali disimpan di tabel perantara 'dosen_wali'.
// ============================================================
class DosenWaliController extends Controller
{
    // ===== HALAMAN DOSEN WALI (GET /mahasiswa/dosen-wali) =====
    public function show()
    {
        // Data mahasiswa yang login + relasi dosen walinya (eager loading)
        $mahasiswa = auth()->user()->mahasiswa()->with('dosenWali.dosen')->first();

        // Ambil data dosen dari relasi.
        // Rantai: mahasiswa -> dosenWali (tabel perantara) -> dosen.
        // ?-> = jika belum ada dosen wali, hasilnya null (tidak error).
        $dosenWali = $mahasiswa->dosenWali?->dosen;

        return view('mahasiswa.dosen-wali', compact('mahasiswa', 'dosenWali'));
    }
}
