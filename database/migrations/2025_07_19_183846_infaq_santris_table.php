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
        Schema::create('infaq_santris', function (Blueprint $table) {
            $table->id();
            $table->foreignId('santri_id')->constrained('santri')->onDelete('cascade'); // Ubah 'santris' jadi 'santri'
            $table->foreignId('infaq_tahunan_id')->constrained('infaq_tahunans')->onDelete('cascade');
            $table->string('bulan');
            $table->integer('infaq_wajib')->default(0);
            $table->integer('infaq_sukarela')->default(0);
            $table->timestamps();
        });

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('infaq_santris');
    }
};
