<?php

namespace App\Model\Nhif;

use Illuminate\Database\Eloquent\Model;

class InsuaranceItemPrice extends Model
{
    protected $table = "tbl_insuarance_item_prices";

    protected $guarded = ["id"];
   
	public static $rules = [
          ];
	
	public static $create_rules = [
        
    ];
}