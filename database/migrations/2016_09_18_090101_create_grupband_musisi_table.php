<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateGrupbandMusisiTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('grupband_musisi', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('position_id');
            $table->foreign('position_id')->references('id')->on('positions')->onDelete('CASCADE');
            $table->unsignedInteger('musician_id');
            $table->foreign('musician_id')->references('id')->on('musicians')->onDelete('CASCADE');
            $table->unsignedInteger('grupband_id');
            $table->foreign('grupband_id')->references('id')->on('grupbands')->onDelete('CASCADE');
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
        Schema::drop('grupband_musisi');
    }
}
