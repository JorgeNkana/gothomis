<?php

namespace App\laboratory;

use Illuminate\Database\Eloquent\Model;

class Tbl_equipment extends Model
{
	//use \App\UuidForKey; 
    //
protected  $fillable=['eraser','equipment_name','sub_department_id','user_id','reagents','equipment_status_id','facility_id'];
}