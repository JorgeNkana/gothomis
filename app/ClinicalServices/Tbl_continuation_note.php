<?php

namespace App\ClinicalServices;

use Illuminate\Database\Eloquent\Model;

class Tbl_continuation_note extends Model
{
	//use \App\UuidForKey; 
    protected $fillable = ['notes','patient_id','visit_id','notes_type','user_id','facility_id'];
}