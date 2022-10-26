<?php

namespace App\Inventory;

use Illuminate\Database\Eloquent\Model;

class Tbl_inventory_request extends Model
{
	//use \App\UuidForKey; 
    protected $fillable=['quantity','item_id','department_id','facility_id','status'];
}