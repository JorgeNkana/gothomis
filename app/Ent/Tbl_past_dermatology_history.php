<?php

namespace App\Ent;

use Illuminate\Database\Eloquent\Model;

class Tbl_past_dermatology_history extends Model
{
    //
    //use \App\UuidForKey;
    protected $fillable=['patient_id','visit_date_id','user_id','past_dermatology','admission_id','facility_id'];
}