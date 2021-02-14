<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateGuchisTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('guchis', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('body');
            $table->unsignedBigInteger('user_id')->nullable();
            $table->unsignedBigInteger('guchi_room_id');
            $table->boolean('anonymous');
            $table->timestamps();
            $table->foreign('guchi_room_id')->references('id')->on('guchi_rooms')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('guchis');
    }
}
