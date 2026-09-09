<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Dosen;
use App\Models\JadwalPemilihan;
use App\Models\Pilihan;
use App\Models\User;

class DashboardController extends Controller
{
    public function index()
    {
        $totalMahasiswa = User::where('role', 'mahasiswa')->count();
        $totalDosen = Dosen::count();
        $totalKuota = (int) Dosen::sum('kuota');
        $totalTerpakai = (int) Dosen::sum('kuota_terpakai');
        $sudahPilih = Pilihan::count();
        $belumPilih = max(0, $totalMahasiswa - $sudahPilih);
        $persenTerisi = $totalKuota > 0 ? round(($totalTerpakai / $totalKuota) * 100) : 0;
        $persenSudahPilih = $totalMahasiswa > 0 ? round(($sudahPilih / $totalMahasiswa) * 100) : 0;

        $dosens = Dosen::orderByDesc('kuota_terpakai')->get();
        $jadwal = JadwalPemilihan::aktif();

        return view('admin.dashboard', compact(
            'totalMahasiswa',
            'totalDosen',
            'totalKuota',
            'totalTerpakai',
            'sudahPilih',
            'belumPilih',
            'persenTerisi',
            'persenSudahPilih',
            'dosens',
            'jadwal'
        ));
    }
}
