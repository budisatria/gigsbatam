<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateKonfirmasiPembayaransTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('konfirmasi_pembayarans', function (Blueprint $table) {
            $table->increments('id');
            $table->string('nama_rek');
            $table->string('no_rek');
            $table->string('nama_bank');
            $table->string('photo');
            $table->unsignedInteger('sewa_id');
            $table->foreign('sewa_id')->references('id')->on('sewas')->onDelete('CASCADE');
            $table->Integer('bank_admin_id');;
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
        Schema::drop('konfirmasi_pembayarans');
    }
}
