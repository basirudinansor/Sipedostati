@extends('layouts.app')
@section('title', 'Dashboard')

@push('styles')
<style>
    .status-panel { padding: 44px; position: relative; overflow: hidden; }
    .status-panel__eyebrow { display: block; margin-bottom: 14px; }
    .status-name { font-size: clamp(26px, 3vw, 34px); margin-bottom: 6px; }
    .status-field { color: var(--text-muted); font-size: 15px; }
    .status-empty { color: var(--text-muted); font-size: 15.5px; max-width: 46ch; margin-bottom: 24px; line-height: 1.7; }
    .stamp-wrap { position: absolute; top: 36px; right: 40px; opacity: 0; animation: stampIn .5s cubic-bezier(.2,1.4,.4,1) .3s forwards; }
    @keyframes stampIn { from { opacity: 0; transform: rotate(-2deg) scale(1.4);} to { opacity: 1; transform: rotate(-2deg) scale(1);} }
    @media (max-width: 640px) { .stamp-wrap { position: static; margin-bottom: 20px; display: inline-flex; } }
    .status-actions { margin-top: 24px; display: flex; gap: 12px; flex-wrap: wrap; }
    .cancel-hint { color: var(--text-muted); font-size: 13px; margin-top: 10px; }
</style>
@endpush

@section('content')
<span class="eyebrow">Dashboard Mahasiswa</span>
<h1 class="page-title">Status Bimbingan Anda</h1>
<p class="page-subtitle">Ringkasan status pemilihan dosen pembimbing tugas akhir.</p>

<div class="panel status-panel">
    @if($pilihan)
        <div class="stamp-wrap"><span class="stamp">✓ Terdaftar</span></div>
        <span class="eyebrow status-panel__eyebrow">Dosen Pembimbing Anda</span>
        <h2 class="status-name">{{ $pilihan->dosen->nama }}</h2>
        <p class="status-field">{{ $pilihan->dosen->bidang_keahlian ?: 'Bidang keahlian belum diisi' }}</p>
        <p class="status-field">NIP {{ $pilihan->dosen->nip }}</p>

        @if($bisaBatal)
            <div class="status-actions">
                <form action="{{ route('pilih-dosen.destroy') }}" method="POST"
                    onsubmit="return confirm('Yakin ingin membatalkan pilihan dosen pembimbing ini? Kuota akan dikembalikan dan Anda bisa memilih dosen lain selama pemilihan masih dibuka.')">
                    @csrf @method('DELETE')
                    <button type="submit" class="btn btn--danger">Batalkan Pilihan</button>
                </form>
            </div>
            <p class="cancel-hint">Pembatalan hanya bisa dilakukan selama periode pemilihan masih dibuka admin.</p>
        @else
            <p class="cancel-hint">Periode pemilihan sudah ditutup, pilihan ini bersifat final dan tidak bisa dibatalkan.</p>
        @endif
    @else
        <span class="eyebrow status-panel__eyebrow">Belum Ada Pembimbing</span>
        <h2 class="status-name">Anda belum memilih dosen pembimbing</h2>
        <p class="status-empty">
            Begitu admin membuka jadwal pemilihan, Anda bisa memilih dosen pembimbing
            di tab <strong>Pilih Dosen Pembimbing</strong>. Kuota tiap dosen terbatas,
            jadi datang tepat waktu saat pemilihan dibuka.
        </p>
        <a href="{{ route('pilih-dosen') }}" class="btn btn--primary">Buka halaman pemilihan</a>
    @endif
</div>
@endsection
