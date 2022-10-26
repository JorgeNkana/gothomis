<?php

namespace App\Pharmacy;

use Illuminate\Database\Eloquent\Model;

class Tbl_tracer_medicine extends Model
{
	//use \App\UuidForKey; 
    //
    protected $fillable=['id','item_name','status'];
	
	
	public function mappings()
    {
        return $this->hasMany('App\Pharmacy\Tbl_tracer_medicine_mapping', 'tracer_medicine_id');
    }
}