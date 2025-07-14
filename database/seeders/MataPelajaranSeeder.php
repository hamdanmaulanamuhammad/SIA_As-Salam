<?php

namespace Database\Seeders;

use App\Models\Mapel;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class MataPelajaranSeeder extends Seeder
{
    public function run(): void
    {
        Mapel::create(['nama_mapel' => 'Juz 30', 'kategori' => 'Hafalan']);
        Mapel::create(['nama_mapel' => 'Ujian Tulis Diniyyah', 'kategori' => 'Teori']);
        Mapel::create(['nama_mapel' => 'Wudhu', 'kategori' => 'Praktik']);
        Mapel::create(['nama_mapel' => 'Akidah dan Akhlak', 'kategori' => 'Praktik']);
        Mapel::create(['nama_mapel' => 'Fiqih', 'kategori' => 'Praktik']);
    }
}
