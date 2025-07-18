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
        'tahun_bergabung',

        // Akademik
        'sekolah',
        'kelas',
        'jilid_juz',
        'status',
        'kelas_awal_id',
        'kelas_id',

        // Wali
        'nama_wali',
        'pekerjaan_wali',
        'no_hp_wali',

        // Dokumen
        'pas_foto_path',
        'akta_path',
    ];

    protected $casts = [
        'tanggal_lahir' => 'date',
        'tahun_bergabung' => 'integer',
    ];

    // ========================
    //        RELATIONS
    // ========================

    public function kelas()
    {
        return $this->belongsTo(Kelas::class, 'kelas_id');
    }

    public function kelasAwal()
    {
        return $this->belongsTo(Kelas::class, 'kelas_awal_id');
    }

    public function kelasSemester()
    {
        return $this->hasMany(SantriKelasSemester::class);
    }

    // ========================
    //        ACCESSORS
    // ========================

    public function getPasFotoUrlAttribute()
    {
        return $this->pas_foto_path ? asset('storage/' . $this->pas_foto_path) : asset('images/default-profile.jpg');
    }

    public function getAktaUrlAttribute()
    {
        return $this->akta_path ? asset('storage/' . $this->akta_path) : null;
    }

    public function getNamaAktaAttribute()
    {
        return $this->akta_path ? basename($this->akta_path) : null;
    }

    public function getTanggalLahirFormattedAttribute()
    {
        return $this->tanggal_lahir?->format('d/m/Y');
    }

    public function getTtlAttribute()
    {
        return $this->tempat_lahir . ', ' . $this->tanggal_lahir_formatted;
    }

    // ========================
    //        MUTATORS
    // ========================

    public function setNisAttribute($value)
    {
        $this->attributes['nis'] = strtoupper($value);
    }

    public function setNamaLengkapAttribute($value)
    {
        $this->attributes['nama_lengkap'] = ucwords(strtolower($value));
    }

    // ========================
    //        SCOPES
    // ========================

    public function scopeFilter($query, array $filters)
    {
        $query->when($filters['search'] ?? false, fn($q, $search) =>
            $q->where('nama_lengkap', 'like', "%$search%")
              ->orWhere('nis', 'like', "%$search%")
        );

        $query->when($filters['kelas'] ?? false, fn($q, $kelas) =>
            $q->where('kelas_id', $kelas)
        );

        $query->when($filters['status'] ?? false, fn($q, $status) =>
            $q->where('status', $status)
        );
    }

    public function kelasRelation()
    {
        return $this->belongsTo(Kelas::class, 'kelas_id');
    }
}
