<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class TblTraumaConcepts extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tbl_trauma_concepts', function (Blueprint $table) {
            $table->increments('id');
            $table->string('concept_name',150);
            $table->string('concept_code',150);
            $table->string('section_code',150);
            $table->string('section_name',150);
            $table->string('sub_section_code',150);
            $table->string('sub_section_name',150);
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
        Schema::dropIfExists('tbl_trauma_concepts');
    }
}