<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

// ============================================================
// CONTROLLER AUTH (Login / Logout)
// ============================================================
// Controller = "pemain peran" yang menerima permintaan dari route,
// memproses logika, lalu mengembalikan jawaban (halaman / redirect).
// Controller ini menangani semua proses login & logout user.
// ============================================================
class AuthController extends Controller
{
    // ===== MENAMPILKAN FORM LOGIN =====
    // Dipanggil saat user membuka URL /login (GET)
    public function showLoginForm()
    {
        // Jika user SUDAH login, tidak perlu melihat form login lagi.
        // Langsung arahkan ke halaman dashboard (yang otomatis memilih
        // dashboard sesuai peran: admin/mahasiswa/dosen).
        if (Auth::check()) {
            return redirect()->route('dashboard');
        }

        // Jika belum login, tampilkan halaman login.
        return view('auth.login');
    }

    // ===== MEMPROSES FORM LOGIN =====
    // Dipanggil saat form login di-submit (POST /login)
    public function login(Request $request)
    {
        // 1. Validasi: pastikan username & password diisi.
        // Jika gagal, Laravel otomatis mengembalikan user ke halaman
        // sebelumnya beserta pesan error.
        $credentials = $request->validate([
            'username' => ['required', 'string'],
            'password' => ['required', 'string'],
        ]);

        // 2. Coba autentikasi. Auth::attempt mencocokkan username+password
        //    dengan data di tabel users. Parameter kedua = "ingat saya"
        //    (remember me) -> membuat cookie login tahan lama.
        //    Jika cocok, Laravel otomatis membuat sesi login.
        if (Auth::attempt($credentials, $request->boolean('remember'))) {
            // Regenerasi ID sesi = mencegah serangan session fixation
            $request->session()->regenerate();

            // Berhasil login -> ke dashboard (otomatis sesuai peran)
            return redirect()->route('dashboard');
        }

        // 3. Gagal login: kembali ke halaman login dengan pesan error.
        //    onlyInput('username') = isi field username dipertahankan
        //    supaya user tidak perlu mengetik ulang.
        return back()->withErrors([
            'username' => 'Username atau password salah.',
        ])->onlyInput('username');
    }

    // ===== LOGOUT (KELUAR) =====
    // Dipanggil saat tombol "Keluar" diklik (POST /logout)
    public function logout(Request $request)
    {
        // Hapus sesi login user
        Auth::logout();

        // Bersihkan sesi & buat ulang token CSRF agar sesi lama
        // tidak bisa digunakan kembali (keamanan).
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        // Kembali ke halaman login
        return redirect()->route('login');
    }
}
