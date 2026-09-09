<?php

namespace App\Http\Controllers;

use App\Models\JadwalPemilihan;
use App\Models\Pilihan;

class DashboardController extends Controller
{
    public function index()
    {
        $pilihan = Pilihan::with('dosen')
            ->where('mahasiswa_id', auth()->id())
            ->first();

        $jadwal = JadwalPemilihan::aktif();
        $bisaBatal = $pilihan && $jadwal && $jadwal->sudahDibuka();

        return view('dashboard', compact('pilihan', 'bisaBatal'));
    }
}
