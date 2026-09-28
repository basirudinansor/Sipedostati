<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Pilihan extends Model
{
    protected $fillable = ['mahasiswa_id', 'dosen_id', 'jenis'];

    public function mahasiswa()
    {
        return $this->belongsTo(User::class, 'mahasiswa_id');
    }

    public function dosen()
    {
        return $this->belongsTo(Dosen::class);
    }
}
