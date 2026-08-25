@extends('layouts.app')

{{-- Judul di tab browser dan di topbar --}}
@section('title', 'Histori Perwalian')
@section('heading', 'Histori Perwalian')

@section('content')
    {{-- KARTU IDENTITAS MAHASISWA: menampilkan info singkat mahasiswa yang sedang dilihat --}}
    <div class="card mb-3 animate-fade-up">
        <div class="card-body py-3 d-flex align-items-center justify-content-between flex-wrap gap-2">
            <div class="d-flex align-items-center gap-3">
                <div class="stat-icon navy" style="width:46px;height:46px;"><i class="bi bi-person"></i></div>
                <div>
                    <h6 class="fw-bold mb-0">{{ $mahasiswa->nama }}</h6>
                    <small class="text-muted">{{ $mahasiswa->npm }} • {{ $mahasiswa->prodi }} • Angkatan {{ $mahasiswa->angkatan }}</small>
                </div>
            </div>
            {{-- Tombol kembali ke daftar mahasiswa bimbingan --}}
            <a href="{{ route('dosen.bimbingan.index') }}" class="btn btn-outline-secondary btn-sm">
                <i class="bi bi-arrow-left me-1"></i> Kembali
            </a>
        </div>
    </div>

    {{-- KARTU RIWAYAT: tabel semua catatan perwalian mahasiswa ini (dikirim dari controller) --}}
    <div class="card animate-fade-up delay-1">
        <div class="card-header d-flex align-items-center justify-content-between">
            <span><i class="bi bi-journal-text me-2 text-primary"></i>Riwayat Perwalian</span>
            <span class="badge badge-soft-primary">{{ $perwalian->count() }} catatan</span>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead>
                        <tr>
                            <th>Tanggal</th>
                            <th>Semester</th>
                            <th>Hasil Diskusi</th>
                            <th>Kendala</th>
                            <th>Rencana Perbaikan</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($perwalian as $p)
                            <tr>
                                <td class="text-nowrap">{{ $p->tanggal->translatedFormat('d M Y') }}</td>
                                <td><span class="badge badge-soft-primary">Semester {{ $p->semester }}</span></td>
                                <td style="max-width:260px;">{{ $p->hasil_perwalian }}</td>
                                <td style="max-width:200px;">{{ $p->kendala ?? '-' }}</td>
                                <td style="max-width:200px;">{{ $p->rencana_perbaikan ?? '-' }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="text-center text-muted py-4">
                                    <div class="empty-state">
                                        <i class="bi bi-inbox"></i>
                                        <p class="mb-0">Belum ada catatan perwalian dari mahasiswa ini.</p>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endsection
