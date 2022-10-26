<?php

namespace App\admin;

use Illuminate\Database\Eloquent\Model;

class Tbl_route_key extends Model
{
	//use \App\UuidForKey; 
	protected $hidden = [
    'id'
];
     protected  $fillable=['facility_id','private_keys','public_keys','base_urls'];
}