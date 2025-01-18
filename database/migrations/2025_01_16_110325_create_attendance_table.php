<?php

use Illuminate\Database\Migrations\Migration;  
use Illuminate\Database\Schema\Blueprint;  
use Illuminate\Support\Facades\Schema;  
  
class CreateAttendanceTable extends Migration  
{  
    public function up()  
    {  
        Schema::create('attendance', function (Blueprint $table) {  
            $table->id();  
            $table->foreignId('event_id')->constrained()->onDelete('cascade'); // Relasi ke tabel events  
            $table->unsignedBigInteger('user_id'); // ID pengguna yang hadir  
            $table->enum('status', ['hadir', 'tidak hadir', 'izin']); // Status kehadiran  
            $table->timestamps(); // Kolom created_at dan updated_at  
        });  
    }  
  
    public function down()  
    {  
        Schema::dropIfExists('attendance');  
    }  
}  
