<?php

namespace App\Urology;

use Illuminate\Database\Eloquent\Model;

class Tbl_past_urology_history extends Model
{
    //
    protected $fillable=['patient_id','visit_date_id','user_id','past_urology','admission_id','facility_id'];
}