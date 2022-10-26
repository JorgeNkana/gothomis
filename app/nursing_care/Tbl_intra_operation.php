<?php

namespace App\nursing_care;

use Illuminate\Database\Eloquent\Model;

class Tbl_intra_operation extends Model
{
	//use \App\UuidForKey; 
    protected  $fillable=['erasor','admission_id','request_id','information_category','noted_value','doctor_id','remarks','nurse_id'];
}