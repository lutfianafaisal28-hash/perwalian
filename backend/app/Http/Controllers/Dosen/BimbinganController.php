<?php

namespace App\Http\Controllers\Dosen;

use App\Http\Controllers\Controller;
use App\Models\Dosen;
use App\Models\Mahasiswa;
use App\Models\Perwalian;
use App\Services\ExcelExportService;
use Illuminate\Http\Request;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;

// ============================================================
// CONTROLLER BIMBINGAN (Sisi Dosen)
// ============================================================
// Dosen wali melihat daftar mahasiswa bimbingannya, detail catatan
// perwalian tiap mahasiswa, dan bisa mengunduh (ekspor CSV).
// Hanya dosen yang tercatat sebagai wali mahasiswa tersebut yang
// boleh melihat detailnya (ada pemeriksaan di method show).
// ============================================================
class BimbinganController extends Controller
{
    // ===== DAFTAR MAHASISWA BIMBINGAN (GET /dosen/bimbingan) =====
    public function index(Request $request)
    {
        // Dosen yang sedang login
        $dosen = auth()->user()->dosen()->first();

        // Semua mahasiswa bimbingan dosen ini + jumlah perwalian tiap mahasiswa
        $query = $dosen->mahasiswaBimbingan()->withCount('perwalian');

        // Pencarian berdasarkan NPM / nama / prodi
        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->where(function ($q) use ($search) {
                $q->where('npm', 'ilike', "%{$search}%")
                    ->orWhere('nama', 'ilike', "%{$search}%")
                    ->orWhere('prodi', 'ilike', "%{$search}%");
            });
        }

        // Filter berdasarkan angkatan
        if ($request->filled('angkatan')) {
            $query->where('angkatan', $request->input('angkatan'));
        }

        // Daftar angkatan yang dimiliki mahasiswa bimbingan ini,
        // dipakai untuk dropdown filter angkatan.
        $angkatanList = $dosen->mahasiswaBimbingan()
            ->distinct()
            ->orderBy('angkatan')
            ->pluck('angkatan');

        $mahasiswa = $query->orderBy('nama')->paginate(6)->withQueryString();

