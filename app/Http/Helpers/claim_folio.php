<?php
error_reporting(E_ALL ^ E_DEPRECATED);
use Illuminate\Contracts\Routing\ResponseFactory;

/**  Create Patient Visit Serial number for NHIF claims sending **/
if (!function_exists('createPatientVisitSerialNumber')) {

    function createPatientVisitSerialNumber($visit_id){
		$serial_number =0;
		 $name="SELECT t1.* ,t2.id AS account_id,DATE_FORMAT(date_attended, '%m') AS month_attended ,DATE_FORMAT(date_attended, '%Y') AS year_attended              
		   FROM tbl_patients t1
		   INNER JOIN tbl_accounts_numbers t2 ON t1.id=t2.patient_id
		   WHERE  t2.id='".$visit_id."'";
       $names = DB::SELECT($name); 
	   $check_if_assigned_serial_number ="SELECT * FROM tbl_patient_visit_serials t1 WHERE t1.visit_id='".$visit_id."'";
	   $is_assigned_serial_number = DB::SELECT($check_if_assigned_serial_number); 
	   if(count($is_assigned_serial_number)>0){
		   return $is_assigned_serial_number[0]->serial_number;
	   }else{
		  $check_last_number ="SELECT id,serial_number FROM tbl_patient_visit_serials t1 WHERE t1.month_of_visit='".$names[0]->month_attended."' AND  t1.year_of_visit='".$names[0]->year_attended."' ORDER BY id DESC";
	  	  $last_number = DB::SELECT($check_last_number);
         //check last number if exists
         if(count($last_number) ==0){
			 $serial_number =1;	      
	      }else{
			 $serial_number = $last_number[0]->serial_number+1;
		  }
		  $sql="INSERT INTO tbl_patient_visit_serials SET medical_record_number=".$names[0]->medical_record_number.",visit_id=".$visit_id.", serial_number=".$serial_number.",month_of_visit='".$names[0]->month_attended."',year_of_visit='".$names[0]->year_attended."', created_at=CURRENT_TIMESTAMP,updated_at=CURRENT_TIMESTAMP";
		  DB::statement($sql);
		  return $serial_number;
      }
	}
}



if (!function_exists('getNhifFacilityCodes')) {
    function getNhifFacilityCodes(){
		$sql="SELECT * FROM tbl_api_credentials t1 WHERE t1.active=1";
		$nhifFacilityCodes = DB::select($sql);
	}
}

function readClientsignature($visitId){
	  try{
		    $fileName=$visitId.'.txt';
		  if (Storage::disk('local')->exists($fileName)) {
          //return Storage::disk($disk)->get($fileName);
		 return  Storage::disk('local')->get($fileName);
          }
		  		
		
	  }
	  catch (\Exception $e) {
         return response()->json([
                               "Message"=>"Client signature failed to be read!, ".$e,
                               "status"=>"error"
                             ]);
       }
	}
	
	function readDoctorSignature($doctorId){
	  try{
		    $fileName="doctor-".$doctorId.'.txt';
		  if (Storage::disk('local')->exists($fileName)) {
          //return Storage::disk($disk)->get($fileName);
		 return  Storage::disk('local')->get($fileName);
          }
		  		
		
	  }
	  catch (\Exception $e) {
         return response()->json([
                               "Message"=>"Client signature failed to be read!, ".$e,
                               "status"=>"error"
                             ]);
       }
	}
	

