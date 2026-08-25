@extends('layouts.app')

@section('title', 'Tambah Dosen')
@section('heading', 'Tambah Dosen')

@section('content')
    <div class="card animate-fade-up" style="max-width:640px;">
        <div class="card-header"><i class="bi bi-person-plus me-2 text-primary"></i>Form Tambah Dosen</div>
        <div class="card-body">
            {{-- Form dikirim ke admin.dosen.store dengan method POST.
                 @csrf = token keamanan anti serangan CSRF (wajib untuk POST). --}}
            <form method="POST" action="{{ route('admin.dosen.store') }}">
                @csrf
                <div class="mb-3">
                    <label class="form-label">NIDN <span class="text-danger">*</span></label>
                    {{-- old('nidn') = isi ulang nilai jika validasi gagal.
                         @error + is-invalid = tampilkan border merah bila ada error. --}}
                    <input type="text" name="nidn" value="{{ old('nidn') }}" class="form-control @error('nidn') is-invalid @enderror" placeholder="Contoh: 0416078001" required>
                    @error('nidn')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <div class="mb-3">
                    <label class="form-label">Nama Lengkap <span class="text-danger">*</span></label>
                    <input type="text" name="nama" value="{{ old('nama') }}" class="form-control @error('nama') is-invalid @enderror" required>
                    @error('nama')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <div class="mb-3">
                    <label class="form-label">Email (opsional)</label>
                    <input type="email" name="email" value="{{ old('email') }}" class="form-control @error('email') is-invalid @enderror">
                    @error('email')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <div class="mb-3">
                    <label class="form-label">Password Login (opsional)</label>
                    {{-- Input password default: jika dikosongkan, controller memakai '123456' --}}
                    <input type="text" name="password" value="{{ old('password') }}" class="form-control" placeholder="Default: 123456">
                    <div class="form-text">Kosongkan untuk memakai password default <b>123456</b>.</div>
                </div>

                <div class="d-flex gap-2">
                    <button type="submit" class="btn btn-success"><i class="bi bi-check-lg me-1"></i> Simpan</button>
                    <a href="{{ route('admin.dosen.index') }}" class="btn btn-outline-secondary">Batal</a>
                </div>
            </form>
        </div>
    </div>
@endsection
