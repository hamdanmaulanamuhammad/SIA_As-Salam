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
        Schema::create('pengeluaran_administrasi', function (Blueprint $table) {
            $table->id();
            $table->foreignId('administrasi_bulanan_id')->constrained('administrasi_bulanans')->onDelete('cascade');
            $table->string('nama_pengeluaran');
            $table->integer('jumlah');
            $table->text('keterangan')->nullable();
            $table->timestamps();
        });

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pengeluaran_administrasi');
    }
};
