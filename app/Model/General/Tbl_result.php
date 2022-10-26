<?php

namespace App\Model\General;

use Illuminate\Database\Eloquent\Model;

class Tbl_result extends Model
{
    protected $table       =  "tbl_results";
    protected $guarded     =  ['id'];

    public static $create_rules = [
        'description' => 'required',
        'item_id' => 'required',
        'post_user' => 'required'
    ];

    public static $rules = [
        'description' => 'required',
        'item_id' => 'required',
        'post_user' => 'required'
    ];
}