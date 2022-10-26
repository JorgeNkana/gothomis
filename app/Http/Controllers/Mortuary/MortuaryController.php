<?php

namespace App\Http\Controllers\mortuary;

use App\classes\patientRegistration;
use App\Item_setups\Tbl_item_type_mapped;
use App\laboratory\Tbl_item;
use App\Mortuary\Tbl_cabinet;
use App\Payments\Tbl_encounter_invoice;
use App\Payments\Tbl_invoice_line;
use App\Mortuary\Tbl_corpse_admission;
use App\Mortuary\Tbl_corpse_service;
use App\Mortuary\Tbl_mortuary;
use App\Patient\Tbl_corpse;
use App\Mortuary\Tbl_permit;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;

class MortuaryController extends Controller
{
    public function addMortuaryClass(Request $request)
    {
        $mortuary_class=strtoupper($request->mortuary_class);

         if(empty($mortuary_class)){
            return response()->json([
                'data' => 'MORTUARY CLASS MUST BE FILLED',
                'status' => 0
            ]);

         }
         else if(patientRegistration::duplicate('tbl_items',array('item_name',"((timestampdiff(minute,created_at,CURRENT_TIMESTAMP) >=0))"), array($mortuary_class))==true){

            return response()->json([
                'data' => $mortuary_class.' ALREADY EXISTS',
                'status' => '0'
            ]);

        }

        $item_name_added =Tbl_item::create(array('item_name'=>$mortuary_class,'dept_id'=>7));


        if($item_name_added){

              $item_name_added =Tbl_item_type_mapped::create(array('item_id'=>$item_name_added->id,'item_category'=>'MORTUARY','sub_item_category'=>'MORTUARY'));

            return response()->json([
                'data' => $mortuary_class.' WAS SUCCESSFULLY ADDED',
                'status' => 1
                       ]);
        }else{

            return response()->json([
                'data' => 'SOMETHING WENT WRONG IN YOUR SERVER,FAILED TO ADD NEW ITEM',
                'status' => '0'
            ]);
        }

    }
    public  function giveCabinetCorpse(Request $request){
        $corpse_admission_id=$request->corpse_admission_id;
        $cabinet_id=$request->cabinet_id;
       // $sql="UPDATE `tbl_cabinets` SET `occupied=occupied+1 WHERE id=".$cabinet_id;
       if(Tbl_corpse_admission::WHERE("id",$corpse_admission_id)->update(array("cabinet_id"=>$cabinet_id,"admission_status_id"=>2))){
          // DB::SELECT($sql);
           return response()->json([
               'data' =>'CABINET WAS SUCCESFULLY ASSIGNED',
               'status' => 1
           ]);
       }
    }

