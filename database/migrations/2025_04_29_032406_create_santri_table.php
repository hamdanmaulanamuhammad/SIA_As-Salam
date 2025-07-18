<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
         Schema::create('santri', function (Blueprint $table) {
            $table->id();

            // Identitas Santri
            $table->string('nis')->unique();
            $table->string('nama_lengkap');
            $table->string('nama_panggilan')->nullable();
            $table->enum('jenis_kelamin', ['Laki-laki', 'Perempuan']);
            $table->string('tempat_lahir')->nullable();
            $table->date('tanggal_lahir')->nullable();
            $table->integer('umur')->nullable();
            $table->string('hobi')->nullable();
            $table->text('riwayat_penyakit')->nullable();
            $table->text('alamat')->nullable();
            $table->year('tahun_bergabung'); // Kolom tahun bergabung

            // Akademik
            $table->string('sekolah')->nullable();
            $table->string('kelas')->nullable(); // Kelas di sekolah (string)
            $table->string('jilid_juz')->nullable();
            $table->string('status')->default('Aktif');

            // Relasi ke kelas TPA
            $table->unsignedBigInteger('kelas_awal_id')->nullable();
            $table->unsignedBigInteger('kelas_id')->nullable();

            // Wali
            $table->string('nama_wali')->nullable();
            $table->string('pekerjaan_wali')->nullable();
            $table->string('no_hp_wali')->nullable();

            // Dokumen Upload
            $table->string('pas_foto_path')->nullable();
            $table->string('akta_path')->nullable();

            // Timestamps & Soft Deletes
            $table->timestamps();
            $table->softDeletes();

            // Foreign Keys
            $table->foreign('kelas_awal_id')->references('id')->on('kelas')->onDelete('set null');
            $table->foreign('kelas_id')->references('id')->on('kelas')->onDelete('set null');
        });

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('santri');
    }
};
