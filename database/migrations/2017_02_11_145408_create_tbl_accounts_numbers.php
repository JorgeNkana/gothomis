<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTblAccountsNumbers extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tbl_accounts_numbers', function (Blueprint $table) {
            $table->increments('id');
            
            $table->integer('status',false,true)->length(1)->unsigned()->nullable();
            $table->integer('visit_type',false,true)->length(1)->default(1);
            $table->integer('tallied',false,true)->unsigned()->nullable();
            $table->integer('patient_id')->unsigned();
            $table->foreign('patient_id')->references('id')->on('tbl_patients');
            $table->string('account_number',35);
            $table->string('authorization_number',60)->nullable();
            $table->string('membership_number',60)->nullable();
            $table->string('card_no',20)->nullable();
            $table->integer('facility_id')->unsigned();
            $table->foreign('facility_id')->references('id')->on('tbl_facilities');
           $table->integer('user_id')->unsigned()->nullable();
            $table->foreign('user_id')->references('id')->on('users');
            $table->date('date_attended',35);
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
        Schema::dropIfExists('tbl_accounts_numbers');
    }
}