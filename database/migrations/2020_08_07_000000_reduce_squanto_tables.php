<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class ReduceSquantoTables extends Migration
{
    public function up()
    {
        // TODO: proper migrate instead of bluntly drop everything!!
        Schema::dropIfExists('squanto_line_translations');
        Schema::dropIfExists('squanto_lines');
        Schema::dropIfExists('squanto_pages');

        $this->createNewSchema();
    }

    public function down()
    {
        Schema::dropIfExists('squanto_lines');
    }

    private function createNewSchema()
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
}
