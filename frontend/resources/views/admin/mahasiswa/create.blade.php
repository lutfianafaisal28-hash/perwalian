@extends('layouts.app')

@section('title', 'Tambah Mahasiswa')
@section('heading', 'Tambah Mahasiswa')

@section('content')
    <div class="card animate-fade-up" style="max-width:720px;">
        <div class="card-header"><i class="bi bi-person-plus me-2 text-primary"></i>Form Tambah Mahasiswa</div>
        <div class="card-body">
            {{-- Form dikirim ke admin.mahasiswa.store via POST (+ @csrf). --}}
            <form method="POST" action="{{ route('admin.mahasiswa.store') }}">
                @csrf
                <div class="row g-3">
                    {{-- NPM = sekaligus username login mahasiswa --}}
                    <div class="col-md-6">
                        <label class="form-label">NPM <span class="text-danger">*</span></label>
                        <input type="text" name="npm" value="{{ old('npm') }}" class="form-control @error('npm') is-invalid @enderror" placeholder="Contoh: 20221001" required>
                        @error('npm')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Angkatan <span class="text-danger">*</span></label>
                        <input type="text" name="angkatan" value="{{ old('angkatan') }}" class="form-control @error('angkatan') is-invalid @enderror" placeholder="Contoh: 2022" required>
                        @error('angkatan')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="col-12">
                        <label class="form-label">Nama Lengkap <span class="text-danger">*</span></label>
                        <input type="text" name="nama" value="{{ old('nama') }}" class="form-control @error('nama') is-invalid @enderror" required>
                        @error('nama')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="col-12">
                        <label class="form-label">Program Studi <span class="text-danger">*</span></label>
                        {{-- Dropdown prodi dengan daftar tetap; @selected = pilih yang lama jika ada error --}}
                        <select name="prodi" class="form-select @error('prodi') is-invalid @enderror" required>
                            <option value="">-- Pilih Prodi --</option>
                            @foreach (['Teknik Informatika', 'Sistem Informasi', 'Manajemen Informatika', 'Teknik Komputer'] as $prodi)
                                <option value="{{ $prodi }}" @selected(old('prodi') === $prodi)>{{ $prodi }}</option>
                            @endforeach
                        </select>
                        @error('prodi')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Email (opsional)</label>
                        <input type="email" name="email" value="{{ old('email') }}" class="form-control @error('email') is-invalid @enderror">
                        @error('email')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Password Login (opsional)</label>
                        {{-- Jika dikosongkan, controller memakai default '123456' --}}
                        <input type="text" name="password" value="{{ old('password') }}" class="form-control" placeholder="Default: 123456">
                        <div class="form-text">Kosongkan untuk memakai password default <b>123456</b>.</div>
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
