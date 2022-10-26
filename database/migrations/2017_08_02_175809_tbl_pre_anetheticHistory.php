<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class TblPreAnetheticHistory extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tbl_pre_history_anethetics', function (Blueprint $table) {
            $table->increments('id');
            
			 $table->integer('visit_date_id')->unsigned();
             $table->foreign('visit_date_id')->references('id')->on('tbl_accounts_numbers');                          
             $table->string('history_type',100)->nullable();   
             $table->string('remarks',100)->nullable();
             $table->string('medical',150)->nullable();
             $table->string('surgical',150)->nullable();  			 
             $table->string('anethetic',150)->nullable();  			 
             $table->string('descriptions',150)->nullable();  			 
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
        Schema::dropIfExists('tbl_pre_history_anethetics');
    }
}