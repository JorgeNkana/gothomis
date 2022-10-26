<?php

namespace App\Drf;

use Illuminate\Database\Eloquent\Model;

class Tbl_stock extends Model
{
    //
    protected  $fillable=[
        'item_id',
        'item_name',
        'vendor_name',
        'invoice_number',
        'batch_number',
        'expiry_date',
        'received_date',
        'unit_price',
        'quantity',
        'user_name',
        'user_id',
        'balance',
        'pending_balance',
        'useless',
        'useless_reason',
        'control_in',
        'control_out',
    ];
}