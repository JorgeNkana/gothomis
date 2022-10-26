<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTblVendors extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tbl_vendors', function (Blueprint $table) {
            $table->increments('id');
            
            $table->string('vendor_code',15);
            $table->string('vendor_name',50);
            $table->string('vendor_address',30);
            $table->string('vendor_phone_number',15);
            $table->string('vendor_contact_person',15);
            $table->integer('facility_id')->unsigned();
            $table->foreign('facility_id')->references('id')->on('tbl_facilities');
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
        Schema::dropIfExists('tbl_vendors');
    }
}