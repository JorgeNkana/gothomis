<?php
namespace App\classes;
use App\ClinicalServices\Tbl_bills_category;
use App\Facility\Tbl_facility;
use App\laboratory\Tbl_order;
use App\patient\Tbl_encounter_invoice;
use App\patient\Tbl_invoice_line;
use App\RCH\Tbl_anti_natal_register;
use App\RCH\Tbl_child_register;
use App\RCH\Tbl_family_planning_register;
use App\RCH\Tbl_labour_register;
use App\RCH\Tbl_post_natal_register;
use App\Patient\Tbl_patient;
use App\admin\Tbl_integrating_key;
use App\Patient\Tbl_next_of_kin;
use App\Patient\Tbl_accounts_number;
use App\Patient\Tbl_exemption_number;
use App\Patient\Tbl_corpse;
use App\Payment_types\Tbl_pay_cat_sub_category;
use App\Payment_types\Tbl_payments_category;
use App\Http\Controllers\reports\ReportGenerators;
use DateTime;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;

class patientRegistration
{

    public static function  countReattendance($gender, $dob,$facility_code){
       $gender = strtoupper($gender);
	   $dob = new \DateTime($dob);
       $interval = (new \DateTime())->diff($dob);
       $age_group = $interval->m + $interval->y*12;
     
       if($age_group <= 0){
           $age_group = $gender == "MALE" ? "male_under_one_month" : "female_under_one_month";
           $total_group = "total_under_one_month";
       }
       elseif($age_group <= 11){
           $age_group = $gender == "MALE" ? "male_under_one_year" : "female_under_one_year";
           $total_group = "total_under_one_year";
       }elseif($age_group <= 59){
           $age_group = $gender == "MALE" ? "male_under_five_year" : "female_under_five_year";
           $total_group = "total_under_five_year";
       }elseif($age_group <= 719){
           $age_group = $gender == "MALE" ? "male_above_five_under_sixty" : "female_above_five_under_sixty";
           $total_group = "total_above_five_under_sixty";
       }elseif($age_group >= 720){
           $age_group = $gender == "MALE" ? "male_above_sixty" : "female_above_sixty";
           $total_group = "total_above_sixty";
       }

       $todayCounts = DB::select("select count(*) count from tbl_reatend_patient_reports where facility_code='".$facility_code."'  AND date=CURRENT_DATE");
       if(is_array($todayCounts)  && $todayCounts[0]->count > 0){
           $sql="update tbl_reatend_patient_reports set `$age_group` = `$age_group`+1,
               `$total_group`=`$total_group`+1,".($gender == "MALE" ? "total_male = total_male+1" : "total_female=total_female+1").",grand_total=grand_total+1 where date = CURRENT_DATE and facility_code='".$facility_code."'";
                 DB::statement($sql);
       }else{
           DB::statement("INSERT INTO tbl_reatend_patient_reports(facility_code,$age_group,$total_group,".($gender == "MALE" ? "total_male" : "total_female").",grand_total,date,created_at,updated_at) 
               select '".$facility_code."',1,1,1,1, CURRENT_DATE,CURRENT_TIMESTAMP,CURRENT_TIMESTAMP");
       }
   }

     //this function also srve purposes for re-attendance
   public static function countNewAttendance($gender, $dob,$facility_code){
       $gender = strtoupper($gender);
	   $dob = new \DateTime($dob);
       $interval = (new \DateTime())->diff($dob);
       $age_group = $interval->m + $interval->y*12;
     
       if($age_group <= 0){
           $age_group = $gender == "MALE" ? "male_under_one_month" : "female_under_one_month";
           $total_group = "total_under_one_month";
       }
       elseif($age_group <= 11){
           $age_group = $gender == "MALE" ? "male_under_one_year" : "female_under_one_year";
           $total_group = "total_under_one_year";
       }elseif($age_group <= 59){
           $age_group = $gender == "MALE" ? "male_under_five_year" : "female_under_five_year";
           $total_group = "total_under_five_year";
       }elseif($age_group <= 719){
           $age_group = $gender == "MALE" ? "male_above_five_under_sixty" : "female_above_five_under_sixty";
           $total_group = "total_above_five_under_sixty";
       }elseif($age_group >= 720){
           $age_group = $gender == "MALE" ? "male_above_sixty" : "female_above_sixty";
           $total_group = "total_above_sixty";
       }

       $todayCounts = DB::select("select count(*) count from tbl_patient_registration_reports where facility_code='".$facility_code."'  AND date=CURRENT_DATE");
       if(is_array($todayCounts)  && $todayCounts[0]->count > 0){
           $sql="update tbl_patient_registration_reports set `$age_group` = `$age_group`+1,
               `$total_group`=`$total_group`+1,".($gender == "MALE" ? "total_male = total_male+1" : "total_female=total_female+1").",grand_total=grand_total+1 where date = CURRENT_DATE and facility_code='".$facility_code."'";
                 DB::statement($sql);
       }else{
           DB::statement("INSERT INTO tbl_patient_registration_reports(facility_code,$age_group,$total_group,".($gender == "MALE" ? "total_male" : "total_female").",grand_total,date,created_at,updated_at) select '".$facility_code."',1,1,1,1, CURRENT_DATE,CURRENT_TIMESTAMP,CURRENT_TIMESTAMP");
       }
   }

      	public static function seachForPatients($request){
		$searchKey = $request->input('searchKey');
        $patientSearched=DB::table('vw_patients_search')
		->where('fullname','like','%'.$searchKey.'%')
		->orWhere('medical_record_number','like','%'.$searchKey.'%')
		->orWhere('account_number','like','%'.$searchKey.'%')
		->orwhere('mobile_number','like','%'.$searchKey.'%')
		->groupBy('patient_id')
		 ->get()
		 ->take(15);
		 return $patientSearched;
       	}

       	public static function seachForInsuarancePatients($request){
		$searchKey = $request->input('searchKey');
        $patientSearched=DB::table('vw_patients_search')
		->where('membership_number','like','%'.$searchKey.'%')
		->groupBy('patient_id')
		 ->get();
		 return $patientSearched;
       	}
		
		
		public static function getMaritalStatus($request){
			
		$searchKey = $request->input('searchKey');
        $getMaritalStatus=DB::table('tbl_maritals')->get();		 
		 return $getMaritalStatus;
       	}
		
		
		public static function getTribe($request){
		$searchKey = $request->input('searchKey');
        $getTribe=DB::table('tbl_tribes')
		->where('tribe_name','like','%'.$searchKey.'%')
		->get()->take(5);		 
		 return $getTribe;
       	}
		
		public static function getOccupation($request){
		$searchKey = $request->input('searchKey');
        $getTribe=DB::table('tbl_occupations')
		->where('occupation_name','like','%'.$searchKey.'%')
		->get();		 
		 return $getTribe;
       	}
		
		public static function getCountry($request){
		$searchKey = $request->input('searchKey');
        $getCountry=DB::table('tbl_countries')
		->where('country_name','like','%'.$searchKey.'%')
		->get();		 
		return $getCountry;
       	}
		
		public static function getRelationships(){
		 $getRelationships=DB::table('tbl_relationships')->get();		 
		return $getRelationships;
       	}
	
	public static function calculateDaysInterval($last_visit){
		$today_date_time=date('Y-m-d h:i:s');
		$bday = new DateTime($last_visit);
        $today = new DateTime($today_date_time); 
        $diff = $today->diff($bday);
		return $diff->y.' Years '.$diff->m.' Month '.$diff->d.' Days ago' ;
       	}
	
	
	
	
	public static function calculatePatientAge($request){
		$dob=$request->dob;
		$today_date_time=date('Y-m-d h:i:s');
		$bday = new DateTime($dob);
        $today = new DateTime($today_date_time); 
        $diff = $today->diff($bday);
		return $diff->y.' Years '.$diff->m.' Month '.$diff->d.' Days' ;
       	}
		
		
		public static function duplicate($table,$fields, $values, $updating=false,  $updatingKey=0, $primaryKey='id'){
			$query = "select count(*) as count from $table where ";
			for($field = 0; $field < count($fields); $field++)
				$query .= $fields[$field] .((strpos($fields[$field], "))") > 0) ? "" : "= '".$values[$field]."'"). (($field+1) < count($fields) ? " and " : "");
		
			if($updating)
				$query .= " and $primaryKey <> '$updatingKey'";
			try{
				$result = DB::select($query);
				if($result[0]->count !=0){
					$GLOBALS['data'] = array('message'=>array('type'=>'warning','simple'=>'Attempt to add a duplicate value','real'=>null), 'data'=>null);
					return true;
				}
			return false;
			}catch(QueryException $exception){
				$GLOBALS['data'] = array('message'=>array('type'=>'error','simple'=>'An error occured while checking the new value','real'=>$exception->getMessage()), 'data'=>null);
				return true;//cant check. return true to prevent blind insert
			}

		}
		
		
		
		public static function calculateTimeDifference($created_at){
			$to_time = strtotime(date('Y-m-d h:i:s'));
            $from_time = strtotime($created_at);
            return round(abs($to_time - $from_time) /(60* 60),2);		
       	}



//    EMERGENCE CLASSES

    public static function seachForPatientsEm($request)
    {
        $searchKey = $request->input('searchKey');
        $patientSearched=DB::table('vw_patients_search')
            ->where('fullname','like','%'.$searchKey.'%')
            ->orWhere('medical_record_number','like','%'.$searchKey.'%')
            ->orWhere('account_number','like','%'.$searchKey.'%')
            ->orwhere('mobile_number','like','%'.$searchKey.'%')
            ->groupBy('patient_id')
            ->get();

        return $patientSearched;
    }

    public static function searchForEm($request)
    {
        $searchKey = $request->input('searchKey');
        $patientSearched=DB::table('vw_opd_patients')
            ->where('status',1)
            ->where('payment_status_id',2)
            ->where('first_name','like','%'.$searchKey.'%')
            ->orWhere('medical_record_number','like','%'.$searchKey.'%')
            ->orWhere('account_number','like','%'.$searchKey.'%')
            ->orwhere('middle_name','like','%'.$searchKey.'%')
            ->orwhere('last_name','like','%'.$searchKey.'%')
            ->groupBy('patient_id')
            ->get();

        return $patientSearched;
    }

    public static function searchFoCasualty($request){
        $searchKey = $request->input('searchKey');
        $patientSearched=DB::table('vw_opd_patients')
            ->where('status',2)
            ->where('payment_status_id',2)
            ->where('first_name','like','%'.$searchKey.'%')
            ->orWhere('medical_record_number','like','%'.$searchKey.'%')
            ->orWhere('account_number','like','%'.$searchKey.'%')
            ->orwhere('middle_name','like','%'.$searchKey.'%')
            ->orwhere('last_name','like','%'.$searchKey.'%')
            ->groupBy('patient_id')
            ->get();

        return $patientSearched;
    }






    public static function emergencyAccountNumber($facility_id,$patient_id, $user_id, $gender, $dob)
    {
        $tallied = Tbl_accounts_number::where('patient_id',$patient_id)->where(DB::Raw("YEAR(date_attended)"), date('Y'))->where('tallied',1)->count();
		$patient = Tbl_accounts_number::create(array(
				'user_id'=>$user_id,
				'account_number'=>'',
			 	'tallied'=>($tallied > 0 ? 1 : NULL),
				'facility_id'=>$facility_id,
				'patient_id'=>$patient_id,
				'date_attended'=>date('Y-m-d')));
				
		if($patient->save()){
			if($tallied != 0 && $gender)
				ReportGenerators::countReattendance(new Request(["facility_id"=>$facility_id, "gender"=>$gender, "dob"=>$dob, "clinic_id"=>1]));
			else
				Tbl_accounts_number::where('patient_id',$patient_id)->where(DB::Raw("YEAR(date_attended)"), date("Y"))->update(["tallied"=>NULL]);return $patient->id;
		}
    }



    public static function emergency_registration($request)
    {
        $responses=[];

        $id=$request->input('facility_id');
        $sql=Tbl_facility::where('id',$id)->first();
        $facility_id = preg_replace("/[_-]/","",$sql->facility_code);
        $first_name=$request->input('first_name');
        $middle_name=$request->input('middle_name');
        $last_name=$request->input('last_name');
        $gender=$request->input('gender');
        $mobile_number=$request->input('mobile_number');
        $residence_id=$request->input('residence_id');
        $dob=$request->input('dob');
        $marital_status=$request->input('marital_status');
        $occupation=$request->input('occupation_id');
        $tribe_id=$request->input('tribe');
        $country_id=$request->input('country_id');
        $user_id=$request->input('user_id');
        $next_of_kin_name=$request->input('next_of_kin_name');
        $next_of_kin_resedence_id=$request->input('next_of_kin_resedence_id');
        $relationship=$request->input('relationship');
        $mobile_number_next_kin=$request->input('mobile_number_next_kin');
        while(true){
            $patientnumber = DB::table('tbl_patients')
                ->join('tbl_facilities', 'tbl_patients.facility_id', '=', 'tbl_facilities.id')
                ->where('tbl_facilities.id',$id)
                ->orderBy('tbl_patients.id','DESC')
                ->take(1)->get();

            //$facility ='10001';
            if(count($patientnumber)==1){
                $CustomerExecute =  $patientnumber[0]->medical_record_number;
                $TempArray = explode('/',$CustomerExecute);
                $temp_array1 = $TempArray[0];
                if( $TempArray[1]== date('Y') )
                {
                    $TempArray_value = explode('-',$temp_array1);
                    $num1 = $TempArray_value[1];
                    $num2 = $TempArray_value[2];
                    $num3 = $TempArray_value[3];
                    if($num3 < 99) {  $num3 = $num3+1;   if(strlen($num3)==1) $num3 = '0'.$num3;  $account_number = $num1.'-'.$num2. '-'. $num3;  }
                    else if( $num3 ==99 && $num2 < 99 ) { $num3 = '00'; $num2 = $num2+1; if(strlen($num2)==1) $num2 = '0'.$num2;    $account_number = $num1.'-'.$num2. '-'. $num3;  }
                    else if( $num3 ==99 && $num2 == 99 && $num1 < 99) { $num3 = '00'; $num2 = '00';  $num1 = $num1+1; if(strlen($num1)==1) $num1 = '0'.$num1;   $account_number = $num1.'-'.$num2. '-'. $num3;  }
                    else { $num3 = '00'; $num2 = '00';  $num1 = $num1+1; if(strlen($num1)==1) $num1 = '0'.$num1;   $account_number = $num1.'-'.$num2. '-'. $num3;  }
                    $account_number  = $facility_id.'-'.$account_number.'/'.$TempArray[1];
                }else{
                    $account_number  = $facility_id.'-00-00-01/'.date('Y');}
            }else{
                $account_number  = $facility_id.'-00-00-01/'.date('Y');
            }
            $ExecuteQuery = Tbl_patient::select('medical_record_number')->where('medical_record_number','=',$account_number)->count();
            if($ExecuteQuery ==0){
                $patient =Tbl_patient::create(array('first_name'=>$first_name,'middle_name'=>$middle_name,'last_name'=>$last_name,'dob'=>$dob,'gender'=>$gender,
                    'medical_record_number'=>$account_number,'mobile_number'=>$mobile_number,'residence_id'=>$residence_id,'marital_id'=>$marital_status,'occupation_id'=>$occupation,
                    'tribe_id'=>$tribe_id,'country_id'=>$country_id,'facility_id'=>$id,'user_id'=>$user_id));

                //$patient = new Tbl_patient($request->all());
                $patient['medical_record_number'] = $account_number;
                if(!$patient->save())
                    continue;
                $facility_id=$patient->facility_id;
                $patient_id =$patient->id;
                $user_id    =$patient->user_id;
                if(!empty($next_of_kin_name)){
                    $next_kin =Tbl_next_of_kin::create(array('patient_id'=>$patient_id,'next_of_kin_name'=>$next_of_kin_name,'mobile_number'=>$mobile_number_next_kin,'residence_id'=>$next_of_kin_resedence_id,'relationship'=>$relationship));
                    $next_kin->save();
                }
                self::emergencyAccountNumber($facility_id,$patient_id,$user_id,$gender, $dob);
                $responses[]=$patient;
                $responses[]=Tbl_accounts_number::Where('patient_id',$patient_id)->orderBy('id','DESC')->take(1)->get();
                $responses[]=DB::table('vw_residences')->where('residence_id',$residence_id)->get();
                $responses[]=self::getLastVisit($facility_id,$patient_id);

                return $responses;
            }else{
                continue;
            }

        }
    }
	
	
	
	public static function labRequestAPI()
    {

	
	    $intergratingKeys=Tbl_integrating_key::where('api_type',9)->where('active',1)->get();
		 $base_urls=$intergratingKeys[0]->base_urls;
		 $private_keys=$intergratingKeys[0]->private_keys;
		 $public_keys=$intergratingKeys[0]->public_keys;
		 $active=$intergratingKeys[0]->active;
		 $record_returned= count($intergratingKeys);

		if($record_returned > 0){
        $ch = curl_init($base_urls);
        $request_method = 'GET';
        $nonce=uniqid("",true);
        // calculate the hash
        $timestamp=strval(time());
        $data_hash='';
        $signature_raw_data=$public_keys.$request_method.$timestamp.$nonce.$data_hash;
        $hash = hash_hmac ('sha256', $signature_raw_data, $private_keys,$raw=true);
        $signature = base64_encode($hash);
        $amx=$public_keys.':'.$signature.':'.$nonce.':'.$timestamp;
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array('Authorization: amx '.$amx));
        $result = curl_exec($ch);
        $StatusCode=  curl_getinfo($ch,CURLINFO_HTTP_CODE);
         if($StatusCode == 200){
            $array_data = json_decode($result,true);
            $array_data['StatusCode'] = $StatusCode;
            $result = json_encode($array_data);
        }else{
            $array_data = array();
            $array_data['StatusCode'] = $StatusCode;
            $array_data['Message'] = $result;
            $result = json_encode($array_data);
        }

        curl_close($ch);


        return $result;

		 }



    }
	
	
	
	

//@END EMERGENCE CLASSES

