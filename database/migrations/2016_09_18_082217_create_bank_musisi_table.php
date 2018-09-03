<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateBankMusisiTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('bank_musisi', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('musician_id');
            $table->foreign('musician_id')->references('id')->on('musicians')->onDelete('CASCADE');
            $table->unsignedInteger('bank_id');
            $table->foreign('bank_id')->references('id')->on('banks')->onDelete('CASCADE');
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
        Schema::drop('bank_musisi');
    }
}
