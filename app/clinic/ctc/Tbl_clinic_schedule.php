<?php

namespace App\clinic\ctc;

use Illuminate\Database\Eloquent\Model;

class Tbl_clinic_schedule extends Model
{
    protected  $fillable=['on_off','clinic_id','clinic_date','week_day','user_id','facility_id'];
}