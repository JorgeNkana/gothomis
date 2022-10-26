<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class TblForTraumaClients extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tbl_trauma_clients', function (Blueprint $table) {
            $table->increments('id');
            $table->string('first_name',50)->nullable();
            $table->string('surname',50)->nullable();
            $table->string('gender',6)->nullable();
            $table->date('dob')->nullable();
            $table->integer('estimated_age')->nullable();
            $table->string('estimated_age_group')->nullable();
            $table->integer('arrival_mode',false,true)->length(1)->nullable();
            $table->foreign('arrival_mode')->references('id')->on('tbl_arrival_modes');
            $table->boolean('mass_casuality')->default(false);
            $table->boolean('dead_on_arrival')->default(false);
            $table->string('incident_location',180)->nullable();
            $table->boolean('pregnant')->default(false);
            $table->integer('triage_category',false,true)->length(1)->nullable();
            $table->foreign('triage_category')->references('id')->on('tbl_triage_categories');
            $table->integer('registered_by')->nullable()->unsigned();
            $table->foreign('registered_by')->references('id')->on('users');
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
        Schema::dropIfExists('tbl_trauma_clients');
    }
}