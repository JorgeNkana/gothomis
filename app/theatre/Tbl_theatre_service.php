<?php

namespace App\admin;

use Illuminate\Database\Eloquent\Model;

class Tbl_theatre_service extends Model
{
    //use \App\UuidForKey; 
	protected $fillable=['service_type','procedure_category','item_id'];
}