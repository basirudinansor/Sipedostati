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
    .dosen-ticket__stub {
        border-left: 2px dashed var(--ink-line); padding: 22px 26px; display: flex; flex-direction: column;
        align-items: center; justify-content: center; gap: 10px; min-width: 176px; background: var(--paper-dim);
    }
    .quota-figure { font-family: var(--font-mono); font-size: 13px; color: var(--text-muted); }
    .quota-figure strong { color: var(--ink); font-size: 15px; }
    .quota-gauge { width: 120px; height: 6px; background: var(--ink-line); border-radius: 3px; overflow: hidden; }
    .quota-gauge__fill { height: 100%; border-radius: 3px; transition: width .4s ease; }
    .dosen-ticket.is-full { opacity: .62; }
    .dosen-ticket.is-full:hover { transform: none; box-shadow: none; border-color: var(--ink-line); }

    @media (max-width: 620px) {
        .dosen-ticket { grid-template-columns: 1fr; }
        .dosen-ticket__stub { border-left: none; border-top: 2px dashed var(--ink-line); flex-direction: row; justify-content: space-between; }
    }
</style>
@endpush

@section('content')
<span class="eyebrow">Pilih Dosen Pembimbing</span>
<h1 class="page-title">Pemilihan Dosen Pembimbing Tugas Akhir</h1>
<p class="page-subtitle">Kuota tiap dosen terbatas. Pilihan bersifat final dan tidak dapat diubah setelah dikonfirmasi.</p>

@if($sudahPilih)
    @php
        $bisaBatal = $jadwal && $jadwal->sudahDibuka();
    @endphp
    <div class="panel waiting-panel">
        <div class="waiting-panel__icon">✓</div>
        <h2>Anda sudah memilih dosen pembimbing</h2>
        <p>Silakan cek detailnya di halaman Dashboard.</p>
        @if($bisaBatal)
            <form action="{{ route('pilih-dosen.destroy') }}" method="POST" style="margin-top: 20px;"
                onsubmit="return confirm('Yakin ingin membatalkan pilihan dosen pembimbing ini?')">
                @csrf @method('DELETE')
                <button type="submit" class="btn btn--danger">Batalkan Pilihan</button>
            </form>
        @endif
    </div>
@elseif(!$jadwal || $jadwal->status === 'draft')
    <div class="panel waiting-panel">
        <div class="waiting-panel__icon">?</div>
        <h2>Belum dijadwalkan</h2>
        <p>Admin belum menetapkan jadwal pemilihan dosen pembimbing. Silakan cek kembali nanti.</p>
    </div>
@else
    @php
        $sudahDibukaAwal = $jadwal->sudahDibuka();
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
        <form action="{{ route('pilih-dosen.store') }}" method="POST" id="form-pilih">
            @csrf
            <div class="dosen-grid">
                @foreach($dosens as $i => $dosen)
                    @php
                        $pct = $dosen->kuota > 0 ? min(100, round(($dosen->kuota_terpakai / $dosen->kuota) * 100)) : 100;
                        $gaugeColor = $dosen->penuh ? 'var(--crimson)' : ($pct >= 70 ? 'var(--brass)' : 'var(--forest)');
                    @endphp
                    <div class="dosen-ticket {{ $dosen->penuh ? 'is-full' : '' }}" style="animation-delay: {{ $i * 0.05 }}s">
                        <div class="dosen-ticket__main">
                            <span class="dosen-ticket__no">{{ str_pad($i + 1, 2, '0', STR_PAD_LEFT) }}</span>
                            <div>
                                <div class="dosen-ticket__name">{{ $dosen->nama }}</div>
                                <div class="dosen-ticket__field">{{ $dosen->bidang_keahlian ?: 'Bidang keahlian belum diisi' }} · NIP {{ $dosen->nip }}</div>
                            </div>
                        </div>
                        <div class="dosen-ticket__stub">
                            <div class="quota-figure"><strong>{{ $dosen->kuota_terpakai }}</strong> / {{ $dosen->kuota }} kuota</div>
                            <div class="quota-gauge"><div class="quota-gauge__fill" style="width: {{ $pct }}%; background: {{ $gaugeColor }};"></div></div>
                            <button type="submit" name="dosen_id" value="{{ $dosen->id }}"
                                data-dosen-nama="{{ $dosen->nama }}"
                                {{ $dosen->penuh ? 'disabled' : '' }}
                                class="btn {{ $dosen->penuh ? 'btn--outline' : 'btn--primary' }}" style="padding: 9px 20px; font-size: 13.5px;">
                                {{ $dosen->penuh ? 'Kuota penuh' : 'Pilih dosen ini' }}
                            </button>
                        </div>
                    </div>
                @endforeach
            </div>
        </form>
    </div>
@endif

<script>
document.addEventListener('DOMContentLoaded', function () {
    const formPilih = document.getElementById('form-pilih');
    if (formPilih) {
        formPilih.addEventListener('submit', function (e) {
            const btn = e.submitter;

            // Tampilkan dialog konfirmasi sebelum benar-benar mengirim pilihan.
            const nama = btn ? btn.dataset.dosenNama : null;
            const pesan = nama
                ? `Apakah Anda yakin memilih ${nama} sebagai dosen pembimbing? Pilihan ini final selama pemilihan masih dibuka.`
                : 'Apakah Anda yakin dengan pilihan ini?';
            if (!confirm(pesan)) {
                e.preventDefault();
                return;
            }

            // PENTING: jangan disable tombol yang baru diklik (btn) itu sendiri,
            // karena browser tidak akan mengirim value dari tombol yang sudah disabled.
            // Cukup disable tombol-tombol LAIN supaya tidak bisa diklik ganda.
            formPilih.querySelectorAll('button').forEach(b => {
                if (b !== btn) b.disabled = true;
            });
            if (btn) btn.textContent = 'Memproses…';
        });
    }

    // PENTING: kalau pemilihan SUDAH dibuka saat halaman ini dimuat,
    // jangan jalankan countdown/polling sama sekali -> mencegah reload berulang.
    const sudahDibukaAwal = @json($sudahDibukaAwal ?? false);
    const waktuBuka = {{ $jadwal && $jadwal->waktu_buka ? $jadwal->waktu_buka->timestamp : 'null' }};
    const areaCountdown = document.getElementById('area-countdown');

    if (sudahDibukaAwal || !waktuBuka || !areaCountdown) {
        return; // stop di sini, tidak ada countdown/polling yang perlu jalan
    }

    let serverOffset = 0;
    let lastValues = { h: null, m: null, s: null };
    let sudahReload = false; // pengaman tambahan: reload cuma boleh terjadi sekali

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
        if (sudahReload) return; // sudah pernah reload sekali, jangan reload lagi
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
