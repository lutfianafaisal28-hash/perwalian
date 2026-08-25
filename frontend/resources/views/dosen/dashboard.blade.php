@extends('layouts.app')

@section('title', 'Dashboard Dosen')
@section('heading', 'Dashboard Dosen')

@section('content')
    {{-- BLOK PHP: hitung berapa mahasiswa bimbingan yang BELUM pernah
         melakukan perwalian sama sekali (relasi perwalian kosong). --}}
    @php
        $belumPerwalian = $bimbingan->filter(fn ($m) => $m->perwalian->isEmpty())->count();
    @endphp

    {{-- ===== PANEL SAMBUTAN (HERO) ===== --}}
    <div class="hero-panel mb-4 animate-fade-up">
        <div class="d-flex flex-wrap align-items-center gap-4">
            <div class="hero-avatar"><i class="bi bi-person-badge-fill"></i></div>
            <div class="flex-grow-1" style="min-width:200px;">
                <div class="small" style="color:rgba(255,255,255,0.7);">Selamat Datang,</div>
                <h3 class="fw-bold mb-1">{{ $dosen->nama }}</h3>
                <div style="color:rgba(255,255,255,0.85);">
                    <i class="bi bi-person-vcard me-1"></i>NIDN {{ $dosen->nidn }}
                    <span class="mx-2" style="color:rgba(255,255,255,0.35);">|</span>
                    Dosen Wali STMIK Bandung
                </div>
            </div>
            {{-- Tombol cepat ke daftar bimbingan --}}
            <a href="{{ route('dosen.bimbingan.index') }}" class="btn btn-success btn-lg px-4 fw-semibold" style="border-radius:14px;">
                <i class="bi bi-people me-1"></i> Lihat Bimbingan
            </a>
        </div>
    </div>

    {{-- ===== KARTU STATISTIK ===== --}}
    <div class="row g-3 mb-4">
        <div class="col-sm-6 col-xl-4">
            <div class="stat-card p-3 animate-fade-up delay-1">
                <div class="d-flex align-items-center gap-3">
                    <div class="stat-icon navy"><i class="bi bi-people-fill"></i></div>
                    <div>
                        <div class="stat-label">Mahasiswa Bimbingan</div>
                        <div class="stat-value">{{ $jumlahBimbingan }}</div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-sm-6 col-xl-4">
            <div class="stat-card p-3 animate-fade-up delay-2">
                <div class="d-flex align-items-center gap-3">
                    <div class="stat-icon emerald"><i class="bi bi-journal-check"></i></div>
                    <div>
                        <div class="stat-label">Total Catatan Perwalian</div>
                        <div class="stat-value">{{ $totalCatatan }}</div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-sm-6 col-xl-4">
            <div class="stat-card p-3 animate-fade-up delay-3">
                <div class="d-flex align-items-center gap-3">
                    <div class="stat-icon amber"><i class="bi bi-hourglass-split"></i></div>
                    <div>
                        <div class="stat-label">Belum Pernah Perwalian</div>
                        <div class="stat-value">{{ $belumPerwalian }}</div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- ===== TABEL MAHASISWA BIMBINGAN ===== --}}
    <div class="card animate-fade-up delay-1">
        <div class="card-header d-flex align-items-center justify-content-between flex-wrap gap-2">
            <span><i class="bi bi-person-lines-fill me-2 text-primary"></i>Mahasiswa Bimbingan Saya</span>
            <span class="badge badge-soft-primary">{{ $jumlahBimbingan }} mahasiswa</span>
        </div>
        <div class="card-body p-0">
            @if ($bimbingan->isEmpty())
                <div class="empty-state">
                    <i class="bi bi-inbox"></i>
                    <p class="text-muted mb-0">Anda belum memiliki mahasiswa bimbingan.</p>
                </div>
            @else
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead>
                            <tr>
                                <th>NPM</th>
                                <th>Nama</th>
                                <th>Program Studi</th>
                                <th>Angkatan</th>
                                <th class="text-center">Perwalian</th>
                                <th class="text-end">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($bimbingan as $m)
                                <tr>
                                    <td class="fw-semibold">{{ $m->npm }}</td>
                                    <td>{{ $m->nama }}</td>
                                    <td>{{ $m->prodi }}</td>
                                    <td><span class="badge badge-soft-secondary">{{ $m->angkatan }}</span></td>
                                    <td class="text-center">
                                        {{-- Tampilkan jumlah catatan atau badge "Belum ada" --}}
                                        @if ($m->perwalian->count())
                                            <span class="badge badge-soft-success">{{ $m->perwalian->count() }} catatan</span>
                                        @else
                                            <span class="badge badge-soft-warning">Belum ada</span>
                                        @endif
                                    </td>
                                    <td class="text-end">
                                        {{-- Tombol lihat histori perwalian mahasiswa ini --}}
                                        <a href="{{ route('dosen.bimbingan.show', $m->id) }}" class="btn btn-sm btn-icon btn-outline-primary" title="Lihat Histori">
                                            <i class="bi bi-eye"></i>
                                        </a>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </div>
    </div>
@endsection
