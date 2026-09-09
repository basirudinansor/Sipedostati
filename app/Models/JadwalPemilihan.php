<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class JadwalPemilihan extends Model
{
    protected $fillable = ['waktu_buka', 'waktu_tutup', 'status'];

    protected $casts = [
        'waktu_buka' => 'datetime',
        'waktu_tutup' => 'datetime',
    ];

    public static function aktif()
    {
        return static::latest()->first();
    }

    public function sudahDibuka(): bool
    {
        if (!$this->waktu_buka || $this->status === 'draft' || $this->status === 'ditutup') {
            return false;
        }

        if ($this->waktu_tutup && now()->gte($this->waktu_tutup)) {
            return false;
        }

        return now()->gte($this->waktu_buka);
    }
}
