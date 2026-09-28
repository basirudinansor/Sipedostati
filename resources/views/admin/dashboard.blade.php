@extends('layouts.app')
@section('title', 'Dashboard Admin')

@push('styles')
<style>
    .stat-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(210px, 1fr));
        gap: 18px;
        margin-bottom: 32px;
    }
    .stat-card {
        background: #fff; border: 1px solid var(--ink-line); border-radius: 8px;
        padding: 26px 24px; position: relative; overflow: hidden;
        opacity: 0; animation: statIn .45s ease forwards;
    }
    .stat-card::before { content: ''; position: absolute; top: 0; left: 0; right: 0; height: 3px; background: var(--brass); }
    @keyframes statIn { from { opacity: 0; transform: translateY(10px);} to { opacity: 1; transform: translateY(0);} }
    .stat-card__label { font-family: var(--font-mono); font-size: 11.5px; letter-spacing: .1em; text-transform: uppercase; color: var(--text-muted); margin-bottom: 10px; }
    .stat-card__value { font-family: var(--font-display); font-size: 34px; color: var(--ink); line-height: 1; margin-bottom: 6px; }
    .stat-card__sub { font-size: 13px; color: var(--text-muted); }
    .stat-card__value.crimson { color: var(--crimson); }
    .stat-card__value.forest { color: var(--forest); }
    .stat-card__value.brass { color: var(--brass); }

    .chart-grid { display: grid; grid-template-columns: 1.6fr 1fr; gap: 20px; margin-bottom: 32px; align-items: stretch; }
    .chart-panel { padding: 28px; display: flex; flex-direction: column; }
    .chart-panel h2 { font-size: 17px; margin-bottom: 4px; }
    .chart-panel .chart-hint { color: var(--text-muted); font-size: 13px; margin: 0 0 20px; }
    .chart-canvas-wrap { position: relative; flex: 1; min-height: 260px; }

    .jadwal-strip {
        display: flex; align-items: center; justify-content: space-between; gap: 16px;
        background: var(--ink); color: var(--paper); border-radius: 8px; padding: 20px 28px; margin-bottom: 32px;
        flex-wrap: wrap;
    }
    .jadwal-strip__label { font-family: var(--font-mono); font-size: 11.5px; letter-spacing: .1em; text-transform: uppercase; color: var(--brass-bright); margin-bottom: 4px; }
    .jadwal-strip__value { font-family: var(--font-display); font-size: 19px; }
    .jadwal-strip a { color: var(--brass-bright); font-size: 13.5px; text-decoration: underline; }

    @media (max-width: 900px) { .chart-grid { grid-template-columns: 1fr; } }
</style>
@endpush

@section('content')
<span class="eyebrow">Panel Admin</span>
<h1 class="page-title">Ringkasan Pemilihan Dosen Pembimbing</h1>
<p class="page-subtitle">Pantau progres kuota dosen dan status pemilihan mahasiswa (Pembimbing 1 & 2) secara real-time.</p>

<div class="jadwal-strip">
    <div>
        <div class="jadwal-strip__label">Status Jadwal Pemilihan</div>
        <div class="jadwal-strip__value">
            @if(!$jadwal || $jadwal->status === 'draft')
                Belum dijadwalkan
            @else
                {{ ucfirst($jadwal->status) }} — buka {{ $jadwal->waktu_buka?->translatedFormat('d F Y, H:i') }} WIB
            @endif
        </div>
    </div>
    <a href="{{ route('admin.jadwal.index') }}">Kelola jadwal →</a>
</div>

