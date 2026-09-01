<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Mahasiswa;
use App\Models\User;
use App\Services\ExcelExportService;
use Illuminate\Http\Request;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;

// ============================================================
// CONTROLLER KELOLA DATA MAHASISWA (Sisi Admin)
// ============================================================
// Menangani CRUD (Create, Read, Update, Delete) data mahasiswa.
// Setiap mahasiswa otomatis punya akun login (User) dengan
// username = NPM dan password default.
//
// Model "route model binding" dipakai di sini: parameter
// (Mahasiswa $mahasiswa) otomatis berisi data mahasiswa sesuai id
// dari URL, jadi tidak perlu query manual.
// ============================================================
class MahasiswaController extends Controller
{
    // ===== MENAMPILKAN DAFTAR MAHASISWA (GET /admin/mahasiswa) =====
    public function index(Request $request)
    {
        // with('dosenWali.dosen') = eager loading (preload relasi
        // sekaligus) supaya query TIDAK n+1. 'dosenWali.dosen'
        // berarti: relasi dosenWali, lalu dari dosenWali ambil dosen.
        $query = Mahasiswa::with('dosenWali.dosen');

        // Fitur pencarian: jika ada parameter 'search' di URL
        if ($request->filled('search')) {
            $search = $request->input('search');

            // where(function) = bungkus beberapa kondisi OR
            // 'ilike' = pencarian tidak peka huruf besar/kecil (PostgreSQL)
            $query->where(function ($q) use ($search) {
                $q->where('npm', 'ilike', "%{$search}%")
                    ->orWhere('nama', 'ilike', "%{$search}%")
                    ->orWhere('prodi', 'ilike', "%{$search}%")
                    ->orWhere('angkatan', 'ilike', "%{$search}%");
            });
        }

        // latest() = urutkan paling baru; paginate(6) = 6 data per halaman;
        // withQueryString() = pertahankan parameter URL (mis. kata kunci
        // pencarian) saat pindah halaman paginasi.
        $mahasiswa = $query->latest()->paginate(6)->withQueryString();

        return view('admin.mahasiswa.index', compact('mahasiswa'));
    }

    // ===== EKSPOR DAFTAR MAHASISWA KE XLSX ELEGANT (GET /admin/mahasiswa/export) =====
    public function exportCsv(Request $request)
    {
        $query = Mahasiswa::with('dosenWali.dosen');
        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->where(function ($q) use ($search) {
                $q->where('npm', 'ilike', "%{$search}%")
                    ->orWhere('nama', 'ilike', "%{$search}%")
                    ->orWhere('prodi', 'ilike', "%{$search}%")
                    ->orWhere('angkatan', 'ilike', "%{$search}%");
            });
        }
        $mahasiswa = $query->orderByDesc('id')->get();
        $filename = 'data-mahasiswa-'.date('Y-m-d').'.xlsx';

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        ExcelExportService::setupSheet($sheet, 'Data Mahasiswa', 'Mahasiswa');

        $row = 1;
        $lastCol = 'F';
        $filters = $request->filled('search') ? ['search="'.$request->input('search').'"'] : [];
        ExcelExportService::addTitleBlock($sheet, 'Data Mahasiswa — SI Perwalian STMIK Bandung', $lastCol, $row, [
            'total'   => $mahasiswa->count(),
            'filters' => $filters,
        ]);

        $headerRow = $row;
        $headers = ['No', 'NPM', 'Nama Lengkap', 'Program Studi', 'Angkatan', 'Dosen Wali'];
        $col = 'A';
        foreach ($headers as $h) {
            $sheet->setCellValue($col.$headerRow, $h);
            $col++;
        }
        ExcelExportService::styleHeaderRow($sheet, "A{$headerRow}:{$lastCol}{$headerRow}");

