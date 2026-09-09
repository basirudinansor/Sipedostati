@extends('layouts.app')
@section('title', 'Kelola Jadwal')
@section('page-class', 'page--narrow')

@push('styles')
<style>
    .status-row { display: flex; align-items: center; gap: 12px; margin: 6px 0 20px; flex-wrap: wrap; }
    .status-badge { font-family: var(--font-mono); font-size: 12px; letter-spacing: .06em; text-transform: uppercase; padding: 5px 12px; border-radius: 20px; font-weight: 600; }
    .status-badge--draft { background: var(--paper-dim); color: var(--text-muted); }
    .status-badge--terjadwal { background: #FBF1DB; color: #8A6A16; }
    .status-badge--dibuka { background: #EAF3ED; color: var(--forest); }
    .status-badge--ditutup { background: #F7E9EA; color: var(--crimson); }
    .section-gap { margin-bottom: 32px; }
</style>
@endpush

@section('content')
<span class="eyebrow">Panel Admin</span>
<h1 class="page-title">Kelola Jadwal Pemilihan</h1>
<p class="page-subtitle">Tentukan kapan mahasiswa bisa mulai memilih dosen pembimbing.</p>

<div class="panel panel--pad section-gap">
    <h2 style="font-size: 17px; margin-bottom: 4px;">Jadwal saat ini</h2>
    @if($jadwal)
        <div class="status-row">
            <span class="status-badge status-badge--{{ $jadwal->status }}">{{ $jadwal->status }}</span>
            <span style="color: var(--text-muted); font-size: 14.5px;">Buka {{ $jadwal->waktu_buka?->translatedFormat('d F Y, H:i') }} WIB</span>
        </div>
        @if($jadwal->status !== 'ditutup')
        <form action="{{ route('admin.jadwal.tutup', $jadwal) }}" method="POST"
            onsubmit="return confirm('Yakin tutup pemilihan sekarang? Mahasiswa tidak akan bisa memilih setelah ini.')">
            @csrf
            <button type="submit" class="btn btn--danger">Tutup pemilihan sekarang</button>
        </form>
        @endif
    @else
        <p style="color: var(--text-muted); font-size: 14.5px;">Belum ada jadwal yang dibuat.</p>
    @endif
</div>

<div class="panel panel--pad">
    <h2 style="font-size: 17px; margin-bottom: 20px;">Buat / ubah jadwal</h2>
    <form action="{{ route('admin.jadwal.store') }}" method="POST">
        @csrf
        <div style="margin-bottom: 18px;">
            <label for="waktu_buka">Waktu buka</label>
            <input type="datetime-local" id="waktu_buka" name="waktu_buka" required>
        </div>
        <div style="margin-bottom: 22px;">
            <label for="waktu_tutup">Waktu tutup (opsional)</label>
            <input type="datetime-local" id="waktu_tutup" name="waktu_tutup">
        </div>
        <button type="submit" class="btn btn--primary">Simpan jadwal</button>
    </form>
</div>
@endsection