	public static function EMRintegrationAPI($dataToEMR,$account_number_id,$patient_id,$bill_id,$user_id,$dept_id){
		
		 $intergratingKeys=Tbl_integrating_key::where('api_type',9)->where('active',1)->get();
		 $base_urls=$intergratingKeys[0]->base_urls;
		 $private_keys=$intergratingKeys[0]->private_keys;
		 $public_keys=$intergratingKeys[0]->public_keys;
		 $active=$intergratingKeys[0]->active;
		 $record_returned= count($intergratingKeys);
		 
		$sql="SELECT * FROM tbl_next_of_kins t1 WHERE t1.patient_id='".$patient_id."'";	
	    $nextOfKins=DB::SELECT($sql);
		if(count($nextOfKins) >0 ){
		$next_of_kin_name=$nextOfKins[0]->next_of_kin_name;
	    $next_of_kin_resedence_id=$nextOfKins[0]->residence_id;
	    $relationship=$nextOfKins[0]->relationship;
	    $mobile_number_next_kin=$nextOfKins[0]->mobile_number;
			
		}
		
        $first_name=$dataToEMR[0]->first_name;
	    $middle_name=$dataToEMR[0]->middle_name;
	    $last_name=$dataToEMR[0]->last_name;
	    $gender=$dataToEMR[0]->gender;
	    $mobile_number=$dataToEMR[0]->mobile_number;
	    $residence_id=$dataToEMR[0]->residence_id;
	    $dob=$dataToEMR[0]->dob;
	    $marital_status=$dataToEMR[0]->marital_id;
	    $occupation=$dataToEMR[0]->occupation_id;
	    $tribe_id=$dataToEMR[0]->tribe_id;
	    $country_id=$dataToEMR[0]->country_id;
	    $user_id=$dataToEMR[0]->user_id;
	  
	    $account_number=$dataToEMR[0]->medical_record_number;

  
        $foliolist_array=array();		         
        $patient_infos=array();
        $names=array();
        $visitTypes=array();
        $telecoms=array();
        $address=array();
        $identifications=array();
        $nextOFkin =array();
		$patient_infos['contact']=array();
		$patient_infos['telecom']=array();              
		$patient_infos['addresses']=array();   
        $entity_array =array();
		        $patient_infos['identifier']=array();
               
                $entity_array["PatientResources"]=array();			
                $patient_infos["resourceType"]="Patient";			
                
                $identifications['identifierSourceUuid']=$patient_id;
                $identifications['value']=$account_number;
				
				array_push($patient_infos['identifier'],$identifications);      
				
	
                $patient_infos['name']=array();
				
                $patient_infos['visits']=array();
				
                $names["use"]="usual";
                $names["family"]="";
                $names["firstName"]=$first_name;
                $names['middleName']=$middle_name;
                $names['lastName']=$last_name;
                array_push($patient_infos['name'],$names);  
				
				$telecoms["system"]="phone";
                $telecoms["value"]=$mobile_number;
                $telecoms["use"]="work";				
               array_push($patient_infos['telecom'],$telecoms);
			   
			    $visitTypes["visitID"]=$account_number_id;
			    $visitTypes["paymentsCategories"]=$bill_id;
			    $visitTypes["senderID"]=$user_id;
			    $visitTypes["clinicID"]=$dept_id;
                $visitTypes["dateAttended"]=date('Y-m-d');				
               array_push($patient_infos['visits'],$visitTypes);
				
				$patient_infos["gender"]=$gender;			
                $patient_infos["birthDate"]=$dob;			
                $patient_infos["deceasedBoolean"]=false;
                $patient_infos["maritialStatus"]=$marital_status;			
               				
                  
               							  
                
				$address["use"]="home";              
				$address["street"]=$residence_id;
                array_push($patient_infos['addresses'],$address);
           if(count($nextOfKins) >0 ){
                $nextOFkin['relationship']=$relationship;
                $nextOFkin['name']=$next_of_kin_name;
                $nextOFkin['mobile']=$mobile_number_next_kin;
                $nextOFkin['address']=$next_of_kin_resedence_id;
                array_push($patient_infos['contact'],$nextOFkin);
		   }

              $patient_infos["active"]=true;			
                
            array_push($foliolist_array,$patient_infos);


        
        $entity_array["PatientResources"]=$foliolist_array;
        $data_string=json_encode($entity_array,JSON_PRETTY_PRINT);
	  
	  if($record_returned >0){
		 
        $request_method = 'POST';				
		$ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $base_urls);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array('content-type: application/json'));
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS,$data_string);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
        $response  = curl_exec($ch);
		$StatusCode=  curl_getinfo($ch,CURLINFO_HTTP_CODE);

        curl_close($ch);
        return $response;
	  }
		
       
		
		
	} 
	
    public static function patient_registration($request)
    {
		$responses=[];
     
	    $id=$request->input('facility_id');
        $sql=Tbl_facility::where('id',$id)->first();
        $facility_id = preg_replace("/[_-]/","",$sql->facility_code);
	    $first_name=$request->input('first_name');
	    $middle_name=$request->input('middle_name');
	    $last_name=$request->input('last_name');
	    $gender=strtoupper($request->input('gender')); 
	    $mobile_number=$request->input('mobile_number');
	    $residence_id=$request->input('residence_id');
	    $dob=$request->input('dob');
	    $marital_status=$request->input('marital_status');
	    $occupation=$request->input('occupation_id');
	    $tribe_id=$request->input('tribe');
	    $country_id=$request->input('country_id');
	    $user_id=$request->input('user_id');
	    $next_of_kin_name=$request->input('next_of_kin_name');
	    $next_of_kin_resedence_id=$request->input('next_of_kin_resedence_id');
	    $relationship=$request->input('relationship');
	    $mobile_number_next_kin=$request->input('mobile_number_next_kin');
       while(true){
		$patientnumber = DB::table('tbl_patients')
			->join('tbl_facilities', 'tbl_patients.facility_id', '=', 'tbl_facilities.id')
			->where('tbl_facilities.id',$id)
			->orderBy('tbl_patients.id','DESC')
			->take(1)->get();

           // return $patientnumber;

            //$facility ='10001';
        if(count($patientnumber)==1){
        $CustomerExecute =  $patientnumber[0]->medical_record_number;
        $TempArray = explode('/',$CustomerExecute);
        $temp_array1 = $TempArray[0];
        if( $TempArray[1]== date('Y') )
        {
        $TempArray_value = explode('-',$temp_array1);
        $num1 = $TempArray_value[1];
        $num2 = $TempArray_value[2];
        $num3 = $TempArray_value[3];
        if($num3 < 99) {  $num3 = $num3+1;   if(strlen($num3)==1) $num3 = '0'.$num3;  $account_number = $num1.'-'.$num2. '-'. $num3;  }
        else if( $num3 ==99 && $num2 < 99 ) { $num3 = '00'; $num2 = $num2+1; if(strlen($num2)==1) $num2 = '0'.$num2;    $account_number = $num1.'-'.$num2. '-'. $num3;  }
        else if( $num3 ==99 && $num2 == 99 && $num1 < 99) { $num3 = '00'; $num2 = '00';  $num1 = $num1+1; if(strlen($num1)==1) $num1 = '0'.$num1;   $account_number = $num1.'-'.$num2. '-'. $num3;  }
        else { $num3 = '00'; $num2 = '00';  $num1 = $num1+1; if(strlen($num1)==1) $num1 = '0'.$num1;   $account_number = $num1.'-'.$num2. '-'. $num3;  }
        $account_number  = $facility_id.'-'.$account_number.'/'.$TempArray[1];
        }else{
        $account_number  = $facility_id.'-00-00-01/'.date('Y');}
        }else{
        $account_number  = $facility_id.'-00-00-01/'.date('Y');
        }
        $ExecuteQuery = Tbl_patient::select('medical_record_number')->where('medical_record_number',$account_number)->count();
        if($ExecuteQuery ==0){
              
	 $patient =Tbl_patient::create(array('first_name'=>$first_name,'middle_name'=>$middle_name,'last_name'=>$last_name,'dob'=>$dob,'gender'=>$gender,
	 'medical_record_number'=>$account_number,'mobile_number'=>$mobile_number,'residence_id'=>$residence_id,'marital_id'=>$marital_status,'occupation_id'=>$occupation,
     'tribe_id'=>$tribe_id,'country_id'=>$country_id,'facility_id'=>$id,'user_id'=>$user_id));
     
     
			
        //$patient = new Tbl_patient($request->all());
        $patient['medical_record_number'] = $account_number;
        if(!$patient->save())
        continue;
        $facility_id=$patient->facility_id;
        $patient_id =$patient->id;
        $user_id    =$patient->user_id;
		if(!empty($next_of_kin_name)){
		$next_kin =Tbl_next_of_kin::create(array('patient_id'=>$patient_id,'next_of_kin_name'=>$next_of_kin_name,'mobile_number'=>$mobile_number_next_kin,'residence_id'=>$next_of_kin_resedence_id,'relationship'=>$relationship));
		$next_kin->save();
		}
		
		//Melchiory: this is causing multiple accNos in one visit as the encounter created another one!!!
        //self::patientAccountNumber($facility_id,$patient_id,$user_id,$gender, $dob);
       
		 
		 
		 $sql="SELECT t1.*,CONCAT(t1.first_name,' ',t1.middle_name,' ',t1.last_name) AS fullname,t2.residence_name,'' AS council_name,
		   CASE WHEN t1.occupation_id IS NOT NULL THEN (SELECT occupation_name FROM tbl_occupations t4 WHERE t4.id=t1.occupation_id) END AS occupation_name
		   
		   
		   FROM tbl_patients t1
		         INNER JOIN tbl_residences t2 ON t1.residence_id=t2.id
				 WHERE t1.id='".$patient_id."'";
			$patient_list=DB::SELECT($sql);
		
            $responses[]=$patient_list;
			$responses[]=Tbl_accounts_number::Where('patient_id',"'".$patient_id."'")->orderBy('id','DESC')->take(1)->get();
            $responses[]=DB::table('vw_residences')->where('residence_id',$residence_id)->get();
            $responses[]=self::getLastVisit($facility_id,$patient_id);
			
	    
            return $responses;
        }else{
                continue;
            }

				}
    }


    public static function getCurrentPatientAccountNumber($patient_id,$facility_id)
    { 
	     $getCurrentPatientAccountNumber = Tbl_accounts_number::
        select('id as account_number_id','account_number')
            ->where('patient_id',$patient_id)
            ->where('facility_id',$facility_id)
            ->orderBy('created_at','DESC')
            ->first();
        return $getCurrentPatientAccountNumber;
    }
	
	public static function GUID()
    {
        if (function_exists('com_create_guid') === true)
        {
            return trim(com_create_guid(), '{}');
        }

        return sprintf('%04X%04X-%04X-%04X-%04X-%04X%04X%04X', mt_rand(0, 65535), mt_rand(0, 65535), mt_rand(0, 65535), mt_rand(16384, 20479), mt_rand(32768, 49151), mt_rand(0, 65535), mt_rand(0, 65535), mt_rand(0, 65535));
    }
