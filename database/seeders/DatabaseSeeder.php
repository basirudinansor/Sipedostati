<?php

namespace Database\Seeders;

use App\Models\Dosen;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // ---------- Akun admin ----------
        User::create([
            'name' => 'Admin',
            'email' => 'admin@kampus.ac.id',
            'password' => Hash::make('admin123'),
            'role' => 'admin',
            'must_change_password' => false,
        ]);

        // ---------- Dosen contoh (ganti/tambah lewat menu Kelola Dosen) ----------
        Dosen::insert([
            ['nama' => 'Dr. Budi Santoso', 'nip' => '198001012005011001', 'bidang_keahlian' => 'Kecerdasan Buatan', 'kuota' => 5, 'kuota_terpakai_p1' => 0, 'kuota_terpakai_p2' => 0, 'created_at' => now(), 'updated_at' => now()],
            ['nama' => 'Dr. Siti Aminah', 'nip' => '198203152006042002', 'bidang_keahlian' => 'Rekayasa Perangkat Lunak', 'kuota' => 5, 'kuota_terpakai_p1' => 0, 'kuota_terpakai_p2' => 0, 'created_at' => now(), 'updated_at' => now()],
            ['nama' => 'Ir. Ahmad Fauzi, M.Kom', 'nip' => '197911202004031003', 'bidang_keahlian' => 'Jaringan Komputer', 'kuota' => 3, 'kuota_terpakai_p1' => 0, 'kuota_terpakai_p2' => 0, 'created_at' => now(), 'updated_at' => now()],
        ]);

        // ---------- 34 mahasiswa ----------
        // Login pakai NIM, password awal = NIM, wajib ganti password saat login pertama.
        $mahasiswa = [
            ['C2F023001', 'Raina Artika Ramadlonia'],
            ['C2F023002', 'Taufik Ismail'],
            ['C2F023004', 'Auliya Rohman Riquelme Al Ubaidah'],
            ['C2F023005', 'Aqib Ainun Najib'],
            ['C2F023007', 'Naimatul Husna'],
            ['C2F023008', 'Diaz Aditya'],
            ['C2F023009', 'Imanuel Laturake'],
            ['C2F023011', 'Yasyifa Nur Bani'],
            ['C2F023012', 'Syahrul Ibnu Ramadhan'],
            ['C2F023013', 'Muhammad Faza Ardianto'],
            ['C2F023014', 'M. Fiqri Zulfikar'],
            ['C2F023015', 'Syahrul Ramadhon'],
            ['C2F023016', 'Eva Febyliana'],
            ['C2F023017', 'Teuku Zaine Abror Attolok'],
            ['C2F023018', 'Wahyu Hari Saputra'],
            ['C2F023019', 'Dewi Rahmawati'],
            ['C2F023020', 'Irba Ilzami Al Haq'],
            ['C2F023021', 'Nabila Ismawarni. Ka'],
            ['C2F023022', 'Nisa Atthalia'],
            ['C2F023023', 'Maulana Sihdi Habibie'],
            ['C2F023024', 'Alfa Hikmatun Nabilah'],
            ['C2F023025', 'Mustika Restu Nur Asri'],
            ['C2F023026', 'Dyah Ayu Kusumaningtyas'],
            ['C2F023027', 'Muchamad Faris Chakim'],
            ['C2F023028', 'Herdiansyah Maulan Ahmad'],
            ['C2F023029', 'Dwi Ayu Latifah'],
            ['C2F023030', 'Ahmad Thoriq Alqi Ighafur'],
            ['C2F023031', 'Fahri'],
            ['C2F023032', 'Yusuf Faridz Maulana'],
            ['C2F023033', 'Kilala Mahadewi'],
            ['C2F023034', 'Muhamad Ipan Alkahfi'],
            ['C2F023035', 'Ahmad Munfarid'],
            ['C2F023036', 'Muhamad Fahmi Husaini'],
            ['C2F023037', 'Karima Elsami'],
        ];

        foreach ($mahasiswa as [$nim, $nama]) {
            User::create([
                'name' => $nama,
                'nim' => $nim,
                'email' => strtolower($nim) . '@mahasiswa.local',
                'password' => Hash::make($nim),
                'role' => 'mahasiswa',
                'must_change_password' => true,
            ]);
        }
    }
}
