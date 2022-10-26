<?php

namespace App\ClinicalServices;

use Illuminate\Database\Eloquent\Model;

class Tbl_complaint extends Model
{
	//use \App\UuidForKey; 
    protected  $fillable =['description','duration','duration_unit','status','other_complaints','history_exam_id','hpi'];
}