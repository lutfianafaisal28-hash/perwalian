@extends('layouts.app')

@section('title', 'Detail Mahasiswa')
@section('heading', 'Detail Mahasiswa')

@section('content')
    <div class="row g-3">
        {{-- ===== KOLOM KIRI: IDENTITAS MAHASISWA ===== --}}
        <div class="col-lg-5">
            <div class="card h-100 animate-fade-up">
                <div class="card-header"><i class="bi bi-person-vcard me-2 text-primary"></i>Identitas Mahasiswa</div>
                <div class="card-body">
                    {{-- Avatar dengan inisial huruf pertama nama --}}
                    <div class="d-flex align-items-center gap-3 mb-4">
                        <div class="hero-avatar" style="width:60px;height:60px;font-size:1.5rem;border-radius:16px;">{{ substr($mahasiswa->nama, 0, 1) }}</div>
                        <div>
                            <h6 class="fw-bold mb-0">{{ $mahasiswa->nama }}</h6>
                            <small class="text-muted">{{ $mahasiswa->npm }}</small>
                        </div>
                    </div>
                    {{-- Tabel detail data mahasiswa --}}
                    <table class="table table-sm detail-table mb-0">
                        <tr>
                            <td class="text-muted">Program Studi</td>
                            <td class="fw-semibold text-end">{{ $mahasiswa->prodi }}</td>
                        </tr>
                        <tr>
                            <td class="text-muted">Angkatan</td>
                            <td class="text-end"><span class="badge badge-soft-secondary">{{ $mahasiswa->angkatan }}</span></td>
                        </tr>
                        <tr>
                            <td class="text-muted">Dosen Wali</td>
                            <td class="text-end">
                                {{-- Relasi: mahasiswa -> dosenWali (perantara) -> dosen --}}
                                @if ($mahasiswa->dosenWali?->dosen)
                                    {{ $mahasiswa->dosenWali->dosen->nama }}
                                    <small class="d-block text-muted">NIDN {{ $mahasiswa->dosenWali->dosen->nidn }}</small>
                                @else
                                    <span class="badge badge-soft-warning">Belum ditentukan</span>
                                @endif
                            </td>
                        </tr>
                        <tr>
                            <td class="text-muted">Username Login</td>
                            {{-- ?? '-' = tampilkan '-' jika user tidak ada / username kosong --}}
                            <td class="fw-semibold text-end">{{ $mahasiswa->user?->username ?? '-' }}</td>
                        </tr>
                        <tr>
                            <td class="text-muted">Email</td>
                            <td class="text-end">{{ $mahasiswa->user?->email ?? '-' }}</td>
                        </tr>
                    </table>

                    <div class="d-flex gap-2 mt-4">
                        <a href="{{ route('admin.mahasiswa.edit', $mahasiswa->id) }}" class="btn btn-sm btn-outline-secondary"><i class="bi bi-pencil me-1"></i> Edit</a>
                        <a href="{{ route('admin.mahasiswa.index') }}" class="btn btn-sm btn-outline-secondary"><i class="bi bi-arrow-left me-1"></i> Kembali</a>
                    </div>
                </div>
            </div>
        </div>

        {{-- ===== KOLOM KANAN: RIWAYAT PERWALIAN ===== --}}
        <div class="col-lg-7">
            <div class="card h-100 animate-fade-up delay-1">
                <div class="card-header d-flex align-items-center justify-content-between">
                    <span><i class="bi bi-journal-text me-2 text-primary"></i>Riwayat Perwalian</span>
                    <span class="badge badge-soft-primary">{{ $mahasiswa->perwalian->count() }} catatan</span>
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
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($mahasiswa->perwalian as $p)
                                    <tr>
                                        {{-- translatedFormat = format tanggal dengan nama bulan Bahasa Indonesia --}}
                                        <td class="text-nowrap">{{ $p->tanggal->translatedFormat('d M Y') }}</td>
                                        <td><span class="badge badge-soft-primary">Semester {{ $p->semester }}</span></td>
                                        {{-- Str::limit = potong teks panjang, beri tanda '...' --}}
                                        <td style="max-width:240px;">{{ Str::limit($p->hasil_perwalian, 70) }}</td>
                                        <td style="max-width:160px;">{{ Str::limit($p->kendala ?? '-', 40) }}</td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="4" class="text-center text-muted py-4">
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
        </div>
    </div>
@endsection
