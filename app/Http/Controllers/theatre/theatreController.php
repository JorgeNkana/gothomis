<?php

namespace App\Http\Controllers\theatre;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use DB;
use App\admin\Tbl_theatre_service;
use App\theatre\Tbl_nurse_runner;
use App\Item_setups\Tbl_item;
class theatreController extends Controller
{
    //
	
public function getListTheatreQueues(Request $request){
 
		
	$sql="SELECT t1.*,t5.item_name,t4.bill_id,t2.id AS account_id,t2.patient_id,t3.dob,t3.first_name,t3.middle_name,t3.last_name,t3.gender,t3.medical_record_number,t3.mobile_number,t3.residence_id,
        CASE WHEN TIMESTAMPDIFF(YEAR,dob, CURRENT_DATE) <> 0 THEN CONCAT(TIMESTAMPDIFF(YEAR,dob, CURRENT_DATE), ' Years') ELSE CASE WHEN TIMESTAMPDIFF(MONTH,dob, CURRENT_DATE) <> 0 THEN CONCAT(TIMESTAMPDIFF(MONTH, dob, CURRENT_DATE), ' Months') ELSE CONCAT(TIMESTAMPDIFF(DAY, dob, CURRENT_DATE), ' Days') END END
AS age
	FROM  tbl_patient_procedures t1
	     INNER JOIN tbl_theatre_services t6 ON t6.item_id=t1.item_id
         INNER JOIN tbl_items t5 ON t1.item_id=t5.id         
         INNER JOIN tbl_accounts_numbers t2 ON t1.visit_date_id=t2.id
         INNER JOIN tbl_patients t3 ON t3.id=t2.patient_id
         INNER JOIN tbl_bills_categories t4 ON t2.id=t4.account_id
        
		 WHERE    t1.status IN (0,1) AND 
		  (timestampdiff(hour,t1.created_at,CURRENT_TIMESTAMP) <=24)
		 
		 
		 GROUP BY t1.patient_id,t5.id ORDER BY t1.created_at DESC LIMIT 30";
		
		return DB::SELECT($sql);
	}
	
	public function getListTheatreToMortuary(Request $request){
		
	$sql="SELECT t1.*,t5.item_name,t4.bill_id,t2.id AS account_id,t2.patient_id,t3.dob,t3.first_name,t3.middle_name,t3.last_name,t3.gender,t3.medical_record_number,t3.mobile_number,t3.residence_id,
        CASE WHEN TIMESTAMPDIFF(YEAR,dob, CURRENT_DATE) <> 0 THEN CONCAT(TIMESTAMPDIFF(YEAR,dob, CURRENT_DATE), ' Years') ELSE CASE WHEN TIMESTAMPDIFF(MONTH,dob, CURRENT_DATE) <> 0 THEN CONCAT(TIMESTAMPDIFF(MONTH, dob, CURRENT_DATE), ' Months') ELSE CONCAT(TIMESTAMPDIFF(DAY, dob, CURRENT_DATE), ' Days') END END
AS age
	FROM  tbl_patient_procedures t1
	     INNER JOIN tbl_theatre_services t6 ON t6.item_id=t1.item_id
         INNER JOIN tbl_items t5 ON t1.item_id=t5.id         
         INNER JOIN tbl_accounts_numbers t2 ON t1.visit_date_id=t2.id
         INNER JOIN tbl_patients t3 ON t3.id=t2.patient_id
         INNER JOIN tbl_bills_categories t4 ON t2.id=t4.account_id
		 WHERE  t2.facility_id='".$request->facility_id."'
		 
		 AND t1.patient_id NOT IN (SELECT patient_id FROM  tbl_corpse_admissions t7 WHERE t7.patient_id=t1.patient_id)
		 GROUP BY t1.patient_id ORDER BY t1.created_at DESC LIMIT 30";
		
		return DB::SELECT($sql);
	}
	
	
	public function getProcessWork(Request $request){
		
	$sql="SELECT t1.*,t2.patient_id,t3.dob,t3.first_name,t3.middle_name,t3.last_name,t3.gender,t3.medical_record_number,t3.mobile_number,
        CASE WHEN TIMESTAMPDIFF(YEAR,dob, CURRENT_DATE) <> 0 THEN CONCAT(TIMESTAMPDIFF(YEAR,dob, CURRENT_DATE), ' Years') ELSE CASE WHEN TIMESTAMPDIFF(MONTH,dob, CURRENT_DATE) <> 0 THEN CONCAT(TIMESTAMPDIFF(MONTH, dob, CURRENT_DATE), ' Months') ELSE CONCAT(TIMESTAMPDIFF(DAY, dob, CURRENT_DATE), ' Days') END END
AS age,
(SELECT name FROM users t4 WHERE t1.user_id=t4.id LIMIT 1) AS doctor_name

	FROM  tbl_pre_history_anethetics t1
         INNER JOIN tbl_accounts_numbers t2 ON t1.visit_date_id=t2.id
         INNER JOIN tbl_patients t3 ON t3.id=t2.patient_id
		 WHERE t1.visit_date_id='".$request->visit_date_id."'  AND t2.facility_id='".$request->facility_id."'				   
		 AND (timestampdiff(hour,t1.created_at,CURRENT_TIMESTAMP) <=24) GROUP BY t1.history_type ORDER BY t1.created_at ASC ";
		
		return DB::SELECT($sql);
	}
	
