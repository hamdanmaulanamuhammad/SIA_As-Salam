<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class InfaqTahunan extends Model
{
    use HasFactory;

    protected $table = 'infaq_tahunans';
    protected $fillable = ['tahun'];

    public function infaqSantris()
    {
        return $this->hasMany(InfaqSantri::class);
    }
}
