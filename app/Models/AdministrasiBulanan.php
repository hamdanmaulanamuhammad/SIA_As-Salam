<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AdministrasiBulanan extends Model
{
    use HasFactory;
    protected $table = 'administrasi_bulanans';

    protected $fillable = ['bulan', 'tahun'];

    public function pengeluaranBulanan()
    {
        return $this->hasMany(PengeluaranBulanan::class);
    }
}
