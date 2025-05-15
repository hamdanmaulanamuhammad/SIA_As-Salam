<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;

class UsersTableSeeder extends Seeder
{
    public function run()
    {
        // Seeder untuk Admin
        DB::table('users')->insert([
            'username' => 'admin',
            'email' => 'admin@example.com',
            'phone' => '081234567890',
            'university' => 'University XYZ',
            'address' => 'Jl. Admin No.1, Jakarta',
            'full_name' => 'Admin User',
            'password' => Hash::make('admin123'), // Sesuaikan dengan password yang diinginkan
            'role' => 'admin',
            'accepted' => '1',
             // Role admin
        ]);

        // Seeder untuk Pengajar
        DB::table('users')->insert([
            'username' => 'pengajar1',
            'email' => 'pengajar1@example.com',
            'phone' => '081234567891',
            'university' => 'University ABC',
            'address' => 'Jl. Pengajar No.2, Jakarta',
            'full_name' => 'Pengajar Satu',
            'password' => Hash::make('pengajar123'),
            'role' => 'pengajar',
            'accepted' => '1',
        ]);
    }
}
