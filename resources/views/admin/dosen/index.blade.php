@extends('layouts.app')
@section('title', 'Kelola Dosen')

@push('styles')
<style>
    .form-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 18px; }
    .form-grid .span-2 { grid-column: span 2; }
    .form-hint { color: var(--text-muted); font-size: 12.5px; margin-top: -12px; margin-bottom: 4px; }
    .data-table { width: 100%; border-collapse: collapse; font-size: 14.5px; }
    .data-table th {
        text-align: left; font-family: var(--font-mono); font-size: 11.5px;
        letter-spacing: .1em; text-transform: uppercase; color: var(--text-muted);
        padding: 14px 20px; background: var(--paper-dim); border-bottom: 1px solid var(--ink-line);
    }
    .data-table td { padding: 16px 20px; border-bottom: 1px solid var(--ink-line); vertical-align: middle; }
    .data-table tr:last-child td { border-bottom: none; }
    .data-table tbody tr:hover { background: var(--paper-dim); }
    .kuota-pill { font-family: var(--font-mono); font-size: 12.5px; padding: 4px 10px; border-radius: 20px; background: var(--paper-dim); border: 1px solid var(--ink-line); display: inline-block; white-space: nowrap; }
    .kuota-pill.is-full { background: #F7E9EA; border-color: var(--crimson); color: var(--crimson); }
    .section-heading { font-size: 18px; margin-bottom: 20px; }
    .section-gap { margin-bottom: 40px; }
    .row-actions { display: flex; gap: 8px; justify-content: flex-end; flex-wrap: wrap; }

    /* Modal */
    .modal-overlay {
        display: none; position: fixed; inset: 0; background: rgba(20, 33, 61, .55);
        align-items: center; justify-content: center; z-index: 50; padding: 20px;
    }
    .modal-overlay.is-open { display: flex; }
    .modal-box {
        background: #fff; border-radius: 8px; padding: 32px; width: 100%; max-width: 480px;
        box-shadow: var(--shadow-md); animation: modalIn .25s ease;
    }
    @keyframes modalIn { from { opacity: 0; transform: translateY(10px) scale(.98);} to { opacity: 1; transform: translateY(0) scale(1);} }
    .modal-box h2 { font-size: 19px; margin-bottom: 20px; }
    .modal-actions { display: flex; gap: 10px; justify-content: flex-end; margin-top: 24px; }
</style>
@endpush

@section('content')
<span class="eyebrow">Panel Admin</span>
<h1 class="page-title">Kelola Dosen Pembimbing</h1>
<p class="page-subtitle">Kuota yang diisi berlaku SAMA untuk peran Pembimbing 1 maupun Pembimbing 2 (masing-masing dihitung terpisah).</p>

<div class="panel panel--pad section-gap">
    <h2 class="section-heading">Tambah dosen baru</h2>
    <form action="{{ route('admin.dosen.store') }}" method="POST" class="form-grid">
        @csrf
        <div class="span-2">
            <label for="nama">Nama dosen</label>
            <input type="text" id="nama" name="nama" required placeholder="Dr. Nama Dosen, M.Kom">
        </div>
        <div>
            <label for="nip">NIP</label>
            <input type="text" id="nip" name="nip" required placeholder="198001012005011001">
        </div>
        <div>
            <label for="kuota">Kuota (per peran)</label>
            <input type="number" id="kuota" name="kuota" min="0" required placeholder="5">
        </div>
        <div class="span-2 form-hint">Contoh: kuota 5 berarti dosen ini bisa membimbing maksimal 5 mahasiswa sebagai Pembimbing 1, DAN 5 mahasiswa lain sebagai Pembimbing 2 (total maksimal 10).</div>
        <div class="span-2">
            <label for="bidang_keahlian">Bidang keahlian</label>
            <input type="text" id="bidang_keahlian" name="bidang_keahlian" placeholder="Kecerdasan Buatan">
        </div>
        <div class="span-2">
            <button type="submit" class="btn btn--primary">Tambah dosen</button>
        </div>
    </form>
</div>

<div class="panel" style="overflow: hidden;">
    <table class="data-table">
        <thead>
            <tr><th>Nama</th><th>NIP</th><th>Kuota per Peran</th><th>Sbg Pembimbing 1</th><th>Sbg Pembimbing 2</th><th></th></tr>
        </thead>
        <tbody>
            @forelse($dosens as $dosen)
            <tr>
                <td>
                    <div style="font-weight: 600; color: var(--ink);">{{ $dosen->nama }}</div>
                    <div style="color: var(--text-muted); font-size: 13px;">{{ $dosen->bidang_keahlian }}</div>
                </td>
                <td style="font-family: var(--font-mono); color: var(--text-muted);">{{ $dosen->nip }}</td>
                <td style="font-family: var(--font-mono);">{{ $dosen->kuota }}</td>
                <td><span class="kuota-pill {{ $dosen->penuh_p1 ? 'is-full' : '' }}">{{ $dosen->kuota_terpakai_p1 }} / {{ $dosen->kuota }}</span></td>
                <td><span class="kuota-pill {{ $dosen->penuh_p2 ? 'is-full' : '' }}">{{ $dosen->kuota_terpakai_p2 }} / {{ $dosen->kuota }}</span></td>
                <td>
                    <div class="row-actions">
                        <button type="button" class="btn btn--outline btn-edit-dosen" style="padding: 7px 14px; font-size: 13px;"
                            data-id="{{ $dosen->id }}"
                            data-nama="{{ $dosen->nama }}"
                            data-nip="{{ $dosen->nip }}"
                            data-bidang="{{ $dosen->bidang_keahlian }}"
                            data-kuota="{{ $dosen->kuota }}">
                            Edit
                        </button>
                        <form action="{{ route('admin.dosen.destroy', $dosen) }}" method="POST"
                            onsubmit="return confirm('Hapus dosen ini?')">
                            @csrf @method('DELETE')
                            <button type="submit" class="btn btn--danger" style="padding: 7px 14px; font-size: 13px;">Hapus</button>
                        </form>
                    </div>
                </td>
            </tr>
            @empty
            <tr><td colspan="6" style="text-align:center; color: var(--text-muted); padding: 32px;">Belum ada dosen ditambahkan.</td></tr>
            @endforelse
        </tbody>
    </table>
</div>

<div class="modal-overlay" id="modal-edit-dosen">
    <div class="modal-box">
        <h2>Edit Data Dosen</h2>
        <form id="form-edit-dosen" method="POST">
            @csrf
            @method('PUT')
            <div style="margin-bottom: 16px;">
                <label for="edit_nama">Nama dosen</label>
                <input type="text" id="edit_nama" name="nama" required>
            </div>
            <div style="margin-bottom: 16px;">
                <label for="edit_nip">NIP</label>
                <input type="text" id="edit_nip" name="nip" required>
            </div>
            <div style="margin-bottom: 16px;">
                <label for="edit_kuota">Kuota (per peran)</label>
                <input type="number" id="edit_kuota" name="kuota" min="0" required>
            </div>
            <div style="margin-bottom: 8px;">
                <label for="edit_bidang">Bidang keahlian</label>
                <input type="text" id="edit_bidang" name="bidang_keahlian">
            </div>
            <div class="modal-actions">
                <button type="button" class="btn btn--outline" id="btn-cancel-edit-dosen">Batal</button>
                <button type="submit" class="btn btn--primary">Simpan Perubahan</button>
            </div>
        </form>
    </div>
</div>

<script>
(function () {
    const modal = document.getElementById('modal-edit-dosen');
    const form = document.getElementById('form-edit-dosen');
    const baseUrl = "{{ url('/admin/dosen') }}";

    document.querySelectorAll('.btn-edit-dosen').forEach(btn => {
        btn.addEventListener('click', () => {
            document.getElementById('edit_nama').value = btn.dataset.nama;
            document.getElementById('edit_nip').value = btn.dataset.nip;
            document.getElementById('edit_kuota').value = btn.dataset.kuota;
            document.getElementById('edit_bidang').value = btn.dataset.bidang || '';
            form.action = `${baseUrl}/${btn.dataset.id}`;
            modal.classList.add('is-open');
        });
    });

    document.getElementById('btn-cancel-edit-dosen').addEventListener('click', () => {
        modal.classList.remove('is-open');
    });

    modal.addEventListener('click', (e) => {
        if (e.target === modal) modal.classList.remove('is-open');
    });
})();
</script>
@endsection
