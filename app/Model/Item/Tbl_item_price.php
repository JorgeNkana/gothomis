<?php

namespace App\Model\Item;

use Illuminate\Database\Eloquent\Model;

class Tbl_item_price extends Model
{
    protected $table = "tbl_item_prices";
    
    protected $guarded = ["id"];
    protected $hidden  = array('pivot');
    
    public static $rules = [
    
    ];
    
    public static $create_rules = [
    
    ];
    
    function item()
    {
        return $this->belongsTo("App\Model\Item\Tbl_item", "item_id");
    }
    function category()
    {
        return $this->belongsTo("App\Model\Item\Tbl_main_category", "sub_category_id");
    }

}