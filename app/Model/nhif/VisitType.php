<?php

namespace App\Model\Nhif;

use Illuminate\Database\Eloquent\Model;

class VisitType extends Model
{
    protected $table = "tbl_visit_types";

    protected $guarded = ["id"];
   
	public static $rules = [
          ];
	
	public static $create_rules = [
        
    ];
}