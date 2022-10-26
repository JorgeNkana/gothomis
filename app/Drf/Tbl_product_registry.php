<?php

namespace App\Drf;

use Illuminate\Database\Eloquent\Model;

class Tbl_product_registry extends Model
{
    //
    protected $fillable=[
        'item_code',
        'item_name',
        'item_category',
        'item_sub_category',
        'unit_of_measure',
    ];
}