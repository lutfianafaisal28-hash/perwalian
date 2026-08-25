<!DOCTYPE html>
{{-- ============================================================
     LAYOUT UTAMA (Kerangka Semua Halaman)
     ============================================================
     File ini adalah "template dasar" untuk semua halaman aplikasi
     (dashboard admin, dosen, mahasiswa, dll). Halaman lain hanya
     mengisi bagian "@section('content')" lewat @extends('layouts.app').
     Keuntungan: tampilan sidebar, topbar, dan CSS cukup ditulis
     sekali di sini, dipakai oleh semua halaman.
     ============================================================ --}}
<html lang="id" data-bs-theme="light">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    {{-- Token CSRF untuk keamanan form (dipakai oleh AJAX) --}}
    <meta name="csrf-token" content="{{ csrf_token() }}">
    {{-- @yield('title') = judul halaman; jika halaman tidak set, default "SI Perwalian Mahasiswa" --}}
    <title>@yield('title', 'SI Perwalian Mahasiswa')</title>

    {{-- Google Fonts: Inter (teks biasa) & Poppins (judul/heading) --}}
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=Poppins:wght@500;600;700&display=swap" rel="stylesheet">

    {{-- Library eksternal via CDN: Bootstrap (styling), Bootstrap Icons (ikon), dan CSS aplikasi sendiri --}}
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <link href="{{ asset('css/app.css') }}" rel="stylesheet">

    @yield('head')
    @stack('styles')
</head>
{{-- Simpan pesan flash (sukses/gagal) di atribut HTML agar bisa dibaca JavaScript --}}
<body data-flash-success="{{ session('success') }}" data-flash-error="{{ session('error') }}">
@php
    // Ambil data user yang sedang login
    $user = auth()->user();
    // Nama route aktif saat ini (misal "admin.dashboard") untuk deteksi menu aktif
    $route = request()->route() ? request()->route()->getName() : '';
    // Huruf pertama nama user (dipakai untuk avatar lingkaran, contoh: "Budi" -> "B")
    $initial = strtoupper(substr($user->name, 0, 1));
    // Label peran user dalam Bahasa Indonesia
    $roleLabel = $user->isAdmin() ? 'Administrator' : ($user->isMahasiswa() ? 'Mahasiswa' : 'Dosen');
@endphp

{{-- MENU SIDEBAR — beda per peran. Setiap array berisi label, ikon, dan nama route. --}}
@if ($user->isAdmin())
    @php
        $menus = [
            ['label' => 'Dashboard', 'icon' => 'bi-speedometer2', 'route' => 'admin.dashboard'],
            ['label' => 'Data Mahasiswa', 'icon' => 'bi-people', 'route' => 'admin.mahasiswa.index'],
            ['label' => 'Data Dosen', 'icon' => 'bi-person-badge', 'route' => 'admin.dosen.index'],
            ['label' => 'Penentuan Dosen Wali', 'icon' => 'bi-person-lines-fill', 'route' => 'admin.dosen-wali.index'],
            ['label' => 'Rekap Perwalian', 'icon' => 'bi-clipboard-data', 'route' => 'admin.rekap.index'],
        ];
    @endphp
@elseif ($user->isMahasiswa())
    @php
        $menus = [
            ['label' => 'Dashboard', 'icon' => 'bi-speedometer2', 'route' => 'mahasiswa.dashboard'],
            ['label' => 'Profil', 'icon' => 'bi-person', 'route' => 'mahasiswa.profil'],
            ['label' => 'Dosen Wali', 'icon' => 'bi-person-lines-fill', 'route' => 'mahasiswa.dosen-wali'],
            ['label' => 'Isi Perwalian', 'icon' => 'bi-journal-plus', 'route' => 'mahasiswa.perwalian.create'],
            ['label' => 'Riwayat Perwalian', 'icon' => 'bi-journal-text', 'route' => 'mahasiswa.perwalian.index'],
        ];
    @endphp
@elseif ($user->isDosen())
    @php
        $menus = [
            ['label' => 'Dashboard', 'icon' => 'bi-speedometer2', 'route' => 'dosen.dashboard'],
            ['label' => 'Mahasiswa Bimbingan', 'icon' => 'bi-people', 'route' => 'dosen.bimbingan.index'],
        ];
    @endphp
@endif

