<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

// ============================================================
// MIGRASI: TABEL PERWALIAN (Catatan Hasil Bimbingan)
// ============================================================
// Satu mahasiswa bisa punya BANYAK catatan perwalian (one-to-many).
// Struktur:
//  - mahasiswa_id      : pemilik catatan (foreign key ke mahasiswa).
//  - tanggal           : tanggal perwalian berlangsung.
//  - semester          : semester saat perwalian (contoh: '5').
//  - hasil_perwalian   : isi diskusi (wajib diisi).
//  - kendala           : kendala yang dihadapi (opsional).
//  - rencana_perbaikan : rencana tindak lanjut (opsional).
// ============================================================
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('perwalian', function (Blueprint $table) {
            $table->id();
            $table->foreignId('mahasiswa_id')->constrained('mahasiswa')->onDelete('cascade');
            $table->date('tanggal');
            $table->string('semester');
            $table->text('hasil_perwalian');
            $table->text('kendala')->nullable(); // nullable = boleh kosong
            $table->text('rencana_perbaikan')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('perwalian');
    }
};