//NHIF API TO SHARE DATA
    public static function getInsurancePerPatient($request)
    {   $folioID=strtolower(self::GUID());
        $visit_id=$request->visit_id;
        $facility_id=$request->facility_id;
        $foliolist_array=array();

        $patient_infos=array();
        $diseases=array();
        $items_array =array();
        //$entity_array =array();
        $entity_array["entities"]=array();

        $sqlp ="select * from vw_nhif_patients WHERE visit_id = '".$visit_id."'  AND facility_id= '".$facility_id."'  ";
        $patient=DB::select(DB::raw($sqlp)); //patient particulars

        $sql ="SELECT * FROM vw_prev_diagnosis WHERE visit_date_id = '".$visit_id."'  AND facility_id= '".$facility_id."'  ";
        $diseases_diagnosis=DB::select(DB::raw($sql));

        $sql_2 ="SELECT * FROM vw_folio_items WHERE visit_id = '".$visit_id."'  AND facility_id= '".$facility_id."'";

        $items=DB::select(DB::raw($sql_2)); //ALL ITEMS GIVEN


        foreach($patient as $row) {
            $patient_infos["FolioID"]=$folioID;
            $patient_infos['CardNo']=$row->card_no;
            $patient_infos['AuthorizationNo']=$row->authorization_number;
            $patient_infos['ClaimYear']=$row->ClaimYear;
            $patient_infos['ClaimMonth']=$row->ClaimMonth;
            $patient_infos['FolioNo']=$row->FolioNo;
            $patient_infos['FacilityCode']=$row->facility_code;
            $patient_infos['PatientFileNo']=$row->medical_record_number;
            $patient_infos['FirstName']=$row->first_name;
            $patient_infos['LastName']=$row->last_name;
            $patient_infos['Gender']=$row->gender;
            $patient_infos['PatientTypeCode']='OUT';
            $patient_infos['SerialNo']=null;
            $patient_infos['FolioDisaeses']=array();
            $patient_infos['FolioItems']=array();

            foreach($diseases_diagnosis as $disease) {
                $diseases["FolioDiseaseID"]=strtolower(self::GUID());
                $diseases['DiseaseCode']=$disease->DiseaseCode;
                $diseases["FolioID"]=$folioID;
                $diseases['Status']=$disease->status;
                array_push($patient_infos['FolioDisaeses'],$diseases);


            }

            foreach($items as $item) {
                $items_array["FolioItemID"]=strtolower(self::GUID());
                $items_array["FolioID"]=$folioID;
                $items_array['ItemCode']=$item->item_code;
                $items_array['ItemQuantity']=$item->ItemQuantity;
                $items_array['AmountClaimed']=$item->AmountClaimed;
                $items_array['OtherDetails']=null;
                $items_array['ApprovalRefNo']=null;
                $items_array['CreatedBy']=$item->createdBy;
                $items_array['DateCreated']=$item->date_attended;

                array_push($patient_infos['FolioItems'],$items_array);


            }
            array_push($foliolist_array,$patient_infos);


        }
        $entity_array["entities"]=$foliolist_array;
        //array_push($entity_array["entities"],$foliolist_array);
        $jsonData=json_encode($entity_array,JSON_PRETTY_PRINT);
        return $jsonData;
    }





	//Generate FolioNo Number for NHIF CLAIM
