<?php

namespace App\Http\Controllers\BloodBank;

use App\BloodBank\Tbl_blood_donation;
use App\BloodBank\Tbl_blood_request;
use App\BloodBank\Tbl_blood_screening;
use App\BloodBank\Tbl_blood_stock;
use App\BloodBank\Tbl_donor_infor;
use App\BloodBank\Tbl_donor_investigation;
use App\classes\patientRegistration;
use App\Patient\Tbl_patient;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;

class BloodBankController extends Controller
{
    //

    public function blood_bank_registration(Request $request)
    {
        foreach($request->all() as $key=>$value)
            $request[$key] = strtoupper($value);
        $genders=array('MALE','FEMALE');

        $facility_id=$request->input('facility_id');
        $gender=$request->input('gender');
        $mobile_number=$request->input('mobile_number');
        $residence_id=$request->input('residence_id');
        $dob=$request->input('dob');
        $tribe=$request->input('tribe');
        $mobile_pattern='#^[0][6-7][1-9][2-9][0-9]{6}$#';
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

        else if (!is_numeric($residence_id)) {

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

         return $data= patientRegistration::patient_registration($request);

             
        }
    }



    public function blood_stock(Request $data)
    {
        foreach ($data->all() as $request ){

        if ($request['blood_group']==''){
            return response()->json([
                'msg' => 'Please Choose Blood Group',
                'status' => 0
            ]);
        }
       else if ($request['unit']==''){
            return response()->json([
                'msg' => 'Please enter Number of Blood Unit',
                'status' => 0
            ]);
        }
        else if(patientRegistration::duplicate('tbl_blood_stocks',array('blood_group','unit','facility_id','user_id',"((timestampdiff(minute,created_at,CURRENT_TIMESTAMP) <=5))"), array($request['blood_group'],$request['unit'],$request['facility_id'],$request['user_id']))==true) {

            return response()->json([
                'msg' => 'DUPLICATE WAS DETECTED, PLEASE DONT RE-SUBMIT FORM TWICE FOR THE SAME REQUEST',
                'status' => '0'
            ]);
        }
else{


      Tbl_blood_stock::create($request)  ;



    }
            
    }
        return response()->json([
            'msg' => 'Blood units Successful Saved ',
            'status' => 1
        ]);
    }

    public function blood_stock_balance($facility_id)
    {
    return DB::table('tbl_blood_stocks')->select('tbl_blood_stocks.*',DB::raw('sum(unit) as available_unit'))
        ->where('control','l')
        ->where('facility_id',$facility_id)
        ->groupBy('blood_group')->orderBy('blood_group','asc')->get();
    }

    public function blood_stock_issued(Request $request)
    {
        $facility_id=$request['facility_id'];
        $start_date=$request['start_date'];
        $end_date=$request['end_date'];
$usedall=[];
        $usedall[]= DB::table('tbl_blood_stocks')->select('tbl_blood_stocks.*',DB::raw('sum(unit_issued) as unit_issued'))
        
        ->where('facility_id',$facility_id)
        ->where('patient_id','!=',null)
        ->whereBetween('created_at',[$start_date,$end_date])
        ->groupBy('blood_group')->orderBy('blood_group','asc')->get();

        $usedall[]= DB::table('tbl_blood_stocks')->select('unit_issued_out','tbl_blood_stocks.*',DB::raw('sum(unit_issued) as unit_issued'))

        ->where('facility_id',$facility_id)
            ->where('unit_issued_out','!=',null)
        ->whereBetween('created_at',[$start_date,$end_date])
        ->groupBy('unit_issued_out')
        ->groupBy('blood_group')
            ->orderBy('blood_group','asc')->get();
        return $usedall;
    }

