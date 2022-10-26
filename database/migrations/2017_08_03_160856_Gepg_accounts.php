<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class GepgAccounts extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('gepg_accounts', function (Blueprint $table) {
            $table->increments('id');
            
            $table->string('url',50)->default('http://154.118.230.18/api/bill/request');
            $table->string('self_url',50);
            $table->string('intermediate_url',50);
            $table->string('SpCode',50);
            $table->string('SubSpCode',50);
            $table->string('GfsCode',50);
            $table->string('SpSysId',50)->default('GOTHOMIS');
            $table->string('Ccy',50)->default('TZS');
            $table->string('RemFlag',5)->default('true');
            $table->string('RtrRespFlg',5)->default('true');
            $table->string('UseItemRefOnPay',5)->default('N');
            $table->string('BillPayOpt',50)->default('1');
            $table->string('facility_code',20);
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
        //
    }
}