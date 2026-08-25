<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

// ============================================================
// MODEL DOSEN WALI (Tabel dosen_wali) — TABEL PERANTARA
// ============================================================
// Tabel ini menghubungkan MANY-TO-MANY antara mahasiswa dan dosen.
// Satu baris = "mahasiswa X punya dosen wali Y".
// Karena satu mahasiswa hanya punya 1 wali, tabel ini sebenarnya
// relasi "hasOne" dari sisi mahasiswa, dan "hasMany" dari sisi dosen.
// ============================================================
class DosenWali extends Model
{
    use HasFactory;

    // Nama tabel yang dipakai model ini
    protected $table = 'dosen_wali';

    // Kolom yang boleh diisi lewat mass assignment
    protected $fillable = [
        'mahasiswa_id', // FK ke tabel mahasiswa
        'dosen_id',     // FK ke tabel dosen
    ];

    // Relasi: baris dosen_wali belongsTo (menunjuk ke) 1 mahasiswa
    public function mahasiswa(): BelongsTo
    {
        return $this->belongsTo(Mahasiswa::class, 'mahasiswa_id');
    }

    // Relasi: baris dosen_wali belongsTo (menunjuk ke) 1 dosen
    public function dosen(): BelongsTo
    {
        return $this->belongsTo(Dosen::class, 'dosen_id');
    }
}
