<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTblPermissionRoles extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tbl_permission_roles', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('permission_id',false,true)->length(9)->unsigned();
            $table->foreign('permission_id')->references('id')->on('tbl_permissions');
            $table->integer('role_id',false,true)->length(9)->unsigned();
            $table->foreign('role_id')->references('id')->on('tbl_roles');
            $table->boolean('grant');
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
        Schema::dropIfExists('tbl_permission_roles');
    }
}