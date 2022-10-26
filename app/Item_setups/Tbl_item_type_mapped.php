<?php

namespace App\Item_setups;

use Illuminate\Database\Eloquent\Model;

class Tbl_item_type_mapped extends Model
{
	  protected $fillable=['item_id','item_code','item_category','Dose_formulation',
        'sub_item_category','strength','volume','IsRestricted','unit_of_measure','dispensing_unit','exemption_status', 'nhif_mapping_id'];
}