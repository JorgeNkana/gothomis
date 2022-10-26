<?php

namespace App\Drf;

use Illuminate\Database\Eloquent\Model;

class Tbl_product_price extends Model
{
    //
    protected $fillable=[
        'item_id',
        'item_code',
        'item_name',
        'item_price',
        'category',
        'status'
    ];
}