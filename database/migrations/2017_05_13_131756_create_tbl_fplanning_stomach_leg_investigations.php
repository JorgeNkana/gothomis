<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTblFplanningStomachLegInvestigations extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tbl_fplanning_stomach_leg_investigations', function (Blueprint $table) {
            $table->increments('id');
            
            $table->integer('client_id')->unsigned();
            $table->foreign('client_id')->references('id')->on('tbl_family_planning_registers');
            $table->integer('user_id')->unsigned();
            $table->foreign('user_id')->references('id')->on('users');
            $table->integer('facility_id')->unsigned();
            $table->foreign('facility_id')->references('id')->on('tbl_facilities');
            $table->string('liver_inflammation',8);
            $table->string('leg_inflammation',8);
            $table->string('vericose_vein',8);
            $table->string('others',8)->nullable();
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
        Schema::dropIfExists('tbl_fplanning_stomach_leg_investigations');
    }
}