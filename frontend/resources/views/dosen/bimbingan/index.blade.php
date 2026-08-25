@extends('layouts.app')

{{-- Judul di tab browser dan di topbar --}}
@section('title', 'Mahasiswa Bimbingan')
@section('heading', 'Mahasiswa Bimbingan')

@section('content')
    {{-- KARTU FILTER: form pencarian mahasiswa bimbingan (dikirim via GET agar URL bisa di-share) --}}
    <div class="card mb-3 animate-fade-up">
        <div class="card-header"><i class="bi bi-funnel me-2 text-primary"></i>Filter Mahasiswa</div>
        <div class="card-body py-3">
            <form method="GET" action="{{ route('dosen.bimbingan.index') }}" class="row g-2 align-items-end">
                <div class="col-md-4">
                    <label class="form-label small mb-1">Cari NPM / Nama / Prodi</label>
                    {{-- request('search') = mempertahankan kata kunci yang sudah diketik setelah filter --}}
                    <input type="text" name="search" value="{{ request('search') }}" class="form-control form-control-sm" placeholder="Ketik NPM, nama, atau prodi...">
                </div>
                <div class="col-md-2">
                    <label class="form-label small mb-1">Angkatan</label>
                    <select name="angkatan" class="form-select form-select-sm">
                        <option value="">Semua</option>
                        {{-- Daftar angkatan unik diambil dari controller; @selected menandai pilihan aktif --}}
                        @foreach ($angkatanList as $a)
                            <option value="{{ $a }}" @selected(request('angkatan') === $a)>{{ $a }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2 d-grid">
                    <button class="btn btn-primary btn-sm"><i class="bi bi-funnel me-1"></i> Filter</button>
                </div>
            </form>
        </div>
    </div>

    {{-- Baris info jumlah data + tombol Export CSV & Reset --}}
    <div class="d-flex justify-content-between align-items-center mb-2 flex-wrap gap-2 animate-fade-up delay-1">
        <span class="text-muted small">Menampilkan <b>{{ $mahasiswa->total() }}</b> mahasiswa bimbingan</span>
        <div class="d-flex gap-2">
            {{-- Export CSV membawa query filter yang sama (search & angkatan) --}}
            <a href="{{ route('dosen.bimbingan.export', request()->query()) }}" class="btn btn-sm btn-outline-secondary">
                <i class="bi bi-download me-1"></i> Export CSV
            </a>
            {{-- Reset: kembali ke halaman tanpa filter --}}
            <a href="{{ route('dosen.bimbingan.index') }}" class="btn btn-sm btn-outline-secondary">
                <i class="bi bi-arrow-clockwise me-1"></i> Reset
            </a>
        </div>
    </div>

    <div class="card animate-fade-up delay-1">
        <div class="card-header d-flex align-items-center justify-content-between flex-wrap gap-2">
            <span><i class="bi bi-people me-2 text-primary"></i>Daftar Mahasiswa Bimbingan</span>
            <span class="badge badge-soft-primary">{{ $mahasiswa->total() }} mahasiswa</span>
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
                                    {{-- perwalian_count dihasilkan dengan()->withCount('perwalian') di controller --}}
                                    @if ($m->perwalian_count)
                                        <span class="badge badge-soft-success">{{ $m->perwalian_count }} catatan</span>
                                    @else
                                        <span class="badge badge-soft-warning">Belum ada</span>
                                    @endif
                                </td>
                                <td class="text-end">
                                    {{-- Tombol "Lihat Histori" menuju halaman detail perwalian mahasiswa ini --}}
                                    <a href="{{ route('dosen.bimbingan.show', $m->id) }}" class="btn btn-sm btn-outline-primary">
                                        <i class="bi bi-eye me-1"></i> Lihat Histori
                                    </a>
                                </td>
                            </tr>
                        @empty
                            {{-- Pesan jika tidak ada data hasil filter --}}
                            <tr>
                                <td colspan="6" class="text-center text-muted py-4">
                                    <div class="empty-state">
                                        <i class="bi bi-inbox"></i>
                                        <p class="mb-0">Tidak ada mahasiswa bimbingan ditemukan.</p>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        {{-- NAVIGASI HALAMAN (pagination): hanya tampil jika data lebih dari 1 halaman --}}
        @if ($mahasiswa->hasPages())
            <div class="card-footer d-flex justify-content-between align-items-center py-2 flex-wrap gap-2">
                <span class="text-muted small">Menampilkan {{ $mahasiswa->firstItem() }}–{{ $mahasiswa->lastItem() }} dari {{ $mahasiswa->total() }} mahasiswa</span>
                <div class="d-flex gap-2">
                    {{-- Tombol "Sebelumnya": aktif hanya jika bukan halaman pertama --}}
                    @if ($mahasiswa->onFirstPage())
                        <button type="button" class="btn btn-sm btn-outline-primary disabled" disabled><i class="bi bi-chevron-left me-1"></i> Sebelumnya</button>
                    @else
                        <a href="{{ $mahasiswa->previousPageUrl() }}" class="btn btn-sm btn-outline-primary"><i class="bi bi-chevron-left me-1"></i> Sebelumnya</a>
                    @endif
                    {{-- Tombol "Berikutnya": aktif hanya jika masih ada halaman selanjutnya --}}
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
