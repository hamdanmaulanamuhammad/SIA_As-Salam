<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class InfaqSantri extends Model
{
    use HasFactory;

    protected $table = 'infaq_santris';
    protected $fillable = [
        'santri_id',
        'infaq_tahunan_id',
        'bulan',
        'infaq_wajib',
        'infaq_sukarela'
    ];

    public function santri()
    {
        return $this->belongsTo(Santri::class);
    }

    public function infaqTahunan()
    {
        return $this->belongsTo(InfaqTahunan::class);
    }
}
