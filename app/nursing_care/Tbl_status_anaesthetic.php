<?php

namespace App\nursing_care;

use Illuminate\Database\Eloquent\Model;

class Tbl_status_anaesthetic extends Model
{
	//use \App\UuidForKey; 
    protected  $fillable=['erasor','admission_id','request_id','information_category','value_noted','nurse_id'];
}