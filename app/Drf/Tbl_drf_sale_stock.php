<?php

namespace App\Drf;

use Illuminate\Database\Eloquent\Model;

class Tbl_drf_sale_stock extends Model
{
    //
    protected  $fillable=[
        'item_id',
        'batch_number',
        'expiry_date', 
        'quantity',
        'user_name',
        'user_id',
        
       
    ];
}