<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Santri;
use App\Models\Kelas;
use Faker\Factory as Faker;

class SantriSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $faker = Faker::create('id_ID');

        // Ambil semua kelas yang tersedia
        $kelasIds = Kelas::pluck('id')->toArray();

        // Data pilihan untuk beberapa field
        $jenisKelamin = ['Laki-laki', 'Perempuan'];
        $statusOptions = ['Aktif'];
        $jenjangKelas = ['1A', '1B', '2A', '2B', '3A', '3B', '4A', '4B', '5A', '5B', '6A', '6B'];
        $jenjangSekolah = ['SDN', 'SMPN', 'SMKN', 'SMA'];
        $juzJilid = ['Jilid 1', 'Jilid 2', 'Jilid 3', 'Jilid 4', 'Jilid 5', 'Jilid 6', 'Juz 1', 'Juz 2', 'Juz 3', 'Juz 4', 'Juz 5'];

        $hobbies = [
            'Membaca Al-Qur\'an', 'Sepak bola', 'Menggambar', 'Bermain musik', 'Kaligrafi',
            'Tilawah', 'Futsal', 'Badminton', 'Bernyanyi', 'Menulis', 'Berkebun'
        ];

        $pekerjaan = [
            'Karyawan Swasta', 'PNS', 'Guru', 'Pedagang', 'Wiraswasta', 'Buruh',
            'Petani', 'Dokter', 'Perawat', 'Sopir', 'Ibu Rumah Tangga'
        ];

        // Generate 50 santri
        for ($i = 1; $i <= 50; $i++) {
            $jenisKelaminRandom = $faker->randomElement($jenisKelamin);
            $tanggalLahir = $faker->dateTimeBetween('-18 years', '-6 years');
            $umur = date('Y') - $tanggalLahir->format('Y');

            // Pilih kelas awal dan kelas saat ini
            $kelasAwal = $faker->randomElement($kelasIds);
            $kelasSaatIni = $faker->randomElement($kelasIds);

            Santri::create([
                'nis' => 'TPA' . str_pad($i, 3, '0', STR_PAD_LEFT),
                'nama_lengkap' => $jenisKelaminRandom == 'Laki-laki' ?
                    $faker->firstNameMale . ' ' . $faker->lastName :
                    $faker->firstNameFemale . ' ' . $faker->lastName,
                'nama_panggilan' => $faker->firstName,
                'jenis_kelamin' => $jenisKelaminRandom,
                'tempat_lahir' => $faker->city,
                'tanggal_lahir' => $tanggalLahir->format('Y-m-d'),
                'umur' => $umur,
                'hobi' => implode(', ', $faker->randomElements($hobbies, rand(1, 3))),
                'riwayat_penyakit' => $faker->optional(0.3)->sentence(5),
                'alamat' => $faker->address,
                'sekolah' => $faker->randomElement($jenjangSekolah) . ' ' . $faker->numberBetween(1, 20) . ' ' . $faker->city,
                'kelas' => $faker->randomElement($jenjangKelas),
                'jilid_juz' => $faker->randomElement($juzJilid),
                'status' => $faker->randomElement($statusOptions),
                'kelas_awal_id' => $kelasAwal,
                'kelas_id' => $kelasSaatIni,
                'nama_ayah' => $faker->firstNameMale . ' ' . $faker->lastName,
                'nama_ibu' => $faker->firstNameFemale . ' ' . $faker->lastName,
                'pekerjaan_ayah' => $faker->randomElement($pekerjaan),
                'pekerjaan_ibu' => $faker->randomElement($pekerjaan),
                'no_hp_ayah' => '08' . $faker->numerify('##########'),
                'no_hp_ibu' => '08' . $faker->numerify('##########'),
                'nama_wali' => $faker->optional(0.2)->name,
                'pekerjaan_wali' => $faker->optional(0.2)->randomElement($pekerjaan),
                'no_hp_wali' => $faker->optional(0.2)->numerify('08##########'),
                'pas_foto_path' => null,
                'akta_path' => null,
            ]);
        }
    }
}
