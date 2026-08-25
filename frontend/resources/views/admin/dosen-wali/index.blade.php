@extends('layouts.app')

@section('title', 'Penentuan Dosen Wali')
@section('heading', 'Penentuan Dosen Wali')

@section('content')
    {{-- Info singkat penggunaan halaman --}}
    <div class="alert alert-info d-flex align-items-center gap-2 animate-fade-up" role="alert">
        <i class="bi bi-info-circle-fill"></i>
        <span>Tetapkan dosen wali untuk setiap mahasiswa. Satu mahasiswa hanya memiliki satu dosen wali.</span>
    </div>

    {{-- Form pencarian mahasiswa --}}
    <form method="GET" action="{{ route('admin.dosen-wali.index') }}" class="mb-3 d-flex gap-2 flex-wrap animate-fade-up delay-1">
        <div class="input-group" style="max-width:340px;">
            <span class="input-group-text"><i class="bi bi-search"></i></span>
            <input type="text" name="search" value="{{ request('search') }}" class="form-control" placeholder="Cari NPM / nama / angkatan">
            @if (request('search'))
                <a href="{{ route('admin.dosen-wali.index') }}" class="btn btn-outline-secondary" title="Reset"><i class="bi bi-x-lg"></i></a>
            @endif
        </div>
        <button class="btn btn-primary" type="submit"><i class="bi bi-search me-1"></i> Cari</button>
    </form>

    {{-- ===== TABEL PENETAPAN WALI ===== --}}
    <div class="card animate-fade-up delay-1">
        <div class="card-header d-flex align-items-center justify-content-between flex-wrap gap-2">
            <span><i class="bi bi-person-lines-fill me-2 text-primary"></i>Daftar Mahasiswa</span>
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
                            <th style="min-width:360px;">Dosen Wali</th>
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
                                    {{-- Form per baris: pilih dosen wali lalu simpan.
                                         @method('PUT') + @csrf seperti biasa untuk form update. --}}
                                    <form method="POST" action="{{ route('admin.dosen-wali.update', $m->id) }}" class="d-flex gap-2">
                                        @csrf
                                        @method('PUT')
                                        <select name="dosen_id" class="form-select form-select-sm @error('dosen_id') is-invalid @enderror" required>
                                            <option value="">-- Pilih Dosen Wali --</option>
                                            @foreach ($dosen as $d)
                                                {{-- @selected = tandai option yang sedang menjadi wali mahasiswa ini --}}
                                                <option value="{{ $d->id }}" @selected($m->dosenWali?->dosen_id === $d->id)>{{ $d->nama }} ({{ $d->nidn }})</option>
                                            @endforeach
                                        </select>
                                        <button class="btn btn-sm btn-success text-nowrap"><i class="bi bi-check-lg me-1"></i> Simpan</button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="text-center text-muted py-4">
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
        {{-- Paginasi (sama seperti halaman lain) --}}
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
