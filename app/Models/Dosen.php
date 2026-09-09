<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Dosen extends Model
{
    protected $fillable = ['nama', 'nip', 'bidang_keahlian', 'kuota', 'kuota_terpakai'];

    public function pilihans()
    {
        return $this->hasMany(Pilihan::class);
    }

    public function getSisaKuotaAttribute(): int
    {
        return max(0, $this->kuota - $this->kuota_terpakai);
    }

    public function getPenuhAttribute(): bool
    {
        return $this->kuota_terpakai >= $this->kuota;
    }
}
