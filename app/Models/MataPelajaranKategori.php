<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MataPelajaranKategori extends Model
{
    use HasFactory;

    protected $fillable = ['mata_pelajaran_id', 'kategori_ujian'];

    public function mataPelajaran()
    {
        return $this->belongsTo(MataPelajaran::class);
    }
}