public static function nhifFolioNo($facility_id){
   $constant=1;
        $sql="SELECT YEAR(date_attended) as ClaimYear,MONTH(date_attended) as ClaimMonth,account_number FROM `tbl_accounts_numbers` WHERE facility_id='{$facility_id}' AND YEAR(date_attended)='".date('Y')."' AND  MONTH(date_attended)='".date('m')."'  ORDER BY id DESC LIMIT 1";


        $nhifFolioNo = DB::SELECT($sql);

           if(count($nhifFolioNo)>0) {
               $FolioNo = $nhifFolioNo[0]->account_number;
               $CurrentMonth = $nhifFolioNo[0]->ClaimMonth;
               $CurrentYear = $nhifFolioNo[0]->ClaimYear;
               if (($CurrentMonth != date('m')) AND ($CurrentYear != date('Y'))) {
                   $FolioNo = 1;
               } else {
                   $FolioNo = $FolioNo + 1;
               }
           }
           else{
               $FolioNo =1;
           }



     return $FolioNo;

	}	
	

public static function enterEnvoiceBima($request,$account_number_id,$patient_id,$patient_main_category_id){
        $membership_number=$request->input('membership_number');
	    $authorization_number=$request->input('authorization_number');
	    $user_id=$request->input('user_id');
	    $card_no=$request->input('card_no');
    // some validation may be required..
    $facility_id=$request->input('facility_id');
    //$patient_id=$request->input('patient_id');
    //$price_id=$request->input('price_id');
   // $service_id=$request->input('service_id');
    //$item_type_id=$request->input('item_type_id');
    $user_id=$request->input('user_id');

    $item_type_id=$request->input('item_type_id');
    $price_id=$request->input('price_id');
    $patientservices=$request->input('patientservices');
    $patient_category=$request->input('patient_category');
    $scheme_id=$request->input('scheme_id');

    $quantity=1;
    $status_id=1;
    $payment_filter=$request->input('payment_filter');

    if($request->input('main_category_id')!=1)
    {
        $status_id=1;
        $payment_filter=$request->input('payment_filter');
    }
    if(self::duplicate('tbl_patients',array('id',"((timestampdiff(minute,created_at,CURRENT_TIMESTAMP) >= 60))"), array($patient_id))==true) {
        self::patientAccountNumberInsuarance($facility_id,$patient_id,$membership_number,$user_id,$authorization_number,$card_no);
    }
    $bill_id=$request->input('patient_category');
    $main_category_id=$request->input('main_category_id');
    //return $getLastVisit[0]->created_at;
    if (!is_numeric($patientservices)) {

        return response()->json([
            'data' => 'PLEASE SELECT SERVICE FROM THE SUGESTION LIST',
            'status' => '0'
        ]);
    }

    else{

        if(patientRegistration::duplicate('tbl_invoice_lines',array('patient_id','item_type_id','quantity',"((timestampdiff(minute,created_at,CURRENT_TIMESTAMP) <=5))"), array($patient_id,$item_type_id,$quantity,''))==true){

            return response()->json([
                'data' => 'DUPLICATE WAS DETECTED, PLEASE DONT RE-SUBMIT FORM TWICE FOR THE SAME REQUEST',
                'status' => '0'
            ]);
        }

        else{
            $payment_category =Tbl_bills_category::create(['patient_id'=>$patient_id,'account_id'=>$account_number_id,'user_id'=>$user_id,'bill_id'=>$bill_id,'main_category_id'=>$patient_main_category_id]);

            $encounter =Tbl_encounter_invoice::create(array('account_number_id'=>$account_number_id,'facility_id'=>$facility_id,'user_id'=>$user_id));


                $invoice_line =Tbl_invoice_line::create(array('invoice_id'=>$encounter->id,'payment_filter'=>$payment_filter,
                    'item_type_id'=>$item_type_id,'facility_id'=>$facility_id,'quantity'=>$quantity,'user_id'=>$user_id,'item_price_id'=>$price_id,'status_id'=>$status_id,'discount'=>0,'discount_by'=>$user_id,'patient_id'=>$patient_id));


        }
    }




}



     public static function patient_registration_insuarance($request)
    {
		$responses=[];
	    $item_type_id=$request->input('item_type_id');
	    $price_id=$request->input('price_id');
	    $patientservices=$request->input('patientservices');
	    $patient_category=$request->input('patient_category');
	    $patient_main_category_id=$request->input('patient_main_category_id');
	     $facility_id=$request->input('facility_id');
	    $first_name=$request->input('first_name');
	    $middle_name=$request->input('middle_name');
	    $last_name=$request->input('last_name');
	    $gender=strtoupper($request->input('gender'));
	    $mobile_number=$request->input('mobile_number');
	    $residence_id=$request->input('residence_id');
	    $dob=$request->input('dob');
	    $marital_status=$request->input('marital_status');
	    $occupation=$request->input('occupation');
	    $tribe_id=$request->input('tribe');
	    $country_id=$request->input('country_id');
	    $membership_number=$request->input('membership_number');
	    $authorization_number=$request->input('authorization_number');
	    $user_id=$request->input('user_id');
	    $next_of_kin_name=$request->input('next_of_kin_name');
	    $next_of_kin_resedence_id=$request->input('next_of_kin_resedence_id');
	    $relationship=$request->input('relationship');
	    $mobile_number_next_kin=$request->input('mobile_number_next_kin');
		$card_no=$request->input('card_no');
		$scheme_id=$request->input('scheme_id');
		$visit_type=$request->input('visit_type');

        $main_category = Tbl_payments_category::where("category_description", "Insurance")->first();
		$sub_category = Tbl_pay_cat_sub_category::where("sub_category_name", "NHIF")->first();
			
		$check_membership=Tbl_accounts_number::where('card_no',$card_no)->get();
		
		if(count($check_membership)>0){
			$patient_id= $check_membership[0]->patient_id;
            $patient =Tbl_patient::where('id',$patient_id)->update(array('first_name'=>$first_name,'middle_name'=>$middle_name,'last_name'=>$last_name,'dob'=>$dob,'gender'=>$gender,
                'mobile_number'=>$mobile_number,'residence_id'=>$residence_id,'occupation_id'=>$occupation,
               'facility_id'=>$facility_id,'user_id'=>$user_id));

			$patientList =Tbl_patient::where('id',$patient_id)->get();
            $account_number_id=self::patientAccountNumber($facility_id,$patient_id,$user_id,$gender,$dob,$membership_number,$authorization_number,$card_no,$scheme_id,$visit_type);

			$payment_category =Tbl_bills_category::create(['patient_id'=>$patient_id,'account_id'=>$account_number_id,'user_id'=>$user_id,'bill_id'=>$sub_category->id,'main_category_id'=>$main_category->id]);



            $encounter =Tbl_encounter_invoice::create(array('account_number_id'=>$account_number_id,'facility_id'=>$facility_id,'user_id'=>$user_id));


			$invoice_line =Tbl_invoice_line::create(array('invoice_id'=>$encounter->id,'payment_filter'=>$sub_category->id,
                    'item_type_id'=>$item_type_id,'facility_id'=>$facility_id,'quantity'=>1,'user_id'=>$user_id,'item_price_id'=>$price_id,'status_id'=>1,'discount'=>0,'discount_by'=>$user_id,'patient_id'=>$patient_id));

            $responses[]=$patientList;
            $responses[]=Tbl_accounts_number::Where('authorization_number',$authorization_number)->orderBy('id','DESC')->take(1)->get();
            $responses[]=DB::table('vw_residences')->where('residence_id',$residence_id)->get();
            $responses[]=self::getLastVisit($facility_id,$patient_id);
            $responses[]=self::enterEnvoiceBima($request,$account_number_id,$patient_id,$patient_main_category_id);
            
			return $responses;
        }

       while(true){
			$sql="SELECT t1.*,t2.facility_code FROM tbl_patients t1 
		      INNER JOIN tbl_facilities t2 ON t1.facility_id=t2.id  
			  WHERE t1.facility_id='".$facility_id."'
			  ORDER BY t1.created_at DESC LIMIT 1";
			  $patientnumber=DB::SELECT($sql);

			$sql_code="SELECT * FROM tbl_facilities t1 WHERE t1.id='".$facility_id."'";
			$facilityCodes=DB::SELECT($sql_code);
			$facility_code=preg_replace("/[_-]/","",$facilityCodes[0]->facility_code);

			//$facility ='10001';
			if(count($patientnumber)==1){

				$CustomerExecute =  $patientnumber[0]->medical_record_number;
				$TempArray = explode('/',$CustomerExecute);
				$temp_array1 = $TempArray[0];
				if( $TempArray[1]== date('Y') )
				{
					$TempArray_value = explode('-',$temp_array1);
					$num1 = $TempArray_value[1];
					$num2 = $TempArray_value[2];
					$num3 = $TempArray_value[3];
					
					if($num3 < 99) {  
						$num3 = $num3+1;   
						if(strlen($num3)==1) 
								$num3 = '0'.$num3;  
						$account_number = $num1.'-'.$num2. '-'. $num3;  
					}
					else if( $num3 ==99 && $num2 < 99 ) { 
						$num3 = '00'; $num2 = $num2+1; 
						if(strlen($num2)==1) 
							$num2 = '0'.$num2;    
						$account_number = $num1.'-'.$num2. '-'. $num3;  
					}
					else if( $num3 ==99 && $num2 == 99 && $num1 < 99) { 
						$num3 = '00'; 
						$num2 = '00';  
						$num1 = $num1+1; 
						if(strlen($num1)==1) 
							$num1 = '0'.$num1;   
						$account_number = $num1.'-'.$num2. '-'. $num3;  
					}
					else { 
						$num3 = '00'; 
						$num2 = '00';  
						$num1 = $num1+1; 
						if(strlen($num1)==1) 
							$num1 = '0'.$num1;   
						$account_number = $num1.'-'.$num2. '-'. $num3;  
					}
					$account_number  = $facility_code.'-'.$account_number.'/'.$TempArray[1];
				}else{
					$account_number  = $facility_code.'-00-00-01/'.date('Y');
				}
			}else{
				$account_number  = $facility_code.'-00-00-01/'.date('Y');
			}
			
			$ExecuteQuery = Tbl_patient::select('medical_record_number')->where('medical_record_number','=',$account_number)->count();
			
			if($ExecuteQuery ==0){
				$patient =Tbl_patient::create(array(
					'first_name'=>$first_name,
					'middle_name'=>$middle_name,
					'last_name'=>$last_name,
					'dob'=>$dob,'gender'=>$gender,
					'medical_record_number'=>$account_number,
					'mobile_number'=>$mobile_number,
					'residence_id'=>$residence_id,
					'marital_id'=>$marital_status,
					'occupation_id'=>$occupation,
					'tribe_id'=>$tribe_id,
					'country_id'=>$country_id,
					'facility_id'=>$facility_id,
					'user_id'=>$user_id)
					);

				//$patient = new Tbl_patient($request->all());
				$patient['medical_record_number'] = $account_number;
				if(!$patient->save())
					continue;
				
				$facility_id=$patient->facility_id;
				$patient_id =$patient->id;
				$user_id    =$patient->user_id;
				if(!empty($next_of_kin_name)){
					$next_kin =Tbl_next_of_kin::create(array(
						'patient_id'=>$patient_id,
						'next_of_kin_name'=>$next_of_kin_name,
						'mobile_number'=>$mobile_number_next_kin,
						'residence_id'=>$next_of_kin_resedence_id,
						'relationship'=>$relationship)
						);
					
					$next_kin->save();
				}
				$account_number_id=self::patientAccountNumber($facility_id,$patient_id,$user_id,$gender,$dob,$membership_number,$authorization_number,$card_no,$scheme_id,$visit_type);

				//enter patient bill type
				$payment_category =Tbl_bills_category::create(['patient_id'=>$patient_id,'account_id'=>$account_number_id,'user_id'=>$user_id,'bill_id'=>$sub_category->id,'main_category_id'=>$main_category->id]);


				$encounter =Tbl_encounter_invoice::create(array('account_number_id'=>$account_number_id,'facility_id'=>$facility_id,'user_id'=>$user_id));


				$invoice_line =Tbl_invoice_line::create(array('invoice_id'=>$encounter->id,'payment_filter'=>$sub_category->id,
					'item_type_id'=>$item_type_id,'facility_id'=>$facility_id,'quantity'=>1,'user_id'=>$user_id,'item_price_id'=>$price_id,'status_id'=>1,'discount'=>0,'discount_by'=>$user_id,'patient_id'=>$patient_id));

				$patientList =Tbl_patient::where('id',$patient_id)->get();
				$responses[]=$patientList;
				$responses[]=Tbl_accounts_number::Where('patient_id',$patient_id)->orderBy('id','DESC')->take(1)->get();
				$responses[]=DB::table('vw_residences')->where('residence_id',$residence_id)->get();
				$responses[]=self::getLastVisit($facility_id,$patient_id);
				$responses[]=self::enterEnvoiceBima($request,$account_number_id,$patient_id,$patient_main_category_id);
				
				return $responses;
			}else{
                continue;
            }
		}
    }

	public static function corpsesNumber($request){

        
        $facility_id=$request['facility_id'];
        $gender=$request['gender'];
        $mobile_number=$request['mobile_number'];
        $residence_id=$request['residence_id'];
         $dob=$request['dob'];
        $dod=$request['dod'];
        $time = strtotime($dob);
        $time_2 = strtotime($dod);
        $dob = date('Y-m-d',$time);
        $dod = date('Y-m-d',$time_2);

        $time = strtotime($dob);
        $dob = date('Y-m-d',$time);
        $user_id=$request['user_id'];
        $first_name=$request['first_name'];
        $middle_name=$request['middle_name'];
        $last_name=$request['last_name'];
        $residence_found=$request['residence_found'];
        $storage_reason=$request['storage_reason'];
        $corpse_brought_by=$request['corpse_brought_by'];
        $description=$request['description'];
        $transport=$request['transport'];
        $corpse_conditions=$request['corpse_conditions'];
        $police_mobile_no=$request['mobile_number'];
        $country_id=$request['country_id'];
    $diagnosis_id = $request['diagnosis_id'];
    $diagnosis_code = $request['diagnosis_code'];
        $corpse_properties=$request['corpse_properties'];
        $time_of_death_certifier=date('Y-m-d H:i:s');

        while(true){
            $corpse_number = DB::table('tbl_corpses')
                ->where('facility_id',$facility_id)
                ->orderBy('corpse_record_number','DESC')
                ->take(1)->get();
            if(count($corpse_number)>0){
                $CustomerExecute =  $corpse_number[0]->corpse_record_number;

                $corpse_number  =str_pad($CustomerExecute+1,7,'0',STR_PAD_LEFT);
            }else{
                $corpse_number='0000001';
            }

            $ExecuteQuery = DB::table('tbl_corpses')
                ->where('corpse_record_number',$corpse_number)
                ->where('facility_id',$facility_id)
                ->count();

            if($ExecuteQuery ==0){
                $corpse = new Tbl_corpse(array('gender'=>$gender,
				'dob'=>$dob,
				'dod'=>$dod,
				'first_name'=>$first_name,
				'middle_name'=>$middle_name,
				'last_name'=>$last_name,
				'corpse_brought_by'=>$corpse_brought_by,
				'storage_reason'=>$storage_reason,
				'residence_found'=>$residence_found,
				'transport'=>$transport,
				'residence_id'=>$residence_id,
				'corpse_record_number'=>$corpse_number,
				'facility_id'=>$facility_id,
				'country_id'=>$country_id,
				'corpse_conditions'=>$corpse_conditions,
				'description'=>$description,
                    "diagnosis_id" =>$diagnosis_id,
                    "diagnosis_code" =>$diagnosis_code,
				'police_mobile_no'=>$police_mobile_no,
				'corpse_properties'=>$corpse_properties,
				'user_id'=>$user_id));
				  // Auth::onceUsingId($user_id);
                    $corpse->save();
                if(!$corpse->save())
                    continue;
                $corpse_id=$corpse->id;
                $user_id=$corpse->user_id;
                $facility_id=$corpse->facility_id;

                return $corpse;


            }else{
                continue;
            }
        }
    }



    public static function patientAccountNumber($facility_id,$patient_id,$user_id, $gender = null, $dob = null,$membership_number = null,$authorization_number =null,$card_no =null,$scheme_id=null,$visit_type=null)
    {
        //return $authorization_number;
		$tallied = Tbl_accounts_number::where('patient_id',$patient_id)->where(DB::Raw("YEAR(date_attended)"), date('Y'))->where('tallied',1)->count();
		
		if($authorization_number != null && Tbl_accounts_number::where("authorization_number", $authorization_number)->count() > 0){
			$account = Tbl_accounts_number::where("authorization_number", $authorization_number)->first();
		}else{
			$account = Tbl_accounts_number::create(array(
					'user_id'=>$user_id,
					'membership_number'=>$membership_number,
					'authorization_number'=>$authorization_number,
					'account_number'=>'',
					'card_no'=>$card_no,
					'tallied'=>($tallied > 0 ? 1 : NULL),
					'facility_id'=>$facility_id,
					'scheme_id'=>$scheme_id,
					'visit_type'=>$visit_type,
					'patient_id'=>$patient_id,
					'date_attended'=>date('Y-m-d')));
					
			$account->save();
			
			if($tallied != 0 && $gender != NULL)
				ReportGenerators::countReattendance(new Request(["facility_id"=>$facility_id, "gender"=>$gender, "dob"=>$dob, "clinic_id"=>1]));
			else
				Tbl_accounts_number::where('patient_id',$patient_id)->where(DB::Raw("YEAR(date_attended)"), date("Y"))->update(["tallied"=>NULL]);
		}
		
		return $account->id;
    }



    public static function patientAccountNumberInsuarance($facility_id,$patient_id,$membership_number,$user_id,$authorization_number,$card_no, $gender = null, $dob = null)
    {
		$tallied = Tbl_accounts_number::where('patient_id',$patient_id)->where(DB::Raw("YEAR(date_attended)"), date('Y'))->where('tallied',1)->count();
		$patient = Tbl_accounts_number::create(array(
				'user_id'=>$user_id,
				'membership_number'=>$membership_number,
				'authorization_number'=>$authorization_number,
				'account_number'=>'',
			 	'card_no'=>$card_no,
			 	'tallied'=>($tallied > 0 ? 1 : NULL),
				'facility_id'=>$facility_id,
				'patient_id'=>$patient_id,
				'date_attended'=>date('Y-m-d')));
				
		if($patient->save()){
			if($tallied != 0 && $gender)
				ReportGenerators::countReattendance(new Request(["facility_id"=>$facility_id, "gender"=>$gender, "dob"=>$dob, "clinic_id"=>1]));
			else
				Tbl_accounts_number::where('patient_id',$patient_id)->where(DB::Raw("YEAR(date_attended)"), date("Y"))->update(["tallied"=>NULL]);
			
			return $patient->id;
		}
    }



    public static function checkForExemptionNumber($facility_id,$patient_id,$user_id){
        $is_patient_ExemptionNumber = DB::table('tbl_exemption_numbers')
            ->where('patient_id',$patient_id)
            ->orderBy('id','DESC')
            ->take(1)->get();
        if(count($is_patient_ExemptionNumber)>0){
            echo "Client already have Exemption number.. ";
        }	else{
           // $this->patientExemptionNumber($facility_id,$patient_id,$user_id) ;

        }


    }
	
	public static function getLastVisit($facility_id,$patient_id){
		/**
          $getLastVisit= DB::table('tbl_accounts_numbers')
            ->where('patient_id',$patient_id)
            ->where('facility_id',$facility_id)
            ->orderBy('id','DESC')
            ->take(2)->get();
			**/
			$sql="SELECT * FROM tbl_accounts_numbers t1 WHERE t1.patient_id='".$patient_id."' AND facility_id='".$facility_id."' order by id desc LIMIT 2";
			//print_r($sql);
			$getLastVisit=DB::SELECT($sql);


			//$getLastVisit=json_decode($getLastVisit);
//return count($getLastVisit);
			if(count($getLastVisit) >0){

        $last_visit=$getLastVisit[0]->created_at;
        $last_ago=self::calculateDaysInterval($last_visit);


            return 'LAST VISIT AT: '.$getLastVisit[0]->created_at.'('.$last_ago.')';
			}else{
				
			 return ', FIRST VISIT TO THIS FACILITY ';
				
			}

			
			//return $getLastVisit[0]->created_at;
			
        if(count($getLastVisit)==1){
            $last_visit=$getLastVisit[0]->created_at;
            $last_ago=self::calculateDaysInterval($last_visit);

            return response()->json([
                'registration_title' => 'LAST VISIT AT: '.$getLastVisit[0]->created_at.'('.$last_ago.')',
                'patient_id'=>$patient_id,
                'facility_id'=>$facility_id,
                'status' => '1'
            ]);


        }	else{

			$last_visit=$getLastVisit[0]->created_at;
			$last_ago=self::calculateDaysInterval($last_visit);
			
			return response()->json([
										'registration_title' => 'LAST VISIT AT: '.$getLastVisit[0]->created_at.'('.$last_ago.')',
										'patient_id'=>$patient_id,
										'facility_id'=>$facility_id,
										'status' => '1'
										]);
																			
			

        }


    }

    public static function patientExemptionNumber($facility_id,$patient_id,$user_id)
    {
        while(true){
            $patient_ExemptionNumber = DB::table('tbl_exemption_numbers')
                ->where('facility_id',$facility_id)
                //->where('patient_id',$patient_id)
                ->orderBy('id','DESC')
                ->take(1)->get();
            if(count($patient_ExemptionNumber)>0){
                $CustomerExecute =  $patient_ExemptionNumber[0]->exemption_number;
                if(substr($CustomerExecute,6,10) !=date('my')){
                    $exemption_numbers  ='000001'.date('my');
                }else{
                    $exemption_numbers  =str_pad((substr($CustomerExecute,0,6)+1),6,'0',STR_PAD_LEFT).date('my');
                }
            }else{
                $exemption_numbers  ='000001'.date('my');
            }

            $ExecuteQuery = DB::table('tbl_exemption_numbers')
                ->where('exemption_number',$exemption_numbers)
                ->where('facility_id',$facility_id)
                ->count();


            if($ExecuteQuery ==0){
                $patient = Tbl_exemption_number::create(array('exemption_number'=>$exemption_numbers,'facility_id'=>$facility_id,'patient_id'=>$patient_id,'user_id'=>$user_id));
                if(!$patient->save())
                    continue;
                return $exemption_numbers;
                //return $patient;
            }else{
                continue;
            }
        }
    }


