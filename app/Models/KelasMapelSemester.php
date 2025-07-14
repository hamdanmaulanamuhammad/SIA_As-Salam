<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class KelasMapelSemester extends Model
{
    use HasFactory;

    protected $table = 'kelas_mapel_semester';

    protected $fillable = ['kelas_semester_id', 'mata_pelajaran_id'];

    public function kelasSemester()
    {
        return $this->belongsTo(KelasSemester::class, 'kelas_semester_id');
    }

    public function mataPelajaran()
    {
        return $this->belongsTo(Mapel::class, 'mata_pelajaran_id');
    }

    public function nilaiRapor()
    {
        return $this->hasMany(NilaiRapor::class);
    }
}
