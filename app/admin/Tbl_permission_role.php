<?php

namespace App\admin;

use Illuminate\Database\Eloquent\Model;

class Tbl_permission_role extends Model
{
	 
    protected  $fillable=['permission_id','role_id','grant'];
}