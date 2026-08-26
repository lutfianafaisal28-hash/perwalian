@extends('layouts.app')

@section('title', 'Data Mahasiswa')
@section('heading', 'Data Mahasiswa')

@section('content')
    {{-- ===== BARIS PENCARIAN + AKSI ===== --}}
    <div class="d-flex justify-content-between align-items-center mb-3 flex-wrap gap-2 animate-fade-up">
        {{-- Form pencarian: GET, kata kunci muncul di URL --}}
        <form method="GET" action="{{ route('admin.mahasiswa.index') }}" class="d-flex gap-2 flex-wrap">
            <div class="input-group" style="max-width:340px;">
                <span class="input-group-text"><i class="bi bi-search"></i></span>
                <input type="text" name="search" value="{{ request('search') }}" class="form-control" placeholder="Cari NPM / nama / prodi / angkatan">
                @if (request('search'))
                    <a href="{{ route('admin.mahasiswa.index') }}" class="btn btn-outline-secondary" title="Reset"><i class="bi bi-x-lg"></i></a>
                @endif
            </div>
            <button class="btn btn-primary" type="submit">Cari</button>
        </form>
        <div class="d-flex gap-2">
            {{-- Export CSV: request()->query() = ikutkan filter pencarian yang aktif
                 supaya hasil ekspor sama dengan yang tampil di layar. --}}
            <a href="{{ route('admin.mahasiswa.export', request()->query()) }}" class="btn btn-outline-secondary">
                <i class="bi bi-file-earmark-spreadsheet me-1"></i> Export Excel
            </a>
            <a href="{{ route('admin.mahasiswa.create') }}" class="btn btn-success">
                <i class="bi bi-plus-lg me-1"></i> Tambah Mahasiswa
            </a>
        </div>
    </div>

    {{-- ===== TABEL DAFTAR MAHASISWA ===== --}}
    <div class="card animate-fade-up delay-1">
        <div class="card-header d-flex align-items-center justify-content-between flex-wrap gap-2">
            <span><i class="bi bi-people me-2 text-primary"></i>Daftar Mahasiswa</span>
            <span class="badge badge-soft-primary">{{ $mahasiswa->total() }} data</span>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead>
                        <tr>
                            <th>NPM</th>
                            <th>Nama</th>
                            <th>Prodi</th>
                            <th>Angkatan</th>
                            <th>Dosen Wali</th>
                            <th class="text-end">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($mahasiswa as $m)
                            <tr>
                                <td class="fw-semibold">{{ $m->npm }}</td>
                                <td>{{ $m->nama }}</td>
                                <td>{{ $m->prodi }}</td>
                                <td><span class="badge badge-soft-secondary">{{ $m->angkatan }}</span></td>
                                <td>
                                    {{-- Tampilkan nama dosen wali, atau badge "Belum ada" --}}
                                    @if ($m->dosenWali?->dosen)
                                        {{ $m->dosenWali->dosen->nama }}
                                    @else
                                        <span class="badge badge-soft-warning">Belum ada</span>
                                    @endif
                                </td>
                                <td class="text-end text-nowrap">
                                    {{-- Tombol aksi: detail, edit, hapus (dengan konfirmasi JS) --}}
                                    <a href="{{ route('admin.mahasiswa.show', $m->id) }}" class="btn btn-sm btn-icon btn-outline-primary" title="Detail"><i class="bi bi-eye"></i></a>
                                    <a href="{{ route('admin.mahasiswa.edit', $m->id) }}" class="btn btn-sm btn-icon btn-outline-secondary" title="Edit"><i class="bi bi-pencil"></i></a>
                                    <form action="{{ route('admin.mahasiswa.destroy', $m->id) }}" method="POST" class="d-inline">
                                        @csrf
                                        @method('DELETE')
                                        <button class="btn btn-sm btn-icon btn-outline-danger" title="Hapus" data-confirm="Hapus mahasiswa {{ $m->nama }}?"><i class="bi bi-trash"></i></button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="text-center text-muted py-4">
                                    <div class="empty-state">
                                        <i class="bi bi-inbox"></i>
                                        <p class="mb-0">Tidak ada data mahasiswa.</p>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        {{-- Paginasi (pola sama seperti halaman daftar lainnya) --}}
        @if ($mahasiswa->hasPages())
            <div class="card-footer d-flex justify-content-between align-items-center py-2 flex-wrap gap-2">
                <span class="text-muted small">Menampilkan {{ $mahasiswa->firstItem() }}–{{ $mahasiswa->lastItem() }} dari {{ $mahasiswa->total() }} data</span>
                <div class="d-flex gap-2">
                    @if ($mahasiswa->onFirstPage())
                        <button type="button" class="btn btn-sm btn-outline-primary disabled" disabled><i class="bi bi-chevron-left me-1"></i> Sebelumnya</button>
                    @else
                        <a href="{{ $mahasiswa->previousPageUrl() }}" class="btn btn-sm btn-outline-primary"><i class="bi bi-chevron-left me-1"></i> Sebelumnya</a>
                    @endif
                    @if ($mahasiswa->hasMorePages())
                        <a href="{{ $mahasiswa->nextPageUrl() }}" class="btn btn-sm btn-outline-primary">Berikutnya <i class="bi bi-chevron-right ms-1"></i></a>
                    @else
                        <button type="button" class="btn btn-sm btn-outline-primary disabled" disabled>Berikutnya <i class="bi bi-chevron-right ms-1"></i></button>
                    @endif
                </div>
            </div>
        @endif
    </div>
@endsection
