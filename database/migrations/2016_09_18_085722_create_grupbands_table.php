<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateGrupbandsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('grupbands', function (Blueprint $table) {
            $table->increments('id');
            $table->string('nama_grupband');
            $table->text('deskripsi');
            $table->string('tipe')->default('Group');
            $table->string('basis')->default('-');
            $table->string('Wilayah');
            $table->string('photo');
            $table->string('cover');
            $table->integer('harga');
            $table->text('youtube_video');
            $table->text('url_website');
            $table->text('username_soundcloud');
            $table->text('username_reverbnation');
            $table->enum('aktif', ['Y', 'N']);
            $table->unsignedInteger('admin_id');
            $table->foreign('admin_id')->references('id')->on('musicians')->onDelete('CASCADE');
            $table->text('slug');
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
        Schema::drop('grupbands');
    }
}
