<?php

namespace App\Http\Controllers\admin;
use App\chats\tbl_notification;
use Illuminate\Support\Facades\Input;
use Illuminate\Http\Request;

use App\Http\Controllers\Controller;
use App\admin\Tbl_permission;
use App\admin\Tbl_permission_user;
use App\admin\Tbl_permission_role;
use App\admin\Tbl_role;
use App\admin\Tbl_staff_photo;
use App\admin\Tbl_route_key;
use App\admin\Tbl_integrating_key;
use DB;
use App\classes\systemViews;
use App\classes\Uuids;
use Config;
use Illuminate\Support\Facades\Artisan;
class stateController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
	 
	 public function saveRoutingKeys(Request $request){
		//return Uuids::createUid();
		//$execute=Artisan::call('migrate', ["--force"=> true ]);
		$dbHost='localhost';
		$dbPort='3306';
		$dbUser='root';
		$dbPass='';
		
		// Setting connection info from console prompts
		$this->config->set('database.connections.mysql.host', $dbHost);
		$this->config->set('database.connections.mysql.port', $dbPort);
		$this->config->set('database.connections.mysql.database', null);
		$this->config->set('database.connections.mysql.username', $dbUser);
		$this->config->set('database.connections.mysql.password', $dbPass);

		// Creating DB if it doesn't exist
		$this->db->purge('mysql');
		$this->db->connection('mysql')->getPdo()->exec("CREATE DATABASE IF NOT EXISTS `{$dbName}`");
	 }

	 public function createNewDatabase(Request $request){
		 $database="mimi";
		$mysql_only_connect = [
				'driver'    => 'mysql',
				'host'      => env('DB_HOST', '127.0.0.1'),
				'port'      => env('DB_PORT', '3306'),
				'database'  => null,
				'username'  => env('DB_USERNAME', 'root'),
				'password'  => env('DB_PASSWORD', '')
		];

		DB::connection($mysql_only_connect)->statement("CREATE DATABASE ".$database." DEFAULT CHARACTER SET utf8;");
	 }
	 
	 public function uploadEntry(Request $request){
      //laravel form submission
      /*  
      if($request->hasFile('uploadedFile')){
        $files = $request->file('uploadedFile');
        foreach ($files as $file) {  
            $fileName = rand(11111,99999).'.jpg';
            $file->move( 'uploads' , $fileName);
        }
      } */
       
      //angular API submission
       // return $request->all();
        $file =0; 
		//echo Input::hasFile($file);
        while (Input::hasFile($file)) {
          $destinationPath = 'uploads'; // upload path
          $fileName =  $request->photo_for.'-'.date('dmyhis').'-'.rand(11111,99999).'.jpg'; // renameing image
		  
          if(Input::file($file)->move($destinationPath, $fileName)){
	      $admin = new Tbl_staff_photo($request->all());
		  //return $admin;
		  $admin['photo_path']=$fileName;
		  $admin['photo_for']=$request->photo_for;
		  //return $admin;
		   // return response($admin, 101);
		  
		  
				if(!$admin->save()){
			   return response("Error Encounted: Failed to save", 101);
					
						}
	      return response("FILE WAS SUCCESSFULLY UPLOADED.", 200);
		  } else{
			  
			  return response("UNABLE TO UPLOAD FILE", 101);
		  
		  }// uploading file to given path
          $file++;
        }
   }
	 
	 
	 
	  	 
	 public function userView($facility_id){
		systemViews::useraccessLevel();
		systemViews::vw_dispensing_item_balance();
		systemViews::vw_prescriptions_dispensed();
		systemViews::vw_prescriptions();
		systemViews::conceptDictionary();
		systemViews::billsPayments($facility_id);
		systemViews::createUserDetails();
		systemViews::residencesView();	
		systemViews::labTests($facility_id);
		systemViews::equipmentsInfo($facility_id);
		systemViews::labRequests($facility_id);
		systemViews::collectedSamples();
		systemViews::testsResultsDone();
		systemViews::approvedResultsDone();
		systemViews::sampleNumberLists();
		systemViews::createCabinetsDetails();
		systemViews::patientsToPoS($facility_id);
		systemViews::departmentalReports();	
		systemViews::searchpatient();	
		systemViews::pendingAdmission();	
		systemViews::approvedAdmission();	
		systemViews::createWardDetails();	
		systemViews::createBedsDetails();
		systemViews::PatientWardBed();
		systemViews::radiologyPatients();
		systemViews::patient_xray();
		systemViews::emergency_records();
		systemViews::imaging_rejesta();
		systemViews::patientsDetails();
		systemViews::postedResults();
		systemViews::vitalSign();
		systemViews::vitalSignOutput();
		systemViews::clinicPayee();
		systemViews::clinic_attendency_payee();
		systemViews::equipmentStatuses();
		systemViews::opdPatients();
		systemViews::ipdPatients();
		systemViews::investigationTests();
		systemViews::investigationResults();
		systemViews::continuationNotes();
		systemViews::diagnosis();
		systemViews::familyHistory();
		systemViews::birthHistory();
		systemViews::historyExaminations();
		systemViews::obs();
		systemViews::ros();
		systemViews::physicalExaminations();
		systemViews::admittedPatients();
		systemViews::referrals();
		systemViews::allergies();
		systemViews::detailedReports();
		systemViews::itemPrices();
		systemViews::registrarServices();
		systemViews::labPanels();
		systemViews::getDischargedPatients();
		systemViews::getCabinets();
		systemViews::corpsesInside();
		systemViews::corpsesOutside();
		systemViews::labTestsToDoctor();
		systemViews::assignedPerms();
		systemViews::assignedPermsRoles();
		systemViews::getListForCtc();
		systemViews::getAttendanceListForCtc();
		//replaced by one function mtuhaViews
		/*
		systemViews::createViewIpdAdmitted();
		systemViews::createViewNewOpdAttendaces();
		systemViews::createViewClinicAttendances();
		systemViews::createViewDiagnosisOpdAttendaces();
		systemViews::createViewIpdDiagnosis();
		*/
		systemViews::mtuhaViews();

		systemViews::getAdmittedPatientsIPd();
		systemViews::createViewMarudioOpdAttendaces();
		systemViews::createViewOpdAttendaces();
		systemViews::createViewTestsOutOfstock();
		systemViews::createViewOutOfstock();
		systemViews::vw_invoice();
		systemViews::vw_exemptions();
		systemViews::vw_receivings();
		systemViews::vw_pharmacy_items();
		systemViews::vw_substore();
		systemViews::vw_dispensing_window();
		systemViews::vw_sub_store_incoming_order();
		systemViews::vw_main_store_incoming_order();
		systemViews::vw_receivings_issuing_details();
		systemViews::vw_gbv_vacs();
		systemViews::vw_vulnerables();
		systemViews::vw_temporary_exemptions();
		systemViews::vw_violance();
		systemViews::nhifPatients();
		systemViews::bimaPrescriptions();
		systemViews::bimaInvestigations();
		//systemViews::pendingBills();
		systemViews::createViewDoctors();
		systemViews::bimaProcedures();
		systemViews::vw_child_attendance();
		systemViews::vw_child_feedings();
		systemViews::investigationLists();
		systemViews::previousMedications();
		systemViews::specialClinicClients();
		systemViews::previousProcedures();
		systemViews::specialClinics();
		systemViews::createViewStaffPermance();
		systemViews::vw_exemption_finance();
		systemViews::getListOfTestsToVerify();
		systemViews::vw_exemption_finance_depts();
		systemViews::vw_exemption_servce_summary();
		systemViews::vw_exemp_sub_department_summary();
		systemViews::getPrescribedItems();
		systemViews::pendingDischarge();
		systemViews::subDepartmentReports();
		systemViews::therapy_assessments();
		systemViews::therapyTreatments();
		systemViews::exempted_clinic();
		systemViews::continuation_notes();
		systemViews::createTreatmentChart();
		systemViews::FolioItems();
		systemViews::getSampleReports();
		systemViews::remoteInvestigationResults();
		systemViews::RadiologyUsers();
		systemViews::continuationRemarks();
		systemViews::Emmergency_procedures();
		systemViews::vw_shop_item_balance();
		systemViews::eyeExamination();		
     }
	 
	 
	 
		 
	 public function checkIfisEmpty($item_name){
		 $this->item_name = (is_null($item_name) || empty($item_name) || strlen($item_name) < 1 ? NULL : $item_name);

    return $this->item_name;
		 
	 }
	 
	 public function geticon(){		 
		    $geticon=  DB::table('tbl_glyphicons')
						->get();
						return $geticon;
	 } 
	 
	 public function getUserImage($id){		 
		    $getphoto=  DB::table('tbl_staff_photos')
			            ->select('photo_path')
			            ->where('photo_for',$id)
			            ->orderBy('id','DESC')
						->get()->take(1);
					return $getphoto;
	 }


	 public function sendSmsToGroup(request $request){
	     $response=[];
	     $message=$request->message;
         $response[] =Tbl_notification::create($request->all());

         $response[] =Tbl_notification::orderBy('id','DESC')->get();

         $response[] =date('d-m-Y h:i:s');

         $response[] =Tbl_notification::count();


         return $response;


     }


	  public function getNotifications(request $request){
	     $response=[];
	     $response[] =Tbl_notification::orderBy('id','ASC')->get();

         $response[] =date('d-m-Y h:i:s');

         $response[] =Tbl_notification::count();

         $response[] =Tbl_notification::orderBy('id','DESC')->take(4)->get();


         return $response;


     }

	 
	 public function getPermissions(){		 
		    $permissions =  DB::table('tbl_permissions')
						->get();
						return $permissions;
	 }
	 
	 public function getRoles(){		 
		    $roles =  DB::table('tbl_roles')
						->get();
						return $roles;
	 }
	 
	 
	 public function getRoleName($role_id){		 
		    $role_name=  DB::table('tbl_roles')
						->select('title')
						->where('id',$role_id)
						->first();
						return $role_name->title;
	 }
	 
	 
	 public function getUserName($user_id){		 
		    $user_name=  DB::table('users')
						->select('name')
						->where('id',$user_id)
						->first();
						return   $user_name->name;
	 }
	 
	 
	 public function getPermissionName($permission_id){
                         //$permission_id=$request->input('permission_id');		 
		    $permission=  DB::table('tbl_permissions')
						->select('title')
						->where('id',$permission_id)
						->first();
						
						return $permission->title;
	 }
	 
	public function checkIfStateExists(Request $request){
				
		$state_name=$request->input('module');	
		    $state_if_exist= DB::table('tbl_permissions')
						->select('module')
						->where('module',$state_name)
						->count();
						
				if($state_if_exist>0){					
					return $state_name.' , already exists. Register state with other name';
										
				}else{
				$admin = new Tbl_permission($request->all());
				if(!$admin->save()){
					return 'Error 101: System failed to save this Menu,try again';
					
						}else{
							return 'Success: The state name was successfully saved.';
					
						}		
		
		
	} 
	}	
	
	public function checkIfRoleExists(Request $request){
				
		$role_name=strtoupper($request->input('title'));		
		$task_performed=strtoupper($request->input('parent'));

     //return $task_performed;		
		
	    $role_if_exist= DB::table('tbl_roles')
						->select('title')
						->where('title',$role_name)
						->where('parent',$task_performed)
						->count();
							//return $state_name;		
				if($role_if_exist>0){					
					return $task_performed.' , already assigned to '.$role_name.'. Register another ROLE TASK';
										
				}else{
				$admin = new Tbl_role($request->all());
				if(!$admin->save()){
					return 'Error 101: System failed to save this Role,try again';
					
						}else{
							return 'Success: The ROLE name was successfully saved.';
					
						}	
		
	} 
	}
	
	
	
	 // ASSIGN PERMISSIONS AND USERS
	public function checkIfPermissionUserExists(Request $request){
				
		$permission_id=strtoupper($request->input('permission_id'));		
		$user_id=strtoupper($request->input('user_id'));	
		
		$permission_user_if_exist= DB::table('tbl_permission_users')
						->where('permission_id',"'".$permission_id."'")
						->where('user_id',"'".$user_id."'")
						->where('grant',1)	
						->first();
			//return $request->all();
				$admin = new Tbl_permission_user($request->all());
				if(!$admin->save()){
					return 'Error 101: System failed to save this Role,try again';
					
						}else{
                    return response()->json([
                        'data' =>$this->getPermissionName($permission_id).' was successfully assigned to '.$this->getUserName($user_id),
                        'status' =>1
                    ]);
					
						
	} 
	}
	public function searchUser(Request $request){
		$facility_id=$request->facility_id;
		$keyword=$request->email;
		$sql="SELECT * FROM users t1 WHERE t1.email LIKE '%".$keyword."%'
		                               AND t1.facility_id='".$facility_id."'";
		return DB::SELECT($sql);
		
		
		
		
	}
	
	public function changeStatus(Request $request){
		$user_id=$request->user_id;
		$sql="UPDATE users t1 SET `loggedIn`=0  WHERE t1.id='".$user_id."'";
		if(DB::STATEMENT($sql)){
			return 1;
		}else{
			return 0;
		}
			
	}
	
	public function getFacilityCentrally($facility_code){
		
		if($facility_code=='all'){
			$sql="SELECT  * FROM tbl_facilities t1 GROUP BY facility_code";
			
		}else{
			$sql="SELECT  * FROM tbl_facilities t1 WHERE t1.facility_code='".$facility_code."'";
		}
		
	
	$data=DB::SELECT($sql);
	if(count($data)>0){
		  return response()->json([
                'data' => $data,
                'status' => 1
            ]);
		
	}else{
		 return response()->json([
           'data' => 'Sorry this facility code, '.$facility_code.', Not yet registered',
           'status' => 0
            ]);
	}
			
			
	}
	
	public function synchronizeFacilityCentrally(Request $request){
		$facility_code="all";
		
		if(isset($request->facility_code)){
			$facility_code=$request->facility_code;
		}
		
		$intergratingKeys=Tbl_integrating_key::where('api_type',1)->get();
		      $base_urls=$intergratingKeys[0]->base_urls;
		      $private_key=$intergratingKeys[0]->private_keys;
		      $public_key=$intergratingKeys[0]->public_keys;
		 
	   
	$request=$base_urls.'/api/getFacilityCentrally/'.$facility_code;
		//return $request;
		//  Initiate curl
		
		//return $request;

     
        $ch = curl_init($request);
        $request_method = 'GET';
        $nonce=uniqid("",true);
        // calculate the hash
        $timestamp=strval(time());
        $data_hash='';
        $signature_raw_data=$public_key.$request_method.$timestamp.$nonce.$data_hash;
        $hash = hash_hmac ('sha256', $signature_raw_data, $private_key,$raw=true);
        $signature = base64_encode($hash);
        $amx=$public_key.':'.$signature.':'.$nonce.':'.$timestamp;
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array('Authorization: amx '.$amx));
        $result = curl_exec($ch);
        $StatusCode=  curl_getinfo($ch,CURLINFO_HTTP_CODE);

        if($StatusCode == 200){
            $array_data = json_decode($result,true);
            $array_data['StatusCode'] = $StatusCode;
            $result = json_encode($array_data);
        }
		else{
            $array_data = array();
            $array_data['StatusCode'] = $StatusCode;
            $array_data['Message'] = $result;
            $result = json_encode($array_data);
        }

        curl_close($ch);
		
		//$result = json_decode($result);

        return $result;
			
	}
	
	
	
	
	public function getAssignedMenu($assignedUserId){

        	return DB::table('vw_assigned_perms')
                ->where('user_id',$assignedUserId)
                ->where('grant',1)
                        ->get();
    }


