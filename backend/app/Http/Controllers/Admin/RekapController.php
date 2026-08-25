<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Dosen;
use App\Models\Mahasiswa;
use App\Models\Perwalian;
use Illuminate\Http\Request;

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

    // ===== EKSPOR REKAP KE CSV (GET /admin/rekap/export) =====
    public function exportCsv(Request $request)
    {
        // Ambil semua data (tanpa paginasi) sesuai filter aktif
        $perwalian = $this->buildQuery($request)->get();

        $filename = 'rekap-perwalian-'.date('Y-m-d').'.csv';

        return response()->streamDownload(function () use ($perwalian) {
            $out = fopen('php://output', 'w');
            // BOM UTF-8 supaya tidak rusak saat dibuka di Excel
            fwrite($out, "\xEF\xBB\xBF");

            fputcsv($out, ['Tanggal', 'NPM', 'Nama Mahasiswa', 'Semester', 'Dosen Wali', 'Hasil Diskusi', 'Kendala', 'Rencana Perbaikan']);

            foreach ($perwalian as $p) {
                fputcsv($out, [
                    $p->tanggal?->toDateString() ?? '',
                    $p->mahasiswa?->npm ?? '',
                    $p->mahasiswa?->nama ?? '',
                    $p->semester,
                    $p->mahasiswa?->dosenWali?->dosen?->nama ?? '',
                    $p->hasil_perwalian,
                    $p->kendala ?? '',
                    $p->rencana_perbaikan ?? '',
                ]);
            }
            fclose($out);
        }, $filename, ['Content-Type' => 'text/csv; charset=UTF-8']);
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
