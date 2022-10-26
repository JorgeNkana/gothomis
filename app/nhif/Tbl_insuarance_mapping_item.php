<?php

namespace App\nhif;

use Illuminate\Database\Eloquent\Model;

class Tbl_insuarance_mapping_item extends Model
{
     protected  $fillable=['item_code','item_name','item_type_id','package_id',
                           'maximum_quantity','item_id','unit_price'];

                
}