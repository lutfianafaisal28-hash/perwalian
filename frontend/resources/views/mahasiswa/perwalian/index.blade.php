@extends('layouts.app')

{{-- Judul di tab browser dan di topbar --}}
@section('title', 'Riwayat Perwalian')
@section('heading', 'Riwayat Perwalian')

@section('content')
    {{-- Tombol untuk membuka halaman isi perwalian baru --}}
    <div class="d-flex justify-content-end mb-3 animate-fade-up">
        <a href="{{ route('mahasiswa.perwalian.create') }}" class="btn btn-success">
            <i class="bi bi-plus-circle me-1"></i> Isi Perwalian Baru
        </a>
    </div>

    <div class="card animate-fade-up delay-1">
        <div class="card-header d-flex align-items-center justify-content-between flex-wrap gap-2">
            <span><i class="bi bi-journal-text me-2 text-primary"></i>Riwayat Perwalian Saya</span>
            {{-- Jumlah total catatan perwalian yang sudah diisi mahasiswa ini --}}
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
                            <th class="text-end">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        {{-- @forelse = loop data; jika kosong langsung ke @empty --}}
                        @forelse ($perwalian as $p)
                            <tr>
                                {{-- Tanggal diformat Bahasa Indonesia (contoh: 12 Ags 2026) --}}
                                <td class="text-nowrap">{{ $p->tanggal->translatedFormat('d M Y') }}</td>
                                <td><span class="badge badge-soft-primary">Semester {{ $p->semester }}</span></td>
                                {{-- ?? '-' = jika kolom kosong/null, tampilkan tanda "-" --}}
                                <td style="max-width:260px;">{{ $p->hasil_perwalian }}</td>
                                <td style="max-width:200px;">{{ $p->kendala ?? '-' }}</td>
                                <td style="max-width:200px;">{{ $p->rencana_perbaikan ?? '-' }}</td>
                                <td class="text-end text-nowrap">
                                    {{-- Tombol edit: arahkan ke halaman edit dengan id catatan ini --}}
                                    <a href="{{ route('mahasiswa.perwalian.edit', $p->id) }}" class="btn btn-sm btn-icon btn-outline-secondary" title="Edit"><i class="bi bi-pencil"></i></a>
                                </td>
                            </tr>
                        @empty
                            {{-- Jika belum ada catatan sama sekali, tampilkan pesan kosong --}}
                            <tr>
                                <td colspan="6" class="text-center text-muted py-4">
                                    <div class="empty-state">
                                        <i class="bi bi-inbox"></i>
                                        <p class="mb-0">Belum ada catatan perwalian.</p>
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
