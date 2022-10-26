<?php

namespace App\Http\Controllers\Nhif;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use ServiceManager;
use App\Model\Nhif\InsuaranceItem;
use App\Model\Nhif\InsuaranceItemPrice;
use App\Model\Nhif\TempPriceStack;
use DB;
class NhifPriceController extends Controller
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
		ini_set('max_execution_time', -1);
		$fin_year=date('y');
		$curr_year=date('Y')-1;

		$returned_items =priceItemsNhif();
		   


        $items = json_decode($returned_items, true);
		
		DB::table('tbl_temp_price_lists')->truncate();
		foreach ($items as $value) {
		   if($value == '200')
			   continue;
		   
		   $ItemCode   =$value['ItemCode'];
		   $UnitPrice  =$value['UnitPrice'];
		   $PackageID  =$value['PackageID'];
		   $SchemeCode  = $value['SchemeID'];
		   
		   $ItemName   =$value['ItemName'];
		   $is_restricted   =$value['IsRestricted'];
		   $dosage   =$value['Dosage'];
		   
		   $strength   =$value['Strength'];
		   $maximum_quantity   =$value['MaximumQuantity'];
		   $item_type_id   =$value['ItemTypeID'];
		 
			$data=['item_code'    =>$ItemCode,
					'amount'   =>$UnitPrice,
					'scheme_code'  =>$SchemeCode,
					'is_restricted'=>$is_restricted,
					'dosage'=>$dosage,                                           
					'active'=>1,
					
					'financial_year'=>$curr_year.'/'.$fin_year,
					'strength'=>$strength,
					'maximum_quantity'=>$maximum_quantity,           
					
						];

			$tempPrice=[
				'item_code'    =>$ItemCode,
				'item_name'    =>$ItemName ,
				'item_type_id'  =>$item_type_id,                        
				'amount'   =>$UnitPrice,
				'scheme_code'  =>$SchemeCode,
				'package_id'  =>$PackageID,
				'is_restricted'=>$is_restricted,
				'dosage'=>$dosage,                                           
				'active'=>1,
				'strength'=>$strength,
				'maximum_quantity'=>$maximum_quantity                    
				  ];

			TempPriceStack::create($tempPrice);
		}
	   return $this->checkAnyChangedPrice();
    }



    public function checkAnyChangedPrice(){
        $fin_year=date('y');
        $curr_year=date('Y')-1;

        $items_list=TempPriceStack::get();
        //check if new items added..
        $sql="SELECT * FROM tbl_temp_price_lists t1 WHERE t1.item_code";
        
        $packages =DB::SELECT($sql);

        foreach ($packages as $package) {            
           // $package
           $extraItems=['item_code'    =>$package->item_code,
                       'item_type_id'=>$package->item_type_id, 
                       'item_name'    =>$package->item_name                        
                       ];

            $dataUpdates=[
                        'item_code'    =>$package->item_code,
                        'amount'   =>$package->amount,
                        'scheme_code'  =>$package->scheme_code,
                        'is_restricted'=>$package->is_restricted,
                        'dosage'=>$package->dosage,                                           
                        'active'=>1,
                        'financial_year'=>$curr_year.'/'.$fin_year,
                        'strength'=>$package->strength,
                        'maximum_quantity'=>$package->maximum_quantity                    
                          ];
						  
			if(!empty($package->item_code) && InsuaranceItem::where('item_code',$package->item_code)->count()==0){
			   InsuaranceItem::create($extraItems);
			}			   
						  
            

			if(InsuaranceItemPrice::where('item_code',$package->item_code)->where('scheme_code',$package->scheme_code)->where('active',1)->count()==0 && !empty($package->item_code)){
				InsuaranceItemPrice::create($dataUpdates);
			}
		}

		//check if new items removed..
		$sql="SELECT * FROM tbl_insuarance_items  t1 WHERE t1.item_code NOT IN (SELECT item_code FROM tbl_temp_price_lists)";

		$packages =DB::SELECT($sql);

		foreach ($packages as $package) {            
		  // $package                  

		   InsuaranceItemPrice::where('item_code',$package->item_code)
		   ->where('scheme_code',$package->scheme_code)
		   ->update(['active'=>0]);
		   
		}
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
		ini_set('max_execution_time', -1);
		$returned_items =priceItemsNhif();
		$items = json_decode($returned_items, true);
		$fin_year=date('y');
		$curr_year=date('Y')-1;
 
          
		foreach ($items as $value) {
			$ItemCode   =$value['ItemCode'];
			$UnitPrice  =$value['UnitPrice'];
			$PackageID  =$value['PackageID'];
			$ItemName   =$value['ItemName'];
			$is_restricted   =$value['IsRestricted'];
			$dosage   =$value['Dosage'];
			
			$strength   =$value['Strength'];
			$maximum_quantity   =$value['MaximumQuantity'];
			$item_type_id   =$value['ItemTypeID'];
		  
			$data=['amount'   =>$UnitPrice,                      
				   'is_restricted'=>$is_restricted,
				   'financial_year'=>$curr_year.'/'.$fin_year,
				   'strength'=>$strength,
				   'maximum_quantity'=>$maximum_quantity          
				   
					];
					  

			 InsuaranceItemPrice::where('item_code',$ItemCode)
								 ->where('scheme_code',$PackageID)
								 ->update($data);
								
		}
				   
		return;
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $sql="SELECT t1.item_code,item_name FROM tbl_insuarance_items t1
              INNER JOIN tbl_insuarance_item_prices t2 ON t2.item_code=t1.item_code
         WHERE t2.is_restricted=1 AND  t1.item_name LIKE '%".$id."%' GROUP BY t1.item_code";
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