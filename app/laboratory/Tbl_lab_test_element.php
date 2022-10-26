<?php

namespace App\laboratory;

use Illuminate\Database\Eloquent\Model;

class Tbl_lab_test_element extends Model
{
	//use \App\UuidForKey; 
    //
protected  $fillable=['item_id','item_test_range','units','item_test_indicator','sample_to_collect','sub_department_id','equipment_id'];
}