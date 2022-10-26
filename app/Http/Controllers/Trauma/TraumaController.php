<?php

namespace App\Http\Controllers\Trauma;

use App\Model\Trauma\Trauma_accident_location;
use App\Model\Trauma\TraumaAssesmentPlan;

use App\Model\Trauma\TraumaChiefComplaint;
use App\Model\Trauma\TraumaClientDiagnosis;
use App\Model\Trauma\TraumaClientDisposition;
use App\Model\Trauma\TraumaHpi;
use App\Model\Trauma\TraumaHpiInjuryMechanism;
use App\Model\Trauma\TraumaHysicalExamination;
use App\Model\Trauma\TraumaPastMedicalAllergyHistory;
use App\Model\Trauma\TraumaPastMedicalHistory;
use App\Model\Trauma\TraumaPrimaryBreathingSurvey;
use App\Model\Trauma\TraumaPrimaryCirculationSurvey;
use App\Model\Trauma\TraumaPrimaryDisabilitySurvey;
use App\Model\Trauma\TraumaPrimaryExposureSurvey;
use App\Model\Trauma\TraumaPrimaryFastSurvey;
use App\Model\Trauma\TraumaPrimarySurvey;
use App\Model\Trauma\TraumaLabResult;
use App\Model\Trauma\TraumaImagingResult;
use App\Model\Trauma\TraumaFluidMedication;
use App\Model\Trauma\TraumaProcedure;
use App\Model\Trauma\TraumaReAssesmentPlan;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Model\Trauma\Setup\TraumaConcept;
use App\Model\Trauma\Setup\TriageCategory;
use Illuminate\Support\Facades\DB;
use Validator;
use App\classes\SystemTracking;
use App\Trackable;


class TraumaController extends Controller
{
       public function  saveChiefComplaint(Request $request)
    {
        $data      =  $request->all();
        if(!is_array($data))
            return ["status"=>"error", "text"=>"Invalid request data {".GETTYPE($data)."} received, whereas {ARRAY} was expected"];

        $validator =  Validator::make($data[0], TraumaChiefComplaint::$create_rules);
        if ($validator->fails()) {
            return ["status"=>"error", "text"=>"Validation error encountered"];
        }

        foreach($data as $datum){
            if(TraumaChiefComplaint::isDuplicate((array)$datum))
                return ["status"=>"error", "text"=>"Possible duplicate record"];
        }

        $error = false;

        foreach($data as $datum){
            
            $client_id=  $data[0]['client_id'] ;
            $user_id=  $data[0]['user_id'] ;
            if(!$result = TraumaChiefComplaint::create($datum))
               
                $error = true;
$pt=DB::select("select patient_id from tbl_trauma_clients where id ='".$client_id."' limit 1");
             $oldData=null;
                $patient_id=$pt[0]->patient_id;
                $trackable_id=$result->id;
                SystemTracking::Tracking($user_id,$patient_id,$trackable_id,$result,$oldData);
        }

        if(!$error) {
            return ["status"=>"success", "text"=>"Chief Complaint successfully created"];
        } else {
            return ["status"=>"warning", "text"=>"An error encountered. Data not saved"];
        }
}

