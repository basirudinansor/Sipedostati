<?php

namespace App\Http\Controllers;

use App\Models\JadwalPemilihan;
use App\Models\Pilihan;

class DashboardController extends Controller
{
    public function index()
    {
        $milikSaya = Pilihan::with('dosen')
            ->where('mahasiswa_id', auth()->id())
            ->get()
            ->keyBy('jenis');

        $pilihan1 = $milikSaya->get('pembimbing_1');
        $pilihan2 = $milikSaya->get('pembimbing_2');

        $jadwal = JadwalPemilihan::aktif();
        $bisaBatal = $jadwal && $jadwal->sudahDibuka();

        return view('dashboard', compact('pilihan1', 'pilihan2', 'bisaBatal'));
    }
}
