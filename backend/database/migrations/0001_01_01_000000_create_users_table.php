<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

// ============================================================
// MIGRASI BAWAAN LARAVEL: TABEL USERS + PENDUKUNG LOGIN
// ============================================================
// Migration ini dibuat otomatis oleh Laravel (scaffolding) lalu
// dimodifikasi: kolom 'username' dan 'role' ditambahkan sesuai
// kebutuhan aplikasi ini.
//
// Yang dibuat:
//  1. Tabel 'users'               -> data akun (login) semua orang.
//  2. Tabel 'password_reset_tokens' -> token lupa password.
//  3. Tabel 'sessions'            -> sesi login (cookie di server).
//
// Catatan: kolom 'role' menentukan jenis akun:
//   'admin', 'dosen', atau 'mahasiswa'.
// ============================================================
return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('name');                       // nama tampilan
            $table->string('email')->nullable()->unique(); // email (boleh kosong)
            $table->string('username')->unique();         // username = NPM/NIDN/admin
            $table->string('role')->default('mahasiswa'); // peran default mahasiswa
            $table->timestamp('email_verified_at')->nullable();
            $table->string('password');                   // tersimpan sebagai hash
            $table->rememberToken();                      // kolom remember_token
            $table->timestamps();
        });

        Schema::create('password_reset_tokens', function (Blueprint $table) {
            $table->string('email')->primary();
            $table->string('token');
            $table->timestamp('created_at')->nullable();
        });

        Schema::create('sessions', function (Blueprint $table) {
            $table->string('id')->primary();
            $table->foreignId('user_id')->nullable()->index();
            $table->string('ip_address', 45)->nullable();
            $table->text('user_agent')->nullable();
            $table->longText('payload');
            $table->integer('last_activity')->index();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('users');
        Schema::dropIfExists('password_reset_tokens');
        Schema::dropIfExists('sessions');
    }
};
