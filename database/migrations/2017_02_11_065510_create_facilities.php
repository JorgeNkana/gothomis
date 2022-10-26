<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateFacilities extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tbl_facilities', function (Blueprint $table) {
            $table->increments('id');
            
			$table->string('facility_code',15);
			$table->string('facility_name',50);
			$table->integer('facility_type_id',false,true)->unsigned();
			$table->string('address',50);
			$table->string('mobile_number',15);
			$table->string('email',30)->unique();
			$table->integer('council_id',false,true)->unsigned();
			$table->integer('region_id',false,true)->unsigned();
			$table->foreign('facility_type_id')->references('id')->on('tbl_facility_types');
			$table->foreign('council_id')->references('id')->on('tbl_councils');
			$table->foreign('region_id')->references('id')->on('tbl_regions');
			
			
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
        Schema::dropIfExists('tbl_facilities');
    }
}