<?php

namespace App\Http\Controllers\Nhif;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use DB;
class NhifConsultationServices extends Controller
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
        $searchKey = $request->input('item_name');
        $scheme_id = $request->input('scheme_id');
           $sql="SELECT t2.item_name,t1.amount AS price,4 AS patient_category_id,2 AS patient_main_category_id,'NHIF' AS patient_category,1 AS item_type_id,1 AS service_id  FROM tbl_insuarance_item_prices t1
             INNER JOIN tbl_insuarance_items t2 ON t1.item_code=t2.item_code
              WHERE t1.active=1 AND t1.scheme_code=".$scheme_id." AND t2.item_type_id=1 AND  t2.item_name LIKE '%".$searchKey."%'
              GROUP BY t1.item_code";
        
        
        return DB::SELECT($sql);
            
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