    public function  saveAirwayPrimarySurvey(Request $request)
    {
        $data      =  $request->all();
        if(!is_array($data))
            return ["status"=>"error", "text"=>"Invalid request data {".GETTYPE($data)."} received, whereas {ARRAY} was expected"];

        $validator =  Validator::make($data[0], TraumaPrimarySurvey::$create_rules);
        if ($validator->fails()) {
            return ["status"=>"error", "text"=>"Validation error encountered"];
        }

        foreach($data as $datum){
            if(TraumaPrimarySurvey::isDuplicate((array)$datum))
                return ["status"=>"error", "text"=>"Possible duplicate record"];
        }

        $error = false;
        foreach($data as $datum){
            if(!$result = TraumaPrimarySurvey::create($datum))
                $error = true;
        }

        if(!$error) {
            return ["status"=>"success", "text"=>"Airway survey successfully created"];
        } else {
            return ["status"=>"warning", "text"=>"An error encountered. Data not saved"];
        }
}
    public function  saveBreathingPrimarySurvey(Request $request)
    {
        $data      =  $request->all();
        if(!is_array($data))
            return ["status"=>"error", "text"=>"Invalid request data {".GETTYPE($data)."} received, whereas {ARRAY} was expected"];

        $validator =  Validator::make($data[0], TraumaPrimaryBreathingSurvey::$create_rules);
        if ($validator->fails()) {
            return ["status"=>"error", "text"=>"Validation error encountered"];
        }

        foreach($data as $datum){
            if(TraumaPrimaryBreathingSurvey::isDuplicate((array)$datum))
                return ["status"=>"error", "text"=>"Possible duplicate record"];
        }

        $error = false;
        foreach($data as $datum){
            if(!$result = TraumaPrimaryBreathingSurvey::create($datum))
                $error = true;
        }

        if(!$error) {
            return ["status"=>"success", "text"=>"Breathing survey successfully created"];
        } else {
            return ["status"=>"warning", "text"=>"An error encountered. Data not saved"];
        }
}
    public function  saveCirculationPrimarySurvey(Request $request)
    {
        $data      =  $request->all();
        if(!is_array($data))
            return ["status"=>"error", "text"=>"Invalid request data {".GETTYPE($data)."} received, whereas {ARRAY} was expected"];

        $validator =  Validator::make($data[0], TraumaPrimaryCirculationSurvey::$create_rules);
        if ($validator->fails()) {
            return ["status"=>"error", "text"=>"Validation error encountered"];
        }

        foreach($data as $datum){
            if(TraumaPrimaryCirculationSurvey::isDuplicate((array)$datum))
                return ["status"=>"error", "text"=>"Possible duplicate record"];
        }

        $error = false;
        foreach($data as $datum){
            if(!$result = TraumaPrimaryCirculationSurvey::create($datum))
                $error = true;
        }

        if(!$error) {
            return ["status"=>"success", "text"=>"Circulation survey successfully created"];
        } else {
            return ["status"=>"warning", "text"=>"An error encountered. Data not saved"];
        }
}
    public function  saveDisabilityPrimarySurvey(Request $request)
    {
        $data      =  $request->all();
        if(!is_array($data))
            return ["status"=>"error", "text"=>"Invalid request data {".GETTYPE($data)."} received, whereas {ARRAY} was expected"];

        $validator =  Validator::make($data[0], TraumaPrimaryDisabilitySurvey::$create_rules);
        if ($validator->fails()) {
            return ["status"=>"error", "text"=>"Validation error encountered"];
        }

        foreach($data as $datum){
            if(TraumaPrimaryDisabilitySurvey::isDuplicate((array)$datum))
                return ["status"=>"error", "text"=>"Possible duplicate record"];
        }

        $error = false;
        foreach($data as $datum){
            if(!$result = TraumaPrimaryDisabilitySurvey::create($datum))
                $error = true;
        }

        if(!$error) {
            return ["status"=>"success", "text"=>"Disability survey successfully created"];
        } else {
            return ["status"=>"warning", "text"=>"An error encountered. Data not saved"];
        }
}
    public function  saveExposurePrimarySurvey(Request $request)
    {
        $data      =  $request->all();
        if(!is_array($data))
            return ["status"=>"error", "text"=>"Invalid request data {".GETTYPE($data)."} received, whereas {ARRAY} was expected"];

        $validator =  Validator::make($data[0], TraumaPrimaryExposureSurvey::$create_rules);
        if ($validator->fails()) {
            return ["status"=>"error", "text"=>"Validation error encountered"];
        }

        foreach($data as $datum){
            if(TraumaPrimaryExposureSurvey::isDuplicate((array)$datum))
                return ["status"=>"error", "text"=>"Possible duplicate record"];
        }

        $error = false;
        foreach($data as $datum){
            if(!$result = TraumaPrimaryExposureSurvey::create($datum))
                $error = true;
        }

        if(!$error) {
            return ["status"=>"success", "text"=>"Exposure survey successfully created"];
        } else {
            return ["status"=>"warning", "text"=>"An error encountered. Data not saved"];
        }
}
    public function  saveFastPrimarySurvey(Request $request)
    {
        $data      =  $request->all();
        if(!is_array($data))
            return ["status"=>"error", "text"=>"Invalid request data {".GETTYPE($data)."} received, whereas {ARRAY} was expected"];

        $validator =  Validator::make($data[0], TraumaPrimaryFastSurvey::$create_rules);
        if ($validator->fails()) {
            return ["status"=>"error", "text"=>"Validation error encountered"];
        }

        foreach($data as $datum){
            if(TraumaPrimaryFastSurvey::isDuplicate((array)$datum))
                return ["status"=>"error", "text"=>"Possible duplicate record"];
        }

        $error = false;
        foreach($data as $datum){
            if(!$result = TraumaPrimaryFastSurvey::create($datum))
                $error = true;
        }

        if(!$error) {
            return ["status"=>"success", "text"=>"Exposure survey successfully created"];
        } else {
            return ["status"=>"warning", "text"=>"An error encountered. Data not saved"];
        }
}
    public function  savePastMedicalHistory(Request $request)
    {
        $data      =  $request->all();
        if(!is_array($data))
            return ["status"=>"error", "text"=>"Invalid request data {".GETTYPE($data)."} received, whereas {ARRAY} was expected"];

        $validator =  Validator::make($data[0], TraumaPastMedicalHistory::$create_rules);
        if ($validator->fails()) {
            return ["status"=>"error", "text"=>"Validation error encountered"];
        }

        foreach($data as $datum){
            if(TraumaPastMedicalHistory::isDuplicate((array)$datum))
                return ["status"=>"error", "text"=>"Possible duplicate record"];
        }

        $error = false;
        foreach($data as $datum){
            if(!$result = TraumaPastMedicalHistory::create($datum))
                $error = true;
        }

        if(!$error) {
            return ["status"=>"success", "text"=>"Past Medical History successfully created"];
        } else {
            return ["status"=>"warning", "text"=>"An error encountered. Data not saved"];
        }
}
    public function  savePastMedicalAllergyHistory(Request $request)
    {
        $data      =  $request->all();
        if(!is_array($data))
            return ["status"=>"error", "text"=>"Invalid request data {".GETTYPE($data)."} received, whereas {ARRAY} was expected"];

        $validator =  Validator::make($data[0], TraumaPastMedicalAllergyHistory::$create_rules);
        if ($validator->fails()) {
            return ["status"=>"error", "text"=>"Validation error encountered"];
        }

        foreach($data as $datum){
            if(TraumaPastMedicalAllergyHistory::isDuplicate((array)$datum))
                return ["status"=>"error", "text"=>"Possible duplicate record"];
        }

        $error = false;
        foreach($data as $datum){
            if(!$result = TraumaPastMedicalAllergyHistory::create($datum))
                $error = true;
        }

        if(!$error) {
            return ["status"=>"success", "text"=>"Past Medical Allergy History successfully created"];
        } else {
            return ["status"=>"warning", "text"=>"An error encountered. Data not saved"];
        }
}
    public function  saveTraumaHpi(Request $request)
    {
        $data      =  $request->all();
        if(!is_array($data))
            return ["status"=>"error", "text"=>"Invalid request data {".GETTYPE($data)."} received, whereas {ARRAY} was expected"];

        $validator =  Validator::make($data[0], TraumaHpi::$create_rules);
        if ($validator->fails()) {
            return ["status"=>"error", "text"=>"Validation error encountered"];
        }

        foreach($data as $datum){
            if(TraumaHpi::isDuplicate((array)$datum))
                return ["status"=>"error", "text"=>"Possible duplicate record"];
        }

        $error = false;
        foreach($data as $datum){
            if(!$result = TraumaHpi::create($datum))
                $error = true;
        }

        if(!$error) {
            return ["status"=>"success", "text"=>"HPI successfully created"];
        } else {
            return ["status"=>"warning", "text"=>"An error encountered. Data not saved"];
        }
}
    public function  saveTraumaInjuryMechanism(Request $request)
    {
        $data      =  $request->all();
        if(!is_array($data))
            return ["status"=>"error", "text"=>"Invalid request data {".GETTYPE($data)."} received, whereas {ARRAY} was expected"];

        $validator =  Validator::make($data[0], TraumaHpiInjuryMechanism::$create_rules);
        if ($validator->fails()) {
            return ["status"=>"error", "text"=>"Validation error encountered"];
        }

        foreach($data as $datum){
            if(TraumaHpiInjuryMechanism::isDuplicate((array)$datum))
                return ["status"=>"error", "text"=>"Possible duplicate record"];
        }

        $error = false;
        foreach($data as $datum){
            if(!$result = TraumaHpiInjuryMechanism::create($datum))
                $error = true;
        }

        if(!$error) {
            return ["status"=>"success", "text"=>"Injury Mechanism successfully created"];
        } else {
            return ["status"=>"warning", "text"=>"An error encountered. Data not saved"];
        }
}
    public function  saveTraumaPhysicalExam(Request $request)
    {
        $data      =  $request->all();
        if(!is_array($data))
            return ["status"=>"error", "text"=>"Invalid request data {".GETTYPE($data)."} received, whereas {ARRAY} was expected"];

        $validator =  Validator::make($data[0], TraumaHysicalExamination::$create_rules);
        if ($validator->fails()) {
            return ["status"=>"error", "text"=>"Validation error encountered"];
        }

        foreach($data as $datum){
            if(TraumaHysicalExamination::isDuplicate((array)$datum))
                return ["status"=>"error", "text"=>"Possible duplicate record"];
        }

        $error = false;
        foreach($data as $datum){
            if(!$result = TraumaHysicalExamination::create($datum))
                $error = true;
        }

        if(!$error) {
            return ["status"=>"success", "text"=>"Physical Examination successfully created"];
        } else {
            return ["status"=>"warning", "text"=>"An error encountered. Data not saved"];
        }
}
    public function  saveTraumaLabResult(Request $request)
    {
        $data      =  $request->all();
        if(!is_array($data))
            return ["status"=>"error", "text"=>"Invalid request data {".GETTYPE($data)."} received, whereas {ARRAY} was expected"];

        $validator =  Validator::make($data[0], TraumaLabResult::$create_rules);
        if ($validator->fails()) {
            return ["status"=>"error", "text"=>"Validation error encountered"];
        }

        foreach($data as $datum){
            if(TraumaLabResult::isDuplicate((array)$datum))
                return ["status"=>"error", "text"=>"Possible duplicate record"];
        }

        $error = false;
        foreach($data as $datum){
            if(!$result = TraumaLabResult::create($datum))
                $error = true;
        }

        if(!$error) {
            return ["status"=>"success", "text"=>"Lab Result successfully saved"];
        } else {
            return ["status"=>"warning", "text"=>"An error encountered. Data not saved"];
        }
}
    public function  saveTraumaImageResult(Request $request)
    {
        $data      =  $request->all();
        if(!is_array($data))
            return ["status"=>"error", "text"=>"Invalid request data {".GETTYPE($data)."} received, whereas {ARRAY} was expected"];

        $validator =  Validator::make($data[0], TraumaImagingResult::$create_rules);
        if ($validator->fails()) {
            return ["status"=>"error", "text"=>"Validation error encountered"];
        }

        foreach($data as $datum){
            if(TraumaImagingResult::isDuplicate((array)$datum))
                return ["status"=>"error", "text"=>"Possible duplicate record"];
        }

        $error = false;
        foreach($data as $datum){
            if(!$result = TraumaImagingResult::create($datum))
                $error = true;
        }

        if(!$error) {
            return ["status"=>"success", "text"=>"Lab Result successfully saved"];
        } else {
            return ["status"=>"warning", "text"=>"An error encountered. Data not saved"];
        }
}
    public function  saveTraumaProcedure(Request $request)
    {
        $data      =  $request->all();
        if(!is_array($data))
            return ["status"=>"error", "text"=>"Invalid request data {".GETTYPE($data)."} received, whereas {ARRAY} was expected"];

        $validator =  Validator::make($data[0], TraumaProcedure::$create_rules);
        if ($validator->fails()) {
            return ["status"=>"error", "text"=>"Validation error encountered"];
        }

        foreach($data as $datum){
            if(TraumaProcedure::isDuplicate((array)$datum))
                return ["status"=>"error", "text"=>"Possible duplicate record"];
        }

        $error = false;
        foreach($data as $datum){
            if(!$result = TraumaProcedure::create($datum))
                $error = true;
        }

        if(!$error) {
            return ["status"=>"success", "text"=>"Procedures successfully saved"];
        } else {
            return ["status"=>"warning", "text"=>"An error encountered. Data not saved"];
        }
    }
    public function  saveTraumaFluidMedication(Request $request)
    {
        $data      =  $request->all();
        if(!is_array($data))
            return ["status"=>"error", "text"=>"Invalid request data {".GETTYPE($data)."} received, whereas {ARRAY} was expected"];

        $validator =  Validator::make($data[0], TraumaFluidMedication::$create_rules);
        if ($validator->fails()) {
            return ["status"=>"error", "text"=>"Validation error encountered"];
        }

        foreach($data as $datum){
            if(TraumaFluidMedication::isDuplicate((array)$datum))
                return ["status"=>"error", "text"=>"Possible duplicate record"];
        }

        $error = false;
        foreach($data as $datum){
            if(!$result = TraumaFluidMedication::create($datum))
                $error = true;
        }

        if(!$error) {
            return ["status"=>"success", "text"=>"Fluid and Medications successfully saved"];
        } else {
            return ["status"=>"warning", "text"=>"An error encountered. Data not saved"];
        }
    }
    public function  saveTraumaClientAssesment(Request $request)
    {
        $records     =  $request->all()['records'];
        $diagnosis     =  $request->all()['diagnosis'];
        $error = false;
        foreach($records as $datum){
            if(!$result = TraumaAssesmentPlan::create($datum))
                $error = true;
        }
        foreach($diagnosis as $datum){
            if(!$result = TraumaClientDiagnosis::create($datum))
                $error = true;
        }

        if(!$error) {
            return ["status"=>"success", "text"=>"Assesement and Plan successfully saved"];
        } else {
            return ["status"=>"warning", "text"=>"An error encountered. Data not saved"];
        }
    }
    public function  saveTraumaClientReAssesment(Request $request)
    {
        $records     =  $request->all();
        $error = false;
        foreach($records as $datum){
            if(!$result = TraumaReAssesmentPlan::create($datum))
                $error = true;
        }


        if(!$error) {
            return ["status"=>"success", "text"=>"Re-Assessment and Plan successfully saved"];
        } else {
            return ["status"=>"warning", "text"=>"An error encountered. Data not saved"];
        }
    }
    public function  saveTraumaClientDisposition(Request $request)
    {
        $records     =  $request->all()['records'];
        $diagnosis     =  $request->all()['diagnosis'];
        $error = false;
        foreach($records as $datum){
            if(!$result = TraumaClientDisposition::create($datum))
                $error = true;
        }
        foreach($diagnosis as $datum){
            if(!$result = TraumaClientDiagnosis::create($datum))
                $error = true;
        }


        if(!$error) {
            return ["status"=>"success", "text"=>"Re-Assessment and Plan successfully saved"];
        } else {
            return ["status"=>"warning", "text"=>"An error encountered. Data not saved"];
        }
    }

