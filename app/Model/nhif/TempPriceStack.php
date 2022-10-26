<?php

namespace App\Model\Nhif;

use Illuminate\Database\Eloquent\Model;

class TempPriceStack extends Model
{
    protected $table = "tbl_temp_price_lists";

    protected $guarded = ["id"];
   
	public static $rules = [
          ];
	
	public static $create_rules = [
        
    ];
}