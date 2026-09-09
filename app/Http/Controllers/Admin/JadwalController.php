<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\JadwalPemilihan;
use Illuminate\Http\Request;

class JadwalController extends Controller
{
    public function index()
    {
        $jadwal = JadwalPemilihan::aktif();

        return view('admin.jadwal.index', compact('jadwal'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'waktu_buka' => 'required|date',
            'waktu_tutup' => 'nullable|date|after:waktu_buka',
        ]);

        JadwalPemilihan::create([
            'waktu_buka' => $data['waktu_buka'],
            'waktu_tutup' => $data['waktu_tutup'] ?? null,
            'status' => 'terjadwal',
        ]);

        return back()->with('success', 'Jadwal pemilihan berhasil diset.');
    }

    public function tutup(JadwalPemilihan $jadwal)
    {
        $jadwal->update(['status' => 'ditutup']);

        return back()->with('success', 'Pemilihan ditutup.');
    }
}
