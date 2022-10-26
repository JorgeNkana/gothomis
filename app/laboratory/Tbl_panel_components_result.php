<?php

namespace App\laboratory;

use Illuminate\Database\Eloquent\Model;

class Tbl_panel_components_result extends Model
{
	//use \App\UuidForKey; 
    //
    protected  $fillable=['item_id','component_id','order_id','sample_no','component_name_value','component_name','si_units','minimum_limit','maximum_limit', 'visit_date_id'];

}