<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

// ============================================================
// MODEL MAHASISWA (Tabel mahasiswa)
// ============================================================
// Mewakili tabel `mahasiswa` — data detail akademik mahasiswa.
// Data login mahasiswa (username, password) ada di tabel `users`,
// sedangkan data akademiknya (npm, prodi, angkatan) ada di sini.
// Keduanya dihubungkan lewat kolom `user_id`.
// ============================================================
class Mahasiswa extends Model
{
    use HasFactory;

    // Nama tabel yang dipakai model ini
    protected $table = 'mahasiswa';

    // Kolom yang boleh diisi lewat mass assignment
    protected $fillable = [
        'user_id', // penunjuk ke akun login (tabel users)
        'npm',     // nomor pokok mahasiswa
        'nama',    // nama lengkap mahasiswa
        'prodi',   // program studi (misal: Sistem Informasi)
        'angkatan',// tahun masuk (misal: 2022)
    ];

    // Relasi: mahasiswa belongsTo (milik) 1 user
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    // Relasi: mahasiswa hasOne (memiliki) 1 catatan dosen wali.
    // Relasi dibaca: "Mahasiswa ini punya satu DosenWali".
    public function dosenWali(): HasOne
    {
        return $this->hasOne(DosenWali::class, 'mahasiswa_id');
    }

    // Relasi: mahasiswa hasMany (memiliki banyak) catatan perwalian.
    // Dipakai untuk mengambil semua riwayat perwalian milik mahasiswa ini.
    public function perwalian(): HasMany
    {
        return $this->hasMany(Perwalian::class, 'mahasiswa_id');
    }

    // Method bantu: ambil data Dosen wali-nya langsung.
    // ?Dosen = bisa null jika dosen wali belum ditentukan.
    public function wali(): ?Dosen
    {
        return $this->dosenWali?->dosen;
    }
}
