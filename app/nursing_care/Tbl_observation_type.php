<?php

namespace App\nursing_care;

use Illuminate\Database\Eloquent\Model;

class Tbl_observation_type extends Model
{
    //use \App\UuidForKey; 
	protected  $fillable=['observation_name','observation_key_word'];
}