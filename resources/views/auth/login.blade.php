@extends('layouts.app')
@section('title', 'Masuk — Pemilihan Dosbing')
@section('page-class', 'page--full')

@push('styles')
<style>
    body { overflow-x: hidden; }
    .login-shell { min-height: 100vh; display: grid; grid-template-columns: 1.1fr 1fr; }
    .login-side {
        background: var(--ink); color: var(--paper); padding: 64px;
        display: flex; flex-direction: column; justify-content: space-between;
        position: relative; overflow: hidden;
    }
    #neuron-canvas {
        position: absolute; inset: 0; width: 100%; height: 100%;
        display: block; z-index: 0;
    }
    .login-side__top, .login-side__bottom { position: relative; z-index: 1; pointer-events: none; }
    .login-side__eyebrow {
        font-family: var(--font-mono); font-size: 12.5px; letter-spacing: .18em; text-transform: uppercase;
        color: var(--brass-bright); opacity: 0; animation: fadeUp .6s ease .1s forwards;
    }
    .login-side__title {
        font-family: var(--font-display); font-size: clamp(38px, 5vw, 58px); line-height: 1.06;
        margin: 18px 0 22px; max-width: 11ch; opacity: 0; animation: fadeUp .7s ease .25s forwards;
    }
    .login-side__desc {
        font-size: 15.5px; color: rgba(251, 249, 244, .72); max-width: 40ch; line-height: 1.7;
        opacity: 0; animation: fadeUp .7s ease .4s forwards;
    }
    @keyframes fadeUp { from { opacity: 0; transform: translateY(14px);} to { opacity: 1; transform: translateY(0);} }
    .login-side__ticket {
        border-top: 1px dashed rgba(251, 249, 244, .3); padding-top: 22px; display: flex; gap: 32px;
        opacity: 0; animation: fadeUp .7s ease .55s forwards;
    }
    .login-side__ticket div { font-family: var(--font-mono); }
    .login-side__ticket .num { font-size: 22px; color: var(--brass-bright); font-weight: 600; }
    .login-side__ticket .label { font-size: 11px; letter-spacing: .1em; text-transform: uppercase; color: rgba(251, 249, 244, .55); margin-top: 2px; }

    .login-form-side { display: flex; align-items: center; justify-content: center; padding: 48px; background: var(--paper); }
    .login-form-card { width: 100%; max-width: 380px; opacity: 0; animation: fadeUp .6s ease .3s forwards; }
    .login-form-card h1 { font-size: 26px; margin-bottom: 6px; }
    .login-form-card p.hint { color: var(--text-muted); font-size: 14px; margin: 0 0 32px; }
    .field { margin-bottom: 18px; }
    .login-form-card .btn { width: 100%; justify-content: center; margin-top: 8px; padding: 13px; }

    @media (max-width: 880px) {
        .login-shell { grid-template-columns: 1fr; }
        .login-side { padding: 48px 32px; min-height: 320px; }
        .login-side__ticket { display: none; }
    }
</style>
@endpush

@section('content')
<div class="login-shell">
    <div class="login-side" id="neuron-host">
        <canvas id="neuron-canvas"></canvas>
        <div class="login-side__top">
            <span class="login-side__eyebrow">Sistem Akademik · Tugas Akhir</span>
            <h1 class="login-side__title">Pemilihan Dosen Pembimbing</h1>
            <p class="login-side__desc">
                Satu tempat untuk melihat status bimbingan Anda dan mengikuti
                pemilihan dosen pembimbing saat dibuka oleh admin.
            </p>
        </div>
        <div class="login-side__bottom">
            <div class="login-side__ticket">
                <div><div class="num">01</div><div class="label">Login</div></div>
                <div><div class="num">02</div><div class="label">Tunggu jadwal</div></div>
                <div><div class="num">03</div><div class="label">Pilih dosen</div></div>
            </div>
        </div>
    </div>

    <div class="login-form-side">
        <div class="login-form-card">
            <h1>Masuk</h1>
            <p class="hint">Mahasiswa: login pakai NIM. Admin: login pakai email.</p>
            <form action="{{ route('login') }}" method="POST">
                @csrf
                <div class="field">
                    <label for="identifier">NIM / Email</label>
                    <input type="text" id="identifier" name="identifier" value="{{ old('identifier') }}" required autofocus placeholder="Contoh: C2F023001">
                </div>
                <div class="field">
                    <label for="password">Kata sandi</label>
                    <input type="password" id="password" name="password" required placeholder="••••••••">
                </div>
                <button class="btn btn--primary" type="submit">Masuk ke sistem</button>
            </form>
        </div>
    </div>
</div>

