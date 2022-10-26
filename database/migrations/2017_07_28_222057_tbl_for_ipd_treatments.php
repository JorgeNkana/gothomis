<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class TblForIpdTreatments extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tbl_ipdtreatments', function (Blueprint $table) {
             $table->increments('id');
             
             $table->string('remarks',150)->nullable();
             $table->string('deleted',1)->nullable();
             $table->string('timedosage',12);
             $table->date('date_dosage');
             $table->integer('admission_id')->unsigned();
             $table->foreign('admission_id')->references('id')->on('tbl_admissions');  
             $table->integer('patient_id')->unsigned();
             $table->foreign('patient_id')->references('id')->on('tbl_patients');       
             $table->integer('item_id',false,true)->unsigned()->nullable();  
             $table->foreign('item_id')->references('id')->on('tbl_items');              
             $table->integer('user_id')->unsigned();
             $table->foreign('user_id')->references('id')->on('users');
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
        Schema::dropIfExists('tbl_ipdtreatments');
    }
}