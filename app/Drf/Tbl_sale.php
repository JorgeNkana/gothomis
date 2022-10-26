<?php

namespace App\Drf;

use Illuminate\Database\Eloquent\Model;

class Tbl_sale extends Model
{
    //
    protected  $fillable=[
        'auth_no',
        'payment_type',
        'nhif_id',
        'item_id',
        'item_name',
        'unit_price',
        'quantity',
        'expiry_date',
        'invoice_number',
        'batch_number',
        'payment_status',
        'buyer_name',
        'seller_name',
        'user_id',
        'PayCntrNum',
        'gepg_receipt',
        'BillId',
    ];
}