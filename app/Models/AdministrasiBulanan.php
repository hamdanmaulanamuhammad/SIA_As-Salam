<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AdministrasiBulanan extends Model
{
    use HasFactory;
    protected $table = 'administrasi_bulanans';

    protected $fillable = ['bulan', 'tahun', 'bank_account_id'];

    public function pengeluaranBulanan()
    {
        return $this->hasMany(PengeluaranBulanan::class);
    }

    public function bankAccount()
    {
        return $this->belongsTo(BankAccount::class);
    }
}
