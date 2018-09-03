<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSewasTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('sewas', function (Blueprint $table) {
            $table->increments('id');
            $table->Integer('total_biaya');
            $table->unsignedInteger('gig_id');
            $table->foreign('gig_id')->references('id')->on('gigs')->onDelete('CASCADE');
            $table->Integer('object_id');
            $table->Integer('subject_id');
            $table->enum('status', ['0', '1', '2', '3', '4','5']);
            $table->enum('status_request', ['0', '1', '2']);
            $table->enum('type_sewa', ['hireband', 'hiremusisi', 'bandhire', 'musisihire']);
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
        Schema::drop('sewas');
    }
}
