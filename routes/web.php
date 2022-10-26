<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    return view('index');
});
Route::get('/info', function() {
    return phpinfo();
});

Route::group(['prefix' => '/test'], function () {
	Route::get('signature', 'Integrations\GePG\SendBillDeamon@testSignature');
});

Route::group(['prefix' => 'lab/api'], function()
{
Route::get('lab-api','laboratory\LaboratoryController@labAPI');
Route::post('post-lab-api','laboratory\LaboratoryController@PostLabAPI');
});

Route::group(['prefix' => 'dashboard'], function () {
	Route::get('reporting/{facility_id}'	,	function($facility_id){
		$request = new \Illuminate\Http\Request(['facility_id'=>$facility_id]);
		$call = new \App\Http\Controllers\Integrations\Dashboard\Data\DashboardReportingController($request);
		return $call->computeAndSend();
	});
});

//server request routes
Route::group(['prefix' => 'gepg/gepg_handler/new/'], function()
{
    Route::post('send_bill', 'Integrations\GePG\FacilityRequestsHandler@receiveBillRequest');
    //Route::post('cancel_bill','Integrations\GePG\FacilityRequestsHandler@cancellingRequest');
    Route::post('cancel_bill','Integrations\GePG\FacilityRequestsHandler@gepgCancelBill');
    Route::post('reconcile', function(){
		return array("success"=>1, "generic"=>"Reconcilliation request received. In about a few minutes, a reconcilliation message will pop up on your screen","real"=> "");
	});
    Route::post('reconcile_batch','Integrations\GePG\FacilityRequestsHandler@receiveReconcilliationRequest');
    
	Route::post('pending_details','Integrations\GePG\FacilityRequestsHandler@downloadPendingControlNumbersAndPayments');
    Route::post('pending_recons','Integrations\GePG\FacilityRequestsHandler@downloadPendingReconcilliations');
	
    Route::post('successful_details','Integrations\GePG\FacilityRequestsHandler@markSuccessfullyDownloadedControlNumbersAndPayments');
    Route::post('successful_recons','Integrations\GePG\FacilityRequestsHandler@markSuccessfullyDownloadedReconcillations');
	
	
    Route::get('check', 'Integrations\GePG\FacilityRequestsHandler@checkConnectivity');
});

Route::group(['prefix' => 'gepg/gepg_handler/test/'], function()
{
	Route::post('send_bill', 'Integrations\GePG\GLite\GothomisLiteRequestHandler@receiveBillRequest');
    Route::post('cancel_bill','Integrations\GePG\GLite\GothomisLiteRequestHandler@gepgCancelBill');
    Route::post('reconcile', function(){
		return array("success"=>1, "generic"=>"Reconcilliation request received. In about a few minutes, a reconcilliation message will pop up on your screen","real"=> "");
	});
	
	Route::post('pending_details','Integrations\GePG\GLite\GothomisLiteRequestHandler@downloadPendingControlNumbersAndPayments');
    Route::post('pending_recons','Integrations\GePG\GLite\GothomisLiteRequestHandler@downloadPendingReconcilliations');
	
    Route::post('successful_details','Integrations\GePG\GLite\GothomisLiteRequestHandler@markSuccessfullyDownloadedControlNumbersAndPayments');
    Route::post('successful_recons','Integrations\GePG\GLite\GothomisLiteRequestHandler@markSuccessfullyDownloadedReconcillations');
	
	
    Route::get('check', 'Integrations\GePG\GLite\GothomisLiteRequestHandler@checkConnectivity');
    Route::get('repairRecons', 'Integrations\GePG\GLite\GothomisLiteRequestHandler@reppairRecons');
});

//client routes
Route::group(['prefix' => 'gepg/new/'], function()
{
    Route::get('getPaymentOption', 'Integrations\GePG\GePG@getPaymentOption');
    Route::post('changePayOption', 'Integrations\GePG\GePG@changePayOption');
    Route::post('facility/configuration', 'Integrations\GePG\GePG@facilityConfiguration');
    
	Route::post('send_bill','Integrations\GePG\GePG@gepgSendBill');
    Route::post('cancel_bill','Integrations\GePG\GePG@gepgCancelBill');
    Route::post('reconcile', function(){
		return array("success"=>1, "generic"=>"Reconcilliation request received. In about a few minutes, a reconcilliation message will pop up on your screen","real"=> "");
	});
	
    Route::post('reconciled_bills','Integrations\GePG\GePG@downloadPendingRecons');
	Route::post('pending_bills','Integrations\GePG\GePG@downloadPendingControlNumbersAndPayments');
	
    Route::post('mark_processed_bill','Integrations\GePG\GePG@gepgMarkProcessedBills');
    Route::post('getGePGPaidBill','Integrations\GePG\GePG@getGePGPaidBill');
    Route::post('getBill','Integrations\GePG\GePG@getBill');
    Route::post('rollback','Integrations\GePG\GePG@rollback');
    Route::post('getCashDeposits','Integrations\GePG\GePG@cashDepositTrail');
    Route::get('applyStoredReconcs','Integrations\GePG\GePG@applyStoredReconcs');
    Route::post('printBill','Integrations\GePG\GePG@printBill');
    Route::post('resendBill','Integrations\GePG\GePG@resendBill');
});

//gepg MOF callback Routes
Route::post('gothomis/api/gepg/bill/testPayload', 'Integrations\GePG\GePGHandler@testPayload');
Route::post('gothomis/api/gepg/bill/gepgbillsubresp', 'Integrations\GePG\GePGHandler@gepgBillSubResp');
Route::post('gothomis/api/gepg/bill/gepgpmtspinfo', 'Integrations\GePG\GePGHandler@gepgPmtSpInfo');
Route::post('gothomis/api/gepg/reconciliation/gepgspreconcresp', 'Integrations\GePG\GePGHandler@gepgSpReconcResp');



//gepg MOF callback Routes for gothomis lite testing
Route::post('glite/api/gepg/bill/gepgbillsubresp', 'Integrations\GePG\GLite\GePGGLiteHandler@gepgBillSubResp');
Route::post('glite/api/gepg/bill/gepgpmtspinfo', 'Integrations\GePG\GLite\GePGGLiteHandler@gepgPmtSpInfo');
Route::post('glite/api/gepg/reconciliation/gepgspreconcresp', 'Integrations\GePG\GLite\GePGGLiteHandler@gepgSpReconcResp');

Route::group(['prefix' => 'gepg'], function()
{
	Route::get('check', 'Integrations\GePG\FacilityRequestsHandler@checkConnectivity');
    Route::get('facility/configuration/govnet', 'Integrations\GePG\GePG@useGovNETAddress');
    Route::get('facility/configuration/internet', 'Integrations\GePG\GePG@useInternetAddress');
	Route::post('gepg_handler/gepg_send_bill', 'Integrations\GePG\_GePG_Handler@gepgSendBill');
    Route::post('gepg_handler/gepg_cancel_bill','Integrations\GePG\_GePG_Handler@gepgCancelBill');
    Route::post('gepg_handler/gepg_check_pending_bills','Integrations\GePG\_GePG_Handler@gepgCheckPendingBills');
    Route::post('gepg_handler/temp/gepg_check_pending_bills','Integrations\GePG\_GePG_Handler@new_gepgCheckPendingBills');
    Route::post('gepg_handler/temp/build_recons','Integrations\GePG\temp_GePG_Handler@buildRecon');
    Route::post('gepg_handler/gepg_check_paid_bills','Integrations\GePG\_GePG_Handler@gepgPaidBills');
    Route::post('gepg_handler/gepg_mark_processed_bills','Integrations\GePG\_GePG_Handler@gepgMarkProcessedBills');
    Route::post('gepg_handler/gepg_reconciled','Integrations\GePG\_GePG_Handler@gepgReconcile');
    Route::post('gepg_handler/gepg_reconciled_bills','Integrations\GePG\_GePG_Handler@gepgReconciledBills');
    Route::get('gepg_handler/gepg_batch_reconcilliations','Integrations\GePG\_GePG_Handler@gepgBatchReconcilliations');
});

//end gepg routes

//For testing purposes. This was done so that the url set at gepg test server be used also in the live transactions.
//not here the request is redirected to the test app using a currl request
Route::post('gothomis/api/gepg/bill/gepgbillsubresp/test', function()
{
	$ch = curl_init("http://localhost:81/gothomis/api/gepg/bill/gepgbillsubresp");
	curl_setopt($ch, CURLOPT_POST, 1);
	curl_setopt($ch, CURLOPT_POSTFIELDS, file_get_contents("php://input"));
	curl_setopt($ch,  CURLOPT_RETURNTRANSFER,1);
	curl_setopt($ch, CURLOPT_HTTPHEADER,array('Gepg-Com: default.sp.in','Content-Type: application/xml'));
	return curl_exec($ch);
});

Route::post('gothomis/api/gepg/bill/gepgbillsubresp/test', function()
{
	$ch = curl_init("http://localhost:81/gothomis/api/gepg/bill/gepgbillsubresp");
	curl_setopt($ch, CURLOPT_POST, 1);
	curl_setopt($ch, CURLOPT_POSTFIELDS, file_get_contents("php://input"));
	curl_setopt($ch,  CURLOPT_RETURNTRANSFER,1);
	curl_setopt($ch, CURLOPT_HTTPHEADER,array('Gepg-Com: default.sp.in','Content-Type: application/xml'));
	return curl_exec($ch);
});


Route::post('gothomis/api/gepg/bill/gepgpmtspinfo/test', function()
{
	$ch = curl_init("http://localhost:81/gothomis/api/gepg/bill/gepgpmtspinfo");
	curl_setopt($ch, CURLOPT_POST, 1);
	curl_setopt($ch, CURLOPT_POSTFIELDS, file_get_contents("php://input"));
	curl_setopt($ch,  CURLOPT_RETURNTRANSFER,1);
	curl_setopt($ch, CURLOPT_HTTPHEADER,array('Gepg-Com: default.sp.in','Content-Type: application/xml'));
	return curl_exec($ch);
});


Route::post('gothomis/api/gepg/reconciliation/gepgspreconcresp/test', function()
{
	$ch = curl_init("http://localhost:81/gothomis/api/gepg/reconciliation/gepgspreconcresp");
	curl_setopt($ch, CURLOPT_POST, 1);
	curl_setopt($ch, CURLOPT_POSTFIELDS, file_get_contents("php://input"));
	curl_setopt($ch,  CURLOPT_RETURNTRANSFER,1);
	curl_setopt($ch, CURLOPT_HTTPHEADER,array('Gepg-Com: default.sp.in','Content-Type: application/xml'));
	return curl_exec($ch);
});

//NHIF new routes routes
Route::group(['prefix' => 'api'], function()
{
	Route::post('client-signature-pad','nhif\NhifController@saveClientsignature');
	Route::resource('client-registration'   , 'Nhif\RegController');
	Route::resource('nhif-item-price'       , 'Nhif\NhifPriceController');
	Route::resource('api-credential'    , 'Nhif\SetApiCredentialController');
	Route::resource('nhif-item'             , 'Nhif\NhifItemController');
	Route::resource('claim-submission'      , 'Nhif\ClaimSubmissionController');
	Route::resource('claim-bulk-submission'          , 'Nhif\ClaimBulkSubmissionController');
	Route::resource('client-card'           , 'Nhif\ClientCardController');
	Route::resource('offline-registration'  , 'Nhif\OfflineRegistrationController');
	Route::resource('patient-file'          , 'Nhif\PatientFileController');
	Route::resource('verified-claim'        , 'Nhif\VerifiedClaimController');
	Route::resource('submitted-claim'        , 'Nhif\SubmittedClaimController');
	Route::resource('consultation-service'   , 'Nhif\NhifConsultationServices');
	Route::resource('pre-approval-service'   , 'Nhif\NhifPreApprovalController');
	Route::resource('pre-approval-checkup'   , 'Nhif\NhifController');

	Route::post('getNHIFprices','nhif\NhifController@getNHIFprices');
	Route::post('map-facility-code','nhif\NhifController@mapFacilityCode');
	Route::post('mark-as-ok','nhif\NhifController@markAsOk');
	Route::post('verified-claims','nhif\NhifController@verifiedClaims');
	Route::post('send-bulk-claims','nhif\NhifController@sendBulkClaims');
	Route::post('get-non-collected-cards','nhif\NhifController@getNonCollectedCards');
	Route::post('give-cards','nhif\NhifController@giveCards');



	Route::post('getNhifServices','nhif\NhifController@getNhifServices');
	Route::post('getSystemServices','nhif\NhifController@getSystemServices');
	Route::post('mapServices','nhif\NhifController@mapServices');
	Route::post('getNHIFclaims','nhif\NhifController@getNHIFclaims');
	Route::post('getPostClaim','nhif\NhifController@getPostClaim');
	Route::post('getClaimsDetails','nhif\NhifController@getClaimsDetails');
	Route::post('getSubmittedNhifClaims','nhif\NhifController@getSubmittedNhifClaims');
	Route::post('getAmountsClaimed','nhif\NhifController@getAmountsClaimed');
	Route::post('getPatientsFiles','nhif\NhifController@getPatientsFiles');

	Route::post('generate-files','nhif\NhifController@generateFiles');
	Route::post('get-non-verified','nhif\NhifController@getNonVerified');
	Route::post('verify-nhif-card','nhif\NhifController@verifyNhifCard');
	
	Route::post('getMappedPrices', 'nhif\NhifController@getMappedPrices');

});
//END NHIF 

Route::group(['prefix' => 'api',  'middleware' => 'jwt.auth'], function()
{
    require_once("radiology_routes.php");
    require_once ("laboratory/index.php");
    require_once ("configuration/index.php");
    require_once ("item_prices.php");
    require_once ("items_route.php");
});

