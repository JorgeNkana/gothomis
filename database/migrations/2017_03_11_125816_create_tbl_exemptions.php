<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTblExemptions extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tbl_exemptions', function (Blueprint $table) {
            $table->increments('id');
            
            $table->string('exemption_reason',80);
            $table->integer('user_id')->unsigned();
            $table->foreign('user_id')->references('id')->on('users');
            $table->string('exemption_no',10);
            $table->integer('patient_id')->unsigned();
            $table->foreign('patient_id')->references('id')->on('tbl_patients');
            $table->integer('exemption_type_id',false,true)->unsigned();
            $table->foreign('exemption_type_id')->references('id')->on('tbl_pay_cat_sub_categories');
            $table->integer('status_id',false,true);
            $table->foreign('status_id')->references('id')->on('tbl_exemption_statuses');
           $table->integer('attachment_id')->unsigned()->nullable();
            $table->foreign('attachment_id')->references('id')->on('tbl_attachments');
            $table->string('reason_for_revoke',80);
            $table->integer('status',false,true)->length(1)->default(0);
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
        Schema::dropIfExists('tbl_exemptions');
    }
}