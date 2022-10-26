<?php

namespace App\Clinics;

use Illuminate\Database\Eloquent\Model;


class Tbl_eye_examination extends Model
{
	//use \App\UuidForKey; 
    protected  $fillable = ['description','category'];
}