	public function getListOfCorpsesToStore(Request $request){
		$sql="SELECT t1.*,t3.name AS doctor_requested,t2.facility_name,
				
				CASE WHEN t1.corpse_id IS NOT NULL THEN (SELECT CONCAT(t4.first_name,' ' ,t4.middle_name,' ',t4.last_name) AS corpse_name FROM tbl_corpses t4 WHERE t1.corpse_id=t4.id) ELSE (SELECT CONCAT(t5.first_name,' ' ,t5.middle_name,' ',t5.last_name) AS corpse_name FROM tbl_patients t5 WHERE t1.patient_id=t5.id) END AS corpse_name,
				
				CASE WHEN t1.corpse_id IS NOT NULL THEN (SELECT t4.gender FROM tbl_corpses t4 WHERE t1.corpse_id=t4.id) ELSE (SELECT t5.gender FROM tbl_patients t5 WHERE t1.patient_id=t5.id) END AS gender,
				
					
				CASE WHEN t1.corpse_id IS NOT NULL THEN (SELECT t4.corpse_record_number FROM tbl_corpses t4 WHERE t1.corpse_id=t4.id) END AS corpse_record_number,
				
				CASE WHEN t1.corpse_id IS NOT NULL THEN (SELECT t4.immediate_cause FROM tbl_corpses t4 WHERE t1.corpse_id=t4.id) END AS immediate_cause,
				
				CASE WHEN t1.corpse_id IS NOT NULL THEN (SELECT t4.underlying_cause FROM tbl_corpses t4 WHERE t1.corpse_id=t4.id) END AS underlying_cause,
				
				
				CASE WHEN t1.corpse_id IS NOT NULL AND t1.patient_id IS NULL THEN 'OUTSIDE CORPSE' ELSE 'INSIDE CORPSE' END AS corpse_category
				
				
               FROM tbl_corpse_admissions  t1
               INNER JOIN tbl_facilities t2 ON t1.facility_id=t2.id                 
               INNER JOIN users t3 ON t1.user_id=t3.id 
			   WHERE t1.facility_id='".$request->facility_id."' 
			      AND t1.admission_status_id=1";

			   return DB::SELECT($sql);

	}


public function mortuaryGradeSearch(Request $request)
    {
        $search=$request['search'];
        return Tbl_item::where('item_name','like','%'.$search.'%')
                         ->where('dept_id',7)
                          ->get();
}

    public function getMortuaryClasses(Request $request)  {
        $searchKey = $request->input('searchKey');
        $getMortuaryClass=  DB::table('vw_shop_items')
            ->where('dept_id',7)
            ->where('item_name','like','%'.$searchKey.'%')
            ->groupBy('item_name')
            ->get();
        return $getMortuaryClass;
    }

    public function getMortuaryClassLists($facility_id)  {
         $getMortuaryClasses= DB::table('vw_shop_items')
             ->where('item_category','MORTUARY')
             ->where('facility_id',$facility_id)
              ->groupBy('item_name')
             ->get();
        return $getMortuaryClasses;
    }

    public function getMortuaryList(Request $request)  {
       $getMortuaryList=Tbl_mortuary::all();
        return $getMortuaryList;

    }

    public function getCabinetsLists($facility_id)  {
        $sql="SELECT * FROM `vw_cabinet_lists` WHERE facility_id='".$facility_id."'";
        $getCabinetsLists=DB::SELECT($sql);
        return $getCabinetsLists;

    }

	public function getCabinetsPerMortuary($mortuary_id)  {
        $sql="SELECT * FROM `vw_cabinet_lists` WHERE mortuary_id='".$mortuary_id."'";
        $getCabinetsLists=DB::SELECT($sql);
        return $getCabinetsLists;

    }

    public function saveCabinets(Request $request){
        $cabinet_name=strtoupper($request->cabinet_name);
        $mortuary_id=strtoupper($request->mortuary_id);
        $capacity=strtoupper($request->capacity);
        if(empty($cabinet_name)){
            return response()->json([
                'data' => 'CABINET NAME MUST BE FILLED',
                'status' => '0'
            ]);
        }else if(!isset($mortuary_id)){
            return response()->json([
                'data' => 'MORTUARY MUST BE SELECTED FROM THE SUGESTION LIST',
                'status' => '0'
            ]);
        }  else if(patientRegistration::duplicate('tbl_cabinets',array('cabinet_name','mortuary_id',"((timestampdiff(minute,created_at,CURRENT_TIMESTAMP) >=0))"),array($cabinet_name,$mortuary_id))==true){
            return response()->json([
                'data' => $cabinet_name.',CABINETS No. ALREADY EXISTS',
                'status' => '0'
            ]);

        }



        if(Tbl_cabinet::create($request->all())){

            return response()->json([
                'data' => $cabinet_name.' CABINET. SUCCEFULLY SAVED.',
                'status' => '1'
            ]);

        }else{

            return response()->json([
                'data' => 'SOMETHING WENT WRONG IN YOUR SERVER',
                'status' => '0'
            ]);
        }
    }



