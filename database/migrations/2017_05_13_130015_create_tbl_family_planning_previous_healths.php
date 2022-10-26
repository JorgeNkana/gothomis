<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTblFamilyPlanningPreviousHealths extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tbl_family_planning_previous_healths', function (Blueprint $table) {
            $table->increments('id');
            
            $table->integer('client_id')->unsigned();
            $table->foreign('client_id')->references('id')->on('tbl_family_planning_registers');
            $table->integer('user_id')->unsigned();
            $table->foreign('user_id')->references('id')->on('users');
            $table->integer('facility_id')->unsigned();
            $table->foreign('facility_id')->references('id')->on('tbl_facilities');
            $table->string('headache',8);
            $table->string('yellow_fever',8);
            $table->string('heart_disease',8);
            $table->string('bp',8);
            $table->string('diabet',8);
            $table->string('breast_bunje',8);
            $table->string('varicose_vein',8);
            $table->string('kifafa_medics',8);
            $table->string('tb_medics',8);
            $table->string('other_problems',8)->nullable();
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
        Schema::dropIfExists('tbl_family_planning_previous_healths');
    }
}