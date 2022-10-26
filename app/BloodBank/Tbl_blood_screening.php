<?php

namespace App\BloodBank;

use Illuminate\Database\Eloquent\Model;

class Tbl_blood_screening extends Model
{
    //
    protected $fillable=['assay_type','facility_id','user_id','patient_id','blood_group','rh','rpr','hbsag','hcv','hiv','donor_number'];
}