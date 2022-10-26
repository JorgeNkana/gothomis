<?php

namespace App\Inventory;

use Illuminate\Database\Eloquent\Model;

class Tbl_inventory_order extends Model
{
	//use \App\UuidForKey; 
    protected $fillable = ['facility_id','user_id'];
}