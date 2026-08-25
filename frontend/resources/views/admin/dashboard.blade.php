@extends('layouts.app')

@section('title', 'Dashboard Admin')
@section('heading', 'Dashboard Admin')

@section('content')
    {{-- BLOK PHP: Siapkan data untuk grafik Chart.js --}}
    {{-- Controller mengirim $rekapPerBulan (per kelompok bulan). Di sini kita
         ubah jadi dua array: $labels (nama bulan) dan $values (jumlah catatan). --}}
    @php
        $labels = [];
        $values = [];
        foreach ($rekapPerBulan as $item) {
            // '2026-08' -> objek Carbon -> 'Agu 2026' (bulan dalam Bahasa Indonesia)
            $labels[] = \Carbon\Carbon::createFromFormat('Y-m', $item->bulan)->translatedFormat('M Y');
            $values[] = (int) $item->total;
        }
    @endphp

    {{-- ===== KARTU STATISTIK ===== --}}
    {{-- 4 kartu: Total Mahasiswa, Total Dosen, Catatan Perwalian, Belum Wali.
         Kelas animate-fade-up adalah animasi masuk halus (delay-1/2/3 = berurutan). --}}
    <div class="row g-3 mb-4">
        <div class="col-sm-6 col-xl-3">
            <div class="stat-card p-3 animate-fade-up">
                <div class="d-flex align-items-center gap-3">
                    <div class="stat-icon navy"><i class="bi bi-people-fill"></i></div>
                    <div class="min-w-0">
                        <div class="stat-label">Total Mahasiswa</div>
                        <div class="stat-value">{{ $totalMahasiswa }}</div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-sm-6 col-xl-3">
            <div class="stat-card p-3 animate-fade-up delay-1">
                <div class="d-flex align-items-center gap-3">
                    <div class="stat-icon emerald"><i class="bi bi-person-badge-fill"></i></div>
                    <div class="min-w-0">
                        <div class="stat-label">Total Dosen</div>
                        <div class="stat-value">{{ $totalDosen }}</div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-sm-6 col-xl-3">
            <div class="stat-card p-3 animate-fade-up delay-2">
                <div class="d-flex align-items-center gap-3">
                    <div class="stat-icon sky"><i class="bi bi-journal-check"></i></div>
                    <div class="min-w-0">
                        <div class="stat-label">Catatan Perwalian</div>
                        <div class="stat-value">{{ $totalPerwalian }}</div>
                    </div>
                </div>
            </div>
        </div>
        {{-- Kartu "Belum Ada Dosen Wali" bisa diklik -> menuju halaman penetapan wali --}}
        <div class="col-sm-6 col-xl-3">
            <a href="{{ route('admin.dosen-wali.index') }}" class="text-decoration-none">
                <div class="stat-card p-3 animate-fade-up delay-3">
                    <div class="d-flex align-items-center gap-3">
                        <div class="stat-icon amber"><i class="bi bi-person-exclamation"></i></div>
                        <div class="min-w-0">
                            <div class="stat-label">Belum Ada Dosen Wali</div>
                            <div class="stat-value">{{ $belumWali }}</div>
                        </div>
                    </div>
                </div>
            </a>
        </div>
    </div>

    {{-- Peringatan jika masih ada mahasiswa tanpa dosen wali --}}
    @if ($belumWali > 0)
        <div class="alert alert-warning d-flex align-items-center gap-2 mb-4 animate-fade-up" role="alert">
            <i class="bi bi-info-circle-fill"></i>
            <span>Terdapat <b>{{ $belumWali }}</b> mahasiswa yang belum memiliki dosen wali.
                <a href="{{ route('admin.dosen-wali.index') }}" class="fw-semibold">Tetapkan sekarang</a>.</span>
        </div>
    @endif

    <div class="row g-3">
        {{-- ===== GRAFIK BAR (REKAP PER BULAN) ===== --}}
        <div class="col-lg-8">
            <div class="card animate-fade-up delay-1">
                <div class="card-header d-flex align-items-center justify-content-between flex-wrap gap-2">
                    <span><i class="bi bi-graph-up-arrow me-2 text-primary"></i>Rekap Perwalian per Bulan</span>
                    {{-- Badge di header: total seluruh catatan --}}
                    @if (!empty($labels))
                        <span class="badge badge-soft-primary">{{ array_sum($values) }} catatan</span>
                    @endif
                </div>
                <div class="card-body">
                    {{-- Jika belum ada data -> tampilkan state kosong --}}
                    @if (empty($labels))
                        <div class="empty-state">
                            <i class="bi bi-inbox"></i>
                            <p class="text-muted mb-0">Belum ada data perwalian.</p>
                        </div>
                    @else
                        {{-- Elemen <canvas> yang akan digambar oleh Chart.js (lihat push scripts) --}}
                        <canvas id="rekapChart" height="110"></canvas>
                    @endif
                </div>
            </div>
        </div>

        {{-- ===== PANEL RINGKASAN ===== --}}
        <div class="col-lg-4">
            <div class="card animate-fade-up delay-2">
                <div class="card-header"><i class="bi bi-list-check me-2 text-primary"></i>Ringkasan</div>
                <div class="card-body">
                    <div class="soft-panel p-3 mb-3">
                        <div class="d-flex justify-content-between align-items-center">
                            <span class="text-muted small">Dosen Wali yang Terdaftar</span>
                            <span class="fs-5 fw-bold">{{ $totalDosen }}</span>
                        </div>
                    </div>
                    <div class="soft-panel p-3 mb-3">
                        <div class="d-flex justify-content-between align-items-center">
                            <span class="text-muted small">Rata-rata Perwalian / Mahasiswa</span>
                            {{-- Hitung rata-rata; jaga-jaga jika $totalMahasiswa = 0 (hindari bagi nol) --}}
                            <span class="fs-5 fw-bold">{{ $totalMahasiswa ? round($totalPerwalian / $totalMahasiswa, 1) : 0 }}</span>
                        </div>
                    </div>
                    <div class="soft-panel p-3">
                        <div class="d-flex justify-content-between align-items-center">
                            <span class="text-muted small">Bulan dengan Catatan Terbanyak</span>
                            <span class="fs-5 fw-bold">
                                {{-- array_search(max(...)) = cari index nilai terbesar, lalu ambil nama bulannya --}}
                                @if (!empty($labels))
                                    {{ $labels[array_search(max($values), $values)] }}
                                @else
                                    -
                                @endif
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

