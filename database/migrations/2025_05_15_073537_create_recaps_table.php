<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('recaps', function (Blueprint $table) {
            $table->id();
            $table->string('periode');
            $table->time('batas_keterlambatan');
            $table->decimal('mukafaah', 15, 2);
            $table->decimal('bonus', 15, 2);
            $table->json('dates');
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('recaps');
    }
};
