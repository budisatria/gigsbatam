<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateGenreMusisiTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('genre_musisi', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('musician_id');
            $table->foreign('musician_id')->references('id')->on('musicians')->onDelete('CASCADE');
            $table->unsignedInteger('genre_id');
            $table->foreign('genre_id')->references('id')->on('genres')->onDelete('CASCADE');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('genre_musisi');
    }
}
