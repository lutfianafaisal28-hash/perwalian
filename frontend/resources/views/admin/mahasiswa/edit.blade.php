@extends('layouts.app')

@section('title', 'Edit Mahasiswa')
@section('heading', 'Edit Mahasiswa')

@section('content')
    <div class="card animate-fade-up" style="max-width:720px;">
        <div class="card-header"><i class="bi bi-pencil-square me-2 text-primary"></i>Form Edit Mahasiswa</div>
        <div class="card-body">
            {{-- Form ke admin.mahasiswa.update dengan method PUT --}}
            <form method="POST" action="{{ route('admin.mahasiswa.update', $mahasiswa->id) }}">
                @csrf
                @method('PUT')
                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label">NPM <span class="text-danger">*</span></label>
                        {{-- old('npm', $mahasiswa->npm): pakai nilai lama saat ada error, kalau tidak isi dari database --}}
                        <input type="text" name="npm" value="{{ old('npm', $mahasiswa->npm) }}" class="form-control @error('npm') is-invalid @enderror" required>
                        @error('npm')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Angkatan <span class="text-danger">*</span></label>
                        <input type="text" name="angkatan" value="{{ old('angkatan', $mahasiswa->angkatan) }}" class="form-control @error('angkatan') is-invalid @enderror" required>
                        @error('angkatan')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="col-12">
                        <label class="form-label">Nama Lengkap <span class="text-danger">*</span></label>
                        <input type="text" name="nama" value="{{ old('nama', $mahasiswa->nama) }}" class="form-control @error('nama') is-invalid @enderror" required>
                        @error('nama')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="col-12">
                        <label class="form-label">Program Studi <span class="text-danger">*</span></label>
                        <select name="prodi" class="form-select @error('prodi') is-invalid @enderror" required>
                            @foreach (['Teknik Informatika', 'Sistem Informasi', 'Manajemen Informatika', 'Teknik Komputer'] as $prodi)
                                <option value="{{ $prodi }}" @selected(old('prodi', $mahasiswa->prodi) === $prodi)>{{ $prodi }}</option>
                            @endforeach
                        </select>
                        @error('prodi')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Email (opsional)</label>
                        {{-- Email akun login (user) yang terhubung --}}
                        <input type="email" name="email" value="{{ old('email', $mahasiswa->user?->email) }}" class="form-control @error('email') is-invalid @enderror">
                        @error('email')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Password Baru (opsional)</label>
                        {{-- Kosongkan jika tidak ingin mengubah password --}}
                        <input type="text" name="password" class="form-control" placeholder="Kosongkan jika tidak diganti">
                    </div>
                </div>

                <div class="d-flex gap-2 mt-4">
                    <button type="submit" class="btn btn-success"><i class="bi bi-check-lg me-1"></i> Simpan</button>
                    <a href="{{ route('admin.mahasiswa.index') }}" class="btn btn-outline-secondary">Batal</a>
                </div>
            </form>
        </div>
    </div>
@endsection
