<?php

namespace App\BloodBank;

use Illuminate\Database\Eloquent\Model;

class Tbl_blood_request extends Model
{
    //
    protected $fillable=['request_reason','bag_no','blood_group','unit_requested','facility_id','visit_id','patient_id','dept_id','requested_by','processed_by','status','priority'];
}