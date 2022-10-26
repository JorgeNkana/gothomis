<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTblClinicInstructionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
  Schema::create('tbl_clinic_instructions', function (Blueprint $table) {
  $table->increments('id');
           
  $table->string('summary',200)->nullable();
  $table->string('priority',20)->nullable();
  $table->integer('sender_clinic_id',false,true)->unsigned(); 
  $table->foreign('sender_clinic_id')->references('id')->on('tbl_departments');
  $table->integer('received',false,true)->length(1)->unsigned();
  $table->integer('visit_id')->unsigned();
  $table->foreign('visit_id')->references('id')->on('tbl_accounts_numbers');
  $table->integer('dept_id',false,true)->unsigned();
  $table->integer('specialist_id')->unsigned()->nullable();
  $table->integer('doctor_requesting_id')->unsigned()->nullable();
  $table->integer('consultation_id',false,true)->unsigned()->nullable();    
           
  $table->foreign('dept_id')->references('id')->on('tbl_departments');
  $table->foreign('specialist_id')->references('id')->on('users');
  $table->foreign('doctor_requesting_id')->references('id')->on('users');
  $table->foreign('consultation_id')->references('id')->on('tbl_items');
  $table->integer('on_off',false,true)->length(1)->nuulable()->unsigned();        
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
        Schema::dropIfExists('tbl_clinic_instructions');
    }
}