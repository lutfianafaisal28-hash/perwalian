<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

// ============================================================
// MODEL USER (Tabel users)
// ============================================================
// Model mewakili satu tabel database. Di sini "User" mewakili
// tabel `users` yang berisi akun login: admin, dosen, dan mahasiswa.
// Semua akun disimpan di satu tabel, dibedakan lewat kolom `role`.
// ============================================================
class User extends Authenticatable
{
    // HasFactory  = memungkinkan membuat data uji (factory) untuk testing
    // Notifiable  = fitur notifikasi bawaan Laravel
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

    /**
     * $fillable = daftar kolom yang BOLEH diisi lewat mass assignment
     * (misal: User::create([...])). Kolom di luar daftar ini TIDAK bisa
     * diisi sekaligus — ini langkah keamanan untuk mencegah pengisian
     * kolom sensitif (seperti role) dari input user.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',       // nama lengkap user
        'email',      // alamat email
        'username',   // username untuk login (NPM/NIDN/admin)
        'role',       // peran: admin | mahasiswa | dosen
        'password',   // password (otomatis di-hash, lihat casts)
    ];

    /**
     * $hidden = kolom yang disembunyikan saat data dikirim sebagai JSON.
     * Password dan token ingat-saya tidak boleh bocor keluar.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    // ===== KONSTANTA PERAN =====
    // Menyimpan nilai string peran agar kode konsisten & tidak salah ketik
    public const ROLE_ADMIN = 'admin';
    public const ROLE_MAHASISWA = 'mahasiswa';
    public const ROLE_DOSEN = 'dosen';

    // Daftar semua peran yang tersedia di aplikasi
    public const ROLES = [
        self::ROLE_ADMIN,
        self::ROLE_MAHASISWA,
        self::ROLE_DOSEN,
    ];

    /**
     * casts = konversi otomatis tipe data kolom saat diambil/diisi.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime', // kolom ini dianggap objek tanggal
            'password' => 'hashed',            // password otomatis di-hash (bcrypt) saat disimpan
        ];
    }

    // ===== METHOD PEMERIKSA PERAN =====
    // Cara cepat mengecek peran user yang sedang login.
    // Contoh pemakaian: if ($user->isAdmin()) { ... }
    public function isAdmin(): bool
    {
        return $this->role === self::ROLE_ADMIN;
    }

    public function isMahasiswa(): bool
    {
        return $this->role === self::ROLE_MAHASISWA;
    }

    public function isDosen(): bool
    {
        return $this->role === self::ROLE_DOSEN;
    }

    // ===== RELASI =====
    // Satu user MAHASISWA bisa memiliki 1 data mahasiswa terkait.
    // Relasi dibaca: "User ini hasOne Mahasiswa lewat kolom user_id".
    public function mahasiswa(): \Illuminate\Database\Eloquent\Relations\HasOne
    {
        return $this->hasOne(Mahasiswa::class, 'user_id');
    }

    // Satu user DOSEN bisa memiliki 1 data dosen terkait.
    public function dosen(): \Illuminate\Database\Eloquent\Relations\HasOne
    {
        return $this->hasOne(Dosen::class, 'user_id');
    }
}
