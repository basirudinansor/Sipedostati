<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Dosen extends Model
{
    protected $fillable = ['nama', 'nip', 'bidang_keahlian', 'kuota', 'kuota_terpakai_p1', 'kuota_terpakai_p2'];

    public function pilihans()
    {
        return $this->hasMany(Pilihan::class);
    }

    public function getSisaKuotaP1Attribute(): int
    {
        return max(0, $this->kuota - $this->kuota_terpakai_p1);
    }

    public function getSisaKuotaP2Attribute(): int
    {
        return max(0, $this->kuota - $this->kuota_terpakai_p2);
    }

    public function getPenuhP1Attribute(): bool
    {
        return $this->kuota_terpakai_p1 >= $this->kuota;
    }

    public function getPenuhP2Attribute(): bool
    {
        return $this->kuota_terpakai_p2 >= $this->kuota;
    }

    public function getKuotaTerpakaiTotalAttribute(): int
    {
        return $this->kuota_terpakai_p1 + $this->kuota_terpakai_p2;
    }

    /**
     * Helper untuk ambil status kuota sesuai jenis ('pembimbing_1' / 'pembimbing_2').
     */
    public function terpakai(string $jenis): int
    {
        return $jenis === 'pembimbing_1' ? $this->kuota_terpakai_p1 : $this->kuota_terpakai_p2;
    }

    public function penuh(string $jenis): bool
    {
        return $jenis === 'pembimbing_1' ? $this->penuh_p1 : $this->penuh_p2;
    }
}
