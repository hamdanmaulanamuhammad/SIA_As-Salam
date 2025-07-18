<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Santri;
use App\Models\Kelas;
use Faker\Factory as Faker;

class SantriSeeder extends Seeder
{
    public function run(): void
    {
        $faker = Faker::create('id_ID');

        $kelasIds = Kelas::pluck('id')->toArray();

        $jenisKelamin = ['Laki-laki', 'Perempuan'];
        $statusOptions = ['Aktif', 'Tidak Aktif'];
        $jenjangKelas = ['1', '2', '3', '4', '5', '6'];
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

        $counters = [
            'Laki-laki' => [],
            'Perempuan' => [],
        ];

        for ($i = 1; $i <= 50; $i++) {
            $jenisKelaminRandom = $faker->randomElement($jenisKelamin);
            $tanggalLahir = $faker->dateTimeBetween('-18 years', '-6 years');
            $umur = date('Y') - $tanggalLahir->format('Y');
            $tahunBergabung = $faker->numberBetween(2020, date('Y')); // Tahun bergabung acak dari 2020 hingga tahun sekarang
            $tahun = substr($tahunBergabung, -2); // Ambil 2 digit terakhir tahun bergabung

            // Inisialisasi counter untuk tahun jika belum ada
            if (!isset($counters[$jenisKelaminRandom][$tahun])) {
                $counters[$jenisKelaminRandom][$tahun] = 0;
            }

            // Generate NIS
            $prefix = $jenisKelaminRandom === 'Perempuan' ? 'SAA' : 'SIA';
            $counters[$jenisKelaminRandom][$tahun]++;
            $nomorUrut = str_pad($counters[$jenisKelaminRandom][$tahun], 5, '0', STR_PAD_LEFT);
            $nis = $prefix . $tahun . $nomorUrut;

            $kelasAwal = $faker->randomElement($kelasIds);
            $kelasSaatIni = $faker->randomElement($kelasIds);

            Santri::create([
                'nis' => $nis,
                'nama_lengkap' => $jenisKelaminRandom == 'Laki-laki' ?
                    $faker->firstNameMale . ' ' . $faker->lastName :
                    $faker->firstNameFemale . ' ' . $faker->lastName,
                'nama_panggilan' => $faker->firstName,
                'jenis_kelamin' => $jenisKelaminRandom,
                'tempat_lahir' => $faker->city,
                'tanggal_lahir' => $tanggalLahir->format('Y-m-d'),
                'tahun_bergabung' => $tahunBergabung,
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
                'nama_wali' => $faker->optional(0.5)->name,
                'pekerjaan_wali' => $faker->optional(0.5)->randomElement($pekerjaan),
                'no_hp_wali' => $faker->optional(0.5, null)->numerify('08##########'),
                'pas_foto_path' => null,
                'akta_path' => null,
            ]);
        }
    }
}
