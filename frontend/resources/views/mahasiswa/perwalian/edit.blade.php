@extends('layouts.app')

@section('title', 'Edit Perwalian')
@section('heading', 'Edit Catatan Perwalian')

@section('content')
    {{-- ===== INFO MAHASISWA (BAGIAN ATAS) ===== --}}
    <div class="card mb-3 animate-fade-up">
        <div class="card-body py-3">
            <div class="row text-center g-3">
                <div class="col-md-4">
                    <div class="text-muted small">Mahasiswa</div>
                    <div class="fw-semibold">{{ $mahasiswa->nama }} ({{ $mahasiswa->npm }})</div>
                </div>
                <div class="col-md-4">
                    <div class="text-muted small">Program Studi</div>
                    <div class="fw-semibold">{{ $mahasiswa->prodi }} — {{ $mahasiswa->angkatan }}</div>
                </div>
                <div class="col-md-4">
                    <div class="text-muted small">Dosen Wali</div>
                    <div class="fw-semibold">
                        @if ($mahasiswa->dosenWali?->dosen)
                            {{ $mahasiswa->dosenWali->dosen->nama }}
                        @else
                            <span class="badge badge-soft-warning">Belum ditentukan</span>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- ===== FORM EDIT CATATAN ===== --}}
    <div class="card animate-fade-up delay-1" style="max-width:820px;">
        <div class="card-header"><i class="bi bi-journal-richtext me-2 text-primary"></i>Form Edit Perwalian</div>
        <div class="card-body">
            {{-- Dikirim ke mahasiswa.perwalian.update dengan method PUT.
                 @error / old() dipakai agar error & nilai lama tetap tampil. --}}
            <form method="POST" action="{{ route('mahasiswa.perwalian.update', $perwalian->id) }}">
                @csrf
                @method('PUT')
                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label">Tanggal Perwalian <span class="text-danger">*</span></label>
                        {{-- toDateString() = format tanggal jadi YYYY-MM-DD agar cocok dengan input type="date" --}}
                        <input type="date" name="tanggal" value="{{ old('tanggal', $perwalian->tanggal->toDateString()) }}" class="form-control @error('tanggal') is-invalid @enderror" required>
                        @error('tanggal')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Semester <span class="text-danger">*</span></label>
                        <select name="semester" class="form-select @error('semester') is-invalid @enderror" required>
                            @foreach (['1', '2', '3', '4', '5', '6', '7', '8'] as $s)
                                <option value="{{ $s }}" @selected(old('semester', $perwalian->semester) === $s)>Semester {{ $s }}</option>
                            @endforeach
                        </select>
                        @error('semester')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="col-12">
                        <label class="form-label">Hasil Diskusi Perwalian <span class="text-danger">*</span></label>
                        <textarea name="hasil_perwalian" rows="4" class="form-control @error('hasil_perwalian') is-invalid @enderror" placeholder="Tuliskan hasil diskusi dengan dosen wali..." required>{{ old('hasil_perwalian', $perwalian->hasil_perwalian) }}</textarea>
                        @error('hasil_perwalian')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="col-12">
                        <label class="form-label">Kendala yang Dihadapi</label>
                        <textarea name="kendala" rows="3" class="form-control @error('kendala') is-invalid @enderror" placeholder="Kendala yang dihadapi selama perkuliahan (jika ada)...">{{ old('kendala', $perwalian->kendala) }}</textarea>
                        @error('kendala')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="col-12">
                        <label class="form-label">Rencana Perbaikan</label>
                        <textarea name="rencana_perbaikan" rows="3" class="form-control @error('rencana_perbaikan') is-invalid @enderror" placeholder="Rencana perbaikan yang akan dilakukan (jika ada)...">{{ old('rencana_perbaikan', $perwalian->rencana_perbaikan) }}</textarea>
                        @error('rencana_perbaikan')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                </div>

                <div class="d-flex gap-2 mt-4">
                    <button type="submit" class="btn btn-success"><i class="bi bi-check-lg me-1"></i> Simpan Perubahan</button>
                    <a href="{{ route('mahasiswa.perwalian.index') }}" class="btn btn-outline-secondary">Kembali</a>
                </div>
            </form>
        </div>
    </div>
@endsection
