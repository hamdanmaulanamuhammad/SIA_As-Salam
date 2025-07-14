<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Kelas extends Model
{
    use HasFactory;

    protected $table = 'kelas';

    protected $fillable = ['nama_kelas'];

    public function santri()
    {
        return $this->hasMany(Santri::class, 'kelas_id');
    }

    public function santriAwal()
    {
        return $this->hasMany(Santri::class, 'kelas_awal_id');
    }

    public function kelasSemester()
    {
        return $this->hasMany(KelasSemester::class);
    }
    public function getSantriCountAttribute()
    {
        return $this->santri()->count();
    }
}