    public function addMortuary(Request $request)
    {
        $mortuary_name=strtoupper($request->mortuary_name);
        $facility_id=strtoupper($request->facility_id);
        $user_id=strtoupper($request->user_id);
        $mortuary_class_id=strtoupper($request->mortuary_class_id);

        if(empty($mortuary_name)){
            return response()->json([
                'data' => 'MORTUARY NAME MUST BE FILLED',
                'status' => 0
            ]);

        }   else if(!isset($mortuary_class_id)){
            return response()->json([
                'data' => ' MORTUARY  CLASS MUST BE SELECTED FROM THE SUGESTION LIST ',
                'status' => '0'
            ]);

        }
        else if(patientRegistration::duplicate('tbl_mortuaries',array('mortuary_name','mortuary_class_id','facility_id',"((timestampdiff(minute,created_at,CURRENT_TIMESTAMP) >=0))"), array($mortuary_name,$mortuary_class_id,$facility_id))==true){
            return response()->json([
                'data' => $mortuary_name.' ALREADY EXISTS',
                'status' => '0'
            ]);

        }

        $mortuary =Tbl_mortuary::create(array('mortuary_class_id'=>$mortuary_class_id,'user_id'=>$user_id,'facility_id'=>$facility_id,'mortuary_name'=>$mortuary_name));

          return response()->json([
                'data' => $mortuary_name.'  SUCCEFULLY SAVED.',
                'status' => 1
            ]);


    }

	public function saveMortuaryBill(Request $request){

		  $corpse_id=$request->selectedService[0]['corpse_id'];
		  $facility_id=$request->selectedService[0]['facility_id'];
		  $user_id=$request->selectedService[0]['user_id'];
		   $invoice=Tbl_encounter_invoice::create([
		  'corpse_id'=>$corpse_id,
		  'user_id'=>$user_id,
		  'facility_id'=>$facility_id,
		]);
		$invoice_id=$invoice->id;

    foreach($request->selectedService AS $selectedService){
	$facility_id=$selectedService['facility_id'];
	$item_price_id=$selectedService['item_price_id'];
	$item_type_id=$selectedService['item_type_id'];
	$patient_category_id=$selectedService['patient_category_id'];
	$payment_filter=$selectedService['payment_filter'];
	$quantity=$selectedService['quantity'];
	$user_id=$selectedService['user_id'];
	$item_name=$selectedService['item_name'];
	$discount_by=$selectedService['discount_by'];
	$status_id=$selectedService['status_id'];
	$discount=$selectedService['discount'];
	$corpse_id=$selectedService['corpse_id'];
	if(!is_numeric($quantity)){

	return response()->json([	'data' =>'PLEASE ENTER NUMERIC VALUE IN QUANTITY',
								'status' => 0
										]);
}

//check duplicate within 3 minutes
/**
else if(patientRegistration::duplicate('tbl_invoice_lines',array('item_type_id','facility_id','corpse_id',"((timestampdiff(minute,created_at,CURRENT_TIMESTAMP) <=1))"), array($item_type_id,$facility_id,$corpse_id))==true){
	return response()->json(['data' =>$item_name.' already recorded,with the same information',
								'status' => 0
										]);

}
**/
else{


		Tbl_invoice_line::create(['invoice_id'=>$invoice_id,
		                         'discount_by'=>$discount_by,
		                         'facility_id'=>$facility_id,
		                         'item_price_id'=>$item_price_id,
		                         'item_type_id'=>$item_type_id,
		                         'quantity'=>number_format($quantity, 2, '.', ''),
		                         'user_id'=>$user_id,
		                        'corpse_id'=>$corpse_id,
		                         'status_id'=>$status_id,
		                         'discount'=>number_format($discount, 2, '.', ''),
		                         'payment_filter'=>$payment_filter]
		                                    );





}

	}


return response()->json(['data' =>strtoupper($item_name).' WAS SUCCEFULLY SAVED',
								'status' => 1,
								'corpse_id' => $corpse_id,
										]);


	}


