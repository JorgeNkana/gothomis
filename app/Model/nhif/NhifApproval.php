<?php

namespace App\Model\Nhif;

use Illuminate\Database\Eloquent\Model;

class NhifApproval extends Model
{
    protected $table = "tbl_nhif_approval_remarks";

    protected $guarded = ["id"];
   
	public static $rules = [ ];
	
	public static $create_rules = [
        
    ];
}