<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Semester extends Model
{
    use HasFactory;

    protected $fillable = ['nama_semester', 'tahun_ajaran', 'tanggal_mulai', 'tanggal_selesai'];

    public function kelasSemesters()
    {
        return $this->hasMany(KelasSemester::class);
    }

    public function mataPelajaran()
    {
        return $this->hasMany(Mapel::class);
    }
}
