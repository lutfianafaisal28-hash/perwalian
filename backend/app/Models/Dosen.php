<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

// ============================================================
// MODEL DOSEN (Tabel dosen)
// ============================================================
// Mewakili tabel `dosen` — data detail dosen (nidn, nama).
// Akun login dosen ada di tabel `users`, dihubungkan lewat `user_id`.
// ============================================================
class Dosen extends Model
{
    use HasFactory;

    // Nama tabel yang dipakai model ini
    protected $table = 'dosen';

    // Kolom yang boleh diisi lewat mass assignment
    protected $fillable = [
        'user_id', // penunjuk ke akun login (tabel users)
        'nidn',    // nomor induk dosen nasional
        'nama',    // nama lengkap dosen
    ];

    // Relasi: dosen belongsTo (milik) 1 user
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    // Relasi: dosen hasMany (memiliki banyak) catatan "DosenWali".
    // Artinya: satu dosen bisa menjadi wali dari banyak mahasiswa.
    public function bimbingan(): HasMany
    {
        return $this->hasMany(DosenWali::class, 'dosen_id');
    }

    // Relasi LINTAS: ambil semua mahasiswa bimbingan dosen ini.
    //
    // Struktur relasinya:
    //   dosen  ---hasMany--->  dosen_wali  ---belongsTo--->  mahasiswa
    // (lewat kolom dosen_id)                  (lewat kolom mahasiswa_id)
    //
    // hasManyThrough membuat jalan pintas: langsung ambil Mahasiswa
    // yang ada di tabel dosen_wali untuk dosen ini, tanpa query terpisah.
    public function mahasiswaBimbingan(): \Illuminate\Database\Eloquent\Relations\HasManyThrough
    {
        return $this->hasManyThrough(
            Mahasiswa::class,   // model tujuan yang ingin diambil
            DosenWali::class,   // model perantara
            'dosen_id',         // kolom FK di tabel perantara (dosen_wali.dosen_id)
            'id',               // kolom PK di tabel tujuan (mahasiswa.id)
            'id',               // kolom PK di model awal (dosen.id)
            'mahasiswa_id'      // kolom FK di tabel perantara (dosen_wali.mahasiswa_id)
        );
    }
}
