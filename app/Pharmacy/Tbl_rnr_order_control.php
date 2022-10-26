<?php

namespace App\Pharmacy;

use Illuminate\Database\Eloquent\Model;

class Tbl_rnr_order_control extends Model
{
    //
    protected $fillable=[
        'order_number',
        'order_status',
        'facilityCode',
         'user_id'
    ];
}