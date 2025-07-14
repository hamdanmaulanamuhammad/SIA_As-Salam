<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class NilaiRapor extends Model
{
    use HasFactory;

    protected $table = 'nilai_rapor';

    protected $fillable = [
        'santri_id',
        'kelas_mapel_semester_id',
        'nilai',
        'predikat',
        'catatan',
        'keputusan' // foreign ke kelas_id baru
    ];

    public function santri()
    {
        return $this->belongsTo(Santri::class);
    }

    public function kelasMapelSemester()
    {
        return $this->belongsTo(KelasMapelSemester::class);
    }

    public function kelasTujuan()
    {
        return $this->belongsTo(Kelas::class, 'keputusan');
    }
}
