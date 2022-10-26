<?php

namespace App\nursing_care;

use Illuminate\Database\Eloquent\Model;

class Tbl_surgery_family_social extends Model
{
	//use \App\UuidForKey; 
    protected  $fillable=['erasor','admission_id','request_id','information_category','chronic_illness','substance_abuse','adoption','others','nurse_id'];
}