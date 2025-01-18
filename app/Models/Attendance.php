<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Attendance extends Model
{
    use HasFactory;  
  
    protected $fillable = [  
        'event_id', // ID acara  
        'user_id', // ID pengguna yang hadir  
        'status', // Status kehadiran (hadir, tidak hadir, izin)  
    ];  
  
    // Relasi dengan Event  
    public function event()  
    {  
        return $this->belongsTo(Event::class);  
    }  
}