    public function  saveAccidentLocation(Request $request)
    {
         
        $records     =  $request->all();

        $error = false;
        foreach($records as $datum){
            if(!$result = Trauma_accident_location::create($datum))
                $error = true;
        }



        if(!$error) {
            return ["status"=>"success", "text"=>"Accident Location successfully saved"];
        } else {
            return ["status"=>"warning", "text"=>"An error encountered. Data not saved"];
        }
    }

//history records

     public function traumaConcepts(){
        return TraumaConcept::getConceptBySection();
    }
    
    public function triageCategories(){
        return TriageCategory::get();
    }

    public function triageArrivalModes(){
        return DB::select("SELECT *FROM tbl_arrival_modes");
    }

public function getChiefComplaint($patient_id){
        return DB::select("SELECT t1.*,t2.name FROM tbl_trauma_chief_complaints t1 join users t2 on t1.user_id=t2.id where client_id='".$patient_id."' order by id desc");
    }
    public function getAccidentLocation($patient_id){
        return DB::select("SELECT t1.*,t2.name FROM trauma_accident_locations t1 join users t2 on t1.user_id=t2.id where client_id='".$patient_id."' order by id desc");
    }

public function getClientVitals($patient_id){
        return DB::select("SELECT t1.*,t2.name FROM tbl_trauma_vitals t1 left join users t2 on t1.recorded_by=t2.id where client_id='".$patient_id."' order by id desc");
    }

  
    public function getAirwayPrimarySurvey($patient_id){
 return DB::select("SELECT t1.*,t2.name FROM tbl_trauma_airway_primary_surveys t1 join users t2 on t1.user_id=t2.id where client_id='".$patient_id."' order by id desc");
    }

