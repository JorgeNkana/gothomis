<?php

namespace App\Inventory;

use Illuminate\Database\Eloquent\Model;

class Tbl_inventory_issuing extends Model
{
	//use \App\UuidForKey; 
    protected $fillable = ['quantity','item_received_id','issuing_officer_id','receiver_id','department_id'];

    public function receivings(){
        return $this->belongsTo('App\Tbl_inventory_receiving');
    }
}