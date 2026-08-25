# Manual Book — SI Perwalian Mahasiswa STMIK Bandung

Sistem Pencatatan Perwalian Mahasiswa berbasis **Laravel 11 + PostgreSQL + PHP 8.4** yang berjalan di **Laragon 6**.

---

## 1. Ringkasan Aplikasi

Aplikasi mengelola proses **perwalian mahasiswa dengan dosen wali** dengan 3 pengguna:

| Role | Tugas |
|------|-------|
| **Admin** | Mengelola data mahasiswa, data dosen, menentukan dosen wali, melihat rekap seluruh perwalian. |
| **Mahasiswa** | Melihat dosen wali otomatis, mengisi catatan perwalian (tanggal, semester, hasil diskusi, kendala, rencana perbaikan). |
| **Dosen** | Melihat daftar mahasiswa bimbingannya dan histori perwalian masing-masing mahasiswa. |

---

## 2. Kebutuhan Sistem

- Laragon 6 (sudah termasuk)
- PHP 8.4 (`C:\laragon\bin\php\php-8.4.24-Win32-vs17-x64`)
- PostgreSQL 16 (`C:\laragon\bin\postgresql\pgsql`) — sudah terpasang portable
- Composer (untuk install ulang dependency)

---

## 3. Menjalankan Aplikasi

### Cara 1 — Pakai start.bat (paling mudah)

1. Buka folder proyek: `C:\laragon\www\perwalian`
2. Klik dua kali **`start.bat`**
3. PostgreSQL otomatis dijalankan, lalu browser terbuka di **http://127.0.0.1:8000**
4. Untuk menghentikan: tekan `Ctrl+C` pada jendela start.bat

### Cara 2 — Manual

```
# 1. Jalankan PostgreSQL (jika belum)
C:\laragon\bin\postgresql\pgsql\bin\pg_ctl.exe -D C:\laragon\data\postgresql start

# 2. Jalankan Laravel
cd C:\laragon\www\perwalian
C:\laragon\bin\php\php-8.4.24-Win32-vs17-x64\php.exe artisan serve --port=8000
```

### Cara 3 — Melalui Laragon GUI

1. Buka **Laragon**, klik **Start All** (Apache + PostgreSQL dijalankan otomatis)
2. Akses aplikasi di **http://perwalian.test** (vhost otomatis Laragon, docroot diarahkan ke `public/`).
   - Catatan: URL `http://localhost/perwalian` (via Alias) TIDAK berfungsi untuk Laravel karena router
     tidak bisa men-strip prefix `/perwalian`; gunakan vhost `perwalian.test` atau jalankan `start.bat`.
   - Jika `perwalian.test` tidak terbuka, pastikan Laragon sudah menambahkan `perwalian.test` di file
     `C:\Windows\System32\drivers\etc\hosts` (dilakukan otomatis saat Start All, butuh izin admin).
3. Untuk melihat database: jalankan **`pgadmin.bat`** (pgAdmin 4 portable)
   - Host: `127.0.0.1` | Port: `5432` | User: `postgres` | Password: `postgres` | Database: `perwalian`

---

## 4. Akun Login (Data Demo)

Semua password default: **`123456`**

| Role | Username | Password |
|------|----------|----------|
| Admin | `admin` | 123456 |
| Mahasiswa | `20221001` (Andi Pratama) | 123456 |
| Mahasiswa | `20221002` (Budi Hartono) | 123456 |
| Mahasiswa | `20231001` (Gilang Ramadhan) | 123456 |
| Mahasiswa | `20241001` (Laila Nurjanah) | 123456 |
| Dosen | `0416078001` (Dr. Ahmad Fauzi) | 123456 |
| Dosen | `0425028102` (Dr. Siti Rahayu) | 123456 |
| Dosen | `0407088403` (Budi Santoso) | 123456 |
| Dosen | `0427128804` (Rina Wulandari) | 123456 |

> Mahasiswa login menggunakan **NPM**, dosen login menggunakan **NIDN**. Akun baru yang dibuat admin otomatis memakai password default `123456` (dapat diganti dari menu Profil).

---

## 5. Fitur per Role

### A. Admin
- **Dashboard** — statistik jumlah mahasiswa, dosen, catatan perwalian, mahasiswa tanpa wali, rekap per bulan.
- **Data Mahasiswa** — tambah, lihat, edit, hapus, dan cari data mahasiswa.
- **Data Dosen** — tambah, edit, hapus, dan cari data dosen.
- **Dosen Wali** — menetapkan dosen wali untuk setiap mahasiswa (satu mahasiswa = satu wali).
- **Rekap Perwalian** — melihat seluruh catatan perwalian dengan filter (cari, angkatan, dosen wali, rentang tanggal).

