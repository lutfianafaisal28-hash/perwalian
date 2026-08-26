<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Dosen;
use App\Models\Mahasiswa;
use App\Models\Perwalian;
use App\Services\ExcelExportService;
use Illuminate\Http\Request;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;

// ============================================================
// CONTROLLER REKAP PERWALIAN (Sisi Admin)
// ============================================================
// Menampilkan rekap semua catatan perwalian di seluruh program
// studi, lengkap dengan fitur filter (pencarian, angkatan, dosen
// wali, rentang tanggal) dan ekspor ke CSV.
// ============================================================
class RekapController extends Controller
{
    // ===== HALAMAN REKAP (GET /admin/rekap) =====
    public function index(Request $request)
    {
        // Bangun query dasar + terapkan filter dari URL
        $query = $this->buildQuery($request);

        // Paginasi 6 data per halaman
        $perwalian = $query->paginate(6)->withQueryString();

        // total() = jumlah semua data (setelah filter, sebelum paginasi)
        $jumlah = $perwalian->total();

        // Data untuk dropdown filter di halaman
        $angkatanList = Mahasiswa::distinct()->orderBy('angkatan')->pluck('angkatan');
        $dosenList = Dosen::orderBy('nama')->get();

        return view('admin.rekap.index', compact('perwalian', 'jumlah', 'angkatanList', 'dosenList'));
    }

    // ===== EKSPOR REKAP KE XLSX ELEGANT (GET /admin/rekap/export) =====
    public function exportCsv(Request $request)
    {
        $perwalian = $this->buildQuery($request)->get();
        $filename = 'rekap-perwalian-'.date('Y-m-d').'.xlsx';

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        ExcelExportService::setupSheet($sheet, 'Rekap Perwalian', 'Rekap');

        $row = 1;
        $lastCol = 'I';
        $filterInfo = [];
        if ($request->filled('search')) $filterInfo[] = 'search="'.$request->input('search').'"';
        if ($request->filled('angkatan')) $filterInfo[] = 'angkatan='.$request->input('angkatan');
        if ($request->filled('dosen_id')) $filterInfo[] = 'dosen wali';
        if ($request->filled('dari')) $filterInfo[] = 'dari '.$request->input('dari');
        if ($request->filled('sampai')) $filterInfo[] = 'sampai '.$request->input('sampai');
        $filterStr = $filterInfo ? implode(', ', $filterInfo) : 'semua data';

        ExcelExportService::addTitleBlock($sheet, 'Rekap Perwalian — SI Perwalian STMIK Bandung', 'Diekspor: '.now()->translatedFormat('d F Y H:i').' WIB  •  Filter: '.$filterStr.'  •  Total: '.$perwalian->count().' catatan', $lastCol, $row);

        $headerRow = $row;
        $headers = ['No','Tanggal','NPM','Nama Mahasiswa','Semester','Dosen Wali','Hasil Diskusi','Kendala','Rencana Perbaikan'];
        foreach ($headers as $i => $h) {
            $sheet->setCellValue(chr(65+$i).$headerRow, $h);
        }
        ExcelExportService::styleHeaderRow($sheet, "A{$headerRow}:{$lastCol}{$headerRow}");

        $r = $headerRow + 1;
        foreach ($perwalian as $idx => $p) {
            $sheet->setCellValue("A{$r}", $idx+1);
            $sheet->setCellValue("B{$r}", $p->tanggal?->translatedFormat('d MMM yyyy') ?? '');
            $sheet->setCellValue("C{$r}", $p->mahasiswa?->npm ?? '');
            $sheet->setCellValue("D{$r}", $p->mahasiswa?->nama ?? '');
            $sheet->setCellValue("E{$r}", 'Semester '.$p->semester);
            $sheet->setCellValue("F{$r}", $p->mahasiswa?->dosenWali?->dosen?->nama ?? '—');
            $sheet->setCellValue("G{$r}", $p->hasil_perwalian);
            $sheet->setCellValue("H{$r}", $p->kendala ?? '—');
            $sheet->setCellValue("I{$r}", $p->rencana_perbaikan ?? '—');
            $sheet->getRowDimension($r)->setRowHeight(32);
            $r++;
        }
        $lastDataRow = max($r-1, $headerRow);
        if ($perwalian->isNotEmpty()) {
            ExcelExportService::styleDataRows($sheet, $headerRow, $lastDataRow, $lastCol);
            $sheet->getStyle("A".($headerRow+1).":A{$lastDataRow}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            $sheet->getStyle("B".($headerRow+1).":B{$lastDataRow}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            $sheet->getStyle("E".($headerRow+1).":E{$lastDataRow}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            $sheet->getStyle("G".($headerRow+1).":I{$lastDataRow}")->getAlignment()->setWrapText(true);
        } else {
            $sheet->setCellValue("A".($headerRow+1), 'Tidak ada data sesuai filter.');
            $sheet->mergeCells("A".($headerRow+1).":{$lastCol}".($headerRow+1));
            $sheet->getStyle("A".($headerRow+1))->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            $lastDataRow = $headerRow+1;
        }
        ExcelExportService::finalize($sheet, $headerRow, $lastCol, ['A'=>5,'B'=>13,'C'=>13,'D'=>24,'E'=>11,'F'=>24,'G'=>38,'H'=>28,'I'=>32]);
        $sheet->setCellValue("A".($lastDataRow+2), '© '.date('Y').' STMIK Bandung — SI Perwalian Mahasiswa');
        $sheet->getStyle("A".($lastDataRow+2))->getFont()->setSize(7)->setItalic(true)->getColor()->setRGB('64748B');

        return ExcelExportService::downloadResponse($spreadsheet, $filename);
    }

    // ===== HAPUS SATU CATATAN PERWALIAN (DELETE /admin/rekap/{perwalian}) =====
    public function destroy(Perwalian $perwalian)
    {
        $perwalian->delete();

        return redirect()
            ->route('admin.rekap.index')
            ->with('success', 'Catatan perwalian berhasil dihapus.');
    }

    // ===== HELPER: BANGUN QUERY DENGAN FILTER =====
    // Method private: hanya dipakai di dalam class ini, baik oleh
    // index() maupun exportCsv(), supaya filternya sama.
    private function buildQuery(Request $request)
    {
        // with('mahasiswa.dosenWali.dosen') = preload relasi agar
        // data mahasiswa + walinya ikut terbawa.
        // latest('tanggal') = urutkan dari tanggal terbaru.
        $query = Perwalian::with('mahasiswa.dosenWali.dosen')->latest('tanggal');

        // Filter kata kunci: cari lewat mahasiswa terkait
        if ($request->filled('search')) {
            $search = $request->input('search');
            // whereHas = filter baris perwalian yang mahasiswanya cocok
            $query->whereHas('mahasiswa', function ($q) use ($search) {
                $q->where('npm', 'ilike', "%{$search}%")
                    ->orWhere('nama', 'ilike', "%{$search}%");
            });
        }

        // Filter angkatan
        if ($request->filled('angkatan')) {
            $query->whereHas('mahasiswa', fn ($q) => $q->where('angkatan', $request->input('angkatan')));
        }

        // Filter dosen wali
        if ($request->filled('dosen_id')) {
            $query->whereHas('mahasiswa.dosenWali', fn ($q) => $q->where('dosen_id', $request->input('dosen_id')));
        }

        // Filter rentang tanggal (dari / sampai)
        if ($request->filled('dari')) {
            $query->whereDate('tanggal', '>=', $request->input('dari'));
        }

        if ($request->filled('sampai')) {
            $query->whereDate('tanggal', '<=', $request->input('sampai'));
        }

        return $query;
    }
}
