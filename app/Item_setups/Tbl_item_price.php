<?php

namespace App\Item_setups;

use Illuminate\Database\Eloquent\Model;

class Tbl_item_price extends Model
{
	//use \App\UuidForKey; 
    //
    protected $fillable=['item_id','sub_category_id','exemption_status','onetime'
        ,'insurance','facility_id','price','startingFinancialYear','endingFinancialYear','status'];
}