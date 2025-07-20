<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AdditionalMukafaah extends Model
{
    use HasFactory;

    protected $fillable = ['recap_id', 'user_id', 'additional_mukafaah', 'description'];

    public function recap()
    {
        return $this->belongsTo(Recap::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