    public function addCorpseService(Request $request)
    {
        $corpse_admission_id=strtoupper($request->corpse_admission_id);
        $service_number=strtoupper($request->service_number);
        $service_name=strtoupper($request->item_name);
        $user_id=strtoupper($request->user_id);
            if(empty($service_number)){
            return response()->json([
                'data' => 'PLEASE CHECK THE SERVICE',
                'status' => 0
            ]);

        }   else if(!isset($corpse_admission_id)){
            return response()->json([
                'data' => ' CORPSE MUST BE SELECTED FROM THE SUGESTION LIST ',
                'status' => '0'
            ]);

        }
        else if(patientRegistration::duplicate('tbl_corpse_services',array('corpse_admission_id','service_number',"((timestampdiff(minute,created_at,CURRENT_TIMESTAMP) >=0))"), array($corpse_admission_id,$service_number))==true){
            return response()->json([
                'data' => $service_name.' ALREADY ASSIGNED TO THIS CORPSE',
                'status' => '0'
            ]);

        }

        $mortuary =Tbl_corpse_service::create($request->all());

          return response()->json([
                'data' => $service_name.'  SUCCEFULLY SAVED.',
                'status' => 1
            ]);


    }


    public function getMortuaryOneInfo($mortuary_id)  {

       return Tbl_mortuary::where('id',$mortuary_id)->get();

    }

public function getMortuaryServises()  {
  $sql="SELECT * FROM vw_shop_items t1 WHERE t1.dept_id=7";
       return DB::SELECT($sql);

    }
public function givePermissionToCorpse(Request $request){
	if(empty($request->descriptions)){
    return response()->json(['data' =>'PLEASE ENTER ANY REMARKS FOR THIS DISCHARGE',
                'status' => 0
                    ]);
  }
  if(empty($request->permit_number)){
    return response()->json(['data' =>'PLEASE ENTER CORPSE BURIAL PERMIT NUMBER',
                'status' => 0
                    ]);
  }
     Tbl_permit::create($request->all());

    return response()->json([	'data' =>'YOU HAVE SUCCESSFULLY ADDED REMARKS',
								'status' => 1
										]);

}



