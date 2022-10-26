<?php

namespace App\ctc;

use Illuminate\Database\Eloquent\Model;

class Tbl_ctc_patient_visit extends Model
{
    protected  $fillable=['follow_up','ctc_refferal','nutrition_status','anc','patient_id','visit_date_id','visit_type_code','arv_reason_code','arv_status_code','tb_rx_ipt_code','tb_screening_code','pregnant','functional_status_code','signs_symptoms_code','cd_4_count','who_clinical_stage','weight_sign_value_id','length_sign_value_id','residence_id','user_id'];

}