<?php

namespace App\Http\Controllers\Mahasiswa;

use App\Http\Controllers\Controller;
use App\Models\Mahasiswa;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

// ============================================================
// CONTROLLER PROFIL MAHASISWA
// ============================================================
// Mahasiswa melihat profilnya dan mengganti password akunnya.
// Proses ganti password wajib memverifikasi password lama dulu.
// ============================================================
class ProfilController extends Controller
{
    // ===== HALAMAN PROFIL (GET /mahasiswa/profil) =====
    public function show()
    {
        // Data mahasiswa yang login + dosen walinya (untuk info)
        $mahasiswa = auth()->user()->mahasiswa()->with('dosenWali.dosen')->first();

        return view('mahasiswa.profil', compact('mahasiswa'));
    }

    // ===== GANTI PASSWORD (PUT/POST /mahasiswa/profil/password) =====
    public function updatePassword(Request $request)
    {
        // Validasi: password lama wajib, password baru minimal 6 karakter
        // dan harus sama dengan field konfirmasi ('confirmed' otomatis
        // membandingkan dengan input 'password_baru_confirmation').
        $data = $request->validate([
            'password_lama' => ['required', 'string'],
            'password_baru' => ['required', 'string', 'min:6', 'confirmed'],
        ], [
            'password_lama.required' => 'Password lama wajib diisi.',
            'password_baru.required' => 'Password baru wajib diisi.',
            'password_baru.min' => 'Password baru minimal 6 karakter.',
            'password_baru.confirmed' => 'Konfirmasi password tidak sama.',
        ]);

        // Cek apakah password lama sesuai dengan yang tersimpan.
        // Hash::check membandingkan input dengan hash bcrypt di database.
        if (! Hash::check($data['password_lama'], auth()->user()->password)) {
            return back()->withErrors(['password_lama' => 'Password lama salah.']);
        }

        // Update password user yang login.
        // Model User punya mutator yang otomatis meng-hash nilai baru ini.
        auth()->user()->update(['password' => $data['password_baru']]);

        return back()->with('success', 'Password berhasil diganti.');
    }
}
