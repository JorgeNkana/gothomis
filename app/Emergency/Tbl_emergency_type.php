<?php

namespace App\Emergency;

use Illuminate\Database\Eloquent\Model;

class Tbl_emergency_type extends Model
{
	//use \App\UuidForKey; 
    protected $fillable=['emergency_type','emergency_name'];

}