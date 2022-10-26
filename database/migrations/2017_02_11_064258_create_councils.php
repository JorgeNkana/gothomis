<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCouncils extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tbl_councils', function (Blueprint $table) {
            $table->increments('id');
            $table->string('council_code',6);
			$table->string('council_name',30);
			$table->integer('regions_id',false,true)->unsigned();
			$table->integer('council_types_id',false,true)->length(3)->unsigned();
			$table->foreign('regions_id')->references('id')->on('tbl_regions');
			$table->foreign('council_types_id')->references('id')->on('tbl_council_types');
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
        Schema::dropIfExists('tbl_councils');
    }
}