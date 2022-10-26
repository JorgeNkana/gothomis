<?php

namespace App\Dental;

use Illuminate\Database\Eloquent\Model;

class Tbl_past_dental_record extends Model
{
    protected $fillable = ['patient_id','past_dental_history','visit_date_id','user_id'];
}