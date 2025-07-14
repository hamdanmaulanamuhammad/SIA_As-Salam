<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Stamp extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'path'];

    public function kelasSemester()
    {
        return $this->hasMany(KelasSemester::class);
    }
}
