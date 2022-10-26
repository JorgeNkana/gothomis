<?php

namespace App\Drf;

use Illuminate\Database\Eloquent\Model;

class Tbl_drf_reconcilliation extends Model
{
    //$table->double('old_quantity');
             
            protected $fillable=['current_quantity','user_id','stock_id','old_quantity','reason'];
}