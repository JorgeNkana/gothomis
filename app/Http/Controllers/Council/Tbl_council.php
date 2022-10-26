<?php

namespace App\Council;

use Illuminate\Database\Eloquent\Model;

class Tbl_council extends Model
{
    //
    protected $fillable=['council_code','council_name','council_types_id','regions_id'];
}