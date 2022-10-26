<?php


namespace App\Http\Controllers\reports;
ini_set('max_execution_time', -1);

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;

class ReportGenerators extends Controller
{
   public function countNewAttendance(Request $request){
		if(!isset($request->clinic_id))
			return;
        //find age in months
        $dob = explode('-',$request['dob']);
        $dob = new \DateTime($request['dob']);
        $interval = (new \DateTime())->diff($dob);
        $age_group = $interval->m + $interval->y*12;
        $gender = strtoupper($request['gender']);

        if($age_group <= 0){
            $age_group = ($gender == "MALE" ? "male_under_one_month" : "female_under_one_month");
            $total_group = "total_under_one_month";
        }elseif($age_group <= 11){
            $age_group = ($gender == "MALE" ? "male_under_one_year" : "female_under_one_year");
            $total_group = "total_under_one_year";
        }elseif($age_group <= 59){
            $age_group = ($gender == "MALE" ? "male_under_five_year" : "female_under_five_year");
            $total_group = "total_under_five_year";
        }elseif($age_group <= 719){
            $age_group = ($gender == "MALE" ? "male_above_five_under_sixty" : "female_above_five_under_sixty");
            $total_group = "total_above_five_under_sixty";
        }elseif($age_group >= 720){
            $age_group = ($gender == "MALE" ? "male_above_sixty" : "female_above_sixty");
            $total_group = "total_above_sixty";
        }
		
		$todayCounts = DB::select("select count(*) count from tbl_newattendance_registers where facility_id='".$request['facility_id']."' and clinic_id='".$request['clinic_id']."' and date=CURRENT_DATE");
        if(count($todayCounts) > 0  && $todayCounts[0]->count > 0){
            DB::statement("update tbl_newattendance_registers set $age_group = $age_group+1,$total_group=$total_group+1,".($gender == "MALE" ? "total_male = total_male+1" : "total_female=total_female+1").",grand_total=grand_total+1 where date = CURRENT_DATE and facility_id='".$request['facility_id']."' and clinic_id='".$request['clinic_id']."'");
        }else{
            DB::statement("insert into tbl_newattendance_registers(facility_id,clinic_id,$age_group,$total_group,".($gender == "MALE" ? "total_male" : "total_female").",grand_total,date) select '".$request['facility_id']."','".$request['clinic_id']."',1,1,1,1, CURRENT_DATE");
        }
    }

   

    public static function countReattendance(Request $request){
		if(!isset($request->clinic_id))
			return;
        //find age in months
        $dob = explode('-',$request['dob']);
        $dob = new \DateTime($request['dob']);
        $interval = (new \DateTime())->diff($dob);
        $age_group = $interval->m + $interval->y*12;
        $gender = strtoupper($request['gender']);

        if($age_group <= 0){
            $age_group = ($gender == "MALE" ? "male_under_one_month" : "female_under_one_month");
            $total_group = "total_under_one_month";
        }elseif($age_group <= 11){
            $age_group = ($gender == "MALE" ? "male_under_one_year" : "female_under_one_year");
            $total_group = "total_under_one_year";
        }elseif($age_group <= 59){
            $age_group = ($gender == "MALE" ? "male_under_five_year" : "female_under_five_year");
            $total_group = "total_under_five_year";
        }elseif($age_group <= 719){
            $age_group = ($gender == "MALE" ? "male_above_five_under_sixty" : "female_above_five_under_sixty");
            $total_group = "total_above_five_under_sixty";
        }elseif($age_group >= 720){
            $age_group = ($gender == "MALE" ? "male_above_sixty" : "female_above_sixty");
            $total_group = "total_above_sixty";
        }
		
        $todayCounts = DB::select("select count(*) count from tbl_reattendance_registers where facility_id='".$request['facility_id']."' and clinic_id='".$request['clinic_id']."' and date=CURRENT_DATE");
        if(count($todayCounts) > 0  && $todayCounts[0]->count > 0){
            DB::statement("update tbl_reattendance_registers set $age_group = $age_group+1,$total_group=$total_group+1,".($gender == "MALE" ? "total_male = total_male+1" : "total_female=total_female+1").",grand_total=grand_total+1 where date = CURRENT_DATE and facility_id='".$request['facility_id']."' and clinic_id='".$request['clinic_id']."'");
        }else{
            DB::statement("insert into tbl_reattendance_registers(facility_id,clinic_id,$age_group,$total_group,".($gender == "MALE" ? "total_male" : "total_female").",grand_total,date) select '".$request['facility_id']."','".$request['clinic_id']."',1,1,1,1, CURRENT_DATE");
        }
    }

