<?php
namespace App\Http\Controllers\registration;
use App\ClinicalServices\Tbl_bills_category;
use App\Clinics\Tbl_clinic_instruction;
use App\Department\Tbl_department;
use App\ClinicalServices\Tbl_referral;
use App\mortuary\Tbl_corpse_admission;
use App\nursing_care\Tbl_payments_category;
use App\Payment_types\Tbl_pay_cat_sub_category;
use App\Residence\Tbl_residence;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Http\Controllers\Controller;
use App\classes\patientRegistration;
use App\classes\ReportsGeneratorPdf;
use App\Patient\Tbl_invoice_line;
use App\Patient\Tbl_encounter_invoice;
use App\Patient\Tbl_accounts_number;
use App\Patient\Tbl_patient;
use App\Patient\Tbl_received_referral;
use App\Patient\Tbl_corpse;
use App\Facility\Tbl_facility;
use DB;
use App\classes\SystemTracking;
use App\ClinicalServices\Tbl_admission;
use App\Trackable;
use \PDF;
use Auth;

use ServiceManager;

class PatientController extends Controller
{

    public function getRegistrationReports(request $request){
        $response = [];
        $facility_id=$request->facility_id;

$sql=DB::SELECT("SELECT facility_code FROM tbl_facilities WHERE id='".$facility_id."'");
$facility_code=$sql[0]->facility_code;

        if(!isset($request->start_date) OR !isset($request->end_date) ){
            $start_date=date('Y-m-01 07:00:00');
            $end_date=date("Y-m-d H:i:s");
        }else{
            $start_date=$request->start_date;
            $end_date=$request->end_date;
        }
    
    $none = array("male_under_one_month"=>0, "female_under_one_month"=>0, "total_under_one_month"=>0, "male_under_one_year"=>0, "female_under_one_year"=>0, "total_under_one_year"=>0, "male_under_five_year"=>0, "female_under_five_year"=>0, "total_under_five_year"=>0, "male_above_five_under_sixty"=>0, "female_above_five_under_sixty"=>0, "total_above_five_under_sixty"=>0, "male_above_sixty"=>0, "female_above_sixty"=>0, "total_above_sixty"=>0, "grand_total_male"=>0, "grand_total_female"=>0, "grand_total"=>0);
    
        $sql_1="SELECT SUM(female_under_one_month) AS female_under_one_month ,SUM(male_under_one_month) AS male_under_one_month,SUM(total_under_one_month) AS total_under_one_month 
          ,SUM(female_under_one_year) AS female_under_one_year
          ,SUM(male_under_one_year) AS  male_under_one_year
          ,SUM(total_under_one_year) AS total_under_one_year
          
         ,SUM(female_under_five_year) AS female_under_five_year
          ,SUM(male_under_five_year) AS male_under_five_year
          ,SUM(total_under_five_year) AS total_under_five_year
          
         ,SUM(female_above_five_under_sixty) AS female_above_five_under_sixty
          ,SUM(male_above_five_under_sixty) AS male_above_five_under_sixty
          ,SUM(total_above_five_under_sixty) AS total_above_five_under_sixty
        
        ,SUM(female_above_sixty) AS female_above_sixty
          ,SUM(male_above_sixty) AS male_above_sixty
          ,SUM(total_above_sixty) AS total_above_sixty
          
        ,SUM(total_female) AS grand_total_female
          ,SUM(total_male) AS grand_total_male
          ,SUM(grand_total) AS grand_total
        
        
         FROM `tbl_patient_registration_reports` WHERE facility_code='{$facility_code}' AND (date BETWEEN  date('$start_date') AND date('$end_date')) GROUP BY facility_code";
        
    $record_1 = DB::select($sql_1);
    if(count($record_1) > 0)
      $response[] = $record_1;
    else
      $response[] = array($none);


        $sql_2="SELECT SUM(female_under_one_month) AS female_under_one_month ,
          SUM(male_under_one_month) AS male_under_one_month,
          SUM(total_under_one_month) AS total_under_one_month 

          ,SUM(female_under_one_year) AS female_under_one_year
          ,SUM(male_under_one_year) AS  male_under_one_year
          ,SUM(total_under_one_year) AS total_under_one_year
          
          ,SUM(female_under_five_year) AS female_under_five_year
          ,SUM(male_under_five_year) AS male_under_five_year
          ,SUM(total_under_five_year) AS total_under_five_year
          
          ,SUM(female_above_five_under_sixty) AS female_above_five_under_sixty
          ,SUM(male_above_five_under_sixty) AS male_above_five_under_sixty
          ,SUM(total_above_five_under_sixty) AS total_above_five_under_sixty
        
          ,SUM(female_above_sixty) AS female_above_sixty
          ,SUM(male_above_sixty) AS male_above_sixty
          ,SUM(total_above_sixty) AS total_above_sixty
          
          ,SUM(total_female) AS grand_total_female
          ,SUM(total_male) AS grand_total_male
          ,SUM(grand_total) AS grand_total
        
        
         FROM `tbl_reatend_patient_reports` WHERE facility_code='{$facility_code}' AND (date BETWEEN  date('$start_date') AND date('$end_date')) GROUP BY facility_code";
         $record_2 = DB::select($sql_2);
    if(count($record_2) > 0)
      $response[] = $record_2;
    else
      $response[] = array($none);

    return $response;

    }

