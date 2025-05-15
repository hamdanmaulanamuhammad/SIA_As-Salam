<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class Recap extends Model
{
    use HasFactory;

    protected $fillable = ['periode', 'batas_keterlambatan', 'mukafaah', 'bonus', 'dates'];

    public function getPeriodeAttribute($value)
    {
        try {
            return Carbon::createFromFormat('Y-m', $value, 'Asia/Jakarta')
                ->locale('id')
                ->translatedFormat('F Y');
        } catch (\Exception $e) {
            \Log::error('Failed to parse periode: ' . $e->getMessage(), ['periode' => $value]);
            return $value; // Kembalikan nilai mentah jika gagal
        }
    }
}
