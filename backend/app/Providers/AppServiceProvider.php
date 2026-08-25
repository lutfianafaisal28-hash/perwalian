<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

// ============================================================
// APPSERVICEPROVIDER (Penyedia Layanan Utama Aplikasi)
// ============================================================
// Provider = "pemasok layanan" bawaan Laravel yang dipanggil saat
// aplikasi dimulai (bootstrap). Ada banyak provider bawaan; kelas
// ini adalah satu-satunya provider khusus milik aplikasi ini.
//
// Dua method penting:
//   register() -> daftarkan layanan/binding ke container.
//   boot()     -> dijalankan SETELAH semua provider ter-register;
//                 tempat yang tepat untuk mendaftarkan hal yang
//                 dibutuhkan aplikasi (mis. aturan validasi kustom,
//                 view composer, model observer, dll).
//
// Kedua method masih kosong karena aplikasi ini belum
// membutuhkan penyesuaian tersebut.
// ============================================================
class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        if (str_starts_with(config('app.url', ''), 'https://')) {
            \Illuminate\Support\Facades\URL::forceScheme('https');
            return;
        }

        $proto = $_SERVER['HTTP_X_FORWARDED_PROTO'] ?? $_SERVER['REQUEST_SCHEME'] ?? '';
        if ($proto === 'https' || request()->isSecure()) {
            \Illuminate\Support\Facades\URL::forceScheme('https');
        }
    }
}
