<?php

namespace App\Pharmacy;

use Illuminate\Database\Eloquent\Model;

class Tbl_rnr_adjustiment extends Model
{
    protected $fillable=['concept_code','adjustment_code',
        'quantity',
        'facility_code','store_id','item_id'];
}