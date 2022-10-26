<?php

namespace App\Xray;

use Illuminate\Database\Eloquent\Model;

class Tbl_xray extends Model
{
    //
	//use \App\UuidForKey; 
    protected $fillable=['explanation','xray_path'];
}