### B. Mahasiswa
- **Dashboard** — ringkasan data diri dan perwalian terakhir.
- **Profil** — melihat data diri dan mengganti password.
- **Dosen Wali** — melihat dosen wali yang telah ditetapkan admin.
- **Isi Perwalian** — mengisi tanggal, semester, hasil diskusi, kendala, dan rencana perbaikan.
- **Riwayat Perwalian** — melihat seluruh catatan perwalian yang pernah diisi.

### C. Dosen
- **Dashboard** — jumlah mahasiswa bimbingan dan total catatan.
- **Mahasiswa Bimbingan** — daftar mahasiswa bimbingannya beserta jumlah perwalian.
- **Histori Perwalian** — melihat catatan perwalian detail dari setiap mahasiswa bimbingannya.

---

## 6. Struktur Database

Database: **`perwalian`** — PostgreSQL

| Tabel | Kolom Penting |
|-------|---------------|
| `users` | id, name, email, username, role (admin/mahasiswa/dosen), password |
| `mahasiswa` | id, user_id, npm, nama, prodi, angkatan |
| `dosen` | id, user_id, nidn, nama |
| `dosen_wali` | id, mahasiswa_id (unik), dosen_id |
| `perwalian` | id, mahasiswa_id, tanggal, semester, hasil_perwalian, kendala, rencana_perbaikan |

Relasi:
```
Mahasiswa 1---1 DosenWali N---1 Dosen
Mahasiswa 1---N Perwalian
```

Backup database tersedia di: `backend\database\backup\perwalian.sql`

---

## 7. Struktur Folder

```
C:\laragon\www\perwalian\
│
├── backend\                      # BACKEND (logika server, PHP)
│   ├── app\Http\Controllers\     # Controller per role (Admin, Mahasiswa, Dosen, Auth)
│   ├── app\Models\               # User, Mahasiswa, Dosen, DosenWali, Perwalian
│   ├── app\Http\Middleware\      # CheckRole (pembatas akses per role)
│   ├── routes\web.php            # Semua rute aplikasi
│   ├── database\migrations\      # Skema tabel
│   ├── database\seeders\         # Data demo lengkap
│   ├── database\backup\perwalian.sql
│   ├── config\                   # Konfigurasi aplikasi
│   ├── lang\id\                  # Pesan validasi Bahasa Indonesia
│   ├── bootstrap\                # Boot aplikasi + path override (backend/, frontend/)
│   └── storage\                  # Log, cache, session
│
├── frontend\                     # FRONTEND (tampilan)
│   └── resources\
│       ├── views\                # Semua Blade templates (Bootstrap 5)
│       ├── css\                  # Gaya (Tailwind, tidak aktif dipakai)
│       └── js\                   # Script (tidak aktif dipakai)
│
├── public\                       # Titik masuk web (docroot Apache) → index.php
├── vendor\                       # Library (Composer)
├── .env                          # Konfigurasi lingkungan (koneksi PostgreSQL)
├── artisan                       # CLI Laravel
├── start.bat / stop.bat          # Script menjalankan & menghentikan
└── pgadmin.bat                   # Launcher pgAdmin 4
```

---

## 8. Membuat Ulang Database Dari Nol

```
cd C:\laragon\www\perwalian
C:\laragon\bin\php\php-8.4.24-Win32-vs17-x64\php.exe artisan migrate:fresh --seed
```

Perintah di atas menghapus semua tabel lalu membuat ulang beserta data demo.

Import backup manual (jika diperlukan):
```
C:\laragon\bin\postgresql\pgsql\bin\psql.exe -h 127.0.0.1 -U postgres -d perwalian -f backend\database\backup\perwalian.sql
```

---

## 9. Pemecahan Masalah

| Masalah | Solusi |
|---------|--------|
| Browser tidak bisa membuka aplikasi | Pastikan jendela start.bat masih terbuka; tekan `Ctrl+C`, lalu jalankan ulang. |
| `could not connect to server (5432)` | Jalankan `C:\laragon\bin\postgresql\pgsql\bin\pg_ctl.exe -D C:\laragon\data\postgresql start` |
| Halaman error 500 | Jalankan `php artisan optimize:clear` |
| Login gagal | Gunakan username sesuai role (admin / NPM / NIDN) dengan password `123456`. |