        $r = $headerRow + 1;
        foreach ($mahasiswa as $i => $m) {
            $sheet->setCellValue("A{$r}", $i + 1);
            $sheet->setCellValue("B{$r}", $m->npm);
            $sheet->setCellValue("C{$r}", $m->nama);
            $sheet->setCellValue("D{$r}", $m->prodi);
            $sheet->setCellValue("E{$r}", $m->angkatan);
            $sheet->setCellValue("F{$r}", $m->dosenWali?->dosen?->nama ?? '— Belum ditentukan');
            $sheet->getRowDimension($r)->setRowHeight(20);
            $r++;
        }
        $lastDataRow = max($r - 1, $headerRow);
        if ($mahasiswa->isNotEmpty()) {
            ExcelExportService::styleDataRows($sheet, $headerRow, $lastDataRow, $lastCol);
            $sheet->getStyle("A".($headerRow+1).":A{$lastDataRow}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            $sheet->getStyle("E".($headerRow+1).":E{$lastDataRow}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        } else {
            $sheet->setCellValue("A".($headerRow+1), 'Tidak ada data sesuai filter.');
            $sheet->mergeCells("A".($headerRow+1).":{$lastCol}".($headerRow+1));
            $sheet->getStyle("A".($headerRow+1))->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            $lastDataRow = $headerRow + 1;
        }
        ExcelExportService::finalize($sheet, $headerRow, $lastCol, ['A' => 6, 'B' => 16, 'C' => 30, 'D' => 28, 'E' => 12, 'F' => 30]);
        $sheet->mergeCells("A".($lastDataRow + 2).":{$lastCol}".($lastDataRow + 2));
        $sheet->setCellValue("A".($lastDataRow + 2), '© '.date('Y').' STMIK Bandung — SI Perwalian Mahasiswa  •  Dicetak: '.now()->translatedFormat('d F Y H:i'));
        $sheet->getStyle("A".($lastDataRow + 2))->applyFromArray([
            'font' => ['size' => 8, 'italic' => true, 'color' => ['rgb' => '64748B'], 'name' => 'Calibri'],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
        ]);

        return ExcelExportService::downloadResponse($spreadsheet, $filename);
    }

    // ===== FORM IMPORT MAHASISWA DARI EXCEL (GET /admin/mahasiswa/import) =====
    public function importForm()
    {
        return view('admin.mahasiswa.import');
    }

    // ===== PROSES IMPORT MAHASISWA DARI EXCEL (POST /admin/mahasiswa/import) =====
    public function importProcess(Request $request)
    {
        $request->validate([
            'file' => 'required|file|mimes:xlsx,xls|max:10240',
        ]);

        $file = $request->file('file');
        $spreadsheet = \PhpOffice\PhpSpreadsheet\IOFactory::load($file->getRealPath());
        $sheet = $spreadsheet->getActiveSheet();
        $rows = $sheet->toArray();

        if (count($rows) < 2) {
            return back()->withErrors(['file' => 'File Excel kosong atau tidak memiliki data.']);
        }

        // Baris pertama = header — deteksi otomatis kolom mana yang mana
        $header = array_map(fn($h) => strtolower(trim($h)), $rows[0]);

        // Map header fleksibel: terima variasi nama kolom
        $colMap = [];
        foreach ($header as $idx => $h) {
            if (in_array($h, ['npm']))                         $colMap['npm'] = $idx;
            elseif (in_array($h, ['nama', 'nama lengkap']))   $colMap['nama'] = $idx;
            elseif (in_array($h, ['prodi', 'program studi', 'program_studi'])) $colMap['prodi'] = $idx;
            elseif (in_array($h, ['angkatan', 'angkatan']))   $colMap['angkatan'] = $idx;
        }

        // Pastikan semua kolom wajib ditemukan
        $missing = array_diff(['npm', 'nama', 'prodi', 'angkatan'], array_keys($colMap));
        if (!empty($missing)) {
            $kolom = implode(', ', $missing);
            return back()->withErrors(['file' => "Kolom tidak ditemukan: {$kolom}. Pastikan header Excel: NPM, Nama Lengkap, Program Studi, Angkatan."]);
        }

        $success = 0;
        $skipped = 0;
        $errors = [];

        for ($i = 1; $i < count($rows); $i++) {
            $row = $rows[$i];
            $npm = trim((string) ($row[$colMap['npm']] ?? ''));
            $nama = trim((string) ($row[$colMap['nama']] ?? ''));
            $prodi = trim((string) ($row[$colMap['prodi']] ?? ''));
            $angkatan = trim((string) ($row[$colMap['angkatan']] ?? ''));

            // Skip baris kosong
            if ($npm === '' && $nama === '') {
                $skipped++;
                continue;
            }

            // Validasi wajib isi
            if ($npm === '' || $nama === '' || $prodi === '' || $angkatan === '') {
                $errors[] = "Baris ".($i+1).": data tidak lengkap (NPM: {$npm})";
                $skipped++;
                continue;
            }

            // Cek NPM unik
            if (Mahasiswa::where('npm', $npm)->exists()) {
                $errors[] = "Baris ".($i+1).": NPM {$npm} sudah ada";
                $skipped++;
                continue;
            }

            // Buat akun login
            $user = User::create([
                'name' => $nama,
                'username' => $npm,
                'email' => null,
                'role' => User::ROLE_MAHASISWA,
                'password' => '123456',
            ]);

            // Buat data mahasiswa
            Mahasiswa::create([
                'user_id' => $user->id,
                'npm' => $npm,
                'nama' => $nama,
                'prodi' => $prodi,
                'angkatan' => $angkatan,
            ]);

            $success++;
        }

        $msg = "Import selesai: {$success} data berhasil ditambahkan.";
        if ($skipped > 0) {
            $msg .= " {$skipped} data dilewati.";
        }

        if (!empty($errors)) {
            $msg .= "\n".implode("\n", array_slice($errors, 0, 10));
            if (count($errors) > 10) {
                $msg .= "\n... dan ".(count($errors)-10)." error lainnya.";
            }
        }

        return redirect()
            ->route('admin.mahasiswa.index')
            ->with('success', $msg);
    }

