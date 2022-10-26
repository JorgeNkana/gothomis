<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTblCorpseAdmissions extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
Schema::create('tbl_corpse_admissions', function (Blueprint $table) {
$table->increments('id');

$table->date('admission_date')->nullable();
$table->integer('patient_id',false,true)->nullable()->unsigned();
$table->foreign('patient_id')->references('id')->on('tbl_patients');
$table->integer('corpse_id')->unsigned()->nullable();
$table->foreign('corpse_id')->references('id')->on('tbl_corpses');
$table->integer('dept_id',false,true)->unsigned()->nullable();
$table->foreign('dept_id')->references('id')->on('tbl_departments');
$table->integer('admission_status_id',false,true)->unsigned()->nullable();
$table->foreign('admission_status_id')->references('id')->on('tbl_admission_statuses');
$table->integer('facility_id')->unsigned()->nullable();
$table->foreign('facility_id')->references('id')->on('tbl_facilities');			
$table->integer('user_id')->unsigned()->nullable();
$table->foreign('user_id')->references('id')->on('users');
$table->integer('mortuary_id')->unsigned()->nullable();
$table->foreign('mortuary_id')->references('id')->on('tbl_mortuaries');
$table->integer('cabinet_id')->unsigned()->nullable();
$table->foreign('cabinet_id')->references('id')->on('tbl_cabinets');
$table->integer('corpse_received_id')->unsigned()->nullable();
$table->foreign('corpse_received_id')->references('id')->on('users');
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
        Schema::dropIfExists('tbl_corpse_admissions');
    }
}