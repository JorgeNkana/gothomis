<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTblFamilyRelations extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tbl_ctc_family_informations', function (Blueprint $table) {
            $table->increments('id');
            
            $table->integer('patient_id')->unsigned();
            $table->foreign('patient_id')->references('id')->on('tbl_patients');
            $table->integer('user_id')->unsigned();
            $table->foreign('user_id')->references('id')->on('users');
            $table->string('unique_ctc_number',35)->nullable();
            $table->string('relative_name',100)->nullable();
            $table->string('health_facility_file',35)->nullable();
            $table->string('hiv_status',8)->nullable();
            $table->string('hiv_care',1)->nullable();
            $table->string('age',12)->nullable();
            $table->integer('relation_id',false,true)->unsigned();
            $table->foreign('relation_id')->references('id')->on('tbl_relationships');


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
        Schema::dropIfExists('tbl_ctc_family_informations');
    }
}