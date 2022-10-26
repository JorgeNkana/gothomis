<?php

namespace App\laboratory;

use Illuminate\Database\Eloquent\Model;

class Tbl_lab_test_panel extends Model
{
	//use \App\UuidForKey; 
    //
protected  $fillable=['panel_name','Item_test_range','Item_unit','Test_indicator'];
}