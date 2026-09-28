@extends('layouts.app')
@section('title', 'Pilih Dosen Pembimbing')

@push('styles')
<style>
    .waiting-panel { padding: 64px 40px; text-align: center; }
    .waiting-panel__icon {
        width: 56px; height: 56px; margin: 0 auto 22px; border: 2px solid var(--ink-line); border-radius: 50%;
        display: flex; align-items: center; justify-content: center; font-family: var(--font-mono); font-size: 22px; color: var(--text-muted);
    }
    .waiting-panel h2 { font-size: 22px; margin-bottom: 8px; }
    .waiting-panel p { color: var(--text-muted); font-size: 15px; }

    .countdown-panel {
        background: var(--ink); border-radius: 8px; padding: 52px 40px; text-align: center; color: var(--paper);
        position: relative; overflow: hidden;
    }
    .countdown-panel::before {
        content: ''; position: absolute; inset: 0;
        background-image: radial-gradient(rgba(216, 178, 76, .14) 1px, transparent 1px); background-size: 24px 24px;
    }
    .countdown-panel > * { position: relative; z-index: 1; }
    .countdown-panel .eyebrow { color: var(--brass-bright); }
    .countdown-panel .cd-target { font-family: var(--font-display); font-size: clamp(20px, 2.4vw, 26px); margin: 6px 0 34px; color: var(--paper); }
    .flip-row { display: flex; justify-content: center; gap: 14px; flex-wrap: wrap; }
    .flip-group { display: flex; flex-direction: column; align-items: center; gap: 10px; }
    .flip-digits { display: flex; gap: 4px; }
    .flip-tile {
        width: 54px; height: 72px; background: #fff; color: var(--ink); border-radius: 5px;
        font-family: var(--font-mono); font-weight: 700; font-size: 36px;
        display: flex; align-items: center; justify-content: center; box-shadow: 0 4px 0 rgba(0,0,0,.25); position: relative;
    }
    .flip-tile.flipping { animation: flipTile .4s ease; }
    @keyframes flipTile { 0% { transform: rotateX(0deg);} 45% { transform: rotateX(85deg); opacity: .5;} 55% { transform: rotateX(85deg); opacity: .5;} 100% { transform: rotateX(0deg);} }
    .flip-label { font-family: var(--font-mono); font-size: 11px; letter-spacing: .16em; text-transform: uppercase; color: rgba(251, 249, 244, .55); }
    .flip-colon { font-family: var(--font-mono); font-size: 32px; color: rgba(251,249,244,.35); align-self: center; padding-bottom: 24px; }

    /* Step progress indicator */
    .step-progress { display: flex; align-items: center; gap: 10px; margin-bottom: 32px; }
    .step-dot {
        width: 32px; height: 32px; border-radius: 50%; display: flex; align-items: center; justify-content: center;
        font-family: var(--font-mono); font-size: 13px; font-weight: 700; border: 2px solid var(--ink-line); color: var(--text-muted); flex-shrink: 0;
        background: #fff;
    }
    .step-dot.is-done { background: var(--forest); border-color: var(--forest); color: #fff; }
    .step-dot.is-current { background: var(--ink); border-color: var(--ink); color: var(--paper); }
    .step-label { font-size: 14px; color: var(--text-muted); }
    .step-label.is-active { color: var(--ink); font-weight: 600; }
    .step-line { flex: 1; height: 2px; background: var(--ink-line); max-width: 60px; }
    .step-line.is-done { background: var(--forest); }

    .slot-done-panel { padding: 40px; text-align: center; }
    .slot-done-panel .dosen-name { font-family: var(--font-display); font-size: 24px; color: var(--ink); margin: 10px 0 4px; }

    .selesai-panel { padding: 56px 40px; text-align: center; }
    .selesai-panel__icon {
        width: 64px; height: 64px; margin: 0 auto 20px; border-radius: 50%; background: var(--forest); color: #fff;
        display: flex; align-items: center; justify-content: center; font-size: 28px;
    }
    .selesai-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 16px; max-width: 560px; margin: 28px auto 0; text-align: left; }
    .selesai-card { border: 1px solid var(--ink-line); border-radius: 6px; padding: 18px 20px; }
    .selesai-card .eyebrow { margin-bottom: 6px; }
    .selesai-card .nama { font-family: var(--font-display); font-size: 17px; color: var(--ink); }
    @media (max-width: 560px) { .selesai-grid { grid-template-columns: 1fr; } }

    .dosen-grid { display: grid; gap: 16px; }
    .dosen-ticket {
        display: grid; grid-template-columns: 1fr auto; background: #fff; border: 1px solid var(--ink-line); border-radius: 6px;
        overflow: hidden; transition: transform .2s ease, box-shadow .2s ease, border-color .2s ease; opacity: 0; animation: ticketIn .45s ease forwards;
    }
    .dosen-ticket:hover { transform: translateY(-3px); box-shadow: var(--shadow-md); border-color: var(--brass); }
    @keyframes ticketIn { from { opacity: 0; transform: translateY(10px);} to { opacity: 1; transform: translateY(0);} }
    .dosen-ticket__main { padding: 24px 26px; display: flex; gap: 18px; align-items: center; }
    .dosen-ticket__no {
        font-family: var(--font-mono); font-size: 13px; color: var(--text-muted); border: 1px solid var(--ink-line); border-radius: 4px;
        width: 40px; height: 40px; display: flex; align-items: center; justify-content: center; flex-shrink: 0;
    }
    .dosen-ticket__name { font-family: var(--font-display); font-size: 19px; color: var(--ink); margin-bottom: 3px; }
    .dosen-ticket__field { color: var(--text-muted); font-size: 13.5px; }
    .dosen-ticket__badge {
        display: inline-block; margin-top: 6px; font-family: var(--font-mono); font-size: 11px;
        padding: 2px 8px; border-radius: 12px; background: #FBF1DB; color: #8A6A16;
    }
    .dosen-ticket__stub {
        border-left: 2px dashed var(--ink-line); padding: 22px 26px; display: flex; flex-direction: column;
        align-items: center; justify-content: center; gap: 10px; min-width: 176px; background: var(--paper-dim);
    }
    .quota-figure { font-family: var(--font-mono); font-size: 13px; color: var(--text-muted); }
    .quota-figure strong { color: var(--ink); font-size: 15px; }
    .quota-gauge { width: 120px; height: 6px; background: var(--ink-line); border-radius: 3px; overflow: hidden; }
    .quota-gauge__fill { height: 100%; border-radius: 3px; transition: width .4s ease; }
    .dosen-ticket.is-full, .dosen-ticket.is-taken-other { opacity: .55; }
    .dosen-ticket.is-full:hover, .dosen-ticket.is-taken-other:hover { transform: none; box-shadow: none; border-color: var(--ink-line); }

    @media (max-width: 620px) {
        .dosen-ticket { grid-template-columns: 1fr; }
        .dosen-ticket__stub { border-left: none; border-top: 2px dashed var(--ink-line); flex-direction: row; justify-content: space-between; }
    }
