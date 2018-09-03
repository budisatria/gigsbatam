<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateGigsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('gigs', function (Blueprint $table) {
            $table->increments('id');
            $table->string('nama_gig');
            $table->text('deskripsi');
            $table->string('photo_gig');
            $table->string('lokasi');
            $table->text('detail_lokasi');
            $table->string('lat');
            $table->string('lng');
            $table->Datetime('tanggal_mulai');
            $table->Datetime('tanggal_selesai');
            $table->enum('status', ['0', '1']);
            $table->enum('type_gig', ['sewa', 'post']);
            $table->unsignedInteger('user_id');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('CASCADE');
            $table->enum('aktif', ['Y', 'N']);
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
        Schema::drop('gigs');
    }
}
