@extends('layouts.app')
@section('title', 'Ganti Password')
@section('page-class', 'page--narrow')

@section('content')
<span class="eyebrow">Keamanan Akun</span>
<h1 class="page-title">Ganti Kata Sandi</h1>
<p class="page-subtitle">
    Ini login pertama Anda (atau password baru saja direset admin). Silakan buat kata sandi baru sebelum melanjutkan.
</p>

<div class="panel panel--pad">
    <form action="{{ route('password.change.update') }}" method="POST">
        @csrf
        <div style="margin-bottom: 18px;">
            <label for="current_password">Kata sandi saat ini</label>
            <input type="password" id="current_password" name="current_password" required placeholder="Kata sandi lama / NIM Anda">
        </div>
        <div style="margin-bottom: 18px;">
            <label for="password">Kata sandi baru</label>
            <input type="password" id="password" name="password" required minlength="6" placeholder="Minimal 6 karakter">
        </div>
        <div style="margin-bottom: 24px;">
            <label for="password_confirmation">Ulangi kata sandi baru</label>
            <input type="password" id="password_confirmation" name="password_confirmation" required minlength="6">
        </div>
        <button type="submit" class="btn btn--primary">Simpan kata sandi baru</button>
    </form>
</div>
@endsection
