<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
      $this->call([
        UsersTableSeeder::class,
        SantriSeeder::class,
        KelasTableSeeder::class,
        SemesterTableSeeder::class,
        MataPelajaranSeeder::class,
    ]);
    }
}