	public function checkIfPermittedDischarge(Request $request)  {

$corpse_id=$request->corpse_id;
$results=[];
$dataDetail=Tbl_invoice_line::where("corpse_id",$corpse_id)->get();
for($i=0;$i<count($dataDetail);$i++){
    if($dataDetail[$i]->status_id==1 && $dataDetail[$i]->is_payable==true && $dataDetail[$i]->payment_filter !=3){
      return  $results["paybill"]="true"; 
    }
    else{
        $results["paybill"]="false";
    }
}


           
        
  $sql="SELECT t1.*,
   CASE WHEN t1.corpse_id IS NOT NULL THEN (SELECT count(*) FROM tbl_invoice_lines t1 WHERE t1.corpse_id='".$request->corpse_id."' AND  t1.status_id=1  GROUP BY t1.corpse_id) END AS  payment_status,
  
  CASE WHEN t1.corpse_id IS NOT NULL THEN (SELECT t2.permission_status FROM tbl_permits t2 WHERE t2.corpse_id='".$request->corpse_id."'  GROUP BY t2.corpse_id) END AS  permission_status 
          FROM tbl_invoice_lines t1  WHERE t1.corpse_id='".$request->corpse_id."'";
          
          $results[]=DB::SELECT($sql); 
          
          $sql_1="SELECT t1.*,
        CASE WHEN TIMESTAMPDIFF(YEAR,dob, CURRENT_DATE) <> 0 THEN CONCAT(TIMESTAMPDIFF(YEAR,dob, CURRENT_DATE), ' Years') ELSE CASE WHEN TIMESTAMPDIFF(MONTH,dob, CURRENT_DATE) <> 0 THEN CONCAT(TIMESTAMPDIFF(MONTH, dob, CURRENT_DATE), ' Months') ELSE CONCAT(TIMESTAMPDIFF(DAY, dob, CURRENT_DATE), ' Days') END END
AS age,
(SELECT residence_name FROM tbl_residences t2 WHERE t1.residence_taker=t2.id LIMIT 1) AS residence_name_taker,

(SELECT residence_name FROM tbl_residences t2 WHERE t1.residence_found=t2.id LIMIT 1) AS residence_found_corpse,

(SELECT residence_name FROM tbl_residences t2 WHERE t1.residence_id=t2.id LIMIT 1) AS residence_brought_corpse,

(SELECT country_name FROM tbl_countries t2 WHERE t1.country_id=t2.id LIMIT 1) AS country_name,


    (SELECT relationship FROM tbl_relationships t2 WHERE t1.    relationship_taker=t2.id LIMIT 1) AS relationship_to_taker,
    
    (SELECT descriptions FROM tbl_permits t2 WHERE t1.id=t2.corpse_id LIMIT 1) AS descharge_permit,
    (SELECT permit_number FROM tbl_permits t2 WHERE t1.id=t2.corpse_id LIMIT 1) AS permit_number,
    (SELECT created_at FROM tbl_permits t2 WHERE t1.id=t2.corpse_id LIMIT 1) AS descharge_permit_time,
    
    (SELECT name FROM tbl_permits t2
INNER JOIN users t3 WHERE t1.id=t2.corpse_id AND t2.user_id=t3.id LIMIT 1) AS permited_by,

(SELECT name FROM users t3 WHERE t1.death_certifier=t3.id LIMIT 1) AS verified_doctor,
    
    (SELECT residence_name FROM tbl_residences t2 WHERE t1. funeral_site_id=t2.id LIMIT 1) AS funeral_site_to_taker
          FROM tbl_corpses t1  WHERE t1.id='".$request->corpse_id."'";
          
          $results[]=DB::SELECT($sql_1);
          
          
          
         
         if(count($results[0])>0){

          if( $results[0][0]->status_id !=1 AND $results[0][0]->permission_status==1){
         
         
         Tbl_corpse_admission::where('corpse_id',$request->corpse_id)->update(['admission_status_id'=>4]); 
         
         Tbl_corpse::where('id',$request->corpse_id)->update(['status'=>1]);
        
              
          }
         }
         else{
           Tbl_corpse::where('id',$request->corpse_id)->update(['status'=>1]);
         
         }
         
  
       return $results;

   }

