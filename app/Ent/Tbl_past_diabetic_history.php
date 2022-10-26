<?php

namespace App\Ent;

use Illuminate\Database\Eloquent\Model;

class Tbl_past_diabetic_history extends Model
{
    //use \App\UuidForKey;
    protected $fillable=['patient_id','visit_date_id','user_id','past_diabetic','admission_id','facility_id'];
}