<div class="app-shell">
    {{-- ===== SIDEBAR KIRI (logo + menu + info user) ===== --}}
    <aside class="app-sidebar" id="appSidebar">
        <div class="sidebar-brand">
            <img src="{{ asset('images/logo-stmik.svg') }}" alt="Logo STMIK" style="width:42px;height:42px;border-radius:12px;flex-shrink:0;">
            <div>
                <div class="brand-title">SI Perwalian</div>
                <div class="brand-subtitle">STMIK Bandung</div>
            </div>
        </div>

        <nav class="sidebar-nav">
            {{-- Looping menu: tampilkan semua item dari array $menus --}}
            @foreach ($menus as $menu)
                @php
                    // routeIs('admin.dashboard*') = benar jika route saat ini diawali "admin.dashboard" (termasuk sub-halaman)
                    $active = request()->routeIs($menu['route'].'*');
                @endphp
                <a href="{{ route($menu['route']) }}" class="nav-link {{ $active ? 'active' : '' }}">
                    <i class="bi {{ $menu['icon'] }}"></i>
                    <span>{{ $menu['label'] }}</span>
                </a>
            @endforeach
        </nav>

        <div class="sidebar-user">
            <div class="user-chip">
                <div class="user-avatar">{{ $initial }}</div>
                <div class="me-auto lh-sm" style="min-width:0;">
                    <div class="fw-semibold text-truncate" style="font-size:0.85rem;color:#fff;">{{ $user->name }}</div>
                    <small class="text-uppercase" style="color:rgba(255,255,255,0.55);font-size:0.66rem;letter-spacing:0.05em;">{{ $roleLabel }}</small>
                </div>
                <i class="bi bi-shield-check" style="color:var(--emerald);"></i>
            </div>
            {{-- Form logout (metode POST karena butuh token CSRF) --}}
            <form method="POST" action="{{ route('logout') }}" class="mt-2">
                @csrf
                <button type="submit" class="btn btn-sm w-100" style="background:rgba(255,255,255,0.08);color:#fff;border:1px solid rgba(255,255,255,0.14);">
                    <i class="bi bi-box-arrow-right me-1"></i> Keluar
                </button>
            </form>
        </div>
    </aside>

    {{-- Backdrop gelap di belakang sidebar (muncul hanya di layar kecil / mobile) --}}
    <div class="sidebar-backdrop" id="sidebarBackdrop"></div>

    <div class="app-main">
        {{-- ===== TOPBAR ATAS (judul halaman + tombol tema + menu user) ===== --}}
        <header class="app-topbar">
            <div class="topbar-left">
                {{-- Tombol hamburger, hanya terlihat di layar kecil (d-lg-none) untuk membuka sidebar --}}
                <button class="icon-btn d-lg-none" type="button" id="sidebarToggle" aria-label="Menu">
                    <i class="bi bi-list"></i>
                </button>
                <div>
                    {{-- Judul halaman diisi lewat @section('heading') dari halaman masing-masing --}}
                    <h5 class="mb-0 fw-bold page-heading">@yield('heading', 'Dashboard')</h5>
                    <div class="topbar-date mt-1">
                        <i class="bi bi-calendar3"></i>
                        {{-- Tanggal hari ini dalam Bahasa Indonesia (contoh: Jumat, 14 Agustus 2026) --}}
                        <span>{{ now()->translatedFormat('l, d F Y') }}</span>
                    </div>
                </div>
            </div>

            <div class="topbar-right">
                {{-- Tombol mode gelap/terang. Ikon diganti lewat JavaScript --}}
                <button class="icon-btn" type="button" id="themeToggle" aria-label="Mode Gelap/Terang" title="Mode Gelap/Terang">
                    <i class="bi bi-moon-stars" id="themeIcon"></i>
                </button>

                {{-- Dropdown profil user di pojok kanan atas --}}
                <div class="dropdown">
                    <button class="topbar-user-btn" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                        <div class="user-avatar" style="width:34px;height:34px;font-size:0.85rem;">{{ $initial }}</div>
                        <span class="d-none d-md-inline fw-semibold" style="font-size:0.88rem;">{{ $user->name }}</span>
                        <i class="bi bi-chevron-down chev"></i>
                    </button>
                    <ul class="dropdown-menu dropdown-menu-end" style="min-width:200px;">
                        <li>
                            <div class="dropdown-header px-3 py-2">
                                {{ $roleLabel }}<br>
                                <span class="fw-normal" style="text-transform:none;letter-spacing:0;">{{ $user->username }}</span>
                            </div>
                        </li>
                        {{-- Menu "Profil Saya" hanya untuk mahasiswa --}}
                        @if ($user->isMahasiswa())
                            <li><a class="dropdown-item" href="{{ route('mahasiswa.profil') }}"><i class="bi bi-person me-2"></i> Profil Saya</a></li>
                        @endif
                        <li><hr class="dropdown-divider"></li>
                        <li>
                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <button type="submit" class="dropdown-item text-danger"><i class="bi bi-box-arrow-right me-2"></i> Keluar</button>
                            </form>
                        </li>
                    </ul>
                </div>
            </div>
        </header>

        <main class="app-content">
            {{-- Tampilkan semua error validasi form (dari redirect()->back()->withErrors()) --}}
            @if ($errors->any())
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <div class="d-flex gap-2">
                        <i class="bi bi-exclamation-triangle-fill mt-1"></i>
                        <div>
                            <strong>Terjadi kesalahan:</strong>
                            <ul class="mb-0 mt-1">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    </div>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            {{-- ISI UTAMA HALAMAN — diisi oleh setiap halaman lewat @section('content') --}}
            @yield('content')

            <div class="footer-bar text-center">
                © {{ date('Y') }} Sistem Pencatatan Perwalian — STMIK Bandung
            </div>
        </main>
    </div>
