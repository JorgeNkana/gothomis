<?php

namespace App\Http\Controllers\Nhif;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Model\Nhif\InsuaranceItem;
use DB;
class NhifItemController extends Controller
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
        //map items services 
        $itemsMappig=InsuaranceItem::where('item_code',$request->nhif_item_code)
                           ->update(['gothomis_item_id'=>$request->gothomis_item_id]);
       if($itemsMappig){
       return customApiResponse("", "Successfully saved", 200,["error"=>""]);

                       }
                       return customApiResponse("", "Item not found for mapping", 404);

    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $sql="SELECT t1.id, t2.dosage item_code, CONCAT('Tshs. ', FORMAT(t2.amount,0),' - ', t1.item_name) AS item_name FROM tbl_insuarance_items t1 JOIN tbl_insuarance_item_prices t2 on t1.item_code = t2.item_code WHERE t1.item_name LIKE '%".$id."%' order by scheme_code";
        return DB::SELECT($sql);
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