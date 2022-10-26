<?php

namespace App\Pharmacy;

use Illuminate\Database\Eloquent\Model;

class Tbl_stock_reconsilliation extends Model
{
    //
    protected $fillable=['item_id','old_quantity','current_quantity','facility_id','user_id','store_id',
        'store_type_id','batch_no','reason','column_id'];
}