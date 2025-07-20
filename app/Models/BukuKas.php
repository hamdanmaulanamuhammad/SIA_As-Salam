<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BukuKas extends Model
{
    use HasFactory;

    protected $table = 'buku_kas_tahunans';

    protected $fillable = ['tahun'];

    public function transaksiKas()
    {
        return $this->hasMany(TransaksiKas::class, 'buku_kas_tahunan_id'); // ← perbaikan di sini
    }
}
