<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateWithdrawTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('withdraw', function (Blueprint $table) {
            $table->increments('id');
            $table->Integer('jumlah');
            $table->unsignedInteger('saldo_id');
            $table->foreign('saldo_id')->references('id')->on('saldo')->onDelete('CASCADE');
            $table->enum('status', ['0', '1']); 
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
        Schema::drop('withdraw');
    }
}
