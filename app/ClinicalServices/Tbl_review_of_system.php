<?php

namespace App\ClinicalServices;

use Illuminate\Database\Eloquent\Model;

class Tbl_review_of_system extends Model
{
	//use \App\UuidForKey; 
    protected $fillable = ['system_id','status','review_system_id','review_summary'];
}