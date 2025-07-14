<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
    Schema::create('kelas_semester', function (Blueprint $table) {
        $table->id();
        $table->unsignedBigInteger('kelas_id');
        $table->unsignedBigInteger('semester_id');
        $table->unsignedBigInteger('wali_kelas_id');
        $table->unsignedBigInteger('mudir_id');
        $table->boolean('sudah_diproses')->default(false); // untuk deteksi auto insert santri
        $table->timestamps();

        $table->foreign('kelas_id')->references('id')->on('kelas')->onDelete('cascade');
        $table->foreign('semester_id')->references('id')->on('semesters')->onDelete('cascade');
        $table->foreign('wali_kelas_id')->references('id')->on('users')->onDelete('cascade');
        $table->foreign('mudir_id')->references('id')->on('users')->onDelete('cascade');
    });


    }

    public function down()
    {
        Schema::dropIfExists('kelas_semester');
    }
};
