<?php

namespace App\Model\Nhif;

use Illuminate\Database\Eloquent\Model;

class IdentificationType extends Model
{
    protected $table = "tbl_identification_types";

    protected $guarded = ["id"];
   
	public static $rules = [
          ];
	
	public static $create_rules = [
        ];
}