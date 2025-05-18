<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Contract extends Model
{
    /**
     * Kolom yang boleh diisi secara massal.
     *
     * @var array
     */
    protected $fillable = [
        'user_id',
        'start_date',
        'end_date',
        'status',
        'document',
    ];

    /**
     * Kolom yang perlu disesuaikan tipe datanya.
     *
     * @var array
     */
    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
        'status' => 'string', // Enum diperlakukan sebagai string di Eloquent
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Relasi ke model User.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Scope untuk memfilter kontrak hanya untuk pengguna dengan peran 'pengajar'.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeForPengajar($query)
    {
        return $query->whereHas('user', function ($query) {
            $query->where('role', 'pengajar');
        });
    }

    /**
     * Mendapatkan URL dokumen (jika disimpan sebagai path file).
     *
     * @return string|null
     */
    public function getDocumentUrlAttribute()
    {
        return $this->document ? asset('storage/' . $this->document) : null;
    }
}
