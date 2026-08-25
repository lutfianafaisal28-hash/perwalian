@extends('layouts.app')

@section('title', 'Edit Dosen')
@section('heading', 'Edit Dosen')

@section('content')
    <div class="card animate-fade-up" style="max-width:640px;">
        <div class="card-header"><i class="bi bi-pencil-square me-2 text-primary"></i>Form Edit Dosen</div>
        <div class="card-body">
            {{-- Form ke admin.dosen.update. @method('PUT') = mengirim method PUT
                 lewat POST (karena HTML hanya mendukung GET/POST). --}}
            <form method="POST" action="{{ route('admin.dosen.update', $dosen->id) }}">
                @csrf
                @method('PUT')
                <div class="mb-3">
                    <label class="form-label">NIDN <span class="text-danger">*</span></label>
                    {{-- old('nidn', $dosen->nidn): isi lama dari form jika ada error,
                         kalau tidak pakai nilai yang tersimpan di database. --}}
                    <input type="text" name="nidn" value="{{ old('nidn', $dosen->nidn) }}" class="form-control @error('nidn') is-invalid @enderror" required>
                    @error('nidn')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <div class="mb-3">
                    <label class="form-label">Nama Lengkap <span class="text-danger">*</span></label>
                    <input type="text" name="nama" value="{{ old('nama', $dosen->nama) }}" class="form-control @error('nama') is-invalid @enderror" required>
                    @error('nama')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <div class="mb-3">
                    <label class="form-label">Email (opsional)</label>
                    {{-- Email milik akun login (user) yang terhubung ke dosen ini --}}
                    <input type="email" name="email" value="{{ old('email', $dosen->user?->email) }}" class="form-control @error('email') is-invalid @enderror">
                    @error('email')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <div class="mb-3">
                    <label class="form-label">Password Baru (opsional)</label>
                    {{-- Jika dikosongkan, controller mempertahankan password lama --}}
                    <input type="text" name="password" class="form-control" placeholder="Kosongkan jika tidak diganti">
                </div>

                <div class="d-flex gap-2">
                    <button type="submit" class="btn btn-success"><i class="bi bi-check-lg me-1"></i> Simpan</button>
                    <a href="{{ route('admin.dosen.index') }}" class="btn btn-outline-secondary">Batal</a>
                </div>
            </form>
        </div>
    </div>
@endsection
