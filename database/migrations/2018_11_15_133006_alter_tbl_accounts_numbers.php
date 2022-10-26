<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AlterTblAccountsNumbers extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('tbl_accounts_numbers', function (Blueprint $table) {
            $table->integer('arrival_mode',false,true)->length(1)->nullable();
            $table->foreign('arrival_mode')->references('id')->on('tbl_arrival_modes');
			$table->string('arrival_description',100)->nullable();
            $table->boolean('mass_casuality')->default(false);
            $table->boolean('dead_on_arrival')->default(false);
            $table->string('emergency_occured_where',80)->nullable();
            $table->boolean('pregnant')->default(false);
            $table->integer('triage_category',false,true)->length(1)->nullable();
            $table->foreign('triage_category')->references('id')->on('tbl_triage_categories');
        
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('tbl_accounts_numbers', function (Blueprint $table) {
            //
        });
    }
}