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
            $table->integer('page_id')->unsigned()->nullable(); 
            $table->boolean('published')->default(1); 
            $table->string('key')->unique();
            $table->string('label')->nullable();
            $table->string('description')->nullable();
            $table->tinyInteger('sequence')->default(0);
            $table->enum('type', ['text','textarea','editor'])->default('text');
            $table->string('allowed_html')->nullable();
            $table->timestamps();
        });

        Schema::create('squanto_line_translations', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('line_id')->unsigned();
            $table->string('locale');
            $table->text('value')->nullable();

            $table->unique(['line_id','locale']);
            $table->foreign('line_id')->references('id')->on('squanto_lines')->onDelete('cascade');
        });

        Schema::create('squanto_pages', function (Blueprint $table) {
            $table->increments('id');
            $table->string('label'); 
            $table->string('description')->nullable();
            $table->string('key')->unique();
            $table->tinyInteger('sequence')->default(0);
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::drop('squanto_line_translations');
        Schema::drop('squanto_lines');
        Schema::drop('squanto_pages');
    }
}