    public function blood_stock_issuing(Request $request)
    {


if($request['patient_id']=='' && $request['unit_issued_out']==''){
    return response()->json([
        'msg' => 'Please Choose Patient of Facility You want to Issue this Blood Unit',
        'status' => 0
    ]);
}
         else if ($request['blood_group']==''){
            return response()->json([
                'msg' => 'Please Choose Blood Group',
                'status' => 0
            ]);
        }
        else if ($request['unit_issued']==''){
            return response()->json([
                'msg' => 'Please enter Number of Blood Unit',
                'status' => 0
            ]);
        }
        else{
            $blood_group=$request['blood_group'];
             $patient_id=$request['patient_id'];
            $unit_issued=$request['unit_issued'];
            $user_id=$request['user_id'];
            $facility_id=$request['facility_id'];

         if(patientRegistration::duplicate('tbl_blood_stocks',array('blood_group','facility_id','user_id','unit_issued','patient_id',"((timestampdiff(minute,created_at,CURRENT_TIMESTAMP) <=5))"), array($request['blood_group'],$request['facility_id'],$request['user_id'],$request['unit_issued'],$request['patient_id']))==true) {

                return response()->json([
                    'msg' => 'DUPLICATE WAS DETECTED, PLEASE DONT RE-SUBMIT FORM TWICE FOR THE SAME REQUEST',
                    'status' => '0'
                ]);
            }
  $getbalance=Tbl_blood_stock::where('blood_group',$blood_group)
    ->where('facility_id',$facility_id)
    ->where('unit','>=',$unit_issued)
    ->where('control','l')
    ->get();
          if (count($getbalance)>0){
              $ref_id=$getbalance[0]->id;
              $balance_remained=$getbalance[0]->unit-$unit_issued;
              if ($balance_remained >= 0) {
              Tbl_blood_stock::where('id',$ref_id)->update([
                  'control'=>'c'
              ]);
//
              $send=new Tbl_blood_stock($request->all());
              $send['unit']=$balance_remained;
              $send['control']='l';
              $send['facility_id']=$facility_id;
              $send['blood_group']=$blood_group;
              $send['user_id']=$user_id;
              $send['unit_issued']=$request['unit_issued'];
              $send['patient_id']=$request['patient_id'];
              $send->save();
                return response()->json([
                    'msg' =>  'Request Successful Processed',
                    'status' => 1
                ]);

          } else {

              return response()->json([
                  'msg' => 'No Balance Enough To issue Blood Group ' . $blood_group . '.Only ' . $getbalance->unit . ' Unit(s) Remained',
                  'status' => 0
              ]);
          }
        }

    else{
        return response()->json([
            'msg' => ' Blood Group '.$blood_group.  '. Is not available in Your  Stock',
            'status' => 0
        ]);
    }

        }

    }
    
