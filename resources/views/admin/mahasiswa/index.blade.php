@extends('layouts.app')
@section('title', 'Kelola Mahasiswa')

@push('styles')
<style>
    .form-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 18px; }
    .form-grid .span-2 { grid-column: span 2; }
    .data-table { width: 100%; border-collapse: collapse; font-size: 14.5px; }
    .data-table th {
        text-align: left; font-family: var(--font-mono); font-size: 11.5px;
        letter-spacing: .1em; text-transform: uppercase; color: var(--text-muted);
        padding: 14px 20px; background: var(--paper-dim); border-bottom: 1px solid var(--ink-line);
    }
    .data-table td { padding: 14px 20px; border-bottom: 1px solid var(--ink-line); vertical-align: middle; }
    .data-table tr:last-child td { border-bottom: none; }
    .data-table tbody tr:hover { background: var(--paper-dim); }
    .section-heading { font-size: 18px; margin-bottom: 6px; }
    .section-hint { color: var(--text-muted); font-size: 13.5px; margin: 0 0 20px; line-height: 1.6; }
    .section-gap { margin-bottom: 32px; }
    .status-tag { font-family: var(--font-mono); font-size: 11.5px; padding: 3px 10px; border-radius: 20px; white-space: nowrap; }
    .status-tag--yes { background: #EAF3ED; color: var(--forest); }
    .status-tag--no { background: var(--paper-dim); color: var(--text-muted); }
    .status-tag--warn { background: #FBF1DB; color: #8A6A16; }
    .tabs { display: flex; gap: 4px; margin-bottom: 24px; border-bottom: 1px solid var(--ink-line); }
    .tab-btn {
        font-family: var(--font-body); font-size: 14px; font-weight: 600; color: var(--text-muted);
        background: none; border: none; padding: 10px 18px; cursor: pointer; border-bottom: 2px solid transparent; margin-bottom: -1px;
    }
    .tab-btn.active { color: var(--ink); border-color: var(--brass); }
    .tab-content { display: none; }
    .tab-content.active { display: block; }
    .template-box {
        display: flex; align-items: center; justify-content: space-between; gap: 16px;
        background: var(--paper-dim); border: 1px dashed var(--ink-line); border-radius: 6px; padding: 16px 20px; margin-bottom: 20px; flex-wrap: wrap;
    }
    .template-box__text { font-size: 13.5px; color: var(--text-muted); }
    .template-box__text strong { color: var(--ink); display: block; font-size: 14.5px; margin-bottom: 2px; }
    .file-drop {
        border: 1.5px dashed var(--ink-line); border-radius: 6px; padding: 28px; text-align: center; background: var(--paper); cursor: pointer;
        transition: border-color .2s ease, background .2s ease; display: block;
    }
    .file-drop:hover { border-color: var(--brass); background: #FFFCF3; }
    .file-drop input[type="file"] { display: none; }
    .file-drop__label { font-size: 14px; color: var(--text-muted); }
    .file-drop__filename { font-family: var(--font-mono); font-size: 13px; color: var(--ink); margin-top: 8px; font-weight: 600; }
    .import-errors { margin-top: 18px; background: #F7E9EA; border-left: 3px solid var(--crimson); border-radius: 4px; padding: 14px 18px; font-size: 13.5px; color: #7A2530; }
    .import-errors ul { margin: 8px 0 0; padding-left: 18px; }
    .row-actions { display: flex; gap: 8px; justify-content: flex-end; flex-wrap: wrap; }
    .sort-link { color: inherit; text-decoration: none; display: inline-flex; align-items: center; gap: 4px; }
    .sort-link:hover { color: var(--ink); }
    .sort-arrow { font-size: 10px; opacity: .5; }
    .sort-arrow.is-active { opacity: 1; color: var(--brass); }

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

    .table-toolbar {
        display: flex; align-items: center; justify-content: space-between; gap: 16px; flex-wrap: wrap;
        padding: 16px 20px; border-bottom: 1px solid var(--ink-line); background: var(--paper-dim);
    }
    .table-toolbar__left { display: flex; align-items: center; gap: 10px; font-size: 13.5px; color: var(--text-muted); }
    .table-toolbar select {
        font-family: var(--font-body); font-size: 13.5px; padding: 6px 10px; border-radius: 4px;
        border: 1.5px solid var(--ink-line); background: #fff; cursor: pointer;
    }
    .table-toolbar select:focus { outline: none; border-color: var(--brass); }

    .filter-bar {
        display: flex; align-items: flex-end; gap: 14px; flex-wrap: wrap;
        padding: 20px; border-bottom: 1px solid var(--ink-line); background: #fff;
    }
    .filter-field { display: flex; flex-direction: column; gap: 6px; min-width: 170px; }
    .filter-field label { margin: 0; font-size: 12px; }
    .filter-field input, .filter-field select {
        font-family: var(--font-body); font-size: 14px; padding: 9px 12px; border-radius: 4px;
        border: 1.5px solid var(--ink-line); background: var(--paper);
    }
    .filter-field input:focus, .filter-field select:focus { outline: none; border-color: var(--brass); box-shadow: 0 0 0 3px rgba(184,145,46,.18); }
    .filter-actions { display: flex; gap: 8px; }
    .filter-active-note { font-size: 12.5px; color: var(--text-muted); padding: 0 20px 16px; }

    .pagination-bar {
        display: flex; align-items: center; justify-content: space-between; gap: 16px; flex-wrap: wrap;
        padding: 16px 20px;
    }
    .pagination-bar__info { font-size: 13px; color: var(--text-muted); }
    .pagination-bar__links { display: flex; gap: 6px; flex-wrap: wrap; }
    .page-link {
        font-family: var(--font-mono); font-size: 13px; min-width: 34px; text-align: center;
        padding: 7px 10px; border-radius: 4px; border: 1px solid var(--ink-line); text-decoration: none; color: var(--text);
        transition: all .15s ease;
    }
    .page-link:hover { border-color: var(--brass); color: var(--ink); }
    .page-link.is-active { background: var(--ink); color: var(--paper); border-color: var(--ink); }
    .page-link.is-disabled { opacity: .4; pointer-events: none; }
</style>
@endpush

@section('content')
<span class="eyebrow">Panel Admin</span>
<h1 class="page-title">Kelola Akun Mahasiswa</h1>
<p class="page-subtitle">Tambahkan akun mahasiswa satu per satu, atau import dari Excel. Login pakai NIM, password awal = NIM.</p>

<div class="panel panel--pad section-gap">
    <div class="tabs">
        <button type="button" class="tab-btn active" data-tab="satu">Tambah satu</button>
        <button type="button" class="tab-btn" data-tab="excel">Import Excel</button>
    </div>

    <div class="tab-content active" data-tab-content="satu">
        <form action="{{ route('admin.mahasiswa.store') }}" method="POST" class="form-grid">
            @csrf
            <div class="span-2">
                <label for="name">Nama lengkap</label>
                <input type="text" id="name" name="name" required placeholder="Nama Mahasiswa">
            </div>
            <div>
                <label for="nim">NIM</label>
                <input type="text" id="nim" name="nim" required placeholder="C2F023001">
            </div>
            <div>
                <label for="email">Email (opsional)</label>
                <input type="email" id="email" name="email" placeholder="Kosongkan jika tidak ada">
            </div>
            <div class="span-2">
                <button type="submit" class="btn btn--primary">Tambah mahasiswa</button>
            </div>
        </form>
    </div>

    <div class="tab-content" data-tab-content="excel">
        <div class="template-box">
            <div class="template-box__text">
                <strong>Belum punya file template?</strong>
                Download dulu, isi kolom NIM / Nama Lengkap / Email, lalu upload di sini.
            </div>
            <a href="{{ asset('template/template_import_mahasiswa.xlsx') }}" class="btn btn--outline" style="white-space: nowrap;">Download Template</a>
        </div>

        <form action="{{ route('admin.mahasiswa.import') }}" method="POST" enctype="multipart/form-data" id="form-import">
            @csrf
            <label class="file-drop" id="drop-label" for="file">
                <div class="file-drop__label">Klik untuk pilih file Excel (.xlsx), atau tarik file ke sini</div>
                <div class="file-drop__filename" id="filename-display"></div>
            </label>
            <input type="file" id="file" name="file" accept=".xlsx,.xls,.csv" required>
            <div style="margin-top: 16px;">
                <button type="submit" class="btn btn--primary">Import mahasiswa</button>
            </div>
        </form>

        @if(session('import_errors') && count(session('import_errors')) > 0)
            <div class="import-errors">
                <strong>Beberapa baris dilewati:</strong>
                <ul>@foreach(session('import_errors') as $err)<li>{{ $err }}</li>@endforeach</ul>
            </div>
        @endif
    </div>
</div>

<div class="panel" style="overflow: hidden;">
    <form method="GET" action="{{ route('admin.mahasiswa.index') }}" class="filter-bar">
        <input type="hidden" name="per_page" value="{{ $perPage }}">
        <div class="filter-field" style="min-width: 220px;">
            <label for="filter_search">Cari nama / NIM</label>
            <input type="text" id="filter_search" name="search" value="{{ $search }}" placeholder="Ketik nama atau NIM...">
        </div>
        <div class="filter-field">
            <label for="filter_status_pilihan">Status Pilihan</label>
            <select id="filter_status_pilihan" name="status_pilihan">
                <option value="">Semua</option>
                <option value="sudah" {{ $statusPilihan === 'sudah' ? 'selected' : '' }}>Sudah lengkap (P1 & P2)</option>
                <option value="belum" {{ $statusPilihan === 'belum' ? 'selected' : '' }}>Belum lengkap</option>
            </select>
        </div>
        <div class="filter-field">
            <label for="filter_status_password">Status Password</label>
            <select id="filter_status_password" name="status_password">
                <option value="">Semua</option>
                <option value="wajib" {{ $statusPassword === 'wajib' ? 'selected' : '' }}>Wajib ganti</option>
                <option value="sudah" {{ $statusPassword === 'sudah' ? 'selected' : '' }}>Sudah diganti</option>
            </select>
        </div>
        <div class="filter-actions">
            <button type="submit" class="btn btn--primary" style="padding: 9px 20px; font-size: 13.5px;">Terapkan Filter</button>
            @if($search || $statusPilihan || $statusPassword)
                <a href="{{ route('admin.mahasiswa.index') }}" class="btn btn--outline" style="padding: 9px 20px; font-size: 13.5px;">Reset</a>
            @endif
        </div>
    </form>

    <div class="table-toolbar">
        <div class="table-toolbar__left">
            <span>Tampilkan</span>
            <select id="per-page-select">
                @foreach($perPageOptions as $opt)
                    <option value="{{ $opt }}" {{ $perPage == $opt ? 'selected' : '' }}>{{ $opt }}</option>
                @endforeach
            </select>
            <span>baris per halaman</span>
        </div>
        <a href="{{ route('admin.mahasiswa.export') }}" class="btn btn--outline" style="padding: 8px 16px; font-size: 13.5px;">
            Export Excel
        </a>
    </div>

    <table class="data-table">
        <thead>
            <tr>
                <th>
                    <a href="{{ route('admin.mahasiswa.index', array_merge(request()->except(['sort','direction','page']), ['sort' => 'name', 'direction' => $sort === 'name' && $direction === 'asc' ? 'desc' : 'asc'])) }}" class="sort-link">
                        Nama
                        <span class="sort-arrow {{ $sort === 'name' ? 'is-active' : '' }}">{{ $sort === 'name' && $direction === 'desc' ? '▼' : '▲' }}</span>
                    </a>
                </th>
                <th>
                    <a href="{{ route('admin.mahasiswa.index', array_merge(request()->except(['sort','direction','page']), ['sort' => 'nim', 'direction' => $sort === 'nim' && $direction === 'asc' ? 'desc' : 'asc'])) }}" class="sort-link">
                        NIM
                        <span class="sort-arrow {{ $sort === 'nim' ? 'is-active' : '' }}">{{ $sort === 'nim' && $direction === 'desc' ? '▼' : '▲' }}</span>
                    </a>
                </th>
                <th>
                    <a href="{{ route('admin.mahasiswa.index', array_merge(request()->except(['sort','direction','page']), ['sort' => 'status_password', 'direction' => $sort === 'status_password' && $direction === 'asc' ? 'desc' : 'asc'])) }}" class="sort-link">
                        Password
                        <span class="sort-arrow {{ $sort === 'status_password' ? 'is-active' : '' }}">{{ $sort === 'status_password' && $direction === 'desc' ? '▼' : '▲' }}</span>
                    </a>
                </th>
                <th>Pembimbing 1</th>
                <th>Pembimbing 2</th>
                <th></th>
            </tr>
        </thead>
        <tbody>
            @forelse($mahasiswas as $mhs)
            @php
                $p1 = $mhs->pilihans->firstWhere('jenis', 'pembimbing_1');
                $p2 = $mhs->pilihans->firstWhere('jenis', 'pembimbing_2');
            @endphp
            <tr>
                <td style="font-weight: 600; color: var(--ink);">{{ $mhs->name }}</td>
                <td style="font-family: var(--font-mono); color: var(--text-muted);">{{ $mhs->nim }}</td>
                <td>
                    @if($mhs->must_change_password)
                        <span class="status-tag status-tag--warn">Wajib ganti (default = NIM)</span>
                    @else
                        <span class="status-tag status-tag--yes">Sudah diganti</span>
                    @endif
                </td>
                <td>
                    @if($p1)
                        <span class="status-tag status-tag--yes">{{ $p1->dosen->nama }}</span>
                    @else
                        <span class="status-tag status-tag--no">Belum memilih</span>
                    @endif
                </td>
                <td>
                    @if($p2)
                        <span class="status-tag status-tag--yes">{{ $p2->dosen->nama }}</span>
                    @else
                        <span class="status-tag status-tag--no">Belum memilih</span>
                    @endif
                </td>
                <td>
                    <div class="row-actions">
                        <button type="button" class="btn btn--outline btn-edit-mhs" style="padding: 7px 14px; font-size: 13px;"
                            data-id="{{ $mhs->id }}"
                            data-name="{{ $mhs->name }}"
                            data-nim="{{ $mhs->nim }}"
                            data-email="{{ $mhs->email }}">
                            Edit
                        </button>
                        <form action="{{ route('admin.mahasiswa.reset-password', $mhs) }}" method="POST"
                            onsubmit="return confirm('Reset password {{ $mhs->name }} ke NIM ({{ $mhs->nim }})?')">
                            @csrf
                            <button type="submit" class="btn btn--outline" style="padding: 7px 14px; font-size: 13px;">Reset Password</button>
                        </form>
                        <form action="{{ route('admin.mahasiswa.destroy', $mhs) }}" method="POST"
                            onsubmit="return confirm('Hapus akun mahasiswa ini?')">
                            @csrf @method('DELETE')
                            <button type="submit" class="btn btn--danger" style="padding: 7px 14px; font-size: 13px;">Hapus</button>
                        </form>
                    </div>
                </td>
            </tr>
            @empty
            <tr><td colspan="6" style="text-align:center; color: var(--text-muted); padding: 32px;">Belum ada mahasiswa terdaftar.</td></tr>
            @endforelse
        </tbody>
    </table>

    @if($mahasiswas->total() > 0)
    <div class="pagination-bar">
        <div class="pagination-bar__info">
            Menampilkan {{ $mahasiswas->firstItem() }}–{{ $mahasiswas->lastItem() }} dari {{ $mahasiswas->total() }} mahasiswa
        </div>
        <div class="pagination-bar__links">
            <a href="{{ $mahasiswas->previousPageUrl() }}" class="page-link {{ $mahasiswas->onFirstPage() ? 'is-disabled' : '' }}">‹</a>
            @for($p = 1; $p <= $mahasiswas->lastPage(); $p++)
                @if($p == 1 || $p == $mahasiswas->lastPage() || abs($p - $mahasiswas->currentPage()) <= 1)
                    <a href="{{ $mahasiswas->url($p) }}" class="page-link {{ $p == $mahasiswas->currentPage() ? 'is-active' : '' }}">{{ $p }}</a>
                @elseif($p == 2 || $p == $mahasiswas->lastPage() - 1)
                    <span class="page-link is-disabled">…</span>
                @endif
            @endfor
            <a href="{{ $mahasiswas->nextPageUrl() }}" class="page-link {{ !$mahasiswas->hasMorePages() ? 'is-disabled' : '' }}">›</a>
        </div>
    </div>
    @endif
</div>

<script>
document.querySelectorAll('.tab-btn').forEach(btn => {
    btn.addEventListener('click', () => {
        document.querySelectorAll('.tab-btn').forEach(b => b.classList.remove('active'));
        document.querySelectorAll('.tab-content').forEach(c => c.classList.remove('active'));
        btn.classList.add('active');
        document.querySelector(`[data-tab-content="${btn.dataset.tab}"]`).classList.add('active');
    });
});

const fileInput = document.getElementById('file');
const filenameDisplay = document.getElementById('filename-display');
fileInput.addEventListener('change', () => {
    filenameDisplay.textContent = fileInput.files.length ? fileInput.files[0].name : '';
});

document.getElementById('per-page-select').addEventListener('change', function () {
    const url = new URL(window.location.href);
    url.searchParams.set('per_page', this.value);
    url.searchParams.delete('page'); // balik ke halaman 1 tiap ganti jumlah per halaman
    window.location.href = url.toString();
});
</script>

<div class="modal-overlay" id="modal-edit-mhs">
    <div class="modal-box">
        <h2>Edit Data Mahasiswa</h2>
        <form id="form-edit-mhs" method="POST">
            @csrf
            @method('PUT')
            <div style="margin-bottom: 16px;">
                <label for="edit_name">Nama lengkap</label>
                <input type="text" id="edit_name" name="name" required>
            </div>
            <div style="margin-bottom: 16px;">
                <label for="edit_nim">NIM</label>
                <input type="text" id="edit_nim" name="nim" required>
            </div>
            <div style="margin-bottom: 8px;">
                <label for="edit_email">Email</label>
                <input type="email" id="edit_email" name="email">
            </div>
            <div class="modal-actions">
                <button type="button" class="btn btn--outline" id="btn-cancel-edit-mhs">Batal</button>
                <button type="submit" class="btn btn--primary">Simpan Perubahan</button>
            </div>
        </form>
    </div>
</div>

<script>
(function () {
    const modal = document.getElementById('modal-edit-mhs');
    const form = document.getElementById('form-edit-mhs');
    const baseUrl = "{{ url('/admin/mahasiswa') }}";

    document.querySelectorAll('.btn-edit-mhs').forEach(btn => {
        btn.addEventListener('click', () => {
            document.getElementById('edit_name').value = btn.dataset.name;
            document.getElementById('edit_nim').value = btn.dataset.nim;
            document.getElementById('edit_email').value = btn.dataset.email || '';
            form.action = `${baseUrl}/${btn.dataset.id}`;
            modal.classList.add('is-open');
        });
    });

    document.getElementById('btn-cancel-edit-mhs').addEventListener('click', () => {
        modal.classList.remove('is-open');
    });

    modal.addEventListener('click', (e) => {
        if (e.target === modal) modal.classList.remove('is-open');
    });
})();
</script>
@endsection
