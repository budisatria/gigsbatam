<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateGenreBandsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('genre_bands', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('band_id');
            $table->foreign('band_id')->references('id')->on('grupbands')->onDelete('CASCADE');
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
        Schema::drop('genre_bands');
    }
}