public function getAssignedRole($assignedRoleId){

        	return DB::table('vw_assigned_perms_role')
                ->where('role_id',$assignedRoleId)
                ->where('grant',1)
                        ->get();
    }


	public function getSystemActivity(){
           	return Tbl_role::all();
    }

    public function removeAccess(request $request){
	         $permID=$request->perm_id;
	         $title=$request->title;
        Tbl_permission_user::WHERE("id",$permID)->update(array("grant"=>0));

return response()->json([
'data' =>$title.' WAS SUCCEFULLY REMOVED',
'status' =>1
]);
}

    public function removeRoleAccess(request $request){
	         $permission_role_id=$request->permission_role_id;
	         $title=$request->title;
	         $role_name=$request->role_name;
        Tbl_permission_role::WHERE("id",$permission_role_id)->update(array("grant"=>0));

return response()->json([
'data' =>$title.' WAS SUCCEFULLY REMOVED FROM '.$role_name,
'status' =>1
]);
}


					



	 // ASSIGN PERMISSIONS AN ROLES
	public function checkIfPermissionRoleExists(Request $request){
			

			
		$permission_id=strtoupper($request->input('permission_id'));		
		$role_id=strtoupper($request->input('role_id'));	
		
		$permission_role_if_exist= DB::table('tbl_permission_roles')
						->where('permission_id',$permission_id)
						->where('role_id',$role_id)
						->where('grant',1)
	
						->first();
						
					
				$admin = new Tbl_permission_role($request->all());
					
		
	                
				   if(!$admin->save()){
				return 'Error 101: '.$this->getPermissionName($permission_id).' was UNSUCCESSFULY assigned to '.$this->getRoleName($role_id);;
					
						}else{
			           return response()->json([
                           'data' =>$this->getPermissionName($permission_id).' was successfully assigned to '.$this->getRoleName($role_id),
                           'status' =>1
                       ]);
						}
				   
							
						
									
			 
	}  
	 
	 
}