<?php

namespace App\Http\Controllers\Professional;

use App\Professional\Tbl_proffesional;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
 

class ProfessionalController extends Controller
{
    //
	public function store(Request $request)
    {
        
     return Tbl_proffesional::create($request->all());
        
    }
	
public function index()
    {
        return Tbl_proffesional::get();
    }
}