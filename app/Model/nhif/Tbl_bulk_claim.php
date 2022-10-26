<?php

namespace App\Model\nhif;

use Illuminate\Database\Eloquent\Model;

class Tbl_bulk_claim extends Model
{
    protected $table       =  "tbl_bulk_claims";
    protected $guarded     =  ['id'];
	
    protected $softDelete = true;

    public static $create_rules = [
         
    ];

    public static $rules = [
         
	];
}