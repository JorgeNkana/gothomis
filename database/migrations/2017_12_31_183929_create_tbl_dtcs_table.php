<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTblDtcsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tbl_dtcs', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('patient_id',false,true)->length(11)->unsigned();
            $table->foreign('patient_id')->references('id')->on('tbl_patients');
            $table->integer('visit_id',false,true)->length(11)->unsigned();
            $table->foreign('visit_id')->references('id')->on('tbl_accounts_numbers');
            $table->integer('user_id',false,true)->length(11)->unsigned();
            $table->foreign('user_id')->references('id')->on('users');
            $table->integer('facility_id',false,true)->length(11)->unsigned();
            $table->foreign('facility_id')->references('id')->on('tbl_facilities');
            $table->string('dir_duration',3)->nullable();
            $table->decimal('water_sugar_loss')->nullable();
            $table->string('stool_blood',7)->nullable();
            $table->string('fever',7)->nullable();
            $table->string('vomiting',7)->nullable();
            $table->string('other_sign',200)->nullable();
            $table->string('other_treatment',200)->nullable();
            $table->string('intravesel_water',7)->nullable();
            $table->string('dct_duration',2)->nullable();
            $table->string('dct_unit',1)->nullable();
            $table->decimal('ors_in')->nullable();
            $table->decimal('zink_in')->nullable();
            $table->decimal('ors_out')->nullable();
            $table->decimal('zink_out')->nullable();
            $table->string('output')->nullable();
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
        Schema::dropIfExists('tbl_dtcs');
    }
}