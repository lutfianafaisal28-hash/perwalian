<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

// ============================================================
// MIGRASI: TABEL DOSEN
// ============================================================
// Struktur tabel dosen:
//  - user_id : penautan ke akun login (tabel users), boleh kosong.
//  - nidn    : nomor induk dosen nasional (unik, jadi username login).
//  - nama    : nama lengkap dosen.
// ============================================================
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('dosen', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained('users')->onDelete('cascade');
            $table->string('nidn')->unique();
            $table->string('nama');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('dosen');
    }
};
