<?php

namespace App\theatre;

use Illuminate\Database\Eloquent\Model;

class Tbl_teeth_arrangement extends Model
{
	//use \App\UuidForKey; 
    protected $fillable = [
        'teeth_number', 'teeth_position', 'erasor'
    ];
}