@extends('layouts.app')
@section('title', 'Dashboard')

@push('styles')
<style>
    .status-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 18px; }
    .status-panel { padding: 36px; position: relative; overflow: hidden; }
    .status-panel__eyebrow { display: block; margin-bottom: 14px; }
    .status-name { font-size: clamp(22px, 2.6vw, 28px); margin-bottom: 6px; }
    .status-field { color: var(--text-muted); font-size: 14.5px; }
    .status-empty { color: var(--text-muted); font-size: 14.5px; margin-bottom: 20px; line-height: 1.7; }
    .stamp-wrap { position: absolute; top: 24px; right: 24px; opacity: 0; animation: stampIn .5s cubic-bezier(.2,1.4,.4,1) .3s forwards; }
    @keyframes stampIn { from { opacity: 0; transform: rotate(-2deg) scale(1.4);} to { opacity: 1; transform: rotate(-2deg) scale(1);} }
    .status-actions { margin-top: 20px; display: flex; gap: 12px; flex-wrap: wrap; }
    .cancel-hint { color: var(--text-muted); font-size: 12.5px; margin-top: 8px; }
    @media (max-width: 780px) { .status-grid { grid-template-columns: 1fr; } }
</style>
@endpush

@section('content')
<span class="eyebrow">Dashboard Mahasiswa</span>
<h1 class="page-title">Status Bimbingan Anda</h1>
<p class="page-subtitle">Anda perlu memilih 2 dosen pembimbing: Pembimbing 1 dan Pembimbing 2 (harus berbeda orang).</p>

<div class="status-grid">
    <div class="panel status-panel">
        <span class="eyebrow status-panel__eyebrow">Pembimbing 1</span>
        @if($pilihan1)
            <div class="stamp-wrap"><span class="stamp">✓ Terdaftar</span></div>
            <h2 class="status-name">{{ $pilihan1->dosen->nama }}</h2>
            <p class="status-field">{{ $pilihan1->dosen->bidang_keahlian ?: 'Bidang keahlian belum diisi' }}</p>
            <p class="status-field">NIP {{ $pilihan1->dosen->nip }}</p>
            @if($bisaBatal)
                <div class="status-actions">
                    <form action="{{ route('pilih-dosen.destroy') }}" method="POST"
                        onsubmit="return confirm('Yakin batalkan pilihan Pembimbing 1?')">
                        @csrf @method('DELETE')
                        <input type="hidden" name="jenis" value="pembimbing_1">
                        <button type="submit" class="btn btn--danger" style="padding: 9px 18px; font-size: 13.5px;">Batalkan</button>
                    </form>
                </div>
            @else
                <p class="cancel-hint">Periode pemilihan sudah ditutup, tidak bisa dibatalkan.</p>
            @endif
        @else
            <h2 class="status-name">Belum dipilih</h2>
            <p class="status-empty">Silakan pilih Pembimbing 1 di halaman Pilih Dosen Pembimbing.</p>
            <a href="{{ route('pilih-dosen') }}" class="btn btn--primary" style="padding: 10px 20px; font-size: 13.5px;">Pilih sekarang</a>
        @endif
    </div>

    <div class="panel status-panel">
        <span class="eyebrow status-panel__eyebrow">Pembimbing 2</span>
        @if($pilihan2)
            <div class="stamp-wrap"><span class="stamp">✓ Terdaftar</span></div>
            <h2 class="status-name">{{ $pilihan2->dosen->nama }}</h2>
            <p class="status-field">{{ $pilihan2->dosen->bidang_keahlian ?: 'Bidang keahlian belum diisi' }}</p>
            <p class="status-field">NIP {{ $pilihan2->dosen->nip }}</p>
            @if($bisaBatal)
                <div class="status-actions">
                    <form action="{{ route('pilih-dosen.destroy') }}" method="POST"
                        onsubmit="return confirm('Yakin batalkan pilihan Pembimbing 2?')">
                        @csrf @method('DELETE')
                        <input type="hidden" name="jenis" value="pembimbing_2">
                        <button type="submit" class="btn btn--danger" style="padding: 9px 18px; font-size: 13.5px;">Batalkan</button>
                    </form>
                </div>
            @else
                <p class="cancel-hint">Periode pemilihan sudah ditutup, tidak bisa dibatalkan.</p>
            @endif
        @else
            <h2 class="status-name">Belum dipilih</h2>
            <p class="status-empty">Silakan pilih Pembimbing 2 di halaman Pilih Dosen Pembimbing.</p>
            <a href="{{ route('pilih-dosen') }}" class="btn btn--primary" style="padding: 10px 20px; font-size: 13.5px;">Pilih sekarang</a>
        @endif
    </div>
</div>
@endsection
