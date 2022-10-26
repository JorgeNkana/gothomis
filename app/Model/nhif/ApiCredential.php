<?php

namespace App\Model\Nhif;

use Illuminate\Database\Eloquent\Model;

class ApiCredential extends Model
{
    protected $table = "tbl_api_credentials";

    protected $guarded = ["id"];
   
	public static $rules = [
          ];
	
	public static $create_rules = [
        "username"       => "required: username",
        "password"       => "required:password"
    ];

    
	
}