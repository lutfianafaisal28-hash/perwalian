<?php

namespace App\Console\Commands;

use App\Models\Dosen;
use App\Models\DosenWali;
use App\Models\Mahasiswa;
use App\Models\Perwalian;
use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class EnsureAdmin extends Command
{
    protected $signature = 'app:ensure-admin';

    protected $description = 'Ensure all seed data exists (admin, dosen, mahasiswa, dosen wali, perwalian)';

    public function handle(): int
    {
        if (User::count() > 1) {
            $this->info('Seed data already exists.');
            return self::SUCCESS;
        }

        $this->info('Seeding all data...');

        DB::beginTransaction();
        try {
            // 1. Admin
            $admin = User::firstOrCreate(
                ['username' => 'admin'],
                [
                    'name' => 'Administrator',
                    'email' => 'admin@stmikbandung.ac.id',
                    'role' => User::ROLE_ADMIN,
                    'password' => '123456',
                ]
            );

            // 2. Dosen
            $dosenData = [
                ['nidn' => '0416078001', 'nama' => 'Dr. Ahmad Fauzi, M.Kom'],
                ['nidn' => '0425028102', 'nama' => 'Dr. Siti Rahayu, M.TI'],
                ['nidn' => '0407088403', 'nama' => 'Budi Santoso, M.Kom'],
                ['nidn' => '0427128804', 'nama' => 'Rina Wulandari, S.Kom., M.Cs'],
            ];

            $dosenList = collect();
            foreach ($dosenData as $d) {
                $user = User::firstOrCreate(
                    ['username' => $d['nidn']],
                    [
                        'name' => $d['nama'],
                        'email' => strtolower(str_replace(['.', ' ', ','], '', $d['nidn'])).'@dosen.stmikbandung.ac.id',
                        'role' => User::ROLE_DOSEN,
                        'password' => '123456',
                    ]
                );
                $dosenList->push(Dosen::firstOrCreate(
                    ['nidn' => $d['nidn']],
                    ['user_id' => $user->id, 'nama' => $d['nama']]
                ));
            }

            // 3. Mahasiswa
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
            foreach ($mahasiswaData as $m) {
                $user = User::firstOrCreate(
                    ['username' => $m['npm']],
                    [
                        'name' => $m['nama'],
                        'email' => $m['npm'].'@student.stmikbandung.ac.id',
                        'role' => User::ROLE_MAHASISWA,
                        'password' => '123456',
                    ]
                );
                $mahasiswaList->push(Mahasiswa::firstOrCreate(
                    ['npm' => $m['npm']],
                    ['user_id' => $user->id, 'nama' => $m['nama'], 'prodi' => $m['prodi'], 'angkatan' => $m['angkatan']]
                ));
            }

            // 4. Dosen Wali
            foreach ($mahasiswaList as $index => $mahasiswa) {
                $dosen = $dosenList->get($index % $dosenList->count());
                DosenWali::firstOrCreate(
                    ['mahasiswa_id' => $mahasiswa->id],
                    ['dosen_id' => $dosen->id]
                );
            }

            // 5. Perwalian
            $isiPerwalian = [
                'Membahas perkembangan studi semester ini. IPK tetap stabil dan seluruh mata kuliah dinyatakan lulus.',
                'Membahas rencana pengambilan mata kuliah semester depan. Disarankan mengambil mata kuliah pilihan sesuai minat bidang.',
                'Mendiskusikan kesulitan pada mata kuliah pemrograman. Dosen wali memberikan saran untuk mengikuti kelas tambahan.',
                'Mengevaluasi hasil studi dan kendala selama satu semester berjalan. Rencana perbaikan disusun bersama dosen wali.',
                'Membahas kesiapan menghadapi ujian akhir semester serta strategi belajar yang lebih efektif.',
                'Diskusi mengenai pemilihan topik tugas akhir yang sesuai dengan minat dan kemampuan akademik.',
            ];
            $kendalaData = [
                'Kesulitan memahami materi algoritma dan struktur data.',
                'Jadwal kuliah bentrok dengan kegiatan organisasi.',
                'Kurang fokus saat pembelajaran daring.',
                'Kesulitan membagi waktu antara kuliah dan pekerjaan paruh waktu.',
                null,
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
            $banyakPerwalian = [1, 2, 1, 3, 2, 1, 2, 1, 1, 2, 1, 2];

            foreach ($mahasiswaList as $index => $mahasiswa) {
                if (Perwalian::where('mahasiswa_id', $mahasiswa->id)->exists()) continue;

                $angkatan = (int) $mahasiswa->angkatan;
                $banyak = $banyakPerwalian[$index];
                $bulanTerakhir = $angkatan === 2022 ? 18 : ($angkatan === 2023 ? 9 : 3);

                for ($i = 0; $i < $banyak; $i++) {
                    $tanggal = now()->subMonths($bulanTerakhir)->addMonths($i * 5)->setDay(min(5 + $i * 8, 27));
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

            DB::commit();
            $this->info('All seed data created successfully!');
            return self::SUCCESS;

        } catch (\Exception $e) {
            DB::rollBack();
            $this->error('Seed failed: ' . $e->getMessage());
            return self::FAILURE;
        }
    }
}
