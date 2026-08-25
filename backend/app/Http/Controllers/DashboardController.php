<?php

namespace App\Http\Controllers;

// ============================================================
// CONTROLLER DASHBOARD (PENGARAH PERAN)
// ============================================================
// Controller ini dipakai sebagai halaman tujuan setelah login.
// Tugasnya hanya satu: lihat peran user yang login, lalu arahkan
// ke dashboard yang sesuai (admin / mahasiswa / dosen).
// ============================================================
class DashboardController extends Controller
{
    public function index()
    {
        // Ambil data user yang sedang login (helper bawaan Laravel)
        $user = auth()->user();

        // Arahkan sesuai peran. Setiap peran punya dashboard sendiri:
        if ($user->isAdmin()) {
            return redirect()->route('admin.dashboard');
        }

        if ($user->isMahasiswa()) {
            return redirect()->route('mahasiswa.dashboard');
        }

        if ($user->isDosen()) {
            return redirect()->route('dosen.dashboard');
        }

        // Jika peran tidak dikenali (seharusnya tidak terjadi),
        // hentikan akses dengan kode HTTP 403 (Forbidden).
        abort(403);
    }
}
