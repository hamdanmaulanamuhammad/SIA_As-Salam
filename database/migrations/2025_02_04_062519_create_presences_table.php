<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('presences', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->date('date');
            $table->string('day'); // Hari mengajar
            $table->time('arrival_time')->nullable();
            $table->time('end_time')->nullable();
            $table->string('class')->nullable();
            $table->text('material')->nullable();
            $table->string('proof')->nullable();
            $table->text('issues')->nullable();
            $table->text('suggestion')->nullable(); // Kritik/saran
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('presences');
    }
};