/**  Create Patient Claim Folio **/
if (!function_exists('createPdfToNhif')) {

    function createPdfToNhif($visit_id){
		$details ="SELECT t1.*, TIMESTAMPDIFF(YEAR,t1.dob, CURRENT_DATE) AS age, t3.facility_code, t3.facility_name, t3.address, t2.account_number, t2.id AS account_id, t2.card_no, t2.authorization_number, t2.membership_number, t2.date_attended,MONTH(t2.date_attended) AS month_attended,YEAR(t2.date_attended) AS year_attended,t6.occupation_name, residence_name
		   FROM tbl_patients t1
		   INNER JOIN tbl_accounts_numbers t2 ON t2.id=$visit_id AND t1.id=t2.patient_id
		   INNER JOIN tbl_bills_categories t5 ON t2.id=t5.account_id
		   INNER JOIN users t4 ON t4.id=t2.user_id
		   INNER JOIN tbl_facilities t3 ON t3.id=t2.facility_id   
		   INNER JOIN tbl_residences t7 ON t7.id=t1.residence_id   
		   LEFT JOIN tbl_occupations t6 ON t6.id=t1.occupation_id";
		$details = DB::select($details)[0];
	   
		//Consultation
		$sql="SELECT t6.id AS visit_id,t4.amount AS item_price,t3.item_name,t3.item_code, 1 as quantity
			   FROM  tbl_invoice_lines  t1
			   INNER JOIN tbl_encounter_invoices t5 ON NOT EXISTS (SELECT patient_id FROM tbl_admissions t_a1 WHERE t_a1.account_id = $visit_id) AND t5.id = t1.invoice_id AND t5.account_number_id = $visit_id
 			   INNER JOIN tbl_insuarance_item_mapping t2 ON t2.gothomis_item_id = t1.item_id           
			   INNER JOIN tbl_insuarance_items t3 ON t3.id=t2.nhif_item_id AND t3.item_type_id=1   
			   INNER JOIN tbl_insuarance_item_prices t4 ON t4.item_code=t3.item_code 
			   INNER JOIN tbl_accounts_numbers t6 ON   t6.id = t5.account_number_id and t6.scheme_id=t4.scheme_code
			   GROUP BY t4.item_code ";
		$consultations=DB::SELECT($sql);
		
		//Investigations
		$sql  ="SELECT t3.item_name,t3.item_code,t5.amount as item_price, 1 as quantity
				FROM tbl_orders t2
				INNER JOIN tbl_results t1 ON t2.order_id = t1.order_id AND t1.item_id = t2.test_id AND t1.confirmation_status = 1     
			    INNER JOIN tbl_requests t4 ON t4.id=t2.order_id AND DATE(t2.created_at) = DATE(t4.created_at)       
				INNER JOIN tbl_insuarance_item_mapping t7 ON t7.gothomis_item_id = t2.test_id           
				INNER JOIN tbl_insuarance_items t3 ON t3.id=t7.nhif_item_id    
				INNER JOIN tbl_insuarance_item_prices t5 ON t3.item_code=t5.item_code 
				INNER JOIN tbl_accounts_numbers t6 ON   t6.id = t4.visit_date_id AND t6.scheme_id=t5.scheme_code          
				WHERE t4.visit_date_id='".$visit_id."' GROUP BY t3.item_code";
		$investigations=DB::SELECT($sql); 
		
		//Diagnoses
		$sql="SELECT t3.code, t2.status, users.name, users.practioner_no, tbl_proffesionals.prof_name as proffesion 
				FROM tbl_diagnoses t1 
				INNER JOIN tbl_diagnosis_details t2 ON   t2.diagnosis_id = t1.id
				INNER JOIN tbl_diagnosis_descriptions t3 ON   t2.diagnosis_description_id =t3.id
				INNER JOIN users ON users.id = t1.user_id
				INNER JOIN tbl_proffesionals on users.proffesionals_id = tbl_proffesionals.id
				WHERE t1.visit_date_id='".$visit_id."' and t2.status = 'confirmed'";
		$diagnoses=DB::SELECT($sql); 
		$illinesses = "";
		foreach($diagnoses as $diagnosis)
			$illinesses .=$diagnosis->code.", ";// "(".$diagnosis->status."),";
		//Clinician
		$clinician  = count($diagnoses) > 0 ? $diagnoses[0] : null;

		//Prescriptions
		$sql="SELECT t2.item_name,t2.item_code,t3.amount AS item_price, SUM(t1.quantity) as quantity
			   FROM tbl_prescriptions t1 
			   INNER JOIN tbl_insuarance_item_mapping t7 ON t7.gothomis_item_id = t1.item_id     
			   INNER JOIN tbl_insuarance_items t2 ON t2.id=t7.nhif_item_id    
			   INNER JOIN tbl_insuarance_item_prices t3 ON t3.item_code=t2.item_code    
			   INNER JOIN tbl_accounts_numbers t4 ON   t4.id = t1.visit_id AND t4.scheme_id=t3.scheme_code
			   WHERE t1.visit_id='".$visit_id."' AND t1.dispensing_status=1 GROUP BY t2.item_name,t2.item_code,t3.amount";
		$prescriptions=DB::SELECT($sql); 

		//Admission
		$sql="SELECT t1.admission_status_id,t3.ward_name,t4.item_code,CONCAT(t4.item_name, ' (FROM ',DATE(t1.created_at), ' TO ',DATE(t7.updated_at),')') AS item_name, DATE(t1.created_at) AS admission_date,t5.amount AS item_price, CASE WHEN abs(timestampdiff(day, t7.created_at, t1.admission_date)) = 0 THEN 1 ELSE abs(timestampdiff(day, t7.created_at, t1.admission_date)) END as quantity
				FROM  tbl_admissions t1
				INNER JOIN tbl_instructions t2 ON t2.admission_id=t1.id
				INNER JOIN tbl_wards t3 ON t2.ward_id=t3.id
				INNER JOIN tbl_insuarance_item_mapping t8 ON t8.gothomis_item_id = t3.ward_class_id     
				INNER JOIN tbl_insuarance_items t4 ON t4.id=t8.nhif_item_id    
				INNER JOIN tbl_insuarance_item_prices t5 ON t5.item_code=t4.item_code    
				INNER JOIN tbl_accounts_numbers t6 ON   t6.id = t1.account_id AND t6.scheme_id=t5.scheme_code 
				INNER JOIN  tbl_discharge_permits t7 ON t7.admission_id=t1.id AND t7.confirm=1
				WHERE t1.account_id='".$visit_id."'
			    GROUP BY t1.account_id";
		$admissions= DB::SELECT($sql);
			 
		
		//Procedures
		$sql="SELECT t2.item_name,t2.item_code,t3.amount AS item_price, COUNT(*) as quantity
				FROM  tbl_patient_procedures  t1 
				INNER JOIN tbl_insuarance_item_mapping t7 ON t7.gothomis_item_id = t1.item_id     
				INNER JOIN tbl_insuarance_items t2 ON t2.id=t7.nhif_item_id    
				INNER JOIN tbl_insuarance_item_prices t3 ON t3.item_code=t2.item_code
				INNER JOIN tbl_accounts_numbers t4 ON   t4.id = t1.visit_date_id AND t4.scheme_id=t3.scheme_code 
				WHERE t1.visit_date_id='".$visit_id."' GROUP BY t2.item_name,t2.item_code,t3.amount";
		$procedures=DB::SELECT($sql);
		
		 //get details of clinician attended patient
		$sql_clinicians ="SELECT t2.id,name AS seen_by,t3.prof_name,t2.practioner_no FROM tbl_diagnoses t1
		       INNER JOIN users t2 ON t2.id=t1.user_id
			   INNER JOIN tbl_proffesionals t3 ON t3.id=t2.proffesionals_id
        	   WHERE t1.visit_date_id = '".$visit_id."'";
		$clinicians =DB::SELECT($sql_clinicians);

      $sql_labrequests ="SELECT t2.id,name AS seen_by,t3.prof_name,t2.practioner_no FROM tbl_requests t1
		       INNER JOIN users t2 ON t2.id=t1.doctor_id
			   INNER JOIN tbl_proffesionals t3 ON t3.id=t2.proffesionals_id
        	   WHERE t1.visit_date_id = '".$visit_id."'";
	   $lab_requests =DB::SELECT($sql_labrequests);
 		
          $doctor ="";
          $prof_name ="";
         if(count($clinicians)>0){
	     $doctor     = $clinicians[0]->seen_by;
	     $prof_name  = $clinicians[0]->prof_name;		 
	     $practioner_no  = $clinicians[0]->practioner_no;		 
	     $doctor_id  = $clinicians[0]->id;	
          $doctorSignature= readDoctorSignature($doctor_id);		 
           }
         if(count($lab_requests)>0){
	     $doctor    = $lab_requests[0]->seen_by;
	     $prof_name   = $lab_requests[0]->prof_name;	
	     $practioner_no   = $lab_requests[0]->practioner_no;	
         $doctor_id  = $clinicians[0]->id;	
         $doctorSignature= readDoctorSignature($doctor_id);		 
           }	
		
		$folioNo =createPatientVisitSerialNumber($visit_id);
		$sql="SELECT * FROM tbl_api_credentials t1 WHERE t1.active=1";
		$nhifFacilityCodes = DB::select($sql);
		$serialNo =$nhifFacilityCodes[0]->FacilityCode.'/'.$details->month_attended.'/'.$details->year_attended.'/'.$folioNo;
		
		$html = "<html><head><link rel='stylesheet' href='../public/css/bootstrap.css' type='text/css/>";
		$html .= "<script src='../public/css/bootstrap.min.js' type='text/javascript/></head><body>";
		$html .= "<div class='row' style='border:none'><table class='table' width='100%'>";
			$html .= "<tr><td class='col-xs-2' style='text-align: left'><br><img src='../public/img/nhif_logo.jpg' alt='nhif_logo' width='100px'></td><td class='col-xs-5' style='text-align: center'><br>  <p><strong>CONFIDENTIAL <br> THE NHIF-HEALTH PROVIDER IN/OUT PATIENT CLAIM FORM</strong></p></td><td class='col-xs-5' style='text-align: right'><br> <p>Form: NHIF 2A & B <br>Regulation 18(1)</p><br><p>Serial No.: $serialNo </p></td></tr>";
		$html .= "</table></div>";

		$html .= "<strong>A:PARTICULARS.</strong><br>";
		$html .= "<table  width='100%' border='1' class='table table-bordered table-responsive table-striped'>";
			$html .= "<tr>";
					$html .= "<td colspan='5'>1.Name of Hospital/Health Center/Disp:&nbsp;&nbsp; ".$details->facility_name."</td>";
					$html .= "<td colspan='2'>2. Authorization No.&nbsp;&nbsp;".$details->authorization_number."</td>";
			$html .= "</tr>";
			$html .= "<tr>";
					$html .= "<td colspan='5'>3.Address:&nbsp;&nbsp;".$details->address."&nbsp;&nbsp;</td";
					$html .= "<td colspan='2'>4. Registration Fees:</td>";
			$html .= "</tr>";
			$html .= "<tr>";
					$html .= "<td colspan='5'>5.Name of Patient:&nbsp;&nbsp;".$details->first_name."  ".$details->middle_name." ".$details->last_name;
					$html .= "&nbsp;&nbsp;6.DOB:&nbsp;".$details->dob."&nbsp;";
					$html .= "&nbsp;7. Sex:&nbsp;&nbsp;".$details->gender."&nbsp;</td>";
					$html .= "<td colspan='2'>8. Card No.&nbsp;&nbsp;".$details->card_no."</td>";
			$html .= "</tr>";
			$html .= "<tr>";
					$html .= "<td colspan='5'>9.Occupation:&nbsp;&nbsp;".$details->occupation_name."&nbsp;&nbsp;";
					$html .= "10.&nbsp;&nbsp;Type of illness(codes):&nbsp;&nbsp;".$illinesses."&nbsp;</span></td>";
					$html .= "<td colspan='2'>11. Date of Attendance.&nbsp;&nbsp;".$details->date_attended."</td>";
			$html .= "</tr>";
			$html .= "<tr>";
					$html .= "<td colspan='3'>12.Patient File Number:&nbsp;&nbsp;".$details->medical_record_number."&nbsp;&nbsp;";
					$html .= "<td colspan='4'>13.Physical Address:&nbsp;&nbsp;".$details->residence_name."&nbsp;&nbsp;";
					$html .= "</tr>";
		$html .= "</table>";
			
		$html .= "<br><strong>B:COST OF SERVICES</strong> <br>";

		$html .= "<table  width='100%' border='1'  class='table table-bordered table-responsive table-striped'>";
		$html .= "<tr><th colspan='3'>Description</th><th>Item Code</th><th>Qty</th><th>Unit price</th><th>Amount</th></tr>";
		$html .= "<tbody>";
				//consultation
				$total = 0;
				$grand_total = 0;
				$subtotal =0;
				$html .= "<tr><th colspan='7'>Consultation</th></tr>";
				foreach($consultations as $item){
					$subtotal=$item->item_price *$item->quantity;
					$total += $subtotal; 
					$html .= "<tr><td colspan='3' >$item->item_name</td><td>$item->item_code </td><td>$item->quantity</td><td>".number_format($item->item_price,2)."</td><td>".number_format($item->item_price*$item->quantity,2)."</td></tr>";
				}
				$html .= "<tr><td colspan='6'>SUB TOTAL</td><td >".number_format($total,2)."</td></tr>";
				
				// Investigations
				$grand_total += $total;
				$total = 0;
				$subtotal = 0;
				if(count($investigations) > 0){
					$html .= "<tr'><th colspan='7'>Investigation</th></tr>";
					foreach($investigations as $item){
						$subtotal=$item->item_price *$item->quantity;						 
						$total += $subtotal;
						$html .= "<tr><td colspan='3' >$item->item_name</td><td>$item->item_code </td><td>$item->quantity</td><td>".number_format($item->item_price,2)."</td><td>".number_format($item->item_price*$item->quantity,2)."</td></tr>";
					}
					$html .= "<tr><td colspan='6'>SUB TOTAL</td><td >".number_format($total,2)."</td></tr>";
				}
				
				// Medicine
				$grand_total += $total;
				$total = 0;
				$subtotal = 0;
				if(count($prescriptions) > 0){
					$html .= "<tr><th colspan='7'>Medicine</th></tr>";
					foreach($prescriptions as $item){
						$subtotal=$item->item_price *$item->quantity;
						$total += $subtotal;
						$html .= "<tr><td colspan='3' >".(strlen($item->item_name) > 40 ? substr($item->item_name,0,40)."..." : $item->item_name)."</td><td>$item->item_code </td><td>$item->quantity</td><td>".number_format($item->item_price,2)."</td><td>".number_format($item->item_price*$item->quantity,2)."</td></tr>";
					}
					$html .= "<tr><td colspan='6'>SUB TOTAL</td><td >".number_format($total,2)."</td></tr>";
				}
				
				// Admission
				$grand_total += $total;
				$total = 0;
				$subtotal = 0;
				if(count($admissions) > 0){
					$html .= "<tr'><th colspan='7'>In-Patient</th></tr>";
					foreach($admissions as $item){
						$subtotal=$item->item_price *$item->quantity;
						$total += $subtotal;
						$html .= "<tr><td colspan='3' >$item->item_name</td><td>$item->item_code </td><td>$item->quantity</td><td>".number_format($item->item_price,2)."</td><td>".number_format($item->item_price*$item->quantity,2)."</td></tr>";
					}
					$html .= "<tr><td colspan='6'>SUB TOTAL</td><td >".number_format($total,2)."</td></tr>";
				}

				

				// Procedures
				$grand_total += $total;
				$total = 0;
				$subtotal =0;
				if(count($procedures) > 0){
					$html .= "<tr'><th colspan='7'>Procedures</th></tr>";
					foreach($procedures as $item){
						$subtotal=$item->item_price *$item->quantity;
						$total += $subtotal;
						$html .= "<tr><td colspan='3' >$item->item_name</td><td>$item->item_code </td><td>$item->quantity</td><td>".number_format($item->item_price,2)."</td><td>".number_format($item->item_price*$item->quantity,2)."</td></tr>";
					}
					$html .= "<tr><td colspan='6'>SUB TOTAL</td><td style='background-color:#fffeed'>".number_format($total,2)."</td></tr>";
				}
				
				$grand_total += $total;

				//grand total
				$html .= "<tr><td colspan='6'>TOTAL</td><td>".number_format($grand_total,2)."</td></tr>";
		$html .= "</tbody>";
		$html .= "</table>";

		$html .= "<strong>C:Name of attending Clinician: &nbsp;".$doctor."</strong>&nbsp;&nbsp;Qualifications:&nbsp;".$prof_name."&nbsp;&nbsp;Reg No.:".($clinician != null ? $practioner_no: '')."&nbsp;&nbsp;Signature:<img src='".$doctorSignature."' alt='No signature' width='80px'><br><strong>D:Patient Certification:</strong> <br><p>I certify that I received the above named services.&nbsp;&nbsp;Name:&nbsp;&nbsp;".$details->first_name." ".$details->middle_name." ".$details->last_name."&nbsp;&nbsp;Tel.No:&nbsp;&nbsp;".$details->mobile_number."&nbsp;&nbsp;Signature: <img src='".readClientsignature($visit_id)."' alt='No signature' width='80px'></p>";

		$html .= "<strong>E: Description of Out/In patient Management/any other additional information(a separate sheet of paper can be used):</strong>............................................................................................................................................................................<br><br>";

		$html .= "<strong>NB: Fill in Triplicate and please submit the original form on monthly basis, and the claim be attached with Monthly Report.Any falsified information may subject you to prosecution in accordaance with NHIF Act No. 8 0f 1999</strong>";

		$html .= "</tr></table>";
		$html .= "</body></html>";
		
		
		$dompdf = App::make('dompdf.wrapper');
		$dompdf->loadHtml($html);
		$dompdf->setPaper('a4', 'potrait')->setWarnings(false);
		
		return $dompdf->save('../public/nhif_files/claim_file.pdf');
     }
}


