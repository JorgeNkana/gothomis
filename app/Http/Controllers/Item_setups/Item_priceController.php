<?php

namespace App\Http\Controllers\item_setups;

use App\Item_setups\Tbl_item;
use App\Item_setups\Tbl_item_price;
use App\Payment_types\Tbl_pay_cat_sub_category;
use App\ClinicalServices\Tbl_diagnosis_description;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use DB;
use Illuminate\Support\Facades\Crypt;

class Item_priceController extends Controller
{
    //

    public function payment_sub_category_to_set_price()
    {
       return Tbl_pay_cat_sub_category::where('pay_cat_id','!=',3) ->get();
}

 

 public function itemLabSearch(Request $request)
    {
        $search=$request['search'];
        return Tbl_item::where('item_name','like','%'.$search.'%')
                         ->where('dept_id',2)
                          ->get();
}


public function itemWardGradeSearch(Request $request)
    {
        $search=$request['search'];
        return Tbl_item::where('item_name','like','%'.$search.'%')
                         ->where('dept_id',5)
                          ->get();
}

    public function item_price_registration(Request $prices)
    {

        foreach ($prices->all() as $request)
        {


        $payment_sub_category=$request['sub_category_id'];
        $item_id=$request['item_id'];
        $strtinYear=$request['startingFinancialYear'];
        $endinYear=$request['endingFinancialYear'];
        $facility_id=$request['facility_id'];
        $price=$request['price'];


        $check= Tbl_item_price::where('facility_id',$facility_id)
            ->where('item_id',$item_id)
            ->where('sub_category_id',$payment_sub_category)
            ->where('startingFinancialYear',$strtinYear)
            ->where('endingFinancialYear',$endinYear)
            ->where('price',$price)
            ->get();
        if(count($check)==1)
        {

            $rep='Repetition';
        }
        else{
			$request["status"] = 1;
            Tbl_item_price::create($request);


        }
        }
            return response()->json([
                'msg'=>"Item   Price Successful Registered",
                'status'=>1
            ]);



    }
    public function itemSetup(Request $request)
    {
       $name = $request['name'];
        return Tbl_item::where('item_name','like','%'.$name.'%')->get();
    }

    public function item_price_list($facility_id)
    {

        return  DB::table('tbl_pay_cat_sub_categories')
            ->join('tbl_item_prices','tbl_pay_cat_sub_categories.id','=','tbl_item_prices.sub_category_id')
            ->join('tbl_items','tbl_item_prices.item_id','=','tbl_items.id')
            ->where('tbl_item_prices.facility_id',$facility_id)
            ->where('tbl_item_prices.status','=',1)
            ->select('tbl_pay_cat_sub_categories.sub_category_name','tbl_item_prices.*','tbl_items.item_name')
            ->get();
    }

    public function load_item_priced_list_search(Request $request)
    {

        $facility_id=$request->facility_id;
        $item_id=$request->item_id;

        return  DB::table('tbl_pay_cat_sub_categories')
            ->join('tbl_item_prices','tbl_pay_cat_sub_categories.id','=','tbl_item_prices.sub_category_id')
            ->join('tbl_items','tbl_item_prices.item_id','=','tbl_items.id')
            ->where('tbl_item_prices.facility_id',$facility_id)
            ->where('tbl_item_prices.item_id',$item_id)
            ->where('tbl_item_prices.status','=',1)
            ->select('tbl_pay_cat_sub_categories.sub_category_name','tbl_item_prices.*','tbl_items.item_name')
            ->get();
    }

    public function item_ist_search(Request $request)
    {
        $search=$request['search'];
        return Tbl_item::where('item_name','like','%'.$search.'%')->where('status','=',1)
            ->get();
	}
	
 public function diagnosis_registry(Request $request)
    {
		 
        $count=count(Tbl_diagnosis_description::where('description',$request['description'])->get());
		if($count>0){
		 
 return response()->json([
                'msg'=>"Dupplications",
                'status'=>0
            ]);
		}
       Tbl_diagnosis_description::create($request->all());
	   return response()->json([
                'msg'=>"Successful Registered",
                'status'=>1
            ]); 
}
    public function item_price_delete($id)
    {

        return Tbl_item_price::destroy($id);

    }

