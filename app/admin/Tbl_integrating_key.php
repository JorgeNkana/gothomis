<?php

namespace App\admin;

use Illuminate\Database\Eloquent\Model;

class Tbl_integrating_key extends Model
{
    //
	//use \App\UuidForKey; 
  protected $fillable=['facility_id','base_urls','api_type','private_keys','public_keys','active'];
}