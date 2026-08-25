@extends('layouts.app')

@section('title', 'Dashboard Mahasiswa')
@section('heading', 'Dashboard Mahasiswa')

@section('content')
    {{-- ===== PANEL SAMBUTAN (HERO) ===== --}}
    <div class="hero-panel mb-4 animate-fade-up">
        <div class="d-flex flex-wrap align-items-center gap-4">
            {{-- Avatar inisial nama --}}
            <div class="hero-avatar">{{ substr($mahasiswa->nama, 0, 1) }}</div>
            <div class="flex-grow-1" style="min-width:200px;">
                <div class="small" style="color:rgba(255,255,255,0.7);">Selamat Datang,</div>
                <h3 class="fw-bold mb-1">{{ $mahasiswa->nama }}</h3>
                <div class="d-flex flex-wrap gap-3 mt-1">
                    <span style="color:rgba(255,255,255,0.85);"><i class="bi bi-person-vcard me-1"></i>{{ $mahasiswa->npm }}</span>
                    <span style="color:rgba(255,255,255,0.85);"><i class="bi bi-mortarboard me-1"></i>{{ $mahasiswa->prodi }} — Angkatan {{ $mahasiswa->angkatan }}</span>
                </div>
            </div>
            {{-- Tombol cepat: langsung mengisi catatan perwalian --}}
            <a href="{{ route('mahasiswa.perwalian.create') }}" class="btn btn-success btn-lg px-4 fw-semibold" style="border-radius:14px;">
                <i class="bi bi-plus-circle me-1"></i> Isi Perwalian
            </a>
        </div>
    </div>

    {{-- ===== KARTU STATISTIK ===== --}}
    <div class="row g-3 mb-4">
        <div class="col-sm-6 col-xl-4">
            <div class="stat-card p-3 animate-fade-up delay-1">
                <div class="d-flex align-items-center gap-3">
                    <div class="stat-icon sky"><i class="bi bi-journal-check"></i></div>
                    <div>
                        <div class="stat-label">Total Catatan Perwalian</div>
                        <div class="stat-value">{{ $totalPerwalian }}</div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-sm-6 col-xl-4">
            <div class="stat-card p-3 animate-fade-up delay-2">
                <div class="d-flex align-items-center gap-3">
                    <div class="stat-icon emerald"><i class="bi bi-calendar-check"></i></div>
                    <div>
                        <div class="stat-label">Perwalian Terakhir</div>
                        {{-- Jika belum ada catatan, tampilkan teks "Belum ada" --}}
                        <div class="stat-value" style="font-size:1.05rem;">
                            {{ $perwalianTerakhir ? $perwalianTerakhir->tanggal->translatedFormat('d M Y') : 'Belum ada' }}
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-sm-6 col-xl-4">
            <div class="stat-card p-3 animate-fade-up delay-3">
                <div class="d-flex align-items-center gap-3">
                    <div class="stat-icon navy"><i class="bi bi-person-lines-fill"></i></div>
                    <div>
                        <div class="stat-label">Dosen Wali</div>
                        <div class="stat-value" style="font-size:1rem;">
                            @if ($mahasiswa->dosenWali?->dosen)
                                {{ $mahasiswa->dosenWali->dosen->nama }}
                            @else
                                <span class="badge badge-soft-warning">Belum ditentukan</span>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row g-3">
        {{-- ===== DETAIL PERWALIAN TERAKHIR ===== --}}
        <div class="col-lg-5">
            <div class="card h-100 animate-fade-up delay-1">
                <div class="card-header d-flex align-items-center justify-content-between">
                    <span><i class="bi bi-clock-history me-2 text-primary"></i>Perwalian Terakhir</span>
                    @if ($perwalianTerakhir)
                        <span class="badge badge-soft-primary">Semester {{ $perwalianTerakhir->semester }}</span>
                    @endif
                </div>
                <div class="card-body">
                    @if ($perwalianTerakhir)
                        {{-- Tanggal lengkap: contoh "Jumat, 14 Agustus 2026" (translatedFormat = Bahasa Indonesia) --}}
                        <p class="text-muted small mb-2">
                            <i class="bi bi-calendar3 me-1"></i>{{ $perwalianTerakhir->tanggal->translatedFormat('l, d F Y') }}
                        </p>
                        <div class="soft-panel p-3 mb-2">
                            <div class="text-muted small fw-semibold mb-1">Hasil Diskusi</div>
                            <div style="font-size:0.92rem;">{{ $perwalianTerakhir->hasil_perwalian }}</div>
                        </div>
                        {{-- Kendala & rencana perbaikan hanya tampil jika diisi --}}
                        @if ($perwalianTerakhir->kendala)
                            <div class="soft-panel p-3 mb-2">
                                <div class="text-muted small fw-semibold mb-1">Kendala</div>
                                <div style="font-size:0.92rem;">{{ $perwalianTerakhir->kendala }}</div>
                            </div>
                        @endif
                        @if ($perwalianTerakhir->rencana_perbaikan)
                            <div class="soft-panel p-3">
                                <div class="text-muted small fw-semibold mb-1">Rencana Perbaikan</div>
                                <div style="font-size:0.92rem;">{{ $perwalianTerakhir->rencana_perbaikan }}</div>
                            </div>
                        @endif
                    @else
                        {{-- State kosong + ajakan mengisi --}}
                        <div class="empty-state">
                            <i class="bi bi-journal-plus"></i>
                            <p class="text-muted mb-3">Anda belum memiliki catatan perwalian.</p>
                            <a href="{{ route('mahasiswa.perwalian.create') }}" class="btn btn-primary">
                                <i class="bi bi-plus-lg me-1"></i> Isi Perwalian Sekarang
                            </a>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        {{-- ===== TABEL RIWAYAT TERBARU (5 catatan) ===== --}}
        <div class="col-lg-7">
            <div class="card h-100 animate-fade-up delay-2">
                <div class="card-header d-flex align-items-center justify-content-between">
                    <span><i class="bi bi-journal-text me-2 text-primary"></i>Riwayat Terbaru</span>
                    <a href="{{ route('mahasiswa.perwalian.index') }}" class="btn btn-sm btn-outline-primary">
                        Semua Riwayat <i class="bi bi-arrow-right ms-1"></i>
                    </a>
                </div>
                <div class="card-body p-0">
                    @if ($riwayat->isEmpty())
                        <div class="empty-state">
                            <i class="bi bi-inbox"></i>
                            <p class="text-muted mb-0">Belum ada riwayat perwalian.</p>
                        </div>
                    @else
                        <div class="table-responsive">
                            <table class="table table-hover align-middle mb-0">
                                <thead>
                                    <tr>
                                        <th>Tanggal</th>
                                        <th>Semester</th>
                                        <th>Hasil Diskusi</th>
                                        <th class="text-end">Status</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($riwayat as $p)
                                        <tr>
                                            <td class="text-nowrap">{{ $p->tanggal->translatedFormat('d M Y') }}</td>
                                            <td><span class="badge badge-soft-primary">Semester {{ $p->semester }}</span></td>
                                            <td style="max-width:300px;">{{ Str::limit($p->hasil_perwalian, 70) }}</td>
                                            <td class="text-end"><span class="badge badge-soft-success">Selesai</span></td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
@endsection
