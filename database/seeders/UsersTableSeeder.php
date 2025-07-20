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
            'password' => Hash::make('admin123'),
            'role' => 'admin',
            'accepted' => '1',
        ]);

        // Seeder untuk 5 Pengajar
        for ($i = 1; $i <= 5; $i++) {
            DB::table('users')->insert([
                'username'   => "pengajar{$i}",
                'email'      => "pengajar{$i}@example.com",
                'phone'      => "08123456789{$i}",
                'university' => "University ABC",
                'address'    => "Jl. Pengajar No.{$i}, Jakarta",
                'full_name'  => "Pengajar {$i}",
                'password'   => Hash::make("pengajar{$i}123"),
                'role'       => 'pengajar',
                'accepted'   => '1',
            ]);
        }
    }
}
