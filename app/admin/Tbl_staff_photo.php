<?php

namespace App\admin;

use Illuminate\Database\Eloquent\Model;

class Tbl_staff_photo extends Model
{
	//use \App\UuidForKey; 
  protected  $fillable=['photo_path','photo_for'];
}