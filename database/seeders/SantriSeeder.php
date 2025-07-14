<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Faker\Factory as Faker;

class SantriSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $faker = Faker::create('id_ID');

        // Ambil semua kelas yang ada
        $kelasIds = DB::table('kelas')->pluck('id')->toArray();

        // Data dummy untuk seeder
        $santriData = [];

        for ($i = 1; $i <= 50; $i++) {
            $jenisKelamin = $faker->randomElement(['Laki-laki', 'Perempuan']);
            $firstName = $jenisKelamin === 'Laki-laki' ? $faker->firstNameMale : $faker->firstNameFemale;
            $namaLengkap = $firstName . ' ' . $faker->lastName;

            // Randomize kelas
            $kelasAwal = $faker->randomElement($kelasIds);
            $kelasSekarang = $faker->randomElement($kelasIds);

            $santriData[] = [
                'nis' => 'NIS' . str_pad($i, 6, '0', STR_PAD_LEFT),
                'nama_lengkap' => $namaLengkap,
                'nama_panggilan' => $firstName,
                'jenis_kelamin' => $jenisKelamin,
                'tempat_lahir' => $faker->city,
                'tanggal_lahir' => $faker->dateTimeBetween('-18 years', '-5 years')->format('Y-m-d'),
                'umur' => $faker->numberBetween(5, 18),
                'hobi' => $faker->randomElement([
                    'Membaca', 'Bermain sepak bola', 'Menggambar', 'Bernyanyi',
                    'Bermain game', 'Memasak', 'Berkebun', 'Bersepeda'
                ]),
                'riwayat_penyakit' => $faker->optional(0.3)->randomElement([
                    'Asma', 'Alergi makanan', 'Tidak ada', 'Mata minus', 'Demam berdarah'
                ]),
                'alamat' => $faker->address,
                'sekolah' => $faker->randomElement([
                    'SDN 1 Depok', 'SDN 2 Depok', 'SMP Negeri 3 Depok',
                    'SMA Negeri 1 Depok', 'MTs Al-Hikmah', 'MA Daarut Tauhid'
                ]),
                'kelas' => $faker->randomElement([
                    '1A', '1B', '2A', '2B', '3A', '3B', '4A', '4B', '5A', '5B', '6A', '6B'
                ]),
                'jilid_juz' => $faker->randomElement([
                    'Jilid 1', 'Jilid 2', 'Jilid 3', 'Jilid 4', 'Jilid 5', 'Jilid 6',
                    'Juz 1', 'Juz 2', 'Juz 3', 'Al-Qur\'an'
                ]),
                'status' => $faker->randomElement(['Aktif', 'Tidak-Aktif']),
                'kelas_awal_id' => $kelasAwal,
                'kelas_id' => $kelasSekarang,
                'nama_ayah' => $faker->name('male'),
                'nama_ibu' => $faker->name('female'),
                'pekerjaan_ayah' => $faker->randomElement([
                    'Pegawai Negeri', 'Wiraswasta', 'Petani', 'Guru', 'Sopir',
                    'Buruh', 'Pedagang', 'Karyawan Swasta'
                ]),
                'pekerjaan_ibu' => $faker->randomElement([
                    'Ibu Rumah Tangga', 'Guru', 'Pegawai Negeri', 'Wiraswasta',
                    'Pedagang', 'Karyawan Swasta', 'Petani'
                ]),
                'no_hp_ayah' => $faker->phoneNumber,
                'no_hp_ibu' => $faker->phoneNumber,
                'nama_wali' => $faker->optional(0.2)->name,
                'pekerjaan_wali' => $faker->optional(0.2)->randomElement([
                    'Pedagang', 'Guru', 'Pegawai Negeri', 'Wiraswasta'
                ]),
                'no_hp_wali' => $faker->optional(0.2)->phoneNumber,
                'pas_foto_path' => $faker->optional(0.7)->randomElement([
                    'uploads/foto/santri_' . $i . '.jpg',
                    'uploads/foto/santri_' . $i . '.png'
                ]),
                'akta_path' => $faker->optional(0.8)->randomElement([
                    'uploads/akta/akta_' . $i . '.pdf',
                    'uploads/akta/akta_' . $i . '.jpg'
                ]),
                'created_at' => now(),
                'updated_at' => now(),
            ];
        }

        // Insert data ke database
        DB::table('santri')->insert($santriData);
    }
}
