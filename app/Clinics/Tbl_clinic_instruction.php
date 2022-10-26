<?php

namespace App\Clinics;

use Illuminate\Database\Eloquent\Model;

class Tbl_clinic_instruction extends Model
{
	//use \App\UuidForKey; 
    protected $fillable = ['id','on_off','doctor_requesting_id','dept_id','specialist_id','consultation_id','summary',
        'priority','visit_id','received','sender_clinic_id'];
}