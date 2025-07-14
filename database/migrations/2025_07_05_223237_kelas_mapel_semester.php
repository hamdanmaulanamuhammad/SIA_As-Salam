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
        Schema::create('kelas_mapel_semester', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('kelas_semester_id');
            $table->unsignedBigInteger('mata_pelajaran_id');
            $table->timestamps();

            $table->foreign('kelas_semester_id')->references('id')->on('kelas_semester')->onDelete('cascade');
            $table->foreign('mata_pelajaran_id')->references('id')->on('mapels')->onDelete('cascade');
        });

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('kelas_mapel_semester');
    }
};
