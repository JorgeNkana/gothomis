<?php

namespace App\clinic\ctc;

use Illuminate\Database\Eloquent\Model;

class Tbl_clinic_capacity extends Model
{
    protected  $fillable=['clinic_name_id','capacity','user_id','facility_id'];

}