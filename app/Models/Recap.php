<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Recap extends Model
{
    use HasFactory;

    /**
     * Kolom yang dapat diisi secara massal.
     *
     * @var array
     */
    protected $fillable = [
        'nama_rekap',
        'periode',
        'batas_keterlambatan',
        'mukafaah',
        'bonus',
    ];

    /**
     * Konversi periode ke format bulan dan tahun.
     *
     * @return string
     */
    public function getPeriodeAttribute($value)
    {
        return \Carbon\Carbon::parse($value)->format('F Y');
    }
}
