<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Event extends Model
{
    use HasFactory;

    // Tentukan kolom yang dapat diisi
    protected $fillable = [
        'name',
        'date',
        'time',
        'late_limit',
        'duration',
    ];

    // Jika Anda ingin menambahkan relasi, Anda bisa melakukannya di sini
}
