<?php

namespace App\Model\Item;

use Illuminate\Database\Eloquent\Model;

class Tbl_main_category extends Model
{
    protected $table = "tbl_pay_cat_sub_categories";
    
    protected $guarded = ["id"];
    protected $hidden  = array('pivot');
    
    public static $rules = [
    
    ];
    
    public static $create_rules = [
    
    ];
    
//    function categories()
//    {
//        return $this->hasMany("App\Model\Item\Tbl_item", "sub_category_id");
//    }
    function prices()
    {
        return $this->hasMany("App\Model\Item\Tbl_item_price", "sub_category_id");
    }
}