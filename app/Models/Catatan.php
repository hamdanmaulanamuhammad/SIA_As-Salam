<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Catatan extends Model
{
    use HasFactory;

    protected $table = 'catatan_rapor';

    protected $fillable = [
        'santri_id',
        'kelas_semester_id',
        'catatan',
        'keputusan_kelas_id',
        'predikat',
        'status_naik_kelas', // tambahan
    ];

    public function santri()
    {
        return $this->belongsTo(Santri::class);
    }

    public function kelasSemester()
    {
        return $this->belongsTo(KelasSemester::class);
    }

    public function kelasTujuan()
    {
        return $this->belongsTo(Kelas::class, 'keputusan_kelas_id');
    }
}