Route::group(['prefix' => 'api'], function()
{

//trauma routes
Route::get('getAccidentLocation/{id}', 'Trauma\TraumaController@getAccidentLocation');
Route::get('getAirwayPrimarySurvey/{id}'    , 'Trauma\TraumaController@getAirwayPrimarySurvey');
Route::get('getBreathingPrimarySurvey/{id}'    , 'Trauma\TraumaController@getBreathingPrimarySurvey');
Route::get('getCirculationPrimarySurvey/{id}'    , 'Trauma\TraumaController@getCirculationPrimarySurvey');
Route::get('getDisabilityPrimarySurvey/{id}'    , 'Trauma\TraumaController@getDisabilityPrimarySurvey');
Route::get('getExposurePrimarySurvey/{id}'    , 'Trauma\TraumaController@getExposurePrimarySurvey');
Route::get('getFastPrimarySurvey/{id}'    , 'Trauma\TraumaController@getFastPrimarySurvey');
Route::get('getPastMedicalHistory/{id}'    , 'Trauma\TraumaController@getPastMedicalHistory');
Route::get('getPastMedicalAllergyHistory/{id}'    , 'Trauma\TraumaController@getPastMedicalAllergyHistory');
Route::get('getTraumaHpi/{id}'    , 'Trauma\TraumaController@getTraumaHpi');
Route::get('getPhysicalExamination/{id}'    , 'Trauma\TraumaController@getPhysicalExamination');
Route::get('getInjuryMechanism/{id}'    , 'Trauma\TraumaController@getInjuryMechanism');
Route::get('getTraumaLabResults/{id}'    , 'Trauma\TraumaController@getTraumaLabResults');
Route::get('getTraumaImageResults/{id}'    , 'Trauma\TraumaController@getTraumaImageResults');
Route::get('getTraumaassesment/{id}'    , 'Trauma\TraumaController@getTraumaassesment');
Route::get('getTraumareassesment/{id}'    , 'Trauma\TraumaController@getTraumareassesment');
Route::get('getTraumaFluid/{id}'    , 'Trauma\TraumaController@getTraumaFluid');
Route::get('getTraumaProcedure/{id}'    , 'Trauma\TraumaController@getTraumaProcedure');
Route::post('saveAmmendedResult','laboratory\LaboratoryController@saveAmmendedResult');

Route::post('get-trauma-list'    , 'Trauma\TriageController@index');
Route::post('new-client'    , 'Trauma\TriageController@create');
Route::post('trauma-vitals'    , 'Trauma\TriageController@vitals');
Route::post('trauma-concepts'    , 'Trauma\TraumaController@traumaConcepts');
Route::post('triage-categories'    , 'Trauma\TraumaController@triageCategories');
Route::post('triage-arrival-modes'    , 'Trauma\TraumaController@triageArrivalModes');
Route::post('set-acuity'    , 'Trauma\TriageController@setAcuity');
Route::post('save-chief-complaint'    , 'Trauma\TraumaController@saveChiefComplaint');
Route::post('save-airway-primary-survey'    , 'Trauma\TraumaController@saveAirwayPrimarySurvey');
Route::post('save-breathing-primary-survey'    , 'Trauma\TraumaController@saveBreathingPrimarySurvey');
Route::post('save-circulation-primary-survey'    , 'Trauma\TraumaController@saveCirculationPrimarySurvey');
Route::post('save-disability-primary-survey'    , 'Trauma\TraumaController@saveDisabilityPrimarySurvey');
Route::post('save-exposure-primary-survey'    , 'Trauma\TraumaController@saveExposurePrimarySurvey');
Route::post('save-fast-primary-survey'    , 'Trauma\TraumaController@saveFastPrimarySurvey');
Route::post('save-past-medical-history'    , 'Trauma\TraumaController@savePastMedicalHistory');
Route::post('save-past-medical-allergy-history'    , 'Trauma\TraumaController@savePastMedicalAllergyHistory');
Route::post('save-trauma-hpi'    , 'Trauma\TraumaController@saveTraumaHpi');
Route::post('save-trauma-injury-mechanism'    , 'Trauma\TraumaController@saveTraumaInjuryMechanism');
Route::post('save-trauma-physical-exam'    , 'Trauma\TraumaController@saveTraumaPhysicalExam');
Route::post('save-trauma-lab-result'    , 'Trauma\TraumaController@saveTraumaLabResult');
Route::post('save-trauma-image-result'    , 'Trauma\TraumaController@saveTraumaImageResult');
Route::post('save-trauma-procedures'    , 'Trauma\TraumaController@saveTraumaProcedure');
Route::post('save-trauma-fluid-medication'    , 'Trauma\TraumaController@saveTraumaFluidMedication');
Route::post('save-trauma-client-assesment'    , 'Trauma\TraumaController@saveTraumaClientAssesment');
Route::post('save-trauma-client-re-assesment'    , 'Trauma\TraumaController@saveTraumaClientReAssesment');
Route::post('save-trauma-client-disposition'    , 'Trauma\TraumaController@saveTraumaClientDisposition');
Route::get('get-chief-complaint/{id}'    , 'Trauma\TraumaController@getChiefComplaint');
Route::get('get-client-vitals/{id}'    , 'Trauma\TraumaController@getClientVitals');
Route::post('get-trauma-patient-edit'    , 'Trauma\TriageController@getTraumaPatientEdit');
Route::post('update-client'    , 'Trauma\TriageController@updateClient');




    Route::post('getRegistrationReports','registration\PatientController@getRegistrationReports');
	 Route::post('getMahudhurioByArea','registration\PatientController@getMahudhurioByArea');

	 Route::post('getMahudhurioChfByArea','registration\PatientController@getMahudhurioChfByArea');

	 Route::post('getMahudhurioByCategory','registration\PatientController@getMahudhurioByCategory');

    Route::get("userMatrix/{facility_id}", 'admin\menuController@userMatrix');
    Route::post('previous-visits','ClinicalServices\clinicalController@previousVisits');
	
	//data sync routes
	Route::get('sync/{facility_id}', 'Sync\Sync@init');
	Route::post('sync', 'Sync\Sync@sync');
	Route::post('sync_tables', 'Sync\Sync@syncTables');
	Route::get('syncProgress', 'Sync\Sync@syncProgress');
	//end sync routes
	
	//system files update routes
	//this root need be run in the browser manually!!
	Route::get('setUpdatingFiles', 'System_Updates\Updater_Init@setUpdatingFiles');
	//end manually
	Route::get('generateCleanDirectoryMap', 'System_Updates\Updater_Init@generateCleanDirectoryMap');
	Route::get('cleanDirectory', 'System_Updates\Updater_Init@cleanDirectory');
	Route::get('watchUpdate/{token}', 'System_Updates\Updater_Init@watchUpdate');
	Route::get('update/{facility_id},{make_backup},{continue},{do_on_default_account},{demo_or_live}', 'System_Updates\Updater_Init@init');
	Route::post('updater', 'System_Updates\Updater_Init@getUpdater');
	Route::post('notify', 'System_Updates\Updater_Init@notify');
	Route::post('notification', 'System_Updates\Updater_Init@notification');
	Route::post('files', 'System_Updates\Updater_Init@files');
	Route::post('timestamps', 'System_Updates\Updater_Init@timestamps');
	Route::post('key_space_range', 'System_Updates\Updater_Init@key_space_range');
	Route::post('checksum', 'System_Updates\Updater_Init@checksum');
	Route::post('resetFileUpdate', 'System_Updates\Updater_Init@resetFileUpdate');
	Route::post('countUpdatedFiles', 'System_Updates\Updater_Init@countUpdatedFiles');
	Route::get('mtuha', 'System_Updates\Updater_Init@mtuha');
	Route::get('sanitizePathsAndDatabaseObjectNames', 'System_Updates\Updater_Init@sanitizePathsAndDatabaseObjectNames');
	Route::get('cleanDirectory', 'System_Updates\Updater_Init@cleanDirectory');
	Route::get('changeFacilityId/{new_id}', 'System_Updates\Updater_Init@changeFacilityId');
	Route::get('schema', 'System_Updates\Updater_Init@schema');
	Route::get('convertToGuid', 'System_Updates\Updater_Init@convertToGuid');
	//end update routes
	
	//mtuha tallying
    Route::post('countOPDDiagnosis', 'reports\ReportGenerators@countOPDDiagnosis');
    Route::post('countIPDDiagnosis', 'reports\ReportGenerators@countIPDDiagnosis');
    Route::post('countClinicDiagnosis', 'reports\ReportGenerators@countClinicDiagnosis');
    Route::post('countNewAttendance', 'reports\ReportGenerators@countNewAttendance');
    Route::post('countReattendance', 'reports\ReportGenerators@countReattendance');
    Route::post('countAdmission', 'reports\ReportGenerators@countAdmission');
	
	//individual restart of registers
    Route::get('setAttendances/{facility_id}', 'reports\ReportGenerators@setAttendances');
    Route::get('setReattendances/{facility_id}', 'reports\ReportGenerators@setReattendances');
    Route::get('setIpdDiseases/{facility_id}', 'reports\ReportGenerators@setIpdDiseases');
    Route::get('setOpdDiseases/{facility_id}', 'reports\ReportGenerators@setOpdDiseases');
    Route::get('setAdmissions/{facility_id}', 'reports\ReportGenerators@setAdmissions');
    Route::get('setReferrals/{facility_id}', 'reports\ReportGenerators@setReferrals');
    Route::get('setRegistrationAttendances/{facility_id}', 'reports\ReportGenerators@setRegistrationAttendances');
    Route::get('setRegistrationReattendances/{facility_id}', 'reports\ReportGenerators@setRegistrationReattendances');
	// end individual
	
	
    Route::post('tallied', 'reports\ReportGenerators@talliedPatient');
    Route::get('restartRegister/{facility_id}', 'reports\ReportGenerators@restartRegister');
    Route::get('seedMtuha/{facility_id}', 'reports\ReportGenerators@seedMtuha');
    //DHIS
	Route::post('sendToDHIS', 'Integrations\DHIS\DHIS@sendToDHIS');
	//end mtuha tallying

   Route::resource('authenticate', 'AuthenticateController', ['only' => ['index']]);
    Route::post('authenticate', 'AuthenticateController@authenticate');
    Route::post('sendSmsToGroup', 'admin\stateController@sendSmsToGroup');
    Route::get('getNotifications', 'admin\stateController@getNotifications');
    Route::post('saveRoutingKeys', 'admin\stateController@saveRoutingKeys');
	
	//PSYCHIATRIC MODULE
	
	 Route::post('incomingPsychPatients', 'psychiatric\psychiatricController@incomingPsychPatients');
	 Route::post('psychAll', 'psychiatric\psychiatricController@psychAll');
	 Route::post('postPastPsych', 'psychiatric\psychiatricController@postPastPsych');
	 Route::post('forensicHistory', 'psychiatric\psychiatricController@forensicHistory');
   
	//END PSYCHIATRIC

	//CTC CLINIC NASSSORO..
    Route::get('ctcPendingCustomers/{facility_id}', 'ctc\ctcController@ctcPendingCustomers');
    Route::post('getCTCForm', 'ctc\ctcController@getCTCForm');
    Route::post('ctcSheduleTimeTable', 'ctc\ctcController@ctcSheduleTimeTable');
    Route::post('CtcVitalSignRegister', 'ctc\ctcController@CtcVitalSignRegister');
    Route::post('saveCtCRegistration', 'ctc\ctcController@saveCtCRegistration');
    Route::post('saveCTCPatientSupport', 'ctc\ctcController@saveCTCPatientSupport');
    Route::post('giveAppointmentCtc', 'ctc\ctcController@giveAppointmentCtc');
    Route::post('saveFamilyInfo', 'ctc\ctcController@saveFamilyInfo');
    Route::post('addClinCapacity', 'ctc\ctcController@addClinCapacity');
    Route::post('ctc_registration', 'ctc\ctcController@ctc_registration');
    Route::get('getCtcSheduleTimeTable/{facility_id}', 'ctc\ctcController@getCtcSheduleTimeTable');
    Route::get('deleteDayId/{day_id}', 'ctc\ctcController@deleteDayId');
    Route::get('getClinicAttendanceForPatient/{refferal_id}', 'ctc\ctcController@getClinicAttendanceForPatient');
    Route::get('ctcApprovedCustomers/{facility_id}', 'ctc\ctcController@ctcApprovedCustomers');
    Route::post('saveAllerge', 'ctc\ctcController@saveAllerge');
    Route::post('saveARVExposure', 'ctc\ctcController@saveARVExposure');
    Route::post('saveCtcCodes', 'ctc\ctcController@saveCtcCodes');
    Route::post('incomingCtcPatients', 'ctc\ctcController@incomingCtcPatients');
    Route::post('getCodesPerCTC', 'ctc\ctcController@getCodesPerCTC');
    Route::post('savePatientClinic', 'ctc\ctcController@savePatientClinic');


Route::post('getPatientServicesInTheatre','theatre\theatreController@getPatientServicesInTheatre');

	Route::get('authenticate/user', 'AuthenticateController@getAuthenticatedUser');
	//Route::get('logout/user', 'AuthenticateController@logout');
	Route::get('logout/{user_id}', '\App\Http\Controllers\Auth\LoginController@logout');
    //routes for editing user
    Route::post('update_user', 'AuthenticateController@update_user');
    Route::post('register', 'AuthenticateController@register');
    Route::get('delete/{id}', 'AuthenticateController@delete');
    Route::get('edit/{id}', 'AuthenticateController@edit');

    Route::post('nhifClaims','Insurance\insuranceController@nhifClaims');
    Route::post('getNhifDates','Insurance\insuranceController@getNhifDates');
    Route::post('getInsurancePatients','Insurance\insuranceController@getInsurancePatients');
    Route::post('getConfirmed','Insurance\insuranceController@getConfirmed');
    Route::post('investigationDone','Insurance\insuranceController@investigationDone');
    Route::post('prescriptionDone','Insurance\insuranceController@prescriptionDone');
    Route::get('getConsultationFee/{facility_id}','Insurance\insuranceController@getConsultationFee');
    Route::post('postUnavailableInvestigations','ClinicalServices\clinicalController@postUnavailableInvestigations');
    Route::post('reportRefferal','ClinicalServices\clinicalController@reportRefferal');
    Route::get('getTransferOut/{facility_id}','ClinicalServices\clinicalController@getTransferOut');
    Route::post('outgoingReferrals','ClinicalServices\clinicalController@outgoingReferrals');

	
	
	

    //regions routes
    Route::resource('region_registration', 'Region\RegionsController');
    Route::get('delete/{id}', 'Region\RegionsController@delete');
    Route::post('update_region', 'Region\RegionsController@update_region');

    //REPORTS OPD,IPD NASSORO
    Route::post('getMahudhurioOPD', 'reports\reportsController@getMahudhurioOPD');
    Route::post('bed-occupancy','reports\reportsController@getBedOccupancy');
    Route::post('getIpdReport', 'reports\reportsController@getIpdReport');
    Route::post('getDentalClinicReport', 'reports\reportsController@getDentalClinicReport');
    Route::post('getEyeClinicReport', 'reports\reportsController@getEyeClinicReport');
    Route::post('reportsDrugs', 'reports\reportsController@reportsDrugs');
    Route::post('reportsUnavailableTests', 'reports\reportsController@reportsUnavailableTests');
    Route::post('getDoctorsPerfomaces', 'reports\reportsController@getDoctorsPerfomaces');
    Route::post('getStaffPerfomance', 'reports\reportsController@getStaffPerfomance');
    Route::post('pdfPrinting', 'reports\reportsController@pdfPrinting');

    //proffesionals routes
	Route::resource('professional_registration', 'Professional\ProfessionalController');
	Route::post('update_professional', 'Professional\ProfessionalController@update_professional');
	Route::get('deleteprof/{id}', 'Professional\ProfessionalController@deleteprof');
	
	//Country zone routes
	Route::resource('country_zone_registration', 'Country\CountryController');
	Route::post('country_zone_registration', 'Country\CountryController@country_zone_registration');
	Route::get('getcountry_zone', 'Country\CountryController@getcountry_zone');
	Route::post('update_country_zone', 'Country\CountryController@update_country_zone');
	Route::get('deletecountryzone/{id}', 'Country\CountryController@deletecountryzone');
	
	//Country Registration routes
	Route::resource('country_name_registration', 'Country\CountryController');
	Route::post('country_name_registration', 'Country\CountryController@country_name_registration');
	Route::get('getcountries', 'Country\CountryController@getcountries');
	Route::post('update_country_name', 'Country\CountryController@update_country_name');
	Route::get('deletecountry/{id}', 'Country\CountryController@deletecountry');
	
	//Tribe Routes
	Route::post('tribe_registration', 'Tribe\TribeController@tribe_registration');
	Route::get('gettribe_name', 'Tribe\TribeController@gettribe_name');
	Route::post('updatetribe', 'Tribe\TribeController@updatetribe');
	Route::get('deletetribe/{id}', 'Tribe\TribeController@deletetribe');
	
	// Occupation Routes
	Route::post('occupation_registration', 'Occupation\OccupationController@occupation_registration');
	Route::get('getoccupation', 'Occupation\OccupationController@getoccupation');
	Route::post('updateoccupation', 'Occupation\OccupationController@updateoccupation');
	Route::get('deleteoccupation/{id}', 'Occupation\OccupationController@deleteoccupation');
	
	// Marital Routes
	Route::post('marital_registration', 'Marital\MaritalController@marital_registration');
	Route::get('getmarital_status', 'Marital\MaritalController@getmarital_status');
	Route::post('updatemaritalstatus', 'Marital\MaritalController@updatemaritalstatus');
	Route::get('deletemaritalstatus/{id}', 'Marital\MaritalController@deletemaritalstatus');
	
	///residence routes
    Route::post('residence_registration', 'Residence\ResidenceController@residence_registration');
    Route::get('residence_list', 'Residence\ResidenceController@residence_list');
    Route::get('residence_delete/{id}', 'Residence\ResidenceController@residence_delete');
    Route::post('residence_update', 'Residence\ResidenceController@residence_update');
	
	///facilities routes
    Route::post('facility_registration', 'Facility\FacilityController@facility_registration');
    Route::post('downloadFacility', 'Facility\FacilityController@downloadFacility');
    Route::post('saveIpAddress', 'Facility\FacilityController@saveIpAddress');
	
    Route::get('facility_list', 'Facility\FacilityController@facility_list');
    Route::get('facility_delete/{id}', 'Facility\FacilityController@facility_delete');
    Route::post('facility_update', 'Facility\FacilityController@facility_update');
	Route::post('getReferringFacilities', 'Facility\FacilityController@getReferringFacilities');
		
	//facility types routes
    Route::post('facility_type_registration', 'Facility\FacilityController@facility_type_registration');
	
    Route::post('sendFacilityCentrally', 'Facility\FacilityController@sendFacilityCentrally');
	
    Route::get('facility_type_list', 'Facility\FacilityController@facility_type_list');
    Route::get('facility_type_delete/{id}', 'Facility\FacilityController@facility_type_delete');
    Route::post('facility_type_update', 'Facility\FacilityController@facility_type_update');
	
	//councils routes
    Route::post('council_registration', 'Region\RegionsController@council_registration');
    Route::get('council_list', 'Region\RegionsController@council_list');
    Route::get('council_delete/{id}', 'Region\RegionsController@council_delete');
    Route::post('council_update', 'Region\RegionsController@council_update');

   
    //THEATRE METHODS
	Route::post('getListTheatreQueues', 'theatre\theatreController@getListTheatreQueues');
	Route::post('getProcessWork','theatre\theatreController@getProcessWork');
	
	Route::post('getProcedure','theatre\theatreController@getProcedure');
	Route::post('assignTheatreServices','theatre\theatreController@assignTheatreServices');
	Route::post('showProcedures','theatre\theatreController@showProcedures');
	Route::post('changeProcedures','theatre\theatreController@changeProcedures');
	Route::post('getListTheatreToMortuary','theatre\theatreController@getListTheatreToMortuary');
	

	
	//council_type_registration routes
    Route::post('council_type_registration', 'Region\RegionsController@council_type_registration');
    Route::get('council_type_list', 'Region\RegionsController@council_type_list');
    Route::get('council_type_delete/{id}', 'Region\RegionsController@council_type_delete');
    Route::post('council_type_update', 'Region\RegionsController@council_type_update');
	
	///users routes
    Route::post('user_registration', 'User\UsersRegistrationController@user_registration');
    Route::get('user_list', 'User\UsersRegistrationController@user_list');
    Route::get('user_delete/{id}', 'User\UsersRegistrationController@user_delete');
    Route::post('user_update', 'User\UsersRegistrationController@user_update');
	
	
	// Patient Routes
	Route::post('authorizeCardFromMember', 'registration\PatientController@authorizeCardFromMember');
	Route::post('getNHIFItemPrices','registration\PatientController@getNHIFItemPrices');
	Route::post('GUID','registration\PatientController@GUID');
	Route::post('patient_registration','registration\PatientController@patient_registration');
	Route::get('getpatient', 'registration\PatientController@getpatient');
	Route::post('usersReports', 'registration\PatientController@usersReports');
	Route::get('generate-pdf', 'registration\PatientController@pdfview')->name('generate-pdf');
	Route::get('getInsurances', 'registration\PatientController@getInsurances');
    Route::post('createPatientFolio','Insurance\insuranceController@createPatientFolio');
   
	//SYSTEM MENU CONTROLLER   @ NASSORO S KIUTA
	Route::post('adminRegistration', 'User\UsersRegistrationController@adminRegistration');
	
	Route::get('getUsermenu/{id}', 'admin\menuController@getUserMenu');
	Route::get('getLoginUserDetails/{id}', 'admin\menuController@getLoginUserDetails');
	Route::get('getAuthorization/{id},{state_name}', 'admin\menuController@getAuthorization');
	Route::post('addPermission', 'admin\stateController@checkIfStateExists');
	Route::post('addRoles', 'admin\stateController@checkIfRoleExists');
	Route::post('perm_user', 'admin\stateController@checkIfPermissionUserExists');
	Route::get('getAssignedMenu/{user_id}', 'admin\stateController@getAssignedMenu');
	Route::get('getAssignedRole/{role_id}', 'admin\stateController@getAssignedRole');
	Route::get('getSystemActivity', 'admin\stateController@getSystemActivity');
	Route::post('perm_role', 'admin\stateController@checkIfPermissionRoleExists');
	Route::post('removeAccess', 'admin\stateController@removeAccess');
	Route::post('removeRoleAccess', 'admin\stateController@removeRoleAccess');
	Route::get('geticon', 'admin\stateController@geticon');
	Route::get('getPerm', 'admin\stateController@getPermissions');
	Route::get('getPermName/{id}', 'admin\stateController@getPermissionName');
	Route::get('getUserName/{id}', 'admin\stateController@getUserName');
	Route::get('getRoles', 'admin\stateController@getRoles');
	Route::get('userView/{id}', 'admin\stateController@userView');
	Route::post('fileupload', 'admin\stateController@uploadEntry');
	Route::post('searchUser', 'admin\stateController@searchUser');
	Route::post('changeStatus', 'admin\stateController@changeStatus');
	Route::get('getFacilityCentrally/{facility_code}', 'admin\stateController@getFacilityCentrally');
	Route::post('synchronizeFacilityCentrally', 'admin\stateController@synchronizeFacilityCentrally');
	Route::get('getUserImage/{id}', 'admin\stateController@getUserImage');
	Route::post('installSystem', 'installation\installationController@installSystem');
	Route::post('createSchema', 'installation\installationController@createSchema');
	Route::post('createSeeder', 'installation\installationController@createSeeder');
	Route::post('createNewDatabase', 'admin\stateController@createNewDatabase');
	
//PATIENT REGISTRATION   @ NASSORO S KIUTA
   Route::post('getMortuaryServices', 'registration\PatientController@getMortuaryServices');
    Route::post('residence-patients', 'registration\PatientController@patientsResidents');
	Route::post('giveService', 'registration\PatientController@giveService');	
	
     Route::post('editable-patients', 'registration\PatientController@getPatientsToEdit');
      Route::post('patient-encounter', 'registration\PatientController@getPatientsToEncounter');
    Route::get('getClinic', 'registration\PatientController@getClinic');
    Route::post('quick_registration', 'registration\PatientController@quick_registration');
	Route::post('insuaranceRegistration', 'registration\PatientController@insuaranceRegistration');
	Route::post('corpse_registration', 'registration\PatientController@corpse_registration');
	Route::get('getMortuary', 'registration\PatientController@getMortuary');
	Route::post('saveCorpseFromOutsideFacility', 'registration\PatientController@saveCorpseFromOutsideFacility');
	Route::post('full_registration', 'registration\PatientController@full_registration');
	Route::post('complete_registration', 'registration\PatientController@complete_registration');
	Route::post('searchPatientServices', 'registration\PatientController@searchPatientServices');
	Route::post('searchResidences', 'registration\PatientController@searchResidences');
	Route::get('searchPatientCategory/{facility_id}', 'registration\PatientController@searchPatientCategory');
	Route::post('getPatientRegistrationStatus','registration\PatientController@getPatientRegistrationStatus');
	Route::get('getSeachedInsuarancePatients/{id}','registration\PatientController@getSeachedInsuarancePatients');
	Route::post('getPricedItems', 'registration\PatientController@getPricedItems');
	Route::post('mapGfsCodes', 'Item_setups\Item_priceController@mapGfsCodes');
	Route::get('gfs-mappings', 'Item_setups\Item_priceController@gfsMappings');
	Route::get('delete-gfs-mapping/{id}', 'Item_setups\Item_priceController@deleteGfsMappings');
	Route::post('printLastVisit', 'registration\PatientController@printLastVisit');
    Route::post('getTribes', 'registration\PatientController@getTribes');
	Route::post('enterEncounter', 'registration\PatientController@enterEncounter');
	Route::post('getSeachedPatients','registration\PatientController@getSeachedPatients');
	Route::get('getMaritalStatus','registration\PatientController@getMaritalStatus');
	Route::get('getOccupation/{id}','registration\PatientController@getOccupation');
	Route::get('getCountry/{id}','registration\PatientController@getCountry');
	Route::get('getRelationships/{keyword}','registration\PatientController@getRelationships');
	Route::get('getRelationships','registration\PatientController@getRelationships');
	Route::get('getPatientInfo/{id}','registration\PatientController@getPatientInfo');
    //tb & nutrition clinic
	Route::post('getSampleTesttedCount', 'laboratory\LaboratoryController@getSampleTesttedCount');

    Route::post('postpast_orthopedic', 'TB\Tb_Controller@postpast_orthopedic');
    Route::post('OrthHistory', 'TB\Tb_Controller@OrthHistory');
    Route::post('NutritionHistory', 'TB\Tb_Controller@NutritionHistory');
    Route::post('Save_nutritional_consultations', 'TB\Tb_Controller@Save_nutritional_consultations');
    Route::post('Suppliments_Registry', 'TB\Tb_Controller@Suppliments_Registry');
    Route::post('Save_nutritional_supplimentray', 'TB\Tb_Controller@Save_nutritional_supplimentray');
    Route::post('Save_client_nutritional_status', 'TB\Tb_Controller@Save_client_nutritional_status');
    Route::post('nutritionistPerformance', 'TB\Tb_Controller@nutritionistPerformance');
    Route::post('Nutrition_mtuha', 'TB\Tb_Controller@Nutrition_mtuha');
    Route::get('Suppliments_list', 'TB\Tb_Controller@Suppliments_list');
	//TB & nutrition end here
	
	//ward start here..
    Route::post('getPendingBills','nursing_care\nursingCareController@getPendingBills'); 
    Route::post('continuationNotes','nursing_care\nursingCareController@continuationNotes');
    Route::post('doctorNotes','nursing_care\nursingCareController@doctorNotes');  
    Route::post('getPendingAdmissionList','nursing_care\nursingCareController@getPendingAdmissionList');
	Route::post('getServicesGivenWard', 'nursing_care\nursingCareController@getServicesGiven');
	Route::post('searchItemObserved', 'nursing_care\nursingCareController@searchItemObserved');
	Route::post('saveInputs', 'nursing_care\nursingCareController@saveInputs');
	Route::post('saveOutputs', 'nursing_care\nursingCareController@saveOutputs');
	Route::post('getMahudhuriOPDRegistration', 'nursing_care\nursingCareController@getMahudhuriOPDRegistration');
	Route::post('saveTurningChart', 'nursing_care\nursingCareController@saveTurningChart');
	Route::post('getTurningChart', 'nursing_care\nursingCareController@getTurningChart');
	Route::post('saveNotes', 'nursing_care\nursingCareController@saveNotes');
	Route::post('saveDeathNotes', 'nursing_care\nursingCareController@saveDeathNotes');
	Route::post('getWardReport', 'nursing_care\nursingCareController@getWardReport');

	
	 Route::post('getListItemToServiceInWard','nursing_care\nursingCareController@getListItemToServiceInWard');	
	 Route::post('saveWardBill','nursing_care\nursingCareController@saveWardBill');
	 Route::post('getListFromTheatresReport','nursing_care\nursingCareController@getListFromTheatresReport');
	 Route::post('getInputs','nursing_care\nursingCareController@getInputs');
	 Route::post('getOutputs','nursing_care\nursingCareController@getOutputs');
    Route::post('getInstructions','nursing_care\nursingCareController@getInstructions');
    Route::post('saveWardTypes','nursing_care\nursingCareController@saveWardTypes');
    Route::post('getWardTypes','nursing_care\nursingCareController@getWardTypes');
    Route::get('searchWardTypes/{id}','nursing_care\nursingCareController@searchWardTypes');
    Route::post('saveWards','nursing_care\nursingCareController@saveWards');
    Route::get('getWards/{facility_id}','nursing_care\nursingCareController@getWards');
    Route::get('getWardOneInfo/{ward_id}','nursing_care\nursingCareController@getWardOneInfo');
    Route::get('getBeds/{ward_id}','nursing_care\nursingCareController@getBeds');
    Route::get('getBedsNumber/{ward_id}','nursing_care\nursingCareController@getBedsNumber');
    Route::get('searchBedTypes/{searchKey}','nursing_care\nursingCareController@searchBedTypes');
    Route::post('saveBeds','nursing_care\nursingCareController@saveBeds');
    Route::get('OnThisBed/{bed_id}','nursing_care\nursingCareController@getAdmnThisBed');
    Route::post('getAprovedAdmissionList','nursing_care\nursingCareController@getAprovedAdmissionList');
    Route::post('saveDummyBed','nursing_care\nursingCareController@saveDummyBed');
    Route::post('getPendingDischarge','nursing_care\nursingCareController@getPendingDischarge');
    Route::post('addNursingCare','nursing_care\nursingCareController@addNursingCare');
     Route::post('getPrescribedItems','nursing_care\nursingCareController@getPrescribedItems');
    Route::get('getListNursingCare/{admission_id}','nursing_care\nursingCareController@getListNursingCare');
    Route::get('getInfoForAdmittedPatient/{admission_id}','nursing_care\nursingCareController@getInfoForAdmittedPatient');
    Route::get('mynotifications/{user_id}','nursing_care\nursingCareController@mynotifications');
   Route::post('getWardsToChange','nursing_care\nursingCareController@getWardsToChange'); 
    Route::post('prescribeNurse','nursing_care\nursingCareController@prescribeNurse');
    Route::post('changePatientBed','nursing_care\nursingCareController@changePatientBed');
    Route::post('saveOperations','nursing_care\nursingCareController@saveOperations');   
   
	 Route::post('getMahudhurioByNationality','registration\PatientController@getMahudhurioByNationality');

    Route::post('Patient_tracer','Patient_tracer\Patient_tracerController@Patient_tracer');
    Route::post('Patient_flow','Patient_tracer\Patient_tracerController@Patient_flow');
 Route::post('Patient_nhif_tracer','Patient_tracer\Patient_tracerController@Patient_nhif_tracer');
    Route::post('Patient_nhif_service_tracer','Patient_tracer\Patient_tracerController@Patient_nhif_service_tracer');

    Route::post('Patient_history_printed','Patient_tracer\Patient_tracerController@Patient_history_printed');

 	 Route::post('SearchPatientAddmited','nursing_care\nursingCareController@SearchPatientAddmited');
    Route::post('getPatientAddmitedDetail','nursing_care\nursingCareController@getPatientAddmitedDetail');
    Route::post('SearchdoctorNotes','nursing_care\nursingCareController@SearchdoctorNotes');
    Route::post('SearchPendingAdmissionList','nursing_care\nursingCareController@SearchPendingAdmissionList');
    Route::post('SearchPendingAdmissionListData','nursing_care\nursingCareController@SearchPendingAdmissionListData');
    Route::post('SearchgetPendingDischarge','nursing_care\nursingCareController@SearchgetPendingDischarge');
    Route::post('LoadPendingDischargeData','nursing_care\nursingCareController@LoadPendingDischargeData');

    Route::get('getBedsWithNoPatients/{ward_id}','nursing_care\nursingCareController@getBedsWithNoPatients');
    Route::post('giveBed','nursing_care\nursingCareController@giveBed');
    Route::post('getVital','nursing_care\nursingCareController@getVital');
    Route::post('addVitals','nursing_care\nursingCareController@addVitals');
    Route::get('searchDrugs/{searchKey}','nursing_care\nursingCareController@searchDrugs');   
    Route::get('getDischargedLists/{facility_id}','nursing_care\nursingCareController@getDischargedLists');
    Route::get('getWardClasses/{searchKey}','nursing_care\nursingCareController@getWardClasses');
    Route::get('getIntakeSolutions','nursing_care\nursingCareController@getIntakeSolutions');
    Route::post('addIntakeObservation','nursing_care\nursingCareController@addIntakeObservation');
    Route::post('addIntakeFluid','nursing_care\nursingCareController@addIntakeFluid');
    Route::get('getOutPutTypes','nursing_care\nursingCareController@getOutPutTypes');
    Route::post('addOutPuts','nursing_care\nursingCareController@addOutPuts');
    Route::get('getDiagnosis','nursing_care\nursingCareController@getDiagnosis');
    Route::post('addGoals','nursing_care\nursingCareController@addGoals');
    Route::post('addImplementations','nursing_care\nursingCareController@addImplementations');
    Route::post('addEvaluations','nursing_care\nursingCareController@addEvaluations');
    Route::post('addTimes','nursing_care\nursingCareController@addTimes');
    Route::get('getDrugs','nursing_care\nursingCareController@getDrugs');
    Route::post('addDrugs','nursing_care\nursingCareController@addDrugs');
    Route::get('getFullAdmitedPatientInfo/{admission_id}','nursing_care\nursingCareController@getFullAdmitedPatientInfo');
    Route::get('getPatientSentToTheatre','nursing_care\nursingCareController@getPatientSentToTheatre');
    Route::post('addDischargeNotes','nursing_care\nursingCareController@addDischargeNotes');
    Route::post('enterTheatre','nursing_care\nursingCareController@enterTheatre');
    Route::post('saveAssociateHistory','nursing_care\nursingCareController@saveAssociateHistory');
    Route::get('attendPatientTheatre','nursing_care\nursingCareController@attendPatientTheatre');
    Route::post('savePastHistory','nursing_care\nursingCareController@savePastHistory');
    Route::post('saveResipratorySystem','nursing_care\nursingCareController@saveResipratorySystem');
    Route::post('saveSocialHistory','nursing_care\nursingCareController@saveSocialHistory');
    Route::get('getTeethAbove','nursing_care\nursingCareController@getTeethAbove');
    Route::get('getTeethBelow','nursing_care\nursingCareController@getTeethBelow');
    Route::post('addTimesQue','nursing_care\nursingCareController@addTimesQue');
    Route::post('addPrBp','nursing_care\nursingCareController@addPrBp');
    Route::post('addWardGrade','nursing_care\nursingCareController@addWardGrade');   
    Route::post('itemWardGradeSearch','Item_setups\Item_priceController@itemWardGradeSearch');   
    Route::post('diagnosis_registry','Item_setups\Item_priceController@diagnosis_registry');   
    Route::post('searchNurseName','nursing_care\nursingCareController@searchNurseName');
    Route::post('searchWardNurses','nursing_care\nursingCareController@searchWardNurses');	
    Route::post('addNurse ','nursing_care\nursingCareController@addNurse');
    Route::get('wardSampleCollection/{nurse_id}','nursing_care\nursingCareController@wardSampleCollection');    
    Route::post('getTreatmentChart ','nursing_care\nursingCareController@getTreatmentChart');    
    Route::get('getTeethStatusFromPatientAbove/{request_id}','nursing_care\nursingCareController@getTeethStatusFromPatientAbove');
	
	Route::get('getTeethStatusFromPatientBelow/{request_id}','nursing_care\nursingCareController@getTeethStatusFromPatientBelow');
	
	
     Route::post('TheatrePrintOut','nursing_care\nursingCareController@TheatrePrintOut');
     Route::post('TheatrePrintOutByCategory','nursing_care\nursingCareController@TheatrePrintOutByCategory');
     Route::post('TheatrePrintOutDetails','nursing_care\nursingCareController@TheatrePrintOutDetails');
 Route::post('TheatrePatientSearch','nursing_care\nursingCareController@TheatrePatientSearch');
     Route::post('loadVisitDates','nursing_care\nursingCareController@loadVisitDates');
    Route::post('saveTeethStatus','nursing_care\nursingCareController@saveTeethStatus');
    Route::post('saveStatusAnaesthetic','nursing_care\nursingCareController@saveStatusAnaesthetic');
	 Route::post('prescribeNurse','nursing_care\nursingCareController@prescribeNurse');
    Route::post('changePatientBed','nursing_care\nursingCareController@changePatientBed');
    Route::post('changePatientWard','nursing_care\nursingCareController@changePatientWard');
    Route::post('saveConsent','nursing_care\nursingCareController@saveConsent');    
    Route::get('getOrderedProcedures/{nurse_id}','nursing_care\nursingCareController@getOrderedProcedures');
    Route::get('getNursePerWard/{facility_id}','nursing_care\nursingCareController@getNursePerWard');
	
	 ///NEW ROUTE THEATRE
	 Route::get('getAnaethesiaList/{facility_id}','nursing_care\nursingCareController@getAnaethesiaList');   
     Route::get('getAnaethesiaListApproved/{facility_id}','nursing_care\nursingCareController@getAnaethesiaListApproved');
     Route::get('getIntraOperations/{facility_id}','nursing_care\nursingCareController@getIntraOperations');
     Route::get('getListFromRecovery/{facility_id}','nursing_care\nursingCareController@getListFromRecovery');
     Route::get('getListFromPostAnaesthetic/{facility_id}','nursing_care\nursingCareController@getListFromPostAnaesthetic');
     Route::get('getListFromTheatres/{facility_id}','nursing_care\nursingCareController@getListFromTheatres');
     Route::post('saveGivenDrug','nursing_care\nursingCareController@saveGivenDrug');
     Route::post('saveVitalSigns','nursing_care\nursingCareController@saveVitalSigns');
     Route::post('selectedNurse','nursing_care\nursingCareController@selectedNurse');
     Route::post('changeNurseStatus','nursing_care\nursingCareController@changeNurseStatus');
	
  //NASSORO LABORATORY
    //NASSORO LABORATORY
     Route::post('setDefaultMachine','laboratory\LaboratoryController@setDefaultMachine');
   Route::post('setTestOff','laboratory\LaboratoryController@setTestOff');
  
    Route::post('reportElectonically','laboratory\LaboratoryController@reportElectonically');
    Route::post('quickLabSettings','laboratory\LaboratoryController@quickLabSettings');
    Route::post('reportResultsRemotely','laboratory\LaboratoryController@reportResultsRemotely');
    Route::post('getLabTestPerMachine','laboratory\LaboratoryController@getLabTestPerMachine');
    Route::post('getUnavailableTests','laboratory\LaboratoryController@getUnavailableTests');
    Route::post('changeEquipmentStatus','laboratory\LaboratoryController@changeEquipmentStatus');
    Route::post('searchLabTechnologists','laboratory\LaboratoryController@searchLabTechnologists');
    Route::post('saveLabTechnologists','laboratory\LaboratoryController@saveLabTechnologists');
    Route::post('changeAccess','laboratory\LaboratoryController@changeAccess');
	Route::get('getCollectedSampleDepartments/{user_id}','laboratory\LaboratoryController@getCollectedSampleDepartments');
	Route::post('itemLabSearch', 'Item_setups\Item_priceController@itemLabSearch');
    Route::post('addDevices','laboratory\LaboratoryController@addDevices');
    Route::get('processMobileNumber/{id}','laboratory\LaboratoryController@processMobileNumber');
    Route::get('getEquipementStatus','laboratory\LaboratoryController@getEquipementStatus');
    Route::get('getLabDepartments','laboratory\LaboratoryController@getLabDepartments');
    Route::get('getEquipementList','laboratory\LaboratoryController@getEquipementList');
    Route::post('addLabTest','laboratory\LaboratoryController@addLabTest');
    Route::post('addLabPanel','laboratory\LaboratoryController@addLabPanel');
    Route::post('addLabTestPanel','laboratory\LaboratoryController@addLabTestPanel');
    Route::get('getTestPanel','laboratory\LaboratoryController@getTestPanel');
    Route::post('getPanelComponets','laboratory\LaboratoryController@getPanelComponets');
    Route::get('LabTests','laboratory\LaboratoryController@LabTests');
    Route::post('saveNewDeviceStatus','laboratory\LaboratoryController@saveNewDeviceStatus');
    Route::get('LabTestRequest/{facility_id}','laboratory\LaboratoryController@LabTestRequest');
    Route::post('LabTestRequestPatient','laboratory\LaboratoryController@LabTestRequestPatient');

    //central...
	Route::post('getSeachedCorpses','registration\PatientController@getSeachedCorpses');
	Route::post('corpseTaker','registration\PatientController@corpseTaker');
	Route::post('corpseEdit','registration\PatientController@corpseEdit');
	Route::post('falseAdmit','registration\PatientController@falseAdmit');
	
	
	
    Route::get('patientWardBed/{admission_id}','laboratory\LaboratoryController@patientWardBed');
    Route::post('generateSampleNumber','laboratory\LaboratoryController@generateSampleNumber');
    Route::get('getCollectedSample','laboratory\LaboratoryController@getCollectedSample');
    Route::get('getCancelledSample','laboratory\LaboratoryController@getCancelledSample');
    Route::get('getCanceledTest/{order_id}','laboratory\LaboratoryController@getCanceledTest');
    Route::get('getCollectedSampleDepartments','laboratory\LaboratoryController@getCollectedSampleDepartments');
    Route::get('getLabCollectedSample/{department_id}','laboratory\LaboratoryController@getLabCollectedSample');
    Route::post('getLabCollectedSamplePerOrderNumber','laboratory\LaboratoryController@getLabCollectedSamplePerOrderNumber');
    Route::get('getLabCollectedSamplePerSampleNumber/{sample_no}','laboratory\LaboratoryController@getLabCollectedSamplePerSampleNumber');
    Route::post('sampleCancel','laboratory\LaboratoryController@sampleCancel');
    Route::get('getSampleStatus','laboratory\LaboratoryController@getSampleStatus');
    Route::post('sendLabResult','laboratory\LaboratoryController@sendLabResult');
    Route::post('approveLabResult','laboratory\LaboratoryController@approveLabResult');
    Route::get('getLabResults','laboratory\LaboratoryController@getLabResults');
    Route::get('validateLabResults','laboratory\LaboratoryController@validateLabResults');
	 Route::post('getUsersFromLab','laboratory\LaboratoryController@getUsersFromLab');

Route::post('TaTReport', 'laboratory\LaboratoryController@TaTReport');
	
	 Route::post('getTeststo', 'laboratory\LaboratoryController@getTests');
    Route::post('reportsPerTest', 'laboratory\LaboratoryController@testReports');
    Route::get('validateLabResultsPerOrder/{sub_department_id}','laboratory\LaboratoryController@validateLabResultsPerOrder');
    Route::post('validateLabResultsPerRequest','laboratory\LaboratoryController@validateLabResultsPerRequest');
    Route::post('resultsCancel','laboratory\LaboratoryController@resultsCancel');
    Route::get('getApprovedResults','laboratory\LaboratoryController@getApprovedResults');
    Route::post('saveComponentsResults','laboratory\LaboratoryController@saveComponentsResults');
    Route::post('getPanelComponetsResults','laboratory\LaboratoryController@getPanelComponetsResults');
    Route::post('approveComponentsResults','laboratory\LaboratoryController@approveComponentsResults');
    Route::get('getPanels/{searchKey}','laboratory\LaboratoryController@getPanels');
    Route::post('uploadLabResults','laboratory\LaboratoryController@uploadLabResults');

    
    //new route to get results for verification
    Route::post('showResultsToVerify','laboratory\LaboratoryController@showResultsToVerify');
   
    //..... MORTUARY BY NASSORO

    //..... MORTUARY BY NASSORO
	Route::post('getListOfCorpsesToStore', 'Mortuary\MortuaryController@getListOfCorpsesToStore');
	
	Route::post('givePermissionToCorpse', 'Mortuary\MortuaryController@givePermissionToCorpse');
	Route::post('searchCorpseReports', 'Mortuary\MortuaryController@searchCorpseReports');
	
	Route::get('getCabinetsPerMortuary/{mortuary_id}', 'Mortuary\MortuaryController@getCabinetsPerMortuary');
	
	Route::post('mortuaryGradeSearch', 'Mortuary\MortuaryController@mortuaryGradeSearch');
	Route::post('getServicesGiven', 'Mortuary\MortuaryController@getServicesGiven');
	Route::post('saveMortuaryBill', 'Mortuary\MortuaryController@saveMortuaryBill');
	Route::post('checkIfPermittedDischarge', 'Mortuary\MortuaryController@checkIfPermittedDischarge');
	
    Route::post('addMortuaryClass','mortuary\MortuaryController@addMortuaryClass');
    Route::get('getMortuaryClasses/{search}','mortuary\MortuaryController@getMortuaryClasses');
    Route::get('getMortuaryList','mortuary\MortuaryController@getMortuaryList');
    Route::post('addMortuary','mortuary\MortuaryController@addMortuary');
    Route::get('getMortuaryOneInfo/{mortuary_id}','mortuary\MortuaryController@getMortuaryOneInfo');
    Route::get('getCabinetNumber/{mortuary_id}','mortuary\MortuaryController@getCabinetNumber');
    Route::get('getCabinets/{mortuary_id}','mortuary\MortuaryController@getCabinets');
    Route::post('giveCabinetCorpse','mortuary\MortuaryController@giveCabinetCorpse');
    Route::post('saveCabinets','mortuary\MortuaryController@saveCabinets');
    Route::get('getCabinetsLists/{facility_id}','mortuary\MortuaryController@getCabinetsLists');
    Route::get('getMortuaryClassLists/{facility_id}','mortuary\MortuaryController@getMortuaryClassLists');
    Route::get('getPendingCorpses/{facility_id}','mortuary\MortuaryController@getPendingCorpses');
    Route::get('getPendingOutsideCorpses/{facility_id}','mortuary\MortuaryController@getPendingOutsideCorpses');
    Route::get('getApprovedCorpses/{facility_id}','mortuary\MortuaryController@getApprovedCorpses');
    Route::get('getMortuaryServises','mortuary\MortuaryController@getMortuaryServises');
    Route::post('addCorpseService','mortuary\MortuaryController@addCorpseService');
    Route::get('getCabintesWithNoCorpses/{mortuary_id}','mortuary\MortuaryController@getCabintesWithNoCorpses');
    Route::get('getPendingOutsideCorpseInfo/{corpse_admission_id}','mortuary\MortuaryController@getPendingOutsideCorpseInfo');
	
    Route::post('death-report','reports\reportsController@deathReport');
    Route::post('isdr-report', 'reports\reportsController@isdrReports');
    Route::post('sti-report', 'reports\reportsController@stiReports');
	// end of mortuary...
    //clinical services routes: Mazigo Joe
    Route::post('getStores','ClinicalServices\clinicalController@getStores');
    Route::post('getMedicineByStore','ClinicalServices\clinicalController@getMedicineByStore');
    Route::post('getMedicalSuppliesList','ClinicalServices\clinicalController@getMedicalSuppliesList');
    Route::post('getProceduresList','ClinicalServices\clinicalController@getProceduresList');
    Route::get('getPatientCategories','ClinicalServices\clinicalController@getPatientCategories');
    Route::post('filterByCategory','ClinicalServices\clinicalController@filterByCategory');
    Route::post('eyeRefractionFindings','Eye\eyeController@eyeRefractionFindings');
    Route::post('getOpdPatients','ClinicalServices\clinicalController@getOpdPatients');
    Route::post('allOrderedInvestigations','ClinicalServices\clinicalController@allOrderedInvestigations');
    Route::post('requestBlood','ClinicalServices\clinicalController@requestBlood');
    Route::post('Issue_blood_request', 'BloodBank\BloodBankController@Issue_blood_request');
    Route::post('postSummaryPhysical','ClinicalServices\clinicalController@postSummaryPhysical');
    Route::post('postOtherSummary','ClinicalServices\clinicalController@postOtherSummary');
    Route::post('conservatives','ClinicalServices\clinicalController@conservatives');
    Route::post('dischargerReport','ClinicalServices\clinicalController@dischargerReport');
 Route::post('getPrevDiagnosisConfirmed','ClinicalServices\clinicalController@getPrevDiagnosisConfirmed');

    Route::post('prev-history','ClinicalServices\IpdController@prevHistory');
    Route::post('get-prev-diagnosis','ClinicalServices\IpdController@getPrevDiagnosis');
    Route::post('get-prev-ros','ClinicalServices\IpdController@getPrevRos');
    Route::post('get-allergies','ClinicalServices\IpdController@getAllergies');
    Route::post('get-prev-physical','ClinicalServices\IpdController@getPrevPhysical');
    Route::post('prev-investigation-results','ClinicalServices\IpdController@prevInvestigationResults');
    Route::post('get-past-medicine','ClinicalServices\IpdController@getPastMedicine');
    Route::post('get-past-procedures','ClinicalServices\IpdController@getPastProcedures');


    Route::post('stopMedication','ClinicalServices\clinicalController@stopMedication');

    Route::post('getPrevMedications','ClinicalServices\clinicalController@getPrevMedications');
    Route::post('getPastProcedures','ClinicalServices\clinicalController@getPastProcedures');
    Route::post('getIpdPatients','ClinicalServices\clinicalController@getIpdPatients');
    Route::post('getPatientAdmissionInfo','ClinicalServices\clinicalController@getPatientAdmissionInfo');
    Route::post('getAllIpdPatients','ClinicalServices\clinicalController@getAllIpdPatients');
    Route::post('filterByWards','ClinicalServices\clinicalController@filterByWards');
    Route::post('chiefComplaints','ClinicalServices\clinicalController@chiefComplaints');
    Route::post('checkPatientAttendance','ClinicalServices\clinicalController@checkPatientAttendance');
    Route::post('reviewOfSystems','ClinicalServices\clinicalController@reviewOfSystems');
    Route::post('getDiagnosis','ClinicalServices\clinicalController@getDiagnosis');
    Route::post('getInvestigations','ClinicalServices\clinicalController@getInvestigations');
    Route::post('getTests','ClinicalServices\clinicalController@getTests');
    Route::post('getConsultation','ClinicalServices\clinicalController@getConsultation');
    Route::post('getSingleTests','ClinicalServices\clinicalController@getSingleTests');
    Route::post('previousVisits','ClinicalServices\clinicalController@previousVisits');
    Route::post('searchBeds','ClinicalServices\clinicalController@searchBeds');
    Route::post('getSubDepts','ClinicalServices\clinicalController@getSubDepts');
    Route::post('searchWards','ClinicalServices\clinicalController@searchWards');
    Route::post('assignIcuBed','ClinicalServices\clinicalController@assignIcuBed');
    Route::post('postHistory','ClinicalServices\clinicalController@postHistory');
    Route::post('historyRecords','ClinicalServices\clinicalController@historyRecords');
    Route::post('postRoS','ClinicalServices\clinicalController@postRoS');
    Route::post('postPastMed','ClinicalServices\clinicalController@postPastMed');
    Route::post('birthHistory','ClinicalServices\clinicalController@birthHistory');
    Route::post('familyHistory','ClinicalServices\clinicalController@familyHistory');
    Route::post('postObs','ClinicalServices\clinicalController@postObs');
    Route::post('postPhysical','ClinicalServices\clinicalController@postPhysical');
    Route::post('postInvestigations','ClinicalServices\clinicalController@postInvestigations');
    Route::post('postDiagnosis','ClinicalServices\clinicalController@postDiagnosis');
    Route::post('admitPatient','ClinicalServices\clinicalController@admitPatient');
    Route::post('getMedicine','ClinicalServices\clinicalController@getMedicine');
    Route::post('postMedicines','ClinicalServices\clinicalController@postMedicines');
    Route::post('rejectedMedicines','ClinicalServices\clinicalController@rejectedMedicines');
    Route::post('updateMedicines','ClinicalServices\clinicalController@updateMedicines');
    Route::post('getPatientProcedures','ClinicalServices\clinicalController@getPatientProcedures');
    Route::post('getMedicalSupplies','ClinicalServices\clinicalController@getMedicalSupplies');
    Route::post('postMedicalSupplies','ClinicalServices\clinicalController@postMedicalSupplies');
    Route::post('postPatientProcedures','ClinicalServices\clinicalController@postPatientProcedures');
    Route::post('getResults','ClinicalServices\clinicalController@getResults');
    Route::post('getInvestigationResults','ClinicalServices\clinicalController@getInvestigationResults');
    Route::post('prevInvestigationResults','ClinicalServices\clinicalController@prevInvestigationResults');
    Route::post('getPanelComponentResults','ClinicalServices\clinicalController@getPanelComponentResults');
    Route::post('postNotes','ClinicalServices\clinicalController@postNotes');
    Route::post('getNotes','ClinicalServices\clinicalController@getNotes');
    Route::post('prevDiagnosis','ClinicalServices\clinicalController@prevDiagnosis');
    Route::post('prevFamilyHistory','ClinicalServices\clinicalController@prevFamilyHistory');
    Route::post('prevBirthHistory','ClinicalServices\clinicalController@prevBirthHistory');
    Route::post('prevHistoryExaminations','ClinicalServices\clinicalController@prevHistoryExaminations');
    Route::post('prevObsGyn','ClinicalServices\clinicalController@prevObsGyn');
    Route::post('prevRoS','ClinicalServices\clinicalController@prevRoS');
    Route::post('prevPhysicalExaminations','ClinicalServices\clinicalController@prevPhysicalExaminations');
    Route::post('getPrevDiagnosis','ClinicalServices\clinicalController@getPrevDiagnosis');
    Route::post('prevHistory','ClinicalServices\clinicalController@prevHistory');
    Route::post('getPrevRos','ClinicalServices\clinicalController@getPrevRos');
    Route::post('getPrevBirth','ClinicalServices\clinicalController@getPrevBirth');
    Route::post('getPrevObs','ClinicalServices\clinicalController@getPrevObs');
    Route::post('getPrevFamily','ClinicalServices\clinicalController@getPrevFamily');
    Route::post('getPrevPhysical','ClinicalServices\clinicalController@getPrevPhysical');
    Route::post('internalTransfer','ClinicalServices\clinicalController@internalTransfer');
    Route::post('icuPatients','ClinicalServices\clinicalController@icuPatients');
    Route::post('icuVitals','ClinicalServices\clinicalController@icuVitals');
    Route::post('getAllergy','ClinicalServices\clinicalController@getAllergy');
    Route::post('getAllergies','ClinicalServices\clinicalController@getAllergies');
    Route::post('balanceCheck','ClinicalServices\clinicalController@balanceCheck');
    Route::post('outOfStockMedicine','ClinicalServices\clinicalController@outOfStockMedicine');
    Route::post('outOfStockMedicalSupplies','ClinicalServices\clinicalController@outOfStockMedicalSupplies');
    Route::get('getFacilities','ClinicalServices\clinicalController@getFacilities');
    Route::post('postReferral','ClinicalServices\clinicalController@postReferral');
    Route::post('incomingReferrals','ClinicalServices\clinicalController@incomingReferrals');
    Route::post('PostReferalBill','ClinicalServices\clinicalController@PostReferalBill');
    Route::post('getReferrals','ClinicalServices\clinicalController@getReferrals');
    Route::post('dischargePatient','ClinicalServices\clinicalController@dischargePatient');
    Route::post('updateReferals','ClinicalServices\clinicalController@updateReferals');
    Route::get('getExternalRequests/{facility_id}','ClinicalServices\clinicalController@getExternalRequests');
    Route::post('getAssociatedDetails','ClinicalServices\clinicalController@getAssociatedDetails');
    Route::get('getExternalRequestsDetails/{visit_id}','ClinicalServices\clinicalController@getExternalRequestsDetails');
    Route::post('getPanels','ClinicalServices\clinicalController@getPanels');
    Route::post('getPanelComponents','ClinicalServices\clinicalController@getPanelComponents');
    Route::post('vitalCaptions','ClinicalServices\clinicalController@vitalCaptions');
    Route::post('vitalsTime','ClinicalServices\clinicalController@vitalsTime');
    Route::post('patientVitals','ClinicalServices\clinicalController@patientVitals');
    Route::post('postDeceased','ClinicalServices\clinicalController@postDeceased');
    Route::post('getPrevMedicine','ClinicalServices\clinicalController@getPrevMedicine');
    Route::post('getPrevProcedures','ClinicalServices\clinicalController@getPrevProcedures');
    Route::post('getOpdInvPatients','ClinicalServices\clinicalController@getOpdInvPatients');
    Route::post('getProcedures','ClinicalServices\clinicalController@getProcedures');
    Route::post('dosageChecker','ClinicalServices\clinicalController@dosageChecker');
    Route::post('getOpdDrPerformance','ClinicalServices\clinicalController@getOpdDrPerformance');
    Route::post('cancelPatientBill','ClinicalServices\clinicalController@cancelPatientBill');
    Route::post('getBillList','ClinicalServices\clinicalController@getBillList');
    Route::post('cancelBillItem','ClinicalServices\clinicalController@cancelBillItem');
    Route::get('getSpecialClinics','ClinicalServices\clinicalController@getSpecialClinics');
    Route::post('postToClinics','ClinicalServices\clinicalController@postToClinics');
    Route::post('getAllOpdPatients','ClinicalServices\clinicalController@getAllOpdPatients');
    Route::post('investigationList','ClinicalServices\clinicalController@investigationList');
    Route::post('getAllInvPatients','ClinicalServices\clinicalController@getAllInvPatients');
    Route::post('getPastMedicine','ClinicalServices\clinicalController@getPastMedicine');
    Route::post('postLocalPhysical','ClinicalServices\clinicalController@postLocalPhysical');
    Route::post('pastMedications','ClinicalServices\clinicalController@pastMedications');
    Route::post('patientVitals','ClinicalServices\clinicalController@patientVitals');
    Route::post('doctorsPerformance','ClinicalServices\clinicalController@doctorsPerformance');
    Route::post('postHpi','ClinicalServices\clinicalController@postHpi');
    Route::post('postOtherComplaints','ClinicalServices\clinicalController@postOtherComplaints');
    Route::post('postGenPhysical','ClinicalServices\clinicalController@postGenPhysical');
    Route::post('getCorpse','ClinicalServices\clinicalController@getCorpse');
    Route::post('getCorpseList','ClinicalServices\clinicalController@getCorpseList');
    Route::post('certifyCorpse','ClinicalServices\clinicalController@certifyCorpse');
    Route::post('postUnavailableInvestigations','ClinicalServices\clinicalController@postUnavailableInvestigations');
    Route::post('setAppointments','Appointment\appointmentController@setAppointments');
    Route::post('getAppointments','Appointment\appointmentController@getAppointments');
    Route::post('nhifClaims','Insurance\insuranceController@nhifClaims');
    Route::post('getNhifDates','Insurance\insuranceController@getNhifDates');
    Route::post('getInsurancePatients','Insurance\insuranceController@getInsurancePatients');
    Route::post('getConfirmed','Insurance\insuranceController@getConfirmed');
    Route::post('investigationDone','Insurance\insuranceController@investigationDone');
    Route::post('prescriptionDone','Insurance\insuranceController@prescriptionDone');
    Route::get('getConsultationFee/{facility_id}','Insurance\insuranceController@getConsultationFee');

    //Special clinics routes: Mazigo Joe
    Route::post('incomingDentalPatients','Dental\dentalController@incomingDentalPatients');
    Route::post('dentalAll','Dental\dentalController@dentalAll');
    Route::post('prevDentalVisitis','Dental\dentalController@prevDentalVisitis');
    Route::post('postPastDental','Dental\dentalController@postPastDental');
    Route::post('postPastMedicalSurgical','Dental\dentalController@postPastMedicalSurgical');
    Route::post('incomingEyePatients','Eye\eyeController@incomingEyePatients');
    Route::post('eyeAll','Eye\eyeController@eyeAll');
    Route::post('eyeExaminationReport','Eye\eyeController@eyeExaminationReport');
    Route::post('eyeExaminations','Eye\eyeController@eyeExaminations');
    Route::post('eyeFindings','Eye\eyeController@eyeFindings');
    Route::post('postPastEye','Eye\eyeController@postPastEye');
    Route::post('updateClinicClient','Eye\eyeController@updateClinicClient');
    Route::post('incomingSurgicalPatients','Surgical\surgicalController@incomingSurgicalPatients');
    Route::post('surgicalAll','Surgical\surgicalController@surgicalAll');
    Route::post('prevSurgicalVisitis','Surgical\surgicalController@prevSurgicalVisits');
    Route::post('incomingObgyPatients','Obgy\obgyController@incomingObgyPatients');
    Route::post('obgyAll','Obgy\obgyController@obgyAll');
    Route::post('prevObgyVisitis','Obgy\obgyController@prevObgyVisits');
    Route::post('postGyna','Obgy\obgyController@postGyna');
    Route::post('postObs','Obgy\obgyController@postObs');
    Route::post('getPrevObsGyna','Obgy\obgyController@getPrevObsGyna');
    //Payments routes: Mazigo Joe 

    Route::get('getBills/{facility_id}','Payments\paymentsController@getBills');
    Route::post('getAllPatientBills','Payments\paymentsController@getAllPatientBills');
    Route::post('getDetailedReports','Payments\paymentsController@getDetailedReports');
    Route::post('checkBilledItem','Payments\paymentsController@checkBilledItem');
    Route::post('categoriesReport','Payments\paymentsController@categoriesReport');
    Route::post('paidInsuranceReports','Payments\paymentsController@paidInsuranceReports');
    Route::post('getDepartmentalReports','Payments\paymentsController@getDepartmentalReports');
    Route::post('getSubDepartmentalReports','Payments\paymentsController@getSubDepartmentalReports');
    Route::post('getCancelledBills','Payments\financeControlsController@getCancelledBills');
    Route::post('getPatientBill','Payments\paymentsController@getPatientBill');
    Route::post('updateBills','Payments\paymentsController@updateBills');
    Route::post('chfCheckBills','Payments\paymentsController@chfCheckBills');
    Route::post('updateGepgUser','Payments\paymentsController@updateGepgUser');
    Route::post('patientsToPoS','Payments\paymentsController@patientsToPoS');
    Route::post('showCorpse','Payments\paymentsController@showCorpse');
    Route::post('corpseItems','Payments\paymentsController@corpseItems');
    Route::post('itemsToPoS','Payments\paymentsController@itemsToPoS');
    Route::post('getSelectedItemDetails','Payments\paymentsController@getSelectedItemDetails');
    Route::post('saveFromPoS','Payments\paymentsController@saveFromPoS');
    Route::post('detailedData','Payments\paymentsController@detailedData');
    Route::post('getReceiptData','Payments\paymentsController@getReceiptData');
    Route::post('getCashierReports','Payments\paymentsController@getCashierReports');
    Route::post('getCashierTransactions','Payments\paymentsController@getCashierTransactions');
    Route::post('balanceCheckShop','Payments\paymentsController@balanceCheckShop');
    Route::post('itemsToShop','Payments\paymentsController@itemsToShop');
    Route::post('pendingBills','Payments\paymentsController@pendingBills');
    Route::post('discountReport','Payments\paymentsController@discountReport');
    Route::post('pendingBillData','Payments\paymentsController@pendingBillData');
    Route::post('send_folio','ClinicalServices\clinicalController@send_folio');

    // inventory: Mazigo Joe

    Route::post('newLedger','Inventory\inventoryController@newLedger');
    Route::post('getLedgers','Inventory\inventoryController@getLedgers');
    Route::post('updateLedger','Inventory\inventoryController@updateLedger');
    Route::post('postNewItem','Inventory\inventoryController@postNewItem');
    Route::post('getItems','Inventory\inventoryController@getItems');
    Route::post('updateItem','Inventory\inventoryController@updateItem');
    Route::post('getDepartmentOrders','Inventory\inventoryController@getDepartmentOrders');
    Route::post('postOrderItems','Inventory\inventoryController@postOrderItems');
    Route::post('inspectOrders','Inventory\inventoryController@inspectOrders');
    Route::post('getOrderItems','Inventory\inventoryController@getOrderItems');
    Route::post('updateOrderItem','Inventory\inventoryController@updateOrderItem');
    Route::get('getUserDepartments','Inventory\inventoryController@getUserDepartments');
    Route::post('getDepartmentItems','Inventory\inventoryController@getDepartmentItems');
    Route::post('issueInventoryItems','Inventory\inventoryController@issueInventoryItems');
    Route::post('inventoryReports','Inventory\inventoryController@inventoryReports');
    Route::post('sendInventoryRequests','Inventory\inventoryController@sendInventoryRequests');


//routes from mbele
	Route::post('item_registrar_set', 'Item_setups\ItemsController@item_registrar_set');
	Route::post('change_category', 'Item_setups\ItemsController@change_category');
	//regions routes
    Route::resource('region_registration', 'Region\RegionsController');
    Route::get('delete/{id}', 'Region\RegionsController@delete');
    Route::post('update_region', 'Region\RegionsController@update_region');

//council_type_registration routes
    Route::post('council_type_registration', 'Region\RegionsController@council_type_registration');
    Route::get('council_type_list', 'Region\RegionsController@council_type_list');
    Route::get('council_type_delete/{id}', 'Region\RegionsController@council_type_delete');
    Route::post('council_type_update', 'Region\RegionsController@council_type_update');
Route::post('validatedLabResultsPerRequest','laboratory\LaboratoryController@validatedLabResultsPerRequest');


//councils routes
    Route::post('council_registration', 'Region\RegionsController@council_registration');
    Route::get('council_list', 'Region\RegionsController@council_list');
    Route::get('council_delete/{id}', 'Region\RegionsController@council_delete');
    Route::post('council_update', 'Region\RegionsController@council_update');

//facility types routes
    Route::post('facility_type_registration', 'Facility\FacilityController@facility_type_registration');
    Route::get('facility_type_list', 'Facility\FacilityController@facility_type_list');
    Route::get('facility_type_delete/{id}', 'Facility\FacilityController@facility_type_delete');
    Route::post('facility_type_update', 'Facility\FacilityController@facility_type_update');

    ///facilities routes
    Route::post('facility_registration', 'Facility\FacilityController@facility_registration');
    Route::get('facility_list', 'Facility\FacilityController@facility_list');
    Route::get('facility_delete/{id}', 'Facility\FacilityController@facility_delete');
    Route::post('facility_update', 'Facility\FacilityController@facility_update');


    ///users routes
    Route::post('user_registration', 'User\UsersRegistrationController@user_registration');
    Route::get('user_list/{facility}', 'User\UsersRegistrationController@user_list');
    Route::get('user_delete/{id}', 'User\UsersRegistrationController@user_delete');
    Route::post('user_update', 'User\UsersRegistrationController@user_update');
    Route::post('check_password', 'User\UsersRegistrationController@check_password');
    Route::post('reset_password', 'User\UsersRegistrationController@reset_password');


    ///residence routes
    Route::post('residence_registration', 'Residence\ResidenceController@residence_registration');
    Route::get('residence_list', 'Residence\ResidenceController@residence_list');
    Route::get('residence_delete/{id}', 'Residence\ResidenceController@residence_delete');
    Route::post('residence_update', 'Residence\ResidenceController@residence_update');
     
    //exemption_type_registration routes
    Route::post('exemption_type_registration', 'Exemption\ExemptionTypeController@exemption_type_registration');
    Route::get('exemption_type_list', 'Exemption\ExemptionTypeController@exemption_type_list');
    Route::get('exemption_type_delete/{id}', 'Exemption\ExemptionTypeController@exemption_type_delete');
    Route::post('exemption_type_update', 'Exemption\ExemptionTypeController@exemption_type_update');

  Route::post('showBookTopTen', 'reports\reportsController@showBookTopTen');
//exemption_status routes
 Route::post('exemption_finance_depts', 'Exemption\ExemptionController@exemption_finance_depts');
 Route::post('exemption_sub_dept_finance', 'Exemption\ExemptionController@exemption_sub_dept_finance');

    Route::post('exemption_status_registration', 'Exemption\ExemptionStatusController@exemption_status_registration');
    Route::get('exemption_status_list', 'Exemption\ExemptionStatusController@exemption_status_list');
    Route::get('exemption_status_delete/{id}', 'Exemption\ExemptionStatusController@exemption_status_delete');
    Route::post('exemption_status_update', 'Exemption\ExemptionStatusController@exemption_status_update');
    Route::post('get-patient', 'Exemption\ExemptionController@getAllPatient');
    Route::post('social_referral_registry','Exemption\ExemptionController@social_referral_registry');

 //new exemption routes
    Route::post('marriage_issues_register','Exemption\ExemptionController@marriage_issues_register');
    Route::post('marriage_issues_list','Exemption\ExemptionController@marriage_issues_list');
    Route::post('Update_conflict_content','Exemption\ExemptionController@Update_conflict_content');
 Route::post('vulnerable_followup_neglect', 'Exemption\ExemptionController@vulnerable_followup_neglect');
    //exemption_patient_registration
    Route::get('getexemption_services/{facility_id}', 'Exemption\ExemptionController@getexemption_services');
    Route::post('patient_exemption', 'Exemption\ExemptionController@patient_exemption');
    Route::post('patient_exemption_status_update', 'Exemption\ExemptionController@patient_exemption_status_update');
    Route::post('exemption_list', 'Exemption\ExemptionController@exemption_list');
    Route::post('exemption_finance', 'Exemption\ExemptionController@exemption_finance');
    Route::get('temporary_exemption_list/{facility_id}', 'Exemption\ExemptionController@temporary_exemption_list');
    Route::get('temporary_exemption_status_update/{patient_id}', 'Exemption\ExemptionController@temporary_exemption_status_update');
    Route::post('exemption_list_by_gender', 'Exemption\ExemptionController@exemption_list_by_gender');
    Route::post('exemption_filter_by_employee', 'Exemption\ExemptionController@exemption_filter_by_employee');
    Route::post('violation_registration', 'Exemption\ExemptionController@violation_registration');
    Route::get('gbv_vac_list/{facility}', 'Exemption\ExemptionController@gbv_vac_list');


    //exemption_patient_registration
    Route::post('violances', 'Exemption\ExemptionController@violances');
    Route::post('violence_client_informant_registration', 'Exemption\ExemptionController@violence_client_informant_registration');
    Route::post('violence_client_output_registration', 'Exemption\ExemptionController@violence_client_output_registration');
    Route::post('violence_client_service_registration', 'Exemption\ExemptionController@violence_client_service_registration');
    Route::post('violence_client_registration', 'Exemption\ExemptionController@violence_client_registration');
    Route::post('violence_sub_registration', 'Exemption\ExemptionController@violence_sub_registration');
    Route::post('violence_output_registration', 'Exemption\ExemptionController@violence_output_registration');
    Route::post('violence_service_registration', 'Exemption\ExemptionController@violence_service_registration');
    Route::get('get_violence_service_registration', 'Exemption\ExemptionController@get_violence_service_registration');
    Route::get('get_violence_output_registration', 'Exemption\ExemptionController@get_violence_output_registration');
    Route::get('get_violence_sub_category/{item_id}', 'Exemption\ExemptionController@get_violence_sub_category');

    //new routes above

    //new routes above

//new route load_item_priced_list_search
    Route::post('load_item_priced_list_search', 'Item_setups\Item_priceController@load_item_priced_list_search');
    Route::post('load_item_list_search', 'Item_setups\ItemsController@load_item_list_search');
    //new route load_item_priced_list_search

    Route::post('demographicDetails', 'Patient_tracer\Patient_tracerController@demographicDetails');


  //new route...
    Route::get('temporary_exemption_clients/{facility_id}', 'Exemption\ExemptionController@temporary_exemption_clients');
    Route::post('temporary_exemption_status_update', 'Exemption\ExemptionController@temporary_exemption_status_update');
    Route::post('Create_debt', 'Exemption\ExemptionController@Create_debt');
    Route::get('user_list/{facility}', 'User\UsersRegistrationController@user_list');
    Route::post('sendUserCentrally', 'User\UsersRegistrationController@sendUserCentrally');
    Route::post('GetDebts_list_summary', 'Exemption\ExemptionController@GetDebts_list_summary');

    //blood bank
	 Route::post('NumberOfBloodUnitCollected', 'BloodBank\BloodBankController@NumberOfBloodUnitCollected');
	Route::post('blood_bank_screening', 'BloodBank\BloodBankController@blood_bank_screening');
    Route::post('getBloodScreening', 'BloodBank\BloodBankController@getBloodScreening');
    Route::post('blood_stock', 'BloodBank\BloodBankController@blood_stock');
    Route::get('blood_stock_balance/{facility_id}', 'BloodBank\BloodBankController@blood_stock_balance');
    Route::post('blood_stock_issued', 'BloodBank\BloodBankController@blood_stock_issued');
    Route::post('blood_stock_issuing', 'BloodBank\BloodBankController@blood_stock_issuing');
    Route::post('Donor_type_info', 'BloodBank\BloodBankController@Donor_type_info');
    Route::post('Donor_vipimo', 'BloodBank\BloodBankController@Donor_vipimo');
    Route::post('Donor_damu', 'BloodBank\BloodBankController@Donor_damu');
    Route::post('Donor_dodoso', 'BloodBank\BloodBankController@Donor_dodoso');

  Route::post('exemption_finance_detail', 'Exemption\ExemptionController@exemption_finance_detail');
    //new route...
    Route::get('getexemption_services/{facility_id}', 'Exemption\ExemptionController@getexemption_services');
    Route::post('social_issue_register', 'Exemption\ExemptionController@social_issue_register');
    Route::post('ward_round_register', 'Exemption\ExemptionController@ward_round_register');
    Route::post('client_complains_register', 'Exemption\ExemptionController@client_complains_register');
    Route::get('social_issue_list', 'Exemption\ExemptionController@social_issue_list');
    Route::post('exemption_user_configure', 'Exemption\ExemptionController@exemption_user_configure');
    Route::post('complain_report ', 'Exemption\ExemptionController@complain_report');
    Route::post('Attachment', 'Exemption\ExemptionController@Attachment');
    Route::post('ward_round', 'Exemption\ExemptionController@ward_round');
    Route::post('ward_report', 'Exemption\ExemptionController@ward_report');
    Route::post('complain_view', 'Exemption\ExemptionController@complain_view');
    Route::post('Update_complain_content','Exemption\ExemptionController@Update_complain_content');
    Route::post('Update_ward_round_content','Exemption\ExemptionController@Update_ward_round_content');
    Route::get('patients_address_info/{id}','Exemption\ExemptionController@patients_address_info');

//new routes
 Route::post('SocialWelfareData', 'Exemption\ExemptionController@SocialWelfareData');
    Route::post('SocialWelfareDataHistorory', 'Exemption\ExemptionController@SocialWelfareDataHistorory');
    Route::post('UpdateSocialWelfareData', 'Exemption\ExemptionController@UpdateSocialWelfareData');

    Route::get('exemption_type_list/{user_id}', 'Exemption\ExemptionTypeController@exemption_type_list');
    Route::get('exemption_type_s', 'Exemption\ExemptionTypeController@exemption_type_s');
    //new routes
    //violations GBV/VAC
    Route::post('institution_registration', 'Exemption\ExemptionTypeController@institution_registration');
    Route::get('institution_list', 'Exemption\ExemptionTypeController@institution_list');
    Route::post('institution_update', 'Exemption\ExemptionTypeController@institution_update');
    Route::post('violence_cat_registration', 'Exemption\ExemptionTypeController@violence_cat_registration');
    Route::get('violence_cat_list', 'Exemption\ExemptionTypeController@violence_cat_list');
    Route::get('violence_type_list', 'Exemption\ExemptionTypeController@violence_type_list');
    Route::post('violence_cat_update', 'Exemption\ExemptionTypeController@violence_cat_update');
    Route::get('vulnerables/{facility}', 'Exemption\ExemptionController@vulnerables');
    Route::get('violances/{facility}', 'Exemption\ExemptionController@violances');


    //payment_type routes
    Route::post('payment_type_registration', 'Payment_types\Payment_typeController@payment_type_registration');
    Route::get('payment_type_list', 'Payment_types\Payment_typeController@payment_type_list');
    Route::get('payment_type_delete/{id}', 'Payment_types\Payment_typeController@payment_type_delete');
    Route::post('payment_type_update', 'Payment_types\Payment_typeController@payment_type_update');

    //payment_status routes
    Route::post('payment_status_registration', 'Payment_types\Payment_typeController@payment_status_registration');
    Route::get('payment_status_list', 'Payment_types\Payment_typeController@payment_status_list');
    Route::get('payment_status_delete/{id}', 'Payment_types\Payment_typeController@payment_status_delete');
    Route::post('payment_status_update', 'Payment_types\Payment_typeController@payment_status_update');


    //payment_categories routes
    Route::post('payment_category_registration', 'Payment_types\Payment_categoryController@payment_category_registration');
    Route::get('payment_category_list', 'Payment_types\Payment_categoryController@payment_category_list');
    Route::get('payment_category_delete/{id}', 'Payment_types\Payment_categoryController@payment_category_delete');
    Route::post('payment_category_update', 'Payment_types\Payment_categoryController@payment_category_update');


    //departments routes
    Route::post('department_registration', 'Facility\DepartmentController@department_registration');
    Route::get('department_list', 'Facility\DepartmentController@department_list');
    Route::get('department_delete/{id}', 'Facility\DepartmentController@department_delete');
    Route::post('department_update', 'Facility\DepartmentController@department_update');

 Route::post('vitalsreport', 'Vitals\VitalSignController@vitalsreport');
    

    //payment_sub_categories routes
    Route::post('payment_sub_category_registration', 'Payment_types\Payment_categoryController@payment_sub_category_registration');
    Route::get('payment_sub_category_list', 'Payment_types\Payment_categoryController@payment_sub_category_list');
    Route::get('payment_sub_category_delete/{id}', 'Payment_types\Payment_categoryController@payment_sub_category_delete');
    Route::post('payment_sub_category_update', 'Payment_types\Payment_categoryController@payment_sub_category_update');

    //items registration routes
 Route::post('item_exemption_set', 'Item_setups\ItemsController@item_exemption_set');
    Route::post('item_registration', 'Item_setups\ItemsController@item_registration');
    Route::get('item_list', 'Item_setups\ItemsController@item_list');
    Route::get('item_delete/{id}', 'Item_setups\ItemsController@item_delete');
    Route::post('item_update', 'Item_setups\ItemsController@item_update');


 //item category registration routes
    Route::post('item_category_registration', 'Item_setups\ItemsController@item_category_registration');
    Route::get('item_category_list', 'Item_setups\ItemsController@item_category_list');
    Route::get('item_category_delete/{id}', 'Item_setups\ItemsController@item_category_delete');
    Route::post('item_category_update', 'Item_setups\ItemsController@item_category_update');

    //item_type_mapping registration routes
    Route::post('item_type_map_registration', 'Item_setups\Item_type_mappController@item_type_map_registration');
    Route::get('item_type_map_list', 'Item_setups\Item_type_mappController@item_type_map_list');
    Route::get('item_searching/{item}', 'Item_setups\Item_type_mappController@item_searching');
    Route::get('item_type_map_delete/{id}', 'Item_setups\Item_type_mappController@item_type_map_delete');
    Route::post('item_type_map_update', 'Item_setups\Item_type_mappController@item_type_map_update');

	Route::post('change_patient_category_receiption', 'registration\PatientController@change_patient_category_receiption');
//item_price registration routes
    Route::get('payment_sub_category_to_set_price', 'Item_setups\Item_priceController@payment_sub_category_to_set_price');
    Route::post('item_price_registration', 'Item_setups\Item_priceController@item_price_registration');
    Route::get('item_price_list/{facility_id}', 'Item_setups\Item_priceController@item_price_list');
    Route::get('item_price_delete/{id}', 'Item_setups\Item_priceController@item_price_delete');
    Route::post('item_price_update', 'Item_setups\Item_priceController@item_price_update');
    Route::post('item_ist_search', 'Item_setups\Item_priceController@item_ist_search');
    Route::post('gfs_list_search', 'Item_setups\Item_priceController@gfs_list_search');
    Route::post('item-search', 'Item_setups\Item_priceController@itemSetup');
    Route::get('getsub_department_list', 'Item_setups\ItemsController@getsub_department_list');
    Route::post('item_sub_department_registry', 'Item_setups\ItemsController@item_sub_department_registry');
    Route::get('Sub_depts_items_list', 'Item_setups\ItemsController@Sub_depts_items_list');
    Route::post('load_sub_dept_item_list_search', 'Item_setups\ItemsController@load_sub_dept_item_list_search');
    Route::post('sub_item_update', 'Item_setups\ItemsController@sub_item_update');
//mtuha_clinics
    Route::post('mtuha_clinics_registration', 'Item_setups\ItemsController@mtuha_clinics_registration');
    Route::post('mtuha_clinics_set_registration', 'Item_setups\ItemsController@mtuha_clinics_set_registration');
    Route::post('showDiagnosis_parent', 'Item_setups\ItemsController@showDiagnosis_parent');
    Route::post('mtuhaDentalReports', 'reports\reportsController@mtuhaDentalReports');
    Route::post('mtuhaEyeReports', 'reports\reportsController@mtuhaEyeReports');

//invoices discount routes
    Route::get('loadDiscountBill/{id}', 'Transactions\Invoice_linesController@loadDiscountBill');
    Route::post('invoice_discount', 'Transactions\Invoice_linesController@invoice_discount');
    Route::post('searchpatientForBill', 'Transactions\Invoice_linesController@searchpatientForBill');
    Route::post('discountingReason', 'Transactions\Invoice_linesController@discountingReason');
 Route::post('showSearchFordeposit', 'Transactions\Invoice_linesController@showSearchFordeposit');
    Route::post('saveDepositCash', 'Transactions\Invoice_linesController@saveDepositCash');
    Route::post('deposit_summary', 'Transactions\Invoice_linesController@deposit_summary');
    Route::post('return_change', 'Transactions\Invoice_linesController@return_change');
    Route::post('deposit_summary_view', 'Transactions\Invoice_linesController@deposit_summary_view');
    Route::post('getDepositing_lists', 'Transactions\Invoice_linesController@getDepositing_lists');

    Route::post('getEmployeeDepositing_lists', 'Transactions\Invoice_linesController@getEmployeeDepositing_lists');

    //Pharmacy  vendors routes
    Route::post('vendor_registration', 'Pharmacy\PharmacySetupController@vendor_registration');
    Route::get('vendor_list/{facility}', 'Pharmacy\PharmacySetupController@vendor_list');
    Route::get('vendor_delete/{id}', 'Pharmacy\PharmacySetupController@vendor_delete');
    Route::post('vendor_update', 'Pharmacy\PharmacySetupController@vendor_update');
    
    //Pharmacy invoices routes
    Route::post('invoice_registration', 'Pharmacy\PharmacySetupController@invoice_registration');
    Route::get('invoice_list/{facility}', 'Pharmacy\PharmacySetupController@invoice_list');
    Route::get('invoice_delete/{id}', 'Pharmacy\PharmacySetupController@invoice_delete');
    Route::post('invoice_update', 'Pharmacy\PharmacySetupController@invoice_update');

    //Pharmacy stores routes
    Route::post('store_registration', 'Pharmacy\PharmacySetupController@store_registration');
    Route::get('store_list/{user_id}', 'Pharmacy\PharmacySetupController@store_list');
    Route::get('store_delete/{id}', 'Pharmacy\PharmacySetupController@store_delete');
    Route::post('store_update', 'Pharmacy\PharmacySetupController@store_update');
 
    Route::get('SelectedUserWithStroreAccess/{user}', 'Pharmacy\PharmacySetupController@SelectedUserWithStroreAccess');
    Route::get('Remove_user_store_access/{id}', 'Pharmacy\PharmacySetupController@Remove_user_store_access');

    Route::get('TargetedStoreUserToReceive/{store_id},{facility_id}', 'Pharmacy\PharmacySetupController@TargetedStoreUserToReceive');
    Route::post('tracer-medicines-report', 'Pharmacy\PharmacyItemsController@tracer_medicines_report');

    Route::get('Remove_user_Exemption_access/{id}', 'Exemption\ExemptionTypeController@Remove_user_Exemption_access');
    Route::get('SelectedUserWithExemptionAccess/{id}', 'Exemption\ExemptionTypeController@SelectedUserWithExemptionAccess');

    //pharmacy items searching
Route::get('issued_store_voucher_list/{user_id}', 'Pharmacy\PharmacyItemsController@issued_store_voucher_list');
Route::post('LoadbinCardData', 'Pharmacy\PharmacyItemsController@LoadbinCardData');
Route::post('single_item_issue_voucher', 'Pharmacy\PharmacyItemsController@single_item_issue_voucher');
    Route::get('loadStoreVoucherDates/{store_id}', 'Pharmacy\PharmacyItemsController@loadStoreVoucherDates');
    Route::post('ViewVoucherDetails', 'Pharmacy\PharmacyItemsController@ViewVoucherDetails');
Route::post('mark_pos_dispensing', 'Pharmacy\PharmacyItemsController@mark_pos_dispensing');
Route::get('dispensed_group_control_list', 'Pharmacy\PharmacyItemsController@dispensed_group_control_list');
    Route::get('dispensed_groups', 'Pharmacy\PharmacyItemsController@dispensed_groups');
    Route::post('removeFromDispensedGroupMapping','Pharmacy\DispensingController@removeFromDispensedGroupMapping');

Route::post('daily_dispensed_items', 'Pharmacy\DispensingController@daily_dispensed_items');

    Route::post('pharmacy_item_returning', 'Pharmacy\DispensingController@pharmacy_item_returning');

    Route::post('dispensed_item_range_group', 'Pharmacy\DispensingController@dispensed_item_range_group');
    Route::post('saveGrouped', 'Pharmacy\DispensingController@saveGrouped');

Route::post('RnRSearchold', 'Pharmacy\PharmacyItemsController@RnRSearchold');

    Route::post('searchItem', 'Pharmacy\PharmacySetupController@searchItem');
	Route::post('ledger', 'Pharmacy\PharmacyItemsController@ledger');
    Route::post('received_voucher', 'Pharmacy\PharmacyItemsController@received_voucher');
    Route::post('issue_voucher', 'Pharmacy\PharmacyItemsController@issue_voucher');
//Pharmacy store_type routes
    Route::post('store_type_registration', 'Pharmacy\PharmacySetupController@store_type_registration');
    Route::get('store_type_list', 'Pharmacy\PharmacySetupController@store_type_list');
    Route::get('Main_stores_List/{user}', 'Pharmacy\PharmacySetupController@Main_stores_List');
    Route::get('Sub_stores_List/{user}', 'Pharmacy\PharmacySetupController@Sub_stores_List');
    Route::get('Sub_main_stores_List/{user}', 'Pharmacy\PharmacySetupController@Sub_main_stores_List');
    Route::get('Sub_dispensing_stores_List/{user}', 'Pharmacy\PharmacySetupController@Sub_dispensing_stores_List');
    Route::get('Dispensing_stores_List/{userr}', 'Pharmacy\PharmacySetupController@Dispensing_stores_List');
    Route::get('store_type_delete/{id}', 'Pharmacy\PharmacySetupController@store_type_delete');
    Route::post('store_type_update', 'Pharmacy\PharmacySetupController@store_type_update');
    Route::post('store_user_configure', 'Pharmacy\PharmacySetupController@store_user_configure');
    Route::post('store_user_checking', 'Pharmacy\PharmacySetupController@store_user_checking');

//Pharmacy item_receiving routes
    Route::get('expired/{facility}', 'Pharmacy\PharmacyItemsController@expired');
    Route::post('item_receiving_registration', 'Pharmacy\PharmacyItemsController@item_receiving_registration');
    Route::get('item_receiving_list/{facility},{user}', 'Pharmacy\PharmacyItemsController@item_receiving_list');
    Route::get('item_balances_list_in_mainstore/{facility},{user},{report_type}', 'Pharmacy\PharmacyItemsController@item_balances_list_in_mainstore');
    Route::get('main_store_incoming_order/{facility},{user}', 'Pharmacy\PharmacyItemsController@main_store_incoming_order');
    Route::get('item_receiving_delete/{id}', 'Pharmacy\PharmacyItemsController@item_receiving_delete');
    Route::post('item_receiving_update', 'Pharmacy\PharmacyItemsController@item_receiving_update');
    Route::post('searchItemReceived', 'Pharmacy\PharmacyItemsController@searchItemReceived');
    Route::get('batch_list/{item_id},{user}', 'Pharmacy\PharmacyItemsController@batch_list');
    Route::get('batch_list_balance/{item_id},{user}', 'Pharmacy\PharmacyItemsController@batch_list_balance');
    Route::get('loadBatchBalance/{batch_no},{store},{item}', 'Pharmacy\PharmacyItemsController@loadBatchBalance');
    Route::post('pharmacy_item_issuing', 'Pharmacy\PharmacyItemsController@pharmacy_item_issuing');
    Route::post('Order_processing', 'Pharmacy\PharmacyItemsController@Order_processing');
    Route::post('sub_store_Order_processing', 'Pharmacy\PharmacyItemsController@sub_store_Order_processing');
	
	Route::get('loadTracers', 'Pharmacy\PharmacyItemsController@loadTracers');
	Route::post('tracerMapping', 'Pharmacy\PharmacyItemsController@tracerMapping');
	
    Route::post('main_store_item_ordering', 'Pharmacy\PharmacyItemsController@main_store_item_ordering');
    Route::get('item_balance_list/{facility},{user}', 'Pharmacy\PharmacyItemsController@item_balance_list');

//SubStore Items   routes

    Route::get('substore_item_receiving_list/{facility},{user}', 'Pharmacy\SubStoreItemsController@substore_item_receiving_list');
    Route::get('item_balances_list_in_substore/{facility},{user},{report_type}', 'Pharmacy\SubStoreItemsController@item_balances_list_in_substore');
    Route::post('searchItemsubstoreReceived', 'Pharmacy\SubStoreItemsController@searchItemsubstoreReceived');
    Route::get('batchsubstore_list/{item_id},{user}', 'Pharmacy\SubStoreItemsController@batchsubstore_list');
    Route::get('batchsubstore_list_balance/{item_id},{user}', 'Pharmacy\SubStoreItemsController@batchsubstore_list_balance');
    Route::get('loadsubstoreBatchBalance/{batch_no},{store}', 'Pharmacy\SubStoreItemsController@loadsubstoreBatchBalance');
    Route::post('substore_item_issuing', 'Pharmacy\SubStoreItemsController@substore_item_issuing');
    Route::post('substore_item_ordering', 'Pharmacy\SubStoreItemsController@substore_item_ordering');
    Route::get('sub_store_incoming_order/{facility},{user}', 'Pharmacy\SubStoreItemsController@sub_store_incoming_order');
    Route::post('sub_store_Order_processing', 'Pharmacy\SubStoreItemsController@sub_store_Order_processing');



//Dispensing Items   routes
 //new prescriptions verification
Route::post('Save_pos_os', 'Pharmacy\DispensingController@Save_pos_os');
Route::post('dispensed_item_range', 'Pharmacy\DispensingController@dispensed_item_range');
  Route::post('check_password_logout', 'User\UsersRegistrationController@check_password_logout');
  Route::post('savepast_ent', 'TB\Tb_Controller@savepast_ent');
Route::post('savepast_urology', 'TB\Tb_Controller@savepast_urology');
	Route::post('EntHistory', 'TB\Tb_Controller@EntHistory');

    Route::post('savepast_diabetic', 'TB\Tb_Controller@savepast_diabetic');
    Route::post('DiabeticHistory', 'TB\Tb_Controller@DiabeticHistory');
    Route::post('savepast_dermatology', 'TB\Tb_Controller@savepast_dermatology');
    Route::post('DermatologyHistory', 'TB\Tb_Controller@DermatologyHistory');
Route::post('savepast_urology', 'TB\Tb_Controller@savepast_urology');


Route::post('getpatient_tb_history', 'TB\Tb_Controller@getpatient_tb_history');
     
    Route::post('getorthHistory', 'TB\Tb_Controller@getorthHistory');
    Route::post('uroloHistory', 'TB\Tb_Controller@uroloHistory');

    Route::get('patient_to_verify/{patient}', 'Pharmacy\DispensingController@patient_to_verify');
    Route::get('Dispensing_prescription_vefiry_queue/{facility_id}', 'Pharmacy\DispensingController@Dispensing_prescription_vefiry_queue');
    Route::post('LoadPatientTodispenseFromDBverifyprescriptions', 'Pharmacy\DispensingController@LoadPatientTodispenseFromDBverifyprescriptions');
    Route::post('save_verified_item', 'Pharmacy\DispensingController@save_verified_item');
    Route::post('save_cancel_prescription', 'Pharmacy\DispensingController@save_cancel_prescription');
    Route::post('searchPatientToverifyPrescription', 'Pharmacy\DispensingController@searchPatientToverifyPrescription');
    Route::post('postMedicines_verified', 'Pharmacy\DispensingController@postMedicines_verified');

    Route::post('item_balances_list_in_dispensing', 'Pharmacy\DispensingController@item_balances_list_in_dispensing');
    Route::get('dispensing_item_receiving_list/{facility},{user}', 'Pharmacy\DispensingController@dispensing_item_receiving_list');
    Route::post('searchItemdispensingReceived', 'Pharmacy\DispensingController@searchItemdispensingReceived');
    Route::get('batchdispensing_list/{item_id},{user}', 'Pharmacy\DispensingController@batchdispensing_list');
    Route::get('loaddispensingBatchBalance/{batch_no},{store},{item_id}', 'Pharmacy\DispensingController@loaddispensingBatchBalance');
    Route::post('dispensing_item_issuing', 'Pharmacy\DispensingController@dispensing_item_issuing');
    Route::post('dispensing_item_ordering', 'Pharmacy\DispensingController@dispensing_item_ordering');
    Route::get('patient_to_dispense/{patient}', 'Pharmacy\DispensingController@patient_to_dispense');
    Route::get('dispensings/{facility}', 'Pharmacy\DispensingController@dispensings');
    Route::get('Dispensing_queue/{facility_id}', 'Pharmacy\DispensingController@Dispensing_queue');
    Route::post('save_dispensed_item', 'Pharmacy\DispensingController@save_dispensed_item');
    Route::post('searchPatientTodispense', 'Pharmacy\DispensingController@searchPatientTodispense');
    Route::post('LoadPatientTodispenseFromDB', 'Pharmacy\DispensingController@LoadPatientTodispenseFromDB');
    Route::get('batch_patient_dispensing_list/{item_id},{user},{quantity}', 'Pharmacy\DispensingController@batch_patient_dispensing_list');
    Route::get('dispensed_item_list/{facility},{user}', 'Pharmacy\DispensingController@dispensed_item_list');
Route::post('save_dispensed_to_users', 'Pharmacy\DispensingController@save_dispensed_to_users');
//Pharmacy store_request_status routes
    Route::post('store_request_status_registration', 'Pharmacy\PharmacySetupController@store_request_status_registration');
    Route::get('store_request_status_list', 'Pharmacy\PharmacySetupController@store_request_status_list');
    Route::get('store_request_status_delete/{id}', 'Pharmacy\PharmacySetupController@store_request_status_delete');
    Route::post('store_request_status_update', 'Pharmacy\PharmacySetupController@store_request_status_update');
    Route::post('getUserToSetStoreToAccess', 'Pharmacy\PharmacySetupController@getUserToSetStoreToAccess');

Route::get('storesListToAsignAccess/{facility_id}', 'Pharmacy\PharmacySetupController@storesListToAsignAccess'); 
    //Pharmacy transaction type routes
    Route::post('pharmacy_transaction_type_registration', 'Pharmacy\PharmacySetupController@pharmacy_transaction_type_registration');
    Route::get('pharmacy_transaction_type_list', 'Pharmacy\PharmacySetupController@pharmacy_transaction_type_list');
    Route::get('pharmacy_transaction_adjustment', 'Pharmacy\PharmacySetupController@pharmacy_transaction_adjustment');
    Route::get('pharmacy_transaction_type_delete/{id}', 'Pharmacy\PharmacySetupController@pharmacy_transaction_type_delete');
    Route::post('pharmacy_transaction_type_update', 'Pharmacy\PharmacySetupController@pharmacy_transaction_type_update');

  //drf routes to do all functionalities..starts
  
	Route::get("stockValue","Drf\Drf_Controller@stockValue");
    Route::post('SaveNewProduct', 'Drf\Drf_Controller@SaveNewProduct');
    Route::post('SaveProductUpdate', 'Drf\Drf_Controller@SaveProductUpdate');
    Route::post('DrfProducts', 'Drf\Drf_Controller@DrfProducts');
    Route::post('DeleteProduct', 'Drf\Drf_Controller@DeleteProduct');
    Route::post('SaveProductPrice', 'Drf\Drf_Controller@SaveProductPrice');
    Route::post('SaveProductPriceUpdate', 'Drf\Drf_Controller@SaveProductPriceUpdate');
    Route::post('DrfPrices', 'Drf\Drf_Controller@DrfPrices');
    Route::post('DrfProductsToPriceSet', 'Drf\Drf_Controller@DrfProductsToPriceSet');
    Route::post('searchDrfProduct', 'Drf\Drf_Controller@searchDrfProduct');
    Route::post('SalesshowSearch', 'Drf\Drf_Controller@SalesshowSearch');
    Route::post('ViewInvoice', 'Drf\Drf_Controller@ViewInvoice');
    Route::post('freezeInvoice', 'Drf\Drf_Controller@freezeInvoice');
    Route::post('ClearBilledInvoice', 'Drf\Drf_Controller@ClearBilledInvoice');
    Route::post('LoadFinanceDetails', 'Drf\Drf_Controller@LoadFinanceDetails');
    Route::post('LoadFinanceDebts', 'Drf\Drf_Controller@LoadFinanceDebts');
    Route::post('reloadInvoices', 'Drf\Drf_Controller@reloadInvoices');
    Route::post('LoadPriceTag', 'Drf\Drf_Controller@LoadPriceTag');
    Route::post('LoadBatchbalance', 'Drf\Drf_Controller@LoadBatchbalance');
    Route::post('SaveNewSale', 'Drf\Drf_Controller@SaveNewSale');
    Route::post('CancelDrfGepgCell', 'Drf\Drf_Controller@CancelDrfGepgCell');
    Route::post('SaveNewStock', 'Drf\Drf_Controller@SaveNewStock');
    Route::post('StockIssued', 'Drf\Drf_Controller@StockIssued');
    Route::post('StockBalance', 'Drf\Drf_Controller@StockBalance');
    Route::post('LoadStockDetails', 'Drf\Drf_Controller@LoadStockDetails');
Route::post('LoadStockExpires', 'Drf\Drf_Controller@LoadStockExpires');
Route::post('getMedicenes', 'Drf\Drf_Controller@getMedicenes');
    Route::post('LoadFinanceNHIF', 'Drf\Drf_Controller@LoadFinanceNHIF');

    Route::post('LoadCategories', 'Drf\Drf_Controller@LoadCategories');
    Route::post('SaveNewCategory', 'Drf\Drf_Controller@SaveNewCategory');
    Route::post('EditCategory', 'Drf\Drf_Controller@EditCategory');


    Route::post('getReceiptDatadrf', 'Drf\Drf_Controller@getReceiptData');
    
    //drf routes to do all functionalities..ends

//rch mtuha

    Route::post('getChildGrowthAttendanceReport','reports\reportsController@getChildGrowthAttendanceReport');
    Route::post('getChildAttendanceReport','reports\reportsController@getChildAttendanceReport');
    Route::post('getChildfeedingReport','reports\reportsController@getChildfeedingReport');
    Route::post('getChilddewormgivenReport','reports\reportsController@getChilddewormgivenReport');
    Route::post('Anti_natl_mtuha','reports\reportsController@Anti_natl_mtuha');
//rch mtuha
    
 //rch mtuha

    Route::post('getChildGrowthAttendanceReport','reports\reportsController@getChildGrowthAttendanceReport');
    Route::post('getChildAttendanceReport','reports\reportsController@getChildAttendanceReport');
    Route::post('getChildfeedingReport','reports\reportsController@getChildfeedingReport');
    Route::post('getChilddewormgivenReport','reports\reportsController@getChilddewormgivenReport');
    Route::post('Anti_natl_mtuha','reports\reportsController@Anti_natl_mtuha');
    Route::post('mtuhaPost_natal','reports\reportsController@mtuhaPost_natal');
    //DTC's
    Route::post('postDTC', 'Pediatric\Pediatric_controller@postDTC');
    Route::post('mtuhaDTC', 'Pediatric\Pediatric_controller@mtuhaDTC');
   Route::post('mtuhaDTC_central_table', 'Pediatric\Pediatric_controller@mtuhaDTC_central_table');
    //vct'''''''''''''''''''''''''''''vct...................vct

    Route::post('vct_registration', 'VCT\Vct_Controller@vct_registration');
    Route::post('update_referral_Incomming', 'VCT\Vct_Controller@update_referral_Incomming');
    Route::get('searchClinicpatientQueue/{facility}', 'VCT\Vct_Controller@searchClinicpatientQueue');


    //pediatric'''''''''''''''''''''''''''''pediatric...................pediatric
    Route::post('pediatricDietary', 'Pediatric\Pediatric_controller@pediatricDietary');
    Route::post('pediatricPostNatal', 'Pediatric\Pediatric_controller@pediatricPostNatal');
    Route::post('pediatricNatal', 'Pediatric\Pediatric_controller@pediatricNatal');
    Route::post('pediatricPreNatal', 'Pediatric\Pediatric_controller@pediatricPreNatal');
    Route::post('pediatricNutritional', 'Pediatric\Pediatric_controller@pediatricNutritional');

    //tb'''''''''''''''''''''''''''''tb...................tb



    Route::post('tb_pre_entry_registration', 'TB\Tb_Controller@tb_pre_entry_registration');
    Route::post('patient_tb_type_registration', 'TB\Tb_Controller@patient_tb_type_registration');
    Route::post('patient_tb_sputam_registration', 'TB\Tb_Controller@patient_tb_sputam_registration');
    Route::get('treatment_types', 'TB\Tb_Controller@treatment_types');
    Route::post('patient_tb_treatment_types', 'TB\Tb_Controller@patient_tb_treatment_types');
    Route::post('patient_tb_vvu_service', 'TB\Tb_Controller@patient_tb_vvu_service');
    Route::post('tb_patient_medication_followups', 'TB\Tb_Controller@tb_patient_medication_followups');
    Route::post('Tbl_tb_patient_treatment_outputs', 'TB\Tb_Controller@Tbl_tb_patient_treatment_outputs');
    Route::get('patientAge/{patientAge}', 'TB\Tb_Controller@patientAge');
    Route::get('searchClinicpatient/{facility_id}', 'TB\Tb_Controller@searchClinicpatient');
    Route::get('searchClinicpatientFromDb/{search}', 'TB\Tb_Controller@searchClinicpatientFromDb');
    Route::post('postpast_orthopedic', 'TB\Tb_Controller@postpast_orthopedic');
	Route::post('OrthHistory', 'TB\Tb_Controller@OrthHistory');


//rch'''''''''''''''''''''''''''''rch....................anti natal

    Route::post('pregnancy_indicator', 'RCH\Anti_natalController@pregnancy_indicator');
    Route::post('Anti_incoming_referral', 'RCH\Anti_natalController@Anti_incoming_referral');
    Route::post('anti_natal_registration', 'RCH\Anti_natalController@anti_natal_registration');
    Route::post('anti_natal_lab_results', 'RCH\Anti_natalController@anti_natal_lab_results');
    Route::post('partner_lab_results', 'RCH\Anti_natalController@partner_lab_results');
    Route::post('preventives_registration', 'RCH\Anti_natalController@preventives_registration');
    Route::post('reattendance_registration', 'RCH\Anti_natalController@reattendance_registration');
    Route::post('counselling_area_registration', 'RCH\Anti_natalController@counselling_area_registration');
    Route::get('councelling_lists', 'RCH\Anti_natalController@councelling_lists');
    Route::post('counselling_registration', 'RCH\Anti_natalController@counselling_registration');
    //new...........................................above................
    Route::post('SearchStds', 'RCH\Anti_natalController@SearchStds');
    Route::post('searchRchpatient', 'RCH\Anti_natalController@searchRchpatient');
    Route::post('vaccination_registration', 'RCH\Anti_natalController@vaccination_registration');
    Route::post('vaccination_update', 'RCH\Anti_natalController@vaccination_update');
    Route::post('tt_vaccination_registration', 'RCH\Anti_natalController@tt_vaccination_registration');
    Route::post('pregnancy_age_registration', 'RCH\Anti_natalController@pregnancy_age_registration');
    Route::post('prev_preg_info_registration', 'RCH\Anti_natalController@prev_preg_info_registration');


    Route::post('pmtct_registration', 'RCH\Anti_natalController@pmtct_registration');

    Route::post('pmtct_partner_registration', 'RCH\Anti_natalController@pmtct_partner_registration');


    Route::post('ipt_registration', 'RCH\Anti_natalController@ipt_registration');

    Route::post('deworm_given_registration', 'RCH\Anti_natalController@deworm_given_registration');
    Route::post('std_registration', 'RCH\Anti_natalController@std_registration');
    Route::post('referral_registration', 'RCH\Anti_natalController@referral_registration');
    Route::get('vaccination_list', 'RCH\Anti_natalController@vaccination_list');



//rch'''''''''''''''''''''''''''''rch....................post natal

    Route::post('post_natal_observation_lists_registration', 'RCH\Post_natalController@post_natal_observation_lists_registration');
//killing womb_registration
    Route::get('post_natal_observation_lists', 'RCH\Post_natalController@post_natal_observation_lists');
    Route::get('post_natal_observation_description_list/{id}', 'RCH\Post_natalController@post_natal_observation_description_list');
    Route::post('post_natal_observation_descriptions', 'RCH\Post_natalController@post_natal_observation_descriptions');

//killing breast_registration
    Route::post('post_natal_observation_status', 'RCH\Post_natalController@post_natal_observation_status');
    //new routes................................................
    Route::post('Post_natal_serial_no', 'RCH\Post_natalController@Post_natal_serial_no');
    Route::post('post_natal_registration_update', 'RCH\Post_natalController@post_natal_registration_update');
    Route::post('post_birth_info_registration', 'RCH\Post_natalController@post_birth_info_registration');
    Route::post('baby_feed_registration', 'RCH\Post_natalController@baby_feed_registration');
    Route::post('pmtct_post_registration', 'RCH\Post_natalController@pmtct_post_registration');
    Route::post('post_reattendance_registration', 'RCH\Post_natalController@post_reattendance_registration');
    Route::post('post_natal_inv_registration', 'RCH\Post_natalController@post_natal_inv_registration');

    Route::post('dehiscence_registration', 'RCH\Post_natalController@dehiscence_registration');
    Route::post('post_additional_medication_registration', 'RCH\Post_natalController@post_additional_medication_registration');
    Route::post('post_tt_vaccination_registration', 'RCH\Post_natalController@post_tt_vaccination_registration');
    Route::post('post_family_planing_registration', 'RCH\Post_natalController@post_family_planing_registration');
    Route::post('post_chilreattendance_registration', 'RCH\Post_natalController@post_chilreattendance_registration');
    Route::post('post_child_inv_registration', 'RCH\Post_natalController@post_child_inv_registration');
    Route::post('child_vaccination_registration', 'RCH\Post_natalController@child_vaccination_registration');
    Route::post('child_infection_registration', 'RCH\Post_natalController@child_infection_registration');
    Route::post('post_natal_arv_registration', 'RCH\Post_natalController@post_natal_arv_registration');
    Route::post('post_natal_feeding_registration', 'RCH\Post_natalController@post_natal_feeding_registration');
    Route::post('post_referral_registration', 'RCH\Post_natalController@post_referral_registration');


    //rch'''''''''''''''''''''''''''''rch....................labour
    Route::post('labour_observation_registration', 'RCH\LabourController@labour_observation_registration');//emonc routes killed
    //........new routes.................................
    Route::post('Labour_serial_no', 'RCH\LabourController@Labour_serial_no');
    Route::post('Labour_registration_update', 'RCH\LabourController@Labour_registration_update');
    Route::post('labour_birth_info_registration', 'RCH\LabourController@labour_birth_info_registration');
    Route::post('labour_admission_registration', 'RCH\LabourController@labour_admission_registration');
    Route::post('labour_delivery_registration', 'RCH\LabourController@labour_delivery_registration');
    Route::post('labour_referral_registration', 'RCH\LabourController@labour_referral_registration');
    Route::post('newborn_info_registration', 'RCH\LabourController@newborn_info_registration');
    Route::post('labour_fsb_msb_registration', 'RCH\LabourController@labour_fsb_msb_registration');
    Route::post('labour_complication_registration', 'RCH\LabourController@labour_complication_registration');

    Route::post('labour_fgm_registration', 'RCH\LabourController@labour_fgm_registration');
    Route::post('labour_vvu_registration', 'RCH\LabourController@labour_vvu_registration');
    Route::post('labour_child_arv_registration', 'RCH\LabourController@labour_child_arv_registration');
    Route::post('labour_child_feeding_registration', 'RCH\LabourController@labour_child_feeding_registration');
    Route::post('labour_mother_disposition_registration', 'RCH\LabourController@labour_mother_disposition_registration');
    Route::post('labour_child_disposition_registration', 'RCH\LabourController@labour_child_disposition_registration');



    //rch'''''''''''''''''''''''''''''rch....................child
    Route::post('Child_incoming_referral', 'RCH\Child_controller@Child_incoming_referral');
    Route::get('calculateWeek/{client}', 'RCH\Child_controller@calculateWeek');
    Route::post('Child_registration_update', 'RCH\Child_controller@Child_registration_update');
    Route::post('Child_mother_registration', 'RCH\Child_controller@Child_mother_registration');
    Route::post('hiv_ID_registration', 'RCH\Child_controller@hiv_ID_registration');
    Route::post('Child_vaccination_registration', 'RCH\Child_controller@Child_vaccination_registration');
    Route::post('child_growth_registration', 'RCH\Child_controller@child_growth_registration');
    Route::post('child_deworm_registration', 'RCH\Child_controller@child_deworm_registration');
    Route::post('child_voucher_registration', 'RCH\Child_controller@child_voucher_registration');
    Route::post('child_feeding_registration', 'RCH\Child_controller@child_feeding_registration');
    Route::post('child_referral_registration', 'RCH\Child_controller@child_referral_registration');
    Route::post('searchRchAllChild', 'RCH\Child_controller@searchRchAllChild');


    //rch'''''''''''''''''''''''''''''rch....................family planning

    Route::post('Family_incoming_referral', 'RCH\Family_planningController@Family_incoming_referral');

    Route::post('family_planning_registration', 'RCH\Family_planningController@family_planning_registration');
    Route::post('search_family_planing_clients', 'RCH\Family_planningController@search_family_planing_clients');
    Route::post('faimily_health_registration', 'RCH\Family_planningController@faimily_health_registration');
    Route::post('faimily_birth_registration', 'RCH\Family_planningController@faimily_birth_registration');
    Route::post('faimily_delivery_result_registration', 'RCH\Family_planningController@faimily_delivery_result_registration');
    Route::post('faimily_menstral_result_registration', 'RCH\Family_planningController@faimily_menstral_result_registration');
    Route::post('faimily_iptc_registration', 'RCH\Family_planningController@faimily_iptc_registration');
    Route::post('faimily_cancer_registration', 'RCH\Family_planningController@faimily_cancer_registration');
    Route::post('faimily_lab_test_registration', 'RCH\Family_planningController@faimily_lab_test_registration');
    Route::post('fplanning_stomach_leg_investigation', 'RCH\Family_planningController@fplanning_stomach_leg_investigation');
    Route::post('fplanning_viginal_by_arm_investigations', 'RCH\Family_planningController@fplanning_viginal_by_arm_investigations');
    Route::post('fplanning_viginal_by_spec_investigations', 'RCH\Family_planningController@fplanning_viginal_by_spec_investigations');
    Route::post('fplanning_attendances', 'RCH\Family_planningController@fplanning_attendances');
    ///====================new routes=================================

    Route::post('planning_method_list_registration', 'RCH\Family_planningController@planning_method_list_registration');
    Route::get('family_planning_method_list', 'RCH\Family_planningController@family_planning_method_list');
    Route::post('family_planning_method_list_update', 'RCH\Family_planningController@family_planning_method_list_update');
    Route::post('mother_planning_method_registration', 'RCH\Family_planningController@mother_planning_method_registration');
    Route::get('mother_planning_method_status/{patient_id}', 'RCH\Family_planningController@mother_planning_method_status');
    Route::post('mother_planning_method_status_update', 'RCH\Family_planningController@mother_planning_method_status_update');
    Route::post('breast_cancer_registration', 'RCH\Family_planningController@breast_cancer_registration');
    Route::post('cervix_cancer_registration', 'RCH\Family_planningController@cervix_cancer_registration');
    Route::post('planning_vvu_registration', 'RCH\Family_planningController@planning_vvu_registration');
    Route::post('fplanning_referral_registration', 'RCH\Family_planningController@fplanning_referral_registration');
    Route::post('RCH_recommendations_registration', 'RCH\Family_planningController@RCH_recommendations_registration');

    Route::post('condom_usage_registration', 'RCH\Family_planningController@condom_usage_registration');

	
	   Route::post('patient_antrabies_vaccination', 'Environmental\EnvironmentalController@patient_antrabies_vaccination');
	   Route::post('ant_rabies_vaccination_registry', 'Environmental\EnvironmentalController@ant_rabies_vaccination_registry');
	   Route::post('ant_rabies_monitoring', 'Environmental\EnvironmentalController@ant_rabies_monitoring');
	   Route::get('ant_rabies_vaccination_list/{facility_id}', 'Environmental\EnvironmentalController@ant_rabies_vaccination_list');
	   Route::get('ant_rabies_vaccination_usage/{facility_id}', 'Environmental\EnvironmentalController@ant_rabies_vaccination_usage');
	   Route::get('ant_rabies_vaccination_update/{facility_id}', 'Environmental\EnvironmentalController@ant_rabies_vaccination_update');


	     Route::get('recent_notified_disease/{facility_id}', 'Environmental\EnvironmentalController@recent_notified_disease');

	    Route::post('patient_notifed_Diagnosis_freq', 'Environmental\EnvironmentalController@patient_notifed_Diagnosis_freq');
	   Route::post('summary_out_break_disease_death', 'Environmental\EnvironmentalController@summary_out_break_disease_death');
	   Route::get('patient_notified_admision_status/{visit_id}', 'Environmental\EnvironmentalController@patient_notified_admision_status');
	   Route::get('save_notifiable_Diagnosis_list', 'Environmental\EnvironmentalController@save_notifiable_Diagnosis_list');
	   Route::post('patient_notifed_Diagnosis_list', 'Environmental\EnvironmentalController@patient_notifed_Diagnosis_list');
	   Route::post('save_notifiable_Diagnosis', 'Environmental\EnvironmentalController@save_notifiable_Diagnosis');
	   Route::post('nuisance_registration', 'Environmental\EnvironmentalController@nuisance_registration');
    Route::post('nuisance_update', 'Environmental\EnvironmentalController@nuisance_update');
    Route::post('nuisance_composition', 'Environmental\EnvironmentalController@nuisance_composition');
    Route::get('nuisance_list', 'Environmental\EnvironmentalController@nuisance_list');
    Route::post('nuisance_composed', 'Environmental\EnvironmentalController@nuisance_composed');
    Route::post('environment_equipment_type_registration', 'Environmental\EnvironmentalController@environment_equipment_type_registration');
    Route::post('equipment_type_update', 'Environmental\EnvironmentalController@equipment_type_update');
    Route::post('environment_equipment_registration', 'Environmental\EnvironmentalController@environment_equipment_registration');
    Route::post('environment_equipment_registration', 'Environmental\EnvironmentalController@environment_equipment_registration');
    Route::post('environment_equipment_receiving', 'Environmental\EnvironmentalController@environment_equipment_receiving');
    Route::post('equipment_balances', 'Environmental\EnvironmentalController@equipment_balances');
    Route::get('equipment_type_list', 'Environmental\EnvironmentalController@equipment_type_list');
    Route::get('environment_equipment_list/{facility}', 'Environmental\EnvironmentalController@environment_equipment_list');
    Route::get('environment_Receiving_issuing_summary/{facility}', 'Environmental\EnvironmentalController@environment_Receiving_issuing_summary');
    Route::post('equipment_received_list', 'Environmental\EnvironmentalController@equipment_received_list');
    Route::post('equipment_issued_list', 'Environmental\EnvironmentalController@equipment_issued_list');
    Route::post('environment_equipment_issuing', 'Environmental\EnvironmentalController@environment_equipment_issuing');
    Route::post('environment_waste_collection', 'Environmental\EnvironmentalController@environment_waste_collection');
    Route::post('environment_waste_disposal', 'Environmental\EnvironmentalController@environment_waste_disposal');
    Route::post('environment_waste_registration', 'Environmental\EnvironmentalController@environment_waste_registration');
    Route::post('environment_waste_dispose_registration', 'Environmental\EnvironmentalController@environment_waste_dispose_registration');
    Route::post('waste_type_update', 'Environmental\EnvironmentalController@waste_type_update');
    Route::post('waste_dispose_update', 'Environmental\EnvironmentalController@waste_dispose_update');
    Route::post('wastes_collected', 'Environmental\EnvironmentalController@wastes_collected');
    Route::get('waste_type_list', 'Environmental\EnvironmentalController@waste_type_list');
    Route::post('waste_disposal_list', 'Environmental\EnvironmentalController@waste_disposal_list');
    Route::get('waste_dispose_list', 'Environmental\EnvironmentalController@waste_dispose_list');

	
	///updates za 2-10-2017
 Route::post('Post_partial_payment','Payments\paymentsController@Post_partial_payment');
    Route::post('GetAmountPaidPartial','Payments\paymentsController@GetAmountPaidPartial');
    Route::post('GetPartial_list_summary','Payments\paymentsController@GetPartial_list_summary');
 Route::post('getHospitalShopCashierReports','Payments\paymentsController@getHospitalShopCashierReports');
Route::post('detailedDataHospitalShop','Payments\paymentsController@detailedDataHospitalShop');
    Route::post('getDetailedReportsHospitalShop','Payments\paymentsController@getDetailedReportsHospitalShop');

	
	// .......appointment general

    Route::post('Save_general_appointments', 'TB\Tb_Controller@Save_general_appointments');
    Route::post('appointment_list', 'TB\Tb_Controller@appointment_list');
    Route::post('Update_general_appointment', 'TB\Tb_Controller@Update_general_appointment');
    Route::post('today_appointments', 'TB\Tb_Controller@today_appointments');

    //hizi ni new....
    Route::post('appointment_stages', 'TB\Tb_Controller@appointment_stages');
    Route::post('appointment_dated', 'TB\Tb_Controller@appointment_dated');
    Route::post('dept_user_configure', 'TB\Tb_Controller@dept_user_configure');
    Route::get('SelectedUserWithDeptAccess/{user}', 'TB\Tb_Controller@SelectedUserWithDeptAccess');
    Route::get('Remove_user_dept_access/{id}', 'TB\Tb_Controller@Remove_user_dept_access');
//rch'''''''''''''''''''''''''''''rch....................anti natal

	
	
 Route::post('opd_nursing_report', 'nursing_care\OpdNursingController@opd_nursing_report');
 Route::get('cancel_opd_dosage/{patient_id}', 'nursing_care\OpdNursingController@cancel_opd_dosage');
   	
 Route::post('opd_nurse_service', 'nursing_care\OpdNursingController@opd_nurse_service');
 Route::post('SaveOpdService', 'nursing_care\OpdNursingController@SaveOpdService');
 Route::post('ViewProgressDosage', 'nursing_care\OpdNursingController@ViewProgressDosage');
 Route::post('ViewDosageCompleteness', 'nursing_care\OpdNursingController@ViewDosageCompleteness');
 Route::post('checkServicePaymentStatus', 'nursing_care\OpdNursingController@checkServicePaymentStatus');
 Route::get('getOnGoingDosage/{facility_id}','nursing_care\OpdNursingController@getOnGoingDosage');
 Route::get('loadPatientDosagePregres/{visit_id}','nursing_care\OpdNursingController@loadPatientDosagePregres');
 Route::get('Blood_request_queue/{facility_id}', 'BloodBank\BloodBankController@Blood_request_queue');	
 Route::get('Blood_request_queue/{facility_id}', 'BloodBank\BloodBankController@Blood_request_queue');
 Route::post('blood_bank_registration', 'BloodBank\BloodBankController@blood_bank_registration');
 //  mtuha
//rch
    Route::post('getChildAttendanceReport','reports\reportsController@getChildAttendanceReport');
    Route::post('getChildfeedingReport','reports\reportsController@getChildfeedingReport');
    Route::post('getChilddewormgivenReport','reports\reportsController@getChilddewormgivenReport');

	//tb.......
    Route::post('Tb_mtuha','reports\reportsController@Tb_mtuha');
//  mtuha

    //users proffesionals routes
	Route::resource('professional_registration', 'Professional\ProfessionalController');
	Route::post('update_professional', 'Professional\ProfessionalController@update_professional');
	Route::get('deleteprof/{id}', 'Professional\ProfessionalController@deleteprof');


	//Ame routes
	Route::post('updatepatient', 'registration\PatientController@updatepatient');
	//Ame routes
	//Sample Registration
	Route::post('sample_status_registration', 'laboratory\LaboratoryController@sample_status_registration');
	Route::get('getsample_status', 'laboratory\LaboratoryController@getsample_status');
	Route::post('sample_status_update', 'laboratory\LaboratoryController@sample_status_update');
	Route::get('sample_status_delete/{id}', 'laboratory\LaboratoryController@sample_status_delete');
	
	//Equipment Status Registration
	Route::post('equipment_status_registration', 'laboratory\LaboratoryController@equipment_status_registration');
	Route::get('getequipement_status', 'laboratory\LaboratoryController@getequipement_status');
	Route::post('equipement_status_update', 'laboratory\LaboratoryController@equipement_status_update');
	Route::get('equipement_status_delete/{id}', 'laboratory\LaboratoryController@equipement_status_delete');
	
	//Equipment Registration
	Route::post('equipment_registration', 'laboratory\LaboratoryController@equipment_registration');
	Route::get('getequipement', 'laboratory\LaboratoryController@getequipement');
	Route::get('get_department', 'laboratory\LaboratoryController@get_department');
	Route::get('getsub_department', 'laboratory\LaboratoryController@getsub_department');
	Route::post('equipement_update', 'laboratory\LaboratoryController@equipement_update');
	Route::get('equipement_delete/{id}', 'laboratory\LaboratoryController@equipement_delete');
	
	//Lab Test Registration
	Route::post('lab_test_registration', 'laboratory\LaboratoryController@lab_test_registration'); 
	Route::post('get_lab_test', 'laboratory\LaboratoryController@get_lab_test'); 
	Route::post('createLabsOrderNo', 'laboratory\LaboratoryController@createLabsOrderNo'); 
	
	//Sub Department 
	Route::post('sub_department_registration', 'laboratory\LaboratoryController@sub_department_registration');
	Route::post('sub_department_update', 'laboratory\LaboratoryController@sub_department_update');
	Route::get('sub_department_delete/{id}', 'laboratory\LaboratoryController@sub_department_delete');
	//Get Items
    Route::post('getitem', 'laboratory\LaboratoryController@getitem');

	
	
	//Patient Search
	Route::post('getpatient', 'laboratory\LaboratoryController@getpatient');
	//Get Lab Service Details
	Route::post('getservice', 'laboratory\LaboratoryController@getservice');
	//Send to Lab
	Route::post('send_to_lab', 'laboratory\LaboratoryController@send_to_lab');
	//get_lab_order
	Route::get('get_lab_order', 'laboratory\LaboratoryController@get_lab_order');
	Route::get('getlabrequestperdepartment', 'laboratory\LaboratoryController@getlabrequestperdepartment');
	Route::get('getsubdepartment', 'laboratory\LaboratoryController@getsubdepartment');
	Route::get('get_lab_order_collected', 'laboratory\LaboratoryController@get_lab_order_collected');
	//Route::get('get_lab_order_status', 'laboratory\LaboratoryController@get_lab_order_status');
	Route::post('getpatientlaborder', 'laboratory\LaboratoryController@getpatientlaborder');
	Route::post('getlaborderperdepartment', 'laboratory\LaboratoryController@getlaborderperdepartment');
	Route::post('getlaborapproves', 'laboratory\LaboratoryController@getlaborapproves');
	Route::get('getlaborapproved', 'laboratory\LaboratoryController@getlaborapproved');
	Route::post('approveresult', 'laboratory\LaboratoryController@approveresult');
	
	
	Route::post('savepatientlaborder', 'laboratory\LaboratoryController@savepatientlaborder');
	Route::post('postpatientlaborder', 'laboratory\LaboratoryController@postpatientlaborder');
	Route::post('send_lab_result', 'laboratory\LaboratoryController@send_lab_result');
	///Ame
	Route::post('getunit', 'laboratory\LaboratoryController@getunit');
	Route::post('testsample_update', 'laboratory\LaboratoryController@testsample_update');
	Route::post('getcolor', 'laboratory\LaboratoryController@getcolor');
	Route::post('getindicator', 'laboratory\LaboratoryController@getindicator');
	Route::post('test_panel_registration', 'laboratory\LaboratoryController@test_panel_registration');
	Route::post('test_indicator_registration', 'laboratory\LaboratoryController@test_indicator_registration');
	Route::post('test_sample_registration', 'laboratory\LaboratoryController@test_sample_registration');
	Route::post('test_unit_registration', 'laboratory\LaboratoryController@test_unit_registration');
	Route::get('gettest_panel', 'laboratory\LaboratoryController@gettest_panel');
	Route::get('gettest_unit', 'laboratory\LaboratoryController@gettest_unit');
	Route::post('test_unit_update', 'laboratory\LaboratoryController@test_unit_update');
	Route::get('test_unit_delete/{id},{unit}', 'laboratory\LaboratoryController@test_unit_delete');
	
	Route::get('get_test_sample', 'laboratory\LaboratoryController@get_test_sample');
	Route::get('getlab_test_indicator', 'laboratory\LaboratoryController@getlab_test_indicator');
	Route::post('test_panel_update', 'laboratory\LaboratoryController@test_panel_update');
	Route::post('test_indicator_update', 'laboratory\LaboratoryController@test_indicator_update');
	Route::get('test_panel_delete/{id},{panel_name}', 'laboratory\LaboratoryController@test_panel_delete');
	Route::get('testsample_delete/{id},{sample_to_collect}', 'laboratory\LaboratoryController@testsample_delete');
	Route::get('test_indicator_delete/{id},{indicator}', 'laboratory\LaboratoryController@test_indicator_delete');
	Route::post('getSampleStatus', 'laboratory\LaboratoryController@getSampleStatus');
	Route::post('getPermanceAtLab', 'laboratory\LaboratoryController@getPermanceAtLab');
	Route::post('v1/requestInvestigations', 'laboratory\LaboratoryController@requestInvestigations');
    Route::post('v1/requestDrugs', 'laboratory\LaboratoryController@requestDrugs');
	
	Route::post('getsampleReport', 'laboratory\LaboratoryController@getsampleReport');
	Route::post('rePrintResults', 'laboratory\LaboratoryController@rePrintResults');
	Route::post('getsubdepartments', 'laboratory\LaboratoryController@getsubdepartments');
	Route::post('addSingleTest', 'laboratory\LaboratoryController@addSingleTest');
	Route::post('getequpstatus', 'laboratory\LaboratoryController@getequpstatus');
	Route::post('getfacility', 'laboratory\LaboratoryController@getfacility');
	
	Route::post('getequipements', 'laboratory\LaboratoryController@getequipements');
	Route::get('getlabrequest/{facility_id}', 'laboratory\LaboratoryController@getlabrequest');
	Route::post('saveEquipChanges', 'laboratory\LaboratoryController@saveEquipChanges');


//IMAGING DEPARTMENT   @ JAPHARI M MBARU
    Route::get('getradiologypatients', 'Radiology\RadiologyController@getradiologypatients');
     Route::post('permittedUsers', 'Radiology\RadiologyController@permittedUsers');
     Route::post('getUsers', 'Radiology\RadiologyController@getUsers');
     
    
    Route::post('xrayImage', 'Radiology\RadiologyController@xrayImage');
    Route::post('assignPermission', 'Radiology\RadiologyController@assignPermission');
    Route::post('userPermittedUpdates', 'Radiology\RadiologyController@userPermittedUpdates');
    
    
    Route::post('getPostedResults', 'Radiology\RadiologyController@getPostedResults');
    Route::post('getPatientQueXray', 'Radiology\RadiologyController@getPatientQueXray');
    Route::get('getXrayImage', 'Radiology\RadiologyController@getXrayImage');
    Route::get('getEquipmentStatus', 'Radiology\RadiologyController@getEquipmentStatus');
    Route::get('investigationData', 'Radiology\RadiologyController@investigationData');
    Route::get('getItemCategory', 'Radiology\RadiologyController@getItemCategory');
    Route::get('getRegistered_departments', 'Radiology\RadiologyController@getRegistered_departments');
    Route::get('getEquipments_list', 'Radiology\RadiologyController@getEquipments_list');
    Route::get('getEquipments_status/{facility_id}', 'Radiology\RadiologyController@getEquipments_status');
    Route::post('departmentRegistration', 'Radiology\RadiologyController@departmentRegistration');

    Route::post('getAllRadiographics','Radiology\RadiologyController@getAllRadiographics');
    Route::post('departments','Radiology\RadiologyController@departmentRegistration');
    Route::post('getRequestFormData','Radiology\RadiologyController@getRequestFormData');
    Route::post('prevReqRecord','Radiology\RadiologyController@prevReqRecord');
    Route::post('FindingsSaveRegister','Radiology\RadiologyController@FindingsSaveRegister');
    Route::post('doctorRequest','Radiology\RadiologyController@doctorRequest');
    Route::post('verifyPerPatients','Radiology\RadiologyController@verifyPerPatients');
    Route::post('verifyPerRequests','Radiology\RadiologyController@verifyPerRequests');
    Route::post('xray-patients','Radiology\RadiologyController@getPatientQueXrayNotInList');
    Route::get('departments', 'Radiology\RadiologyController@getdepartments');
    Route::get('departments/by-facility/{id}', 'Radiology\RadiologyController@getdepartmentByFacility');

    Route::get('getdepartments', 'Radiology\RadiologyController@getdepartments');
    Route::post('statusRegistration', 'Radiology\RadiologyController@statusRegistration');
    Route::post('equipmentRegistration', 'Radiology\RadiologyController@equipmentRegistration');
    Route::post('statusUpdate', 'Radiology\RadiologyController@statusUpdate');
    Route::post('VerifyXrays', 'Radiology\RadiologyController@VerifyXrays');
    Route::post('departmentUpdate', 'Radiology\RadiologyController@departmentUpdate');
    Route::get('departmentDelete/{id}', 'Radiology\RadiologyController@departmentDelete');
    Route::get('statusDelete/{id}', 'Radiology\RadiologyController@statusDelete');
    Route::post('RadiologyUpdate', 'Radiology\RadiologyController@RadiologyUpdate');
    Route::get('imageStatus/{patient_id}', 'Radiology\RadiologyController@imageStatus');
    Route::post('DeleteXray', 'Radiology\RadiologyController@DeleteXray');
    Route::post('equipmentOnOff', 'Radiology\RadiologyController@equipmentOnOff');
    Route::post('getServicedata', 'Radiology\RadiologyController@getServicedata');
    Route::get('deviceName/{facility_id}', 'Radiology\RadiologyController@deviceName');
    Route::get('deviceServices/{facility_id}', 'Radiology\RadiologyController@deviceServices');
    Route::get('usersSubdepartments/{facility_id}', 'Radiology\RadiologyController@usersSubdepartments');
    Route::get('deniedDevices/{facility_id}', 'Radiology\RadiologyController@deniedDevices');
    Route::get('OnnOffDevices/{facility_id}', 'Radiology\RadiologyController@OnnOffDevices');
    Route::get('PatientsXray/{facility_id}', 'Radiology\RadiologyController@PatientsXray');
    Route::get('getdiagnosis/{patient_id}', 'Radiology\RadiologyController@getdiagnosis');

    Route::post('SearchPatientInXray', 'Radiology\RadiologyController@SearchPatientInXray');
    Route::post('getRegisteredServices', 'Radiology\RadiologyController@getRegisteredServices');
    Route::post('DeactivateUser', 'Radiology\RadiologyController@DeactivateUser');
    Route::post('userRegistration', 'Radiology\RadiologyController@userRegistration');
    Route::post('SaveImage', 'Radiology\RadiologyController@SaveImage');
    Route::post('getRejestaReport', 'Radiology\RadiologyController@getRejestaReport');
    Route::post('requestedInvestigation', 'Radiology\RadiologyController@requestedInvestigation');
    Route::post('skullInvestigation', 'Radiology\RadiologyController@skullInvestigation');
    Route::post('chestInvestigation', 'Radiology\RadiologyController@chestInvestigation');
    Route::post('abdomenInvestigation', 'Radiology\RadiologyController@abdomenInvestigation');
    Route::post('spineInvestigation', 'Radiology\RadiologyController@spineInvestigation');
    Route::post('pelvisInvestigation', 'Radiology\RadiologyController@pelvisInvestigation');
    Route::post('extremitiesInvestigation', 'Radiology\RadiologyController@extremitiesInvestigation');
    Route::post('HSGInvestigation', 'Radiology\RadiologyController@HSGInvestigation');
    Route::post('serviceRegistration', 'Radiology\RadiologyController@serviceRegistration');
    Route::post('InvestigationRegistration', 'Radiology\RadiologyController@InvestigationRegistration');
    Route::post('InvestigationPart', 'Radiology\RadiologyController@InvestigationPart');
    Route::post('loadPatientRadiologyRequest', 'Radiology\RadiologyController@loadPatientRadiologyRequest');
    Route::get('ServiceDelete/{id}', 'Radiology\RadiologyController@ServiceDelete');
    Route::get('getUserDepartment/{facility_id}', 'Radiology\RadiologyController@getUserDepartment');
    Route::get('getRejestaReport/{facility_id}', 'Radiology\RadiologyController@getRejestaReport');



//  EMERGENCY ROUTES @JAPHARI M MBARU
//    Emergency Registration
    Route::post('configCasualtry', 'Emergency\EmergencyController@configCasualtry');
    Route::post('casualtyEncounter', 'Emergency\EmergencyController@casualtyEncounter');
    Route::post('getCasualtyPatientsReport', 'Emergency\EmergencyController@getCasualtyPatientsReport');
    Route::post('getCasualtyProcedures', 'Emergency\EmergencyController@getCasualtyProcedures');
    Route::post('loadEmergencyCount', 'Emergency\EmergencyController@loadEmergencyCount');
    Route::post('emergencyTypeSave', 'Emergency\EmergencyController@emergencyTypeSave');
    Route::post('search-patients', 'Emergency\EmergencyController@registeredPatients');
    Route::post('patient_edit', 'Emergency\EmergencyController@patient_edit');
    Route::post('enterEncounterEmergency', 'Emergency\EmergencyController@enterEncounterEmergency');
    Route::post('patient_exemption_emergency', 'Emergency\EmergencyController@patient_exemption_emergency');
    Route::post('edit_all_data', 'Emergency\EmergencyController@edit_all_data');
    Route::post('emergency_type', 'Emergency\EmergencyController@emergency_type');
    Route::post('search-residents', 'Emergency\EmergencyController@registeredResidents');
    Route::post('getReportedCasualty', 'Emergency\EmergencyController@getReportedCasualty');
    Route::post('reportsCasualty', 'Emergency\EmergencyController@reportsCasualty');
	Route::get('patientsInformation/{id}', 'Emergency\EmergencyController@patientsInformation');
    Route::get('emergency_type_list', 'Emergency\EmergencyController@emergency_type_list');
    Route::get('emergency_report', 'Emergency\EmergencyController@emergency_report');


    Route::post('patient_emergence_registration', 'Emergency\EmergencyController@patient_emergence_registration');
    Route::post('urgency_registration', 'Emergency\EmergencyController@urgency_registration');
    Route::post('enteremergencyEncounter', 'Emergency\EmergencyController@enteremergencyEncounter');
    Route::post('urgencyEncounter', 'Emergency\EmergencyController@urgencyEncounter');
    Route::post('getEmPatients', 'Emergency\EmergencyController@getEmPatients');
    Route::post('enterEncounterEm', 'Emergency\EmergencyController@enterEncounterEm');
    Route::post('getTretPatients', 'Emergency\EmergencyController@getTretPatients');
    Route::post('printLastVisitEm', 'Emergency\EmergencyController@printLastVisitEm');
    Route::post('searchPatientServicesEm', 'Emergency\EmergencyController@searchPatientServicesEm');
    Route::post('SaveAppearance', 'Emergency\EmergencyController@SaveAppearance');
    Route::post('SaveAirway', 'Emergency\EmergencyController@SaveAirway');
    Route::post('getPatientServicesEm','Emergency\EmergencyController@getPatientServicesEm');
    Route::post('SaveCirculation', 'Emergency\EmergencyController@SaveCirculation');
    Route::post('SaveResponsiveness', 'Emergency\EmergencyController@SaveResponsiveness');
    Route::post('SaveIntervention', 'Emergency\EmergencyController@SaveIntervention');
    Route::post('SaveExposure', 'Emergency\EmergencyController@SaveExposure');
    Route::post('quick_registrationEm', 'Emergency\EmergencyController@quick_registrationEm');
    Route::post('investigationListEm','Emergency\EmergencyController@investigationListEm');
    Route::post('getAllOpdPatientsEm','Emergency\EmergencyController@getAllOpdPatientsEm');
    Route::post('getAllInvPatientsEm','Emergency\EmergencyController@getAllInvPatientsEm');
    
//    VITAL SIGNS @ JAPHARI MUNA
    Route::get('vitalSignsUsers/{facility_id}', 'Vitals\VitalSignController@vitalSignsUsers');
    Route::get('getVitalsAccount/{patient_id}', 'Vitals\VitalSignController@getVitalsAccount');
    Route::get('getVitalsDate/{patient_id}', 'Vitals\VitalSignController@getVitalsDate');
    Route::get('getVitals', 'Vitals\VitalSignController@getVitals');
    Route::post('getVitalsPatients', 'Vitals\VitalSignController@getVitalsPatients');
    Route::post('vitalSignsUsers', 'Vitals\VitalSignController@vitalSignsUsers');
    Route::post('prevVitalRecord', 'Vitals\VitalSignController@prevVitalRecord');
    Route::post('vitals-patients', 'Vitals\VitalSignController@vitalPatients');
    Route::post('VitalSignRegister', 'Vitals\VitalSignController@VitalSignRegister');
    Route::post('previousVisitsVitals', 'Vitals\VitalSignController@previousVisitsVitals');

//    CARDIOLOGY CLINIC @JAPHARI MUNA
    Route::get('getloadedClinic', 'Cardiac\cardiacController@getloadedClinic');
    Route::get('cardiacCapacity', 'Cardiac\cardiacController@cardiacCapacity');
    Route::post('saveCardioSetup', 'Cardiac\cardiacController@saveCardioSetup');
    Route::post('getCardiacPatients', 'Cardiac\cardiacController@getCardiacPatients');
    Route::post('getCardiacPatientsFromDoctor', 'Cardiac\cardiacController@getCardiacPatientsFromDoctor');
    Route::post('editCardioSetup', 'Cardiac\cardiacController@editCardioSetup');
    Route::post('setAppointmentCardiac', 'Cardiac\cardiacController@setAppointmentCardiac');
    Route::post('ongoingCardiac', 'Cardiac\cardiacController@ongoingCardiac');
    Route::post('cardiac-apointment', 'Cardiac\cardiacController@appointment_search');
    Route::post('cardiac-refer', 'Cardiac\cardiacController@appointment_refer');

    //    DIABETIC CLINIC @JAPHARI MUNA
    Route::post('diabeticReception', 'Diabetic\diabeticController@diabeticReception');
    Route::post('getDiabeticPatientsFromDoctor', 'Diabetic\diabeticController@getDiabeticPatientsFromDoctor');
    Route::post('ongoingDiabetic', 'Diabetic\diabeticController@ongoingDiabetic');
    Route::post('diabetic-apointment', 'Diabetic\diabeticController@diabeticOngoing');
    Route::post('setAppointmentDiabetic', 'Diabetic\diabeticController@setAppointmentDiabetic');
    Route::post('diabeticOngoing', 'Diabetic\diabeticController@diabeticOngoing');
    Route::post('diabeticSearch', 'Diabetic\diabeticController@diabeticSearch');

//    PHYSIOTHERAPY CLINIC @JAPHARI MUNA
    Route::post('getPhysioPatients', 'Physiotherapy\physiotherapyController@getPhysioPatients');
    Route::post('getPhysioPatientsFromDoctor', 'Physiotherapy\physiotherapyController@getPhysioPatientsFromDoctor');
    Route::post('physio-apointment', 'Physiotherapy\physiotherapyController@physio_search');
    Route::post('physio-refer', 'Physiotherapy\physiotherapyController@appointment_referPhysio');
    Route::post('setAppointmentPhysio', 'Physiotherapy\physiotherapyController@setAppointmentPhysio');
    Route::post('ongoingPhysio', 'Physiotherapy\physiotherapyController@ongoingPhysio');
    Route::post('setContinuePhysio', 'Physiotherapy\physiotherapyController@setContinuePhysio');
    Route::post('postfamily', 'Physiotherapy\physiotherapyController@postfamily');
    Route::post('saveSpecific', 'Physiotherapy\physiotherapyController@saveSpecific');
    Route::post('saveNeurology', 'Physiotherapy\physiotherapyController@saveNeurology');
    Route::post('saveSummary', 'Physiotherapy\physiotherapyController@saveSummary');
    Route::post('saveAim', 'Physiotherapy\physiotherapyController@saveAim');
    Route::post('savePlans', 'Physiotherapy\physiotherapyController@savePlans');
    Route::post('saveTreatment', 'Physiotherapy\physiotherapyController@saveTreatment');
    Route::post('therapy_treatments', 'Physiotherapy\physiotherapyController@therapy_treatments');
    Route::post('therapy_assessments', 'Physiotherapy\physiotherapyController@therapy_assessments');
    Route::post('saveGeneral', 'Physiotherapy\physiotherapyController@saveGeneral');
    Route::post('getSearchPhysio', 'Physiotherapy\physiotherapyController@getSearchPhysio');
    Route::post('saveWorkingDiagnosis', 'Physiotherapy\physiotherapyController@saveWorkingDiagnosis');
    Route::post('getReportedAppointment', 'Physiotherapy\physiotherapyController@getReportedAppointment');
    Route::post('reportsAppointments', 'Physiotherapy\physiotherapyController@reportsAppointments');
 Route::post('showSearchCorpse', 'Mortuary\MortuaryController@showSearchCorpse');

//    TRAUMA  ROUTES
    Route::post('getCorpseTrauma','Trauma\TraumaEmergencyController@getCorpseTrauma');
    Route::post('TraumaPrevHistory','Trauma\TraumaEmergencyController@TraumaPrevHistory');
    Route::post('TraumaGetPrevDiagnosis','Trauma\TraumaEmergencyController@TraumaGetPrevDiagnosis');
    Route::post('TraumaPerformance','Trauma\TraumaEmergencyController@TraumaPerformance');
    Route::post('TraumaGetPrevRos','Trauma\TraumaEmergencyController@TraumaGetPrevRos');
    Route::post('TraumaChiefComplaints','Trauma\TraumaEmergencyController@TraumaChiefComplaints');
    Route::post('TraumaPostHistory','Trauma\TraumaEmergencyController@TraumaPostHistory');
    Route::post('TraumaGetPrevBirth','Trauma\TraumaEmergencyController@TraumaGetPrevBirth');
    Route::post('TraumaPostHpi','Trauma\TraumaEmergencyController@TraumaPostHpi');
    Route::post('TraumaGetInvestigationResults','Trauma\TraumaEmergencyController@TraumaGetInvestigationResults');
    Route::post('TraumaUnavailableInvestigations','Trauma\TraumaEmergencyController@TraumaUnavailableInvestigations');
    Route::post('TraumaGetPanels','Trauma\TraumaEmergencyController@TraumaGetPanels');
    Route::post('TraumaGetSubDepts','Trauma\TraumaEmergencyController@TraumaGetSubDepts');
    Route::post('TraumaPostDiagnosis','Trauma\TraumaEmergencyController@TraumaPostDiagnosis');
    Route::post('TraumaPostRoS','Trauma\TraumaEmergencyController@TraumaPostRoS');
    Route::post('TraumaPostInvestigations','Trauma\TraumaEmergencyController@TraumaPostInvestigations');
    Route::post('TraumaPostPhysical','Trauma\TraumaEmergencyController@TraumaPostPhysical');
    Route::post('TraumaPostLocalPhysical','Trauma\TraumaEmergencyController@TraumaPostLocalPhysical');
    Route::post('TraumaPostGenPhysical','Trauma\TraumaEmergencyController@TraumaPostGenPhysical');
    Route::post('TraumaPostPastMed','Trauma\TraumaEmergencyController@TraumaPostPastMed');
    Route::post('TraumaReviewOfSystems','Trauma\TraumaEmergencyController@TraumaReviewOfSystems');
    Route::post('TraumaPatients','Trauma\TraumaEmergencyController@TraumaPatients');
    Route::post('TraumaGetAllergies','Trauma\TraumaEmergencyController@TraumaGetAllergies');
    Route::post('TraumaVitalsTime','Trauma\TraumaEmergencyController@TraumaVitalsTime');
    Route::post('TraumaGetPastProcedures','Trauma\TraumaEmergencyController@TraumaGetPastProcedures');
    Route::post('TraumaGetPastMedicine','Trauma\TraumaEmergencyController@TraumaGetPastMedicine');
    Route::post('TraumaGetInvestigationResults','Trauma\TraumaEmergencyController@TraumaGetInvestigationResults');
    Route::post('TraumaGetPrevFamily','Trauma\TraumaEmergencyController@TraumaGetPrevFamily');
    Route::post('TraumaGetPrevPhysical','Trauma\TraumaEmergencyController@TraumaGetPrevPhysical');
    Route::post('traumaPrevInvestigationResults','Trauma\TraumaEmergencyController@traumaPrevInvestigationResults');
    Route::post('trauma-patients','Trauma\TraumaEmergencyController@PatientsSearchTrauma');
    Route::post('pay-patients','Trauma\TraumaEmergencyController@PatientsSearchPayee');
    Route::post('traumaInv-patients','Trauma\TraumaEmergencyController@PatientsSearchInvestigationTrauma');
    Route::post('TraumaDoctorsPerformance','Trauma\TraumaEmergencyController@TraumaDoctorsPerformance');
    Route::post('TraumaInvestigationList','Trauma\TraumaEmergencyController@TraumaInvestigationList');
 Route::post('saveTbLeprosyResult', 'laboratory\LaboratoryController@saveTbLeprosyResult');
    Route::post('ProveTbLeprosyResult', 'laboratory\LaboratoryController@ProveTbLeprosyResult');
    Route::post('saveTbLeprosyRequest', 'laboratory\LaboratoryController@saveTbLeprosyRequest');
    Route::get('savedTbLeprosyRequestData', 'laboratory\LaboratoryController@savedTbLeprosyRequestData');
    Route::get('getpatientAddress/{resId}', 'laboratory\LaboratoryController@getpatientAddress');
    Route::get('gettb_leprosyResultToApprove/{resId}', 'laboratory\LaboratoryController@gettb_leprosyResultToApprove');
    Route::get('TB_leprosyResultsQueues', 'laboratory\LaboratoryController@TB_leprosyResultsQueues');
    Route::get('TB_leprosyResultsPerRequest/{resID}', 'laboratory\LaboratoryController@TB_leprosyResultsPerRequest');
 Route::get('labItemsList/{id}', 'laboratory\LaboratoryController@labItemsList');
    Route::post('activateOrDeactivateTestPrice', 'laboratory\LaboratoryController@activateOrDeactivateTestPrice');
  Route::post('reconsiliatedBatch', 'Pharmacy\PharmacyItemsController@reconsiliatedBatch');
    Route::post('stock_reconsilliation', 'Pharmacy\PharmacyItemsController@stock_reconsilliation');
    Route::post('getStockReconcilliated', 'Pharmacy\PharmacyItemsController@getStockReconcilliated');
    Route::post('returnStockReconcilliated', 'Pharmacy\PharmacyItemsController@returnStockReconcilliated');
Route::get('TB_leprosyResultsPerPatient/{resID}', 'laboratory\LaboratoryController@TB_leprosyResultsPerPatient');
  Route::post('load_item_price_per_categories', 'Item_setups\Item_priceController@load_item_price_per_categories');
Route::post('cancelsReport', 'Payments\paymentsController@cancelsReport');
 Route::post('getDischargedReport','nursing_care\nursingCareController@getDischargedReport');


Route::post('RnRSearch', 'Pharmacy\PharmacyItemsController@RnRSearch');
Route::post('preparernr', 'Pharmacy\PharmacyItemsController@preparernr');
Route::post('rnrSavedOrder', 'Pharmacy\PharmacyItemsController@rnrSavedOrder');
Route::post('LoadrnrSavedOrder_numbers', 'Pharmacy\PharmacyItemsController@LoadrnrSavedOrder_numbers');
Route::post('cancelpreparedrnr', 'Pharmacy\PharmacyItemsController@cancelpreparedrnr');
Route::post('Deletepreparedrnr', 'Pharmacy\PharmacyItemsController@Deletepreparedrnr');
Route::post('Initiatepreparedrnr', 'Pharmacy\PharmacyItemsController@Initiatepreparedrnr');
Route::post('Updatepreparedrnr', 'Pharmacy\PharmacyItemsController@Updatepreparedrnr');
Route::post('LoadRnROrderStatus', 'Pharmacy\PharmacyItemsController@LoadRnROrderStatus');
Route::get('PharmacyLists', 'Pharmacy\PharmacyItemsController@PharmacyLists');
Route::post('UpdateItemDetails', 'Pharmacy\PharmacySetupController@UpdateItemDetails');
Route::post('singleItemUomUpdate', 'Pharmacy\PharmacySetupController@singleItemUomUpdate');
Route::post('singleItemCodeUpdate', 'Pharmacy\PharmacySetupController@singleItemCodeUpdate');
Route::post('singleItemMsdProductUpdate', 'Pharmacy\PharmacySetupController@singleItemMsdProductUpdate');
Route::post('UpdateRnrOrderRowData', 'Pharmacy\PharmacyItemsController@UpdateRnrOrderRowData');
Route::post('DeleteItemOrderRow', 'Pharmacy\PharmacyItemsController@DeleteItemOrderRow');
Route::post('elmisData', 'Pharmacy\PharmacyItemsController@elmisData');
 Route::post('elmisProductProgramMapping', 'Pharmacy\PharmacySetupController@elmisProductProgramMapping');
 Route::get('elmis_transaction_type_list', 'Pharmacy\PharmacySetupController@elmis_transaction_type_list');
Route::post('DiagnosisLIst', 'Patient_tracer\Patient_tracerController@DiagnosisLIst');

 Route::post('RnRstatus', 'Integrations\Dashboard\Data\DashboardReportingController@RnRstatus');
 Route::post('lab_test_life', 'laboratory\LaboratoryController@lab_test_life');

 Route::get('getLabReportingControlList', 'laboratory\LaboratoryController@getLabReportingControlList');
 Route::post('labInticatorMapping', 'laboratory\LaboratoryController@labInticatorMapping');
 Route::post('removeFromLabIndicatorGroupMapping', 'laboratory\LaboratoryController@removeFromLabIndicatorGroupMapping');
 Route::get('indicator_groups', 'laboratory\LaboratoryController@indicator_groups');
 Route::post('labMonthlyReport', 'laboratory\LaboratoryController@labMonthlyReport');

    Route::get('getprev_preg_info/{id}', 'RCH\LabourController@getprev_preg_info');
 Route::post('triageRegisteredReport', 'Trauma\TriageController@triageRegisteredReport');
 Route::post('save-accident-location', 'Trauma\TraumaController@saveAccidentLocation');

    Route::post('getDetailedReportsdepartmentally','Payments\paymentsController@getDetailedReportsdepartmentally');
	
	Route::post("DrfIssuing","Drf\Drf_Controller@DrfIssuing");
    Route::post("LoadStockIssuedDetails","Drf\Drf_Controller@LoadStockIssuedDetails");
    Route::post("DispStockBalance","Drf\Drf_Controller@DispStockBalance");
    Route::post("LoadItemDispensingbalance","Drf\Drf_Controller@LoadItemDispensingbalance");
    Route::post("drf_stock_reconsilliation","Drf\Drf_Controller@drf_stock_reconsilliation");
    Route::post("drfreconcilliationReport","Drf\Drf_Controller@drfreconcilliationReport");
    Route::post("drfreconcilliationReturn","Drf\Drf_Controller@drfreconcilliationReturn");


  Route::post('ipdInvoices','Payments\paymentsController@ipdInvoices');
Route::post('getReferralLists','Patient_tracer\Patient_tracerController@getReferralLists');
Route::post('setIndictorsWardStatus','nursing_care\nursingCareController@setIndictorsWardStatus');
Route::post('getDischargedBillReport','Payments\paymentsController@getDischargedBillReport');
Route::post('rejectedResultsFromMachines','laboratory\LaboratoryController@rejectedResultsFromMachines');

Route::post('enterEncounterTriage', 'Trauma\TriageController@enterEncounterTriage');
 Route::post('getInsurancePerformance','Patient_tracer\Patient_tracerController@getInsurancePerformance');
 
Route::post("getGepGPendings","Drf\Drf_Controller@getGepGPendings");
Route::post("CancelGepGPendings","Drf\Drf_Controller@CancelGepGPendings");




















   

});