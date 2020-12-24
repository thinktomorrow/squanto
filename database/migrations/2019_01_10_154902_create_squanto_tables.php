<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSquantoTables extends Migration
{
    public function up()
    {
        Schema::create('squanto_lines', function (Blueprint $table) {
            $table->increments('id');
            $table->string('key')->unique();
            $table->json('values')->nullable();
            $table->json('metadata')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down()
    {
        Schema::dropIfExists('squanto_lines');
    }
}