    public function getBreathingPrimarySurvey($patient_id){
 return DB::select("SELECT t1.*,t2.name FROM trauma_primary_breathing_surveys t1 join users t2 on t1.user_id=t2.id where client_id='".$patient_id."' order by id desc");
    }

    public function getCirculationPrimarySurvey($patient_id){
 return DB::select("SELECT t1.*,t2.name FROM trauma_primary_circulation_surveys t1 join users t2 on t1.user_id=t2.id where client_id='".$patient_id."' order by id desc");
    }
    public function getDisabilityPrimarySurvey($patient_id){
 return DB::select("SELECT t1.*,t2.name FROM trauma_primary_disability_surveys t1 join users t2 on t1.user_id=t2.id where client_id='".$patient_id."' order by id desc");
    }
     public function getExposurePrimarySurvey($patient_id){
 return DB::select("SELECT t1.*,t2.name FROM trauma_primary_exposure_surveys t1 join users t2 on t1.user_id=t2.id where client_id='".$patient_id."' order by id desc");
    }

     public function getFastPrimarySurvey($patient_id){
 return DB::select("SELECT t1.*,t2.name FROM trauma_primary_fast_surveys t1 join users t2 on t1.user_id=t2.id where client_id='".$patient_id."' order by id desc");
    }
      public function getPastMedicalHistory($patient_id){
 return DB::select("SELECT t1.*,t2.name FROM trauma_past_medical_histories t1 join users t2 on t1.user_id=t2.id where client_id='".$patient_id."' order by id desc");
    }
public function getPastMedicalAllergyHistory($patient_id){
 return DB::select("SELECT t1.*,t2.name FROM trauma_past_medical_allergy_histories t1 join users t2 on t1.user_id=t2.id where client_id='".$patient_id."' order by id desc");
    }
    public function getTraumaHpi($patient_id){
 return DB::select("SELECT t1.*,t2.name FROM trauma_hpis t1 join users t2 on t1.user_id=t2.id where client_id='".$patient_id."' order by id desc");
    }
     public function getPhysicalExamination($patient_id){
 return DB::select("SELECT t1.*,t2.name FROM trauma_hysical_examinations t1 join users t2 on t1.user_id=t2.id where client_id='".$patient_id."' order by id desc");
    }

    
     public function getInjuryMechanism($patient_id){
 return DB::select("SELECT t1.*,t2.name FROM trauma_hpi_injury_mechanisms t1 join users t2 on t1.user_id=t2.id where client_id='".$patient_id."' order by id desc");
    }
    public function getTraumaLabResults($patient_id){
 return DB::select("SELECT t1.*,t2.name FROM trauma_lab_results t1 join users t2 on t1.user_id=t2.id where client_id='".$patient_id."' order by id desc");
    }
     public function getTraumaImageResults($patient_id){
 return DB::select("SELECT t1.*,t2.name FROM trauma_imaging_results t1 join users t2 on t1.user_id=t2.id where client_id='".$patient_id."' order by id desc");
    }
    public function getTraumaassesment($patient_id){
 return DB::select("SELECT t1.*,t2.name FROM trauma_assesment_plans t1 join users t2 on t1.user_id=t2.id where client_id='".$patient_id."' order by id desc");
    }
    public function getTraumareassesment($patient_id){
 return DB::select("SELECT t1.*,t2.name FROM trauma_re_assesment_plans t1 join users t2 on t1.user_id=t2.id where client_id='".$patient_id."' order by id desc");
    }

    public function getTraumaFluid($patient_id){
 return DB::select("SELECT t1.*,t2.name FROM trauma_fluid_medications t1 join users t2 on t1.user_id=t2.id where client_id='".$patient_id."' order by id desc");
    }
    public function getTraumaProcedure($patient_id){
 return DB::select("SELECT t1.*,t2.name FROM trauma_procedures t1 join users t2 on t1.user_id=t2.id where client_id='".$patient_id."' order by id desc");
    }
    

    
    
    
     
}