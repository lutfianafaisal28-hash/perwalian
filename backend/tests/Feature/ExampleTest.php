<?php

namespace Tests\Feature;

use Tests\TestCase;

// ============================================================
// CONTOH TEST FITUR (Feature Test)
// ============================================================
// Feature test = test yang mensimulasikan permintaan HTTP
// (seperti membuka halaman / mengisi form) lalu memeriksa hasilnya.
//
// Dua test di bawah hanyalah contoh dasar bawaan Laravel yang
// menguji halaman login. Test sungguhan untuk fitur-fitur aplikasi
// ini bisa ditambahkan di folder ini.
// ============================================================
class ExampleTest extends TestCase
{
    // Test 1: tamu (belum login) yang membuka '/' harus dialihkan
    // ke halaman login.
    public function test_guest_is_redirected_to_login(): void
    {
        $response = $this->get('/');

        $response->assertRedirect(route('login'));
    }

    // Test 2: halaman login harus berhasil dimuat (status 200 = OK).
    public function test_login_page_returns_a_successful_response(): void
    {
        $response = $this->get(route('login'));

        $response->assertStatus(200);
    }
}
