<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateClassifiedPackagesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('classified_packages', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('logo');
            $table->string('name');
            $table->string('price');
            $table->string('product_upload');
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
        Schema::dropIfExists('classified_packages');
    }
}
