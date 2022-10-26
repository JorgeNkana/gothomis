<?php

namespace App\nursing_care;

use Illuminate\Database\Eloquent\Model;

class Tbl_nurse_ward extends Model
{
	//use \App\UuidForKey; 
    protected  $fillable=['deleted','ward_id','nurse_id','facility_id'];

    //
}