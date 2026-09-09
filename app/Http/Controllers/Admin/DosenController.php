<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Dosen;
use Illuminate\Http\Request;

class DosenController extends Controller
{
    public function index()
    {
        $dosens = Dosen::orderBy('nama')->get();

        return view('admin.dosen.index', compact('dosens'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'nama' => 'required|string|max:255',
            'nip' => 'required|string|unique:dosens,nip',
            'bidang_keahlian' => 'nullable|string|max:255',
            'kuota' => 'required|integer|min:0',
        ]);

        Dosen::create($data);

        return back()->with('success', 'Dosen berhasil ditambahkan.');
    }

    public function update(Request $request, Dosen $dosen)
    {
        $data = $request->validate([
            'nama' => 'required|string|max:255',
            'nip' => 'required|string|unique:dosens,nip,' . $dosen->id,
            'bidang_keahlian' => 'nullable|string|max:255',
            'kuota' => 'required|integer|min:0',
        ]);

        $dosen->update($data);

        return back()->with('success', 'Data dosen diperbarui.');
    }

    public function destroy(Dosen $dosen)
    {
        $dosen->delete();

        return back()->with('success', 'Dosen dihapus.');
    }
}
