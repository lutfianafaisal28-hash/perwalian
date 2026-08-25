<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Dosen;
use App\Models\DosenWali;
use App\Models\Mahasiswa;
use Illuminate\Http\Request;

// ============================================================
// CONTROLLER PENETAPAN DOSEN WALI (Sisi Admin)
// ============================================================
// Halaman ini dipakai admin untuk menetapkan dosen wali bagi
// setiap mahasiswa. Penetapan disimpan di tabel perantara
// 'dosen_wali' (mahasiswa_id -> dosen_id).
// ============================================================
class DosenWaliController extends Controller
{
    // ===== DAFTAR PENETAPAN WALI (GET /admin/dosen-wali) =====
    public function index(Request $request)
    {
        // Tampilkan semua mahasiswa beserta dosen walinya (jika sudah ada)
        $query = Mahasiswa::with('dosenWali.dosen');

        // Fitur pencarian mahasiswa
        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->where(function ($q) use ($search) {
                $q->where('npm', 'ilike', "%{$search}%")
                    ->orWhere('nama', 'ilike', "%{$search}%")
                    ->orWhere('angkatan', 'ilike', "%{$search}%");
            });
        }

        $mahasiswa = $query->latest()->paginate(6)->withQueryString();

        // Daftar dosen untuk dropdown pilihan wali
        $dosen = Dosen::orderBy('nama')->get();

        return view('admin.dosen-wali.index', compact('mahasiswa', 'dosen'));
    }

    // ===== SIMPAN/TETAPKAN WALI (PUT /admin/dosen-wali/{mahasiswa}) =====
    public function update(Request $request, Mahasiswa $mahasiswa)
    {
        // Validasi: wajib pilih dosen yang id-nya ada di tabel dosen
        $data = $request->validate([
            'dosen_id' => ['required', 'exists:dosen,id'],
        ]);

        // updateOrCreate = kalau mahasiswa ini sudah punya baris di
        // tabel dosen_wali, perbarui; kalau belum, buat baris baru.
        // Syarat pencocokannya: ['mahasiswa_id' => $mahasiswa->id]
        DosenWali::updateOrCreate(
            ['mahasiswa_id' => $mahasiswa->id],
            ['dosen_id' => $data['dosen_id']],
        );

        return redirect()
            ->route('admin.dosen-wali.index')
            ->with('success', 'Dosen wali untuk '.$mahasiswa->nama.' berhasil ditetapkan.');
    }
}
