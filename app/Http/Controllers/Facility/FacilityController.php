<?php

namespace App\Http\Controllers\Facility;

use App\Facility\Tbl_facility;
use App\Facility\Tbl_facility_type;
use App\Facility\Tbl_reattendance_free_day;
use App\classes\DataSnch;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\admin\Tbl_integrating_key;
use DB;
class FacilityController extends Controller
{
    //
    //facility_type CRUD
    public function facility_type_registration(Request $request)
    {


        $facility_type=$request['description'];
        $check= Tbl_facility_type::where('description',$facility_type)->get();
        if(count($check)==1)
        {
            return  $facility_type." "."Already Registered";
        }
        else{
            Tbl_facility_type::create($request->all());

            return  "SuccessFul!!!..";
        }
    }

    public function facility_type_list()
    {
        return Tbl_facility_type::get();
    }


    public function facility_type_delete($id)
    {

        return Tbl_facility_type::destroy($id);

    }

    public function facility_type_update(Request $request)
    {
        $id=$request['id'];
        return Tbl_facility_type::where('id',$id)->update($request->all());
    }


    //facilities CRUD
    public function facility_registration(Request $request)
    {
		//code inserted to register reattendance free days
		if($request->has('reattendance_free')){
			if(Tbl_reattendance_free_day::where("facility_id",$request->reattendance_free['facility_id'])->count() > 0)
				Tbl_reattendance_free_day::where("facility_id",$request->reattendance_free['facility_id'])->update(['days'=>$request->reattendance_free['reattendance_free_days']]);
			else
				Tbl_reattendance_free_day::create(["facility_id"=>$request->reattendance_free['facility_id'],"days"=>$request->reattendance_free['reattendance_free_days']]);
			
			return  "Value Set Successfully";
		}
		///////end
		
		
		$facility_name=$request['facility_name'];
        $check= Tbl_facility::where('facility_name',$facility_name)->count();
        if($check > 0){
            return  $facility_name." "."Already Registered";
        }else{
			
           $registeredFacility=Tbl_facility::create($request->all());
		   Tbl_reattendance_free_day::create(["facility_id"=>$registeredFacility->id,"days"=>$request->reattendance_free_days,"user_id"=>$request->user_id]);
			/* $foliolist_array=array();
			 
			 
			  $intergratingKeys=Tbl_integrating_key::where('api_type',2)->get();
			  
			  if(count($intergratingKeys) ==0){	 
                  return response()->json([
                           'data' =>'Please Register Central database API',
                           'status' =>0
                       ]);  
                 }
			  
		      $base_urls=$intergratingKeys[0]->base_urls;
		      $private_keys=$intergratingKeys[0]->private_keys;
		      $public_keys=$intergratingKeys[0]->public_keys;
		 
		
        $facility_info=array();
        $diseases=array();
        $items_array =array();
        //$entity_array =array();
        $entity_array["entities"]=array();
			foreach($registeredFacility as $row) {
            $facility_info['facility_id']=$row->id;
            $facility_info['facility_name']=$row->facility_name;
            $facility_info['facility_code']=$row->facility_code;
            $facility_info['facility_type_id']=$row->facility_type_id;
            $facility_info['address']=$row->address;
            $facility_info['mobile_number']=$row->mobile_number;
            $facility_info['email']=$row->email;
            $facility_info['council_id']=$row->council_id;
            $facility_info['region_id']=$row->region_id;
         	         
            array_push($foliolist_array,$facility_info);


        }
		$entity_array["entities"]=$foliolist_array;
        $data_string=json_encode($entity_array,JSON_PRETTY_PRINT);
			//return  $data_string;
			
			
			return self::facilitySync($data_string,$base_urls,$private_keys,$public_keys);
			
            /*/return  $facility_name." Was Successfully registered";
        }
    }
	
	
	public function downloadFacility(Request $request){
		foreach ($request->downloadedFacilities AS $downloadedFacility){
		
		$facility_id=$downloadedFacility['id'];
		$facility_code=$downloadedFacility['facility_code'];
		$facility_name=$downloadedFacility['facility_name'];
		$facility_type_id=$downloadedFacility['facility_type_id'];
		$address=$downloadedFacility['address'];
		$mobile_number=$downloadedFacility['mobile_number'];
		$email=$downloadedFacility['email'];		
		$region_id=$downloadedFacility['region_id'];		
		$council_id=$downloadedFacility['council_id'];		
		
		$sql="INSERT IGNORE INTO tbl_facilities SET 
		      id='".$facility_id."',
		      facility_code='".$facility_code."',    
		      facility_name='".$facility_name."',    
		      facility_type_id='".$facility_type_id."',    
		      address='".$address."',    
		      mobile_number='".$mobile_number."',    
		      email='".$email."',    
			  created_at=CURRENT_TIMESTAMP,    
		      updated_at=CURRENT_TIMESTAMP,   
		      region_id='".$region_id."',    
		      council_id='".$council_id."'";
          DB::statement($sql);
		 
		 
		} 
		
		return response()->json([
                'data' => $facility_name." was success downloaded to your facility server",
                'status' => 1
            ]);
	}	
	public function saveIpAddress(Request $request){
		$facility_id=$request->facility_id;
		$facility_code=$request->ip_address;
		$api_type=$request->api_type;
		
		if(empty($request->ip_address)){
		return response()->json([
                'data' => "BASE URL MUST BE FILLED",
                'status' => 0
            ]);
			
		}else if(empty($request->public_keys)){
			return response()->json([
                'data' => "PUBLIC KEYS MUST BE FILLED",
                'status' => 0
            ]);
			
		}
		else if(empty($request->api_type)){
			return response()->json([
                'data' => "API TYPE MUST BE FILLED",
                'status' => 0
            ]);
			
		}
		
		if(Tbl_integrating_key::where('facility_id',$facility_id)->where('api_type',$api_type)->get()->count() == 0){
			
		Tbl_integrating_key::create($request->all());			
			  
		   return response()->json([
                'data' => "Integrating Keys was successfully saved to your facility server",
                'status' => 1
            ]);
		  
		}else{
			Tbl_integrating_key::where('facility_id',$facility_id)->where('api_type',$api_type)->update(['base_urls'=>$request->ip_address,'private_keys'=>$request->private_keys,'public_keys'=>$request->public_keys]);			
		
		
 return response()->json([
                'data' => "Integrating Keys was successfully Updated",
                'status' => 1
            ]);		
		}
	}
	
