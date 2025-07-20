<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PengeluaranBulanan extends Model
{
    use HasFactory;

    protected $table = 'pengeluaran_administrasi';

    protected $fillable = ['administrasi_bulanan_id', 'keterangan', 'jumlah'];

    public function administrasi()
    {
        return $this->belongsTo(AdministrasiBulanan::class, 'administrasi_bulanan_id');
    }
}

