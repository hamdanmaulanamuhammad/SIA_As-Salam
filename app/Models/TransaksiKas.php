<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TransaksiKas extends Model
{
    use HasFactory;

    protected $table = 'kas_entries';

    protected $fillable = [
        'buku_kas_tahunan_id',
        'tanggal',
        'keterangan',
        'jumlah',
        'jenis',
        'sumber',
        'tujuan',
        'bukti'
    ];

    public function bukuKas()
    {
        return $this->belongsTo(BukuKas::class, 'buku_kas_tahunan_id');
    }
}
