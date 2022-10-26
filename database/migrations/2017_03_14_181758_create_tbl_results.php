\<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTblResults extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        //
 Schema::create('tbl_results', function (Blueprint $table) {
	$table->increments('id');
    
	$table->integer('order_id')->unsigned()->nullable();
	$table->foreign('order_id')->references('id')->on('tbl_requests');
	$table->integer('item_id',false,true)->unsigned()->nullable(); 		
	$table->foreign('item_id')->references('id')->on('tbl_items');
	$table->string('description',150)->nullable();
	$table->string('panel',80)->nullable();
	$table->string('cancel_reason',250)->nullable();
	$table->integer('unit')->unsigned()->nullable();
	$table->integer('eraser',false,true)->length(1)->unsigned()->nullable();
	$table->foreign('unit')->references('id')->on('tbl_units');
	$table->integer('post_user')->unsigned()->nullable();
	$table->foreign('post_user')->references('id')->on('users');
	$table->time('post_time')->nullable();
	$table->integer('verify_user')->unsigned()->nullable();
	$table->foreign('verify_user')->references('id')->on('users');
	$table->time('verify_time')->nullable();
	$table->boolean('confirmation_status')->nullable();
	$table->string('attached_image', 60)->nullable();
	$table->boolean('notification')->nullable();
	$table->string('remarks',60)->nullable();
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
		Schema::dropIfExists('tbl_results');
    }
}