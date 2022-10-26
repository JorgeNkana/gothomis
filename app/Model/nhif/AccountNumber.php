<?php

namespace App\Model\Nhif;

use Illuminate\Database\Eloquent\Model;

class AccountNumber extends Model
{
    protected $table = "tbl_accounts_numbers";

    protected $guarded = ["id"];
   
	public static $rules = [
          ];
	
	public static $create_rules = [
        ];
}