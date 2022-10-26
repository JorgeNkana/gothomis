<?php

namespace App\laboratory;

use Illuminate\Database\Eloquent\Model;

class Tbl_testspanel extends Model
{
	//use \App\UuidForKey; 
    protected  $fillable=['erasor','item_id','si_units','maximum_limit','user_id','minimum_limit','panel_compoent_name','equipment_id'];

}