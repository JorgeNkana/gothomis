<?php

namespace App\laboratory;

use Illuminate\Database\Eloquent\Model;

class Tbl_staff_section extends Model
{
 //use \App\UuidForKey;   
	
protected  $fillable=['section_id','technologist_id','isAllowed'];
}