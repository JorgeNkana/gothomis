<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class TblPatientInformedConsents extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
               
        Schema::create('tbl_informed_consents', function (Blueprint $table) {
             $table->increments('id');
             
             $table->integer('relationshipsID',false,true)->unsigned();
             $table->foreign('relationshipsID')->references('id')->on('tbl_relationships');  
             $table->integer('visit_date_id')->unsigned();
             $table->foreign('visit_date_id')->references('id')->on('tbl_accounts_numbers');                          
             $table->string('relative_name',100)->nullable();   
             $table->string('dateSigned',10)->nullable();                    
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
        Schema::dropIfExists('tbl_informed_consents');
    }
}