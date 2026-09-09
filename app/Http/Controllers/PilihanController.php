<?php

namespace App\Http\Controllers;

use App\Models\Dosen;
use App\Models\JadwalPemilihan;
use App\Models\Pilihan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class PilihanController extends Controller
{
    public function index()
    {
        $jadwal = JadwalPemilihan::aktif();
        $sudahPilih = Pilihan::where('mahasiswa_id', auth()->id())->exists();
        $dosens = Dosen::orderBy('nama')->get();

        return view('pilih-dosen', compact('jadwal', 'sudahPilih', 'dosens'));
    }

    public function serverTime()
    {
        return response()->json(['now' => now()->timestamp]);
    }

    public function status()
    {
        $jadwal = JadwalPemilihan::aktif();

        return response()->json([
            'status' => $jadwal->status ?? 'draft',
            'waktu_buka' => $jadwal?->waktu_buka?->timestamp,
            'sudah_dibuka' => $jadwal ? $jadwal->sudahDibuka() : false,
        ]);
    }

    public function store(Request $request)
    {
        $request->validate(['dosen_id' => 'required|exists:dosens,id']);

        $jadwal = JadwalPemilihan::aktif();
        if (!$jadwal || !$jadwal->sudahDibuka()) {
            return back()->withErrors('Pemilihan belum dibuka.');
        }

        $mahasiswaId = auth()->id();

        if (Pilihan::where('mahasiswa_id', $mahasiswaId)->exists()) {
            return back()->withErrors('Anda sudah memilih dosen pembimbing.');
        }

        try {
            DB::transaction(function () use ($request, $mahasiswaId) {
                $dosen = Dosen::where('id', $request->dosen_id)
                    ->lockForUpdate()
                    ->first();

                if (!$dosen || $dosen->kuota_terpakai >= $dosen->kuota) {
                    throw new \RuntimeException(
                        'Mohon maaf, kuota dosen ini baru saja penuh. Silakan pilih dosen lain.'
                    );
                }

                Pilihan::create([
                    'mahasiswa_id' => $mahasiswaId,
                    'dosen_id' => $dosen->id,
                ]);

                $dosen->increment('kuota_terpakai');
            }, 3);
        } catch (\Illuminate\Database\QueryException $e) {
            Log::warning('Pilihan gagal karena constraint: ' . $e->getMessage());

            return back()->withErrors('Anda sudah memilih dosen pembimbing.');
        } catch (\RuntimeException $e) {
            return back()->withErrors($e->getMessage());
        }

        return redirect()->route('dashboard')->with('success', 'Berhasil memilih dosen pembimbing!');
    }

    /**
     * Batalkan pilihan dosen pembimbing mahasiswa yang sedang login.
     * Hanya boleh selama jadwal pemilihan masih berstatus "dibuka" (belum ditutup admin).
     */
    public function destroy()
    {
        $jadwal = JadwalPemilihan::aktif();

        if (!$jadwal || !$jadwal->sudahDibuka()) {
            return back()->withErrors('Pembatalan tidak bisa dilakukan karena periode pemilihan sudah/belum dibuka.');
        }

        $pilihan = Pilihan::where('mahasiswa_id', auth()->id())->first();

        if (!$pilihan) {
            return back()->withErrors('Anda belum memiliki pilihan dosen pembimbing.');
        }

        DB::transaction(function () use ($pilihan) {
            $dosen = Dosen::where('id', $pilihan->dosen_id)->lockForUpdate()->first();

            $pilihan->delete();

            if ($dosen && $dosen->kuota_terpakai > 0) {
                $dosen->decrement('kuota_terpakai');
            }
        });

        return redirect()->route('dashboard')->with('success', 'Pilihan dosen pembimbing berhasil dibatalkan. Anda bisa memilih ulang selama pemilihan masih dibuka.');
    }
}
