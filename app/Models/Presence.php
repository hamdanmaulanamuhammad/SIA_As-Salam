<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Presence extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'date',
        'arrival_time',
        'end_time',
        'class',
        'material',
        'proof',
        'leave_reason',
        'issues',
        'type'
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id'); // Mengubah relasi ke User
    }
}
