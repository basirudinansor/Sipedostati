<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Dosen;
use App\Models\JadwalPemilihan;
use App\Models\User;

class DashboardController extends Controller
{
    public function index()
    {
        $totalMahasiswa = User::where('role', 'mahasiswa')->count();
        $totalDosen = Dosen::count();

        $sudahP1 = User::where('role', 'mahasiswa')
            ->whereHas('pilihans', fn ($q) => $q->where('jenis', 'pembimbing_1'))
            ->count();

        $sudahP2 = User::where('role', 'mahasiswa')
            ->whereHas('pilihans', fn ($q) => $q->where('jenis', 'pembimbing_2'))
            ->count();

        $sudahLengkap = User::where('role', 'mahasiswa')
            ->whereHas('pilihans', fn ($q) => $q->where('jenis', 'pembimbing_1'))
            ->whereHas('pilihans', fn ($q) => $q->where('jenis', 'pembimbing_2'))
            ->count();

        $p1Saja = User::where('role', 'mahasiswa')
            ->whereHas('pilihans', fn ($q) => $q->where('jenis', 'pembimbing_1'))
            ->whereDoesntHave('pilihans', fn ($q) => $q->where('jenis', 'pembimbing_2'))
            ->count();

        $p2Saja = User::where('role', 'mahasiswa')
            ->whereHas('pilihans', fn ($q) => $q->where('jenis', 'pembimbing_2'))
            ->whereDoesntHave('pilihans', fn ($q) => $q->where('jenis', 'pembimbing_1'))
            ->count();

        $sebagian = $p1Saja + $p2Saja;

        $belumSamaSekali = User::where('role', 'mahasiswa')
            ->whereDoesntHave('pilihans')
            ->count();

        $persenLengkap = $totalMahasiswa > 0
            ? round(($sudahLengkap / $totalMahasiswa) * 100)
            : 0;

        // Grafik dan total terisi dihitung langsung dari tabel pilihans agar selalu sinkron.
        $dosens = Dosen::withCount([
            'pilihans as pilihan_p1_count' => fn ($q) => $q->where('jenis', 'pembimbing_1'),
            'pilihans as pilihan_p2_count' => fn ($q) => $q->where('jenis', 'pembimbing_2'),
        ])
            ->orderByDesc('pilihan_p1_count')
            ->get();

        $totalKuota = (int) $dosens->sum('kuota') * 2;
        $totalTerpakai = (int) $dosens->sum('pilihan_p1_count') + (int) $dosens->sum('pilihan_p2_count');
        $persenTerisi = $totalKuota > 0
            ? round(($totalTerpakai / $totalKuota) * 100)
            : 0;

        $jadwal = JadwalPemilihan::aktif();

        return view('admin.dashboard', compact(
            'totalMahasiswa',
            'totalDosen',
            'totalKuota',
            'totalTerpakai',
            'persenTerisi',
            'sudahP1',
            'sudahP2',
            'sudahLengkap',
            'sebagian',
            'belumSamaSekali',
            'persenLengkap',
            'dosens',
            'jadwal'
        ));
    }
}
