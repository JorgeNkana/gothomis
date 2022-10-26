<?php

namespace App\Pharmacy;

use Illuminate\Database\Eloquent\Model;

class Tbl_tracer_medicine_mapping extends Model
{
    //use \App\UuidForKey; 
    //
    protected $fillable=['item_id','tracer_medicine_id'];
	
	public function item()
    {
        return $this->belongsTo('App\Item_setups\Tbl_item', 'item_id');
    }
}