     public function getMahudhurioByArea(Request $request){
        $facility_id=$request->facility_id;
        if(!isset($request->start_date))
            $start_date=date('Y-m-01 00:00:00');
        else
			$start_date = $request->start_date;
		
		if(!isset($request->end_date))
			$end_date=date("Y-m-d H:i:s");
		else
			$end_date = $request->end_date;
		
		$response[] = DB::SELECT("SELECT residence_name, 
				IFNULL(SUM(case when gender= 'MALE' AND timestampdiff(month,tbl_patients.dob, current_date) < 1 then 1 else 0 end),0) as male_under_one_month,
				IFNULL(SUM(case when gender= 'FEMALE' AND timestampdiff(month,tbl_patients.dob, current_date) < 1 then 1 else 0 end),0) as female_under_one_month,
				IFNULL(SUM(case when gender= 'MALE' AND timestampdiff(month,tbl_patients.dob, current_date) between 1 and 11 then 1 else 0 end),0) as male_under_one_year,
				IFNULL(SUM(case when gender= 'FEMALE' AND timestampdiff(month,tbl_patients.dob, current_date) between 1 and 11 then 1 else 0 end),0) as female_under_one_year,
				IFNULL(SUM(case when gender= 'MALE' AND timestampdiff(month,tbl_patients.dob, current_date) between 12 and 59 then 1 else 0 end),0) as male_under_five_year,
				IFNULL(SUM(case when gender= 'FEMALE' AND timestampdiff(month,tbl_patients.dob, current_date) between 12 and 59 then 1 else 0 end),0) as female_under_five_year,
				IFNULL(SUM(case when gender= 'MALE' AND timestampdiff(month,tbl_patients.dob, current_date) between 60 and 719 then 1 else 0 end),0) as male_above_five_under_sixty,
				IFNULL(SUM(case when gender= 'FEMALE' AND timestampdiff(month,tbl_patients.dob, current_date) between 60 and 719 then 1 else 0 end),0) as female_above_five_under_sixty,
				IFNULL(SUM(case when gender= 'MALE' AND timestampdiff(month,tbl_patients.dob, current_date) >= 720 then 1 else 0 end),0) as male_above_sixty,
				IFNULL(SUM(case when gender= 'FEMALE' AND timestampdiff(month,tbl_patients.dob, current_date) >= 720 then 1 else 0 end),0) as female_above_sixty, count(*) AS grand_total FROM tbl_patients join tbl_residences on tbl_patients.residence_id = tbl_residences.id where tbl_patients.created_at between '$start_date' and '$end_date' and tbl_patients.facility_id='$facility_id' GROUP BY residence_name ORDER BY tbl_residences.council_id, tbl_residences.residence_name ASC");
		
		$response[] = DB::SELECT("SELECT count(*) AS grand_total FROM tbl_patients where tbl_patients.created_at between '$start_date' and '$end_date' and tbl_patients.facility_id='$facility_id'");

		return  $response;
    }
     public function getMahudhurioChfByArea(Request $request){
        $facility_id=$request->facility_id;
        if(!isset($request->start_date))
            $start_date=date('Y-m-01 00:00:00');
        else
			$start_date = $request->start_date;

		if(!isset($request->end_date))
			$end_date=date("Y-m-d H:i:s");
		else
			$end_date = $request->end_date;

		$response[] = DB::SELECT("SELECT residence_name, 
				IFNULL(SUM(case when tbl_patients.gender= 'MALE' AND timestampdiff(month,tbl_patients.dob, current_date) < 1 then 1 else 0 end),0) as male_under_one_month,
				IFNULL(SUM(case when tbl_patients.gender= 'FEMALE' AND timestampdiff(month,tbl_patients.dob, current_date) < 1 then 1 else 0 end),0) as female_under_one_month,
				IFNULL(SUM(case when tbl_patients.gender= 'MALE' AND timestampdiff(month,tbl_patients.dob, current_date) between 1 and 11 then 1 else 0 end),0) as male_under_one_year,
				IFNULL(SUM(case when tbl_patients.gender= 'FEMALE' AND timestampdiff(month,tbl_patients.dob, current_date) between 1 and 11 then 1 else 0 end),0) as female_under_one_year,
				IFNULL(SUM(case when tbl_patients.gender= 'MALE' AND timestampdiff(month,tbl_patients.dob, current_date) between 12 and 59 then 1 else 0 end),0) as male_under_five_year,
				IFNULL(SUM(case when tbl_patients.gender= 'FEMALE' AND timestampdiff(month,tbl_patients.dob, current_date) between 12 and 59 then 1 else 0 end),0) as female_under_five_year,
				IFNULL(SUM(case when tbl_patients.gender= 'MALE' AND timestampdiff(month,tbl_patients.dob, current_date) between 60 and 719 then 1 else 0 end),0) as male_above_five_under_sixty,
				IFNULL(SUM(case when tbl_patients.gender= 'FEMALE' AND timestampdiff(month,tbl_patients.dob, current_date) between 60 and 719 then 1 else 0 end),0) as female_above_five_under_sixty,
				IFNULL(SUM(case when tbl_patients.gender= 'MALE' AND timestampdiff(month,tbl_patients.dob, current_date) >= 720 then 1 else 0 end),0) as male_above_sixty,
				IFNULL(SUM(case when tbl_patients.gender= 'FEMALE' AND timestampdiff(month,tbl_patients.dob, current_date) >= 720 then 1 else 0 end),0) as female_above_sixty, count(*) AS grand_total FROM tbl_patients 
				join tbl_residences on tbl_patients.residence_id = tbl_residences.id
				join tbl_accounts_numbers on tbl_patients.id = tbl_accounts_numbers.patient_id
				 where tbl_accounts_numbers.sub_category_name like '%chf%' AND tbl_accounts_numbers.created_at between '$start_date' and '$end_date' and tbl_patients.facility_id='$facility_id' GROUP BY residence_name ORDER BY tbl_residences.council_id, tbl_residences.residence_name ASC");

		$response[] = DB::SELECT("SELECT count(*) AS grand_total FROM tbl_patients join tbl_accounts_numbers on tbl_patients.id = tbl_accounts_numbers.patient_id where  tbl_accounts_numbers.sub_category_name like '%chf%' AND tbl_accounts_numbers.created_at between '$start_date' and '$end_date' and tbl_patients.facility_id='$facility_id'");

		return  $response;
    }

     public function getMahudhurioByCategory(Request $request){
        $facility_id=$request->facility_id;
        if(!isset($request->start_date))
            $start_date=date('Y-m-01 00:00:00');
        else
			$start_date = $request->start_date;

		if(!isset($request->end_date))
			$end_date=date("Y-m-d H:i:s");
		else
			$end_date = $request->end_date;
         $response[] = DB::SELECT("SELECT sub_category_name as category, 
				IFNULL(SUM(case when gender= 'MALE' AND timestampdiff(month,tbl_patients.dob, current_date) < 1 then 1 else 0 end),0) as male_under_one_month,
				IFNULL(SUM(case when gender= 'FEMALE' AND timestampdiff(month,tbl_patients.dob, current_date) < 1 then 1 else 0 end),0) as female_under_one_month,
				IFNULL(SUM(case when gender= 'MALE' AND timestampdiff(month,tbl_patients.dob, current_date) between 1 and 11 then 1 else 0 end),0) as male_under_one_year,
				IFNULL(SUM(case when gender= 'FEMALE' AND timestampdiff(month,tbl_patients.dob, current_date) between 1 and 11 then 1 else 0 end),0) as female_under_one_year,
				IFNULL(SUM(case when gender= 'MALE' AND timestampdiff(month,tbl_patients.dob, current_date) between 12 and 59 then 1 else 0 end),0) as male_under_five_year,
				IFNULL(SUM(case when gender= 'FEMALE' AND timestampdiff(month,tbl_patients.dob, current_date) between 12 and 59 then 1 else 0 end),0) as female_under_five_year,
				IFNULL(SUM(case when gender= 'MALE' AND timestampdiff(month,tbl_patients.dob, current_date) between 60 and 719 then 1 else 0 end),0) as male_above_five_under_sixty,
				IFNULL(SUM(case when gender= 'FEMALE' AND timestampdiff(month,tbl_patients.dob, current_date) between 60 and 719 then 1 else 0 end),0) as female_above_five_under_sixty,
				IFNULL(SUM(case when gender= 'MALE' AND timestampdiff(month,tbl_patients.dob, current_date) >= 720 then 1 else 0 end),0) as male_above_sixty,
				IFNULL(SUM(case when gender= 'FEMALE' AND timestampdiff(month,tbl_patients.dob, current_date) >= 720 then 1 else 0 end),0) as female_above_sixty, count(*) AS grand_total FROM tbl_patients join tbl_accounts_numbers on tbl_patients.id = tbl_accounts_numbers.patient_id where tbl_accounts_numbers.created_at between '$start_date' and '$end_date' and tbl_accounts_numbers.facility_id='$facility_id' GROUP BY sub_category_name ORDER BY sub_category_name ASC");
		$response[] = DB::SELECT("SELECT count(*) AS grand_total FROM tbl_accounts_numbers where tbl_accounts_numbers.created_at between '$start_date' and '$end_date' and tbl_accounts_numbers.facility_id='$facility_id'");
		return  $response;
    }
     public function getMahudhurioByNationality(Request $request){
        $facility_id=$request->facility_id;
        if(!isset($request->start_date))
            $start_date=date('Y-m-01 00:00:00');
        else
			$start_date = $request->start_date;

		if(!isset($request->end_date))
			$end_date=date("Y-m-d H:i:s");
		else
			$end_date = $request->end_date;
         $response[] = DB::SELECT("SELECT country_name as category, 
				IFNULL(SUM(case when gender= 'MALE' AND timestampdiff(month,tbl_patients.dob, current_date) < 1 then 1 else 0 end),0) as male_under_one_month,
				IFNULL(SUM(case when gender= 'FEMALE' AND timestampdiff(month,tbl_patients.dob, current_date) < 1 then 1 else 0 end),0) as female_under_one_month,
				IFNULL(SUM(case when gender= 'MALE' AND timestampdiff(month,tbl_patients.dob, current_date) between 1 and 11 then 1 else 0 end),0) as male_under_one_year,
				IFNULL(SUM(case when gender= 'FEMALE' AND timestampdiff(month,tbl_patients.dob, current_date) between 1 and 11 then 1 else 0 end),0) as female_under_one_year,
				IFNULL(SUM(case when gender= 'MALE' AND timestampdiff(month,tbl_patients.dob, current_date) between 12 and 59 then 1 else 0 end),0) as male_under_five_year,
				IFNULL(SUM(case when gender= 'FEMALE' AND timestampdiff(month,tbl_patients.dob, current_date) between 12 and 59 then 1 else 0 end),0) as female_under_five_year,
				IFNULL(SUM(case when gender= 'MALE' AND timestampdiff(month,tbl_patients.dob, current_date) between 60 and 719 then 1 else 0 end),0) as male_above_five_under_sixty,
				IFNULL(SUM(case when gender= 'FEMALE' AND timestampdiff(month,tbl_patients.dob, current_date) between 60 and 719 then 1 else 0 end),0) as female_above_five_under_sixty,
				IFNULL(SUM(case when gender= 'MALE' AND timestampdiff(month,tbl_patients.dob, current_date) >= 720 then 1 else 0 end),0) as male_above_sixty,
				IFNULL(SUM(case when gender= 'FEMALE' AND timestampdiff(month,tbl_patients.dob, current_date) >= 720 then 1 else 0 end),0) as female_above_sixty, count(*) AS grand_total FROM tbl_patients left join tbl_countries on tbl_countries.id = tbl_patients.country_id where tbl_patients.created_at between '$start_date' and '$end_date' and tbl_patients.facility_id='$facility_id' GROUP BY country_name ORDER BY country_name ASC");
		$response[] = DB::SELECT("SELECT count(*) AS grand_total FROM tbl_patients where tbl_patients.created_at between '$start_date' and '$end_date' and tbl_patients.facility_id='$facility_id'");
		return  $response;
    }

    public function getClinic(Request $request){

        return Tbl_department::where('id','>',7)->get();

    }
	
	public function usersReports(Request $request){

    $fromDate = $request->input('start_date');
    $toDate = $request->input('end_date');
    $sortBy = $request->input('sort_by');		
       return  ReportsGeneratorPdf::displayReport($fromDate,$toDate,$sortBy);
    }

	 public function  pdfview(Request $request){
        $users = DB::table("users")->get();
        view()->share('users',$users);
		$html="My name is: <h1>John Doe</h1>";

        //if($request->has('download')) {
        	// pass view file
            $pdf = PDF::loadView('pdfile');
            // download pdf
            return $pdf->download('userlist.pdf');
        //}
        //return $html;
    }
	
    public function patientsResidents(Request $request)
    {
        $name = $request['name'];
        $patients = DB::table('tbl_residences')
            ->where('residence_name', 'like', '%' . $name . '%')
            ->select('residence_name', 'id as residence_id', 'id', DB::Raw("'' as council_name"))
            ->limit(25)
            ->get();
        return $patients;
    }
    public function getTribes(Request $request)
    {
        $name = $request['search'];
        $patients = DB::table('tbl_tribes')
            ->where('tribe_name', 'like', '%' . $name . '%')
            ->limit(10)
            ->get();
        return $patients;
    }

    public function getSeachedPatients(Request $request){
        return patientRegistration::seachForPatients($request);

    } 

	public function getSeachedCorpses(Request $request){
        return patientRegistration::getSeachedCorpses($request);

    }
    public function getSeachedInsuarancePatients(Request $request){
        return patientRegistration::seachForInsuarancePatients($request);

    }
    public function getMaritalStatus(Request $request){
        return patientRegistration::getMaritalStatus($request);

    }

    public function getTribe(Request $request){
        return patientRegistration::getTribe($request);

    }

    public function getOccupation(Request $request){
        return patientRegistration::getOccupation($request);
    }

    public function getCountry(Request $request){
        return patientRegistration::getCountry($request);
    }

    public function getRelationships(Request $request){
        return patientRegistration::getRelationships($request);
    }

    public function getInsurances()
    {
        return Tbl_pay_cat_sub_category::where('pay_cat_id',2)->get();
    }

    public function searchResidences(Request $request)
    {
        $searchKey = $request->input('searchKey');
        $residences=DB::table('vw_residences')
            ->where('residence_name','like','%'.$searchKey.'%')
            ->get();
        return $residences;
    }


    public function printLastVisit(Request $request)
    {
        $facility_id=$request->input('facility_id');
        $patient_id=$request->input('patient_id');
        return patientRegistration::getLastVisit($facility_id,$patient_id);
    }


    public function searchPatientCategory($facility_id)
    {
         $patientCategory=DB::table('vw_registrar_services')
            ->where('facility_id',$facility_id)
            ->groupBy('patient_category')
            ->get();
        return $patientCategory;
    } 

	public function corpseEdit(Request $request){
		
		   $dataArray=['middle_name'=>$request->middle_name,'last_name'=>$request->last_name,'gender'=>$request->gender,'dob'=>$request->dob,'dod'=>$request->dod];
		   
		        
				$first_name=$request->first_name;
				$middle_name=$request->middle_name;
				$last_name=$request->last_name;
				$gender=$request->gender;
				$dod=$request->dod;
				$dob=$request->dob;
				$user_id=$request->user_id;
				
		   
		       $edit_corpse = Tbl_corpse::find($request->corpse_id);
 
               $edit_corpse->first_name = $first_name;
               $edit_corpse->middle_name = $middle_name;
               $edit_corpse->last_name = $last_name;
               $edit_corpse->gender = $gender;
               $edit_corpse->dob = $dob;
               $edit_corpse->dod = $dod;
               $edit_corpse->user_id = $user_id;
			   $edit_corpse->save();
			   
			    return response()->json([
                'data' =>'New values for '. $first_name.' '.$middle_name.' '.$last_name.' ,was successfully changed',
                'json' => $edit_corpse,
                'status' => 1
            ]);
		
       
    }
	
	 public function getPatientsToEdit(Request $request)
    {
		//MMEZIDI KUBADILI BADILI ,MNABOA INATAKIWA SEARCH IFANYIKE KTK VIEW
		// pole shekhe,,,this is problem solving
		
		
		// search directed on table to solve speed problem.
		// the search_field column is added to avoid the OR operations...
		// the colum is added by sql update and the field is populated using a trigger
        
		$name = $request['name'];
		$patients="SELECT * FROM tbl_patients WHERE search_field LIKE '%".preg_replace("/\s+/","",$name)."%'";
		
		return DB::SELECT($patients);
		
       
    }

     public function getPatientsToEncounter(Request $request)
    {
        $name = $request['name'];
		$patients="SELECT * FROM tbl_patients WHERE search_field LIKE '%".preg_replace("/\s+/","",$name)."%' LIMIT 20";
        return DB::SELECT($patients);


    }

    public function getPricedItems(Request $request){
		$patient_category=$request->patient_category;
		$facility_id=$request->facility_id;
//        $sql = "select * from vw_registrar_services where patient_category = '".$patient_category."' AND facility_id = '".$facility_id."'
//         AND status = 1 GROUP BY item_name ";
//        $getPricedItems = DB::select(DB::raw($sql));
        $getPricedItems=DB::table('vw_registrar_services')
            ->where('vw_registrar_services.status','=', 1)
            ->where('patient_category',$patient_category)
            ->where('facility_id',$facility_id)
			->groupBy('item_name')
            ->get();
        return $getPricedItems;

    }


    public function searchPatientServices(Request $request)
    {
        $searchKey = $request->input('item_name');
        $patient_category = $request->input('patient_category');
        $facility_id = $request->input('facility_id');

        $searchPatientServices=DB::table('vw_registrar_services')
            ->where('item_name','like','%'.$searchKey.'%')
            ->where('patient_category',$patient_category)
            ->where('facility_id',$facility_id)
            ->get();
        return $searchPatientServices;
    }

    public function quick_registration(Request $request, $updating=false)
    {   
		//if(isset($request['id']))
		//   $updating = true;
		//check if duplicate
		if($request->input('confirmedNotDuplicate', 1)  != 1)
			if($this->duplicate($request, $updating)){
				$duplicate = Tbl_patient::where("first_name",$request['first_name'])
										->where("middle_name",$request['middle_name'])
										->where("last_name",$request['last_name'])
										->where("gender",$request['gender'])
										->where("dob",$request['dob'])
										->where("residence_id",$request['residence_id'])
										->where("tribe_id",$request['tribe'])
										->orderBy("id","desc")
										->get();
				return response()->json(['status'=>'duplicate', 'mrn'=>$duplicate[0]->medical_record_number]);
			}
		
		foreach($request->all() as $key=>$value)
            $request[$key] = strtoupper($value);
        $genders=array('MALE','FEMALE');

        $facility_id=$request->input('facility_id');
        $gender=$request->input('gender');
        $mobile_number=$request->input('mobile_number');
        $residence_id=$request->input('residence_id');
        $dob=$request->input('dob');
        $tribe=$request->input('tribe');
        $mobile_pattern='#^[0][6-7][1-9][0-9][0-9]{6}$#';
        // return patientRegistration::calculatePatientAge($request);

        $pattern='#^(19[0-9][0-9])|(20[0-9][0-9])-((0[1-9])|(1[0-2]))-((0[1-9])|([1-2][0-9])|(3[0-1]))$#';


        if(!in_array($gender,$genders)){

            return response()->json([
                'data' => 'Please Select Gender!',
                'status' => '0'
            ]);
        }
        else if (0 === preg_match($mobile_pattern, $mobile_number) AND !empty($mobile_number)) {

            return response()->json([
                'data' => 'INVALID MOBILE NUMBER',
                'status' => '0'
            ]);
        }

        else if (!isset($residence_id)) {

            return response()->json([
                'data' => 'PLEASE ENTER PATIENT RESIDENCE',
                'status' => '0'
            ]);
        }
        else if (0 === preg_match($pattern, $dob)) {

            return response()->json([
                'data' => 'Invalid Date of Birth',
                'status' => '0'
            ]);
        }
        else {
             return  patientRegistration::patient_registration($request);
        }


    }

    public function insuaranceRegistration(Request $request)
    {    //return $request->all();
     //   foreach($request->all() as $key=>$value)
          //  $request[$key] = strtoupper($value);
			
		 $gender=strtoupper($request->input('gender'));	
		 $gender=strtoupper($gender);	
		  $genders=array('MALE','FEMALE');

        $facility_id=$request->input('facility_id');
        $mobile_number=$request->input('mobile_number');
        $residence_id=$request->input('residence_id');
        $dob=$request->input('dob');
        $mobile_pattern='#^[0][6-7][1-9][0-9][0-9]{6}$#';
        // return patientRegistration::calculatePatientAge($request);

        $pattern='#^(19[0-9][0-9])|(20[0-9][0-9])-((0[1-9])|(1[0-2]))-((0[1-9])|([1-2][0-9])|(3[0-1]))$#';


        if(!in_array($gender,$genders)){

            return response()->json([
                'data' => 'Please Select Gender!',
                'status' => '0'
            ]);
        }
        else if (0 === preg_match($mobile_pattern, $mobile_number) AND !empty($mobile_number)) {

            return response()->json([
                'data' => 'INVALID MOBILE NUMBER',
                'status' => '0'
            ]);
        }

        else if (!isset($residence_id)) {

		     return response()->json([
                'data' => 'PLEASE ENTER PATIENT RESIDENCE',
                'status' => '0'
            ]);
        }
        else if (0 === preg_match($pattern, $dob)) {

            return response()->json([
                'data' => 'Invalid Date of Birth',
                'status' => '0'
            ]);
        }
        else {

            return patientRegistration::patient_registration_insuarance($request);

        }


    }

	public function getMortuaryServices(Request $request){
		$sql="SELECT * FROM vw_shop_items t1 WHERE t1.dept_id=7 AND 
		t1.facility_id='".$request->facility_id."'  
		AND t1.patient_main_category_id=1 GROUP BY t1.item_id";
		
		return DB::SELECT($sql);
		
		
	}
	
	public function giveService(Request $request){		
		$encounter=Tbl_encounter_invoice::create($request->all());
		$invoice_id=$encounter->id;
		
		$item_list = new Tbl_invoice_line($request->all());
        $item_list['invoice_id'] =$invoice_id;
        if($item_list->save()){
			 return response()->json([
                'data' => 'Corpse was successfully registered',
                'status' => 1
            ]);
		}
	     }
	
	
	

    public function corpse_registration(Request $request)
    {   // return $request->input('facility_id');

        $description='';
        $death_condition='';
        $corpse_properties='';
        $names='';

        foreach($request->corpse_details as $key=>$value)

            $request[$key] = strtoupper($value);
        $genders=array('MALE','FEMALE');
         if (isset($request->corpse['description'])) {
            $description=$request->corpse['description'];
             }
             if (isset($request->corpse_details['death_condition'])) {
            $death_condition=$request->corpse_details['death_condition'];
             }
             if (isset($request->corpse['corpse_properties'])) {
            $corpse_properties=$request->corpse['corpse_properties'];
             }
             if (isset($request->corpse_details['names'])) {
            $names=$request->corpse_details['names'];
             }
        $facility_id=$request->input('facility_id');
        $gender=$request->input('gender');
        $mobile_number=$request->input('mobile_number');
        $first_name=$request->input('first_name');
        $middle_name=$request->input('middle_name');
        $last_name=$request->input('last_name');
        $residence_id=$request->input('residence_id');
        $death_condition=$request->input('death_condition');

        $dob=$request->input('dob');
        $dod=$request->input('dod');
        $time = strtotime($dob);
        $time_2 = strtotime($dod);
        $dob = date('Y-m-d',$time);
        $dod = date('Y-m-d',$time_2);
      
        $user_id=$request->input('user_id');
        $mobile_pattern='#^[0][6-7][1-9][0-9][0-9]{6}$#';
       
        $pattern='#^(19[0-9][0-9])|(20[0-9][0-9])-((0[1-9])|(1[0-2]))-((0[1-9])|([1-2][0-9])|(3[0-1]))$#';


        if(!in_array($gender,$genders)){

            return response()->json([
                'data' => 'Please Select Gender!',
                'status' => '0'
            ]);
        }
        else if (0 === preg_match($mobile_pattern, $mobile_number) AND !empty($mobile_number)) {

            return response()->json([
                'data' => 'INVALID MOBILE NUMBER',
                'status' => '0'
            ]);
        }

        else if (!is_numeric($residence_id)) {

            return response()->json([
                'data' => 'PLEASE ENTER CORPSE RESIDENCE',
                'status' => '0'
            ]);
        }
        else if (0 === preg_match($pattern, $dob)) {

            return response()->json([
                'data' => 'Invalid Date of Birth',
                'status' => '0'
            ]);
        } 
		
		
		
		
        else {
			$mobile_number_supporter='';
			$names='';
			$relationship='';
			$storage_reason='';
			$vehicle_number=''; 
			if (!isset($request->corpse['names'])) {			  
                  return response()->json([
                'data' => 'Name of the supporter/relative is needed',
                'status' => 0
            ]);    
             }
			 
			 if (!isset($request->corpse['vehicle_number'])) {			  
                  return response()->json([
                'data' => 'Vehicle/Transport details is needed',
                'status' => 0
            ]);    
             } 
			 
			 if (!isset($request->corpse['storage_reason'])) {			  
                  return response()->json([
                'data' => 'Storage Reasons is needed',
                'status' => 0
            ]);    
             }
			 
			$mobile_number_supporter=$request->corpse['mobile_number'];
            $names=$request->corpse['names'];
            $relationship=$request->corpse['relationship'];
            $storage_reason=$request->corpse['storage_reason'];
            $vehicle_number=$request->corpse['vehicle_number'];  
             
			
			 
			 
$registrationInfos=['gender'=>$gender,
				    'dob'=>$dob,
				    'dod'=>$dod,
				    'first_name'=>$first_name,
				    'middle_name'=>$middle_name,
				    'last_name'=>$last_name,
				    'residence_id'=>$residence_id,
				    'residence_found'=>$request->whereFoundId,
				    'country_id'=>$request->country_id,
				    'facility_id'=>$facility_id,
				    'mobile_number'=>$mobile_number_supporter,
				    'transport'=>$vehicle_number,
				    'storage_reason'=>$storage_reason,
				    'relationship'=>$relationship,
				    'corpse_brought_by'=>$names,
				    'description'=>$description,
				    'corpse_conditions'=>$death_condition,
				    'corpse_properties'=>$corpse_properties,
				    'corpse_properties_given_to'=>$names,
				    'diagnosis_id'=>null,
				    'diagnosis_code'=>null,
				    'user_id'=>$user_id];
					
// return $registrationInfos;
			 
            return patientRegistration::corpsesNumber($registrationInfos);

        }


    } 
	
	public function corpseTaker(Request $request){  
	
	 $description='';
	
        foreach($request->all() as $key=>$value)
            $request[$key] = strtoupper($value);
         
        $mobile_number=$request->input('mobile_number');
        $corpseTakerName=$request->input('corpseTakerName');
        $vehicle_number=$request->input('vehicle_number');
        $identityNumber=$request->input('identityNumber');
        $identityType=$request->input('identityType');
        $corpseID=$request->input('corpseID');
        $funeralSiteId=$request->input('funeralSiteId');
        $residenceCorpseTakerId=$request->input('residenceCorpseTakerId');
        $user_id=$request->input('user_id');
        $mobile_pattern='#^[0][6-7][1-9][0-9][0-9]{6}$#';
       
        $pattern='#^(19[0-9][0-9])|(20[0-9][0-9])-((0[1-9])|(1[0-2]))-((0[1-9])|([1-2][0-9])|(3[0-1]))$#';


        if (0 === preg_match($mobile_pattern, $mobile_number) AND !empty($mobile_number)) {

            return response()->json([
                'data' => 'INVALID MOBILE NUMBER',
                'status' => '0'
            ]);
        }

        else if (!is_numeric($residenceCorpseTakerId)) {

            return response()->json([
                'data' => 'PLEASE ENTER RESIDENCE WHERE CORPSE TAKER LIVE',
                'status' => '0'
            ]);
        }
		
		else if (!is_numeric($funeralSiteId)) {

            return response()->json([
                'data' => 'PLEASE ENTER PLACE WHERE FUNERAL TAKES PLACE',
                'status' => 0
            ]);
        }
       	
        else {
			
		$relationship=$request->input('relationship');
		$vehicle_number=$request->input('vehicle_number');
        $identityNumber=$request->input('identityNumber');
        $identityType=$request->input('identityType');
        $corpseID=$request->input('corpseID');
        $funeralSiteId=$request->input('funeralSiteId');
        $residenceCorpseTakerId=$request->input('residenceCorpseTakerId');
         
        $registrationInfos=[
				    'corpse_taken_by'=>$corpseTakerName,
				    'identity_number_taker'=>$identityNumber,
				    'identity_type_taker'=>$identityType,
				    'funeral_site_id'=>$funeralSiteId,
				    'residence_taker'=>$residenceCorpseTakerId,
				    'mobile_number'=>$mobile_number,
				    'transport_taking'=>$vehicle_number,
				    'relationship_taker'=>$relationship,
				    'discharge_info_by'=>$user_id];
				 
       $corpses=Tbl_corpse::where('id',$corpseID)->update($registrationInfos);

	   
	    return response()->json([
                'data' => 'Discharge Notes Successfully Saved',
                'status' => 1
            ]);
        }


    }

    public function authorizeCardFromMember(Request $request){
        $CardNo=$request->cardNo;
		$ReferralNo=0;
         $VisitTypeID=$request->VisitTypeID;
		$patient_id="";
     	if(isset($request->patient_id)){
			   $patient_id=$request->patient_id;	
		}
		
		if(isset($request->ReferralNo)){
			   $ReferralNo=$request->ReferralNo;
       	
		}
		
		$manager = new ServiceManager();
        return $manager->AuthorizeCard($CardNo,$VisitTypeID,$ReferralNo,$patient_id);
    }

    public function getMortuary(Request $request)  {
        $searchKey = $request->input('searchKey');
        $sql="SELECT * FROM vw_shop_items t1,tbl_mortuaries t2 WHERE t2.mortuary_class_id=t1.item_id AND t1.item_category='MORTUARY'";
        $getMortuaryClass=  DB::SELECT($sql);
        return $getMortuaryClass;
    }

    public function saveCorpseFromOutsideFacility(Request $request)  {
        if(Tbl_corpse_admission::create($request->all())){
            return response()->json([
                'data' => 'Corpse was Successfully Saved',
                'status' => 1
            ]);
        }

    }


    public function getPatientRegistrationStatus(request $request){
        $patient_id=$request->patient_id;
        $residence_id=$request->residence_id;
        $facility_id=$request->facility_id;
        $responses=[];
        $profileStatus=Tbl_patient::where('id',$patient_id)
            ->where('occupation_id',null)
            ->get();
        $responses[]=count($profileStatus);
        $responses[]=Tbl_accounts_number::Where('patient_id',$patient_id)->orderBy('id','DESC')->take(1)->get();
		$reattendance = DB::select("select case when t1.main_category_id = 1 and timestampdiff(day,t1.date_attended,current_date) <= t2.days then true else false end as status,t2.days  FROM tbl_accounts_numbers t1 join tbl_reattendance_free_days t2 on t1.patient_id = '$patient_id' and t1.facility_id = t2.facility_id and main_category_id IS NOT NULL order by t1.id desc limit 1");
		if(count($responses[1]) > 0 && count($reattendance) == 0)
			$responses[1][0]->qualifiesFreeReattendance =  false;
		elseif(count($responses[1]) > 0 && count($reattendance) > 0){
			$responses[1][0]->qualifiesFreeReattendance =  $reattendance[0]->status;
			$responses[1][0]->days =  $reattendance[0]->days;


		}else
			$responses[1][] = ["qualifiesFreeReattendance"=>false];

        $responses[]=DB::table('tbl_residences')->where('id',$residence_id)->get();
        $responses[]=patientRegistration::getLastVisit($facility_id,$patient_id);
        $occ=Tbl_patient::where('id',$patient_id)
            ->where('occupation_id',"!=",null)
            ->take(1)
            ->get();
        if(count($occ)>0){
            $occupation_id=$occ[0]->occupation_id;
               $responses[]=DB::table('tbl_occupations')->select("occupation_name")->where('id',$occupation_id)->get();;
     }


        return $responses;

    }



    public function full_registration(Request $request){   // return $request->input('facility_id');

        foreach($request->all() as $key=>$value)
            $request[$key] = strtoupper($value);
        $genders=array('MALE','FEMALE');
        $facility_id=$request->input('facility_id');
        $first_name=$request->input('first_name');
        $gender=$request->input('gender');
        $mobile_number=$request->input('mobile_number');
        $residence_id=$request->input('residence_id');
        $dob=$request->input('dob');
        $marital_status=$request->input('marital_status');
        $occupation=$request->input('occupation_id');
        $tribe=$request->input('tribe_id');
        $country=$request->input('country_id');
        $next_of_kin_name=$request->input('next_of_kin_name');
        $next_of_kin_resedence_id=$request->input('next_of_kin_resedence_id');
        $relationship=$request->input('relationship');
        $mobile_number_next_kin=$request->input('mobile_number_next_kin');
        $mobile_pattern='#^[0][6-7][0-9][0-9][0-9]{6}$#';
        // return patientRegistration::calculatePatientAge($request);

        $pattern='#^(19[0-9][0-9])|(20[0-9][0-9])-((0[1-9])|(1[0-2]))-((0[1-9])|([1-2][0-9])|(3[0-1]))$#';


        if(!preg_match("/^[a-zA-Z'-]+$/",$first_name)) {

            return response()->json([
                'data' => 'Please Enter valid First name wth Letters only',
                'status' => '0'
            ]);
        }
        else if(!in_array($gender,$genders)){

            return response()->json([
                'data' => 'Please Select Gender!',
                'status' => '0'
            ]);
        }
        else if (0 === preg_match($mobile_pattern, $mobile_number) AND !empty($mobile_number)) {

            return response()->json([
                'data' => 'INVALID MOBILE NUMBER',
                'status' => '0'
            ]);
        }
        else if (0 === preg_match($mobile_pattern, $mobile_number_next_kin) AND !empty($mobile_number_next_kin)) {

            return response()->json([
                'data' => 'INVALID MOBILE NUMBER FOR NEXT OF KIN',
                'status' => '0'
            ]);
        }

        else if (!is_numeric($residence_id)) {

            return response()->json([
                'data' => 'PLEASE ENTER PATIENT RESIDENCE',
                'status' => '0'
            ]);
        }

        else if (!is_numeric($next_of_kin_resedence_id)) {

            return response()->json([
                'data' => 'PLEASE ENTER NEXT OF KIN RESIDENCE',
                'status' => '0'
            ]);
        }

        else if (!is_numeric($country)) {

            return response()->json([
                'data' => 'PLEASE SELECT COUNTRY FROM THE SUGESTIONS',
                'status' => '0'
            ]);
        }
        else if (0 === preg_match($pattern, $dob)) {

            return response()->json([
                'data' => 'Invalid Date of Birth',
                'status' => '0'
            ]);
        }
        else {
            return patientRegistration::patient_registration($request);

        }


    }

    public function complete_registration(Request $request) {   // return $request->input('facility_id');
	     $response=[];
        foreach($request->all() as $key=>$value)
            $request[$key] = strtoupper($value);
        $genders=array('MALE','FEMALE');
        $facility_id=$request->input('facility_id');
        $residence_id=$request->input('residence_id');
        $patient_id=$request->input('patient_id');
        $first_name=$request->input('first_name');
        $gender=$request->input('gender');
        $user_id=$request->input('user_id');
        $mobile_number=$request->input('mobile_number');
        $dob=$request->input('dob');
        $marital_status=$request->input('marital_status');
        $occupation=$request->input('occupation_id');
        if($occupation==''){
            $occupation=null;
        }
        if($marital_status==''){
            $marital_status=null;
        }
        $country=$request->input('country_id');
        $next_of_kin_name=$request->input('next_of_kin_name');
        $next_of_kin_resedence_id=$request->input('next_of_kin_resedence_id');
        $relationship=$request->input('relationship');
        $mobile_number_next_kin=$request->input('mobile_number_next_kin');
        $mobile_pattern='#^[0][6-7][0-9][0-9][0-9]{6}$#';
        // return patientRegistration::calculatePatientAge($request);

        $pattern='#^(19[0-9][0-9])|(20[0-9][0-9])-((0[1-9])|(1[0-2]))-((0[1-9])|([1-2][0-9])|(3[0-1]))$#';

        if (0 === preg_match($mobile_pattern, $mobile_number_next_kin) AND !empty($mobile_number_next_kin)) {

            return response()->json([
                'data' => 'INVALID MOBILE NUMBER FOR NEXT OF KIN',
                'status' => '0'
            ]);
        }

        else if (!isset($next_of_kin_resedence_id)) {

            return response()->json([
                'data' => 'PLEASE ENTER NEXT OF KIN RESIDENCE',
                'status' => '0'
            ]);
        }

        else if (!isset($country)) {

            return response()->json([
                'data' => 'PLEASE SELECT COUNTRY FROM THE SUGESTIONS',
                'status' => '0'
            ]);
        }
        else {
            
            $responses[]=DB::table("tbl_patients")->where('id',$patient_id)->get();
            $responses[]=Tbl_accounts_number::Where('patient_id',$patient_id)->orderBy('id','DESC')->take(1)->get();
            $reattendance = DB::select("select case when t1.main_category_id = 1 and timestampdiff(day,t1.date_attended,current_date) <= t2.days then true else false end as status ,t2.days FROM tbl_accounts_numbers t1 join tbl_reattendance_free_days t2 on t1.patient_id = '$patient_id' and t1.facility_id = t2.facility_id order by t1.id desc limit 1");
			if(count($responses[1]) > 0 && count($reattendance) == 0)
				$responses[1][0]->qualifiesFreeReattendance =  false;
			elseif(count($responses[1]) > 0 && count($reattendance) > 0){
				$responses[1][0]->qualifiesFreeReattendance =  $reattendance[0]->status;
				$responses[1][0]->days =  $reattendance[0]->days;

			}else
				$responses[1][] = ["qualifiesFreeReattendance"=>false];
		
			$responses[]=DB::table('vw_residences')->where('residence_id',$residence_id)->get();
            $responses[]=patientRegistration::getLastVisit($facility_id,$patient_id);
            $response[]=Tbl_patient::where('id',$patient_id)->update(["marital_id"=>$marital_status,"occupation_id"=>$occupation,"country_id"=>$country,"user_id"=>$user_id]);
            return $responses;

        }


    }


    public function getCurrentPatientAccountNumber($patient_id,$facility_id)
    { /**
    $checkif_new_account_numberIsrequired= DB::table('tbl_accounts_numbers')
    ->where('patient_id',$patient_id)
    ->where('facility_id',$facility_id)
    ->orderBy('id','ASC')
    ->take(2)->get();
     **/
        //if(count($checkif_new_account_numberIsrequired)==2){
        //$this->addTodayAccountNumber($facility_id,$patient_id);
        //  }



        $getCurrentPatientAccountNumber = Tbl_accounts_number::
        select('id as account_number_id','account_number')
            ->where('patient_id',$patient_id)
            ->where('facility_id',$facility_id)
            ->orderBy('id','DESC')
            ->first();
        return $getCurrentPatientAccountNumber;
    }

    /**
    public function addTodayAccountNumber(){
    //$facility_id=$request->facility_id;
    //$patient_id=$request->patient_id;
    if(patientRegistration::patientAccountNumber($facility_id,$patient_id)){
    return 'success';

    }

    }
     **/

    public function getPatientInfo($patient_id){

        $getPatientId = Tbl_patient::where('id',$patient_id)
            ->orderBy('id','DESC')
            ->first();
        return $getPatientId;
    }

    public function enterEncounter(Request $request) {
		
	    if (!$request->has('free_reattendance') && !$request->has('service_id')) {
            return response()->json([
                'data' => 'PLEASE SELECT SERVICE FROM THE SUGESTION LIST',
                'status' => '0'
            ]);
        }
		
		
		$facility_id=$request->input('facility_id');
        $patient_id=$request->input('patient_id');
        $user_id=$request->input('user_id');
        $dept_id=$request->input('dept_id');
		
		//may miss on reattendance
		$price_id=$request->input('price_id');
        $service_id=$request->input('service_id');
        $item_type_id=$request->input('item_type_id');
        
        $quantity=1;
        $status_id=1;
        $payment_filter=null;

        if($request->input('main_category_id')!=1){
            $status_id=1;
            $payment_filter=$request->input('payment_filter');
        }
		
		//Melchiory: this logic wa wrongly placed..... You should have returned duplicate before creating account number!
		if(!$request->has('free_reattendance') && patientRegistration::duplicate('tbl_invoice_lines',array('patient_id','item_type_id','quantity',"((timestampdiff(minute,created_at,CURRENT_TIMESTAMP) <=0))"), array($patient_id,$item_type_id,$quantity))==true){

			return response()->json([
				'data' => 'DUPLICATE WAS DETECTED, DO NOT RE-SUBMIT FORM TWICE FOR THE SAME REQUEST. THE REQUEST IS ALREADY SENT',
				'status' => '0'
			]);
		}
		
        
		//Melchiory: added for mtuha purposes that needs dob and gender
		$patient = Tbl_patient::where('id',$patient_id)->get();
		//
		
		patientRegistration::patientAccountNumber($facility_id, $patient_id,$user_id, $patient[0]->gender, $patient[0]->dob);
		
		
		
		
        $account=$this->getCurrentPatientAccountNumber($patient_id,$facility_id);
		$account_number_id=$account->account_number_id;
        $bill_id=$request->input('bill_id');
        $main_category_id=$request->input('main_category_id');
		//Melchiory:Code added to capture referral details
		try{
			if($request->is_referral != 0){
				    $referral = [
              "patient_id"=>$patient_id,
              "visit_id"=>$account->account_number_id,
              "facility_id"=> $facility_id,
              "referring_facility_id"=> $request->referring_facility_id,
              "user_id"=> $user_id,
            ];

            $from_facility_id=$request->referring_facility_id;
            $account_id=$account->account_number_id;
            $user_id=$user_id;
        //save referral
            $patientData=Tbl_patient::where("id",$patient_id)->first();
 $checkdup=Tbl_referral::where("visit_id",$account_id)->where('to_facility_id',$from_facility_id)->get();
 if(count($checkdup)==0){

   $aged=DB::select("select  CASE WHEN TIMESTAMPDIFF(YEAR,t6.dob, CURRENT_DATE) <> 0 THEN CONCAT(TIMESTAMPDIFF(YEAR,t6.dob, CURRENT_DATE), ' Years') ELSE CASE WHEN TIMESTAMPDIFF(MONTH,t6.dob, CURRENT_DATE) <> 0 THEN CONCAT(TIMESTAMPDIFF(MONTH, t6.dob, CURRENT_DATE), ' Months') ELSE CONCAT(TIMESTAMPDIFF(DAY, t6.dob, CURRENT_DATE), ' Days') END END
AS age from tbl_patients t6 where id=$patient_id limit 1");
$payl=Tbl_referral::create([
 'visit_id'=>$account_id,     
'referral_code'=>"INCOMING",      
'referral_type'=>1,      
'status'=>1, 
'patient_id'=>$patient_id,        
 'sender_id'=>$user_id,                                
 'from_facility_id'=>$facility_id,     
 'to_facility_id'=>$from_facility_id,
"referral_date"=>Date("Y-m-d"), 
  "name"=>$patientData->first_name. "".$patientData->middle_name." ".$patientData->last_name,
  "gender"=>$patientData->gender,
  "reg"=>$patientData->medical_record_number,
  "age"=>$aged[0]->age,
]);

}
				Tbl_received_referral::create($referral);
			}
		}catch(Exception $ex){}
		
		if($request->has('free_reattendance')){
			$last_visit = DB::select("select main_category_id,  patient_category_id  FROM tbl_accounts_numbers where patient_id = '$patient_id' and facility_id = '$facility_id' and main_category_id IS NOT NULL order by id desc limit 1");
			 $bill_id=$last_visit[0]->patient_category_id;
			 $main_category_id=$last_visit[0]->main_category_id;
		}
		//section modified to accomodate free reattendances
		$payment_category =Tbl_bills_category::create(['patient_id'=>$patient_id,'account_id'=>$account_number_id,'user_id'=>$user_id,'bill_id'=>$bill_id,'main_category_id'=>$main_category_id]);
		
		if(!$request->has('free_reattendance')){	
			$encounter =Tbl_encounter_invoice::create(array('account_number_id'=>$account_number_id,'facility_id'=>$facility_id,'user_id'=>$user_id));


			if($encounter->save()){
				//Important: For user fee clients, we set this encounter as a paid-for 
				//visit so that we track it x days that the facility allow the patient 
				//to revisit without paying consultation fee again.
				//Note that the free_reattendance flag in the request ensures this
				//value is never set twice as long as the registrar accepts the
				//dialog
				Tbl_accounts_number::where("id",$account_number_id)->update(["paid_attendance"=>true]);
				
				$invoice_line =Tbl_invoice_line::create(
						array('invoice_id'=>$encounter->id,'payment_filter'=>$bill_id,
						'item_type_id'=>$item_type_id,'facility_id'=>$facility_id,'quantity'=>$quantity,'user_id'=>$user_id,'item_price_id'=>$price_id,'status_id'=>$status_id,'discount'=>0,'discount_by'=>$user_id,'patient_id'=>$patient_id)
					);

				$oldData=null;
				$patient_id=$patient_id;
				$trackable_id=$invoice_line->id;
				SystemTracking::Tracking($user_id,$patient_id,$trackable_id,$invoice_line,$oldData);

				if($dept_id>7) {
					$clinic_save = Tbl_clinic_instruction::create(array('received' => 0, 'dept_id' => $dept_id, 'doctor_requesting_id' => $user_id,
					'consultation_id' => $service_id, 'sender_clinic_id' => 1, 'visit_id' => $account_number_id, 'on_off' => 0));
				}
            }
		}//END MODIFY
		
		
		$facility_code=DB::SELECT("SELECT facility_code FROM tbl_facilities t1 WHERE t1.id='".$facility_id."'");
		$facility_code=$facility_code[0]->facility_code;
		//check if reattendance to count..
		$last_created=Tbl_patient::where('id',$patient_id)->get();
		$time_created= $last_created[0]->created_at;
		$gender= $last_created[0]->gender;
		$dob= $last_created[0]->dob;
		$time_created = new \DateTime($time_created);
		$interval = (new \DateTime())->diff($time_created);
		$day = $interval->d + $interval->y*12*30;
		if ($day >0){
			patientRegistration::countReattendance($gender, $dob,$facility_code);
		}else{
			patientRegistration::countNewAttendance($gender, $dob,$facility_code);
		}

	   if($dept_id==14){
			   $account_data = Tbl_accounts_number::
			   where('id', $account_number_id)
				   ->where('patient_id', $patient_id)
				   ->update([
					   'status'=>1
				   ]);
	   }
				
		//Melchiory: only after applying GUID to all users should this code run	
		/*
		
		$department_lists=array(8,15,17,18,19,20,21,22);//special EMR Programs
			
		if(in_array($dept_id,$department_lists)){
			$sql="SELECT t1.* FROM tbl_patients t1 WHERE t1.id='".$patient_id."'";	
			$dataToEMR=DB::SELECT($sql);
				
		return patientRegistration::EMRintegrationAPI($dataToEMR,$account_number_id,$patient_id,$bill_id,$user_id,$dept_id);
								
		 }
		 
		 */

		return response()->json([
				'data' => 'VISIT SUCCESSFULLY STARTED',
				'account_number' =>'Account No: ',
				'status' => '1'
			]);
    }











    public function corpsesNumber($facility_id,$patient_id,$user_id)
    {
        while(true){
            $corpse_number = DB::table('tbl_corpses')
                ->where('facility_id',$facility_id)
                ->orderBy('id','DESC')
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
                $patient = Tbl_corpse::create(array('corpse_record_number'=>$corpse_number,'facility_id'=>$facility_id,'patient_id'=>$patient_id,'user_id'=>$user_id));
                if(!$patient->save())
                    continue;
                return $patient;
            }else{
                continue;
            }
        }
    }




    public function patientAccountNumber($facility_id,$patient_id,$user_id)
    {
        while(true){
            $patient_account_number = DB::table('tbl_accounts_numbers')
                ->where('facility_id',$facility_id)
                ->where('date_attended','LIKE','%'.date('Y-m').'%')
                ->orderBy('id','DESC')
                ->take(1)->get();
            if(count($patient_account_number)>0){
                $CustomerExecute =  $patient_account_number[0]->account_number;
                if(substr($CustomerExecute,6,10) !=date('my')){
                    $account_number  ='000001'.date('my');
                }else{
                    $account_number  =str_pad((substr($CustomerExecute,0,6)+1),6,'0',STR_PAD_LEFT).date('my');
                }
            }else{
                $account_number  ='000001'.date('my');
            }

            $ExecuteQuery = DB::table('tbl_accounts_numbers')
                ->where('account_number',$account_number)
                ->where('facility_id',$facility_id)
                ->where('date_attended','LIKE', date('Y-m').'%')
                ->count();
            if($ExecuteQuery ==0){
                $patient = Tbl_accounts_number::create(array('account_number'=>$account_number,'facility_id'=>$facility_id,'patient_id'=>$patient_id,'date_attended'=>date('Y-m-d')));
                if(!$patient->save())
                    continue;
                return $patient;
            }else{
                continue;
            }
        }
    }



    public function checkForExemptionNumber($facility_id,$patient_id,$user_id){
        $is_patient_ExemptionNumber = DB::table('tbl_exemption_numbers')
            ->where('patient_id',$patient_id)
            ->orderBy('id','DESC')
            ->take(1)->get();
        if(count($is_patient_ExemptionNumber)>0){
            echo "Client already have Exemption number.. ";
        }	else{
            $this->patientExemptionNumber($facility_id,$patient_id,$user_id) ;

        }


    }

    public function patientExemptionNumber($facility_id,$patient_id,$user_id)
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





    //Get Patient
    public function getpatient()
    {
        return Tbl_patient::get();
    }


    //Update Patient Ame
    public function updatepatient(Request $request)
    {
        $id=$request['id'];
        return Tbl_patient::where('id',$id)->update($request->all());
    }
	
	public function duplicate(Request $request, $updating)
    {
        if(!$updating && patientRegistration::duplicate("tbl_patients", array("first_name","middle_name","last_name","gender","dob","residence_id","tribe_id"), array($request['first_name'],$request['middle_name'],$request['last_name'],$request['gender'],$request['dob'],$request['residence_id'],$request['tribe'])))
			return true;
		elseif($updating && patientRegistration::duplicate("tbl_patients", array("first_name","middle_name","last_name","gender","dob","residence_id","tribe_id"), array($request['first_name'],$request['middle_name'],$request['last_name'],$request['gender'],$request['dob'],$request['residence_id'],$request['tribe']), $updating, $request['id'], 'id'))
			return true;
		else
			return false;
    }
	
	//added to set admission on facilities not using in ipd but system has to know admitted patients
	public function falseAdmit(Request $request){
		if(DB::select("select count(*) count from tbl_invoice_lines where patient_id='".$request->patient_id."' and timestampdiff(day, created_at, current_date) <=7")[0]->count == 0)
			return "No relevant information found";
		
		Tbl_admission::create([
                'admission_date' => date('Y-m-d H:m:s'),'account_id' => $request->input('account_id'), 'patient_id' => $request->input('patient_id'), 'admission_status_id' => 2, 'facility_id' => $request->input('facility_id'), 'user_id' => $request->input('user_id'),
            ]);
		return "Patient successfully marked as admitted";
	}




    public function change_patient_category_receiption(Request $request)
    {
        $patient_id=$request->patient_id;
        $main_category_id=$request->main_category_id;
        $bill_id=$request->category_id;
       
        //$category_number=Tbl_bills_category::where('patient_id',$patient_id)->orderBy('id','desc')->first();
        $category_number=Tbl_bills_category::where('patient_id',$patient_id)->orderBy('created_at','desc')->take(1)->get();
        //return $update_id=$category_number->id;
        if(count($category_number)<1){
            return response()->json([
                'msg'=>'Patient Has No Category Yet.... ',
                'status'=>0
            ]);
        }
        else{
            $update_id=$category_number[0]->account_id;
            $data=Tbl_bills_category::where('account_id',$update_id)->update([
                'main_category_id'=>$main_category_id,
                'bill_id'=>$bill_id,

            ]);

            return response()->json([
                'msg'=>'ok ',
                'status'=>1
            ]);

        }

    }




}