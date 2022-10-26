<?php

namespace App\Mortuary;

use Illuminate\Database\Eloquent\Model;

class Tbl_cabinet extends Model
{
    //
	//use \App\UuidForKey; 
	protected  $fillable=['cabinet_name','mortuary_id','user_id','capacity','occupied','eraser'];

}