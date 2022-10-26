<?php

namespace App\Model\Item;

use Illuminate\Database\Eloquent\Model;

class Tbl_item extends Model
{
    protected $table = "tbl_items";
    
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
    
    function department()
    {
        return $this->belongsTo("App\Model\Department\Tbl_department", "dept_id");
    }
    function price()
    {
        return $this->hasMany("App\Model\Item\Tbl_item_price", "item_id");
    }
//

    public static function isDuplicate($new){
        $records = Tbl_item::all();
        foreach(array_keys($new) as $field)
            $records = $records->where($field,STRTOUPPER($new[$field]));
        
        return $records->count() > 0 ? True : False;
    }

}