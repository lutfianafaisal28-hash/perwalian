@extends('layouts.app')

@section('title', 'Mahasiswa Bimbingan - '.$dosen->nama)
@section('heading', 'Mahasiswa Bimbingan')

@section('content')
    {{-- ===== KARTU INFO DOSEN ===== --}}
    <div class="card mb-3 animate-fade-up">
        <div class="card-body py-3">
            <div class="row text-center g-3">
                <div class="col-md-6">
                    <div class="text-muted small">Dosen</div>
                    <div class="fw-semibold">{{ $dosen->nama }} ({{ $dosen->nidn }})</div>
                </div>
                <div class="col-md-6">
                    <div class="text-muted small">Jumlah Bimbingan</div>
                    {{-- $mahasiswa = kumpulan (collection) dari controller --}}
                    <div class="fw-semibold">{{ $mahasiswa->count() }} mahasiswa</div>
                </div>
            </div>
        </div>
    </div>

    {{-- ===== TABEL MAHASISWA BIMBINGAN ===== --}}
    <div class="card animate-fade-up delay-1">
        <div class="card-header d-flex align-items-center justify-content-between flex-wrap gap-2">
            <span><i class="bi bi-people me-2 text-primary"></i>Daftar Mahasiswa Bimbingan</span>
            <span class="badge badge-soft-primary">{{ $mahasiswa->count() }} mahasiswa</span>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead>
                        <tr>
                            <th>NPM</th>
                            <th>Nama</th>
                            <th>Program Studi</th>
                            <th>Angkatan</th>
                            <th class="text-center">Jumlah Perwalian</th>
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
                                <td class="text-center">
                                    {{-- perwalian_count berasal dari withCount('perwalian') di controller --}}
                                    @if ($m->perwalian_count)
                                        <span class="badge badge-soft-success">{{ $m->perwalian_count }} catatan</span>
                                    @else
                                        <span class="badge badge-soft-warning">Belum ada</span>
                                    @endif
                                </td>
                                <td class="text-end text-nowrap">
                                    {{-- Tombol lihat detail mahasiswa tersebut --}}
                                    <a href="{{ route('admin.mahasiswa.show', $m->id) }}" class="btn btn-sm btn-icon btn-outline-primary" title="Detail Mahasiswa"><i class="bi bi-eye"></i></a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="text-center text-muted py-4">
                                    <div class="empty-state">
                                        <i class="bi bi-inbox"></i>
                                        <p class="mb-0">Dosen ini belum memiliki mahasiswa bimbingan.</p>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        <div class="card-footer py-2">
            <a href="{{ route('admin.dosen.index') }}" class="btn btn-sm btn-outline-secondary"><i class="bi bi-arrow-left me-1"></i> Kembali ke Data Dosen</a>
        </div>
    </div>
@endsection
