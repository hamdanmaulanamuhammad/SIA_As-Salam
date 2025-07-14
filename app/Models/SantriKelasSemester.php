<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class SantriKelasSemester extends Model
{
    use HasFactory;

    protected $table = 'santri_kelas_semester';

    protected $fillable = [
        'santri_id',
        'kelas_semester_id'
    ];

    public function santri()
    {
        return $this->belongsTo(Santri::class);
    }

    public function kelasSemester()
    {
        return $this->belongsTo(KelasSemester::class);
    }
}
