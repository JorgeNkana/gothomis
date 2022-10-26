<?php

namespace App\Inventory;

use Illuminate\Database\Eloquent\Model;

class Tbl_ledger extends Model
{
	//use \App\UuidForKey; 
    protected $fillable = ['ledger_name','ledger_code','description','facility_id'];
}