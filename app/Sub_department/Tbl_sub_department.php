<?php

namespace App\Sub_department;

use Illuminate\Database\Eloquent\Model;

class Tbl_sub_department extends Model
{
	//use \App\UuidForKey; 
    protected $fillable=['sub_department_name','eraser','department_id'];
}