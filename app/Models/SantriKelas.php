<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SantriKelas extends Model
{
    protected $fillable = ['santri_id', 'kelas_id', 'semester_id'];

    public function santri()
    {
        return $this->belongsTo(Santri::class);
    }

    public function kelas()
    {
        return $this->belongsTo(Kelas::class);
    }

    public function semester()
    {
        return $this->belongsTo(Semester::class);
    }
}
