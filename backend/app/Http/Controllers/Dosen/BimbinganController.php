<?php

namespace App\Http\Controllers\Dosen;

use App\Http\Controllers\Controller;
use App\Models\Dosen;
use App\Models\Mahasiswa;
use App\Models\Perwalian;
use Illuminate\Http\Request;

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

    // ===== EKSPOR DATA BIMBINGAN KE CSV (GET /dosen/bimbingan/export) =====
    public function exportCsv(Request $request)
    {
        $dosen = auth()->user()->dosen()->first();

        $query = $dosen->mahasiswaBimbingan()->withCount('perwalian');

        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->where(function ($q) use ($search) {
                $q->where('npm', 'ilike', "%{$search}%")
                    ->orWhere('nama', 'ilike', "%{$search}%")
                    ->orWhere('prodi', 'ilike', "%{$search}%");
            });
        }

        if ($request->filled('angkatan')) {
            $query->where('angkatan', $request->input('angkatan'));
        }

        // Preload relasi perwalian agar isi catatan bisa ikut diekspor
        $mahasiswa = $query->with('perwalian')->orderBy('nama')->get();

        $filename = 'mahasiswa-bimbingan-'.date('Y-m-d').'.csv';

        return response()->streamDownload(function () use ($mahasiswa) {
            $out = fopen('php://output', 'w');
            // BOM UTF-8 agar aman dibuka di Excel
            fwrite($out, "\xEF\xBB\xBF");

            fputcsv($out, ['NPM', 'Nama', 'Program Studi', 'Angkatan', 'Tanggal', 'Semester', 'Hasil Diskusi', 'Kendala', 'Rencana Perbaikan']);

            foreach ($mahasiswa as $m) {
                // Jika mahasiswa BELUM punya catatan perwalian:
                // tetap tulis satu baris (data mahasiswa + kolom kosong).
                if ($m->perwalian->isEmpty()) {
                    fputcsv($out, [$m->npm, $m->nama, $m->prodi, $m->angkatan, '', '', '', '', '']);
                    continue;
                }

                // Jika punya catatan: satu baris PER catatan perwalian,
                // supaya isi diskusi (hasil, kendala, rencana) ikut terlihat.
                foreach ($m->perwalian as $p) {
                    fputcsv($out, [
                        $m->npm,
                        $m->nama,
                        $m->prodi,
                        $m->angkatan,
                        $p->tanggal?->toDateString() ?? '',
                        $p->semester,
                        $p->hasil_perwalian,
                        $p->kendala ?? '',
                        $p->rencana_perbaikan ?? '',
                    ]);
                }
            }
            fclose($out);
        }, $filename, ['Content-Type' => 'text/csv; charset=UTF-8']);
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
