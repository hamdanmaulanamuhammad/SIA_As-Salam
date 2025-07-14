<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class KelasSemester extends Model
{
    use HasFactory;

    protected $table = 'kelas_semester';

    protected $fillable = [
        'kelas_id',
        'semester_id',
        'wali_kelas_id',
        'mudir_id',
        'sudah_diproses'
    ];

    protected $casts = [
        'sudah_diproses' => 'boolean',
    ];

    public function kelas()
    {
        return $this->belongsTo(Kelas::class);
    }

    public function semester()
    {
        return $this->belongsTo(Semester::class);
    }

    public function waliKelas()
    {
        return $this->belongsTo(User::class, 'wali_kelas_id');
    }

    public function mudir()
    {
        return $this->belongsTo(User::class, 'mudir_id');
    }

    public function santriKelas()
    {
        return $this->hasMany(SantriKelasSemester::class);
    }

    public function mapels()
    {
        return $this->hasMany(KelasMapelSemester::class);
    }
}
