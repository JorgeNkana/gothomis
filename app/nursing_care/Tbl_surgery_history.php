<?php

namespace App\nursing_care;

use Illuminate\Database\Eloquent\Model;

class Tbl_surgery_history extends Model
{
	//use \App\UuidForKey; 
    protected  $fillable=['erasor','admission_id','request_id','information_category','medical','anaesthetic','surgical','nurse_id'];
}