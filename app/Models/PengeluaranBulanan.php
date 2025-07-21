<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PengeluaranBulanan extends Model
{
    use HasFactory;

    protected $table = 'pengeluaran_administrasi';

    protected $fillable = [
        'administrasi_bulanan_id',
        'nama_pengeluaran',
        'jumlah',
        'keterangan',
        'bank_account_id',
    ];

    public function bankAccount()
    {
        return $this->belongsTo(BankAccount::class);
    }

    public function administrasi()
    {
        return $this->belongsTo(AdministrasiBulanan::class, 'administrasi_bulanan_id');
    }
}

