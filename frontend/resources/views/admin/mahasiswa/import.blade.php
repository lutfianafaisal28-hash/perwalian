@extends('layouts.app')

@section('title', 'Import Mahasiswa dari Excel')
@section('heading', 'Import Mahasiswa dari Excel')

@section('content')
    <div class="row justify-content-center">
        <div class="col-lg-8">
            {{-- ===== CARD IMPORT ===== --}}
            <div class="card animate-fade-up">
                <div class="card-header">
                    <i class="bi bi-upload me-2 text-primary"></i>Import Data Mahasiswa
                </div>
                <div class="card-body">
                    {{-- Petunjuk --}}
                    <div class="alert alert-info mb-4">
                        <h6 class="alert-heading fw-bold"><i class="bi bi-info-circle me-1"></i> Petunjuk Import</h6>
                        <ol class="mb-0 small">
                            <li>Download template Excel terlebih dahulu dengan klik tombol <strong>"Download Template"</strong>.</li>
                            <li>Buka template dan isi data mahasiswa (hapus baris contoh yang ada).</li>
                            <li>Kolom yang wajib diisi: <strong>NPM, Nama Lengkap, Program Studi, Angkatan</strong>.</li>
                            <li>Simpan file, lalu upload file tersebut di form berikut.</li>
                            <li>Password default untuk semua akun yang dibuat: <code>123456</code>.</li>
                        </ol>
                    </div>

                    {{-- Download Template --}}
                    <div class="mb-4">
                        <a href="{{ route('admin.mahasiswa.import.template') }}" class="btn btn-outline-primary">
                            <i class="bi bi-file-earmark-spreadsheet me-1"></i> Download Template (100 Data Contoh)
                        </a>
                    </div>

                    <hr>

                    {{-- Form Upload --}}
                    <form action="{{ route('admin.mahasiswa.import.process') }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        <div class="mb-3">
                            <label for="file" class="form-label fw-semibold">Pilih File Excel (.xlsx / .xls)</label>
                            <input type="file" name="file" id="file" class="form-control @error('file') is-invalid @enderror"
                                   accept=".xlsx,.xls" required>
                            @error('file')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="d-flex gap-2">
                            <button type="submit" class="btn btn-primary" onclick="return confirm('Import data mahasiswa dari file ini?')">
                                <i class="bi bi-upload me-1"></i> Import Sekarang
                            </button>
                            <a href="{{ route('admin.mahasiswa.index') }}" class="btn btn-outline-secondary">
                                <i class="bi bi-arrow-left me-1"></i> Kembali
                            </a>
                        </div>
                    </form>
                </div>
            </div>

            {{-- ===== CONTOH FORMAT ===== --}}
            <div class="card animate-fade-up delay-1 mt-4">
                <div class="card-header">
                    <i class="bi bi-table me-2 text-success"></i>Contoh Format Template
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-bordered mb-0 small">
                            <thead class="table-dark">
                                <tr>
                                    <th style="width:18%">NPM</th>
                                    <th style="width:35%">Nama Lengkap</th>
                                    <th style="width:30%">Program Studi</th>
                                    <th style="width:17%">Angkatan</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr><td>2303001</td><td>Budi Santoso</td><td>Teknik Informatika</td><td>2023</td></tr>
                                <tr><td>2303002</td><td>Siti Rahayu</td><td>Sistem Informasi</td><td>2023</td></tr>
                                <tr><td>2303003</td><td>Andi Pratama</td><td>Teknik Komputer</td><td>2024</td></tr>
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="card-footer small text-muted">
                    <i class="bi bi-info-circle me-1"></i>Isi data mahasiswa Anda sesuai format di atas. Password default: <code>123456</code>
                </div>
            </div>
        </div>
    </div>
@endsection
