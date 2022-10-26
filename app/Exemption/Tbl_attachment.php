<?php

namespace App\Exemption;

use Illuminate\Database\Eloquent\Model;

class Tbl_attachment extends Model
{
	//use \App\UuidForKey; 
    //
    protected $fillable=['patient_id','describtion','file_path'];
}