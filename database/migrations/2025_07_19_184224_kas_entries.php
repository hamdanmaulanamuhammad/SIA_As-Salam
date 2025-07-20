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
        Schema::create('kas_entries', function (Blueprint $table) {
            $table->id();
            $table->foreignId('buku_kas_tahunan_id')->constrained('buku_kas_tahunans')->onDelete('cascade');
            $table->enum('jenis', ['debet', 'kredit']);
            $table->integer('jumlah');
            $table->string('sumber')->nullable(); // untuk uang masuk
            $table->string('tujuan')->nullable(); // untuk uang keluar
            $table->text('keterangan')->nullable();
            $table->string('bukti')->nullable();
            $table->date('tanggal');
            $table->timestamps();
        });

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('kas_entries');
    }
};
