<?php

namespace App\BloodBank;

use Illuminate\Database\Eloquent\Model;

class Tbl_blood_stock extends Model
{
    //
    protected $fillable=['blood_group','unit','facility_id','user_id','control','control_in','unit_issued','unit_issued_out','patient_id'];
}