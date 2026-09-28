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

        $milikSaya = Pilihan::with('dosen')
            ->where('mahasiswa_id', auth()->id())
            ->get()
            ->keyBy('jenis');

        $pilihan1 = $milikSaya->get('pembimbing_1');
        $pilihan2 = $milikSaya->get('pembimbing_2');

        $dosens = Dosen::orderBy('nama')->get();

        return view('pilih-dosen', compact('jadwal', 'pilihan1', 'pilihan2', 'dosens'));
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
        $request->validate([
            'dosen_id' => 'required|exists:dosens,id',
            'jenis' => 'required|in:pembimbing_1,pembimbing_2',
        ]);

        $jadwal = JadwalPemilihan::aktif();
        if (!$jadwal || !$jadwal->sudahDibuka()) {
            return back()->withErrors('Pemilihan belum dibuka.');
        }

        $mahasiswaId = auth()->id();
        $jenis = $request->jenis;
        $labelJenis = $jenis === 'pembimbing_1' ? 'Pembimbing 1' : 'Pembimbing 2';
        $kolomTerpakai = $jenis === 'pembimbing_1' ? 'kuota_terpakai_p1' : 'kuota_terpakai_p2';

        if (Pilihan::where('mahasiswa_id', $mahasiswaId)->where('jenis', $jenis)->exists()) {
            return back()->withErrors("Anda sudah memilih {$labelJenis}.");
        }

        // Cek dosen yang sama tidak boleh dipilih untuk kedua slot sekaligus
        $jenisLain = $jenis === 'pembimbing_1' ? 'pembimbing_2' : 'pembimbing_1';
        $labelLain = $jenisLain === 'pembimbing_1' ? 'Pembimbing 1' : 'Pembimbing 2';
        $pilihanLain = Pilihan::where('mahasiswa_id', $mahasiswaId)->where('jenis', $jenisLain)->first();

        if ($pilihanLain && (int) $pilihanLain->dosen_id === (int) $request->dosen_id) {
            return back()->withErrors("Dosen ini sudah Anda pilih sebagai {$labelLain}. Silakan pilih dosen yang berbeda untuk {$labelJenis}.");
        }

        try {
            DB::transaction(function () use ($request, $mahasiswaId, $jenis, $kolomTerpakai, $labelJenis) {
                $dosen = Dosen::where('id', $request->dosen_id)
                    ->lockForUpdate()
                    ->first();

                if (!$dosen || $dosen->$kolomTerpakai >= $dosen->kuota) {
                    throw new \RuntimeException(
                        "Mohon maaf, kuota dosen ini untuk peran {$labelJenis} baru saja penuh. Silakan pilih dosen lain."
                    );
                }

                Pilihan::create([
                    'mahasiswa_id' => $mahasiswaId,
                    'dosen_id' => $dosen->id,
                    'jenis' => $jenis,
                ]);

                $dosen->increment($kolomTerpakai);
            }, 3);
        } catch (\Illuminate\Database\QueryException $e) {
            Log::warning('Pilihan gagal karena constraint: ' . $e->getMessage());
            return back()->withErrors("Anda sudah memilih {$labelJenis}.");
        } catch (\RuntimeException $e) {
            return back()->withErrors($e->getMessage());
        }

        // Setelah Pembimbing 1 selesai dipilih -> otomatis balik ke halaman ini
        // supaya lanjut pilih Pembimbing 2. Setelah Pembimbing 2 selesai -> ke Dashboard.
        if ($jenis === 'pembimbing_1') {
            return redirect()->route('pilih-dosen')
                ->with('success', 'Pembimbing 1 berhasil dipilih! Sekarang silakan pilih Pembimbing 2.');
        }

        return redirect()->route('dashboard')
            ->with('success', 'Pembimbing 2 berhasil dipilih! Kedua dosen pembimbing Anda sudah lengkap.');
    }

    /**
     * Batalkan pilihan untuk satu slot tertentu (pembimbing_1 atau pembimbing_2).
     * Hanya boleh selama jadwal pemilihan masih berstatus dibuka.
     */
    public function destroy(Request $request)
    {
        $request->validate(['jenis' => 'required|in:pembimbing_1,pembimbing_2']);

        $jadwal = JadwalPemilihan::aktif();
        if (!$jadwal || !$jadwal->sudahDibuka()) {
            return back()->withErrors('Pembatalan tidak bisa dilakukan karena periode pemilihan sudah/belum dibuka.');
        }

        $pilihan = Pilihan::where('mahasiswa_id', auth()->id())
            ->where('jenis', $request->jenis)
            ->first();

        if (!$pilihan) {
            return back()->withErrors('Anda belum memiliki pilihan untuk slot ini.');
        }

        $kolomTerpakai = $pilihan->jenis === 'pembimbing_1' ? 'kuota_terpakai_p1' : 'kuota_terpakai_p2';

        DB::transaction(function () use ($pilihan, $kolomTerpakai) {
            $dosen = Dosen::where('id', $pilihan->dosen_id)->lockForUpdate()->first();

            $pilihan->delete();

            if ($dosen && $dosen->$kolomTerpakai > 0) {
                $dosen->decrement($kolomTerpakai);
            }
        });

        return redirect()->route('dashboard')->with('success', 'Pilihan berhasil dibatalkan. Anda bisa memilih ulang selama pemilihan masih dibuka.');
    }
}
