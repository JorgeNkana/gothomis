<?php

namespace App\nursing_care;

use Illuminate\Database\Eloquent\Model;

class Tbl_surgery_physical_examination extends Model
{
	//use \App\UuidForKey; 
    protected  $fillable=['erasor','admission_id','request_id','other_information','inspection','palpation','percussion','auscultation','nurse_id'];
}