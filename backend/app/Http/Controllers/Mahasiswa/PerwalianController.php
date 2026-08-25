<?php

namespace App\Http\Controllers\Mahasiswa;

use App\Http\Controllers\Controller;
use App\Models\Mahasiswa;
use App\Models\Perwalian;
use Illuminate\Http\Request;

// ============================================================
// CONTROLLER CATATAN PERWALIAN (Sisi Mahasiswa)
// ============================================================
// Mahasiswa mencatat hasil perwaliannya: tanggal, semester,
// hasil diskusi, kendala, dan rencana perbaikan.
//
// Catatan penting: setiap operasi edit/update diperiksa dulu
// apakah catatan itu milik mahasiswa yang login (abort_unless),
// agar mahasiswa tidak bisa mengubah catatan milik orang lain.
// ============================================================
class PerwalianController extends Controller
{
    // ===== DAFTAR CATATAN PERWALIAN (GET /mahasiswa/perwalian) =====
    public function index()
    {
        // Mahasiswa yang sedang login
        $mahasiswa = auth()->user()->mahasiswa()->first();

        // Semua catatan perwalian miliknya, dari tanggal terbaru
        $perwalian = Perwalian::where('mahasiswa_id', $mahasiswa->id)
            ->latest('tanggal')
            ->get();

        return view('mahasiswa.perwalian.index', compact('perwalian'));
    }

    // ===== FORM TAMBAH CATATAN (GET /mahasiswa/perwalian/create) =====
    public function create()
    {
        // Muat relasi dosen wali untuk ditampilkan di form
        $mahasiswa = auth()->user()->mahasiswa()->with('dosenWali.dosen')->first();

        return view('mahasiswa.perwalian.create', compact('mahasiswa'));
    }

    // ===== SIMPAN CATATAN BARU (POST /mahasiswa/perwalian) =====
    public function store(Request $request)
    {
        $mahasiswa = auth()->user()->mahasiswa()->first();

        // Validasi input. Parameter kedua adalah pesan error kustom
        // dalam Bahasa Indonesia supaya user paham kesalahannya.
        $data = $request->validate([
            'tanggal' => ['required', 'date'],
            'semester' => ['required', 'string', 'max:20'],
            'hasil_perwalian' => ['required', 'string'],
            'kendala' => ['nullable', 'string'],
            'rencana_perbaikan' => ['nullable', 'string'],
        ], [
            'tanggal.required' => 'Tanggal perwalian wajib diisi.',
            'semester.required' => 'Semester wajib diisi.',
            'hasil_perwalian.required' => 'Hasil diskusi wajib diisi.',
        ]);

        // Simpan catatan, tautkan ke mahasiswa yang login
        Perwalian::create([
            'mahasiswa_id' => $mahasiswa->id,
            'tanggal' => $data['tanggal'],
            'semester' => $data['semester'],
            'hasil_perwalian' => $data['hasil_perwalian'],
            'kendala' => $data['kendala'],
            'rencana_perbaikan' => $data['rencana_perbaikan'],
        ]);

        return redirect()
            ->route('mahasiswa.perwalian.index')
            ->with('success', 'Catatan perwalian berhasil disimpan.');
    }

    // ===== FORM EDIT CATATAN (GET /mahasiswa/perwalian/{perwalian}/edit) =====
    public function edit(Perwalian $perwalian)
    {
        $mahasiswa = auth()->user()->mahasiswa()->with('dosenWali.dosen')->first();

        // PEMERIKSAAN: catatan harus milik mahasiswa ini.
        // Jika tidak -> hentikan dengan 403 Forbidden.
        abort_unless($perwalian->mahasiswa_id === $mahasiswa->id, 403);

        return view('mahasiswa.perwalian.edit', compact('perwalian', 'mahasiswa'));
    }

    // ===== PERBARUI CATATAN (PUT /mahasiswa/perwalian/{perwalian}) =====
    public function update(Request $request, Perwalian $perwalian)
    {
        $mahasiswa = auth()->user()->mahasiswa()->first();

        // Pemeriksaan kepemilikan yang sama seperti edit()
        abort_unless($perwalian->mahasiswa_id === $mahasiswa->id, 403);

        $data = $request->validate([
            'tanggal' => ['required', 'date'],
            'semester' => ['required', 'string', 'max:20'],
            'hasil_perwalian' => ['required', 'string'],
            'kendala' => ['nullable', 'string'],
            'rencana_perbaikan' => ['nullable', 'string'],
        ], [
            'tanggal.required' => 'Tanggal perwalian wajib diisi.',
            'semester.required' => 'Semester wajib diisi.',
            'hasil_perwalian.required' => 'Hasil diskusi wajib diisi.',
        ]);

        $perwalian->update([
            'tanggal' => $data['tanggal'],
            'semester' => $data['semester'],
            'hasil_perwalian' => $data['hasil_perwalian'],
            'kendala' => $data['kendala'],
            'rencana_perbaikan' => $data['rencana_perbaikan'],
        ]);

        return redirect()
            ->route('mahasiswa.perwalian.index')
            ->with('success', 'Catatan perwalian berhasil diperbarui.');
    }
}