</style>
@endpush

@section('content')
<span class="eyebrow">Pilih Dosen Pembimbing</span>
<h1 class="page-title">Pemilihan Dosen Pembimbing Tugas Akhir</h1>
<p class="page-subtitle">Anda perlu memilih 2 dosen pembimbing yang berbeda: Pembimbing 1 dan Pembimbing 2. Kuota tiap dosen terbatas untuk masing-masing peran, dan pilihan bersifat final selama pemilihan masih dibuka.</p>

@if(!$jadwal || $jadwal->status === 'draft')
    <div class="panel waiting-panel">
        <div class="waiting-panel__icon">?</div>
        <h2>Belum dijadwalkan</h2>
        <p>Admin belum menetapkan jadwal pemilihan dosen pembimbing. Silakan cek kembali nanti.</p>
    </div>
@else
    @php
        $sudahDibukaAwal = $jadwal->sudahDibuka();
        $currentStep = (!$pilihan1) ? 1 : ((!$pilihan2) ? 2 : 3);
    @endphp

    <div id="area-countdown" class="countdown-panel" style="{{ $sudahDibukaAwal ? 'display:none' : '' }}">
        <span class="eyebrow">Pemilihan akan dibuka pada</span>
        <p class="cd-target">{{ $jadwal->waktu_buka->translatedFormat('d F Y, H:i') }} WIB</p>
        <div class="flip-row" id="flip-row">
            <div class="flip-group">
                <div class="flip-digits" data-unit="h"><div class="flip-tile">0</div><div class="flip-tile">0</div></div>
                <span class="flip-label">Jam</span>
            </div>
            <span class="flip-colon">:</span>
            <div class="flip-group">
                <div class="flip-digits" data-unit="m"><div class="flip-tile">0</div><div class="flip-tile">0</div></div>
                <span class="flip-label">Menit</span>
            </div>
            <span class="flip-colon">:</span>
            <div class="flip-group">
                <div class="flip-digits" data-unit="s"><div class="flip-tile">0</div><div class="flip-tile">0</div></div>
                <span class="flip-label">Detik</span>
            </div>
        </div>
    </div>

    <div id="area-dosen" style="{{ $sudahDibukaAwal ? '' : 'display:none' }}">

        {{-- Progress indicator: 1 -> 2 -> Selesai --}}
        <div class="step-progress">
            <div class="step-dot {{ $currentStep > 1 ? 'is-done' : 'is-current' }}">{{ $currentStep > 1 ? '✓' : '1' }}</div>
            <span class="step-label {{ $currentStep == 1 ? 'is-active' : '' }}">Pembimbing 1</span>
            <div class="step-line {{ $currentStep > 1 ? 'is-done' : '' }}"></div>
            <div class="step-dot {{ $currentStep > 2 ? 'is-done' : ($currentStep == 2 ? 'is-current' : '') }}">{{ $currentStep > 2 ? '✓' : '2' }}</div>
            <span class="step-label {{ $currentStep == 2 ? 'is-active' : '' }}">Pembimbing 2</span>
            <div class="step-line {{ $currentStep > 2 ? 'is-done' : '' }}"></div>
            <div class="step-dot {{ $currentStep == 3 ? 'is-done' : '' }}">{{ $currentStep == 3 ? '✓' : '' }}</div>
            <span class="step-label {{ $currentStep == 3 ? 'is-active' : '' }}">Selesai</span>
        </div>

        @if($currentStep === 3)
            <div class="panel selesai-panel">
                <div class="selesai-panel__icon">✓</div>
                <h2 style="font-size: 24px;">Pemilihan Anda sudah lengkap</h2>
                <p style="color: var(--text-muted); margin-top: 8px;">Kelola pembatalan (kalau masih dibuka) lewat halaman Dashboard.</p>
                <div class="selesai-grid">
                    <div class="selesai-card">
                        <span class="eyebrow">Pembimbing 1</span>
                        <div class="nama">{{ $pilihan1->dosen->nama }}</div>
                    </div>
                    <div class="selesai-card">
                        <span class="eyebrow">Pembimbing 2</span>
                        <div class="nama">{{ $pilihan2->dosen->nama }}</div>
                    </div>
                </div>
                <a href="{{ route('dashboard') }}" class="btn btn--primary" style="margin-top: 28px;">Buka Dashboard</a>
            </div>
        @else
            @php
                $jenis = $currentStep === 1 ? 'pembimbing_1' : 'pembimbing_2';
                $labelSlot = $currentStep === 1 ? 'Pembimbing 1' : 'Pembimbing 2';
                $pilihanLain = $currentStep === 1 ? null : $pilihan1;
                $labelLain = 'Pembimbing 1';
            @endphp

            <h2 style="font-size: 19px; margin-bottom: 4px;">Langkah {{ $currentStep }} dari 2: Pilih {{ $labelSlot }}</h2>
            @if($currentStep === 2)
                <p style="color: var(--text-muted); font-size: 14px; margin-bottom: 20px;">
                    Pembimbing 1 Anda: <strong style="color: var(--ink);">{{ $pilihan1->dosen->nama }}</strong> — dosen ini tidak bisa dipilih lagi sebagai Pembimbing 2.
                </p>
            @else
                <p style="color: var(--text-muted); font-size: 14px; margin-bottom: 20px;"></p>
            @endif

            <form action="{{ route('pilih-dosen.store') }}" method="POST" class="form-pilih">
                @csrf
                <input type="hidden" name="jenis" value="{{ $jenis }}">
                <div class="dosen-grid">
                    @foreach($dosens as $i => $dosen)
                        @php
                            $terpakai = $dosen->terpakai($jenis);
                            $pct = $dosen->kuota > 0 ? min(100, round(($terpakai / $dosen->kuota) * 100)) : 100;
                            $gaugeColor = $dosen->penuh($jenis) ? 'var(--crimson)' : ($pct >= 70 ? 'var(--brass)' : 'var(--forest)');
                            $dipilihDiSlotLain = $pilihanLain && (int) $pilihanLain->dosen_id === $dosen->id;
                            $disabled = $dosen->penuh($jenis) || $dipilihDiSlotLain;
                        @endphp
                        <div class="dosen-ticket {{ $dosen->penuh($jenis) ? 'is-full' : '' }} {{ $dipilihDiSlotLain ? 'is-taken-other' : '' }}" style="animation-delay: {{ $i * 0.04 }}s">
                            <div class="dosen-ticket__main">
                                <span class="dosen-ticket__no">{{ str_pad($i + 1, 2, '0', STR_PAD_LEFT) }}</span>
                                <div>
                                    <div class="dosen-ticket__name">{{ $dosen->nama }}</div>
                                    <div class="dosen-ticket__field">{{ $dosen->bidang_keahlian ?: 'Bidang keahlian belum diisi' }} · NIP {{ $dosen->nip }}</div>
                                    @if($dipilihDiSlotLain)
                                        <span class="dosen-ticket__badge">Sudah dipilih sebagai {{ $labelLain }}</span>
                                    @endif
                                </div>
                            </div>
                            <div class="dosen-ticket__stub">
                                <div class="quota-figure"><strong>{{ $terpakai }}</strong> / {{ $dosen->kuota }} kuota ({{ $labelSlot }})</div>
                                <div class="quota-gauge"><div class="quota-gauge__fill" style="width: {{ $pct }}%; background: {{ $gaugeColor }};"></div></div>
                                <button type="submit" name="dosen_id" value="{{ $dosen->id }}"
                                    data-dosen-nama="{{ $dosen->nama }}"
                                    data-slot-label="{{ $labelSlot }}"
                                    {{ $disabled ? 'disabled' : '' }}
                                    class="btn {{ $disabled ? 'btn--outline' : 'btn--primary' }}" style="padding: 9px 20px; font-size: 13.5px;">
                                    {{ $dosen->penuh($jenis) ? 'Kuota penuh' : ($dipilihDiSlotLain ? 'Tidak bisa dipilih' : 'Pilih dosen ini') }}
                                </button>
                            </div>
                        </div>
                    @endforeach
                </div>
            </form>
        @endif
    </div>