    // ===== DOWNLOAD TEMPLATE IMPORT (GET /admin/mahasiswa/import/template) =====
    public function importTemplate()
    {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Template Mahasiswa');

        // Header — harus sesuai: NPM, Nama Lengkap, Program Studi, Angkatan
        $headers = ['NPM', 'Nama Lengkap', 'Program Studi', 'Angkatan'];
        foreach ($headers as $i => $h) {
            $col = chr(65 + $i);
            $sheet->setCellValue("{$col}1", $h);
        }

        // Style header: navy bg, white bold text
        $sheet->getStyle('A1:D1')->applyFromArray([
            'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF'], 'size' => 11, 'name' => 'Calibri'],
            'fill' => ['fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID, 'startColor' => ['rgb' => '0F172A']],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
            'borders' => [
                'bottom' => ['borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN, 'color' => ['rgb' => 'CBD5E1']],
            ],
        ]);
        $sheet->getRowDimension(1)->setRowHeight(24);

        // Format kolom
        $sheet->getColumnDimension('A')->setWidth(18);
        $sheet->getColumnDimension('B')->setWidth(32);
        $sheet->getColumnDimension('C')->setWidth(28);
        $sheet->getColumnDimension('D')->setWidth(14);

        // 100 data mahasiswa contoh
        $prodiList = ['Teknik Informatika', 'Sistem Informasi', 'Manajemen Informatika', 'Teknik Komputer'];
        $angkatanList = ['2022', '2023', '2024', '2025'];
        $namaDepan = ['Andi', 'Budi', 'Citra', 'Dewi', 'Eka', 'Fajar', 'Gita', 'Hadi', 'Indah', 'Joko',
                       'Kartika', 'Lukman', 'Maya', 'Nanda', 'Omar', 'Putri', 'Rizky', 'Sari', 'Tono', 'Ulya',
                       'Vina', 'Wira', 'Xenia', 'Yoga', 'Zain'];
        $namaBelakang = ['Pratama', 'Putra', 'Putri', 'Sari', 'Saputra', 'Handayani', 'Susanto', 'Wijaya',
                          'Ramadhan', 'Purnama', 'Setiawan', 'Hidayat', 'Kurniawan', 'Santoso', 'Lestari'];

        for ($i = 0; $i < 100; $i++) {
            $row = $i + 2;
            $npm = '230' . str_pad($i + 1, 4, '0', STR_PAD_LEFT);
            $nama = $namaDepan[array_rand($namaDepan)] . ' ' . $namaBelakang[array_rand($namaBelakang)];
            $prodi = $prodiList[array_rand($prodiList)];
            $angkatan = $angkatanList[array_rand($angkatanList)];

            $sheet->setCellValue("A{$row}", $npm);
            $sheet->setCellValue("B{$row}", $nama);
            $sheet->setCellValue("C{$row}", $prodi);
            $sheet->setCellValue("D{$row}", $angkatan);
        }

        // Zebra striping
        for ($r = 2; $r <= 101; $r++) {
            if ($r % 2 === 0) {
                $sheet->getStyle("A{$r}:D{$r}")->getFill()
                    ->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)
                    ->getStartColor()->setRGB('F8FAFC');
            }
            $sheet->getRowDimension($r)->setRowHeight(18);
        }

        // Freeze header
        $sheet->freezePane('A2');