    public function item_price_update(Request $request)
    {
        $newData=$request->all();
        $id=$request['id'];
        $data=Tbl_item_price::where('id',$id)->get();
        Tbl_item_price::create([
            'item_id'=>$data[0]->item_id,
            'facility_id'=>$data[0]->facility_id,
            'exemption_status'=>$data[0]->exemption_status,
            'onetime'=>$data[0]->onetime,
            'insurance'=>$data[0]->insurance,
            'sub_category_id'=>$data[0]->sub_category_id,
            'price'=>$newData['price'],
            'status'=>1,
            'startingFinancialYear'=>$newData['startingFinancialYear'],
            'endingFinancialYear'=>$newData['endingFinancialYear'],
        ]);
        return Tbl_item_price::where('id',$id)->update(['status'=>0]);

    }
   public function load_item_price_per_categories(Request $request)
    {
        $cat=$request->input("sub_category_id");
        $dept=$request->input("dept_id");
       if ($request->input("sub_category_id") && $request->input("dept_id") ) {
         return DB::select("SELECT t2.item_name,t4.department_name,t3.sub_category_name,t1.price FROM tbl_item_prices t1 JOIN tbl_items t2 on t1.item_id=t2.id 
JOIN tbl_pay_cat_sub_categories t3 on t3.id=t1.sub_category_id
JOIN tbl_departments t4 on t2.dept_id=t4.id where dept_id='".$dept."' and sub_category_id='".$cat."'  AND t1.status=1 group by t1.id order by  department_name,t3.sub_category_name asc;");
       }
       else if ($request->input("sub_category_id")  ) {
           return DB::select("SELECT t2.item_name,t4.department_name,t3.sub_category_name,t1.price FROM tbl_item_prices t1 JOIN tbl_items t2 on t1.item_id=t2.id 
JOIN tbl_pay_cat_sub_categories t3 on t3.id=t1.sub_category_id
JOIN tbl_departments t4 on t2.dept_id=t4.id where  sub_category_id='".$cat."' AND  t1.status=1 group by t1.id order by  department_name,t3.sub_category_name asc;");

       }
       else if ($request->input("dept_id")  ) {
           return DB::select("SELECT t2.item_name,t4.department_name,t3.sub_category_name,t1.price FROM tbl_item_prices t1 JOIN tbl_items t2 on t1.item_id=t2.id 
JOIN tbl_pay_cat_sub_categories t3 on t3.id=t1.sub_category_id
JOIN tbl_departments t4 on t2.dept_id=t4.id where dept_id='".$dept."' AND  t1.status=1  group by t1.id order by department_name,t3.sub_category_name asc;");

       }
       else  {
           return DB::select("SELECT t2.item_name,t4.department_name,t3.sub_category_name,t1.price FROM tbl_item_prices t1 JOIN tbl_items t2 on t1.item_id=t2.id 
JOIN tbl_pay_cat_sub_categories t3 on t3.id=t1.sub_category_id
JOIN tbl_departments t4 on t2.dept_id=t4.id  AND  t1.status=1 group by t1.id order by  department_name,t3.sub_category_name asc;");

       }
    }
	
	
    public function mapGfsCodes(Request $request){ 
	   if(!isset($request->item_id) || !isset($request->gfs_code_id) ){
		 return response()->json(['message'=>"Please enter and select item and Gfs to be mapped","status"=>500],500);
	   }
			
		If(DB::table("tbl_gfs_codes_item_mapping")->where("item_id",$request->item_id)->count() > 0){
			$sql="UPDATE tbl_gfs_codes_item_mapping SET gfs_code_id = $request->gfs_code_id WHERE item_id = $request->item_id";
		}
		else{
			$sql="INSERT INTO tbl_gfs_codes_item_mapping(item_id, gfs_code_id) SELECT $request->item_id, $request->gfs_code_id";
		}
		if(DB::statement($sql)){
			return response()->json(['message'=>"Successfully saved","status"=>200],200);
		}
    }

    public function gfsMappings(Request $request){
		$mappings = DB::SELECT("select mapping.id, item.item_name, gfs.description, gfs.code from tbl_gfs_codes_item_mapping mapping join tbl_items item on mapping.item_id = item.id join tbl_gfs_codes gfs on mapping.gfs_code_id = gfs.id");
		
		//only for non-referral hospitals
		if(count($mappings) == 0){
			array_push($mappings, DB::select("select 0 as id, 'Facility GFS Code' as item_name, 'Facility GFS Code' as description, gfscode as code from gepg_accounts")[0]);
		}
		return response()->json($mappings);
    }
	

    public function gfs_list_search(Request $request)
    {
        $search=$request['search'];
        return DB::table('tbl_gfs_codes')->where('description','like','%'.$search.'%')->orWhere('code','like','%'.$search.'%')->get();
	}

    public function deleteGfsMappings($id){ 
	   DB::statement("delete from tbl_gfs_codes_item_mapping where id = $id");
	   return response()->json(['message'=>"Successfully saved","status"=>200],200);
    }

}