//	search Patient
	//Generate Order Number for Laboratory Order
public static function labOrderNumber($facility_id){
   $constant=1;
        $sql="SELECT * FROM `tbl_sample_number_controls` WHERE facility_id='{$facility_id}' AND sample_no IS NOT NULL ORDER BY created_at DESC LIMIT 1";
        $lab_OrderNumber = DB::SELECT($sql);

           if(count($lab_OrderNumber)>0){
            $CustomerExecute =  $lab_OrderNumber[0]->sample_no;
            if(substr($CustomerExecute,-4,4) !=date('my')){
                $sample_number  ='000001'.date('my');
            }else{
                $sample_number=str_pad(((int)substr($CustomerExecute, -10,6)+1),6,'0',STR_PAD_LEFT).date('my');
            }
        }
           else{
               $sample_number  ='000001'.date('my');
           }



     return $sample_number;

	}


  public static function Anti_Natal_Serial_Number($facility_id,$client_id,$user_id,$client_name,$dob,$voucher_no,
                                                    $height,$occupation_id,$residence_id,$education)
    {

        while(true){
            $patient_account_number = DB::table('tbl_anti_natal_registers')
                ->where('facility_id',$facility_id)
                ->where('year','LIKE','%'.date('Y').'%')
                ->orderBy('id','DESC')
                ->take(1)->get();
            if(count($patient_account_number)>0){
                $CustomerExecute =  $patient_account_number[0]->serial_no;
                if(substr($CustomerExecute,0,4) !=date('Y')){
                    $account_number  =date('Y').'001';
                }else{
                    $account_number  =date('Y').str_pad((substr($CustomerExecute,4,3)+1),3,'0',STR_PAD_LEFT);
                }
            }else{
                $account_number  =date('Y').'001';
            }

            $ExecuteQuery = DB::table('tbl_anti_natal_registers')
                ->where('serial_no',$account_number)
                ->where('facility_id',$facility_id)
                ->where('year','like', date('Y').'%')
                ->count();
            if($ExecuteQuery ==0){
                $patient = Tbl_anti_natal_register::create(array('serial_no'=>$account_number,'facility_id'=>$facility_id,'status'=>1,
                    'user_id'=>$user_id,'client_id'=>$client_id,
                    'client_name'=>$client_name,
                    'dob'=>$dob,
                    'voucher_no'=>$voucher_no,
                    'height'=>$height,
                    'occupation_id'=>$occupation_id,
                    'residence_id'=>$residence_id,
                    'education'=>$education,
                    'year'=>date('Y')));
                if(!$patient->save())
                    continue;
                return $patient;
            }else{
                continue;
            }
        }
    }

    public static function Post_Natal_Serial_Number($facility_id,$patient_id,$user_id)
    {
        while(true){
            $patient_account_number = DB::table('tbl_post_natal_registers')
                ->where('facility_id',$facility_id)
                ->where('year','LIKE','%'.date('Y').'%')
                ->orderBy('id','DESC')
                ->take(1)->get();
            if(count($patient_account_number)>0){
                $CustomerExecute =  $patient_account_number[0]->serial_no;
                if(substr($CustomerExecute,0,4) !=date('Y')){
                    $account_number  =date('Y').'001';
                }else{
                    $account_number  =date('Y').str_pad((substr($CustomerExecute,4,3)+1),3,'0',STR_PAD_LEFT);
                }
            }else{
                $account_number  =date('Y').'001';
            }

            $ExecuteQuery = DB::table('tbl_post_natal_registers')
                ->where('serial_no',$account_number)
                ->where('facility_id',$facility_id)
                ->where('year','like', date('Y').'%')
                ->count();
            if($ExecuteQuery ==0){
                $patient = Tbl_post_natal_register::create(array('serial_no'=>$account_number,'facility_id'=>$facility_id,'status'=>1,'user_id'=>$user_id,'patient_id'=>$patient_id,'year'=>date('Y')));
                if(!$patient->save())
                    continue;
                return $patient;
            }else{
                continue;
            }
        }
    }

    public static function Labour_Serial_Number($facility_id,$patient_id,$user_id)
    {
        while(true){
            $patient_account_number = DB::table('tbl_labour_registers')
                ->where('facility_id',$facility_id)
                ->where('year','LIKE','%'.date('Y').'%')
                ->orderBy('id','DESC')
                ->take(1)->get();
            if(count($patient_account_number)>0){
                $CustomerExecute =  $patient_account_number[0]->serial_no;
                if(substr($CustomerExecute,0,4) !=date('Y')){
                    $account_number  =date('Y').'001';
                }else{
                    $account_number  =date('Y').str_pad((substr($CustomerExecute,4,3)+1),3,'0',STR_PAD_LEFT);
                }
            }else{
                $account_number  =date('Y').'001';
            }

            $ExecuteQuery = DB::table('tbl_labour_registers')
                ->where('serial_no',$account_number)
                ->where('facility_id',$facility_id)
                ->where('year','like', date('Y').'%')
                ->count();
            if($ExecuteQuery ==0){
                $patient = Tbl_labour_register::create(array('serial_no'=>$account_number,'facility_id'=>$facility_id,'status'=>1,'user_id'=>$user_id,'patient_id'=>$patient_id,'year'=>date('Y')));
                if(!$patient->save())
                    continue;
                return $patient;
            }else{
                continue;
            }
        }
    }

    public static function Child_Serial_Number($facility_id,$client_id,$user_id,$client_name,$dob,$residence_id,$father_name,$mobile_number,$mother_name,$midwife,$weight,$delivery_place,$gender)
    {
        while(true){
            $patient_account_number = DB::table('tbl_child_registers')
                ->where('facility_id',$facility_id)
                ->where('year','LIKE','%'.date('Y').'%')
                ->orderBy('id','DESC')
                ->take(1)->get();
            if(count($patient_account_number)>0){
                $CustomerExecute =  $patient_account_number[0]->serial_no;
                if(substr($CustomerExecute,0,4) !=date('Y')){
                    $account_number  =date('Y').'001';
                }else{
                    $account_number  =date('Y').str_pad((substr($CustomerExecute,4,3)+1),3,'0',STR_PAD_LEFT);
                }
            }else{
                $account_number  =date('Y').'001';
            }

            $ExecuteQuery = DB::table('tbl_child_registers')
                ->where('serial_no',$account_number)
                ->where('facility_id',$facility_id)
                ->where('year','like', date('Y').'%')
                ->count();
            if($ExecuteQuery ==0){
                $patient = Tbl_child_register::create(array(
                    'user_id'=>$user_id,
                    'facility_id'=>$facility_id,
                    'patient_id'=>$client_id,
                    'client_name'=>$client_name,
                    'dob'=>$dob,
                    'residence_id'=>$residence_id,
                    'weight'=>$weight,
                    'delivery_place'=>$delivery_place,
                    'father_name'=>$father_name,
                    'mother_name'=>$mother_name,
                    'mobile_number'=>$mobile_number,
                    'gender'=>$gender,
                    'midwife'=>$midwife,
                    'serial_no'=>$account_number,
                    'year'=>date('Y')));
                if(!$patient->save())
                    continue;
                return $patient;
            }else{
                continue;
            }
        }
    }

    public static function Family_planning_Number($facility_id,$client_id,$user_id,$client_name,$dob,$occupation_id,$residence_id,$education)
    {
        while(true){
            $patient_account_number = DB::table('tbl_family_planning_registers')
                ->where('facility_id',$facility_id)
                ->where('year','LIKE','%'.date('Y').'%')
                ->orderBy('id','DESC')
                ->take(1)->get();
            if(count($patient_account_number)>0){
                $CustomerExecute =  $patient_account_number[0]->serial_no;
                if(substr($CustomerExecute,0,4) !=date('Y')){
                    $account_number  =date('Y').'001';
                }else{
                    $account_number  =date('Y').str_pad((substr($CustomerExecute,4,3)+1),3,'0',STR_PAD_LEFT);
                }
            }else{
                $account_number  =date('Y').'001';
            }

            $ExecuteQuery = DB::table('tbl_family_planning_registers')
                ->where('serial_no',$account_number)
                ->where('facility_id',$facility_id)
                // ->where('year','like', date('Y').'%')
                ->count();
            if($ExecuteQuery ==0){
                $patient = Tbl_family_planning_register::create(array('serial_no'=>$account_number,'facility_id'=>$facility_id,
                    'user_id'=>$user_id,'client_id'=>$client_id,
                    'client_name'=>$client_name,
                    'dob'=>$dob,
                    'occupation_id'=>$occupation_id,
                    'residence_id'=>$residence_id,
                    'education'=>$education,
                    'year'=>Date('Y')));
                if(!$patient->save())
                    continue;
                return $patient;
            }else{
                continue;
            }
        }
        }
		
		public  static function getSeachedCorpses(Request $request){
			$searchKey = $request->input('searchKey');
			$patientSearched=DB::table('tbl_corpses')->where('status',0)
			->where('last_name','like','%'.$searchKey.'%')		
			->orWhere('corpse_record_number','like','%'.$searchKey.'%')
			->orwhere('mobile_number','like','%'.$searchKey.'%')
			->groupBy('corpse_record_number')
			 ->get();
			return $patientSearched;
		}

}