        $filename = 'template-import-mahasiswa.xlsx';
        return ExcelExportService::downloadResponse($spreadsheet, $filename);
    }

    // ===== FORM TAMBAH MAHASISWA (GET /admin/mahasiswa/create) =====
    public function create()
    {
        return view('admin.mahasiswa.create');
    }

    // ===== SIMPAN MAHASISWA BARU (POST /admin/mahasiswa) =====
    public function store(Request $request)
    {
        // Validasi: aturan masing-masing field.
        // 'unique:mahasiswa,npm' = npm tidak boleh dobel di tabel mahasiswa.
        $data = $request->validate([
            'npm' => ['required', 'string', 'max:20', 'unique:mahasiswa,npm'],
            'nama' => ['required', 'string', 'max:150'],
            'prodi' => ['required', 'string', 'max:100'],
            'angkatan' => ['required', 'string', 'max:10'],
            'email' => ['nullable', 'email', 'max:150', 'unique:users,email'],
            'password' => ['nullable', 'string', 'min:6'],
        ]);

        // Jika password tidak diisi, pakai default '123456'
        $password = $data['password'] ?: '123456';

        // 1) Buat akun login dulu. username = NPM, role = mahasiswa.
        //    Model User punya mutator yang otomatis meng-hash password.
        $user = User::create([
            'name' => $data['nama'],
            'username' => $data['npm'],
            'email' => $data['email'] ?: null,
            'role' => User::ROLE_MAHASISWA,
            'password' => $password,
        ]);

        // 2) Buat data mahasiswa yang menautkan ke akun user di atas
        Mahasiswa::create([
            'user_id' => $user->id,
            'npm' => $data['npm'],
            'nama' => $data['nama'],
            'prodi' => $data['prodi'],
            'angkatan' => $data['angkatan'],
        ]);

        // 3) Kembali ke daftar + pesan sukses (flash session)
        return redirect()
            ->route('admin.mahasiswa.index')
            ->with('success', 'Data mahasiswa berhasil ditambahkan. Password default: '.$password);
    }

    // ===== DETAIL MAHASISWA (GET /admin/mahasiswa/{mahasiswa}) =====
    public function show(Mahasiswa $mahasiswa)
    {
        // Muat relasi yang dibutuhkan untuk halaman detail:
        // dosen wali (dengan dosennya) + riwayat perwalian
        $mahasiswa->load('dosenWali.dosen', 'perwalian');

        return view('admin.mahasiswa.show', compact('mahasiswa'));
    }

    // ===== FORM EDIT MAHASISWA (GET /admin/mahasiswa/{mahasiswa}/edit) =====
    public function edit(Mahasiswa $mahasiswa)
    {
        return view('admin.mahasiswa.edit', compact('mahasiswa'));
    }

    // ===== PERBARUI MAHASISWA (PUT /admin/mahasiswa/{mahasiswa}) =====
    public function update(Request $request, Mahasiswa $mahasiswa)
    {
        // 'unique:mahasiswa,npm,'.$mahasiswa->id = validasi unik TAPI
        // kecualikan id yang sedang diedit (supaya tidak bentrok diri sendiri).
        $data = $request->validate([
            'npm' => ['required', 'string', 'max:20', 'unique:mahasiswa,npm,'.$mahasiswa->id],
            'nama' => ['required', 'string', 'max:150'],
            'prodi' => ['required', 'string', 'max:100'],
            'angkatan' => ['required', 'string', 'max:10'],
            'email' => ['nullable', 'email', 'max:150', 'unique:users,email,'.$mahasiswa->user_id],
            'password' => ['nullable', 'string', 'min:6'],
        ]);

        // 1) Perbarui tabel mahasiswa
        $mahasiswa->update([
            'npm' => $data['npm'],
            'nama' => $data['nama'],
            'prodi' => $data['prodi'],
            'angkatan' => $data['angkatan'],
        ]);

        // 2) Perbarui akun login-nya juga.
        //    ?-> = user bisa null (jaga-jaga). Password hanya diganti jika
        //    field password diisi; jika kosong, pertahankan password lama.
        $mahasiswa->user?->update([
            'name' => $data['nama'],
            'username' => $data['npm'],
            'email' => $data['email'] ?: null,
            'password' => filled($data['password']) ? $data['password'] : $mahasiswa->user->password,
        ]);

        return redirect()
            ->route('admin.mahasiswa.index')
            ->with('success', 'Data mahasiswa berhasil diperbarui.');
    }

    // ===== HAPUS MAHASISWA (DELETE /admin/mahasiswa/{mahasiswa}) =====
    public function destroy(Mahasiswa $mahasiswa)
    {
        // Simpan relasi user-nya sebelum data mahasiswa dihapus
        $user = $mahasiswa->user;

        // Hapus data mahasiswa
        $mahasiswa->delete();

        // Hapus akun login user-nya juga (agar tidak jadi akun yatim)
        $user?->delete();

        return redirect()
            ->route('admin.mahasiswa.index')
            ->with('success', 'Data mahasiswa berhasil dihapus.');
    }
}
