<?php

namespace App\Http\Controllers\Nhif;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Model\Nhif\ApiCredential;

class SetApiCredentialController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        if(!isset($request->username) || !isset($request->password) || !isset($request->FacilityCode) ){
            return ;
        }
        $exist=ApiCredential::Where('active',1)->get();
        if(count($exist) >0){
         return   ApiCredential::Where('active',1)
                         ->update(['username'=>$request->username,'password'=>$request->password,'FacilityCode'=>$request->FacilityCode]);
        }else{
         return   ApiCredential::create(['active'=>1,'username'=>$request->username,'password'=>$request->password,'FacilityCode'=>$request->FacilityCode]);

        }
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}