<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTblLabourDeliveryEvents extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tbl_labour_delivery_events', function (Blueprint $table) {
            $table->increments('id');
            
            $table->integer('client_id')->unsigned();
            $table->foreign('client_id')->references('id')->on('tbl_anti_natal_registers');
            $table->integer('user_id')->unsigned();
            $table->foreign('user_id')->references('id')->on('users');
            $table->integer('facility_id')->unsigned();
            $table->foreign('facility_id')->references('id')->on('tbl_facilities');
            $table->integer('tailer_id')->unsigned()->nullable();
            $table->foreign('tailer_id')->references('id')->on('users');
            $table->dateTime('delivery_date');
            $table->string('place_of_delivery',10);
            $table->string('method_of_delivery',10);
            $table->string('vitamin_given',10);
            $table->string('reason_for_scisoring',100)->nullable();
            $table->string('placenter_removed',100);
            $table->dateTime('placenter_removed_date',100)->nullable();
            $table->float('blood_discharged')->nullable();
            $table->float('bp')->nullable();
            $table->string('labour_catalyst')->nullable();
            $table->string('msamba');
            $table->string('midwife_name',45)->nullable();
            $table->string('comment',100)->nullable();
            $table->integer('number_of_newborn',false,true)->length(1);
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
        Schema::dropIfExists('tbl_labour_delivery_events');
    }
}