//Patient Files...
if (!function_exists('createPdfPatientFileToNhif')) {

    function createPdfPatientFileToNhif($visit_id){
       $details ="SELECT t1.*, TIMESTAMPDIFF(YEAR,t1.dob, CURRENT_DATE) AS age, t3.facility_code, t3.facility_name, t3.address, t2.account_number, t2.id AS account_id, t2.card_no, t2.authorization_number, t2.membership_number, t2.date_attended,MONTH(t2.date_attended) AS month_attended,YEAR(t2.date_attended) AS year_attended,t6.occupation_name, residence_name
		   FROM tbl_patients t1
		   INNER JOIN tbl_accounts_numbers t2 ON t2.id=$visit_id AND t1.id=t2.patient_id
		   INNER JOIN tbl_bills_categories t5 ON t2.id=t5.account_id
		   INNER JOIN users t4 ON t4.id=t2.user_id
		   INNER JOIN tbl_facilities t3 ON t3.id=t2.facility_id   
		   INNER JOIN tbl_residences t7 ON t7.id=t1.residence_id  
		   LEFT JOIN tbl_occupations t6 ON t6.id=t1.occupation_id";
		$details = DB::select($details)[0];
	   
        $chiefComplaints = "select * from vw_history_examinations where visit_date_id = '".$visit_id."'
        AND description IS NOT NULL AND duration IS NOT NULL";
		$chiefComplaints = DB::select($chiefComplaints);
		
        $otherComplaints = "select * from vw_history_examinations where visit_date_id = '".$visit_id."'
        AND other_complaints IS NOT NULL";
		$otherComplaints = DB::select($otherComplaints);
		
        $hpi = "select * from vw_history_examinations where   visit_date_id = '".$visit_id."'
        AND hpi IS NOT NULL";
		$hpi = Db::select($hpi);
		
		
		//Investigations
		$sql  ="SELECT t3.item_name,t3.item_code,t5.amount as item_price, 1 as quantity, t1.description as result
			  FROM tbl_orders t2
			  INNER JOIN tbl_results t1 ON t2.order_id = t1.order_id AND t1.item_id = t2.test_id AND t1.confirmation_status = 1    
			  INNER JOIN tbl_requests t4 ON t4.id=t2.order_id AND DATE(t2.created_at) = DATE(t4.created_at)     
			  INNER JOIN tbl_insuarance_item_mapping t7 ON t7.gothomis_item_id = t2.test_id           
			  INNER JOIN tbl_insuarance_items t3 ON t3.id=t7.nhif_item_id    
			  INNER JOIN tbl_insuarance_item_prices t5 ON t3.item_code=t5.item_code 
			  INNER JOIN tbl_accounts_numbers t6 ON t6.id = t4.visit_date_id AND t6.scheme_id=t5.scheme_code          
			  WHERE t4.visit_date_id='".$visit_id."' GROUP BY t3.item_code";
		$investigations=DB::SELECT($sql); 
		
		
		//Diagnoses
		$sql="SELECT t3.code, t2.status, users.name, users.practioner_no, tbl_proffesionals.prof_name as proffesion 
				FROM tbl_diagnoses t1 
				INNER JOIN tbl_diagnosis_details t2 ON   t2.diagnosis_id =t1.id
				INNER JOIN tbl_diagnosis_descriptions t3 ON   t2.diagnosis_description_id =t3.id
				INNER JOIN users ON users.id = t1.user_id
				INNER JOIN tbl_proffesionals on users.proffesionals_id = tbl_proffesionals.id
				WHERE t1.visit_date_id='".$visit_id."' and t2.status = 'confirmed'";
		$diagnoses=DB::SELECT($sql);
		$illinesses = "";
		foreach($diagnoses as $diagnosis)
			$illinesses .= $illinesses.$diagnosis->code.",";
		//Clinician
		$clinician  = count($diagnoses) > 0 ? $diagnoses[0] : null;
		
        
		//Prescriptions
		$sql="SELECT t1.*, t2.item_name,t2.item_code,t3.amount AS item_price, SUM(t1.quantity) as quantity
			   FROM tbl_prescriptions t1 
			   INNER JOIN tbl_insuarance_item_mapping t7 ON t7.gothomis_item_id = t1.item_id     
			   INNER JOIN tbl_insuarance_items t2 ON t2.id=t7.nhif_item_id    
			   INNER JOIN tbl_insuarance_item_prices t3 ON t3.item_code=t2.item_code    
			   INNER JOIN tbl_accounts_numbers t4 ON   t4.id = t1.visit_id AND t4.scheme_id=t3.scheme_code
			   WHERE t1.visit_id='".$visit_id."' AND t1.dispensing_status=1 GROUP BY t2.item_name,t2.item_code,t3.amount";
		$prescriptions=DB::SELECT($sql);

		//Procedures
		$sql="SELECT t2.item_name,t2.item_code,t3.amount AS item_price, COUNT(*) as quantity
				FROM  tbl_patient_procedures  t1 
				INNER JOIN tbl_insuarance_item_mapping t7 ON t7.gothomis_item_id = t1.item_id     
				INNER JOIN tbl_insuarance_items t2 ON t2.id=t7.nhif_item_id    
				INNER JOIN tbl_insuarance_item_prices t3 ON t3.item_code=t2.item_code
				INNER JOIN tbl_accounts_numbers t4 ON   t4.id = t1.visit_date_id AND t4.scheme_id=t3.scheme_code 
				WHERE t1.visit_date_id='".$visit_id."' GROUP BY t2.item_name,t2.item_code,t3.amount";
		$procedures=DB::SELECT($sql);
		
		$folioNo =createPatientVisitSerialNumber($visit_id);
		$sql="SELECT * FROM tbl_api_credentials t1 WHERE t1.active=1";
		$nhifFacilityCodes = DB::select($sql);
		$serialNo =$nhifFacilityCodes[0]->FacilityCode.'/'.$details->month_attended.'/'.$details->year_attended.'/'.$folioNo;
	
	     //get details of clinician attended patient
		$sql_clinicians ="SELECT t2.id,name AS seen_by,t3.prof_name FROM tbl_diagnoses t1
		       INNER JOIN users t2 ON t2.id=t1.user_id
			   INNER JOIN tbl_proffesionals t3 ON t3.id=t2.proffesionals_id
        	   WHERE t1.visit_date_id = '".$visit_id."'";
		$clinicians =DB::SELECT($sql_clinicians);

      $sql_labrequests ="SELECT t2.id,name AS  seen_by,t3.prof_name FROM tbl_requests t1
		       INNER JOIN users t2 ON t2.id=t1.doctor_id
			   INNER JOIN tbl_proffesionals t3 ON t3.id=t2.proffesionals_id
        	   WHERE t1.visit_date_id = '".$visit_id."'";
	   $lab_requests =DB::SELECT($sql_labrequests);
 		
          $doctor ="";
          $prof_name ="";
         if(count($clinicians)>0){
	     $doctor     = $clinicians[0]->seen_by;
	     $prof_name  = $clinicians[0]->prof_name;
          $doctor_id  = $clinicians[0]->id;	
          $doctorSignature= readDoctorSignature($doctor_id);		 
           }
         if(count($lab_requests)>0){
	     $doctor    = $lab_requests[0]->seen_by;
	     $prof_name   = $lab_requests[0]->prof_name;	
          $doctor_id  = $clinicians[0]->id;	
          $doctorSignature= readDoctorSignature($doctor_id);		 
           }	
		//$serialNo = DB::select("SELECT CONCAT(facilitycode,'/',LPAD(MONTH(t1.date_attended),2,0), '/',YEAR(t1.date_attended),'/', LPAD(COUNT(*)+1,5,0)) AS serialNo FROM (tbl_accounts_numbers t1 JOIN tbl_accounts_numbers t2 ON t2.id = $visit_id AND t1.is_submitted = 1 AND YEAR(t1.date_attended) AND YEAR(t2.date_attended) AND MONTH(t1.date_attended) = MONTH(t2.date_attended)), tbl_api_credentials")[0]->serialNo;
		
		$html = "<html><head><link rel='stylesheet' href='../public/css/bootstrap.css' type='text/css/>";
		$html .= "<script src='../public/css/bootstrap.min.js' type='text/javascript/></head><body>";
		$html .= "<div class='row' style='border:none'><table class='table' width='100%'>";
		$html .= "<tr><td class='col-xs-2' style='text-align: left'><br><img src='../public/img/nhif_logo.jpg' alt='nhif_logo' width='100px'></td><td class='col-xs-5' style='text-align: center'><br>  <p><strong>CONFIDENTIAL <br> THE NHIF-HEALTH PROVIDER IN/OUT PATIENT FILE</strong></p></td><td class='col-xs-5' style='text-align: right'><br> <p>Form: NHIF 2A & B <br>Regulation 18(1)</p><br><p>Serial No.: $serialNo</p></td></tr>";
		$html .= "</table></div>";

		$html .= "<strong>PARTICULARS</strong><br>";
		$html .= "<table  width='100%' border='1' class='table table-bordered table-responsive table-striped'>";
			$html .= "<tr>";
					$html .= "<td colspan='5'>1.Name of Hospital/Health Center/Disp:&nbsp;&nbsp; ".$details->facility_name."</td>";
					$html .= "<td colspan='2'>2. Authorization No.&nbsp;&nbsp;".$details->authorization_number."</td>";
			$html .= "</tr>";
			$html .= "<tr>";
					$html .= "<td colspan='5'>3.Address:&nbsp;&nbsp;".$details->address."&nbsp;&nbsp;</td";
					$html .= "<td colspan='2'>4. Registration Fees:</td>";
			$html .= "</tr>";
			$html .= "<tr>";
					$html .= "<td colspan='5'>5.Name of Patient:&nbsp;&nbsp;".$details->first_name."  ".$details->middle_name." ".$details->last_name;
					$html .= "&nbsp;&nbsp;6.DOB:&nbsp;".$details->dob."&nbsp;";
					$html .= "&nbsp;7. Sex:&nbsp;&nbsp;".$details->gender."&nbsp;</td>";
					$html .= "<td colspan='2'>8. Card No.&nbsp;&nbsp;".$details->card_no."</td>";
			$html .= "</tr>";
			$html .= "<tr>";
					$html .= "<td colspan='5'>9.Occupation:&nbsp;&nbsp;".$details->occupation_name."&nbsp;&nbsp;";
					$html .= "10.&nbsp;&nbsp;Type of illness(codes):&nbsp;&nbsp;".$illinesses."&nbsp;</span></td>";
					$html .= "<td colspan='2'>11. Date of Attendance.&nbsp;&nbsp;".$details->date_attended."</td>";
			$html .= "</tr>";
			$html .= "<tr>";
					$html .= "<td colspan='3'>12.Patient File Number:&nbsp;&nbsp;".$details->medical_record_number."&nbsp;&nbsp;";
					$html .= "<td colspan='4'>13.Physical Address:&nbsp;&nbsp;".$details->residence_name."&nbsp;&nbsp;";
					$html .= "</tr>";
		$html .= "</table>";
		
		$html .= "<strong>COMPLAINS AND HPI</strong><br>";
        $html .= "<table  width='100%' border='1' class='table table-bordered table-responsive'>";
			//Chief Complaints
			$html .= "<tr><th colspan='5'> <p style='text-align: left'>Chief Complain</p></th></tr>";
			$html .= "<tr><th colspan='4'>Complain</th><th>Duration</th></tr>";
			foreach( $chiefComplaints AS $item ){
				$html .="<tr><td colspan='4' style='text-align: left'>$item->description</td><td>$item->duration , $item->duration_unit</td></tr>";
			}
			
			//Other complaints
			$html .= "<tr><th colspan='5'> <p style='text-align: left'>Other Complains</p></th></tr>";
			$html .= "<tr><td colspan='5' style='text-align: left'>";
			foreach( $otherComplaints AS $item ){
				$html .=$item->description."<br />";
			}
			$html .="</tr>";
			
			//HPI
			$html .= "<tr><th colspan='5' style='text-align: left'>History of Presenting Illiness</th></tr>";
			$html .= "<tr><td colspan='5' style='text-align: left'>";
			foreach( $hpi AS $item ){
				$html.=$item->description."<br />";
			}
			$html .="</tr>";
		$html .="</table>";
		
		if(count($investigations) > 0){
			$html .= "<strong>INVESTIGATIONS</strong><br>";
			$html .= "<table  width='100%' border='1' class='table table-bordered table-responsive'>";
				$html .= "<tr><td colspan='2' style='text-align: left'>Investigation</td><td colspan='2' style='text-align: left'>Results</td></tr>";
				foreach( $investigations AS $item ){
					$html.="<tr><td colspan='2' style='text-align: left'>$item->item_name</td>";
					$html.="<td colspan='2' style='text-align: left'>$item->result</td></tr>";
				}
			$html .="</table>";
		}
		
		$html .= "<strong>DIAGNOSES</strong><br>";
		$html .= "<table  width='100%' border='1' class='table table-bordered table-responsive'>";
			$html .= "<tr><td colspan='4' style='text-align: left'></tr>";
			foreach( $diagnoses AS $item ){
				$html.="<tr><td colspan='4' style='text-align: left'>$item->code ($item->status)</td></tr>";
			}
		$html .="</table>";
		
		if(count($prescriptions) > 0){
			$html .= "<strong>PRESCRIPTIONS</strong><br>";
			$html .= "<table  width='100%' border='1' class='table table-bordered table-responsive'>";
				$html .= "<tr><td style='text-align: left'>Item</td><td style='text-align: left'>Dosage</td><td style='text-align: left'>Frequency</td><td style='text-align: left'>Duration</td></tr>";
				$sno =0;
				foreach( $prescriptions AS $item ){
					$html.="<tr><td style='text-align: left'>".(strlen($item->item_name) > 40 ? substr(htmlspecialchars($item->item_name, ENT_QUOTES),0,40)."..." : htmlspecialchars($item->item_name, ENT_QUOTES))." ($item->item_code)</td>";
					$html.="<td>$item->dose</td><td>$item->frequency</td><td>$item->duration</td></tr>";
				}
			$html .="</table>";
		}
		
		if(count($procedures) > 0){
			$html .= "<strong>PROCEDURES</strong><br>";
			$html .= "<table  width='100%' border='1' class='table table-bordered table-responsive'>";
				foreach( $procedures AS $item ){
					$html.="<tr><td colspan='4' style='text-align: left'>$item->item_name</td></tr>";
				}
			$html .="</table>";
		}
		
		$html .= "<br /><hr /><strong>Name of attending Clinician: &nbsp;".$doctor."</strong>&nbsp;&nbsp;Qualifications:&nbsp;".$prof_name ."&nbsp;&nbsp;Reg No.:".($clinician != null ? $clinician->practioner_no: '')."&nbsp;&nbsp;Signature: <img src='".$doctorSignature."' alt='No signature' width='80px'><br><strong>";
     
		$dompdf = App::make('dompdf.wrapper');
		$dompdf->loadHtml($html);
		$dompdf->setPaper('a4', 'potrait')->setWarnings(false);
		
		return $dompdf->save('../public/nhif_files/patient_file.pdf');
     }
}





