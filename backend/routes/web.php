<?php

// ============================================================
// ROUTES (Daftar Alamat URL Aplikasi)
// ============================================================
// File ini adalah "peta jalan" aplikasi: setiap alamat URL yang
// dibuka user akan diteruskan ke Controller + method tertentu.
//
// Contoh: user membuka http://perwalian.test/login
//   -> Route di baris 22 menangkap URL '/login'
//   -> memanggil method showLoginForm di AuthController
//   -> lalu menampilkan halaman login.
// ============================================================

use App\Http\Controllers\Admin\DashboardController as AdminDashboardController;
use App\Http\Controllers\Admin\DosenController as AdminDosenController;
use App\Http\Controllers\Admin\DosenWaliController;
use App\Http\Controllers\Admin\MahasiswaController as AdminMahasiswaController;
use App\Http\Controllers\Admin\RekapController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\Dosen\BimbinganController;
use App\Http\Controllers\Dosen\DashboardController as DosenDashboardController;
use App\Http\Controllers\Mahasiswa\DashboardController as MahasiswaDashboardController;
use App\Http\Controllers\Mahasiswa\DosenWaliController as MahasiswaDosenWaliController;
use App\Http\Controllers\Mahasiswa\PerwalianController;
use App\Http\Controllers\Mahasiswa\ProfilController;
use Illuminate\Support\Facades\Route;

// ===== HALAMAN UTAMA =====
// Jika user membuka "/" (root), langsung diarahkan ke halaman login.
// (anonymoufunction = fungsi tanpa nama yang mengembalikan redirect)
Route::get('/', function () {
    return redirect()->route('login');
});

// ===== ROUTES LOGIN & LOGOUT (tanpa autentikasi) =====
// route('login')     = menampilkan form login   (method showLoginForm)
// route('login.post') = memproses form login     (method login)
// route('logout')    = keluar dari aplikasi      (method logout)
Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
Route::post('/login', [AuthController::class, 'login'])->name('login.post');
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// ===== KELOMPOK ROUTES YANG WAJIB LOGIN =====
// middleware('auth') = semua route di dalam blok ini hanya bisa
// diakses oleh user yang SUDAH login. Jika belum, diarahkan ke login.
Route::middleware('auth')->group(function () {
    // Dashboard umum (arahkan berdasarkan peran) — dipakai sebagai landing page
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // ===== ROUTES KHUSUS ADMIN =====
    // prefix('admin')  = semua URL diawali "admin/" (contoh: admin/mahasiswa)
    // name('admin.')   = semua nama route diawali "admin." (contoh: admin.dashboard)
    // middleware('role:admin') = hanya user dengan peran "admin" yang boleh masuk.
    //                            Dosen & mahasiswa akan ditolak (403).
    Route::prefix('admin')->name('admin.')->middleware('role:admin')->group(function () {
        Route::get('/', [AdminDashboardController::class, 'index'])->name('dashboard');

        // Export data mahasiswa ke CSV (harus didefinisikan SEBELUM
        // route resource mahasiswa, agar tidak "tertelan" oleh {mahasiswa})
        Route::get('mahasiswa/export', [AdminMahasiswaController::class, 'exportCsv'])->name('mahasiswa.export');

        // Route::resource = membuat 7 route sekaligus untuk CRUD mahasiswa:
        //   index (daftar), create (form baru), store (simpan),
        //   show (detail), edit (form ubah), update (proses ubah), destroy (hapus)
        Route::resource('mahasiswa', AdminMahasiswaController::class)
            ->only(['index', 'create', 'store', 'show', 'edit', 'update', 'destroy']);

        // Export dosen harus SEBELUM resource agar tidak tertelan {dosen}
        Route::get('dosen/export', [AdminDosenController::class, 'exportCsv'])->name('dosen.export');

        // CRUD dosen (sama seperti mahasiswa di atas)
        Route::resource('dosen', AdminDosenController::class)
            ->only(['index', 'create', 'store', 'show', 'edit', 'update', 'destroy']);

        // Penentuan dosen wali per mahasiswa
        Route::get('dosen-wali', [DosenWaliController::class, 'index'])->name('dosen-wali.index');
        Route::put('dosen-wali/{mahasiswa}', [DosenWaliController::class, 'update'])->name('dosen-wali.update');

        // Rekap perwalian: lihat, export CSV, dan hapus catatan
        Route::get('rekap', [RekapController::class, 'index'])->name('rekap.index');
        Route::get('rekap/export', [RekapController::class, 'exportCsv'])->name('rekap.export');
        Route::delete('rekap/{perwalian}', [RekapController::class, 'destroy'])->name('rekap.destroy');
    });

    // ===== ROUTES KHUSUS MAHASISWA =====
    // Hanya user berperan "mahasiswa" yang bisa mengakses (selain harus login).
    Route::prefix('mahasiswa')->name('mahasiswa.')->middleware('role:mahasiswa')->group(function () {
        Route::get('/', [MahasiswaDashboardController::class, 'index'])->name('dashboard');

        // Profil pribadi + ganti password
        Route::get('profil', [ProfilController::class, 'show'])->name('profil');
        Route::put('profil/password', [ProfilController::class, 'updatePassword'])->name('profil.password');

        // Lihat info dosen wali
        Route::get('dosen-wali', [MahasiswaDosenWaliController::class, 'show'])->name('dosen-wali');

        // CRUD perwalian milik mahasiswa yang sedang login
        Route::get('perwalian', [PerwalianController::class, 'index'])->name('perwalian.index');
        Route::get('perwalian/create', [PerwalianController::class, 'create'])->name('perwalian.create');
        Route::post('perwalian', [PerwalianController::class, 'store'])->name('perwalian.store');
        // {perwalian} = parameter dinamis (ID catatan yang ingin diedit),
        //               contoh: /mahasiswa/perwalian/5/edit berarti edit catatan id=5
        Route::get('perwalian/{perwalian}/edit', [PerwalianController::class, 'edit'])->name('perwalian.edit');
        Route::put('perwalian/{perwalian}', [PerwalianController::class, 'update'])->name('perwalian.update');
    });

    // ===== ROUTES KHUSUS DOSEN =====
    // Hanya user berperan "dosen" yang bisa mengakses.
    Route::prefix('dosen')->name('dosen.')->middleware('role:dosen')->group(function () {
        Route::get('/', [DosenDashboardController::class, 'index'])->name('dashboard');

        // Daftar mahasiswa bimbingan + lihat histori perwalian + export CSV
        Route::get('bimbingan', [BimbinganController::class, 'index'])->name('bimbingan.index');
        Route::get('bimbingan/export', [BimbinganController::class, 'exportCsv'])->name('bimbingan.export');
        Route::get('bimbingan/{mahasiswa}', [BimbinganController::class, 'show'])->name('bimbingan.show');
    });
});
