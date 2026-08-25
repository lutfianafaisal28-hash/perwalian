<?php

namespace Tests;

use Illuminate\Contracts\Console\Kernel;
use Illuminate\Foundation\Testing\TestCase as BaseTestCase;

// ============================================================
// TESTCASE (Kelas Dasar untuk Semua Test)
// ============================================================
// Setiap file test (Unit/Feature) mewarisi kelas ini.
// Method createApplication dipanggil PHPUnit secara otomatis
// untuk menyiapkan (bootstrap) aplikasi Laravel sebelum test
// dijalankan, jadi di dalam test kita bisa memakai fitur Laravel
// seperti route(), Auth, database, dsb.
//
// Catatan: karena app berada di folder 'backend', path bootstrap
// ditulis manual: backend/bootstrap/app.php (default Laravel
// mengasumsikan lokasi di root).
// ============================================================
abstract class TestCase extends BaseTestCase
{
    public function createApplication()
    {
        $app = require dirname(__DIR__, 2).'/backend/bootstrap/app.php';

        $app->make(Kernel::class)->bootstrap();

        return $app;
    }
}
