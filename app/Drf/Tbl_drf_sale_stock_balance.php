<?php

namespace App\Drf;

use Illuminate\Database\Eloquent\Model;

class Tbl_drf_sale_stock_balance extends Model
{
    //
     protected  $fillable=[
        'item_id',
        'balance',
        'pending_balance',
        'batch_number'
    ];
}