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
    ];

    public function santri()
    {
        return $this->belongsTo(Santri::class);
    }

    public function kelasMapelSemester()
    {
        return $this->belongsTo(KelasMapelSemester::class);
    }
}
