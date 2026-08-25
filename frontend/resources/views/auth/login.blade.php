<!DOCTYPE html>
{{-- ============================================================
     HALAMAN LOGIN
     ============================================================
     Halaman ini TIDAK memakai @extends('layouts.app') karena halaman
     login berdiri sendiri (belum ada user yang login, jadi sidebar
     belum perlu tampil). Semua CSS halaman ditulis inline di <style>.
     ============================================================ --}}
<html lang="id" data-bs-theme="light">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login — SI Perwalian Mahasiswa STMIK Bandung</title>

    {{-- Google Fonts: Inter (teks) & Poppins (judul) --}}
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=Poppins:wght@500;600;700;800&display=swap" rel="stylesheet">

    {{-- Bootstrap (styling) + Bootstrap Icons (ikon) + CSS aplikasi --}}
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <link href="{{ asset('css/app.css') }}" rel="stylesheet">

    <style>
        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }

        {{-- MEMATIKAN LINGKARAN: semua pseudo-element di halaman login
             di-nonaktifkan agar tidak ada dekorasi lingkaran yang muncul
             di atas foto gedung (::before / ::after biasanya dipakai
             CSS untuk membuat lingkaran dekoratif). --}}
        .login-page::before,
        .login-page::after,
        .login-card::before,
        .login-card::after,
        .login-right::before,
        .login-right::after,
        .login-left::before,
        .login-left::after {
            content: none !important;
            display: none !important;
            background: none !important;
            width: 0 !important;
            height: 0 !important;
        }

        {{-- BACKGROUND HALAMAN: gabungan dua lapis background dalam satu properti:
             1) gradient gelap semi-transparan (agar kartu login mudah dibaca)
             2) foto gedung STMIK Bandung yang menutupi seluruh layar --}}
        body.login-page {
            min-height: 100vh;
            background:
                linear-gradient(160deg, rgba(0,0,0,0.30) 0%, rgba(0,0,0,0.15) 40%, rgba(0,0,0,0.10) 70%, rgba(0,0,0,0.20) 100%),
                url('{{ asset("images/building-photo.jpg") }}') center center / cover no-repeat fixed;
            display: flex;
            align-items: center;
            justify-content: center;
            position: relative;
            font-family: 'Inter', sans-serif;
        }

        {{-- Pembungkus kartu login (pembatas lebar maksimal) --}}
        .login-wrapper {
            position: relative;
            z-index: 1;
            width: 100%;
            max-width: 880px;
            padding: 1.5rem;
        }

        {{-- KARTU UTAMA: wadah putih yang berisi 2 panel (form kiri + branding kanan) --}}
        .login-card {
            display: flex;
            border-radius: 20px;
            overflow: hidden;
            box-shadow: 0 25px 60px rgba(0, 0, 0, 0.35), 0 0 0 1px rgba(255,255,255,0.06);
            animation: cardFadeIn 0.6s ease both;
            min-height: 480px;
        }

        {{-- Animasi munculnya kartu dari bawah --}}
        @keyframes cardFadeIn {
            from { opacity: 0; transform: translateY(24px) scale(0.98); }
            to { opacity: 1; transform: translateY(0) scale(1); }
        }

        /* ===================== PANEL KIRI (Form Login) ===================== */
        .login-left {
            flex: 1;
            background: #fff;
            padding: 2.5rem 2.5rem;
            display: flex;
            flex-direction: column;
        }

        {{-- Baris logo + nama sistem di atas form --}}
        .brand-row {
            display: flex;
            align-items: center;
            gap: 0.75rem;
            margin-bottom: 2.2rem;
        }

        .brand-row img {
            height: 50px;
            width: auto;
        }

        .brand-text h5 {
            font-family: 'Poppins', sans-serif;
            font-weight: 700;
            font-size: 0.95rem;
            margin: 0;
            line-height: 1.2;
            color: #1e293b;
        }

        .brand-text small {
            color: #94a3b8;
            font-size: 0.72rem;
        }

        {{-- Teks sapaan "Selamat Datang" --}}
        .greeting {
            font-family: 'Poppins', sans-serif;
            font-weight: 700;
            font-size: 1.5rem;
            color: #0f172a;
            margin-bottom: 0.3rem;
        }

        .greeting-sub {
            color: #94a3b8;
            font-size: 0.82rem;
            margin-bottom: 1.75rem;
        }

        {{-- Label kecil di atas setiap input --}}
        .field-label {
            font-weight: 600;
            font-size: 0.78rem;
            color: #475569;
            margin-bottom: 0.35rem;
            display: block;
        }

        {{-- KOTAK INPUT BERSIH: wadah flex yang berisi ikon + input, tanpa border default --}}
        .input-clean {
            display: flex;
            align-items: center;
            background: #f8fafc;
            border: 1.5px solid #e2e8f0;
            border-radius: 10px;
            padding: 0 0.85rem;
            height: 46px;
            transition: border-color 0.2s, box-shadow 0.2s, background 0.2s;
            margin-bottom: 1rem;
        }

        {{-- Saat input di dalamnya fokus (diklik), wadah diberi border navy --}}
        .input-clean:focus-within {
            border-color: #1e3a8a;
            box-shadow: 0 0 0 3px rgba(30, 58, 138, 0.1);
            background: #fff;
        }

        .input-clean i {
            color: #94a3b8;
            font-size: 0.95rem;
            margin-right: 0.65rem;
            flex-shrink: 0;
        }

        {{-- Input itu sendiri tanpa border agar menyatu dengan wadah --}}
        .input-clean input {
            flex: 1;
            border: none;
            outline: none;
            background: transparent;
            font-size: 0.88rem;
            color: #1e293b;
            padding: 0;
            height: 100%;
        }

        .input-clean input::placeholder {
            color: #cbd5e1;
        }

        {{-- Tombol mata (show/hide password) di dalam input password --}}
        .input-clean .btn-eye {
            background: none;
            border: none;
            color: #94a3b8;
            cursor: pointer;
            padding: 0.25rem;
            font-size: 1rem;
            transition: color 0.2s;
            flex-shrink: 0;
        }

        .input-clean .btn-eye:hover {
            color: #64748b;
        }

        {{-- Baris "Ingat saya" --}}
        .check-row {
            display: flex;
            align-items: center;
            gap: 0.5rem;
            margin-bottom: 1.4rem;
        }

        .check-row input[type="checkbox"] {
            width: 16px;
            height: 16px;
            accent-color: #1e3a8a;
            cursor: pointer;
        }

        .check-row label {
            font-size: 0.8rem;
            color: #64748b;
            cursor: pointer;
            user-select: none;
        }

        {{-- Tombol MASUK (lebar penuh) --}}
        .btn-masuk {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 0.5rem;
            width: 100%;
            height: 48px;
            background: linear-gradient(135deg, #1e3a8a 0%, #2347a0 100%);
            color: #fff;
            border: none;
            border-radius: 10px;
            font-weight: 700;
            font-size: 0.92rem;
            cursor: pointer;
            transition: background 0.2s, transform 0.15s, box-shadow 0.2s;
        }

        .btn-masuk:hover {
            background: linear-gradient(135deg, #172554 0%, #1e3a8a 100%);
            transform: translateY(-1px);
            box-shadow: 0 8px 24px rgba(30, 58, 138, 0.35);
        }

        .btn-masuk:active {
            transform: translateY(0);
        }

        {{-- Footer kecil di bagian bawah panel kiri --}}
        .form-footer {
            margin-top: auto;
            padding-top: 1.5rem;
            text-align: center;
        }

        .form-footer small {
            color: #94a3b8;
            font-size: 0.7rem;
        }

        /* ===================== PANEL KANAN (Branding) ===================== */
        .login-right {
            width: 340px;
            flex-shrink: 0;
            background: linear-gradient(165deg, #0f2566 0%, #1a3378 40%, #1e3a8a 70%, #234190 100%);
            color: #fff;
            padding: 2.5rem 2rem;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            position: relative;
            overflow: hidden;
        }

        {{-- Semua anak elemen panel kanan berada di atas dekorasi --}}
        .login-right > * { position: relative; z-index: 1; }

        {{-- Logo + nama sistem di panel kanan --}}
        .right-brand {
            display: flex;
            align-items: center;
            gap: 0.65rem;
            margin-bottom: 2.2rem;
        }

        {{-- filter brightness(0) invert(1) = logo diubah jadi putih --}}
        .right-brand img {
            height: 44px;
            width: auto;
            filter: brightness(0) invert(1);
        }

        .right-brand-text h6 {
            font-family: 'Poppins', sans-serif;
            font-weight: 700;
            font-size: 0.92rem;
            margin: 0;
            line-height: 1.2;
        }

        .right-brand-text small {
            color: rgba(255,255,255,0.55);
            font-size: 0.65rem;
        }

        {{-- Judul besar promosi di panel kanan --}}
        .right-title {
            font-family: 'Poppins', sans-serif;
            font-weight: 800;
            font-size: 1.4rem;
            line-height: 1.3;
            margin-bottom: 0.85rem;
        }

        .right-desc {
            color: rgba(255,255,255,0.65);
            font-size: 0.8rem;
            line-height: 1.6;
            margin-bottom: 1.5rem;
        }

        {{-- Tiap baris keunggulan fitur (ikon kotak + teks) --}}
        .feat {
            display: flex;
            align-items: center;
            gap: 0.7rem;
            padding: 0.5rem 0;
        }

        .feat-icon {
            width: 32px;
            height: 32px;
            border-radius: 9px;
            background: rgba(16, 185, 129, 0.18);
            color: #6ee7b7;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 0.85rem;
            flex-shrink: 0;
        }

        .feat span {
            font-size: 0.78rem;
            color: rgba(255,255,255,0.8);
            line-height: 1.35;
        }

        .right-footer small {
            color: rgba(255,255,255,0.35);
            font-size: 0.65rem;
        }

        /* ===================== RESPONSIVE (LAYAR KECIL) ===================== */
        {{-- Di layar kecil (<768px): panel kanan disembunyikan, kartu jadi satu kolom --}}
        @media (max-width: 767.98px) {
            .login-right { display: none; }
            .login-card { max-width: 440px; margin: 0 auto; }
            .login-left { padding: 2rem 1.75rem; }
        }

        {{-- Di layar sangat kecil (<576px): padding diperkecil lagi --}}
        @media (max-width: 575.98px) {
            .login-wrapper { padding: 1rem; }
            .login-left { padding: 1.75rem 1.25rem; }
            .login-card { border-radius: 16px; }
            .greeting { font-size: 1.25rem; }
        }
    </style>
</head>
<body class="login-page">
    <div class="login-wrapper">
        <div class="login-card">
            <!-- ===== PANEL KIRI: Form Login ===== -->
            <div class="login-left">
                {{-- Logo + nama aplikasi --}}
                <div class="brand-row">
                    <img src="{{ asset('images/logo-stmik.svg') }}" alt="Logo STMIK Bandung">
                    <div class="brand-text">
                        <h5>Sistem Pencatatan<br>Perwalian</h5>
                        <small>STMIK Bandung</small>
                    </div>
                </div>

                {{-- Teks sapaan --}}
                <div class="greeting">Selamat Datang</div>
                <p class="greeting-sub">Masuk menggunakan akun Anda untuk melanjutkan.</p>

                {{-- Jika ada error validasi dari server (misal username salah), tampilkan alert merah --}}
                @if ($errors->any())
                    <div class="alert alert-danger py-2 px-3 d-flex align-items-center gap-2 mb-3" role="alert" style="border-radius:10px;font-size:0.82rem;">
                        <i class="bi bi-exclamation-triangle-fill"></i>
                        <div>{{ $errors->first() }}</div>
                    </div>
                @endif

                {{-- FORM LOGIN: dikirim via POST ke route 'login.post'.
                     @csrf = token keamanan wajib untuk semua form POST di Laravel. --}}
                <form method="POST" action="{{ route('login.post') }}">
                    @csrf
                    {{-- Field username (NPM/NIDN/admin) --}}
                    <label class="field-label">Username</label>
                    <div class="input-clean">
                        <i class="bi bi-person"></i>
                        {{-- old('username') = mengembalikan isi yang pernah diketik saat gagal login --}}
                        <input type="text" name="username" value="{{ old('username') }}" placeholder="NPM / NIDN / admin" required autofocus>
                    </div>
                    @error('username')
                        <div class="text-danger small" style="margin-top:-0.6rem;margin-bottom:0.75rem;">{{ $message }}</div>
                    @enderror

                    {{-- Field password + tombol mata untuk show/hide --}}
                    <label class="field-label">Password</label>
                    <div class="input-clean">
                        <i class="bi bi-lock"></i>
                        <input type="password" name="password" id="passwordInput" placeholder="Masukkan password" required>
                        <button type="button" class="btn-eye" id="togglePassword" aria-label="Tampilkan password">
                            <i class="bi bi-eye"></i>
                        </button>
                    </div>
                    @error('password')
                        <div class="text-danger small" style="margin-top:-0.6rem;margin-bottom:0.75rem;">{{ $message }}</div>
                    @enderror

                    {{-- Checkbox "Ingat saya" (remember me) --}}
                    <div class="check-row">
                        <input type="checkbox" name="remember" id="remember" {{ old('remember') ? 'checked' : '' }}>
                        <label for="remember">Ingat saya</label>
                    </div>

                    {{-- Tombol submit --}}
                    <button type="submit" class="btn-masuk">
                        <i class="bi bi-box-arrow-in-right"></i> Masuk
                    </button>
                </form>

                {{-- Footer kecil --}}
                <div class="form-footer">
                    <small>&copy; {{ date('Y') }} Sistem Pencatatan Perwalian &mdash; STMIK Bandung</small>
                </div>
            </div>

            <!-- ===== PANEL KANAN: Branding & Keunggulan Fitur ===== -->
            <div class="login-right">
                <div>
                    {{-- Logo + nama sistem --}}
                    <div class="right-brand">
                        <img src="{{ asset('images/logo-stmik.svg') }}" alt="Logo STMIK Bandung">
                        <div class="right-brand-text">
                            <h6>SI Perwalian</h6>
                            <small>Sistem Informasi Perwalian Mahasiswa</small>
                        </div>
                    </div>

                    {{-- Kalimat promosi utama --}}
                    <div class="right-title">Kelola Perwalian<br>Jadi Lebih Mudah</div>
                    <p class="right-desc">Platform pencatatan perwalian untuk mahasiswa, dosen wali, dan admin STMIK Bandung dalam satu tempat.</p>

                    {{-- Daftar fitur unggulan (ikon + deskripsi singkat) --}}
                    <div class="feat">
                        <div class="feat-icon"><i class="bi bi-journal-check"></i></div>
                        <span>Pencatatan hasil perwalian secara digital</span>
                    </div>
                    <div class="feat">
                        <div class="feat-icon"><i class="bi bi-person-lines-fill"></i></div>
                        <span>Penentuan dosen wali per mahasiswa</span>
                    </div>
                    <div class="feat">
                        <div class="feat-icon"><i class="bi bi-clipboard-data"></i></div>
                        <span>Rekap &amp; laporan perwalian terpusat</span>
                    </div>
                    <div class="feat">
                        <div class="feat-icon"><i class="bi bi-shield-lock"></i></div>
                        <span>Akses aman sesuai peran pengguna</span>
                    </div>
                </div>

                <div class="right-footer">
                    <small>&copy; {{ date('Y') }} STMIK Bandung</small>
                </div>
            </div>
        </div>
    </div>

    {{-- JavaScript: menambahkan tombol show/hide password
         Saat tombol mata diklik, type input password diganti text (dan sebaliknya),
         lalu ikon mata juga diganti bi-eye / bi-eye-slash. --}}
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        document.getElementById('togglePassword').addEventListener('click', function () {
            const input = document.getElementById('passwordInput');
            const icon = this.querySelector('i');
            const show = input.type === 'password';
            input.type = show ? 'text' : 'password';
            icon.className = 'bi ' + (show ? 'bi-eye-slash' : 'bi-eye');
        });
    </script>
</body>
</html>