	public function countReferral(Request $request){
		$dob = explode('-',$request['dob']);
        $dob = new \DateTime($request['dob']);
        $interval = (new \DateTime())->diff($dob);
        $age_group = $interval->m + $interval->y*12;
        $gender = strtoupper($request['gender']);

        if($age_group <= 0){
            $age_group = ($gender == "MALE" ? "male_under_one_month" : "female_under_one_month");
            $total_group = "total_under_one_month";
        }elseif($age_group <= 11){
            $age_group = ($gender == "MALE" ? "male_under_one_year" : "female_under_one_year");
            $total_group = "total_under_one_year";
        }elseif($age_group <= 59){
            $age_group = ($gender == "MALE" ? "male_under_five_year" : "female_under_five_year");
            $total_group = "total_under_five_year";
        }elseif($age_group <= 719){
            $age_group = ($gender == "MALE" ? "male_above_five_under_sixty" : "female_above_five_under_sixty");
            $total_group = "total_above_five_under_sixty";
        }elseif($age_group >= 720){
            $age_group = ($gender == "MALE" ? "male_above_sixty" : "female_above_sixty");
            $total_group = "total_above_sixty";
        }

        $todayCounts = DB::select("select count(*) count from tbl_outgoing_referral_registers where facility_id='".$request['facility_id']."' and date=CURRENT_DATE");
        if(count($todayCounts) > 0  && $todayCounts[0]->count > 0){
            DB::statement("update tbl_outgoing_referral_registers set $age_group = $age_group+1,$total_group=$total_group+1,".($gender == "MALE" ? "total_male = total_male+1" : "total_female=total_female+1").",grand_total=grand_total+1 where date = CURRENT_DATE and facility_id='".$request['facility_id']."'");
        }else{
            DB::statement("insert into tbl_outgoing_referral_registers(facility_id,$age_group,$total_group,".($gender == "MALE" ? "total_male" : "total_female").",grand_total,date) select '".$request['facility_id']."',1,1,1,1, CURRENT_DATE");
        }
    }

	
    public static function countAdmission(Request $request){
        //find age in months
        $dob = explode('-',$request['dob']);
        $dob = new \DateTime($request['dob']);
        $interval = (new \DateTime())->diff($dob);
        $age_group = $interval->m + $interval->y*12;
        $gender = strtoupper($request['gender']);

        if($age_group <= 0){
            $age_group = ($gender == "MALE" ? "male_under_one_month" : "female_under_one_month");
            $total_group = "total_under_one_month";
        }elseif($age_group <= 11){
            $age_group = ($gender == "MALE" ? "male_under_one_year" : "female_under_one_year");
            $total_group = "total_under_one_year";
        }elseif($age_group <= 59){
            $age_group = ($gender == "MALE" ? "male_under_five_year" : "female_under_five_year");
            $total_group = "total_under_five_year";
        }elseif($age_group <= 719){
            $age_group = ($gender == "MALE" ? "male_above_five_under_sixty" : "female_above_five_under_sixty");
            $total_group = "total_above_five_under_sixty";
        }elseif($age_group >= 720){
            $age_group = ($gender == "MALE" ? "male_above_sixty" : "female_above_sixty");
            $total_group = "total_above_sixty";
        }

        $todayCounts = DB::select("select count(*) count from tbl_admission_registers where facility_id='".$request['facility_id']."' and  date=CURRENT_DATE");
        if(count($todayCounts) > 0  && $todayCounts[0]->count > 0){
            DB::statement("update tbl_admission_registers set $age_group = $age_group+1,$total_group=$total_group+1,".($gender == "MALE" ? "total_male = total_male+1" : "total_female=total_female+1").",grand_total=grand_total+1 where date = CURRENT_DATE and facility_id='".$request['facility_id']."'");
        }else{
            DB::statement("insert into tbl_admission_registers(facility_id,$age_group,$total_group,".($gender == "MALE" ? "total_male" : "total_female").",grand_total,date) select '".$request['facility_id']."',1,1,1,1, CURRENT_DATE");
        }
    }
	