if (!function_exists('createClaimFolio')) {

function createClaimFolio($visit_id) {
	//make a summaries
	$gender = "";
	$consultation_amount = 0.00;
	$investigation_amount = 0.00;
	$prescription_amount = 0.00;
	$procedure_amount = 0.00;
	$admission_amount = 0.00;
	//				
	
	$responses=[];
	$patient_type_code="IN";
	$facility_code=null;
	$sql_fac="SELECT * FROM tbl_api_credentials t1 WHERE t1.active=1";
	$facilities=DB::SELECT($sql_fac); 

	if(count($facilities)>0){		 
		$facility_code=$facilities[0]->FacilityCode;
	}

	$get_facility_codes="SELECT FacilityCode from  tbl_api_credentials t1 WHERE active=1";
	$get_facility_codes=DB::SELECT($get_facility_codes);
	
	//make a record into summaries
	$summary = "CREATE TABLE IF NOT EXISTS claim_summary(
					id int auto_increment not null,
					visit_account_id int not null,
					gender varchar(6) not null,
					date_submitted date null,
					consultations decimal(10,2) not null default 0.00,
					investigations decimal(10,2) not null default 0.00,
					prescriptions decimal(10,2) not null default 0.00,
					procedures decimal(10,2) not null default 0.00,
					admissions decimal(10,2) not null default 0.00,
					primary key(id)
				)";
	DB::statement($summary);
		
    $sql="SELECT t1.* ,t3.facility_code,t2.account_number,card_no,authorization_number,membership_number,DATE(t2.created_at) AS attended_date,MONTH(t2.date_attended) AS month_attended,YEAR(t2.date_attended) AS year_attended,
          TIMESTAMPDIFF(YEAR,dob, CURRENT_DATE) AS age ,t4.practioner_no,t4.name  AS created_by,t2.created_at AS time_created,t2.updated_at AS last_modified                 
		FROM tbl_patients t1
		INNER JOIN tbl_accounts_numbers t2 ON t1.id=t2.patient_id
		INNER JOIN users t4 ON t4.id=t2.user_id
		INNER JOIN tbl_facilities t3 ON t3.id=t2.facility_id
		WHERE t2.id='".$visit_id."' LIMIT 1";
	$patient_details = DB::SELECT($sql);
	//for summary
	$gender = $patient_details[0]->gender;
	
	$responses[]=$patient_details; 

    $sql_1="SELECT t1.*,t3.code AS disease_code,t4.name  AS created_by,t1.created_at AS time_created,t1.updated_at AS last_modified   FROM tbl_diagnoses t1 
           INNER JOIN tbl_diagnosis_details t2 ON   t2.diagnosis_id =t1.id
           INNER JOIN tbl_diagnosis_descriptions t3 ON   t2.diagnosis_description_id =t3.id 
           INNER JOIN users t4 ON t4.id=t1.user_id
           WHERE t1.visit_date_id='".$visit_id."' GROUP BY disease_code";
   $responses[]=DB::SELECT($sql_1); 

	$bills = [];
	
    //Consultation
	$sql="SELECT t5.id AS bill_no, t3.item_name, t3.item_code, t4.amount AS unit_price, t1.quantity, (t1.quantity * t4.amount) AS amount_claimed, t1.created_at AS time_created, t1.updated_at AS last_modified  
		   FROM  tbl_invoice_lines  t1
		   INNER JOIN tbl_encounter_invoices t5 ON NOT EXISTS (SELECT patient_id FROM tbl_admissions t_a1 WHERE t_a1.account_id = $visit_id) AND t5.id = t1.invoice_id AND t5.account_number_id = $visit_id
		   INNER JOIN tbl_insuarance_item_mapping t2 ON t2.gothomis_item_id = t1.item_id           
		   INNER JOIN tbl_insuarance_items t3 ON t3.id=t2.nhif_item_id AND t3.item_type_id=1   
		   INNER JOIN tbl_insuarance_item_prices t4 ON t4.item_code=t3.item_code 
		   INNER JOIN tbl_accounts_numbers t6 ON t6.id = t5.account_number_id and t6.scheme_id=t4.scheme_code 
		   GROUP BY t3.item_code ";
	$consultations=DB::SELECT($sql);
	
	if(count($consultations) > 0){
		$bills = $consultations;
		
		foreach($consultations as $claim)
			$consultation_amount += $claim->amount_claimed;
	}
	
	//Investigations
	$sql  ="SELECT t1.invoice_id AS bill_no, t3.item_name, t3.item_code, t4.amount AS unit_price, t1.quantity, (t1.quantity * t4.amount) AS amount_claimed, t1.created_at AS time_created, t1.updated_at AS last_modified
			FROM tbl_orders t2
			INNER JOIN tbl_results t9 ON t2.order_id = t9.order_id AND t9.item_id = t2.test_id AND t9.confirmation_status = 1   
			INNER JOIN tbl_requests t5 ON t5.id=t2.order_id AND DATE(t2.created_at) = DATE(t5.created_at)       
			INNER JOIN tbl_insuarance_item_mapping t7 ON t7.gothomis_item_id = t2.test_id           
			INNER JOIN tbl_insuarance_items t3 ON t3.id=t7.nhif_item_id    
			INNER JOIN tbl_insuarance_item_prices t4 ON t3.item_code=t4.item_code 
			INNER JOIN tbl_accounts_numbers t6 ON t6.id = t5.visit_date_id AND t6.scheme_id=t4.scheme_code 
			INNER JOIN tbl_encounter_invoices t8 ON t8.account_number_id = t6.id
            INNER JOIN tbl_invoice_lines t1 ON t1.invoice_id = t8.id AND t1.item_id = t2.test_id
			WHERE t6.id='".$visit_id."' GROUP BY t3.item_code";
	$investigations=DB::SELECT($sql); 
	
	if(count($investigations) > 0){
		$bills = array_merge($bills , $investigations);
		
		foreach($investigations as $claim)
			$investigation_amount += $claim->amount_claimed;
	}
	
	//Prescriptions
	$sql="SELECT t1.invoice_id AS bill_no, t3.item_name, t3.item_code, t4.amount AS unit_price, SUM(t1.quantity) as quantity, (SUM(t1.quantity) * t4.amount) AS amount_claimed, t1.created_at AS time_created, t1.updated_at AS last_modified
			FROM tbl_prescriptions t9 
			INNER JOIN tbl_insuarance_item_mapping t7 ON t7.gothomis_item_id = t9.item_id     
			INNER JOIN tbl_insuarance_items t3 ON t3.id=t7.nhif_item_id    
			INNER JOIN tbl_insuarance_item_prices t4 ON t3.item_code=t4.item_code    
			INNER JOIN tbl_accounts_numbers t5 ON t5.id = t9.visit_id AND t5.scheme_id=t4.scheme_code
			INNER JOIN tbl_encounter_invoices t8 ON t8.account_number_id = t5.id
			INNER JOIN tbl_invoice_lines t1 ON t1.invoice_id = t8.id AND t1.item_id = t9.item_id
			WHERE t9.visit_id='".$visit_id."' AND t9.dispensing_status=1 GROUP BY t3.item_code";
	$prescriptions=DB::SELECT($sql); 
	
	if(count($prescriptions) > 0){
		$bills = array_merge($bills , $prescriptions);
		
		foreach($prescriptions as $claim)
			$prescription_amount += $claim->amount_claimed;
	}
		 
	
	//Procedures
	$sql="SELECT * FROM( SELECT t1.invoice_id AS bill_no, t3.item_name, t3.item_code, t4.amount AS unit_price, SUM(t1.quantity) as quantity, (SUM(t1.quantity) * t4.amount) AS amount_claimed, t1.created_at AS time_created, t1.updated_at AS last_modified
			FROM  tbl_patient_procedures  t9 
			INNER JOIN tbl_insuarance_item_mapping t7 ON t7.gothomis_item_id = t9.item_id     
			INNER JOIN tbl_insuarance_items t3 ON t3.id=t7.nhif_item_id    
			INNER JOIN tbl_insuarance_item_prices t4 ON t3.item_code=t4.item_code
			INNER JOIN tbl_accounts_numbers t5 ON t5.id = t9.visit_date_id AND t5.scheme_id=t4.scheme_code 
			INNER JOIN tbl_encounter_invoices t8 ON t8.account_number_id = t5.id
			INNER JOIN tbl_invoice_lines t1 ON t1.invoice_id = t8.id AND t1.item_id = t9.item_id
			WHERE t9.visit_date_id='".$visit_id."') AS TEMP WHERE bill_no IS NOT NULL";
	$procedures=DB::SELECT($sql);
	
	if(count($procedures) > 0){
		$bills = array_merge($bills , $procedures);
		
		foreach($procedures as $claim)
			$procedure_amount += $claim->amount_claimed;
	}

	//Admission
	/*
	$sql="SELECT t1.invoice_id AS bill_no, t3.item_name, t3.item_code, t4.amount AS unit_price, t1.quantity, (t1.quantity * t4.amount) AS amount_claimed, t1.created_at AS time_created, t1.updated_at AS last_modified
			FROM  tbl_admissions t10
			INNER JOIN tbl_instructions t2 ON t2.admission_id=t10.id
			INNER JOIN tbl_wards t9 ON t2.ward_id=t9.id
			INNER JOIN tbl_insuarance_item_mapping t8 ON t8.gothomis_item_id = t9.ward_class_id     
			INNER JOIN tbl_insuarance_items t3 ON t3.id=t8.nhif_item_id    
			INNER JOIN tbl_insuarance_item_prices t4 ON t4.item_code=t3.item_code    
			INNER JOIN tbl_accounts_numbers t5 ON t5.id = t10.account_id AND t5.scheme_id=t4.scheme_code 
			INNER JOIN  tbl_discharge_permits t7 ON t7.admission_id=t10.id AND t7.confirm=1
			INNER JOIN tbl_encounter_invoices t6 ON t6.account_number_id = t5.id
			INNER JOIN tbl_invoice_lines t1 ON t1.invoice_id = t6.id AND t1.item_id = t9.ward_class_id
			WHERE t5.id='".$visit_id."'
			GROUP BY t3.item_code";
	*/
	$sql="SELECT CONCAT($visit_id, '010101', t1.id) AS bill_no, t3.item_name, t3.item_code, t4.amount AS unit_price, CASE WHEN abs(timestampdiff(day, t7.created_at, t1.admission_date)) = 0 THEN 1 ELSE abs(timestampdiff(day, t7.created_at, t1.admission_date)) END AS quantity, (CASE WHEN abs(timestampdiff(day, t7.created_at, t1.admission_date)) = 0 THEN 1 ELSE abs(timestampdiff(day, t7.created_at, t1.admission_date)) END * t4.amount) AS amount_claimed, t1.created_at AS time_created, t7.updated_at AS last_modified
			FROM  tbl_admissions t1
			INNER JOIN tbl_instructions t2 ON t2.admission_id=t1.id
			INNER JOIN tbl_wards t9 ON t2.ward_id=t9.id
			INNER JOIN tbl_insuarance_item_mapping t8 ON t8.gothomis_item_id = t9.ward_class_id     
			INNER JOIN tbl_insuarance_items t3 ON t3.id=t8.nhif_item_id    
			INNER JOIN tbl_insuarance_item_prices t4 ON t4.item_code=t3.item_code    
			INNER JOIN tbl_accounts_numbers t5 ON t5.id = t1.account_id AND t5.scheme_id=t4.scheme_code 
			INNER JOIN  tbl_discharge_permits t7 ON t7.admission_id=t1.id AND t7.confirm=1
			WHERE t5.id='".$visit_id."'
			GROUP BY t3.item_code";
	$admissions= DB::SELECT($sql);
	
	if(count($admissions) > 0){
		$bills = array_merge($bills , $admissions);
		
		foreach($admissions as $claim)
			$admission_amount += $claim->amount_claimed;
	}
	
	$responses[] = $bills;
	
	//check if admitted...
	$admns="SELECT  DATE(t1.created_at) AS admission_date,
	DATE(t2.created_at) AS discharge_date
	FROM  tbl_admissions t1
	INNER JOIN tbl_discharge_permits t2 ON t1.id=t2.admission_id
	WHERE t1.account_id='".$visit_id."' LIMIT 1";
	$admssion_details= DB::SELECT($admns);
	if(count($admssion_details) >0 ){
		$admission_date=$admssion_details[0]->admission_date;
		$discharge_date=$admssion_details[0]->discharge_date;
	}
	else{
		$admission_date=null;
		$discharge_date=null;
		$patient_type_code="OUT";
	 }

	$sql_diagn = "select description from vw_history_examinations where   visit_date_id = '".$visit_id."'
	AND description IS NOT NULL AND duration IS NOT NULL AND duration_unit IS NOT NULL";


	$sql_diagn_1 = "
	select  other_complaints AS description from vw_history_examinations where  visit_date_id = '".$visit_id."'
	AND other_complaints IS NOT NULL ";

	//get details of clinician attended patient
	$sql_clinicians ="SELECT name AS seen_by,t3.prof_name,t2.practioner_no FROM tbl_diagnoses t1
	INNER JOIN users t2 ON t2.id=t1.user_id
	INNER JOIN tbl_proffesionals t3 ON t3.id=t2.proffesionals_id
	WHERE t1.visit_date_id = '".$visit_id."'";
	$clinicians =DB::SELECT($sql_clinicians);

	$sql_labrequests ="SELECT name AS  seen_by,t3.prof_name,t2.practioner_no FROM tbl_requests t1
	INNER JOIN users t2 ON t2.id=t1.doctor_id
	INNER JOIN tbl_proffesionals t3 ON t3.id=t2.proffesionals_id
	WHERE t1.visit_date_id = '".$visit_id."'";
	$lab_requests =DB::SELECT($sql_labrequests);
 		
	$doctor ="";
	$prof_name ="";
	if(count($clinicians)>0){
		$doctor     = $clinicians[0]->seen_by;
		$prof_name  = $clinicians[0]->prof_name;		 
		$practioner_no  = $clinicians[0]->practioner_no;		 
	}
	
	if(count($lab_requests)>0){
		$doctor    = $lab_requests[0]->seen_by;
		$prof_name   = $lab_requests[0]->prof_name;		 
		$practioner_no   = $lab_requests[0]->practioner_no;		 
	}			   
			   
		

        $sql_diagn_2 = "
        select hpi AS description from vw_history_examinations where   visit_date_id = '".$visit_id."'
        AND hpi IS NOT NULL GROUP BY description";
        $list_diagns_1=DB::SELECT($sql_diagn);
        $list_diagns_2=DB::SELECT($sql_diagn_1);
        $list_diagns_3=DB::SELECT($sql_diagn_2);
        $descripions='';
        $other_complaints='';
        $hpi='';
        if(count($list_diagns_1) >0){
          $descripions=$list_diagns_1[0]->description;
        }
        if(count($list_diagns_2) >0){
          $other_complaints=$list_diagns_2[0]->description;
        }
        if(count($list_diagns_3) >0){
          $hpi=$list_diagns_3[0]->description;
        }

        $patient_history=$descripions.','.$other_complaints.','.$hpi;

		$folioID=gen_uuid();
        $foliolist_array=array();		
        $patient_infos=array();
        $FolioItems=array();
        $FolioDiseases=array();
        $diseases=array();        
        $items_array =array();
        $patientInvestigationResults =array();
        //generate pdf file

        createPdfToNhif($visit_id);
        createPdfPatientFileToNhif($visit_id);
		
		$folioNo =createPatientVisitSerialNumber($visit_id);
		$sql="SELECT * FROM tbl_api_credentials t1 WHERE t1.active=1";
		$nhifFacilityCodes = DB::select($sql);
		
        //$entity_array =array();
        $entity_array["entities"]=array();      
        foreach($responses[0] as $row) {
			
			$serialNo =$nhifFacilityCodes[0]->FacilityCode.'/'.$row->month_attended.'/'.$row->year_attended.'/'.$folioNo;	
            $patient_infos["FolioID"]=$folioID;
            $patient_infos['FacilityCode']=$facility_code;
            $patient_infos['ClaimYear']=(int)$row->year_attended;
            $patient_infos['ClaimMonth']=(int)$row->month_attended;
            $patient_infos['FolioNo']= (int)$folioNo;           
            $patient_infos['SerialNo']=$serialNo;
	   // AS PROVIDED BY NHIF
            $patient_infos['CardNo']=$row->card_no;
            $patient_infos['FirstName']=$row->first_name;
            $patient_infos['LastName']=$row->last_name;
            $patient_infos['Gender']=ucfirst(strtolower($row->gender));
            $patient_infos['DateOfBirth']=$row->dob;
			      $patient_infos['Age']=(int)$row->age;
            $patient_infos['TelephoneNo']=$row->mobile_number;
            $patient_infos['PatientFileNo']=$row->medical_record_number;
            $patient_infos['ClinicalNotes']=$patient_history;
            $patient_infos['PatientFile']=null; //base64_encode(file_get_contents("../public/nhif_files/patient_file.pdf")); 
            $patient_infos['ClaimFile']=base64_encode(file_get_contents("../public/nhif_files/claim_file.pdf")); 
         
            
            $patient_infos['AuthorizationNo']=$row->authorization_number;
            $patient_infos['AttendanceDate']=$row->attended_date;
            $patient_infos['PatientTypeCode']= $patient_type_code;
            $patient_infos['DateAdmitted']=$admission_date;
            $patient_infos['DateDischarged']=$discharge_date;
            $patient_infos['PractitionerNo']=$practioner_no;  
            $patient_infos['CreatedBy']= $doctor;
            $patient_infos['DateCreated']=$row->time_created;
            $patient_infos['LastModifiedBy']=$doctor;
            $patient_infos['LastModified']=$row->last_modified;      
            $patient_infos['FolioDiseases']=array();
            $patient_infos['FolioItems']=array();
			foreach($responses[1] as $disease) {
                $diseases["FolioID"]=$folioID;
                $diseases['FolioDiseaseID']=gen_uuid();
                $diseases['DiseaseCode']=$disease->disease_code;
                $diseases['Remarks']=null;
                $diseases['Status']='Final';
                $diseases['CreatedBy']=$doctor;
                $diseases['DateCreated']=$disease->time_created;
                $diseases['LastModifiedBy']=$doctor; 
                $diseases['LastModified']=$disease->last_modified;                             
                array_push($patient_infos['FolioDiseases'],$diseases);
            }  
			
			foreach($responses[2] as $folio_item) {
                $items_array['FolioItemID']=gen_uuid();
                $items_array["FolioID"]=$folioID;                
                $items_array['BillNo']=$folio_item->bill_no;
                $items_array['ItemCode']=$folio_item->item_code;
                $items_array['ItemaName']=$folio_item->item_name;
                $items_array['OtherDetails']=null;
                $items_array['ItemQuantity']=(int)$folio_item->quantity;
                $items_array['UnitPrice']=(double)$folio_item->unit_price;
                $items_array['AmountClaimed']=(double)$folio_item->amount_claimed; 
                $items_array['ApprovalRefNo']=null;
                $items_array['CreatedBy']=$doctor;
                $items_array['DateCreated']=$folio_item->time_created;
                $items_array['LastModifiedBy']=$doctor;
                $items_array['LastModified']=$folio_item->last_modified;
                                        
                array_push($patient_infos['FolioItems'],$items_array);
            }  
		     
            array_push($foliolist_array,$patient_infos);
        }
        $entity_array["entities"]=$foliolist_array;
        $data_string=json_encode($entity_array,JSON_PRETTY_PRINT);
		
		file_put_contents('claim.txt', $data_string);
		
		//make a record or update the existing to catch any changes in the bill_no
		if(count(DB::select("select id from claim_summary where visit_account_id = $visit_id")) == 0){
			DB::statement("insert into claim_summary(gender, visit_account_id, consultations, investigations, prescriptions, procedures, admissions) select '$gender', $visit_id, $consultation_amount, $investigation_amount, $prescription_amount, $procedure_amount, $admission_amount");
		}else{
			DB::statement("update claim_summary set gender = '$gender', consultations = $consultation_amount, investigations = $investigation_amount, prescriptions = $prescription_amount, procedures = $procedure_amount, admissions = $admission_amount where visit_account_id = $visit_id");
		}
		
        return $data_string;
    }
 }
 
 //Summary form...
