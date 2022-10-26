<?php

namespace App\laboratory;

use Illuminate\Database\Eloquent\Model;

class Tbl_lab_result extends Model
{
	//use \App\UuidForKey; 
    //
protected  $fillable=['result','lab_order','verified_by','verified_time','approved_by','approved_time'];
}