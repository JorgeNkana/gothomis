<?php

namespace App\Clinics;

use Illuminate\Database\Eloquent\Model;

class Tbl_follow_up_status extends Model
{
    protected $fillable = ['follow_up_status_description','follow_up_status_code'];
}