if (!function_exists('createClaimSummary')) {

    function createClaimSummary($facility_id, $start_date, $end_date){
		$start_date = date("Y-m-d", strtotime($start_date));
		$end_date = date("Y-m-d", strtotime($end_date));
		
		$facility ="SELECT t1.*, t3.council_name, t2.region_name
					   FROM tbl_facilities t1
					   INNER JOIN tbl_regions t2 ON t1.id=$facility_id AND t2.id=t1.region_id   
					   INNER JOIN tbl_councils t3 ON t3.id = t1.council_id";
		$facility = DB::select($facility)[0];
		
		//Beneficiaries
		$sql="SELECT LOWER(gender) as gender,COUNT(*) AS total FROM claim_summary where date_submitted BETWEEN '".$start_date."' AND '".$end_date."' GROUP BY gender";
		
		$result=DB::SELECT($sql);
		$beneficiaries = new \stdClass();
		$beneficiaries->male = 0;
		$beneficiaries->female = 0;
		foreach($result as $gender)
			if($gender->gender == "male")
				$beneficiaries->male = $gender->total;
			else
				$beneficiaries->female = $gender->total;
		
		//Consultation
		$sql="SELECT IFNULL(SUM(consultations),0) AS total, COUNT(*) AS total_patients FROM claim_summary where date_submitted BETWEEN '".$start_date."' AND '".$end_date."' and consultations <> 0";
		$consultations=DB::SELECT($sql)[0];
		
		//Investigations
		$sql="SELECT IFNULL(SUM(investigations),0) AS total, COUNT(*) AS total_patients FROM claim_summary where date_submitted BETWEEN '".$start_date."' AND '".$end_date."' and investigations <> 0";
		$investigations=DB::SELECT($sql)[0]; 
		
		//Prescriptions
		$sql="SELECT IFNULL(SUM(prescriptions),0) AS total, COUNT(*) AS total_patients FROM claim_summary where date_submitted BETWEEN '".$start_date."' AND '".$end_date."' and prescriptions <> 0";
		$prescriptions=DB::SELECT($sql)[0]; 

		//Admission
		$sql="SELECT IFNULL(SUM(admissions),0) AS total, COUNT(*) AS total_patients FROM claim_summary where date_submitted BETWEEN '".$start_date."' AND '".$end_date."' and admissions <> 0";
		$admissions= DB::SELECT($sql)[0];
			 
		
		//Procedures
		$sql="SELECT IFNULL(SUM(procedures),0) AS total, COUNT(*) AS total_patients FROM claim_summary where date_submitted BETWEEN '".$start_date."' AND '".$end_date."' and procedures <> 0";
		$procedures=DB::SELECT($sql)[0];
		
		$claims = new \stdClass();
		$claims->total = $consultations->total+$investigations->total
							+$prescriptions->total+$admissions->total
							+$procedures->total;
		$claims->total_patients = $beneficiaries->male + $beneficiaries->female;
		
		$claims->consultations = new \stdClass();
		$claims->consultations->total = $consultations->total;
		$claims->consultations->total_patients = $consultations->total_patients;
		
		$claims->investigations = new \stdClass();
		$claims->investigations->total = $investigations->total;
		$claims->investigations->total_patients = $investigations->total_patients;		
		
		$claims->prescriptions = new \stdClass();
		$claims->prescriptions->total = $prescriptions->total;
		$claims->prescriptions->total_patients = $prescriptions->total_patients;
		
		$claims->admissions = new \stdClass();
		$claims->admissions->total = $admissions->total;
		$claims->admissions->total_patients = $admissions->total_patients;
		
		$claims->procedures = new \stdClass();
		$claims->procedures->total = $procedures->total;
		$claims->procedures->total_patients = $procedures->total_patients;
		
		
		$html = "<html><head><link rel='stylesheet' href='../public/css/bootstrap.css' type='text/css/>";
		$html .= "<script src='../public/css/bootstrap.min.js' type='text/javascript/><style>.borderless td, .borderless th {border: none;}</head><body>";
		$html .= "<div class='row' style='border:none'><table class='borderless' width='100%'>";
			$html .= "<tr><td colspan='3' style='text-align: center'><img src='../public/img/nhif_logo.jpg' alt='nhif_logo' width='100px'></td></tr><tr><td colspan='3' style='text-align: center; font-weight:bold'><strong>NATIONAL HEALTH INSURANCE FUND</strong><br /><span style='text-align: center; font-style:italic'>Dedicated to providing quality health care to its beneficiaries</span></td></tr><tr><td class='col-xs-2'></td><td class='col-xs-7' style='text-align: center; font-weight:bold; vertical-align:top'><strong>MONTHLY REPORT FORM</strong></td><td class='col-xs-3'><span style='text-align: right; font-weight:bold; vertical-align:top'>Revised From NHIF 6 (Regulation 40)</span></td></tr>";
		$html .= "</table></div>";
		
		$html .= "<p>&nbsp;</p>";
		$html .= "<table  width='100%' class='table table-responsive table-bordered'>";
			$html .= "<tr>";
					$html .= "<td colspan='1' style='font-weight:bold'>1. ACCREDITION NUMBER: </td>";
					$html .= "<td colspan='3'>&nbsp;$facility->nhif_facility_code</td>";
			$html .= "</tr>";
			$html .= "<tr>";
					$html .= "<td colspan='1' style='font-weight:bold'>2. NAME OF FACILITY: </td>";
					$html .= "<td colspan='3'>$facility->facility_name</td>";
			$html .= "</tr>";
			$html .= "<tr>";
					$html .= "<td colspan='1' style='font-weight:bold'>3. ADDRESS: </td>";
					$html .= "<td colspan='3'>$facility->address</td>";
			$html .= "</tr>";
			$html .= "<tr>";
					$html .= "<td colspan='1' style='font-weight:bold'>&nbsp;&nbsp;&nbsp;&nbsp;REGION: </td>";
					$html .= "<td colspan='1'>$facility->region_name</td>";
					$html .= "<td colspan='1' style='font-weight:bold'>DISTRICT: </td>";
					$html .= "<td colspan='1'>$facility->council_name</td>";
			$html .= "</tr>";
			$html .= "<tr>";
					$html .= "<td colspan='1' style='font-weight:bold'>4. FACILITY OWNERSHIP: </td>";
					$html .= "<td colspan='3'>Government</td>";
			$html .= "</tr>";
			$html .= "<tr>";
					$html .= "<td colspan='1' style='font-weight:bold'>5. FACILITY CODE: </td>";
					$html .= "<td colspan='3'>04: Health Center</td>";
			$html .= "</tr>";
			$html .= "<tr>";
					$html .= "<td colspan='2' rowspan='3' style='font-weight:bold'>6. NUMBER OF BENEFICIARIES TREATED: </td>";
					$html .= "<td colspan='1' style='font-weight:bold'>Male: </td>";
					$html .= "<td colspan='1' style='font-weight:bold; text-align:right'>$beneficiaries->male</td>";
			$html .= "</tr>";
			$html .= "<tr>";
					$html .= "<td colspan='1' style='font-weight:bold;'>Female: </td>";
					$html .= "<td colspan='1' style='font-weight:bold; text-align:right'>$beneficiaries->female</td>";
			$html .= "</tr>";
			$html .= "<tr>";
					$html .= "<td colspan='1' style='font-weight:bold;'>Total: </td>";
					$html .= "<td colspan='1' style='font-weight:bold; text-align:right'>".($beneficiaries->male+$beneficiaries->female)."</td>";
			$html .= "</tr>";
			$html .= "<tr>";
					$html .= "<td colspan='2' style='font-weight:bold'>7. DATE OF TREATMENT:</td>";
					$html .= "<td colspan='1' style='font-weight:bold'>From: ".$start_date."</td>";
					$html .= "<td colspan='1' style='font-weight:bold'>To: ".$end_date."</td>";
			$html .= "</tr>";
			$html .= "<tr>";
					$html .= "<td colspan='2' style='font-weight:bold'>8. AMOUNT CLAIMED: </td";
					$html .= "<td colspan='2' style='font-weight:bold;text-align:right'>Tshs. ".number_format($claims->total, 2, ".", ",")."</td>";
			$html .= "</tr>";
			$html .= "</table>";
			$html .= "<p>&nbsp;</p>";
			$html .= "<table  width='100%' class='table table-bordered table-responsive'>";
			$html .= "<tr>";
					$html .= "<td colspan='5' style='font-weight:bold; text-align:center'>BREAKDOWN OF AMOUNT CLAIMED</td>";
			$html .= "</tr>";
			$html .= "<tr>";
					$html .= "<th colspan='1'></th>";
					$html .= "<th colspan='2'>SERVICE PROVIDED</th>";
					$html .= "<th colspan='1'>No. of Patients</th>";
					$html .= "<th colspan='1'>Amount (Tshs.)</th>";
			$html .= "</tr>";
			$html .= "<tr>";
					$html .= "<td colspan='1'>(i)</td>";
					$html .= "<td colspan='2'>Consultation Fees</td>";
					$html .= "<td colspan='1' style='text-align:right'>".number_format($claims->consultations->total_patients, 0, ".", ",")."</td>";
					$html .= "<td colspan='1' style='text-align:right'>".number_format($claims->consultations->total, 2, ".", ",")."</td>";
			$html .= "</tr>";
			$html .= "<tr>";
					$html .= "<td colspan='1'>(ii)</td>";
					$html .= "<td colspan='2'>Investigation Fees</td>";
					$html .= "<td colspan='1' style='text-align:right'>".number_format($claims->investigations->total_patients, 0, ".", ",")."</td>";
					$html .= "<td colspan='1' style='text-align:right'>".number_format($claims->investigations->total, 2, ".", ",")."</td>";
			$html .= "</tr>";
			$html .= "<tr>";
					$html .= "<td colspan='1'>(iii)</td>";
					$html .= "<td colspan='2'>Medicine & Other Supplies</td>";
					$html .= "<td colspan='1' style='text-align:right'>".number_format($claims->prescriptions->total_patients, 0, ".", ",")."</td>";
					$html .= "<td colspan='1' style='text-align:right'>".number_format($claims->prescriptions->total, 2, ".", ",")."</td>";
			$html .= "</tr>";
			$html .= "<tr>";
					$html .= "<td colspan='1'>(iv)</td>";
					$html .= "<td colspan='2'>Admission/Accomodation</td>";
					$html .= "<td colspan='1' style='text-align:right'>".number_format($claims->admissions->total_patients, 0, ".", ",")."</td>";
					$html .= "<td colspan='1' style='text-align:right'>".number_format($claims->admissions->total, 2, ".", ",")."</td>";
			$html .= "</tr>";
			$html .= "<tr>";
					$html .= "<td colspan='1'>(v)</td>";
					$html .= "<td colspan='2'>Procedures</td>";
					$html .= "<td colspan='1' style='text-align:right'>".number_format($claims->procedures->total_patients, 0, ".", ",")."</td>";
					$html .= "<td colspan='1' style='text-align:right'>".number_format($claims->procedures->total, 2, ".", ",")."</td>";
			$html .= "</tr>";
			$html .= "<tr>";
					$html .= "<th colspan='1'></th>";
					$html .= "<th colspan='2'>GRAND TOTAL</th>";
					$html .= "<th colspan='1' style='text-align:right'>".number_format($claims->total_patients, 0, ".", ",")."</th>";
					$html .= "<th colspan='1' style='text-align:right'>".number_format($claims->total, 2, ".", ",")."</th>";
			$html .= "</tr>";
		$html .= "</table>";
			
		$html .= "<p>&nbsp;</p>";
		$html .= "<table  width='100%' class='borderless'>";
			$html .= "<tr>";
					$html .= "<td colspan='1' style='font-weight:bold; width:45%'>9. NUMBER OF NHIF 2A &amp; BATCHED: </td>";
					$html .= "<td colspan='3' style='border-bottom:thin dashed black'>&nbsp;</td>";
			$html .= "</tr>";
			$html .= "<tr>";
					$html .= "<td colspan='1' style='font-weight:bold; width:45%'>10. PAYEE&apos;S NAME: </td>";
					$html .= "<td colspan='3' style='border-bottom:thin dashed black'>&nbsp;</td>";
			$html .= "</tr>";
			$html .= "<tr>";
					$html .= "<td colspan='4' style='font-weight:bold'>11. FACILITY INCHARGE: </td>";
			$html .= "</tr>";
			$html .= "<tr>";
					$html .= "<td colspan='1' style='font-weight:bold; width:45%'>&nbsp;&nbsp;&nbsp;&nbsp;NAME: </td>";
					$html .= "<td colspan='3' style='border-bottom:thin dashed black'>&nbsp;</td>";
			$html .= "</tr>";
			$html .= "<tr>";
					$html .= "<td colspan='1' style='font-weight:bold; width:45%'>&nbsp;&nbsp;&nbsp;&nbsp;SIGNATURE: </td>";
					$html .= "<td colspan='3' style='border-bottom:thin dashed black'>&nbsp;</td>";
			$html .= "</tr>";
			$html .= "</table>";
		$html .= "</body></html>";
		
		
		$dompdf = App::make('dompdf.wrapper');
		$dompdf->loadHtml($html);
		$dompdf->setPaper('a4', 'potrait')->setWarnings(false);
		
		return $dompdf->save('../public/nhif_files/claim_summary.pdf');	
	}
}
?>