	 //
	public function countClinicDiagnosis(Request $request){
        
    }

	
    public function countOPDDiagnosis(Request $request){
        //find age in months
        $dob = new \DateTime($request['dob']);
        $interval = (new \DateTime())->diff($dob);
        $age_group = $interval->m + $interval->y*12;
        $gender = strtoupper($request['gender']);

        if($age_group <= 0){
            $age_group = ($gender == "MALE" ? "male_under_one_month" : "female_under_one_month");
            $total_group = "total_under_one_month";
        }elseif($age_group <= 11){
            $age_group = ($gender == "MALE" ? "male_under_one_year" : "female_under_one_year");
            $total_group = "total_under_one_year";
        }elseif($age_group <= 59){
            $age_group = ($gender == "MALE" ? "male_under_five_year" : "female_under_five_year");
            $total_group = "total_under_five_year";
        }elseif($age_group <= 719){
            $age_group = ($gender == "MALE" ? "male_above_five_under_sixty" : "female_above_five_under_sixty");
            $total_group = "total_above_five_under_sixty";
        }elseif($age_group >= 720){
            $age_group = ($gender == "MALE" ? "male_above_sixty" : "female_above_sixty");
            $total_group = "total_above_sixty";
        }
		
		$uid_map = DB::select("SELECT a male_under_one_month,
									  b female_under_one_month,
									  c male_under_one_year,
									  d female_under_one_year,
									  e male_under_five_year,
									  f female_under_five_year,
									  g male_above_five_under_sixty,
									  h female_above_five_under_sixty,
									  i male_above_sixty,
									  j female_above_sixty
							   FROM `dhis_book_uid_maps` where book='v6wdME3ouXu'");
			
        $observations = $request['concepts'];
        foreach($observations as $observation){
            $mtuha_diagnosis = DB::select("select opd_mtuha_diagnosis_id as mtuha_diagnosis_id from tbl_opd_mtuha_icd_blocks where ".(strpos($observation['code'], ".") === false || (strpos($observation['code'], ".") !== false && \App\Mtuha\Tbl_opd_mtuha_icd_block::where("icd_block",$observation['code'])->count() != 0) ? "icd_block = '".$observation['code']."'" : "'".$observation['code']."' like concat(icd_block,'%')"));
			if(count($mtuha_diagnosis) == 0){//no match, count as others
				$todayCounts = DB::select("select count(*) count from tbl_opd_diseases_registers where opd_mtuha_diagnosis_id IS NULL and facility_id='".$request['facility_id']."' and date=CURRENT_DATE");
				if(count($todayCounts) > 0  && $todayCounts[0]->count > 0){
					DB::statement("update tbl_opd_diseases_registers set $age_group = $age_group+1,$total_group=$total_group+1,".($gender == "MALE" ? "total_male = total_male+1" : "total_female=total_female+1").",grand_total=grand_total+1 where date = CURRENT_DATE  and opd_mtuha_diagnosis_id IS NULL and facility_id='".$request['facility_id']."'");
				}else{
					DB::statement("insert into tbl_opd_diseases_registers(facility_id,opd_mtuha_diagnosis_id,$age_group,$total_group,".($gender == "MALE" ? "total_male" : "total_female").",grand_total,date) select '".$request['facility_id']."',NULL,1,1,1,1, CURRENT_DATE");
				}
				continue;
			}
				
			foreach($mtuha_diagnosis as $diagnosis){
				if(empty($uid_map[$diagnosis->mtuha_diagnosis_id+2]->{$age_group}))
					continue;
				$todayCounts = DB::select("select count(*) count from tbl_opd_diseases_registers where opd_mtuha_diagnosis_id = '". $diagnosis->mtuha_diagnosis_id."' and facility_id='".$request['facility_id']."' and date=CURRENT_DATE");
				if(count($todayCounts) > 0  && $todayCounts[0]->count > 0){
					DB::statement("update tbl_opd_diseases_registers set $age_group = $age_group+1,$total_group=$total_group+1,".($gender == "MALE" ? "total_male = total_male+1" : "total_female=total_female+1").",grand_total=grand_total+1 where date = CURRENT_DATE  and opd_mtuha_diagnosis_id = ".$diagnosis->mtuha_diagnosis_id." and facility_id='".$request['facility_id']."'");
				}else{
					DB::statement("insert into tbl_opd_diseases_registers(facility_id,opd_mtuha_diagnosis_id,$age_group,$total_group,".($gender == "MALE" ? "total_male" : "total_female").",grand_total,date) select '".$request['facility_id']."',".$diagnosis->mtuha_diagnosis_id.",1,1,1,1, CURRENT_DATE");
				}
			}
        }
    }

    public function countIPDDiagnosis(Request $request){
        //find age in months
        $dob = explode('-',$request['dob']);
        $dob = new \DateTime($request['dob']);
        $interval = (new \DateTime())->diff($dob);
        $age_group = $interval->m + $interval->y*12;
        $gender = strtoupper($request['gender']);

        if($age_group <= 0){
            $age_group = ($gender == "MALE" ? "male_under_one_month" : "female_under_one_month");
            $total_group = "total_under_one_month";
        }elseif($age_group <= 11){
            $age_group = ($gender == "MALE" ? "male_under_one_year" : "female_under_one_year");
            $total_group = "total_under_one_year";
        }elseif($age_group <= 59){
            $age_group = ($gender == "MALE" ? "male_under_five_year" : "female_under_five_year");
            $total_group = "total_under_five_year";
        }elseif($age_group <= 719){
            $age_group = ($gender == "MALE" ? "male_above_five_under_sixty" : "female_above_five_under_sixty");
            $total_group = "total_above_five_under_sixty";
        }elseif($age_group >= 720){
            $age_group = ($gender == "MALE" ? "male_above_sixty" : "female_above_sixty");
            $total_group = "total_above_sixty";
        }

        $uid_map = DB::select("SELECT a male_under_one_month,
									  b female_under_one_month,
									  c male_under_one_year,
									  d female_under_one_year,
									  e male_under_five_year,
									  f female_under_five_year,
									  g male_above_five_under_sixty,
									  h female_above_five_under_sixty,
									  i male_above_sixty,
									  j female_above_sixty
							   FROM `dhis_book_uid_maps` where book='qpcwPcj8D6u'");
		
        $observations = $request['concepts'];
        foreach($observations as $observation){
            $mtuha_diagnosis = DB::select("select ipd_mtuha_diagnosis_id as mtuha_diagnosis_id from tbl_ipd_mtuha_icd_blocks where ".(strpos($observation['code'], ".") === false || (strpos($observation['code'], ".") !== false && \App\Mtuha\Tbl_ipd_mtuha_icd_block::where("icd_block",$observation['code'])->count() != 0) ? "icd_block = '".$observation['code']."'" : "'".$observation['code']."' like concat(icd_block,'%')"));
			if(count($mtuha_diagnosis) == 0){
				$todayCounts = DB::select("select count(*) count from tbl_ipd_diseases_registers where ipd_mtuha_diagnosis_id IS NULL and facility_id='".$request['facility_id']."' and date=CURRENT_DATE");
				if(count($todayCounts) > 0  && $todayCounts[0]->count > 0){
					DB::statement("update tbl_ipd_diseases_registers set $age_group = $age_group+1,$total_group=$total_group+1,".($gender == "MALE" ? "total_male = total_male+1" : "total_female=total_female+1").",grand_total=grand_total+1 where date = CURRENT_DATE  and ipd_mtuha_diagnosis_id IS NULL and facility_id='".$request['facility_id']."'");
				}else{
					DB::statement("insert into tbl_ipd_diseases_registers(facility_id,ipd_mtuha_diagnosis_id,$age_group,$total_group,".($gender == "MALE" ? "total_male" : "total_female").",grand_total,date) select '".$request['facility_id']."',NULL,1,1,1,1, CURRENT_DATE");
				}
				continue;
			}
				
			foreach($mtuha_diagnosis as $diagnosis){
				if(empty($uid_map[$diagnosis->mtuha_diagnosis_id]->{$age_group}))
					continue;
				$todayCounts = DB::select("select count(*) count from tbl_ipd_diseases_registers where ipd_mtuha_diagnosis_id = '". $diagnosis->mtuha_diagnosis_id."' and facility_id='".$request['facility_id']."' and date=CURRENT_DATE");
				if(count($todayCounts) > 0  && $todayCounts[0]->count > 0){
					DB::statement("update tbl_ipd_diseases_registers set $age_group = $age_group+1,$total_group=$total_group+1,".($gender == "MALE" ? "total_male = total_male+1" : "total_female=total_female+1").",grand_total=grand_total+1 where date = CURRENT_DATE  and ipd_mtuha_diagnosis_id = ".$diagnosis->mtuha_diagnosis_id." and facility_id='".$request['facility_id']."'");
				}else{
					DB::statement("insert into tbl_ipd_diseases_registers(facility_id,ipd_mtuha_diagnosis_id,$age_group,$total_group,".($gender == "MALE" ? "total_male" : "total_female").",grand_total,date) select '".$request['facility_id']."',".$diagnosis->mtuha_diagnosis_id.",1,1,1,1, CURRENT_DATE");
				}
			}
        }
    }
    public function restartRegister($facility_id){
		$started_at = new \DateTime();
		$this->setAdmissions($facility_id);
		$this->setAttendances($facility_id);
		$this->setReattendances($facility_id);
		$this->setOpdDiseases($facility_id);
		$this->setIpdDiseases($facility_id);
		$this->setReferrals($facility_id);		
		return response()->json("Process completed. Time taken:".(new \DateTime())->diff($started_at)->format('%H:%i:%s') );
    }
	
	
	public function seedMtuha($facility_id){
		return response()->json("//TO DO");
	}
	
	public static function setAttendances($facility_id, $clinic_id = 1){
		DB::statement("SET @opd_attendance_message = '".$facility_id."'");
		DB::statement("CALL database_maintenance_generate_opd_attendance_register(@opd_attendance_message)");
		$opd_attendance_response = DB::select("SELECT @opd_attendance_message");
		return $opd_attendance_response[0]->{'@opd_attendance_message'} != 0 ? 'Process Completed': 'Procee failed';	
	}
	
	public static function setReattendances($facility_id, $clinic_id = 1){
		DB::statement("SET @opd_reattendance_message = '".$facility_id."'");
		DB::statement("CALL database_maintenance_generate_opd_reattendance_register(@opd_reattendance_message)");
		$opd_reattendance_response = DB::select("SELECT @opd_reattendance_message");
		return $opd_reattendance_response[0]->{'@opd_reattendance_message'} != 0 ? 'Process Completed': 'Procee failed';			
	}
	
	public static function setAdmissions($facility_id){
		DB::statement("SET @ipd_admission_message = '".$facility_id."'");
		DB::statement("CALL database_maintenance_generate_admission_register(@ipd_admission_message)");
		$ipd_admission_response = DB::select("SELECT @ipd_admission_message");
		return $ipd_admission_response[0]->{'@ipd_admission_message'} != 0 ? 'Process Completed': 'Procee failed';	
	}
	
	public static function setReferrals($facility_id){
        DB::statement("delete from  tbl_outgoing_referral_registers where facility_id='$facility_id' order by id asc");
		//TODO
		
        DB::statement("update tbl_outgoing_referral_registers set total_under_one_month = male_under_one_month+female_under_one_month where facility_id='$facility_id'");
        DB::statement("update tbl_outgoing_referral_registers set total_under_one_year = male_under_one_year+female_under_one_year where facility_id='$facility_id'");
        DB::statement("update tbl_outgoing_referral_registers set total_under_five_year = male_under_five_year+female_under_five_year where facility_id='$facility_id'");
        DB::statement("update tbl_outgoing_referral_registers set total_above_five_under_sixty = male_above_five_under_sixty+female_above_five_under_sixty where facility_id='$facility_id'");
        DB::statement("update tbl_outgoing_referral_registers set total_above_sixty = male_above_sixty+female_above_sixty where facility_id='$facility_id'");
        DB::statement("update tbl_outgoing_referral_registers set total_male = male_under_one_month+male_under_one_year+male_under_five_year+male_above_five_under_sixty+male_above_sixty where facility_id='$facility_id'");
        DB::statement("update tbl_outgoing_referral_registers set total_female=female_under_one_month+female_under_one_year+female_under_five_year+female_above_five_under_sixty+female_above_sixty where facility_id='$facility_id'");
        DB::statement("update tbl_outgoing_referral_registers set grand_total = total_male+total_female where facility_id='$facility_id'");
		return 'Process Completed';
	}
	
	public static function setOpdDiseases($facility_id){
        DB::statement("SET @opd_disease_message = '".$facility_id."'");
		DB::statement("CALL database_maintenance_generate_opd_disease_register(@opd_disease_message)");
		$opd_disease_response = DB::select("SELECT @opd_disease_message");
		return $opd_disease_response[0]->{'@opd_disease_message'} != 0 ? 'Process Completed': 'Procee failed';	
	}
	
	public static function setIpdDiseases($facility_id){
		DB::statement("SET @ipd_disease_message = '".$facility_id."'");
		DB::statement("CALL database_maintenance_generate_ipd_disease_register(@ipd_disease_message)");
		$ipd_disease_response = DB::select("SELECT @ipd_disease_message");
		return $ipd_disease_response[0]->{'@ipd_disease_message'} != 0 ? 'Process Completed': 'Procee failed';	
	}
	
	public function talliedPatient(Request $request){
		DB::statement('update tbl_accounts_numbers set tallied = 1 where patient_id = '.$request['patient_id']);
	}

	
	public static function setRegistrationAttendances($facility_id){
		DB::statement("CALL database_maintenance_generate_opd_attendance_register('$facility_id')");
		return  'Process Completed';
	}
	
	public static function setRegistrationReattendances($facility_id){
		DB::statement("CALL database_maintenance_generate_opd_reattendance_register('$facility_id')");
		return  'Process Completed';	
	}
}