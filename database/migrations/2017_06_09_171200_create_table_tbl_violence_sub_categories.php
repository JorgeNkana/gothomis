<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTableTblViolenceSubCategories extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tbl_violence_sub_categories', function (Blueprint $table) {
            $table->increments('id');
            $table->string('sub_violence',200);
            $table->integer('violence_category_id',false,true)->unsigned();
            $table->foreign('violence_category_id')->references('id')->on('tbl_violence_categories');
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
        Schema::dropIfExists('tbl_violence_sub_categories');
    }
}