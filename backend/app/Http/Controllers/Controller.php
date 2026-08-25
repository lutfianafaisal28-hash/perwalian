<?php

namespace App\Http\Controllers;

// ============================================================
// CONTROLLER DASAR (Base Controller)
// ============================================================
// abstract = kelas ini TIDAK bisa dipakai langsung, hanya bisa
// diwariskan (extends) oleh controller lain.
//
// Semua controller di aplikasi ini (AuthController, Admin\*,
// Dosen\*, Mahasiswa\*) extends Controller ini. Saat ini kelas ini
// masih kosong, tapi jika nanti ada helper/fitur yang dipakai
// SEMUA controller, cukup taruh di sini dan otomatis diwariskan.
// ============================================================
abstract class Controller
{
    //
}
