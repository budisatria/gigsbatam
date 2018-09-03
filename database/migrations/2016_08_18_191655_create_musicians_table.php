<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateMusiciansTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('musicians', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name');
            $table->string('email')->unique();
            $table->string('password');
            $table->string('tipe')->default('Solo');
            $table->string('basis')->default('-');
            $table->string('deskripsi');
            $table->string('no_telp');
            $table->string('Wilayah');
            $table->integer('harga_sewa');
            $table->string('photo');
            $table->string('youtube_video');
            $table->string('url_website');
            $table->string('username_soundcloud');
            $table->string('username_reverbnation');
            $table->enum('aktif', ['Y', 'N']);
            $table->text('slug');
            $table->rememberToken();
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
        Schema::drop('musicians');
    }
}
