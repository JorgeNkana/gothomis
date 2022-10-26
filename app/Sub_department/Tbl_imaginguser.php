<?php

namespace App\Sub_department;

use Illuminate\Database\Eloquent\Model;

class Tbl_imaginguser extends Model
{
	//use \App\UuidForKey; 
    protected $fillable=['user_id','assigned_by','subdept_id','grant','del_user'];
}