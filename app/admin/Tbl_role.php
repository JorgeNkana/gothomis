<?php

namespace App\admin;

use Illuminate\Database\Eloquent\Model;

class Tbl_role extends Model
{
	 
    protected  $fillable=['title','parent'];
}