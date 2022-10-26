<?php

namespace App\ctc;

use Illuminate\Database\Eloquent\Model;

class Tbl_ctc_family_information extends Model
{
    protected  $fillable=['unique_ctc_number','relative_name','health_facility_file','relation_id','age','hiv_status','hiv_care','patient_id','user_id'];

}