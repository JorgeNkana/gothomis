<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTblCtcPatientVisits extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tbl_ctc_patient_visits', function (Blueprint $table) {
            $table->increments('id');
            
            $table->integer('visit_date_id')->unsigned();
            $table->foreign('visit_date_id')->references('id')->on('tbl_accounts_numbers');
            $table->integer('patient_id')->unsigned();
            $table->foreign('patient_id')->references('id')->on('tbl_patients');
			$table->integer('user_id')->unsigned();
            $table->foreign('user_id')->references('id')->on('users');
            $table->string('visit_type_code',8);
            $table->integer('weight_sign_value_id')->unsigned();
            $table->foreign('weight_sign_value_id')->references('id')->on('tbl_vital_signs');
            $table->integer('length_sign_value_id')->unsigned();
            $table->foreign('length_sign_value_id')->references('id')->on('tbl_vital_signs');
            $table->string('who_clinical_stage',4)->nullable();
            $table->string('cd_4_count',4)->nullable();
            $table->string('signs_symptoms_code',8)->nullable();
            $table->string('functional_status_code',8)->nullable();
            $table->string('pregnant',8)->nullable();
            $table->string('anc',20)->nullable();
            $table->string('ctc_refferal',20)->nullable();
            $table->string('follow_up',20)->nullable();
            $table->string('nutrition_status',20)->nullable();
            $table->string('tb_screening_code',8)->nullable();
            $table->string('tb_rx_ipt_code',8)->nullable();
            $table->string('arv_status_code',8)->nullable();
            $table->string('arv_reason_code',8)->nullable();
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
        Schema::dropIfExists('tbl_ctc_patient_visits');
    }
}