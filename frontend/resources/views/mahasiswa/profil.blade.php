@extends('layouts.app')

@section('title', 'Profil')
@section('heading', 'Profil Saya')

@section('content')
    <div class="row g-3">
        {{-- ===== KOLOM KIRI: DATA DIRI ===== --}}
        <div class="col-lg-5">
            <div class="card h-100 animate-fade-up">
                <div class="card-header"><i class="bi bi-person-vcard me-2 text-primary"></i>Data Diri</div>
                <div class="card-body">
                    {{-- Avatar inisial + nama --}}
                    <div class="d-flex align-items-center gap-3 mb-4">
                        <div class="hero-avatar" style="width:60px;height:60px;font-size:1.5rem;border-radius:16px;">{{ substr($mahasiswa->nama, 0, 1) }}</div>
                        <div>
                            <h6 class="fw-bold mb-0">{{ $mahasiswa->nama }}</h6>
                            <small class="text-muted">{{ $mahasiswa->npm }}</small>
                        </div>
                    </div>
                    {{-- Detail identitas --}}
                    <table class="table table-sm detail-table mb-0">
                        <tr>
                            <td class="text-muted">NPM</td>
                            <td class="fw-semibold text-end">{{ $mahasiswa->npm }}</td>
                        </tr>
                        <tr>
                            <td class="text-muted">Program Studi</td>
                            <td class="text-end">{{ $mahasiswa->prodi }}</td>
                        </tr>
                        <tr>
                            <td class="text-muted">Angkatan</td>
                            <td class="text-end"><span class="badge badge-soft-secondary">{{ $mahasiswa->angkatan }}</span></td>
                        </tr>
                        <tr>
                            <td class="text-muted">Dosen Wali</td>
                            <td class="text-end">
                                @if ($mahasiswa->dosenWali?->dosen)
                                    {{ $mahasiswa->dosenWali->dosen->nama }}
                                @else
                                    <span class="badge badge-soft-warning">Belum ditentukan</span>
                                @endif
                            </td>
                        </tr>
                        {{-- auth()->user() = data akun login --}}
                        <tr>
                            <td class="text-muted">Username Login</td>
                            <td class="fw-semibold text-end">{{ auth()->user()->username }}</td>
                        </tr>
                        <tr>
                            <td class="text-muted">Email</td>
                            <td class="text-end">{{ auth()->user()->email ?? '-' }}</td>
                        </tr>
                    </table>
                </div>
            </div>
        </div>

        {{-- ===== KOLOM KANAN: GANTI PASSWORD ===== --}}
        <div class="col-lg-7">
            <div class="card animate-fade-up delay-1">
                <div class="card-header"><i class="bi bi-shield-lock me-2 text-primary"></i>Ganti Password</div>
                <div class="card-body">
                    {{-- Form ke mahasiswa.profil.password (method PUT + @csrf) --}}
                    <form method="POST" action="{{ route('mahasiswa.profil.password') }}">
                        @csrf
                        @method('PUT')
                        <div class="mb-3">
                            <label class="form-label">Password Lama</label>
                            <input type="password" name="password_lama" class="form-control @error('password_lama') is-invalid @enderror" required>
                            @error('password_lama')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Password Baru</label>
                            <input type="password" name="password_baru" class="form-control @error('password_baru') is-invalid @enderror" required>
                            @error('password_baru')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        {{-- Field konfirmasi: validasi 'confirmed' di controller akan
                             membandingkannya dengan field password_baru_confirmation --}}
                        <div class="mb-3">
                            <label class="form-label">Konfirmasi Password Baru</label>
                            <input type="password" name="password_baru_confirmation" class="form-control" required>
                        </div>
                        <button type="submit" class="btn btn-success"><i class="bi bi-shield-lock me-1"></i> Ganti Password</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection
