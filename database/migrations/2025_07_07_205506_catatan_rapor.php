<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('catatan_rapor', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('santri_id');
            $table->unsignedBigInteger('kelas_semester_id');
            $table->text('catatan')->nullable();
            $table->unsignedBigInteger('keputusan_kelas_id')->nullable(); // jika naik, pilih kelas tujuan
            $table->string('predikat')->nullable(); // predikat berdasarkan rata-rata
            $table->timestamps();

            $table->foreign('santri_id')->references('id')->on('santri')->onDelete('cascade');
            $table->foreign('kelas_semester_id')->references('id')->on('kelas_semester')->onDelete('cascade');
            $table->foreign('keputusan_kelas_id')->references('id')->on('kelas')->onDelete('set null');
            $table->enum('status_naik_kelas', ['naik', 'tinggal_kelas'])->default('naik');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('catatan_rapor');
    }
};
