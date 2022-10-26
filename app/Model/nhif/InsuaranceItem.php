<?php

namespace App\Model\Nhif;

use Illuminate\Database\Eloquent\Model;

class InsuaranceItem extends Model
{
    protected $table = "tbl_insuarance_items";

    protected $guarded = ["id"];
   
	public static $rules = [
          ];
	
	public static $create_rules = [
        
    ];
}