<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTblForRoutingKeys extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tbl_route_keys', function (Blueprint $table) {
             $table->increments('id');
             
			 $table->integer('facility_id')->unsigned();
			 $table->foreign('facility_id')->references('id')->on('tbl_facilities');
			 $table->string('private_keys',80)->nullable();
			 $table->string('public_keys',80)->nullable();
			 $table->string('base_urls',100)->nullable();
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
        Schema::dropIfExists('tbl_route_keys');
    }
}