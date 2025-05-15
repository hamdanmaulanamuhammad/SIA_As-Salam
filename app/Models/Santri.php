<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Santri extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'santri';

    protected $fillable = [
        // Identitas Santri
        'nis',
        'nama_lengkap',
        'nama_panggilan',
        'jenis_kelamin',
        'tempat_lahir',
        'tanggal_lahir',
        'umur',
        'hobi',
        'riwayat_penyakit',
        'alamat',

        // Akademik
        'sekolah',
        'kelas',
        'jilid_juz',
        'status',

        // Orang Tua/Wali
        'nama_ayah',
        'nama_ibu',
        'pekerjaan_ayah',
        'pekerjaan_ibu',
        'no_hp_ayah',
        'no_hp_ibu',
        'nama_wali',
        'pekerjaan_wali',
        'no_hp_wali',

        // Dokumen
        'pas_foto_path',
        'akta_path'
    ];

    protected $casts = [
        'tanggal_lahir' => 'date',
    ];

    // Scope untuk filtering
    public function scopeFilter($query, array $filters)
    {
        $query->when($filters['search'] ?? false, function($query, $search) {
            return $query->where('nama_lengkap', 'like', '%'.$search.'%')
                        ->orWhere('nis', 'like', '%'.$search.'%');
        });

        $query->when($filters['kelas'] ?? false, function($query, $kelas) {
            return $query->where('kelas', $kelas);
        });

        $query->when($filters['status'] ?? false, function($query, $status) {
            return $query->where('status', $status);
        });
    }

    // Accessor untuk URL foto
    public function getPasFotoUrlAttribute()
    {
        return $this->pas_foto_path ? asset('storage/'.$this->pas_foto_path) : asset('images/default-profile.jpg');
    }

    // Accessor untuk URL akta
    public function getAktaUrlAttribute()
    {
        return $this->akta_path ? asset('storage/'.$this->akta_path) : null;
    }

    // Accessor untuk nama file akta
    public function getNamaAktaAttribute()
    {
        return $this->akta_path ? basename($this->akta_path) : null;
    }

    // Accessor untuk format tanggal lahir
    public function getTanggalLahirFormattedAttribute()
    {
        return $this->tanggal_lahir->format('d/m/Y');
    }

    // Accessor untuk tempat tanggal lahir lengkap
    public function getTtlAttribute()
    {
        return $this->tempat_lahir.', '.$this->tanggal_lahir_formatted;
    }

    // Mutator untuk NIS (uppercase)
    public function setNisAttribute($value)
    {
        $this->attributes['nis'] = strtoupper($value);
    }

    // Mutator untuk nama lengkap (proper case)
    public function setNamaLengkapAttribute($value)
    {
        $this->attributes['nama_lengkap'] = ucwords(strtolower($value));
    }

    // Relationship jika diperlukan (contoh: dengan tabel pembayaran/pembelajaran)
    /*
    public function pembayaran()
    {
        return $this->hasMany(Pembayaran::class);
    }
    */
}
