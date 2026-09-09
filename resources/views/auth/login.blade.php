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
    .login-side::before {
        content: ''; position: absolute; inset: 0;
        background-image: radial-gradient(rgba(216, 178, 76, .18) 1px, transparent 1px);
        background-size: 26px 26px; opacity: .5; animation: driftGrid 40s linear infinite;
    }
    @keyframes driftGrid { from { background-position: 0 0; } to { background-position: 260px 260px; } }
    .login-side__top, .login-side__bottom { position: relative; z-index: 1; }
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
        .login-side { padding: 48px 32px; min-height: 260px; }
        .login-side__ticket { display: none; }
    }
</style>
@endpush

@section('content')
<div class="login-shell">
    <div class="login-side">
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
@endsection
