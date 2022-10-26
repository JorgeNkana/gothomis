<?php

namespace App\ctc;

use Illuminate\Database\Eloquent\Model;

class Tbl_ctc_patient_support extends Model
{
    protected  $fillable=['name_treatment_supporter','telephone_number','joined_organisation','on_off','name_organisation','facility_id','patient_id','user_id'];

}