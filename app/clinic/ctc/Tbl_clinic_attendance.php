<?php

namespace App\clinic\ctc;

use Illuminate\Database\Eloquent\Model;

class Tbl_clinic_attendance extends Model
{
    protected  $fillable=['clinic_capacity','refferal_id','visit_id','next_visit','follow_up_status'];

}