   public function Issue_blood_request(Request $request)
    {
 
 
          if ($request['blood_group']==''){
            return response()->json([
                'msg' => 'Please Choose Blood Group',
                'status' => 0
            ]);
        }
        else if ($request['unit_issued']==''){
            return response()->json([
                'msg' => 'Please enter Number of Blood Unit',
                'status' => 0
            ]);
        }
        else if ($request['bag_no']==''){
            return response()->json([
                'msg' => 'Please enter BloodBag Number',
                'status' => 0
            ]);
        }
        else{
            $id=$request['id'];
            $blood_group=$request['blood_group'];
            $bag_no=$request['bag_no'];
             $patient_id=$request['patient_id'];
            $unit_issued=$request['unit_issued'];
            $user_id=$request['user_id'];
            $facility_id=$request['facility_id'];

         if(patientRegistration::duplicate('tbl_blood_stocks',array('blood_group','facility_id','user_id','unit_issued','patient_id',"((timestampdiff(minute,created_at,CURRENT_TIMESTAMP) <=5))"), array($request['blood_group'],$request['facility_id'],$request['user_id'],$request['unit_issued'],$request['patient_id']))==true) {

                return response()->json([
                    'msg' => 'DUPLICATE WAS DETECTED, PLEASE DONT RE-SUBMIT FORM TWICE FOR THE SAME REQUEST',
                    'status' => '0'
                ]);
            }
  $getbalance=Tbl_blood_stock::where('blood_group',$blood_group)
    ->where('facility_id',$facility_id)
    ->where('unit','>=',$unit_issued)
    ->where('control','l')
    ->get();
          if (count($getbalance)>0) {

              $ref_id = $getbalance[0]->id;
              $balance_remained = $getbalance[0]->unit - $unit_issued;
              if ($balance_remained >= 0) {
                  Tbl_blood_stock::where('id', $ref_id)->update([
                      'control' => 'c'
                  ]);
//
                  $send = new Tbl_blood_stock($request->all());
                  $send['unit'] = $balance_remained;
                  $send['control'] = 'l';
                  $send['facility_id'] = $facility_id;
                  $send['blood_group'] = $blood_group;
                  $send['user_id'] = $user_id;
                  $send['unit_issued'] = $request['unit_issued'];
                  $send['patient_id'] = $request['patient_id'];

                  $send->save();
                  Tbl_blood_request::where('id', $id)->update([
                      'status' => 1,
                      'bag_no'=>$bag_no,
                      'processed_by' => $user_id
                  ]);
//
                  return response()->json([
                      'msg' => 'Request Successful Processed',
                      'status' => 1
                  ]);

              } else {

                  return response()->json([
                      'msg' => 'No Balance Enough To issue Blood Group ' . $blood_group . '.Only ' . $getbalance->unit . ' Unit(s) Remained',
                      'status' => 0
                  ]);
              }
          }

                else{
                    return response()->json([
                        'msg' => ' Blood Group '.$blood_group.  '. Is not available in Your  Stock',
                        'status' => 0
                    ]);
                }


        }

    }

    public function Donor_type_info(Request $request)
    {

        $checked=Tbl_donor_infor::where('patient_id',$request['patient_id'])->get();

         if(count($checked)>0 && $request['donor_no'] !=$checked[0]->donor_no ){

                 return response()->json([
                     'msg' => ' DONOR NUMBER Supplied Does not Match....'. 'Verify with This '.$checked->donor_no,
                     'status' => '0'
                 ]);

        }

        if($request['donor_no'] ==''){
            return response()->json([
                'msg' => 'Please Enter DONOR NUMBER',
                'status' => '0'
            ]);
        }
        if($request['donor_type'] ==''){
            return response()->json([
                'msg' => 'Please Enter Donor Type',
                'status' => '0'
            ]);
        }
        if($request['donor_condition'] ==''){
            return response()->json([
                'msg' => 'Please Enter Donor Condition',
                'status' => '0'
            ]);
        }

        if(patientRegistration::duplicate('tbl_donor_infors',array('patient_id',"((timestampdiff(minute,created_at,CURRENT_TIMESTAMP) <=2))"), array($request['patient_id']))==true) {

            return response()->json([
                'msg' => 'DUPLICATE WAS DETECTED, PLEASE DONT RE-SUBMIT FORM TWICE FOR THE SAME REQUEST',
                'status' => '0'
            ]);
        }
        Tbl_donor_infor::create($request->all());

        return response()->json([
            'msg' => '  Successful Saved ',
            'status' => 1
        ]);
    }

    public function Donor_damu(Request $request)
    {
        if(patientRegistration::duplicate('tbl_blood_donations',array('patient_id',"((timestampdiff(minute,created_at,CURRENT_TIMESTAMP) <=2))"), array($request['patient_id']))==true) {

            return response()->json([
                'msg' => 'DUPLICATE WAS DETECTED, PLEASE DONT RE-SUBMIT FORM TWICE FOR THE SAME REQUEST',
                'status' => '0'
            ]);
        }

        Tbl_blood_donation::create($request->all());
        return response()->json([
            'msg' => '  Successful Saved ',
            'status' => 1
        ]);
    }