	public function getServicesGiven(Request $request)  {
		$responses=[];

  $sql="SELECT t1.*,t3.item_name,t4.name AS requested_by,t1.created_at AS requested_on,(select ps.payment_status from  tbl_payment_statuses ps where ps.id= t1.status_id) AS payment_status
 ,CASE WHEN t3.id IS NOT NULL THEN (SELECT (t5.price * t1.quantity) AS cost FROM  tbl_item_prices t5 WHERE t5.item_id=t3.id AND t5.sub_category_id=t1.payment_filter GROUP BY t5.item_id) END AS cost,
 CASE WHEN t3.id IS NOT NULL THEN (SELECT SUM(t5.price * t1.quantity) AS total_cost FROM  tbl_item_prices t5 WHERE t5.item_id=t3.id AND t5.sub_category_id=t1.payment_filter GROUP BY t1.corpse_id) END AS total_cost
 ,
 CASE WHEN t1.status_id=2 THEN (SELECT SUM(t5.price * t1.quantity) AS total_cost FROM  tbl_item_prices t5 WHERE t1.status_id=2 AND t5.item_id=t3.id AND t5.sub_category_id=t1.payment_filter GROUP BY t1.corpse_id) ELSE 0 END AS total_cost_paid
 ,
 CASE WHEN t1.status_id=1  THEN (SELECT SUM(t5.price * t1.quantity) AS total_cost FROM  tbl_item_prices t5 WHERE t1.status_id=1 AND t5.item_id=t3.id AND t5.sub_category_id=t1.payment_filter GROUP BY t1.corpse_id) ELSE 0 END AS total_cost_unpaid
 
 

 FROM tbl_invoice_lines t1 
         INNER JOIN  tbl_item_type_mappeds t2 ON t1.item_type_id=t2.id
         INNER JOIN  tbl_items t3 ON t3.id=t2.item_id
         INNER JOIN  users t4 ON t4.id=t1.user_id
		 WHERE t3.dept_id=7
		     AND t1.facility_id='".$request->facility_id."' 
			 AND t1.corpse_id='".$request->corpse_id."'"
			;

    $responses[]=DB::SELECT($sql);

    $sql_1="SELECT t1.*,(SELECT residence_name FROM tbl_residences t2 WHERE t1.residence_taker=t2.id LIMIT 1) AS residence_name_taker,
	(SELECT relationship FROM tbl_relationships t2 WHERE t1.	relationship_taker=t2.id LIMIT 1) AS relationship_to_taker,
	
	(SELECT residence_name FROM tbl_residences t2 WHERE t1.	funeral_site_id=t2.id LIMIT 1) AS funeral_site_to_taker
	FROM tbl_corpses t1 
	       WHERE  t1.id='".$request->corpse_id."'";

	$responses[]=DB::SELECT($sql_1);
	return $responses;
	  }

    public function getCabinetNumber(Request $request)  {
        $mortuary_id=$request->mortuary_id;
        $mortuary=DB::table('vw_cabinets')
            ->where('mortuary_id',$mortuary_id)
            ->get();
        return $mortuary->count();

    }

    public function getCabinets(Request $request)  {
        $mortuary_id=$request->mortuary_id;
        $mortuary=DB::table('vw_cabinets')
            ->where('mortuary_id',$mortuary_id)
            ->get();
        return $mortuary;

    }

    public function getCabintesWithNoCorpses($mortuary_id)
    {
         $getCabinetsWithNoCorpses =Tbl_cabinet::where('mortuary_id',$mortuary_id)->orderBy('occupied','ASC')->get();
        return $getCabinetsWithNoCorpses;
    }

    public function getPendingCorpses($facility_id){
          $wardcorpses=DB::table('vw_wardcorpses')
            ->where('cabinet_id',NULL)
            ->where('facility_id',$facility_id)
            ->get()->take(8);
        return $wardcorpses;

    }

    public function getPendingOutsideCorpses($facility_id){
          $pendingCorpse=DB::table('vw_outsidecorpses')
            ->where('cabinet_id',NULL)
            ->where('facility_id',$facility_id)
            ->get()->take(8);
        return $pendingCorpse;

    }


    public function getPendingOutsideCorpseInfo($corpse_admission_id){
          $pendingCorpse=DB::table('vw_outsidecorpses')
            ->where('cabinet_id',NULL)
            ->where('corpse_admission_id',$corpse_admission_id)
            ->get()->take(1);
        return $pendingCorpse;

    }