<div class="stat-grid">
    <div class="stat-card" style="animation-delay: .02s">
        <div class="stat-card__label">Total Mahasiswa</div>
        <div class="stat-card__value">{{ $totalMahasiswa }}</div>
        <div class="stat-card__sub">Akun terdaftar di sistem</div>
    </div>

    <div class="stat-card" style="animation-delay: .05s">
        <div class="stat-card__label">Sudah Pilih Pembimbing 1</div>
        <div class="stat-card__value brass">{{ $sudahP1 }}</div>
        <div class="stat-card__sub">Mahasiswa yang sudah menentukan P1</div>
    </div>

    <div class="stat-card" style="animation-delay: .08s">
        <div class="stat-card__label">Sudah Pilih Pembimbing 2</div>
        <div class="stat-card__value">{{ $sudahP2 }}</div>
        <div class="stat-card__sub">Mahasiswa yang sudah menentukan P2</div>
    </div>

    <div class="stat-card" style="animation-delay: .11s">
        <div class="stat-card__label">Sudah Lengkap (P1 & P2)</div>
        <div class="stat-card__value forest">{{ $sudahLengkap }}</div>
        <div class="stat-card__sub">{{ $persenLengkap }}% dari total mahasiswa</div>
    </div>

    <div class="stat-card" style="animation-delay: .14s">
        <div class="stat-card__label">Belum Sama Sekali</div>
        <div class="stat-card__value crimson">{{ $belumSamaSekali }}</div>
        <div class="stat-card__sub">Belum memilih P1 maupun P2</div>
    </div>

    <div class="stat-card" style="animation-delay: .17s">
        <div class="stat-card__label">Kuota Terisi</div>
        <div class="stat-card__value">{{ $persenTerisi }}%</div>
        <div class="stat-card__sub">{{ $totalTerpakai }} dari {{ $totalKuota }} slot bimbingan</div>
    </div>
</div>

<div class="chart-grid">
    <div class="panel chart-panel">
        <h2>Kuota Terisi per Dosen</h2>
        <p class="chart-hint">Emas = terisi sebagai Pembimbing 1, Navy = terisi sebagai Pembimbing 2. Data dihitung langsung dari pilihan mahasiswa.</p>
        <div class="chart-canvas-wrap"><canvas id="chartDosen"></canvas></div>
    </div>
    <div class="panel chart-panel">
        <h2>Status Pemilihan Mahasiswa</h2>
        <p class="chart-hint">Lengkap = sudah pilih P1 dan P2. Sebagian = baru salah satu.</p>
        <div class="chart-canvas-wrap"><canvas id="chartStatus"></canvas></div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.4/dist/chart.umd.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function () {
    const brassColor = '#B8912E';
    const crimsonColor = '#A6303F';
    const forestColor = '#2E6B4F';

    const dosenLabels = @json($dosens->pluck('nama'));
    const dosenP1 = @json($dosens->pluck('pilihan_p1_count'));
    const dosenP2 = @json($dosens->pluck('pilihan_p2_count'));
    const dosenKuota = @json($dosens->pluck('kuota'));

    new Chart(document.getElementById('chartDosen'), {
        type: 'bar',
        data: {
            labels: dosenLabels,
            datasets: [
                { label: 'Sbg Pembimbing 1', data: dosenP1, backgroundColor: brassColor, borderRadius: 4 },
                { label: 'Sbg Pembimbing 2', data: dosenP2, backgroundColor: '#14213D', borderRadius: 4 },
            ]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            scales: {
                x: { ticks: { font: { family: 'IBM Plex Sans', size: 11 } } },
                y: { beginAtZero: true, ticks: { precision: 0 }, suggestedMax: Math.max(...dosenKuota, 1) }
            },
            plugins: {
                legend: { position: 'bottom', labels: { font: { family: 'IBM Plex Sans', size: 12 } } }
            }
        }
    });

    new Chart(document.getElementById('chartStatus'), {
        type: 'doughnut',
        data: {
            labels: ['Lengkap (P1 & P2)', 'Sebagian (1 saja)', 'Belum sama sekali'],
            datasets: [{
                data: [{{ $sudahLengkap }}, {{ $sebagian }}, {{ $belumSamaSekali }}],
                backgroundColor: [forestColor, brassColor, crimsonColor],
                borderWidth: 0,
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            cutout: '68%',
            plugins: {
                legend: { position: 'bottom', labels: { font: { family: 'IBM Plex Sans', size: 12 } } }
            }
        }
    });
});
</script>
@endsection