    public function Donor_vipimo(Request $request)
    {

        if(patientRegistration::duplicate('tbl_donor_investigations',array('patient_id',"((timestampdiff(minute,created_at,CURRENT_TIMESTAMP) <=2))"), array($request['patient_id']))==true) {

            return response()->json([
                'msg' => 'DUPLICATE WAS DETECTED, PLEASE DONT RE-SUBMIT FORM TWICE FOR THE SAME REQUEST',
                'status' => '0'
            ]);
        }
        Tbl_donor_investigation::create($request->all());
        return response()->json([
            'msg' => '  Successful Saved ',
            'status' => 1
        ]);
    }
    public function Donor_dodoso(Request $request)
    {
        if(patientRegistration::duplicate('tbl_donor_investigations',array('patient_id',"((timestampdiff(minute,created_at,CURRENT_TIMESTAMP) <=2))"), array($request['patient_id']))==true) {

            return response()->json([
                'msg' => 'DUPLICATE WAS DETECTED, PLEASE DONT RE-SUBMIT FORM TWICE FOR THE SAME REQUEST',
                'status' => '0'
            ]);
        }
        return response()->json([
            'msg' => '  Successful Saved ',
            'status' => 1
        ]);
    }

    public function blood_bank_screening(Request $request)
    {
if($request['blood_group']==''){
    return response()->json([
        'msg' => 'Please Fill Blood Group',
        'status' => '0'
    ]);
}
        if($request['rh']==''){
    return response()->json([
        'msg' => 'Please Fill RH',
        'status' => '0'
    ]);
}
        if($request['rpr']==''){
    return response()->json([
        'msg' => 'Please Fill RPR',
        'status' => '0'
    ]);
} if($request['hbsag']==''){
    return response()->json([
        'msg' => 'Please Fill HBSAG',
        'status' => '0'
    ]);
}if($request['hcv']==''){
    return response()->json([
        'msg' => 'Please Fill HCV',
        'status' => '0'
    ]);
}if($request['hiv']==''){
    return response()->json([
        'msg' => 'Please Fill HIV',
        'status' => '0'
    ]);
}if($request['assay_type']==''){
    return response()->json([
        'msg' => 'Please Fill ASSAY TYPE',
        'status' => '0'
    ]);
}
    $facility_id=$request['facility_id'];
    $user_id=$request['user_id'];
    $patient_id=$request['patient_id'];
    $blood_group=$request['blood_group'];
    $rh=$request['rh'];
    $rpr=$request['rpr'];
    $hbsag=$request['hbsag'];
    $hcv=$request['hcv'];
    $hiv=$request['hiv'];
    $assey_type=$request['assay_type'];
        if(patientRegistration::duplicate('tbl_blood_screenings',array('patient_id',"((timestampdiff(minute,created_at,CURRENT_TIMESTAMP) <=2))"), array($request['patient_id']))==true) {

            return response()->json([
                'msg' => 'DUPLICATE WAS DETECTED, PLEASE DONT RE-SUBMIT FORM TWICE FOR THE SAME REQUEST',
                'status' => '0'
            ]);
        }
        else{
            $donor=Tbl_donor_infor::where('patient_id',$patient_id)->get();
            if(count($donor)>0) {

                $donor_number = $donor[0]->donor_no;
                Tbl_blood_screening::create([
                    'facility_id' => $facility_id,
                    'user_id' => $user_id,
                    'patient_id' => $patient_id,
                    'blood_group' => $blood_group,
                    'rh' => $rh,
                    'rpr' => $rpr,
                    'hcv' => $hcv,
                    'hiv' => $hiv,
                    'hbsag' => $hbsag,
                    'assay_type' =>$assey_type,
                    'donor_number' => $donor_number,
                ]);
                return response()->json([
                    'msg' => 'Donor with Number  ' . $donor_number . '  Successful Saved ',
                    'status' => 1
                ]);
            }
            else{
                return response()->json([
                    'msg' => 'Donor Has no Donation Number Yet... Please Complete Information For This Donor in Blood BAnk Registration Point ',
                    'status' => 0
                ]);
            }
        }

    }