{{-- BLOK JAVASCRIPT: inisialisasi grafik Chart.js --}}
@push('scripts')
    <script>
        // ubah data PHP menjadi JSON agar bisa dipakai JavaScript
        const labels = @json($labels);
        const values = @json($values);

        if (labels.length) {
            // Ambil elemen canvas dari halaman
            const ctx = document.getElementById('rekapChart').getContext('2d');
            // Cek tema saat ini (terang/gelap) agar warna grafik menyesuaikan
            const theme = document.documentElement.getAttribute('data-bs-theme');
            const gridColor = theme === 'dark' ? 'rgba(148,163,184,0.15)' : 'rgba(100,116,139,0.12)';
            const tickColor = theme === 'dark' ? '#94a3b8' : '#64748b';

            // Buat grafik baru dengan Chart.js (library Chart.js dimuat di layouts/app)
            new Chart(ctx, {
                type: 'bar', // jenis grafik: batang
                data: {
                    labels: labels,
                    datasets: [{
                        label: 'Jumlah Perwalian',
                        data: values,
                        backgroundColor: 'rgba(16, 185, 129, 0.75)',
                        hoverBackgroundColor: '#10b981',
                        borderRadius: 8, // sudut batang membulat
                        maxBarThickness: 46,
                    }],
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: true,
                    plugins: {
                        legend: { display: false },
                        tooltip: { // kotak info saat mouse diarahkan ke batang
                            backgroundColor: 'rgba(15,23,42,0.9)',
                            padding: 12,
                            cornerRadius: 10,
                            callbacks: {
                                label: (ctx) => ` ${ctx.parsed.y} catatan`,
                            },
                        },
                    },
                    scales: {
                        x: {
                            grid: { display: false },
                            ticks: { color: tickColor, font: { family: 'Inter', size: 11 } },
                        },
                        y: {
                            beginAtZero: true,
                            ticks: { stepSize: 1, color: tickColor, font: { family: 'Inter', size: 11 } },
                            grid: { color: gridColor },
                            border: { display: false },
                        },
                    },
                },
            });
        }
    </script>
@endpush
