<?php

namespace App\Exemption;

use Illuminate\Database\Eloquent\Model;

class Tbl_violence_sub_category extends Model
{
    //
	////use \App\UuidForKey; 
    protected $fillable=['violence_category_id','sub_violence'];
}