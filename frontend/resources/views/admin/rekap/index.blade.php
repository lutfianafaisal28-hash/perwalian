@extends('layouts.app')

@section('title', 'Rekap Perwalian')
@section('heading', 'Rekap Perwalian')

@section('content')
    {{-- ===== PANEL FILTER ===== --}}
    {{-- Semua filter dikirim lewat GET sehingga URL berisi query string,
         dan hasil filter bisa dibagikan / di-refresh. Controller membaca
         request('search'), request('angkatan'), dst. --}}
    <div class="card mb-3 animate-fade-up">
        <div class="card-header"><i class="bi bi-funnel me-2 text-primary"></i>Filter Rekap</div>
        <div class="card-body py-3">
            <form method="GET" action="{{ route('admin.rekap.index') }}" class="row g-2 align-items-end">
                <div class="col-md-3">
                    <label class="form-label small mb-1">Cari NPM / Nama</label>
                    <input type="text" name="search" value="{{ request('search') }}" class="form-control form-control-sm" placeholder="Ketik NPM / nama...">
                </div>
                <div class="col-md-2">
                    <label class="form-label small mb-1">Angkatan</label>
                    <select name="angkatan" class="form-select form-select-sm">
                        <option value="">Semua</option>
                        {{-- Dropdown diisi dari $angkatanList (angkatan unik di database) --}}
                        @foreach ($angkatanList as $a)
                            <option value="{{ $a }}" @selected(request('angkatan') === $a)>{{ $a }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2">
                    <label class="form-label small mb-1">Dosen Wali</label>
                    <select name="dosen_id" class="form-select form-select-sm">
                        <option value="">Semua</option>
                        @foreach ($dosenList as $d)
                            {{-- @selected membandingkan nilai URL dengan id dosen (dibuat string agar tipe sama) --}}
                            <option value="{{ $d->id }}" @selected((string) request('dosen_id') === (string) $d->id)>{{ $d->nama }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2">
                    <label class="form-label small mb-1">Dari Tanggal</label>
                    <input type="date" name="dari" value="{{ request('dari') }}" class="form-control form-control-sm">
                </div>
                <div class="col-md-2">
                    <label class="form-label small mb-1">Sampai Tanggal</label>
                    <input type="date" name="sampai" value="{{ request('sampai') }}" class="form-control form-control-sm">
                </div>
                <div class="col-md-1 d-grid">
                    <button class="btn btn-primary btn-sm"><i class="bi bi-funnel me-1"></i> Filter</button>
                </div>
            </form>
        </div>
    </div>

    {{-- Baris info jumlah + tombol export/reset --}}
    <div class="d-flex justify-content-between align-items-center mb-2 flex-wrap gap-2 animate-fade-up delay-1">
        <span class="text-muted small">Menampilkan <b>{{ $perwalian->total() }}</b> catatan perwalian</span>
        <div class="d-flex gap-2">
            {{-- Export CSV mengikuti filter aktif (request()->query()) --}}
            <a href="{{ route('admin.rekap.export', request()->query()) }}" class="btn btn-sm btn-outline-secondary">
                <i class="bi bi-download me-1"></i> Export CSV
            </a>
            <a href="{{ route('admin.rekap.index') }}" class="btn btn-sm btn-outline-secondary">
                <i class="bi bi-arrow-clockwise me-1"></i> Reset
            </a>
        </div>
    </div>

    {{-- ===== TABEL REKAP PERWALIAN ===== --}}
    <div class="card animate-fade-up delay-1">
        <div class="card-header d-flex align-items-center justify-content-between flex-wrap gap-2">
            <span><i class="bi bi-clipboard-data me-2 text-primary"></i>Rekap Perwalian</span>
            <span class="badge badge-soft-primary">{{ $perwalian->total() }} catatan</span>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead>
                        <tr>
                            <th>Tanggal</th>
                            <th>NPM</th>
                            <th>Mahasiswa</th>
                            <th>Semester</th>
                            <th>Dosen Wali</th>
                            <th>Hasil Diskusi</th>
                            <th>Kendala</th>
                            <th class="text-end">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($perwalian as $p)
                            <tr>
                                <td class="text-nowrap">{{ $p->tanggal->translatedFormat('d M Y') }}</td>
                                <td class="fw-semibold">{{ $p->mahasiswa?->npm }}</td>
                                <td>{{ $p->mahasiswa?->nama }}</td>
                                <td><span class="badge badge-soft-primary">Semester {{ $p->semester }}</span></td>
                                {{-- Rantai relasi: perwalian -> mahasiswa -> dosenWali -> dosen --}}
                                <td>{{ $p->mahasiswa?->dosenWali?->dosen?->nama ?? '-' }}</td>
                                {{-- Str::limit = potong teks panjang agar tabel tetap rapi --}}
                                <td style="max-width:220px;">{{ Str::limit($p->hasil_perwalian, 60) }}</td>
                                <td style="max-width:160px;">{{ Str::limit($p->kendala ?? '-', 40) }}</td>
                                <td class="text-end text-nowrap">
                                    {{-- Hapus satu catatan (dengan konfirmasi JS) --}}
                                    <form action="{{ route('admin.rekap.destroy', $p->id) }}" method="POST" class="d-inline">
                                        @csrf
                                        @method('DELETE')
                                        <button class="btn btn-sm btn-icon btn-outline-danger" title="Hapus" data-confirm="Hapus catatan perwalian {{ $p->mahasiswa?->nama }} ({{ $p->tanggal?->translatedFormat('d M Y') }})?"><i class="bi bi-trash"></i></button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="text-center text-muted py-4">
                                    <div class="empty-state">
                                        <i class="bi bi-inbox"></i>
                                        <p class="mb-0">Tidak ada data perwalian.</p>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        {{-- Paginasi --}}
        @if ($perwalian->hasPages())
            <div class="card-footer d-flex justify-content-between align-items-center py-2 flex-wrap gap-2">
                <span class="text-muted small">Menampilkan {{ $perwalian->firstItem() }}–{{ $perwalian->lastItem() }} dari {{ $perwalian->total() }} catatan</span>
                <div class="d-flex gap-2">
                    @if ($perwalian->onFirstPage())
                        <button type="button" class="btn btn-sm btn-outline-primary disabled" disabled><i class="bi bi-chevron-left me-1"></i> Sebelumnya</button>
                    @else
                        <a href="{{ $perwalian->previousPageUrl() }}" class="btn btn-sm btn-outline-primary"><i class="bi bi-chevron-left me-1"></i> Sebelumnya</a>
                    @endif
                    @if ($perwalian->hasMorePages())
                        <a href="{{ $perwalian->nextPageUrl() }}" class="btn btn-sm btn-outline-primary">Berikutnya <i class="bi bi-chevron-right ms-1"></i></a>
                    @else
                        <button type="button" class="btn btn-sm btn-outline-primary disabled" disabled>Berikutnya <i class="bi bi-chevron-right ms-1"></i></button>
                    @endif
                </div>
            </div>
        @endif
    </div>
@endsection