    public function getApprovedCorpses($facility_id){
          $sql="SELECT t1.*,t3.name AS doctor_requested,t2.facility_name,
				
				CASE WHEN t1.corpse_id IS NOT NULL THEN (SELECT CONCAT(t4.first_name,' ' ,t4.middle_name,' ',t4.last_name) AS corpse_name FROM tbl_corpses t4 WHERE t1.corpse_id=t4.id) ELSE (SELECT CONCAT(t5.first_name,' ' ,t5.middle_name,' ',t5.last_name) AS corpse_name FROM tbl_patients t5 WHERE t1.patient_id=t5.id) END AS corpse_name,
				
				CASE WHEN t1.corpse_id IS NOT NULL THEN (SELECT t4.gender FROM tbl_corpses t4 WHERE t1.corpse_id=t4.id) ELSE (SELECT t5.gender FROM tbl_patients t5 WHERE t1.patient_id=t5.id) END AS gender,
				(SELECT permission_status FROM tbl_permits t4 WHERE t1.corpse_id=t4.corpse_id LIMIT 1) AS permission_status,
				
					
				CASE WHEN t1.corpse_id IS NOT NULL THEN (SELECT t4.corpse_record_number FROM tbl_corpses t4 WHERE t1.corpse_id=t4.id) END AS corpse_record_number,
				
				CASE WHEN t1.corpse_id IS NOT NULL THEN (SELECT t4.immediate_cause FROM tbl_corpses t4 WHERE t1.corpse_id=t4.id) END AS immediate_cause,
				
				CASE WHEN t1.corpse_id IS NOT NULL THEN (SELECT t4.underlying_cause FROM tbl_corpses t4 WHERE t1.corpse_id=t4.id) END AS underlying_cause,
				
				
				CASE WHEN t1.corpse_id IS NOT NULL AND t1.patient_id IS NULL THEN 'OUTSIDE CORPSE' ELSE 'INSIDE CORPSE' END AS corpse_category
				
				
               FROM tbl_corpse_admissions  t1
               INNER JOIN tbl_facilities t2 ON t1.facility_id=t2.id                 
               INNER JOIN users t3 ON t1.user_id=t3.id 
			   WHERE t1.facility_id='".$facility_id."' 
			      AND t1.admission_status_id=2";

			   return DB::SELECT($sql);

    }

	public function searchCorpseReports(Request $request){
		     $facility_id=$request->facility_id;
		     $start_date=DATE($request->start_date,strtotime('Y-m-d H:i:s'));
		     $end_date=DATE($request->end_date,strtotime('Y-m-d H:i:s'));
		     $responses=[];

		   $sql1="SELECT t2.*,t1.*,t3.name AS doctor_requested,CONCAT(t1.first_name,' ', t1.middle_name,' ',t1.last_name) as corpse_name,
CASE WHEN t2.corpse_id IS NOT NULL AND t2.patient_id IS NULL THEN 'OUTSIDE CORPSE' ELSE 'INSIDE CORPSE' END AS corpse_category

from tbl_corpses t1 join tbl_corpse_admissions t2 on t1.id=t2.corpse_id join users t3 on t3.id=t2.user_id left join tbl_patients t4 on t4.id=t2.patient_id
  WHERE  t2.admission_status_id=2
			      AND t2.created_at  BETWEEN  '{$start_date}' AND '{$end_date}'";
        $sql2="SELECT t2.*,t1.*,t3.name AS doctor_requested,CONCAT(t1.first_name,' ', t1.middle_name,' ',t1.last_name) as corpse_name
,
CASE WHEN t2.corpse_id IS NOT NULL AND t2.patient_id IS NULL THEN 'OUTSIDE CORPSE' ELSE 'INSIDE CORPSE' END AS corpse_category
 from tbl_corpses t1 join tbl_corpse_admissions t2 on t1.id=t2.corpse_id join users t3 on t3.id=t2.user_id left join tbl_patients t4 on t4.id=t2.patient_id
  WHERE  t2.admission_status_id=4
			      AND t2.created_at  BETWEEN  '{$start_date}' AND '{$end_date}'";
            
				  $responses[]=DB::SELECT($sql1);

				   
			   $responses[]=DB::SELECT($sql2);

			   return $responses;

    }
 public function showSearchCorpse(Request $request)
    {

        $search=$request['search'];
        return Tbl_corpse::where('corpse_record_number','like',"%$search%")
            ->Orwhere('first_name','like',"%$search%")
            ->select('id','id as corpse_id','corpse_record_number','first_name','middle_name','last_name','gender')
            ->get();
}

}