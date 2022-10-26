<?php

namespace App\Inventory;

use Illuminate\Database\Eloquent\Model;

class Tbl_inventory_item extends Model
{
	//use \App\UuidForKey; 
    protected $fillable = ['item_name','item_code','item_type_id'];
}