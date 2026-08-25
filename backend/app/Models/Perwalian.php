<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

// ============================================================
// MODEL PERWALIAN (Tabel perwalian)
// ============================================================
// Mewakili tabel `perwalian` — satu baris = SATU CATATAN hasil
// perwalian yang diisi mahasiswa (hasil diskusi, kendala, rencana).
// ============================================================
class Perwalian extends Model
{
    use HasFactory;

    // Nama tabel yang dipakai model ini
    protected $table = 'perwalian';

    // Kolom yang boleh diisi lewat mass assignment
    protected $fillable = [
        'mahasiswa_id',       // FK ke tabel mahasiswa (siapa yang mengisi)
        'tanggal',            // tanggal perwalian dilakukan
        'semester',           // semester saat perwalian
        'hasil_perwalian',    // isi hasil diskusi dengan dosen wali (WAJIB)
        'kendala',            // kendala yang dihadapi mahasiswa (opsional)
        'rencana_perbaikan',  // rencana perbaikan ke depan (opsional)
    ];

    // casts: kolom 'tanggal' otomatis diubah menjadi objek Carbon (tanggal),
    // sehingga bisa diformat: $perwalian->tanggal->format('d M Y')
    protected $casts = [
        'tanggal' => 'date',
    ];

    // Relasi: catatan perwalian belongsTo (milik) 1 mahasiswa.
    // Dipakai untuk akses $perwalian->mahasiswa->nama, dst.
    public function mahasiswa(): BelongsTo
    {
        return $this->belongsTo(Mahasiswa::class, 'mahasiswa_id');
    }
}
