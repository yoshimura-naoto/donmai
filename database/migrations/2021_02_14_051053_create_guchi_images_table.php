<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateGuchiImagesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('guchi_images', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('guchi_id');
            $table->string('path');
            $table->timestamps();
            $table->foreign('guchi_id')->references('id')->on('guchis')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('guchi_images');
    }
}