        return view('dosen.bimbingan.index', compact('mahasiswa', 'angkatanList'));
    }

    // ===== EKSPOR DATA BIMBINGAN KE XLSX ELEGANT (GET /dosen/bimbingan/export) =====
    public function exportCsv(Request $request)
    {
        $dosen = auth()->user()->dosen()->first();
        $query = $dosen->mahasiswaBimbingan()->withCount('perwalian');
        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->where(function ($q) use ($search) {
                $q->where('npm', 'ilike', "%{$search}%")->orWhere('nama', 'ilike', "%{$search}%")->orWhere('prodi', 'ilike', "%{$search}%");
            });
        }
        if ($request->filled('angkatan')) {
            $query->where('angkatan', $request->input('angkatan'));
        }
        $mahasiswa = $query->with('perwalian')->orderBy('nama')->get();
        $filename = 'mahasiswa-bimbingan-'.date('Y-m-d').'.xlsx';

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        ExcelExportService::setupSheet($sheet, 'Bimbingan', 'Bimbingan');

        $row = 1;
        $lastCol = 'I';
        $filters = [];
        if ($request->filled('search')) $filters[] = 'search="'.$request->input('search').'"';
        if ($request->filled('angkatan')) $filters[] = 'angkatan '.$request->input('angkatan');
        ExcelExportService::addTitleBlock($sheet, 'Mahasiswa Bimbingan — '.$dosen->nama.' (STMIK Bandung)', $lastCol, $row, [
            'total'   => $mahasiswa->count(),
            'filters' => $filters,
        ]);

        $headerRow = $row;
        foreach (['No','NPM','Nama','Program Studi','Angkatan','Tanggal','Semester','Hasil Diskusi','Kendala / Rencana'] as $i => $h) {
            $sheet->setCellValue(chr(65+$i).$headerRow, $h);
        }
        ExcelExportService::styleHeaderRow($sheet, "A{$headerRow}:{$lastCol}{$headerRow}");

        $r = $headerRow + 1;
        $no = 1;
        foreach ($mahasiswa as $m) {
            if ($m->perwalian->isEmpty()) {
                $sheet->setCellValue("A{$r}", $no++);
                $sheet->setCellValue("B{$r}", $m->npm);
                $sheet->setCellValue("C{$r}", $m->nama);
                $sheet->setCellValue("D{$r}", $m->prodi);
                $sheet->setCellValue("E{$r}", $m->angkatan);
                $sheet->setCellValue("F{$r}", '—');
                $sheet->setCellValue("G{$r}", '—');
                $sheet->setCellValue("H{$r}", 'Belum ada catatan perwalian');
                $sheet->setCellValue("I{$r}", '—');
                $sheet->getRowDimension($r)->setRowHeight(20);
                $r++;
                continue;
            }
            foreach ($m->perwalian as $p) {
                $sheet->setCellValue("A{$r}", $no++);
                $sheet->setCellValue("B{$r}", $m->npm);
                $sheet->setCellValue("C{$r}", $m->nama);
                $sheet->setCellValue("D{$r}", $m->prodi);
                $sheet->setCellValue("E{$r}", $m->angkatan);
                $sheet->setCellValue("F{$r}", $p->tanggal?->translatedFormat('d MMM yyyy') ?? '');
                $sheet->setCellValue("G{$r}", 'Semester '.$p->semester);
                $sheet->setCellValue("H{$r}", $p->hasil_perwalian);
                $sheet->setCellValue("I{$r}", ($p->kendala ? "Kendala: {$p->kendala}\n" : '').($p->rencana_perbaikan ? "Rencana: {$p->rencana_perbaikan}" : '—'));
                // Dynamic row height based on content
                $texts = [$p->hasil_perwalian, $p->kendala ?? '', $p->rencana_perbaikan ?? ''];
                $rowHeight = ExcelExportService::calcRowHeight($texts, [44, 36, 40], 24, 14);
                $sheet->getRowDimension($r)->setRowHeight($rowHeight);
                $r++;
            }
        }
        $lastDataRow = max($r - 1, $headerRow);
        if ($mahasiswa->isNotEmpty()) {
            ExcelExportService::styleDataRows($sheet, $headerRow, $lastDataRow, $lastCol);
            $sheet->getStyle("A".($headerRow+1).":A{$lastDataRow}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            $sheet->getStyle("E".($headerRow+1).":G{$lastDataRow}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            $sheet->getStyle("H".($headerRow+1).":I{$lastDataRow}")->getAlignment()->setWrapText(true);
        }
        ExcelExportService::finalize($sheet, $headerRow, $lastCol, ['A' => 6, 'B' => 15, 'C' => 28, 'D' => 24, 'E' => 12, 'F' => 14, 'G' => 12, 'H' => 44, 'I' => 44]);
        $sheet->mergeCells("A".($lastDataRow + 2).":{$lastCol}".($lastDataRow + 2));
        $sheet->setCellValue("A".($lastDataRow + 2), '© '.date('Y').' STMIK Bandung — SI Perwalian');
        $sheet->getStyle("A".($lastDataRow + 2))->applyFromArray([
            'font' => ['size' => 8, 'italic' => true, 'color' => ['rgb' => '64748B'], 'name' => 'Calibri'],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
        ]);

        return ExcelExportService::downloadResponse($spreadsheet, $filename);
    }

    // ===== DETAIL BIMBINGAN SATU MAHASISWA (GET /dosen/bimbingan/{mahasiswa}) =====
    public function show(Request $request, Mahasiswa $mahasiswa)
    {
        // Dosen yang sedang login
        $dosen = auth()->user()->dosen()->first();

        // PEMERIKSAAN KEAMANAN: cek apakah mahasiswa ini benar-benar
        // mahasiswa bimbingan dosen yang login. whereKey = cari dosen
        // dengan id tersebut; whereHas('bimbingan') = yang terhubung
        // ke mahasiswa ini lewat tabel perantara bimbingan.
        $terdaftar = Dosen::whereKey($dosen->id)
            ->whereHas('bimbingan', fn ($q) => $q->where('mahasiswa_id', $mahasiswa->id))
            ->exists();

        // Jika bukan bimbingannya -> tolak akses 403
        if (! $terdaftar) {
            abort(403, 'Mahasiswa tersebut bukan mahasiswa bimbingan Anda.');
        }

        // Ambil semua catatan perwalian mahasiswa (terbaru di atas)
        $perwalian = Perwalian::where('mahasiswa_id', $mahasiswa->id)
            ->latest('tanggal')
            ->get();

        return view('dosen.bimbingan.show', compact('mahasiswa', 'perwalian'));
    }
}
