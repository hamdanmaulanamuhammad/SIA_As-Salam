<?php

namespace Database\Seeders;

use App\Models\Semester;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class SemesterTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Semester::create([
            'nama_semester' => 'Ganjil',
            'tahun_ajaran' => '2024/2025',
            'tanggal_mulai' => '2024-07-01',
            'tanggal_selesai' => '2024-12-31',
        ]);

        Semester::create([
            'nama_semester' => 'Genap',
            'tahun_ajaran' => '2024/2025',
            'tanggal_mulai' => '2025-01-01',
            'tanggal_selesai' => '2025-06-30',
        ]);
    }
}
