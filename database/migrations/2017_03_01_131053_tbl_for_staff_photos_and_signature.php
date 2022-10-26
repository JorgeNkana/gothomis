<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class TblForStaffPhotosAndSignature extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
     Schema::create('tbl_staff_photos',function (Blueprint $table){
            $table->increments('id');
            
            $table->string('photo_path',80);
            $table->string('photo_for',80);
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
        Schema::dropIfExists('tbl_staff_photos');
    }
}