<?php

namespace App\laboratory;

use Illuminate\Database\Eloquent\Model;

class Tbl_panel extends Model
{
	//use \App\UuidForKey; 
    protected  $fillable=['erasor','item_id','user_id','panel_name','equipment_id'];

}