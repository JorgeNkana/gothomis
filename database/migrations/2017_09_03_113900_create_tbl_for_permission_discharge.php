<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTblForPermissionDischarge extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tbl_permits', function (Blueprint $table) {
            $table->increments('id');
            
			$table->integer('corpse_id')->unsigned();
            $table->integer('facility_id')->unsigned();
            $table->foreign('corpse_id')->references('id')->on('tbl_corpses');
            $table->foreign('facility_id')->references('id')->on('tbl_facilities');
            $table->integer('permission_status',false,true)->length(1);
            $table->string('descriptions',200)->nullable();
            $table->integer('user_id')->unsigned();
            $table->foreign('user_id')->references('id')->on('users');
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
        Schema::dropIfExists('tbl_permits');
    }
}