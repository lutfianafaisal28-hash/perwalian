<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

// ============================================================
// MIGRASI: TABEL MAHASISWA
// ============================================================
// Migration = "cetak biru" struktur tabel di database.
// up()   -> perintah yang dijalankan saat migrate (membuat tabel).
// down() -> perintah saat rollback (menghapus tabel).
//
// Struktur tabel mahasiswa:
//  - user_id  : penautan ke akun login (tabel users), boleh kosong.
//  - npm      : nomor pokok mahasiswa (unik, jadi username login).
//  - nama     : nama lengkap mahasiswa.
//  - prodi    : program studi (contoh: Teknik Informatika).
//  - angkatan : tahun masuk (contoh: 2022).
// ============================================================
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('mahasiswa', function (Blueprint $table) {
            $table->id();

            // foreignId + constrained = kolom foreign key yang otomatis
            // menautkan ke kolom 'id' di tabel 'users'.
            // onDelete('cascade') = jika user dihapus, data mahasiswa
            // terkait ikut terhapus.
            $table->foreignId('user_id')->nullable()->constrained('users')->onDelete('cascade');

            $table->string('npm')->unique(); // unique = tidak boleh dobel
            $table->string('nama');
            $table->string('prodi');
            $table->string('angkatan');

            $table->timestamps(); // otomatis kolom created_at & updated_at
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('mahasiswa');
    }
};
