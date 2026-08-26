@extends('layouts.app')

@section('title', 'Data Dosen')
@section('heading', 'Data Dosen')

@section('content')
    {{-- ===== BARIS PENCARIAN + TOMBOL TAMBAH ===== --}}
    <div class="d-flex justify-content-between align-items-center mb-3 flex-wrap gap-2 animate-fade-up">
        {{-- Form pencarian: method GET, jadi kata kunci muncul di URL (?search=...) --}}
        <form method="GET" action="{{ route('admin.dosen.index') }}" class="d-flex gap-2 flex-wrap">
            <div class="input-group" style="max-width:320px;">
                <span class="input-group-text"><i class="bi bi-search"></i></span>
                {{-- request('search') = ambil nilai 'search' dari URL agar kotak terisi ulang --}}
                <input type="text" name="search" value="{{ request('search') }}" class="form-control" placeholder="Cari NIDN / nama">
                {{-- Jika ada kata kunci, tampilkan tombol reset (x) --}}
                @if (request('search'))
                    <a href="{{ route('admin.dosen.index') }}" class="btn btn-outline-secondary" title="Reset"><i class="bi bi-x-lg"></i></a>
                @endif
            </div>
            <button class="btn btn-primary" type="submit">Cari</button>
        </form>
        <div class="d-flex gap-2">
            <a href="{{ route('admin.dosen.export', request()->query()) }}" class="btn btn-outline-secondary">
                <i class="bi bi-file-earmark-spreadsheet me-1"></i> Export Excel
            </a>
            <a href="{{ route('admin.dosen.create') }}" class="btn btn-success">
                <i class="bi bi-plus-lg me-1"></i> Tambah Dosen
            </a>
        </div>
    </div>

    {{-- ===== TABEL DAFTAR DOSEN ===== --}}
    <div class="card animate-fade-up delay-1">
        <div class="card-header d-flex align-items-center justify-content-between flex-wrap gap-2">
            <span><i class="bi bi-person-badge me-2 text-primary"></i>Daftar Dosen</span>
            {{-- total() = jumlah seluruh data (bukan hanya halaman ini) --}}
            <span class="badge badge-soft-primary">{{ $dosen->total() }} data</span>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead>
                        <tr>
                            <th>NIDN</th>
                            <th>Nama Dosen</th>
                            <th class="text-center">Jumlah Bimbingan</th>
                            <th class="text-end">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        {{-- forelse = foreach dengan fallback @empty jika data kosong --}}
                        @forelse ($dosen as $d)
                            <tr>
                                <td class="fw-semibold">{{ $d->nidn }}</td>
                                <td>{{ $d->nama }}</td>
                                <td class="text-center">
                                    {{-- Badge jumlah bimbingan (dari withCount di controller). --}}
                                    <a href="{{ route('admin.dosen.show', $d->id) }}" class="badge badge-soft-primary text-decoration-none">
                                        {{ $d->mahasiswa_bimbingan_count }} mahasiswa <i class="bi bi-eye ms-1"></i>
                                    </a>
                                </td>
                                <td class="text-end text-nowrap">
                                    {{-- Tombol aksi: lihat bimbingan, edit, hapus --}}
                                    <a href="{{ route('admin.dosen.show', $d->id) }}" class="btn btn-sm btn-icon btn-outline-primary" title="Lihat Bimbingan"><i class="bi bi-people"></i></a>
                                    <a href="{{ route('admin.dosen.edit', $d->id) }}" class="btn btn-sm btn-icon btn-outline-secondary" title="Edit"><i class="bi bi-pencil"></i></a>
                                    {{-- Form hapus: pakai POST + @method('DELETE') karena HTML tidak punya method DELETE.
                                         data-confirm dipicu oleh JavaScript confirmasi di layouts/app. --}}
                                    <form action="{{ route('admin.dosen.destroy', $d->id) }}" method="POST" class="d-inline">
                                        @csrf
                                        @method('DELETE')
                                        <button class="btn btn-sm btn-icon btn-outline-danger" title="Hapus" data-confirm="Hapus dosen {{ $d->nama }}?"><i class="bi bi-trash"></i></button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            {{-- Jika tidak ada data sama sekali --}}
                            <tr>
                                <td colspan="4" class="text-center text-muted py-4">
                                    <div class="empty-state">
                                        <i class="bi bi-inbox"></i>
                                        <p class="mb-0">Tidak ada data dosen.</p>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        {{-- ===== NAVIGASI PAGINASI (hanya muncul jika data > 1 halaman) ===== --}}
        @if ($dosen->hasPages())
            <div class="card-footer d-flex justify-content-between align-items-center py-2 flex-wrap gap-2">
                <span class="text-muted small">Menampilkan {{ $dosen->firstItem() }}–{{ $dosen->lastItem() }} dari {{ $dosen->total() }} data</span>
                <div class="d-flex gap-2">
                    {{-- Tombol "Sebelumnya": nonaktif jika sudah di halaman pertama --}}
                    @if ($dosen->onFirstPage())
                        <button type="button" class="btn btn-sm btn-outline-primary disabled" disabled><i class="bi bi-chevron-left me-1"></i> Sebelumnya</button>
                    @else
                        <a href="{{ $dosen->previousPageUrl() }}" class="btn btn-sm btn-outline-primary"><i class="bi bi-chevron-left me-1"></i> Sebelumnya</a>
                    @endif
                    {{-- Tombol "Berikutnya": nonaktif jika sudah di halaman terakhir --}}
                    @if ($dosen->hasMorePages())
                        <a href="{{ $dosen->nextPageUrl() }}" class="btn btn-sm btn-outline-primary">Berikutnya <i class="bi bi-chevron-right ms-1"></i></a>
                    @else
                        <button type="button" class="btn btn-sm btn-outline-primary disabled" disabled>Berikutnya <i class="bi bi-chevron-right ms-1"></i></button>
                    @endif
                </div>
            </div>
        @endif
    </div>
@endsection
