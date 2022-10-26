<?php

namespace App\admin;

use Illuminate\Database\Eloquent\Model;

class Tbl_permission_user extends Model
{
	 //use \App\UuidForKey; 
     protected  $fillable=['permission_id','user_id','grant'];
}