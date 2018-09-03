<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSaldoDetailTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('saldo_detail', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('saldo_id');
            $table->foreign('saldo_id')->references('id')->on('saldo')->onDelete('CASCADE');
            $table->unsignedInteger('sewa_id');
            $table->foreign('sewa_id')->references('id')->on('sewas')->onDelete('CASCADE');
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
        Schema::drop('saldo_detail');
    }
}
