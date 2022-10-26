<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTblVitalSign extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tbl_vital_signs', function (Blueprint $table) {
            $table->increments('id');
            
            $table->integer('vital_sign_id',false,true)->unsigned();
            $table->foreign('vital_sign_id')->references('id')->on('tbl_vitals');
            $table->string('vital_sign_value',8)->nullable();
            $table->integer('visiting_id')->nullable()->unsigned();
            $table->foreign('visiting_id')->references('id')->on('tbl_accounts_numbers');
            $table->integer('registered_by')->nullable()->unsigned();
            $table->foreign('registered_by')->references('id')->on('users');
            $table->date('date_taken',35);
            $table->string('time_taken',30);
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
        Schema::dropIfExists('tbl_vital_signs');
    }
}