	public function sendFacilityCentrally(Request $request){
		//$info = json_decod$request,true);
		//return $request->all();
		$facility_id=$request['entities'][0]['facility_id'];
		$facility_name=$request['entities'][0]['facility_name'];
		$facility_code=$request['entities'][0]['facility_code'];
		$facility_type_id=$request['entities'][0]['facility_type_id'];
		$address=$request['entities'][0]['address'];
		$mobile_number=$request['entities'][0]['mobile_number'];
		$email=$request['entities'][0]['email'];
		$council_id=$request['entities'][0]['council_id'];
		$region_id=$request['entities'][0]['region_id'];
		
		if(Tbl_facility::where('id',$facility_id)->get()->count() == 0){
		$sql="INSERT INTO tbl_facilities SET 
		      id='".$facility_id."',
		      facility_code='".$facility_code."',    
		      facility_name='".$facility_name."',    
		      facility_type_id='".$facility_type_id."',    
		      address='".$address."',    
		      mobile_number='".$mobile_number."',    
		      email='".$email."',    
			  created_at=CURRENT_TIMESTAMP,    
		      updated_at=CURRENT_TIMESTAMP,   
		      region_id='".$region_id."',    
		      council_id='".$council_id."'";
          DB::statement($sql);
return  $facility_name." Was Successfully registered and Synchronized Centrally.";		  
		}else{
			return  $facility_name.", Was already registered and Synchronized Centrally.";		
			
		}
		
	}
	
	
	public static function facilitySync($data_string,$base_urls,$private_keys,$public_keys)
    {
         $request=$base_urls.'/api/sendFacilityCentrally';
         $url=$base_urls.'/api/sendFacilityCentrally';
		 $request_method = 'POST';				
		$ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json'));
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS,$data_string);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $response  = curl_exec($ch);
        curl_close($ch);
        return $response;
		
	  
    }

    public function facility_list()
    {
        return Tbl_facility::get();
    }


    public function facility_delete($id)
    {

        return Tbl_facility::destroy($id);

    }

    public function facility_update(Request $request)
    {
        $id=$request['id'];
        return Tbl_facility::where('id',$id)->update($request->all());
    }

    public function getReferringFacilities(Request $request)
    {
    	 return DB::table('tbl_facilities')
					->join('tbl_councils', 'tbl_facilities.council_id','=','tbl_councils.id')
					->where('tbl_facilities.facility_name','like',"%$request->key%")
					->select('tbl_facilities.id', DB::Raw("concat(tbl_facilities.facility_name, ' (',tbl_councils.council_name,')') as facility_name"))
					->get()->take(20);
     //    return DB::table('tbl_referring_facilities')
					// ->join('tbl_councils', 'tbl_referring_facilities.council_id','=','tbl_councils.id')
					// ->where('tbl_referring_facilities.facility_name','like',"%$request->key%")
					// ->select('tbl_referring_facilities.id', DB::Raw("concat(tbl_referring_facilities.facility_name, ' (',tbl_councils.council_name,')') as facility_name"))
					// ->get()->take(20);
    }
}