<script>
(function () {
    const canvas = document.getElementById('neuron-canvas');
    const host = document.getElementById('neuron-host');
    const ctx = canvas.getContext('2d');
    const reduceMotion = window.matchMedia('(prefers-reduced-motion: reduce)').matches;

    let width, height, dpr;
    let nodes = [];
    let mouse = { x: null, y: null, active: false };

    const NODE_COUNT_BASE = 70;      // jumlah node per 1.000.000 px^2, disesuaikan luas layar
    const LINK_DIST = 130;           // jarak maksimal antar-node supaya digambar garis
    const MOUSE_DIST = 190;          // jarak maksimal node ke mouse supaya digambar garis
    const BRASS = '184, 145, 46';    // warna aksen (brass) dalam format "r, g, b"
    const BRASS_BRIGHT = '216, 178, 76';

    function resize() {
        dpr = window.devicePixelRatio || 1;
        width = host.clientWidth;
        height = host.clientHeight;
        canvas.width = width * dpr;
        canvas.height = height * dpr;
        canvas.style.width = width + 'px';
        canvas.style.height = height + 'px';
        ctx.setTransform(dpr, 0, 0, dpr, 0, 0);

        const area = width * height;
        const count = Math.max(24, Math.min(110, Math.round((area / 1000000) * NODE_COUNT_BASE)));

        nodes = Array.from({ length: count }, () => ({
            x: Math.random() * width,
            y: Math.random() * height,
            vx: (Math.random() - 0.5) * 0.35,
            vy: (Math.random() - 0.5) * 0.35,
            r: Math.random() * 1.6 + 1,
        }));
    }

    function step() {
        ctx.clearRect(0, 0, width, height);

        // Update posisi node, pantul di tepi
        nodes.forEach(n => {
            n.x += n.vx;
            n.y += n.vy;
            if (n.x < 0 || n.x > width) n.vx *= -1;
            if (n.y < 0 || n.y > height) n.vy *= -1;
            n.x = Math.max(0, Math.min(width, n.x));
            n.y = Math.max(0, Math.min(height, n.y));
        });

        // Garis penghubung antar node yang berdekatan
        for (let i = 0; i < nodes.length; i++) {
            for (let j = i + 1; j < nodes.length; j++) {
                const dx = nodes[i].x - nodes[j].x;
                const dy = nodes[i].y - nodes[j].y;
                const dist = Math.sqrt(dx * dx + dy * dy);
                if (dist < LINK_DIST) {
                    const alpha = (1 - dist / LINK_DIST) * 0.35;
                    ctx.strokeStyle = `rgba(${BRASS}, ${alpha})`;
                    ctx.lineWidth = 1;
                    ctx.beginPath();
                    ctx.moveTo(nodes[i].x, nodes[i].y);
                    ctx.lineTo(nodes[j].x, nodes[j].y);
                    ctx.stroke();
                }
            }
        }

        // Garis dari node ke posisi mouse (efek "mengikuti kursor")
        if (mouse.active) {
            nodes.forEach(n => {
                const dx = n.x - mouse.x;
                const dy = n.y - mouse.y;
                const dist = Math.sqrt(dx * dx + dy * dy);
                if (dist < MOUSE_DIST) {
                    const alpha = (1 - dist / MOUSE_DIST) * 0.65;
                    ctx.strokeStyle = `rgba(${BRASS_BRIGHT}, ${alpha})`;
                    ctx.lineWidth = 1.1;
                    ctx.beginPath();
                    ctx.moveTo(n.x, n.y);
                    ctx.lineTo(mouse.x, mouse.y);
                    ctx.stroke();

                    // sedikit tarikan halus ke arah mouse, biar terasa "hidup"
                    n.vx += (mouse.x - n.x) * 0.00003;
                    n.vy += (mouse.y - n.y) * 0.00003;
                }
            });

            // Titik pusat di posisi mouse
            ctx.beginPath();
            ctx.arc(mouse.x, mouse.y, 2.5, 0, Math.PI * 2);
            ctx.fillStyle = `rgba(${BRASS_BRIGHT}, 0.9)`;
            ctx.fill();
        }

        // Gambar node
        nodes.forEach(n => {
            ctx.beginPath();
            ctx.arc(n.x, n.y, n.r, 0, Math.PI * 2);
            ctx.fillStyle = `rgba(${BRASS_BRIGHT}, 0.55)`;
            ctx.fill();
        });

        if (!reduceMotion) {
            requestAnimationFrame(step);
        }
    }

    host.addEventListener('mousemove', (e) => {
        const rect = host.getBoundingClientRect();
        mouse.x = e.clientX - rect.left;
        mouse.y = e.clientY - rect.top;
        mouse.active = true;
    });
    host.addEventListener('mouseleave', () => { mouse.active = false; });

    window.addEventListener('resize', resize);
    resize();

    if (reduceMotion) {
        step(); // gambar satu frame statis saja, tanpa animasi berjalan terus
    } else {
        requestAnimationFrame(step);
    }
})();
</script>
@endsection
