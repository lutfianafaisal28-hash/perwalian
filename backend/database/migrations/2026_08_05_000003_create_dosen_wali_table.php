<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

// ============================================================
// MIGRASI: TABEL PERANTARA DOSEN_WALI
// ============================================================
// Tabel ini menghubungkan mahasiswa dengan dosen walinya.
// Karena satu mahasiswa HANYA punya satu dosen wali, kolom
// mahasiswa_id dibuat UNIQUE (tidak boleh ada dua baris untuk
// mahasiswa yang sama).
//
//  - mahasiswa_id : foreign key ke tabel mahasiswa.
//  - dosen_id     : foreign key ke tabel dosen (walinya).
// ============================================================
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('dosen_wali', function (Blueprint $table) {
            $table->id();
            $table->foreignId('mahasiswa_id')->constrained('mahasiswa')->onDelete('cascade');
            $table->foreignId('dosen_id')->constrained('dosen')->onDelete('cascade');
            $table->timestamps();

            // unique('mahasiswa_id') = setiap mahasiswa maksimal satu wali
            $table->unique('mahasiswa_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('dosen_wali');
    }
};