@endif

<script>
document.addEventListener('DOMContentLoaded', function () {
    document.querySelectorAll('.form-pilih').forEach(formPilih => {
        formPilih.addEventListener('submit', function (e) {
            const btn = e.submitter;
            const nama = btn ? btn.dataset.dosenNama : null;
            const slotLabel = btn ? btn.dataset.slotLabel : '';
            const pesan = nama
                ? `Apakah Anda yakin memilih ${nama} sebagai ${slotLabel}? Pilihan ini final selama pemilihan masih dibuka.`
                : 'Apakah Anda yakin dengan pilihan ini?';
            if (!confirm(pesan)) {
                e.preventDefault();
                return;
            }
            formPilih.querySelectorAll('button').forEach(b => {
                if (b !== btn) b.disabled = true;
            });
            if (btn) btn.textContent = 'Memproses…';
        });
    });

    const sudahDibukaAwal = @json($sudahDibukaAwal ?? false);
    const waktuBuka = {{ $jadwal && $jadwal->waktu_buka ? $jadwal->waktu_buka->timestamp : 'null' }};
    const areaCountdown = document.getElementById('area-countdown');

    if (sudahDibukaAwal || !waktuBuka || !areaCountdown) {
        return;
    }

    let serverOffset = 0;
    let lastValues = { h: null, m: null, s: null };
    let sudahReload = false;

    function setUnit(unit, value) {
        const wrap = document.querySelector(`.flip-digits[data-unit="${unit}"]`);
        if (!wrap) return;
        const str = String(value).padStart(2, '0');
        const tiles = wrap.querySelectorAll('.flip-tile');
        [...str].forEach((digit, i) => {
            if (tiles[i].textContent !== digit) {
                tiles[i].textContent = digit;
                tiles[i].classList.remove('flipping');
                void tiles[i].offsetWidth;
                tiles[i].classList.add('flipping');
            }
        });
    }

    fetch('/api/server-time')
        .then(r => r.json())
        .then(data => {
            serverOffset = data.now - Math.floor(Date.now() / 1000);
            tick();
            setInterval(tick, 1000);
        })
        .catch(() => { tick(); setInterval(tick, 1000); });

    function tick() {
        const now = Math.floor(Date.now() / 1000) + serverOffset;
        const diff = waktuBuka - now;

        if (diff <= 0) {
            setUnit('h', 0); setUnit('m', 0); setUnit('s', 0);
            checkStatusAndReveal();
            return;
        }

        const h = Math.floor(diff / 3600);
        const m = Math.floor((diff % 3600) / 60);
        const s = diff % 60;

        if (h !== lastValues.h) setUnit('h', h);
        if (m !== lastValues.m) setUnit('m', m);
        setUnit('s', s);
        lastValues = { h, m, s };
    }

    function checkStatusAndReveal() {
        if (sudahReload) return;
        fetch('/api/pemilihan-status')
            .then(r => r.json())
            .then(data => {
                if (data.sudah_dibuka && !sudahReload) {
                    sudahReload = true;
                    location.reload();
                } else if (!data.sudah_dibuka) {
                    setTimeout(checkStatusAndReveal, 2000);
                }
            })
            .catch(() => setTimeout(checkStatusAndReveal, 3000));
    }
});
</script>
@endsection
