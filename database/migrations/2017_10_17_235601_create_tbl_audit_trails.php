<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTblAuditTrails extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tbl_audit_trails', function (Blueprint $table) {
            $table->increments('id');
            
            $table->string('formName',50);
            $table->string('action',50);
            $table->integer('record_id');
            $table->integer('fields_name');
            $table->text('old_value');
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
        Schema::dropIfExists('tbl_audit_trails');
    }
}