    public function getBloodScreening(Request $request)
    {
        $facility_id=$request['facility_id'];
        $start_date=$request['start_date'];
        $end_date=$request['end_date'];
      return Tbl_blood_screening::where('facility_id',$facility_id)
          ->whereBetween('created_at',[$start_date,$end_date])->get() ;
    }

    public function NumberOfBloodUnitCollected(Request $request)
    {
        $facility_id = $request['facility_id'];
        $start_date = $request['start_date'];
        $end_date = $request['end_date'];
$all=[];
       $all[]= DB::select("SELECT
 sum(CASE when gender='MALE' AND  donor_type='vnr' then 1 ELSE  0 END ) as vnr_male, 
 sum(CASE when gender='FEMALE' AND  donor_type='vnr' then 1 ELSE  0 END ) as vnr_female,
 sum(CASE when gender='MALE' AND  donor_type='fr' then 1 ELSE  0 END ) as fr_male, 
 sum(CASE when gender='FEMALE' AND  donor_type='fr' then 1 ELSE  0 END ) as fr_female, 
 sum(CASE when gender='FEMALE' AND  donor_condition='Marudio' then 1 ELSE  0 END ) as total_donor_repeaters, 
 sum(CASE when (gender='MALE' or gender='FEMALE') AND  (donor_type='fr' or donor_type='vnr') then 1 ELSE  0 END ) as total_collected 
   from tbl_donor_infors  inner JOIN tbl_patients on tbl_patients.id=tbl_donor_infors.patient_id
 WHERE   tbl_donor_infors.facility_id='$facility_id' and tbl_donor_infors.created_at BETWEEN '" . $start_date . "' and '" . $end_date . "' "
    );
        $all[]= DB::select("SELECT
 sum(CASE when hiv='POSITIVE' AND  donor_type='vnr' then 1 ELSE  0 END ) as hiv_vnr, 
 sum(CASE when hiv='POSITIVE' AND  donor_type='fr' then 1 ELSE  0 END ) as hiv_fr,  
 sum(CASE when hiv='POSITIVE' AND  (donor_type='fr' or donor_type='vnr') then 1 ELSE  0 END ) as total_hiv,
 
  sum(CASE when hbsag='POSITIVE' AND  donor_type='vnr' then 1 ELSE  0 END ) as hbsag_vnr, 
 sum(CASE when hbsag='POSITIVE' AND  donor_type='fr' then 1 ELSE  0 END ) as hbsag_fr, 
 sum(CASE when hbsag='POSITIVE' AND  (donor_type='fr' or donor_type='vnr') then 1 ELSE  0 END ) as total_hbsag,
 
 sum(CASE when hcv='POSITIVE' AND  donor_type='vnr' then 1 ELSE  0 END ) as hcv_nvr, 
 sum(CASE when hcv='POSITIVE' AND  donor_type='fr' then 1 ELSE  0 END ) as hcv_fr, 
 sum(CASE when hcv='POSITIVE' AND  (donor_type='fr' or donor_type='vnr') then 1 ELSE  0 END ) as total_hcv, 
 
 sum(CASE when rpr='POSITIVE' AND  donor_type='vnr' then 1 ELSE  0 END ) as rpr_nvr, 
 sum(CASE when rpr='POSITIVE' AND  donor_type='fr' then 1 ELSE  0 END ) as rpr_fr, 
 sum(CASE when rpr='POSITIVE' AND  (donor_type='fr' or donor_type='vnr') then 1 ELSE  0 END ) as total_rpr ,
 sum(CASE when  hiv='POSITIVE' or hbsag='POSITIVE' or hcv='POSITIVE' OR rpr='POSITIVE'  then 1 ELSE  0 END ) as total_tti 
   from tbl_blood_screenings  inner JOIN tbl_patients on tbl_patients.id=tbl_blood_screenings.patient_id
   inner JOIN tbl_donor_infors on tbl_blood_screenings.patient_id=tbl_donor_infors.patient_id
 WHERE assay_type='in' AND  tbl_blood_screenings.facility_id='$facility_id' and tbl_blood_screenings.created_at BETWEEN '" . $start_date . "' and '" . $end_date . "' "
    );
        $all[]= DB::select("SELECT
 sum(CASE when hiv='POSITIVE' AND  donor_type='vnr' then 1 ELSE  0 END ) as hiv_vnr, 
 sum(CASE when hiv='POSITIVE' AND  donor_type='fr' then 1 ELSE  0 END ) as hiv_fr,  
 sum(CASE when hiv='POSITIVE' AND  (donor_type='fr' or donor_type='vnr') then 1 ELSE  0 END ) as total_hiv,
 
  sum(CASE when hbsag='POSITIVE' AND  donor_type='vnr' then 1 ELSE  0 END ) as hbsag_vnr, 
 sum(CASE when hbsag='POSITIVE' AND  donor_type='fr' then 1 ELSE  0 END ) as hbsag_fr, 
 sum(CASE when hbsag='POSITIVE' AND  (donor_type='fr' or donor_type='vnr') then 1 ELSE  0 END ) as total_hbsag,
 
 sum(CASE when hcv='POSITIVE' AND  donor_type='vnr' then 1 ELSE  0 END ) as hcv_nvr, 
 sum(CASE when hcv='POSITIVE' AND  donor_type='fr' then 1 ELSE  0 END ) as hcv_fr, 
 sum(CASE when hcv='POSITIVE' AND  (donor_type='fr' or donor_type='vnr') then 1 ELSE  0 END ) as total_hcv, 
 
 sum(CASE when rpr='POSITIVE' AND  donor_type='vnr' then 1 ELSE  0 END ) as rpr_nvr, 
 sum(CASE when rpr='POSITIVE' AND  donor_type='fr' then 1 ELSE  0 END ) as rpr_fr, 
 sum(CASE when rpr='POSITIVE' AND  (donor_type='fr' or donor_type='vnr') then 1 ELSE  0 END ) as total_rpr ,
 sum(CASE when  hiv='POSITIVE' or hbsag='POSITIVE' or hcv='POSITIVE' OR rpr='POSITIVE'  then 1 ELSE  0 END ) as total_tti 
   from tbl_blood_screenings  inner JOIN tbl_patients on tbl_patients.id=tbl_blood_screenings.patient_id
   inner JOIN tbl_donor_infors on tbl_blood_screenings.patient_id=tbl_donor_infors.patient_id
 WHERE assay_type='sup' AND  tbl_blood_screenings.facility_id='$facility_id' and tbl_blood_screenings.created_at BETWEEN '" . $start_date . "' and '" . $end_date . "' "
    );
return $all;
    }


    public function Blood_request_queue($facility_id)
    {
      return DB::select("SELECT users.name,tbl_blood_requests.*,tbl_departments.department_name,tbl_patients.gender,tbl_patients.first_name,tbl_patients.middle_name,tbl_patients.last_name,tbl_patients.medical_record_number,tbl_patients.dob FROM `tbl_blood_requests` INNER join tbl_patients on tbl_blood_requests.patient_id=tbl_patients.id INNER JOIN tbl_departments on tbl_departments.id=tbl_blood_requests.dept_id INNER JOIN users on users.id=tbl_blood_requests.requested_by where tbl_blood_requests.facility_id='$facility_id' AND tbl_blood_requests.status=0 AND timestampdiff(day, tbl_blood_requests.created_at, CURRENT_TIMESTAMP)<2");
    }
}