<?php

namespace App\Model\Department;

use Illuminate\Database\Eloquent\Model;

class Tbl_department extends Model
{
    protected $table = "tbl_departments";
    
    protected $guarded = ["id"];
    protected $hidden  = array('pivot');
    
    public static $rules = [
    
    ];
    
    public static $create_rules = [
    
    ];
    function items()
    {
        return $this->hasMany("App\Model\Item\Tbl_item", "dept_id");
    }
    public static function isDuplicate($new){
        $records = Tbl_department::all();
        foreach(array_keys($new) as $field)
            $records = $records->where($field,STRTOUPPER($new[$field]));
        
        return $records->count() > 0 ? True : False;
    }
}