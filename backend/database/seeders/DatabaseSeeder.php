<?php

namespace Database\Seeders;

use App\Models\Dosen;
use App\Models\DosenWali;
use App\Models\Mahasiswa;
use App\Models\Perwalian;
use App\Models\User;
use Illuminate\Database\Seeder;

// ============================================================
// DATABASESEEDER (Pengisi Data Contoh)
// ============================================================
// Seeder = file untuk mengisi database dengan data awal / contoh.
// Dijalankan dengan perintah: php artisan db:seed
//
// Data yang dibuat di sini:
//  1. 1 akun admin            (username: admin, password: 123456)
//  2. 4 dosen                 (username = NIDN,  password: 123456)
//  3. 12 mahasiswa            (username = NPM,   password: 123456)
//  4. Penetapan dosen wali untuk tiap mahasiswa
//  5. Catatan perwalian contoh untuk tiap mahasiswa
// ============================================================
class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        if (User::count() > 0) {
            return;
        }
        // ===== 1. AKUN ADMIN =====
        // User::factory()->create() = buat 1 baris user dengan data
        // tertentu (mengisi kolom yang tidak disebut otomatis/random).
        User::factory()->create([
            'name' => 'Administrator',
            'username' => 'admin',
            'email' => 'admin@stmikbandung.ac.id',
            'role' => User::ROLE_ADMIN,
            'password' => '123456',
        ]);

        // ===== 2. DATA DOSEN =====
        // Array sederhana berisi NIDN + nama dosen contoh.
        $dosenData = [
            ['nidn' => '0416078001', 'nama' => 'Dr. Ahmad Fauzi, M.Kom'],
            ['nidn' => '0425028102', 'nama' => 'Dr. Siti Rahayu, M.TI'],
            ['nidn' => '0407088403', 'nama' => 'Budi Santoso, M.Kom'],
            ['nidn' => '0427128804', 'nama' => 'Rina Wulandari, S.Kom., M.Cs'],
        ];

        // Koleksi kosong untuk menyimpan daftar dosen yang dibuat
        $dosenList = collect();

        foreach ($dosenData as $d) {
            // Setiap dosen butuh akun login. username = NIDN.
            // Email dibuat otomatis dari NIDN (huruf kecil, tanpa simbol).
            $user = User::factory()->create([
                'name' => $d['nama'],
                'username' => $d['nidn'],
                'email' => strtolower(str_replace(['.', ' ', ','], '', $d['nidn'])).'@dosen.stmikbandung.ac.id',
                'role' => User::ROLE_DOSEN,
                'password' => '123456',
            ]);

            // Simpan data dosen yang menautkan ke akun user di atas.
            // push() = tambahkan ke dalam koleksi $dosenList.
            $dosenList->push(Dosen::create([
                'user_id' => $user->id,
                'nidn' => $d['nidn'],
                'nama' => $d['nama'],
            ]));
        }

        // ===== 3. DATA MAHASISWA =====
        $mahasiswaData = [
            ['npm' => '20221001', 'nama' => 'Andi Pratama', 'prodi' => 'Teknik Informatika', 'angkatan' => '2022'],
            ['npm' => '20221002', 'nama' => 'Budi Hartono', 'prodi' => 'Sistem Informasi', 'angkatan' => '2022'],
            ['npm' => '20221003', 'nama' => 'Citra Lestari', 'prodi' => 'Manajemen Informatika', 'angkatan' => '2022'],
            ['npm' => '20221004', 'nama' => 'Dewi Anggraini', 'prodi' => 'Teknik Komputer', 'angkatan' => '2022'],
            ['npm' => '20221005', 'nama' => 'Eko Saputra', 'prodi' => 'Teknik Informatika', 'angkatan' => '2022'],
            ['npm' => '20221006', 'nama' => 'Fitri Handayani', 'prodi' => 'Sistem Informasi', 'angkatan' => '2022'],
            ['npm' => '20231001', 'nama' => 'Gilang Ramadhan', 'prodi' => 'Teknik Informatika', 'angkatan' => '2023'],
            ['npm' => '20231002', 'nama' => 'Hesti Puspita', 'prodi' => 'Manajemen Informatika', 'angkatan' => '2023'],
            ['npm' => '20231003', 'nama' => 'Indra Wijaya', 'prodi' => 'Teknik Komputer', 'angkatan' => '2023'],
            ['npm' => '20231004', 'nama' => 'Joko Susilo', 'prodi' => 'Sistem Informasi', 'angkatan' => '2023'],
            ['npm' => '20241001', 'nama' => 'Laila Nurjanah', 'prodi' => 'Teknik Informatika', 'angkatan' => '2024'],
            ['npm' => '20241002', 'nama' => 'Muhammad Rizki', 'prodi' => 'Sistem Informasi', 'angkatan' => '2024'],
        ];

        $mahasiswaList = collect();

        foreach ($mahasiswaData as $index => $m) {
            // Sama seperti dosen: buat akun login dulu (username = NPM)
            $user = User::factory()->create([
                'name' => $m['nama'],
                'username' => $m['npm'],
                'email' => $m['npm'].'@student.stmikbandung.ac.id',
                'role' => User::ROLE_MAHASISWA,
                'password' => '123456',
            ]);

            $mahasiswaList->push(Mahasiswa::create([
                'user_id' => $user->id,
                'npm' => $m['npm'],
                'nama' => $m['nama'],
                'prodi' => $m['prodi'],
                'angkatan' => $m['angkatan'],
            ]));
        }

        // ===== 4. PENETAPAN DOSEN WALI =====
        // Setiap mahasiswa diberi dosen wali secara bergantian
        // ($index % jumlah dosen = giliran melingkar 0,1,2,3,0,1,2,...).
        foreach ($mahasiswaList as $index => $mahasiswa) {
            $dosen = $dosenList->get($index % $dosenList->count());

            DosenWali::create([
                'mahasiswa_id' => $mahasiswa->id,
                'dosen_id' => $dosen->id,
            ]);
        }

        // ===== 5. CATATAN PERWALIAN CONTOH =====
        // Beberapa template isi diskusi, kendala, dan rencana perbaikan.
        $isiPerwalian = [
            'Membahas perkembangan studi semester ini. IPK tetap stabil dan seluruh mata kuliah dinyatakan lulus tanpa nilai mengulang.',
            'Membahas rencana pengambilan mata kuliah semester depan. Disarankan mengambil mata kuliah pilihan sesuai minat bidang.',
            'Mendiskusikan kesulitan pada mata kuliah pemrograman. Dosen wali memberikan saran untuk mengikuti kelas tambahan dan berlatih mandiri.',
            'Mengevaluasi hasil studi dan kendala selama satu semester berjalan. Rencana perbaikan disusun bersama dosen wali.',
            'Membahas kesiapan menghadapi ujian akhir semester serta strategi belajar yang lebih efektif.',
            'Diskusi mengenai pemilihan topik tugas akhir yang sesuai dengan minat dan kemampuan akademik.',
        ];

        $kendalaData = [
            'Kesulitan memahami materi algoritma dan struktur data.',
            'Jadwal kuliah bentrok dengan kegiatan organisasi.',
            'Kurang fokus saat pembelajaran daring.',
            'Kesulitan membagi waktu antara kuliah dan pekerjaan paruh waktu.',
            null, // mahasiswa ke-5 tidak punya kendala (contoh null)
            'Belum menentukan topik yang tepat untuk tugas akhir.',
        ];

        $rencanaData = [
            'Mengikuti les tambahan dan memperbanyak latihan soal setiap minggu.',
            'Membuat jadwal belajar yang lebih teratur dan menyesuaikan kegiatan organisasi.',
            'Mencari tempat belajar yang kondusif dan membatasi distraksi.',
            'Menyusun prioritas dan mengatur jadwal agar kuliah tetap menjadi prioritas utama.',
            'Meningkatkan intensitas belajar dan mengikuti kelompok belajar.',
            'Melakukan riset pustaka dan konsultasi rutin dengan dosen pembimbing.',
        ];

        // Jumlah catatan perwalian untuk tiap mahasiswa (sesuai urutan array)
        $banyakPerwalian = [1, 2, 1, 3, 2, 1, 2, 1, 1, 2, 1, 2];

        foreach ($mahasiswaList as $index => $mahasiswa) {
            $angkatan = (int) $mahasiswa->angkatan;
            $banyak = $banyakPerwalian[$index];

            // Penanda bulan: mahasiswa angkatan 2022 diasumsikan sudah
            // 18 bulan kuliah, angkatan 2023 -> 9 bulan, 2024 -> 3 bulan.
            $bulanTerakhir = $angkatan === 2022 ? 18 : ($angkatan === 2023 ? 9 : 3);

            // Buat $banyak catatan perwalian per mahasiswa.
            // Tanggal dibuat menyebar (beberapa bulan ke belakang),
            // lalu semester dihitung mundur dari semester saat ini.
            for ($i = 0; $i < $banyak; $i++) {
                $tanggal = now()->subMonths($bulanTerakhir)->addMonths($i * 5)->setDay(min(5 + $i * 8, 27));

                // Jaga-jaga: jika tanggal hasil hitungan masih di masa depan,
                // ganti menjadi beberapa hari yang lalu.
                if ($tanggal->isFuture()) {
                    $tanggal = now()->subDays(($banyak - $i) * 9);
                }

                $semesterAwal = $angkatan === 2022 ? 8 : ($angkatan === 2023 ? 6 : 4);
                $semester = max(1, $semesterAwal - ($banyak - 1 - $i));

                Perwalian::create([
                    'mahasiswa_id' => $mahasiswa->id,
                    'tanggal' => $tanggal,
                    'semester' => (string) $semester,
                    'hasil_perwalian' => $isiPerwalian[$i % count($isiPerwalian)],
                    'kendala' => $kendalaData[$i % count($kendalaData)],
                    'rencana_perbaikan' => $rencanaData[$i % count($rencanaData)],
                ]);
            }
        }
    }
}