</div>

{{-- Library JavaScript eksternal --}}
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.3/dist/chart.umd.min.js"></script>
<script>
    (function () {
        // ===== FITUR MODE GELAP/TERANG =====
        // Baca tema yang tersimpan di localStorage browser; jika belum ada, default 'light'
        const themeToggle = document.getElementById('themeToggle');
        const themeIcon = document.getElementById('themeIcon');
        const savedTheme = localStorage.getItem('perwalian-theme') || 'light';
        document.documentElement.setAttribute('data-bs-theme', savedTheme);
        themeIcon.className = 'bi ' + (savedTheme === 'dark' ? 'bi-sun' : 'bi-moon-stars');

        // Saat tombol tema diklik: balik ke mode sebaliknya, simpan, dan ganti ikon
        themeToggle.addEventListener('click', function () {
            const next = document.documentElement.getAttribute('data-bs-theme') === 'dark' ? 'light' : 'dark';
            document.documentElement.setAttribute('data-bs-theme', next);
            localStorage.setItem('perwalian-theme', next);
            themeIcon.className = 'bi ' + (next === 'dark' ? 'bi-sun' : 'bi-moon-stars');
        });

        // ===== SIDEBAR (KHUSUS MOBILE) =====
        // Di layar kecil, sidebar tersembunyi dan muncul saat tombol hamburger diklik
        const sidebar = document.getElementById('appSidebar');
        const backdrop = document.getElementById('sidebarBackdrop');
        const toggle = document.getElementById('sidebarToggle');

        function closeSidebar() { document.body.classList.remove('sidebar-open'); }
        toggle.addEventListener('click', () => document.body.classList.toggle('sidebar-open'));
        backdrop.addEventListener('click', closeSidebar);
        sidebar.querySelectorAll('.nav-link').forEach(link => link.addEventListener('click', closeSidebar));

        // ===== NOTIFIKASI TOAST (PESAN SUKSES / GAGAL) =====
        // Baca pesan dari atribut data-flash-* di <body>, lalu tampilkan popup kecil (toast)
        const flashSuccess = document.body.getAttribute('data-flash-success');
        const flashError = document.body.getAttribute('data-flash-error');
        const Toast = Swal.mixin({ toast: true, position: 'top-end', showConfirmButton: false, timer: 3500, timerProgressBar: true });
        if (flashSuccess) Toast.fire({ icon: 'success', title: flashSuccess });
        if (flashError) Toast.fire({ icon: 'error', title: flashError });

        // ===== TAMPILAN TABEL RESPONSIF DI MOBILE =====
        // Di layar kecil, tabel disulap jadi kartu: tiap baris jadi kartu,
        // dan label kolom diambil dari <th> lalu disimpan di atribut data-label tiap <td>.
        document.querySelectorAll('.table-responsive table').forEach(function (table) {
            const headers = Array.prototype.map.call(table.querySelectorAll('thead th'), function (th) {
                return th.textContent.trim();
            });
            table.querySelectorAll('tbody tr').forEach(function (tr) {
                const cells = tr.querySelectorAll('td');
                if (!cells.length) return;
                if (cells[0].colSpan > 1) return;
                cells.forEach(function (td, i) {
                    if (headers[i]) td.setAttribute('data-label', headers[i]);
                });
            });
        });

        // ===== KONFIRMASI HAPUS DENGAN SWEETALERT =====
        // Semua tombol hapus diberi atribut data-confirm="pesan...". Saat diklik,
        // tampilkan dialog konfirmasi; jika "Ya" maka form di sekitarnya baru di-submit.
        document.addEventListener('click', function (e) {
            const trigger = e.target.closest('[data-confirm]');
            if (!trigger) return;
            e.preventDefault();
            const form = trigger.closest('form');
            Swal.fire({
                title: 'Yakin ingin menghapus?',
                text: trigger.getAttribute('data-confirm'),
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#ef4444',
                cancelButtonColor: '#64748b',
                confirmButtonText: 'Ya, hapus',
                cancelButtonText: 'Batal',
            }).then((result) => {
                if (result.isConfirmed) form.submit();
            });
        });
    })();
</script>
@stack('scripts')
</body>
</html>