	public function assignTheatreServices(Request $request){
		$exists=Tbl_theatre_service::where('item_id',$request->item_id)->get();
		
		if(count($exists)>0){
		return response()->json(
			['data'=>$request->item_name.",already assigned",
			'status'=>0
			]
			);  	
		}
		
		$save=new Tbl_theatre_service($request->all()); 
    	if($save->save()){
		
		return response()->json(
			['data'=>$request->item_name.",Was Successfully registered",
			'status'=>1
			]
			);  
	}
		
	
	}	
	
	public function getSwabsRecords(Request $request){
		
		$sql="SELECT t1.*,t2.name,t3.item_name AS swab_type
		FROM tbl_nurse_runners t1 
		      INNER JOIN users t2 ON t2.id=t1.user_id
		      INNER JOIN tbl_items t3 ON t3.id=t1.material_id
			  WHERE t1.item_id='".$request->item_id."'
			    AND t1.visit_id='".$request->visit_date_id."'		  
			  ";
			  
	    return DB::SELECT($sql);
		
	}	
	
	public function postSwab(Request $request){
		$exists=Tbl_nurse_runner::where('item_id',$request->item_id)
		                        ->where('visit_id',$request->visit_id)->get();
		
		if(count($exists)>0){
		return response()->json(
			['data'=>"Already assigned",
			'status'=>0
			]
			);  	
		}
		
		$save=new Tbl_nurse_runner($request->all()); 
    	if($save->save()){
		
		return response()->json(
			['data'=>"Successfully registered",
			'status'=>1
			]
			);  
	}
		
	
	}
	
	public function getSwabs(Request $request){
    return Tbl_item::where('item_name','LIKE','%'.$request->swab.'%')
	               ->where('dept_id',44)->get();
		
		
	}
		
	
	
	
	public function changeProcedures(Request $request){
		
	
	$change=Tbl_theatre_service::where('item_id',$request->item_id)
	  ->update(['procedure_category'=>$request->service_type,    'service_type'=>$request->procedure_category]); 
	
    	if($change){
		return response()->json(
			['data'=>$request->item_name." Data ,Was Successfully Modified",
			'status'=>1
			]
			);  
		}
		else{
			
		return response()->json(
			['data'=>$request->item_name.",action not completed",
			'status'=>0
			]
			);  	
		}
		
	
	}
	
	public function showProcedures(Request $request){
	
	    
	if($request->procedure_category==0 AND $request->service_type==0){
		$sql="SELECT t1.* ,t3.item_id,t3.procedure_category,t3.service_type FROM tbl_items t1
		INNER JOIN tbl_item_type_mappeds t2  ON t1.id=t2.item_id 
		INNER JOIN tbl_theatre_services t3  ON t3.item_id=t2.item_id ";
		}
	else if($request->procedure_category==0 AND $request->service_type !=0){
		$sql="SELECT t1.*,t3.item_id ,t3.procedure_category,t3.service_type FROM tbl_items t1
		INNER JOIN tbl_item_type_mappeds t2  ON t1.id=t2.item_id 
		INNER JOIN tbl_theatre_services t3  ON t3.item_id=t2.item_id 
		WHERE t3.service_type=".$request->service_type;
		
			
		}
	else if($request->procedure_category!=0 AND $request->service_type ==0){
		$sql="SELECT t1.*,t3.item_id,t3.procedure_category,t3.service_type FROM tbl_items t1
		INNER JOIN tbl_item_type_mappeds t2  ON t1.id=t2.item_id 
		INNER JOIN tbl_theatre_services t3  ON t3.item_id=t2.item_id 
		WHERE t3.procedure_category=".$request->procedure_category;
		
			
		}
	else {
	$sql="SELECT t1.*,t3.item_id,t3.procedure_category,t3.service_type FROM tbl_items t1
		INNER JOIN tbl_item_type_mappeds t2  ON t1.id=t2.item_id 
		INNER JOIN tbl_theatre_services t3  ON t3.item_id=t2.item_id 
		WHERE t3.procedure_category=".$request->procedure_category."		
		AND  t3.service_type=".$request->service_type;
		
			
		}
		
		
		
		return DB::SELECT($sql);
		
		
	
	}
	public function getProcedure(Request $request){
	
		$sql="SELECT t1.* FROM tbl_items t1 INNER JOIN tbl_item_type_mappeds t2 ON  t1.id=t2.item_id where t2.item_category='procedure'
		
		AND t1.item_name LIKE '%".$request->keyWord."%'";
		
		return DB::SELECT($sql);

	}

    public function getPatientServicesInTheatre(Request $request)
    {
        $search = $request->input('search');
        $id = $request->input('facility_id');
        $category_id = $request->input('patient_category_id');
        $limit = 10;
        $sql = "select * from vw_shop_items where item_name like '%".$search."%' AND (item_category ='PROCEDURE' OR item_category ='Medication' OR item_category ='Medical Supplies' OR item_category ='SPECIALISED PROCEDURES' OR item_category ='MAJOR PROCEDURES' OR item_category='MINOR PROCEDURES' ) AND patient_category_id ='".$category_id."' AND facility_id ='".$id."' limit ".$limit;
        $patient = DB::select(DB::raw($sql));
        return $patient;
    }
	
}