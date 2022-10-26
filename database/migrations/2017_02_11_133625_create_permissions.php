<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePermissions extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tbl_permissions', function (Blueprint $table) {
            $table->increments('id',9);
            $table->string('module',80);
            $table->string('glyphicons',30);
            $table->string('title',50);
            $table->integer('main_menu',false,true)->length(1)->unsigned();
            $table->string('keyGenerated',250)->nullable();
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
        Schema::dropIfExists('tbl_permissions');
    }
}