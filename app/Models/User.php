<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<string>
     */
    protected $fillable = [
        'username',      // Tambahkan kolom username
        'full_name',     // Tambahkan kolom full_name
        'email',         // Kolom email
        'phone',         // Kolom phone
        'university',    // Kolom university
        'address',       // Kolom address
        'password',      // Kolom password
        'role',          // Kolom role
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
        ];
    }

    /**
     * Mutator untuk meng-hash password sebelum disimpan.
     *
     * @param string $value
     * @return void
     */
    
}
