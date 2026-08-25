<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

// ============================================================
// MIDDLEWARE CHECKROLE (Pemeriksa Peran)
// ============================================================
// Middleware = "satpam" yang memeriksa setiap permintaan (request)
// SEBELUM request diproses oleh controller.
//
// Middleware ini dipakai lewat route dengan sintaks:
//   middleware('role:admin')     -> hanya admin
//   middleware('role:mahasiswa') -> hanya mahasiswa
//
// Jika lolos, request diteruskan ke controller ($next).
// Jika tidak, akses dihentikan.
// ============================================================
class CheckRole
{
    // $roles = daftar peran yang diizinkan (diambil dari parameter
    //         'role:...' di route, contoh: 'role:admin,mahasiswa')
    public function handle(Request $request, Closure $next, string ...$roles): Response
    {
        // 1. Pastikan ada user yang login. Jika tidak -> lempar ke login.
        if (! $request->user()) {
            return redirect()->route('login');
        }

        // 2. Cek apakah peran user ada di daftar $roles.
        //    in_array(..., ..., true) = perbandingan ketat (===)
        //    Jika tidak cocok -> hentikan dengan kode 403.
        if (! in_array($request->user()->role, $roles, true)) {
            abort(403, 'Anda tidak memiliki akses ke halaman ini.');
        }

        // 3. Lolos semua pemeriksaan -> lanjutkan ke controller berikutnya
        return $next($request);
    }
}
