<?php

namespace App\Model\Nhif;

use Illuminate\Database\Eloquent\Model;

class InsuaranceScheme extends Model
{
    protected $table = "tbl_insuarance_schemes";

    protected $guarded = ["id"];
   
	public static $rules = [
          ];
	
	public static $create_rules = [
        
    ];
}