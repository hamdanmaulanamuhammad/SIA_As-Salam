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
        Schema::create('presences', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade'); // Mengubah teacher_id menjadi user_id
            $table->date('date');
            $table->time('arrival_time')->nullable();
            $table->time('end_time')->nullable();
            $table->string('class')->nullable();
            $table->text('material')->nullable();
            $table->string('proof')->nullable(); // Bukti presensi atau izin
            $table->string('leave_reason')->nullable(); // Alasan izin
            $table->text('issues')->nullable(); // Kendala (bisa untuk presensi atau izin)
            $table->enum('type', ['presence', 'leave'])->default('presence'); // Tipe data: presensi atau izin
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('presences');
    }
};
