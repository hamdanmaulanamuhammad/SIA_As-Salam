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
        'keputusan'
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
