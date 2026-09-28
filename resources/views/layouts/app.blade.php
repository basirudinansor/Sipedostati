<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Pemilihan Dosen Pembimbing')</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Fraunces:opsz,wght@9..144,400;9..144,500;9..144,600;9..144,700&family=IBM+Plex+Sans:wght@400;500;600;700&family=IBM+Plex+Mono:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        :root {
            --ink: #14213D;
            --ink-soft: #223259;
            --ink-line: rgba(20, 33, 61, .14);
            --paper: #FBF9F4;
            --paper-dim: #F1EEE2;
            --brass: #B8912E;
            --brass-bright: #D8B24C;
            --crimson: #A6303F;
            --forest: #2E6B4F;
            --text: #1C1B18;
            --text-muted: #6B6A62;
            --shadow-sm: 0 2px 8px rgba(20, 33, 61, .08);
            --shadow-md: 0 12px 32px rgba(20, 33, 61, .14);
            --font-display: 'Fraunces', Georgia, serif;
            --font-body: 'IBM Plex Sans', -apple-system, sans-serif;
            --font-mono: 'IBM Plex Mono', 'Courier New', monospace;
        }

        * { box-sizing: border-box; }
        html { scroll-behavior: smooth; }
        body {
            margin: 0; min-height: 100vh;
            background: var(--paper); color: var(--text);
            font-family: var(--font-body); font-size: 16.5px; line-height: 1.6;
            -webkit-font-smoothing: antialiased;
        }
        h1, h2, h3 { font-family: var(--font-display); color: var(--ink); margin: 0; letter-spacing: -.01em; }
        a { color: inherit; }

        /* ---------- Header (full width) ---------- */
        .site-header { background: var(--ink); border-bottom: 3px solid var(--brass); }
        .site-header__inner {
            width: 100%;
            margin: 0; padding: 18px clamp(24px, 3.5vw, 64px);
            display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 14px;
        }
        .brand { display: flex; align-items: center; gap: 12px; text-decoration: none; }
        .brand__seal {
            width: 40px; height: 40px; border-radius: 50%;
            border: 2px solid var(--brass);
            display: flex; align-items: center; justify-content: center;
            font-family: var(--font-display); font-weight: 600; font-size: 15px;
            color: var(--brass-bright); flex-shrink: 0;
        }
        .brand__text { display: flex; flex-direction: column; line-height: 1.15; }
        .brand__eyebrow { font-family: var(--font-mono); font-size: 11px; letter-spacing: .16em; color: var(--brass-bright); text-transform: uppercase; }
        .brand__title { font-family: var(--font-display); font-size: 19px; color: var(--paper); font-weight: 600; }
        .site-nav { display: flex; align-items: center; gap: 26px; flex-wrap: wrap; }
        .site-nav a {
            font-size: 14.5px; color: rgba(251, 249, 244, .78); text-decoration: none;
            position: relative; padding-bottom: 4px; transition: color .2s ease;
        }
        .site-nav a::after {
            content: ''; position: absolute; left: 0; right: 100%; bottom: 0;
            height: 2px; background: var(--brass-bright); transition: right .25s ease;
        }
        .site-nav a:hover { color: var(--paper); }
        .site-nav a:hover::after { right: 0; }
        .site-nav a.active { color: var(--brass-bright); }
        .site-nav form button {
            background: transparent; border: 1px solid rgba(251, 249, 244, .3);
            color: rgba(251, 249, 244, .85); font-family: var(--font-body); font-size: 13.5px;
            padding: 7px 14px; border-radius: 3px; cursor: pointer; transition: all .2s ease;
        }
        .site-nav form button:hover { border-color: var(--crimson); color: #fff; background: var(--crimson); }

        /* ---------- Layout shell (full width, no wasted margins) ---------- */
        .page { width: 100%; margin: 0; padding: 44px clamp(24px, 3.5vw, 64px) 96px; }
        .page--narrow { max-width: 760px; margin: 0 auto; }
        .page--full { padding: 0; }

        .eyebrow {
            font-family: var(--font-mono); font-size: 12.5px; letter-spacing: .14em;
            text-transform: uppercase; color: var(--brass); margin-bottom: 10px; display: block;
        }
        .page-title { font-size: clamp(28px, 3vw, 40px); margin-bottom: 8px; }
        .page-subtitle { color: var(--text-muted); font-size: 16px; margin: 0 0 36px; max-width: 70ch; }

        /* ---------- Alerts ---------- */
        .alert {
            border-radius: 4px; padding: 14px 18px; font-size: 14.5px; margin-bottom: 24px;
            border-left: 3px solid transparent; display: flex; align-items: flex-start; gap: 10px;
            animation: slideDown .35s ease;
        }
        .alert--success { background: #EAF3ED; border-color: var(--forest); color: #1F4A35; }
        .alert--error { background: #F7E9EA; border-color: var(--crimson); color: #7A2530; }
        .page--full > .alert { margin: 20px clamp(24px, 3.5vw, 64px) 0; }
        @keyframes slideDown { from { opacity: 0; transform: translateY(-8px);} to { opacity: 1; transform: translateY(0);} }

        /* ---------- Cards ---------- */
        .panel { background: #fff; border: 1px solid var(--ink-line); border-radius: 6px; box-shadow: var(--shadow-sm); }
        .panel--pad { padding: 32px; }

        /* ---------- Buttons ---------- */
        .btn {
            font-family: var(--font-body); font-weight: 600; font-size: 14.5px;
            padding: 12px 24px; border-radius: 3px; border: 1.5px solid var(--ink); cursor: pointer;
            display: inline-flex; align-items: center; gap: 8px;
            transition: transform .15s ease, box-shadow .15s ease, background .2s ease, color .2s ease;
            text-decoration: none;
        }
        .btn--primary { background: var(--ink); color: var(--paper); }
        .btn--primary:hover:not(:disabled) { background: var(--brass); border-color: var(--brass); color: var(--ink); transform: translateY(-2px); box-shadow: var(--shadow-md); }
        .btn--outline { background: transparent; color: var(--ink); }
        .btn--outline:hover:not(:disabled) { background: var(--ink); color: var(--paper); }
        .btn--danger { border-color: var(--crimson); color: var(--crimson); background: transparent; }
        .btn--danger:hover { background: var(--crimson); color: #fff; }
        .btn:disabled { opacity: .45; cursor: not-allowed; transform: none !important; box-shadow: none !important; }

        /* ---------- Forms ---------- */
        label { display: block; font-size: 13px; font-weight: 600; color: var(--ink); margin-bottom: 6px; letter-spacing: .01em; }
        input[type="text"], input[type="email"], input[type="password"],
        input[type="number"], input[type="datetime-local"] {
            width: 100%; padding: 12px 14px; border: 1.5px solid var(--ink-line); border-radius: 4px;
            font-family: var(--font-body); font-size: 15px; background: var(--paper);
            transition: border-color .2s ease, box-shadow .2s ease;
        }
        input:focus { outline: none; border-color: var(--brass); box-shadow: 0 0 0 3px rgba(184, 145, 46, .18); }

        /* ---------- Footer ---------- */
        .site-footer { text-align: center; padding: 28px; font-size: 12.5px; color: var(--text-muted); font-family: var(--font-mono); }

        /* ---------- Utility ---------- */
        .stamp {
            display: inline-flex; align-items: center; gap: 8px;
            font-family: var(--font-mono); font-size: 12.5px; letter-spacing: .1em; text-transform: uppercase;
            font-weight: 600; padding: 8px 16px; border: 2px solid var(--forest); color: var(--forest);
            border-radius: 4px; transform: rotate(-2deg);
        }

        @media (max-width: 720px) {
            .page { padding: 32px 20px 64px; }
        }
        @media (prefers-reduced-motion: reduce) {
            *, *::before, *::after { animation-duration: .001ms !important; transition-duration: .001ms !important; }
        }
    </style>
    @stack('styles')
</head>
<body>
    @auth
    <header class="site-header">
        <div class="site-header__inner">
            <a href="{{ auth()->user()->isAdmin() ? route('admin.dashboard') : route('dashboard') }}" class="brand">
                <span class="brand__seal">PD</span>
                <span class="brand__text">
                    <span class="brand__eyebrow">Sistem Akademik</span>
                    <span class="brand__title">Pemilihan Dosbing</span>
                </span>
            </a>
            <nav class="site-nav">
                @if(auth()->user()->isAdmin())
                    <a href="{{ route('admin.dashboard') }}" class="{{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">Dashboard</a>
                    <a href="{{ route('admin.mahasiswa.index') }}" class="{{ request()->routeIs('admin.mahasiswa.*') ? 'active' : '' }}">Kelola Mahasiswa</a>
                    <a href="{{ route('admin.dosen.index') }}" class="{{ request()->routeIs('admin.dosen.*') ? 'active' : '' }}">Kelola Dosen</a>
                    <a href="{{ route('admin.jadwal.index') }}" class="{{ request()->routeIs('admin.jadwal.*') ? 'active' : '' }}">Kelola Jadwal</a>
                @else
                    <a href="{{ route('dashboard') }}" class="{{ request()->routeIs('dashboard') ? 'active' : '' }}">Dashboard</a>
                    <a href="{{ route('pilih-dosen') }}" class="{{ request()->routeIs('pilih-dosen') ? 'active' : '' }}">Pilih Dosen Pembimbing</a>
                    <a href="{{ route('password.change.form') }}">Ganti Password</a>
                @endif
                <form action="{{ route('logout') }}" method="POST">
                    @csrf
                    <button type="submit">Keluar</button>
                </form>
            </nav>
        </div>
    </header>
    @endauth

    <div class="page @yield('page-class')">
        @if(session('success'))
            <div class="alert alert--success">{{ session('success') }}</div>
        @endif
        @if($errors->any())
            <div class="alert alert--error">
                @foreach($errors->all() as $error)
                    <div>{{ $error }}</div>
                @endforeach
            </div>
        @endif

        @yield('content')
    </div>

    @auth
    <footer class="site-footer">Sistem Pemilihan Dosen Pembimbing Tugas Akhir</footer>
    @endauth
</body>
</html>
