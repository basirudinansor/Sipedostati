<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class MahasiswaController extends Controller
{
    private const ALLOWED_PER_PAGE = [10, 20, 50, 100];

    // Kolom yang boleh dipakai untuk sorting, dan kolom DB aslinya.
    private const SORTABLE_COLUMNS = [
        'name' => 'name',
        'nim' => 'nim',
        'status_password' => 'must_change_password',
    ];

    public function index(Request $request)
    {
        $perPage = (int) $request->query('per_page', 20);
        if (!in_array($perPage, self::ALLOWED_PER_PAGE, true)) {
            $perPage = 20;
        }

        $search = trim((string) $request->query('search', ''));
        $statusPilihan = $request->query('status_pilihan', '');
        $statusPassword = $request->query('status_password', '');

        $sort = $request->query('sort', 'name');
        if (!array_key_exists($sort, self::SORTABLE_COLUMNS)) {
            $sort = 'name';
        }
        $direction = $request->query('direction', 'asc') === 'desc' ? 'desc' : 'asc';

        $query = User::where('role', 'mahasiswa')->with('pilihans.dosen');

        if ($search !== '') {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('nim', 'like', "%{$search}%");
            });
        }

        if ($statusPilihan === 'sudah') {
            $query->has('pilihans', '>=', 2);
        } elseif ($statusPilihan === 'belum') {
            $query->has('pilihans', '<', 2);
        }

        if ($statusPassword === 'wajib') {
            $query->where('must_change_password', true);
        } elseif ($statusPassword === 'sudah') {
            $query->where('must_change_password', false);
        }

        $mahasiswas = $query->orderBy(self::SORTABLE_COLUMNS[$sort], $direction)
            ->paginate($perPage)
            ->withQueryString();

        return view('admin.mahasiswa.index', [
            'mahasiswas' => $mahasiswas,
            'perPage' => $perPage,
            'perPageOptions' => self::ALLOWED_PER_PAGE,
            'search' => $search,
            'statusPilihan' => $statusPilihan,
            'statusPassword' => $statusPassword,
            'sort' => $sort,
            'direction' => $direction,
        ]);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'nim' => 'required|string|unique:users,nim',
            'email' => 'nullable|email|unique:users,email',
        ]);

        User::create([
            'name' => $data['name'],
            'nim' => $data['nim'],
            'email' => $data['email'] ?: $data['nim'] . '@mahasiswa.local',
            'password' => Hash::make($data['nim']),
            'role' => 'mahasiswa',
            'must_change_password' => true,
        ]);

        return back()->with('success', 'Mahasiswa ditambahkan. Login pakai NIM, kata sandi awal = NIM.');
    }

    public function update(Request $request, User $mahasiswa)
    {
        if ($mahasiswa->role !== 'mahasiswa') {
            abort(404);
        }

        $data = $request->validate([
            'name' => 'required|string|max:255',
            'nim' => 'required|string|unique:users,nim,' . $mahasiswa->id,
            'email' => 'nullable|email|unique:users,email,' . $mahasiswa->id,
        ]);

        $mahasiswa->update([
            'name' => $data['name'],
            'nim' => $data['nim'],
            'email' => $data['email'] ?: $data['nim'] . '@mahasiswa.local',
        ]);

        return back()->with('success', 'Data mahasiswa berhasil diperbarui.');
    }

    public function importExcel(Request $request)
    {
        $request->validate([
            'file' => 'required|file|mimes:xlsx,xls,csv|max:5120',
        ]);

        $spreadsheet = IOFactory::load($request->file('file')->getPathname());
        $sheet = $spreadsheet->getSheetByName('Mahasiswa') ?? $spreadsheet->getActiveSheet();
        $rows = $sheet->toArray(null, true, true, false);

        $created = 0;
        $skipped = 0;
        $errors = [];

        foreach ($rows as $i => $row) {
            $baris = $i + 1;
            if ($baris === 1) {
                continue;
            }

            $nim = trim((string) ($row[0] ?? ''));
            $nama = trim((string) ($row[1] ?? ''));
            $email = trim((string) ($row[2] ?? ''));

            if ($nim === '' && $nama === '') {
                continue;
            }

            if ($nim === '' || $nama === '') {
                $skipped++;
                $errors[] = "Baris {$baris}: NIM atau Nama kosong.";
                continue;
            }

            if ($email === '') {
                $email = $nim . '@mahasiswa.local';
            } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                $skipped++;
                $errors[] = "Baris {$baris}: format email '{$email}' tidak valid.";
                continue;
            }

            if (User::where('nim', $nim)->orWhere('email', $email)->exists()) {
                $skipped++;
                $errors[] = "Baris {$baris}: NIM {$nim} atau email sudah terdaftar.";
                continue;
            }

            User::create([
                'name' => $nama,
                'nim' => $nim,
                'email' => $email,
                'password' => Hash::make($nim),
                'role' => 'mahasiswa',
                'must_change_password' => true,
            ]);
            $created++;
        }

        $message = "{$created} mahasiswa berhasil diimpor.";
        if ($skipped > 0) {
            $message .= " {$skipped} baris dilewati.";
        }

        return back()->with('success', $message)->with('import_errors', array_slice($errors, 0, 20));
    }

    public function exportExcel()
    {
        $mahasiswas = User::where('role', 'mahasiswa')
            ->with('pilihans.dosen')
            ->orderBy('name')
            ->get();

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Mahasiswa');

        $headers = ['No', 'NIM', 'Nama Lengkap', 'Email', 'Status Password', 'Pembimbing 1', 'Pembimbing 2'];
        $columns = ['A', 'B', 'C', 'D', 'E', 'F', 'G'];
        foreach ($headers as $i => $text) {
            $sheet->setCellValue($columns[$i] . '1', $text);
        }
        $sheet->getStyle('A1:G1')->getFont()->setBold(true)->getColor()->setRGB('FFFFFF');
        $sheet->getStyle('A1:G1')->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setRGB('14213D');
        $sheet->getStyle('A1:G1')->getAlignment()->setVertical(Alignment::VERTICAL_CENTER);
        $sheet->getRowDimension(1)->setRowHeight(22);

        $row = 2;
        foreach ($mahasiswas as $i => $mhs) {
            $p1 = $mhs->pilihans->firstWhere('jenis', 'pembimbing_1');
            $p2 = $mhs->pilihans->firstWhere('jenis', 'pembimbing_2');

            $sheet->setCellValue('A' . $row, $i + 1);
            $sheet->setCellValue('B' . $row, $mhs->nim);
            $sheet->setCellValue('C' . $row, $mhs->name);
            $sheet->setCellValue('D' . $row, $mhs->email);
            $sheet->setCellValue('E' . $row, $mhs->must_change_password ? 'Wajib ganti password' : 'Sudah diganti');
            $sheet->setCellValue('F' . $row, $p1?->dosen?->nama ?? 'Belum memilih');
            $sheet->setCellValue('G' . $row, $p2?->dosen?->nama ?? 'Belum memilih');
            $row++;
        }

        foreach (['A', 'B', 'C', 'D', 'E', 'F', 'G'] as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }

        $writer = new Xlsx($spreadsheet);
        $filename = 'data-mahasiswa-' . now()->format('Y-m-d_H-i') . '.xlsx';

        return response()->streamDownload(function () use ($writer) {
            $writer->save('php://output');
        }, $filename, [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
        ]);
    }

    public function resetPassword(User $mahasiswa)
    {
        if ($mahasiswa->role !== 'mahasiswa') {
            abort(404);
        }

        $mahasiswa->update([
            'password' => Hash::make($mahasiswa->nim),
            'must_change_password' => true,
        ]);

        return back()->with('success', "Password {$mahasiswa->name} direset ke NIM ({$mahasiswa->nim}).");
    }

    public function destroy(User $mahasiswa)
    {
        if ($mahasiswa->role !== 'mahasiswa') {
            abort(404);
        }

        $mahasiswa->delete();

        return back()->with('success', 'Akun mahasiswa dihapus.');
    }
}
