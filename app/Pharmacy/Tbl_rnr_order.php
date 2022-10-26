<?php

namespace App\Pharmacy;

use Illuminate\Database\Eloquent\Model;

class Tbl_rnr_order extends Model
{
    //
    protected $fillable=[
        'fullSupply',
        'emergency',
        'programCode',
        'item_name',
        'item_code',
        'facilityCode',
        'quantityDispensed',
        'quantityReceived',
        'beginningBalance',
        'stockInHand',
        'adjustment',
        'stockOutDays',
        'amountNeeded',
        'quantityRequested',
        'reasonForRequestedQuantity',
        'order_number',
        'order_status',
        'user_id'
    ];
}