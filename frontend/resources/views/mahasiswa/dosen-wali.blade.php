@extends('layouts.app')

@section('title', 'Dosen Wali')
@section('heading', 'Dosen Wali Saya')

@section('content')
    <div class="card animate-fade-up" style="max-width:640px;">
        <div class="card-header"><i class="bi bi-person-lines-fill me-2 text-primary"></i>Dosen Wali Saya</div>
        <div class="card-body">
            {{-- Jika dosen wali sudah ditentukan (dikirim dari controller) --}}
            @if ($dosenWali)
                <div class="d-flex align-items-center gap-3 mb-3">
                    <div class="stat-icon navy" style="width:64px;height:64px;font-size:1.6rem;"><i class="bi bi-person-badge-fill"></i></div>
                    <div>
                        <div class="text-muted small">Dosen Wali Anda</div>
                        <h5 class="mb-0 fw-bold">{{ $dosenWali->nama }}</h5>
                        <small class="text-muted">NIDN: {{ $dosenWali->nidn }}</small>
                    </div>
                </div>

                <div class="divider my-3"></div>

                {{-- Informasi cara menggunakan fitur perwalian --}}
                <div class="soft-panel p-3">
                    <p class="text-muted small mb-0">
                        <i class="bi bi-info-circle me-1"></i>
                        Anda dapat melakukan bimbingan perwalian bersama dosen wali di atas, kemudian
                        mencatat hasil perwalian pada menu <b>Isi Perwalian</b>.
                    </p>
                </div>

                {{-- Tombol cepat mengisi perwalian --}}
                <a href="{{ route('mahasiswa.perwalian.create') }}" class="btn btn-success mt-3">
                    <i class="bi bi-plus-circle me-1"></i> Isi Perwalian Sekarang
                </a>
            @else
                {{-- State kosong: belum ada dosen wali --}}
                <div class="empty-state">
                    <i class="bi bi-person-x"></i>
                    <h6 class="mt-2 fw-semibold">Dosen wali belum ditentukan</h6>
                    <p class="text-muted">Silakan hubungi admin untuk menetapkan dosen wali Anda.</p>
                </div>
            @endif
        </div>
    </div>
@endsection
