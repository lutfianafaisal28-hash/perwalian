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
        ExcelExportService::addTitleBlock($sheet, 'Data Mahasiswa — SI Perwalian STMIK Bandung', 'Diekspor: '.now()->translatedFormat('d F Y H:i').' WIB  •  Filter: '.($request->input('search') ? 'search="'.$request->input('search').'"' : 'semua data').'  •  Total: '.$mahasiswa->count().' mahasiswa', $lastCol, $row);

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
            $sheet->getRowDimension($r)->setRowHeight(18);
            $r++;
        }
        $lastDataRow = max($r - 1, $headerRow);
        if ($mahasiswa->isNotEmpty()) {
            ExcelExportService::styleDataRows($sheet, $headerRow, $lastDataRow, $lastCol);
            $sheet->getStyle("A".($headerRow+1).":A{$lastDataRow}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            $sheet->getStyle("E".($headerRow+1).":E{$lastDataRow}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            // No. column narrow
        } else {
            $sheet->setCellValue("A".($headerRow+1), 'Tidak ada data sesuai filter.');
            $sheet->mergeCells("A".($headerRow+1).":{$lastCol}".($headerRow+1));
            $sheet->getStyle("A".($headerRow+1))->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            $lastDataRow = $headerRow + 1;
        }
        ExcelExportService::finalize($sheet, $headerRow, $lastCol, ['A'=>5,'B'=>14,'C'=>26,'D'=>22,'E'=>10,'F'=>28]);
        // Footer
        $sheet->setCellValue("A".($lastDataRow+2), '© '.date('Y').' STMIK Bandung — SI Perwalian Mahasiswa  •  Dicetak: '.now()->translatedFormat('d F Y H:i'));
        $sheet->getStyle("A".($lastDataRow+2))->getFont()->setSize(7)->setItalic(true)->getColor()->setRGB('64748B');

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
