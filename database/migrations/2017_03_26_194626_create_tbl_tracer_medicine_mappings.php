<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTblTracerMedicineMappings extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tbl_tracer_medicine_mappings', function (Blueprint $table) {
            $table->increments('id');
            
            $table->integer('tracer_medicine_id',false,true,10);
            $table->integer('item_id',false,true,10);
			$table->foreign('tracer_medicine_id')->references('id')->on('tbl_tracer_medicines')->onUpdate('cascade');
			$table->foreign('item_id')->references('id')->on('tbl_items')->onUpdate('cascade');
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
        Schema::dropIfExists('tbl_tracer_medicine_mappings');
    }
}