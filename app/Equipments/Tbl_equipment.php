<?php

namespace App\Equipments;

use Illuminate\Database\Eloquent\Model;

class Tbl_equipment extends Model
{
	//use \App\UuidForKey; 
    protected $fillable=['equipment_name','description','equipment_status_id','facility_id','user_id','conditions','sub_department_id','eraser'];
}