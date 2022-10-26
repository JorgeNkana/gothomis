<?php

namespace App\Pharmacy;

use Illuminate\Database\Eloquent\Model;

class Tbl_elmis_price extends Model
{
    //
    protected $fillable=[
        'product_code',
        'product_price',
    ];
}