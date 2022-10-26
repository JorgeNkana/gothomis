<?php

namespace App\Xray_Test;

use Illuminate\Database\Eloquent\Model;

class Tbl_test extends Model
{
    protected  $fillable=['sub_department_id','equipment_id','item_panel_id',
   'item_id','eraser','item_test_range','sample_to_collect'];
}