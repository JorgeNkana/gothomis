<?php
ini_set('max_execution_time', -1);

use App\ClinicalServices\Tbl_body_system;
use App\Diagnosis\Tbl_diagnosis_description;
use App\Clinics\Tbl_eye_examination;
use App\Exemption\Tbl_exemption_status;
use App\Exemption\Tbl_violence_type;
use App\Item_setups\Tbl_item_category;
use App\Item_setups\Tbl_item_price;
use App\laboratory\Tbl_sample_status;
use App\nursing_care\Tbl_nursing_diagnosise;
use App\nursing_care\Tbl_teeth_arrangement;
use App\Occupation\Tbl_occupation;
use App\Payment_types\Tbl_pay_cat_sub_category;
use App\Payments\Tbl_payment_method;
use App\Pharmacy\Tbl_store_request_status;
use App\Pharmacy\Tbl_store_type;
use App\Pharmacy\Tbl_transaction_type;
use App\RCH\Tbl_vaccination_register;
use App\TB\Tbl_tb_treatment_type;
use App\Tribe\Tbl_tribe;
use Illuminate\Database\Seeder;
use Illuminate\Database\Eloquent\Model;
use App\User;
use App\nursing_care\Tbl_admission_status;
use App\nursing_care\Tbl_payment_status;
use App\nursing_care\Tbl_payments_category;
use App\nursing_care\Tbl_department;
use App\nursing_care\Tbl_country;
use App\nursing_care\Tbl_country_zone;
use App\nursing_care\Tbl_bed_type;
use App\nursing_care\Tbl_marital;
use App\nursing_care\Tbl_proffesional;
use App\nursing_care\Tbl_observation_type;
use App\nursing_care\Tbl_observations_output_type;
use App\Council\Tbl_council_type;
use App\Region\Tbl_region;
use App\Council\Tbl_council;
use App\Residence\Tbl_residence;
use App\Facility\Tbl_facility_type;
use App\Facility\Tbl_facility;
use App\admin\Tbl_role;
use App\admin\Tbl_permission_user;
use App\Patient\Tbl_relationship;
use App\admin\Tbl_permission_role;
use App\nursing_care\Tbl_permission;
use App\nursing_care\Tbl_glyphicon;
use App\nursing_care\Tbl_registrar_service;
use App\Item_setups\Tbl_item;
use App\Item_setups\Tbl_item_type_mapped;
use App\laboratory\Tbl_sub_department;
use App\laboratory\Tbl_equipment_status;
use App\Vital\Tbl_vital;
use App\Pharmacy\Tbl_tracer_medicine;


//for mtuha
use App\Mtuha\Tbl_ipd_mtuha_diagnosis;
use App\Mtuha\Tbl_opd_mtuha_diagnosis;
use App\Mtuha\Tbl_ipd_mtuha_icd_block;
use App\Mtuha\Tbl_opd_mtuha_icd_block;



class DatabaseSeeder extends Seeder
{
    /*
     * Run the database seeds.
     *
     * @return void
     */
    public function run()   {
        Model::unguard();

		
		DB::statement('SET FOREIGN_KEY_CHECKS=0');
		
       DB::table('users')->truncate();       
        DB::table('tbl_payments_categories')->truncate();
        DB::table('tbl_pay_cat_sub_categories')->truncate();
        DB::table('tbl_departments')->truncate();
        DB::table('tbl_countries')->truncate();
        DB::table('tbl_country_zones')->truncate();
        DB::table('tbl_bed_types')->truncate();
        DB::table('tbl_proffesionals')->truncate();
        DB::table('tbl_facility_types')->truncate();
        DB::table('tbl_roles')->truncate();
        DB::table('tbl_permission_roles')->truncate();
        DB::table('tbl_permission_users')->truncate();
        DB::table('tbl_regions')->truncate();
        DB::table('tbl_councils')->truncate();
        DB::table('tbl_council_types')->truncate();
        DB::table('Tbl_residences')->truncate();
        DB::table('tbl_admission_statuses')->truncate();
		DB::table('tbl_payment_statuses')->truncate();
		DB::table('tbl_items')->truncate();
		DB::table('tbl_item_prices')->truncate();
		DB::table('tbl_item_type_mappeds')->truncate();
		DB::table('tbl_permissions')->truncate();
		DB::table('tbl_registrar_services')->truncate();
		DB::table('tbl_payment_methods')->truncate();
		DB::table('tbl_relationships')->truncate();
		DB::table('tbl_tribes')->truncate();
		DB::table('tbl_teeth_arrangements')->truncate();
		DB::table('tbl_nursing_diagnosises')->truncate();
		DB::table('tbl_item_categories')->truncate();
		DB::table('Tbl_ipd_mtuha_icd_blocks')->truncate();
		DB::table('Tbl_opd_mtuha_icd_blocks')->truncate();
		DB::table('Tbl_opd_mtuha_diagnoses')->truncate();
		DB::table('Tbl_ipd_mtuha_diagnoses')->truncate();
		DB::table('tbl_vaccination_registers')->truncate();
		DB::table('tbl_store_types')->truncate();
		DB::table('Tbl_store_request_statuses')->truncate();
		DB::table('Tbl_exemption_statuses')->truncate();
		DB::table('Tbl_violence_types')->truncate();
		DB::table('Tbl_item_categories')->truncate();
		DB::table('Tbl_vitals')->truncate();
		DB::table('Tbl_sample_statuses')->truncate();
		DB::table('Tbl_relationships')->truncate();
		DB::table('Tbl_facilities')->truncate();
		DB::table('Tbl_permission_users')->truncate();
		DB::table('Tbl_registrar_services')->truncate();
		DB::table('Tbl_diagnosis_descriptions')->truncate();
		DB::table('Tbl_eye_examinations')->truncate();
		DB::table('Tbl_observations_output_types')->truncate();
		DB::table('Tbl_observation_types')->truncate();
		DB::table('Tbl_maritalS')->truncate();
		DB::table('Tbl_glyphicons')->truncate();
		DB::table('Tbl_sub_departments')->truncate();
		DB::table('Tbl_equipment_statuses')->truncate();
		DB::table('Tbl_body_systems')->truncate();
		DB::table('Tbl_pay_cat_sub_categories')->truncate();
		DB::table('Tbl_item_categories')->truncate();
		DB::table('Tbl_tb_treatment_types')->truncate();
		DB::table('Tbl_transaction_types')->truncate();
		DB::table('Tbl_occupations')->truncate();
		DB::table('Tbl_eye_examinations')->truncate();
		DB::table('Tbl_tracer_medicines')->truncate();
		
		
		DB::statement('SET FOREIGN_KEY_CHECKS=1');
		$relationships = array(
			['relationship'=>'MOTHER'],
			['relationship'=>'FATHER'],
			['relationship'=>'BROTHER'],
			['relationship'=>'SISTER'],
			['relationship'=>'BROTHER IN-LAW'],
			['relationship'=>'SISTER IN-LAW'],
			['relationship'=>'SON'],
			['relationship'=>'DAUGHTER'],
			['relationship'=>'GRAND FATHER'],
			['relationship'=>'GRAND MOTHER'],
			['relationship'=>'WORK COLEAGUE'],
			['relationship'=>'FRIEND'],
			['relationship'=>'UNCLE'],
			['relationship'=>'AUNT'],
			['relationship'=>'WIFE'],
			['relationship'=>'HUSBAND'],
			['relationship'=>'NONE']
		);
		 
                
				               
					 
	     $admission_statuses= array(
                ['status_name' => 'Pending'],
                ['status_name' =>'Approved'],
                ['status_name' =>'Pending Discharge'],
                ['status_name' =>'Discharged'],
                ['status_name' =>'Transfer Out'],
                ['status_name' =>'Transfer In'],
                ['status_name' =>'Absconded'],
                ['status_name' =>'Dead'],
                ['status_name' =>'DAMA'],
                ['status_name' =>'Serious Patient'],
                     );
        $payment_methods= array(
            ['payment_method' => 'Cash'],
            ['payment_method' => 'GePG'],
                );


	     $teeth_arrangements= array(
                 ['teeth_number' =>7,'teeth_position' =>'A','erasor' =>0],
                 ['teeth_number' =>6,'teeth_position' =>'A','erasor' =>0],
                 ['teeth_number' =>5,'teeth_position' =>'A','erasor' =>0],
                 ['teeth_number' =>4,'teeth_position' =>'A','erasor' =>0],
                 ['teeth_number' =>3,'teeth_position' =>'A','erasor' =>0],
                 ['teeth_number' =>2,'teeth_position' =>'A','erasor' =>0],
                 ['teeth_number' =>1,'teeth_position' =>'A','erasor' =>0],
                 ['teeth_number' =>1,'teeth_position' =>'A','erasor' =>0],
                 ['teeth_number' =>2,'teeth_position' =>'A','erasor' =>0],
                 ['teeth_number' =>3,'teeth_position' =>'A','erasor' =>0],
                 ['teeth_number' =>4,'teeth_position' =>'A','erasor' =>0],
                 ['teeth_number' =>5,'teeth_position' =>'A','erasor' =>0],
                 ['teeth_number' =>6,'teeth_position' =>'A','erasor' =>0],
                 ['teeth_number' =>7,'teeth_position' =>'A','erasor' =>0],
                 ['teeth_number' =>7,'teeth_position' =>'B','erasor' =>0],
                 ['teeth_number' =>6,'teeth_position' =>'B','erasor' =>0],
                 ['teeth_number' =>5,'teeth_position' =>'B','erasor' =>0],
                 ['teeth_number' =>4,'teeth_position' =>'B','erasor' =>0],
                 ['teeth_number' =>3,'teeth_position' =>'B','erasor' =>0],
                 ['teeth_number' =>2,'teeth_position' =>'B','erasor' =>0],
                 ['teeth_number' =>1,'teeth_position' =>'B','erasor' =>0],
                 ['teeth_number' =>1,'teeth_position' =>'B','erasor' =>0],
                 ['teeth_number' =>2,'teeth_position' =>'B','erasor' =>0],
                 ['teeth_number' =>3,'teeth_position' =>'B','erasor' =>0],
                 ['teeth_number' =>4,'teeth_position' =>'B','erasor' =>0],
                 ['teeth_number' =>5,'teeth_position' =>'B','erasor' =>0],
                 ['teeth_number' =>6,'teeth_position' =>'B','erasor' =>0],
                 ['teeth_number' =>7,'teeth_position' =>'B','erasor' =>0],
                     );
					 
		$roles= array(
                ['title' => 'ADMIN','parent' => 'SYSTEM SUPPORT'],
                ['title' => 'DOCTOR','parent' => 'CONSULTATION'],
                ['title' => 'NURSE','parent' => 'NURSING PATIENT'],
                ['title' => 'CASHIER','parent' => 'PAYMENTS PROCESS'],
                ['title' => 'REGISTRAR','parent' => 'CLIENT REGISTRATION'],
                ['title' => 'SOCIAL WELFARE OFFICER','parent' => 'COUNCILING,BILL DISCOUNTS AND  EXEMPTIONS'],
                ['title' => 'PHARMACISTS','parent' => 'PHARMACY MANAGEMENT'],
                ['title' => 'LABTECHNOLOGIST','parent' => 'LABORATORY MANAGEMENT'],
                ['title' => 'RADIOLOGIST','parent' => 'IMAGING MANAGEMENT'],
                ['title' => 'EMERGENCY REGISTRAR','parent' => 'EMERGENCY REGISTRATION'],
                ['title' => 'MORTUARY ATTENDANT','parent' => 'MORTUARY MANAGEMENT'],
                ['title' => 'THEATRE ATTENDANT','parent' => 'THEATRE MANAGEMENT'],
                                
                     );
					 
		$permissions= array(
                ['module' => 'inventory_client','glyphicons' => 'fa fa-plus','title' => 'Inventory Client','keyGenerated' =>Hash::make('inventory_client') ,'main_menu' => 1],
				['module' => 'addModules','glyphicons' => 'fa fa-user','title' => 'Add System Menu','keyGenerated' =>Hash::make('addModules') ,'main_menu' => 1],
				['module' => 'addPermUser','glyphicons' => 'fa fa-user','title' => 'Permission Users','keyGenerated' =>Hash::make('addPermUser') ,'main_menu' => 1],
				['module' => 'addPermRole','glyphicons' => 'fa fa-user-plus','title' => 'Permission Role','keyGenerated' =>Hash::make('addPermRole') ,'main_menu' => 1],
				 ['module' => 'addViews','glyphicons' => 'fa fa-file-archive','title' => 'System Views','keyGenerated' =>Hash::make('addViews') ,'main_menu' => 1],
                 ['module' => 'addUserImage','glyphicons' => 'fa fa-wrench fa-3x','title' => 'Upload User Picture','main_menu' => '1','keyGenerated' =>Hash::make('addUserImage')],
				 ['module' => 'userRegistration','glyphicons' => 'fa fa-file-excel-o','title' => 'Register User','main_menu' => '1','keyGenerated' =>Hash::make('userRegistration')],
				 ['module' => 'payments','glyphicons' => 'fa fa-credit-card','title' => 'Bills Payments','main_menu' => '1','keyGenerated' =>Hash::make('payments')],
				 ['module' => 'point_of_sale','glyphicons' => 'fa fa-credit-card','title' => 'Point of Sale','main_menu' => '1','keyGenerated' =>Hash::make('point_of_sale')],
				 ['module' => 'doctor_opd','glyphicons' => 'fa fa-circle','title' => 'Out Patients','main_menu' => '1','keyGenerated' =>Hash::make('doctor_opd')],
				 ['module'  => 'doctor_ipd','glyphicons' => 'fa fa-circle','title' => 'In Patients','main_menu' => '1','keyGenerated' =>Hash::make('doctor_ipd')],
				 ['module'  => 'icu','glyphicons' => 'fa fa-circle','title' => 'Intensive Care Unit','main_menu' => '1','keyGenerated' =>Hash::make('icu')],
                 ['module'  =>'radiology','glyphicons' => 'fa fa-film','title' => 'Digital Radiograph','main_menu' => '1','keyGenerated' =>Hash::make('radiology')],
                 ['module'  => 'radiologyDepartment','glyphicons' => 'fa fa-film','title' => 'Imaging Department','main_menu' => '1','keyGenerated' =>Hash::make('radiologyDepartment')],
                 ['module'  => 'radiopatients','glyphicons' => 'fa fa-film','title' => 'Imaging Queue','main_menu' => '1','keyGenerated' =>Hash::make('radiopatients')],
                 ['module'  => 'emergency','glyphicons' => 'fa fa-ambulance','title' => 'Emergency Registration','main_menu' => '1','keyGenerated' =>Hash::make('emergency')],
                 ['module'  => 'emergencyDepartment','glyphicons' => 'fa fa-ambulance','title' => 'Resuscitation Room','main_menu' => '1','keyGenerated' =>Hash::make('emergencyDepartment')],
                 ['module'  => 'normalRegistration','glyphicons' => 'fa fa-ambulance','title' => 'Casualty Registration','main_menu' => '1','keyGenerated' =>Hash::make('normalRegistration')],
                 ['module'  => 'treatmentDepartment','glyphicons' => 'fa fa-ambulance','title' => 'Cold Room/Treatment Room','main_menu' => '1','keyGenerated' =>Hash::make('treatmentDepartment')],
                 ['module'  => 'casualtyRoom','glyphicons' => 'fa fa-ambulance','title' => 'Emergency Room','main_menu' => '1','keyGenerated' =>Hash::make('casualtyRoom')],
                 ['module'  => 'observationRoom','glyphicons' => 'fa fa-ambulance','title' => 'Observation Room','main_menu' => '1','keyGenerated' =>Hash::make('observationRoom')],
                 ['module' => 'userRegistration','glyphicons'=>'fa fa-wrench fa-3x','title'=>'User Registration','main_menu'=>1,'keyGenerated' =>Hash::make('userRegistration')],
                 ['module' => 'UsersList','glyphicons'=>'fa fa-list fa-3x','title'=>'Users List','main_menu'=>1,'keyGenerated' =>Hash::make('UsersList')],
                 ['module' => 'password_resset','glyphicons'=>'fa fa-setting fa-3x','title'=>'Password Reset','main_menu'=>1,'keyGenerated' =>Hash::make('password_resset')],
                 ['module' => 'residence','glyphicons'=>'fa fa-home fa-3x','title'=>'Residence Setup','main_menu'=>1,'keyGenerated' =>Hash::make('residence')],
                 ['module' => 'facility','glyphicons'=>'fa fa-wrench fa-3x','title'=>'Facility Setup','main_menu'=>1,'keyGenerated' =>Hash::make('facility')],
                 ['module' => 'facility_list','glyphicons'=>'fa fa-list-o fa-3x','title'=>'Facility List','main_menu'=>1,'keyGenerated' =>Hash::make('facility')],
                 ['module' => 'exemption','glyphicons'=>'fa fa-scisor fa-3x','title'=>'Exemption Setup','main_menu'=>1,'keyGenerated' =>Hash::make('exemption')],
                 ['module' => 'payment_type','glyphicons'=>'fa fa-dollar fa-3x','title'=>'Payment setup','main_menu'=>1,'keyGenerated' =>Hash::make('payment_type')],
                 ['module' => 'item_setup','glyphicons'=>'fa fa-setting fa-3x','title'=>'Item Setup','main_menu'=>1,'keyGenerated' =>Hash::make('item_setup')],
                 ['module' => 'Pharmacy','glyphicons'=>'fa fa-medkit fa-3x','title'=>'Pharmacy Setup','main_menu'=>1,'keyGenerated' =>Hash::make('Pharmacy')],
                 ['module' => 'MainPharmacy','glyphicons'=>'fa fa-medkit fa-3x','title'=>'Main Pharmacy','main_menu'=>1,'keyGenerated' =>Hash::make('MainPharmacy')],
                 ['module' => 'Sub_store','glyphicons'=>'fa fa-medkit fa-3x','title'=>'Sub Store','main_menu'=>1,'keyGenerated' =>Hash::make('Sub_store')],
                 ['module' => 'Dispensing','glyphicons'=>'fa fa-medkit fa-3x','title'=>'Dispensing Window','main_menu'=>1,'keyGenerated' =>Hash::make('Dispensing')],
                 ['module' => 'Discount','glyphicons'=>'fa fa-bar-chart fa-3x','title'=>'Discount','main_menu'=>1,'keyGenerated' =>Hash::make('Discount')],
                 ['module' => 'payment_type','glyphicons'=>'fa fa-dollar fa-3x','title'=>'Payment Type Setup','main_menu'=>1,'keyGenerated' =>Hash::make('payment_type')],
                 ['module' => 'payment_category','glyphicons'=>'fa fa-list-o fa-3x','title'=>'Payment Category Setup','main_menu'=>1,'keyGenerated' =>Hash::make('payment_category')],
                 ['module' => 'item_Price','glyphicons'=>'fa fa-dollar fa-3x','title'=>'Item Price Set Up','main_menu'=>1,'keyGenerated' =>Hash::make('item_Price')],
                 ['module' => 'nursing_care','glyphicons'=>'fa fa-circle fa-3x','title'=>'Nursing Care','main_menu'=>1,'keyGenerated' =>Hash::make('nursing_care')],
                 ['module' => 'ward_management','glyphicons'=>'fa fa-home fa-3x','title'=>'Ward Management','main_menu'=>1,'keyGenerated' =>Hash::make('ward_management')],
                 ['module' => 'exemption_list','glyphicons'=>'fa fa-home fa-3x','title'=>'Exemption Point','main_menu'=>1,'keyGenerated' =>Hash::make('exemption_list')],
                 ['module' => 'reports','glyphicons'=>'fa fa-home fa-3x','title'=>'Financial Reports','main_menu'=>1,'keyGenerated' =>Hash::make('reports')],
                 ['module' => 'laboratory','glyphicons'=>'fa fa-home fa-3x','title'=>'Sample Testing','main_menu'=>1,'keyGenerated' =>Hash::make('laboratory')],
                 ['module' => 'Anti_natal','glyphicons'=>'fa fa-user fa-3x','title'=>'Anti Natal','main_menu'=>1,'keyGenerated' =>Hash::make('Anti_natal')],
                 ['module' => 'Post_natal','glyphicons'=>'fa fa-user fa-3x','title'=>'Post Natal','main_menu'=>1,'keyGenerated' =>Hash::make('Post_natal')],
                 ['module' => 'Labour','glyphicons'=>'fa fa-user fa-3x','title'=>'Labour','main_menu'=>1,'keyGenerated' =>Hash::make('Labour')],
                 ['module' => 'Children','glyphicons'=>'fa fa-child fa-3x','title'=>'Children','main_menu'=>1,'keyGenerated' =>Hash::make('Children')],
                 ['module' => 'Family_Planning','glyphicons'=>'fa fa-users fa-3x','title'=>'Family Planning','main_menu'=>1,'keyGenerated' =>Hash::make('Family_Planning')],
                 ['module' => 'exemption_list','glyphicons'=>'fa fa-scisor fa-3x','title'=>'Social Welfare','main_menu'=>1,'keyGenerated' =>Hash::make('exemption_list')],
                 ['module' => 'Temporary_exemption','glyphicons'=>'fa fa-scisor fa-3x','title'=>'Debtors','main_menu'=>1,'keyGenerated' =>Hash::make('Temporary_exemption')],
                 ['module' => 'patientRegistration','glyphicons'=>'fa fa-dashboard fa-3x','title'=>'Register Patient','main_menu'=>1,'keyGenerated' =>Hash::make('patientRegistration')],
                 ['module' => 'system','glyphicons'=>'fa fa-dashboard fa-3x','title'=>'DB Setup','main_menu'=>1,'keyGenerated' =>Hash::make('system')],
                 ['module' => 'Tb','glyphicons'=>'fa fa-dashboard fa-3x','title'=>'TB Clinic','main_menu'=>1,'keyGenerated' =>Hash::make('system')],
                 ['module' => 'Vct','glyphicons'=>'fa fa-dashboard fa-3x','title'=>'VCT/PITC Clinic','main_menu'=>1,'keyGenerated' =>Hash::make('system')],
                 ['module' => 'Pediatric','glyphicons'=>'fa fa-dashboard fa-3x','title'=>'Paediatric Clinic','main_menu'=>1,'keyGenerated' =>Hash::make('system')],
                 ['module' => 'eye_clinic','glyphicons'=>'fa fa-dashboard fa-3x','title'=>'Eye Clinic','main_menu'=>1,'keyGenerated' =>Hash::make('eye_clinic')],
                 ['module' => 'dental_clinic','glyphicons'=>'fa fa-dashboard fa-3x','title'=>'Dental Clinic','main_menu'=>1,'keyGenerated' =>Hash::make('dental_clinic')],
                 ['module' => 'insurance_management','glyphicons'=>'fa fa-dashboard fa-3x','title'=>'Insurance','main_menu'=>1,'keyGenerated' =>Hash::make('insurance_management')],
                 ['module' => 'inventory','glyphicons'=>'fa fa-dashboard fa-3x','title'=>'Inventory','main_menu'=>1,'keyGenerated' =>Hash::make('inventory')],
                 ['module' => 'referral','glyphicons'=>'fa fa-dashboard fa-3x','title'=>'Referral System','main_menu'=>1,'keyGenerated' =>Hash::make('referral')],
                 ['module' => 'reception','glyphicons'=>'fa fa-dashboard fa-3x','title'=>'Reception','main_menu'=>1,'keyGenerated' =>Hash::make('reception')],
                 ['module' => 'VitalSign','glyphicons'=>'fa fa-dashboard fa-3x','title'=>'Vital Sign','main_menu'=>1,'keyGenerated' =>Hash::make('VitalSign')],
                 ['module' => 'ctcClinic','glyphicons'=>'fa fa-dashboard fa-3x','title'=>'Nurse CTC','main_menu'=>1,'keyGenerated' =>Hash::make('ctcClinic')],
                 ['module' => 'ctcClinicSetup','glyphicons'=>'fa fa-dashboard fa-3x','title'=>'CTC Setup','main_menu'=>1,'keyGenerated' =>Hash::make('ctcClinicSetup')],
                 ['module' => 'DoctorctcClinic','glyphicons'=>'fa fa-dashboard fa-3x','title'=>'CTC Doctor','main_menu'=>1,'keyGenerated' =>Hash::make('DoctorctcClinic')],
                 ['module' => 'labSetting','glyphicons'=>'fa fa-dashboard fa-3x','title'=>'Lab Setting','main_menu'=>1,'keyGenerated' =>Hash::make('labSetting')],
                 ['module' => 'SampleCollection','glyphicons'=>'fa fa-dashboard fa-3x','title'=>'Collect Sample','main_menu'=>1,'keyGenerated' =>Hash::make('SampleCollection')],
                 ['module' => 'physiotherapy','glyphicons'=>'fa fa-dashboard fa-3x','title'=>'Physiotherapy','main_menu'=>1,'keyGenerated' =>Hash::make('physiotherapy')],
                 ['module' => 'diabetic','glyphicons'=>'fa fa-dashboard fa-3x','title'=>'Diabetic','main_menu'=>1,'keyGenerated' =>Hash::make('diabetic')],
                 ['module' => 'nutrition','glyphicons'=>'fa fa-dashboard fa-3x','title'=>'Nutrition','main_menu'=>1,'keyGenerated' =>Hash::make('nutrition')],
                 ['module' => 'Rch_report','glyphicons'=>'fa fa-dashboard fa-3x','title'=>'Rch report','main_menu'=>1,'keyGenerated' =>Hash::make('Rch_report')],
                 ['module' => 'shop','glyphicons'=>'fa fa-dashboard fa-3x','title'=>'Hospital Shop','main_menu'=>1,'keyGenerated' =>Hash::make('shop')],
                 ['module' => 'Medical_clinic','glyphicons'=>'fa fa-dashboard fa-3x','title'=>'Medical Clinic','main_menu'=>1,'keyGenerated' =>Hash::make('Medical_clinic')],
                 ['module' => 'Orthopedic_clinic','glyphicons'=>'fa fa-dashboard fa-3x','title'=>'Orthopedic Clinic','main_menu'=>1,'keyGenerated' =>Hash::make('Orthopedic_clinic')],
                 ['module' => 'BloodBank','glyphicons'=>'fa fa-dashboard fa-3x','title'=>'Blood Bank','main_menu'=>1,'keyGenerated' =>Hash::make('BloodBank')],
                 ['module' => 'AppointmentCardiac','glyphicons'=>'fa fa-dashboard fa-3x','title'=>'Cardiac Appointment','main_menu'=>1,'keyGenerated' =>Hash::make('AppointmentCardiac')],
                 ['module' => 'AppointmentDiabetic','glyphicons'=>'fa fa-dashboard fa-3x','title'=>'Diabetic Appointment','main_menu'=>1,'keyGenerated' =>Hash::make('AppointmentDiabetic')],
                 ['module' => 'AppointmentPhysio','glyphicons'=>'fa fa-dashboard fa-3x','title'=>'Physio Appointment','main_menu'=>1,'keyGenerated' =>Hash::make('AppointmentPhysio')],
                 ['module' => 'patient_tracer','glyphicons'=>'fa fa-dashboard fa-3x','title'=>'Patient Tracer','main_menu'=>1,'keyGenerated' =>Hash::make('patient_tracer')],
                 ['module' => 'Environmental','glyphicons'=>'fa fa-dashboard fa-3x','title'=>'Environmental Health','main_menu'=>1,'keyGenerated' =>Hash::make('Environmental')], 
				 ['module' => 'mortuaryManagement','glyphicons'=>'fa fa-dashboard fa-3x','title'=>'mortuary Management','main_menu'=>1,'keyGenerated' =>Hash::make('mortuaryManagement')],
				 ['module' => 'theatre_managing_list','glyphicons'=>'fa fa-dashboard fa-3x','title'=>'Anaesthesia','main_menu'=>1,'keyGenerated' =>Hash::make('theatre_managing_list')],
				 ['module' => 'doctor_theatre','glyphicons'=>'fa fa-dashboard fa-3x','title'=>'Surgeon','main_menu'=>1,'keyGenerated' =>Hash::make('doctor_theatre')],
				 
				 ['module' => 'General_Appointment','glyphicons'=>'fa fa-dashboard fa-3x','title'=>'General Appointment','main_menu'=>1,'keyGenerated' =>Hash::make('General_Appointment')],
				 
				 ['module' => 'Partial_payment','glyphicons'=>'fa fa-dashboard fa-3x','title'=>'Partial Payment','main_menu'=>1,'keyGenerated' =>Hash::make('Partial_payment')],
				 
				 ['module' => 'obgy_clinic','glyphicons'=>'fa fa-dashboard fa-3x','title'=>'OBSTETRICS AND Gynaecological Clinic','main_menu'=>1,'keyGenerated' =>Hash::make('obgy_clinic')],
	

				['module' => 'Ent_clinic','glyphicons'=>'fa fa-dashboard fa-3x','title'=>'Ent Clinic','main_menu'=>1,'keyGenerated' =>Hash::make('Ent_clinic')],
			 
				 ['module' => 'surgical_clinic','glyphicons'=>'fa fa-dashboard fa-3x','title'=>'Surgical Clinic','main_menu'=>1,'keyGenerated' =>Hash::make('surgical_clinic')],
				 
				 ['module' => 'mtuha_report','glyphicons'=>'fa fa-ambulance fa-3x','title'=>'Mtuha Reports','main_menu'=>1,'keyGenerated' =>Hash::make('mtuha_report')],
                                 ['module' => 'Urology_clinic','glyphicons'=>'fa fa-dashboard fa-3x','title'=>'Urology Clinic','main_menu'=>1,'keyGenerated' =>Hash::make('Urology_clinic')],

                     );

           

        $tbl_sample_statuses= array(
            ['status' => 'Test not available','eraser'=>1],
            ['status' => 'No specimen/sample collection site on requestion','eraser'=>1],
            ['status' => 'No collection date on requistion','eraser'=>1],
            ['status' => 'No test ordered onrequestion','eraser'=>1],
            ['status' => 'No test requestion received','eraser'=>1],
            ['status' => 'Specimen/Sample type unacceptable for test','eraser'=>1],
            ['status' => 'Collection site inappropriate','eraser'=>1],
            ['status' => 'Specimen/Sample ID illegible','eraser'=>1],
            ['status' => 'No specimen/sample Sample ID cannot be established','eraser'=>1],
            ['status' => 'No specimen/sample ID','eraser'=>1],
            ['status' => 'Damaged-too old','eraser'=>1],
            ['status' => 'Damaged-lab accident,unsalvageable','eraser'=>1],
            ['status' => 'Damaged-improper temperature','eraser'=>1],
            ['status' => 'Damaged-improper transport media','eraser'=>1],
            ['status' => 'Damaged-Damaged-improper preservation','eraser'=>1],
            ['status' => 'Damaged-expired transport media','eraser'=>1],
            ['status' => 'Damaged-Contaminated','eraser'=>1],
            ['status' => 'Damaged-Broken or Leaked in transit','eraser'=>1],
            ['status' => 'Lipemic','eraser'=>1],
            ['status' => 'Hemolysed','eraser'=>1],
            ['status' => 'Quantity Not Sufficient','eraser'=>1],
            ['status' => 'No Spacimen/Sample Received','eraser'=>1],
        );


        $council_types= array(
                ['description' => 'TANZANIA'],
                ['description' => 'REGION'],
                ['description' => 'DISTRICT'],
                ['description' => 'COUNCIL'],                
                ['description' => 'DIVISION'],                
                ['description' => 'WARD'],                
                ['description' => 'MTAA/VILLAGE'],                
                ['description' => 'HAMLET'],                
                     );
					 
		$facility_types= array(
                ['description' => 'HEALTH CENTER'],
                ['description' => 'DISPENSARY'],
                ['description' => 'DISTRICT HOSPITAL'],
                ['description' => 'DESIGNATED DISTRICT HOSPITAL'],
                ['description' => 'ZONAL SUPER SPECIALIST HOSPITAL'],
                ['description' => 'REGIONAL REFERRAL HOSPITAL'],
                ['description' => 'OTHER  HOSPITAL'],
                ['description' => 'NATIONAL HOSPITAL'],
                ['description' => 'CLINIC'],
                ['description' => 'HEALTH LABS'],
                ['description' => 'HOSPITAL'],
                ['description' => 'NATIONAL SUPER SPECIALIST HOSPITAL'],
                ['description' => 'REFERRAL HOSPITAL'],
                ['description' => 'NURSING HOME'],
                ['description' => 'MATERNITY HOME'],
                ['description' => 'TOWN COUNCIL HOSPITAL'],
			);
			
			
		$observations_output_types= array(
                ['observations_output_types' => 'URINE'],
                ['observations_output_types' => 'BLOOD'],
                ['observations_output_types' => 'VOMIT'],  
                
                     );
					 
					 
		$facilities= array(
                ['facility_code' => '201701',
                'facility_name' => 'DODOMA HQ',
                'facility_type_id' =>7,
                'address' =>'P.O.BOX 1923,SOKOINE HOUSE',
                'region_id' => 2908,
                'council_id' =>2910,                
                'mobile_number' =>'0262322848',                
                'email' =>'ps@tamisemi.go.tz'],                
                     );
					 
						 
		$proffesionals= array(
                ['prof_name' => 'ACCOUNTANT'],
                ['prof_name' => 'ANAETHETIC'],
                ['prof_name' => 'ASSITANT MEDICAL OFFICER   (AMO)'],
                ['prof_name' => 'ASSISATNT NURSING  (AN)'],
                ['prof_name' => 'CASHIER'],
                ['prof_name' => 'CLINICAL OFFICER   (CO)'],
                ['prof_name' => 'FACILITY HEALTH SECRETARY'],
                ['prof_name' => 'ICT TECHNICIAN'],
                ['prof_name' => 'MEDICAL DOCTOR   (MD)'],
                ['prof_name' => 'NURSING OFFICER (NO)'],
                ['prof_name' => 'UNSPECIFIED'],
                ['prof_name' => 'RECORD OFFICER'],
                ['prof_name' => 'NUTRITIONAL OFFICER'],
                ['prof_name' => 'SECRETARY'],
                ['prof_name' => 'RADIOLOGIST'],
                ['prof_name' => 'REGISTERED NURSING OFFICER (RNO)']
                     );
					 
		$observation_types= array(
                ['observation_name' => 'BLOOD PRESSURE','observation_key_word' => 'BP'],
                ['observation_name' => 'PULSE RATE','observation_key_word' => 'PR'],
                ['observation_name' => 'TEMPERATURE','observation_key_word' => 'T'],
                
		
                     );
					 
		
					 
					 
					
					
					 
	   $country_zones= array(
                ['country_zone' => 'EAST AFRICA'],
                ['country_zone' =>'WEST AFRICA'],
                ['country_zone' =>'UNSPECIFIED'],
			);
			
		$payment_statuses = array(
                ['payment_status' => 'Unpaid'],				
                ['payment_status' =>'Paid'],
                ['payment_status' =>'Cancelled'],
                ['payment_status' =>'Partial Payment'],
                     );
					 
		$permission_roles = array(
		        ['permission_id' => 1,'role_id' =>1,'grant' =>1],
		        ['permission_id' => 2,'role_id' =>1,'grant' =>1],
		        ['permission_id' => 3,'role_id' =>1,'grant' =>1],
		        ['permission_id' => 4,'role_id' =>1,'grant' =>1],
		        ['permission_id' => 5,'role_id' =>1,'grant' =>1],
		        ['permission_id' => 6,'role_id' =>1,'grant' =>1],
		        ['permission_id' => 7,'role_id' =>1,'grant' =>1],
		        ['permission_id' => 8,'role_id' =>4,'grant' =>1],
		        ['permission_id' => 9,'role_id' =>4,'grant' =>1],
		        ['permission_id' => 10,'role_id' =>2,'grant' =>1],
		        ['permission_id' => 11,'role_id' =>2,'grant' =>1],
		        ['permission_id' => 12,'role_id' =>2,'grant' =>1],
                ['permission_id' => 13,'role_id' =>9,'grant' =>1],
                ['permission_id' => 14,'role_id' =>10,'grant' =>1],
                ['permission_id' => 15,'role_id' =>9,'grant' =>1],
                ['permission_id' => 16,'role_id' =>10,'grant' =>1],
                ['permission_id' => 17,'role_id' =>2,'grant' =>1],
                ['permission_id' => 18,'role_id' =>2,'grant' =>1],
                ['permission_id' => 19,'role_id' =>2,'grant' =>1],
                ['permission_id' => 20,'role_id' =>2,'grant' =>1],
                ['permission_id' => 21,'role_id' =>2,'grant' =>1],
		     ['permission_id' => 22,'role_id' =>1,'grant' =>1],
            ['permission_id' => 23,'role_id' =>1,'grant' =>1],
            ['permission_id' => 24,'role_id' =>1,'grant' =>1],
            ['permission_id' => 25,'role_id' =>1,'grant' =>1],
            ['permission_id' => 26,'role_id' =>1,'grant' =>1],
            ['permission_id' => 27,'role_id' =>1,'grant' =>1],
            ['permission_id' => 28,'role_id' =>6,'grant' =>1],
            ['permission_id' => 29,'role_id' =>1,'grant' =>1],
            ['permission_id' => 30,'role_id' =>1,'grant' =>1],
            ['permission_id' => 31,'role_id' =>7,'grant' =>1],
            ['permission_id' => 32,'role_id' =>7,'grant' =>1],
            ['permission_id' => 33,'role_id' =>7,'grant' =>1],
            ['permission_id' => 34,'role_id' =>7,'grant' =>1],
            ['permission_id' => 35,'role_id' =>6,'grant' =>1],
            ['permission_id' => 36,'role_id' =>1,'grant' =>1],
            ['permission_id' => 37,'role_id' =>1,'grant' =>1],
            ['permission_id' => 38,'role_id' =>1,'grant' =>1],
            ['permission_id' => 39,'role_id' =>1,'grant' =>1],
            ['permission_id' => 52,'role_id' =>1,'grant' =>1],

                     );
					 
				 
	  
	   
	   
	   
	   
	   				 
					 
		$bed_types = array(
                ['bed_type' => 'NORMAL METAL BED'],				
                ['bed_type' =>'LABOR AND DELIVERY BED'],
                ['bed_type' =>'SURGICAL BED'],
                ['bed_type' =>'DUMMY BED'],
                     );
					 
		$marital_statuses = array(
                ['marital_status' => 'MARRIED'],			
                ['marital_status' => 'SINGLE'],			
                ['marital_status' => 'DIVORCED'],			
                ['marital_status' => 'CO-HABITING'],			
                ['marital_status' => 'WIDOW'],			
                
                     );
					 
		$payments_categories = array(
                ['category_description' => 'User Fee'],				
                ['category_description' => 'Insurance'],				
                ['category_description' => 'Exemption'],				
                
                     );


		$nursing_diagnosises = array(
                ['diagnosis_name' => 'FEVER'],
                ['diagnosis_name' => 'DIARRHEA'],
                ['diagnosis_name' => 'VOMITING'],
                ['diagnosis_name' => 'BLEEDING'],

                     );


		$glyphicons = array(
                ['icon_class' => 'fa fa-calender fa-3x','icon_name' => 'Calender'],				
                ['icon_class' =>'fa fa-check-square-o fa-3x','icon_name' =>'Checking'],				
                ['icon_class' =>'fa fa-credit-card fa-3x','icon_name' =>'Credit Card'],	
                ['icon_class' =>'fa fa-ambulance fa-3x','icon_name' =>'Checking'],
                ['icon_class' =>'fa fa-film fa-3x','icon_name' =>'Checking'],			
                ['icon_class' =>'fa fa-dashboard fa-3x','icon_name' =>'Dashboard'],				
                ['icon_class' =>'fa fa-pie-chart fa-3x','icon_name' =>'Pie Chart Report'],				
                ['icon_class' =>'fa fa-power-off fa-3x','icon_name' =>'Power Off'],				
                ['icon_class' =>'fa fa-desktop fa-3x','icon_name' =>'Registered'],				
                ['icon_class' =>'fa fa-toggle-on fa-3x','icon_name' =>'Toggle On'],				
                ['icon_class' =>'fa fa-wheelchair fa-3x','icon_name' =>'Wheel Chair'],				
                ['icon_class' =>'fa fa-toggle-off fa-3x','icon_name' =>'Toggle Off'],				
                ['icon_class' =>'fa fa-upload fa-3x','icon_name' =>'Upload Icon'],				
                ['icon_class' =>'fa fa-trash fa-2x','icon_name' =>'Delete Icon'],				
                ['icon_class' =>'fa fa-wrench fa-3x','icon_name' =>'Setting Icon'],				
                ['icon_class' =>'fa fa-server fa-3x','icon_name' =>'Listing Icon'],				
             				
                
                     );
					 
		$departments = array(
                 ['id'=>1,'department_name' => 'OUT PATIENT DEPARTMENT (OPD)'],
				 ['id'=>2,'department_name' => 'LABORATORY'],
                 ['id'=>3,'department_name' => 'IMAGING'],
                 ['id'=>4,'department_name' => 'PHARMACY'],
                 ['id'=>5,'department_name' => 'IN PATIENT DEPARTMENT (IPD)'],
                 ['id'=>7,'department_name' => 'MORTUARY'],

			      ['id'=>8,'department_name' => 'CTC'],
			      ['id'=>9,'department_name' => 'DENTAL'],
			      ['id'=>10,'department_name' => 'EYE'],
                 ['id'=>15,'department_name' => 'TB'],
                 ['id'=>17,'department_name' => 'VCT'],
                 ['id'=>18,'department_name' => 'ANTE-NATAL CARE(ANC)'],
                 ['id'=>19,'department_name' => 'CHILD CARE'],
                 ['id'=>20,'department_name' => 'POST NATAL'],
                 ['id'=>21,'department_name' => 'LABOUR'],
                 ['id'=>22,'department_name' => 'FAMILY PLANNING'],
                 ['id'=>23,'department_name' => 'PAEDIATRIC'],
                 ['id'=>24,'department_name' => 'SOCIAL WELFARE'],
                 ['id'=>26,'department_name' => 'OBSTETRICS AND GYNACOLOGICAL'],
                 ['id'=>27,'department_name' => 'SURGERY'],
                 ['id'=>28,'department_name' => 'INTESIVE CARE UNIT (ICU)'],
                 ['id'=>40,'department_name' => 'PHYSIOTHERAPY'],
                 ['id'=>41,'department_name' => 'DIABETIC'],
                 ['id'=>42,'department_name' => 'CARDIOLOGY'],
                 ['id'=>43,'department_name' => 'DEMATOLOGY'],
                 ['id'=>44,'department_name' => 'THEATRE'],
                 ['id'=>45,'department_name' => 'PSYCHIATRIC'],
                 ['id'=>50,'department_name' => 'NUTRITION'],
                 ['id'=>51,'department_name' => 'ORTHOPEDIC CLINIC'],
                  ['id'=>52,'department_name' => 'ENT CLINIC'],
                 ['id'=>55,'department_name' => 'UROLOGY CLINIC'],                    );

		$lab_departments = array(
                ['sub_department_name' => 'Haematology','department_id'=> 2],
                ['sub_department_name' => 'Microbiology','department_id' => 2],
                ['sub_department_name' => 'Clinical Chemistry','department_id' => 2],
                ['sub_department_name' => 'Serology','department_id' => 2],
                ['sub_department_name' => 'Parasitology','department_id'=> 2],
                ['sub_department_name' => 'TB','department_id'=> 2],
                ['sub_department_name' => 'Immunology','department_id'=> 2],


                     );
        $equipment_statuses = array(
            ['status_name' => 'Under maintainance,testing can not be done','on_off'=>0,'eraser'=> 0],
            ['status_name' => 'No reagent, testing can not be done','on_off'=>0,'eraser'=> 0],
            ['status_name' => 'No Machine Operator ,testing can not be done','on_off'=>0,'eraser'=> 0],
            ['status_name' => 'Equipment not yet start operate, working can not be done','on_off'=>0,'eraser'=> 0],
            ['status_name' => 'Equipment/Reagent is OK, test can be done','on_off'=>1,'eraser'=> 0]
            ,
            ['status_name' => 'Equipment is OK test can be done','on_off'=>1,'eraser'=> 0],
            ['status_name' => 'Equipment has mulfunctional, test can not be done','on_off'=>0,'eraser'=> 0]
                     );
					 
	 
				   


				   
        // Loop through each admission  above and create the record for them in the database
        
		  $tbl_store_types = array(
                ['id' => '2', 'store_type_name' => 'Main Store'],
                ['id' => '3', 'store_type_name' => 'Sub Store'],
                ['id' => '4', 'store_type_name' => 'Dispensing'],
                ['id' => '5', 'store_type_name' => 'Other type'],

        );
        $tbl_store_request_statuses = array(
            ['id' => '1', 'store_request_status' => 'Done'],
            ['id' => '2', 'store_request_status' => 'In Progress'],
            ['id' => '4', 'store_request_status' => 'Pending'],


        );

        $tbl_exemption_statuses = array(
            ['id' => '2', 'exemption_status' => 'Approved'],
            ['id' => '3', 'exemption_status' => 'Pending'],
            ['id' => '4', 'exemption_status' => 'Revoked'],

 
        );

        $tbl_violence_types = array(
            ['id' => '1', 'violence_type_name' => 'GBV'],
            ['id' => '2', 'violence_type_name' => 'VAC'],
            ['id' => '3', 'violence_type_name' => 'GBV/VAC'],




        );


        $systems = array(
            ['name'=>'Fatique',"category"=>"General Review"],
            ['name'=>'Malaise',"category"=>"General Review"],
            ['name'=>'Fever',"category"=>"General Review"],
            ['name'=>'Rigors',"category"=>"General Review"],
            ['name'=>'Night sweats',"category"=>"General Review"],
            ['name'=>'Weight loss',"category"=>"General Review"],
            ['name'=>'Loss of appetite',"category"=>"General Review"],
            ['name'=>'Skin rashes',"category"=>"General Review"],
            ['name'=>'Pruritus',"category"=>"General Review"],
            ['name'=>'Acne',"category"=>"General Review"],

            ['name'=>'Painless swelling',"category"=>"Dental"],
            ['name'=>'Painful swelling',"category"=>"Dental"],
            ['name'=>'Teeth sensitivity',"category"=>"Dental"],
            ['name'=>'Bleeding gums',"category"=>"Dental"],
            ['name'=>'Painful ulcers',"category"=>"Dental"],
            ['name'=>'Painless ulcers',"category"=>"Dental"],
            ['name'=>'Injury on the upper jaw',"category"=>"Dental"],
            ['name'=>'Injury on the lower jaw',"category"=>"Dental"],
            ['name'=>'Injury on the tongue',"category"=>"Dental"],
            ['name'=>'Injury on the lips',"category"=>"Dental"],
            ['name'=>'Toothache',"category"=>"Dental"],

            ['name'=>'Chest pain',"category"=>"Cardiovascular"],
            ['name'=>'Angina',"category"=>"Cardiovascular"],
            ['name'=>'Shortness of breath',"category"=>"Cardiovascular"],
            ['name'=>'Orthopnoea',"category"=>"Cardiovascular"],
            ['name'=>' Paroxysmal nocturnal dyspnoea',"category"=>"Cardiovascular"],
            ['name'=>'Palpitations',"category"=>"Cardiovascular"],
            ['name'=>'Ankle swelling',"category"=>"Cardiovascular"],


            ['name'=>'Chest pain',"category"=>"Respiratory"],
            ['name'=>'wheeze Cough',"category"=>"Respiratory"],
            ['name'=>'Shortness of breath',"category"=>"Respiratory"],
            ['name'=>'Productive cough',"category"=>"Respiratory"],
            ['name'=>'Dry cough',"category"=>"Respiratory"],
            ['name'=>'Foul smelling sputum',"category"=>"Respiratory"],
            ['name'=>'Haemoptysis',"category"=>"Respiratory"],
            ['name'=>'Exercise tolerance',"category"=>"Respiratory"],


            ['name'=>'Loss of  Appetite',"category"=>"Gastrointerstinal"],
            ['name'=>'weight loss',"category"=>"Gastrointerstinal"],
            ['name'=>'Dysphagia',"category"=>"Gastrointerstinal"],
            ['name'=>'Nausea',"category"=>"Gastrointerstinal"],
            ['name'=>'Vomiting',"category"=>"Gastrointerstinal"],
            ['name'=>'Haematemesis',"category"=>"Gastrointerstinal"],
            ['name'=>'Melena',"category"=>"Gastrointerstinal"],
            ['name'=>'Indigestion',"category"=>"Gastrointerstinal"],
            ['name'=>'heart burn',"category"=>"Gastrointerstinal"],
            ['name'=>'Jaundice',"category"=>"Gastrointerstinal"],
            ['name'=>'Abdominal pain',"category"=>"Gastrointerstinal"],
            ['name'=>'Constipation',"category"=>"Gastrointerstinal"],
            ['name'=>'Diarrhoea',"category"=>"Gastrointerstinal"],
            ['name'=>'Blood stool',"category"=>"Gastrointerstinal"],
            ['name'=>'Mucuid stool',"category"=>"Gastrointerstinal"],
            ['name'=>'Passing flatus',"category"=>"Gastrointerstinal"],


            ['name'=>'Kyphoscoliosis',"category"=>"Musculoskeletal"],
            ['name'=>'Scoliosis',"category"=>"Musculoskeletal"],
            ['name'=>'Gibbers',"category"=>"Musculoskeletal"],
            ['name'=>'Kyphosis',"category"=>"Musculoskeletal"],
            ['name'=>'Abnormal spine curvature',"category"=>"Musculoskeletal"],
            ['name'=>'Able to climb up and down stairs',"category"=>"Musculoskeletal"],
            ['name'=>'Able to wash and dress without difficulty',"category"=>"Musculoskeletal"],
            ['name'=>'Restriction of movement ',"category"=>"Musculoskeletal"],
            ['name'=>'Joint stiffness',"category"=>"Musculoskeletal"],
            ['name'=>'Joint swelling',"category"=>"Musculoskeletal"],
            ['name'=>'Joint pain',"category"=>"Musculoskeletal"],
            ['name'=>'Bone Pain',"category"=>"Musculoskeletal"],


            ['name'=>'tingling sensation',"category"=>"Central Nervous System"],
            ['name'=>'Numbness of hand',"category"=>"Central Nervous System"],
            ['name'=>'Numbness of feet',"category"=>"Central Nervous System"],
            ['name'=>'Hearing loss',"category"=>"Central Nervous System"],
            ['name'=>'diplopia',"category"=>"Central Nervous System"],
            ['name'=>'Able to climb up and down stairs',"category"=>"Central Nervous System"],
            ['name'=>'Normlal vision',"category"=>"Central Nervous System"],
            ['name'=>'Dizziness',"category"=>"Central Nervous System"],
            ['name'=>'loss of consciousness',"category"=>"Central Nervous System"],
            ['name'=>'Fits',"category"=>"Central Nervous System"],
            ['name'=>'Faints attack',"category"=>"Central Nervous System"],
            ['name'=>'Headaches',"category"=>"Central Nervous System"],
            ['name'=>'insomnia',"category"=>"Central Nervous System"],
            ['name'=>'ahedonia',"category"=>"Central Nervous System"],
            ['name'=>'depression',"category"=>"Central Nervous System"],
            ['name'=>'Anxiety',"category"=>"Central Nervous System"],
            ['name'=>'personality change',"category"=>"Central Nervous System"],
            ['name'=>'Loss of memory',"category"=>"Central Nervous System"],

            ['name'=>'Menstrual abnormalities',"category"=>"Endocrine"],
            ['name'=>'Hirsutism',"category"=>"Endocrine"],
            ['name'=>'alopecia',"category"=>"Endocrine"],
            ['name'=>'Abnormal secondary sexual features',"category"=>"Endocrine"],
            ['name'=>'Polyuria',"category"=>"Endocrine"],
            ['name'=>'Polydipsia change',"category"=>"Endocrine"],
            ['name'=>'excessive sweating',"category"=>"Endocrine"],
            ['name'=>'Polyuria',"category"=>"Endocrine"],
			
            ['name'=>'Acute otitis media',"category"=>"ENT"],
            ['name'=>'Ceruminosis',"category"=>"ENT"],
            ['name'=>'Dental abscess',"category"=>"ENT"],
            ['name'=>'Pharyngitis',"category"=>"ENT"],
            ['name'=>'Recent change in hearing',"category"=>"ENT"],
            ['name'=>'Increasing headache associated with flexing of the neck',"category"=>"ENT"],
            ['name'=>'Neck enlarged glands',"category"=>"ENT"],
            ['name'=>'Nausea and vomiting',"category"=>"ENT"],
            ['name'=>'Malaise',"category"=>"ENT"],
            ['name'=>'Fever',"category"=>"ENT"],
            ['name'=>'Neck pain',"category"=>"ENT"],
            ['name'=>'Dysphagia',"category"=>"ENT"],
            ['name'=>'Uvula midline',"category"=>"ENT"],
            ['name'=>'Sore throat',"category"=>"ENT"],
            ['name'=>'Bleeding gums',"category"=>"ENT"],
            ['name'=>'Oral lesions',"category"=>"ENT"],
            ['name'=>'Hoarseness or recent voice change',"category"=>"ENT"],
            ['name'=>'Epistaxis',"category"=>"ENT"],
            ['name'=>'Rhinorrhea',"category"=>"ENT"],
            ['name'=>'Obstruction of airflow',"category"=>"ENT"],
            ['name'=>'Anosmia',"category"=>"ENT"],
            ['name'=>'Sneezing',"category"=>"ENT"],
            ['name'=>'Nasal trauma',"category"=>"ENT"],
            ['name'=>'Nasal bleeding',"category"=>"ENT"],
            ['name'=>'Cotton swab use',"category"=>"ENT"],
            ['name'=>'Ear trauma',"category"=>"ENT"],
            ['name'=>'Vertigo',"category"=>"ENT"],
            ['name'=>'Tinnitus',"category"=>"ENT"],
            ['name'=>'Discharge',"category"=>"ENT"],
            ['name'=>'Earache',"category"=>"ENT"],
            ['name'=>'Itching ears',"category"=>"ENT"],


            ['name'=>'Increased frequency of micturation',"category"=>"Genitourinary"],
            ['name'=>'Decreased frequency of micturation',"category"=>"Genitourinary"],
            ['name'=>'Dysuria',"category"=>"Genitourinary"],
            ['name'=>'Nocturia',"category"=>"Genitourinary"],
            ['name'=>'diplopia',"category"=>"Genitourinary"],
            ['name'=>'Polyuria',"category"=>"Genitourinary"],
            ['name'=>'Oliguria',"category"=>"Genitourinary"],
            ['name'=>'Haematuria',"category"=>"Genitourinary"],
            ['name'=>'Incontinence',"category"=>"Genitourinary"],
            ['name'=>'Urgency',"category"=>"Genitourinary"],
            ['name'=>'Impotence',"category"=>"Genitourinary"],
            ['name'=>'Scrotal swelling',"category"=>"Genitourinary"],
            ['name'=>'Genital ulcer',"category"=>"Genitourinary"],
            ['name'=>'Urethral discharge',"category"=>"Genitourinary"],
            ['name'=>'Whitish discharge',"category"=>"Genitourinary"],
            ['name'=>'Fish smell discharge',"category"=>"Genitourinary"],
            ['name'=>'Genital itching',"category"=>"Genitourinary"],
            ['name'=>'menarche at',"category"=>"Genitourinary"],
            ['name'=>'menarche at 12-16',"category"=>"Genitourinary"],
            ['name'=>'menarche <12',"category"=>"Genitourinary"],
            ['name'=>'menarche at 16-18',"category"=>"Genitourinary"],
            ['name'=>'menstral bleeding of 3-5 days',"category"=>"Genitourinary"],
            ['name'=>'menstral bleeding of 5-7',"category"=>"Genitourinary"],
            ['name'=>'menstral bleeding of >7days',"category"=>"Genitourinary"],
            ['name'=>'dysmenorrhoea',"category"=>"Genitourinary"],
            ['name'=>'dyspareunia menopause',"category"=>"Genitourinary"],
            ['name'=>'post-menopausal bleeding',"category"=>"Genitourinary"],
            ['name'=>'Facial symmetry ','category'=>'Inspection'],
            ['name'=>'Facial assymetry','category'=>'Inspection'],
            ['name'=>'Chest symetry ','category'=>'Inspection'],
            ['name'=>'Wasted','category'=>'Inspection'],
            ['name'=>'cachexic','category'=>'Inspection'],
            ['name'=>'Obese','category'=>'Inspection'],
            ['name'=>'Jaundiced','category'=>'Inspection'],
            ['name'=>'Pale','category'=>'Inspection'],
            ['name'=>'Not pale','category'=>'Inspection'],
            ['name'=>'dyspnea','category'=>'Inspection'],
            ['name'=>'Not dyspnic ','category'=>'Inspection'],
            ['name'=>'flat abdomen','category'=>'Inspection'],
            ['name'=>'distended abdomen','category'=>'Inspection'],
            ['name'=>'scaphoid abdomen','category'=>'Inspection'],
            ['name'=>'tachicardia','category'=>'Inspection'],
            ['name'=>'traditional therapeutic marks','category'=>'Inspection'],
            ['name'=>'scratching marks','category'=>'Inspection'],
            ['name'=>'unkept','category'=>'Inspection'],
            ['name'=>'smelling alcohol','category'=>'Inspection'],
            ['name'=>'drousy','category'=>'Inspection'],
            ['name'=>'chest wall indrawing','category'=>'Inspection'],
            ['name'=>'abnormal spine curvatures','category'=>'Inspection'],
            ['name'=>'flair nose','category'=>'Inspection'],
            ['name'=>'normal Hair distribution','category'=>'Inspection'],
            ['name'=>'sparcly hair distribution','category'=>'Inspection'],
            ['name'=>'protruded eyes','category'=>'Inspection'],
            ['name'=>'inverted umbilicus','category'=>'Inspection'],
            ['name'=>'deformed leg','category'=>'Inspection'],
            ['name'=>'deformed foot','category'=>'Inspection'],
            ['name'=>'bleeding wound','category'=>'Inspection'],
            ['name'=>'lethargic','category'=>'Inspection'],
            ['name'=>'sweating','category'=>'Inspection'],
            ['name'=>'anxious ','category'=>'Inspection'],
            ['name'=>'convulsing','category'=>'Inspection'],
            ['name'=>'neck swelling','category'=>'Inspection'],
            ['name'=>'everted umbilicus','category'=>'Inspection'],
            ['name'=>'finger clubbing','category'=>'Inspection'],
            ['name'=>'Macule','category'=>'Inspection'],
            ['name'=>'Papule','category'=>'Inspection'],
            ['name'=>'Echymosis','category'=>'Inspection'],
            ['name'=>'Erytherma','category'=>'Inspection'],
            ['name'=>'Skin lesions','category'=>'Inspection'],
            ['name'=>'Abnormal contour','category'=>'Inspection'],
            ['name'=>'precordial activity','category'=>'Inspection'],
            ['name'=>'engogade neck vessal','category'=>'Inspection'],
            ['name'=>'distended abdominal vessals','category'=>'Inspection'],
            ['name'=>'inguinal s ','category'=>'Inspection'],
            ['name'=>'dilated veins','category'=>'Inspection'],
            ['name'=>'crotal swelling','category'=>'Inspection'],
            ['name'=>'umbilical swelling','category'=>'Inspection'],
            ['name'=>'sister maries nodules','category'=>'Inspection'],
            ['name'=>'scrotal swelling','category'=>'Inspection'],
            ['name'=>'Ear discharge','category'=>'Inspection'],
            ['name'=>'Nose discharge','category'=>'Inspection'],
            ['name'=>'Bruises','category'=>'Inspection'],
            ['name'=>'Oral thrush','category'=>'Inspection'],
            ['name'=>'Hyperemic oral mucosa','category'=>'Inspection'],
            ['name'=>'Hyperemic anal mucosal','category'=>'Inspection'],
            ['name'=>'Hyperemic vagina','category'=>'Inspection'],
            ['name'=>'Peri anal tags','category'=>'Inspection'],
            ['name'=>'Anal warts','category'=>'Inspection'],
            ['name'=>'Fungating mass','category'=>'Inspection'],
            ['name'=>'Angular stomatitis ','category'=>'Inspection'],
            ['name'=>'muscle atrophy','category'=>'Inspection'],
            ['name'=>'fasciculations','category'=>'Inspection'],
            ['name'=>'Laceration','category'=>'Inspection'],
            ['name'=>'Abbration','category'=>'Inspection'],
            ['name'=>'Retracted niple','category'=>'Inspection'],
            ['name'=>'Niple discharge','category'=>'Inspection'],
            ['name'=>'Peudeorange breast','category'=>'Inspection'],
            ['name'=>'Enlarged breast','category'=>'Inspection'],
            ['name'=>'Breast ulcer','category'=>'Inspection'],
            ['name'=>'Anal mass','category'=>'Inspection'],
            ['name'=>'Uterine prolapse','category'=>'Inspection'],
            ['name'=>'Anal prolapse','category'=>'Inspection'],
            ['name'=>'Vescial vaginal fistula','category'=>'Inspection'],
            ['name'=>'Liking perinium','category'=>'Inspection'],
            ['name'=>'Proptosis','category'=>'Inspection'],
            ['name'=>'ptosis','category'=>'Inspection'],
            ['name'=>'Scars','category'=>'Inspection'],
            ['name'=>'Striae','category'=>'Inspection'],
            ['name'=>'Visible masses','category'=>'Inspection'],
            ['name'=>'Discoloration','category'=>'Inspection'],
            ['name'=>'Swelling','category'=>'Inspection'],
            ['name'=>'Tremor','category'=>'Inspection'],
            ['name'=>'cyanosis, ','category'=>'Inspection'],
            ['name'=>'clubbing','category'=>'Inspection'],
            ['name'=>'Edema','category'=>'Inspection'],
            ['name'=>'Pus discharging sinus','category'=>'Inspection'],
            ['name'=>'Bowed knee','category'=>'Inspection'],
            ['name'=>'Bowed elbow','category'=>'Inspection'],
            ['name'=>'Straight back','category'=>'Inspection'],
            ['name'=>'Deformed back','category'=>'Inspection'],
            ['name'=>'spider nevi,','category'=>'Inspection'],
            ['name'=>'petechiae','category'=>'Inspection'],
            ['name'=>'normal posture','category'=>'Inspection'],
            ['name'=>'ulcer','category'=>'Inspection'],
            ['name'=>'abnormal posture','category'=>'Inspection'],
            ['name'=>'wide gait','category'=>'Inspection'],
            ['name'=>'trendelberg gait','category'=>'Inspection'],
            ['name'=>'ataxic gait','category'=>'Inspection'],
            ['name'=>'decorticate rigidity','category'=>'Inspection'],
            ['name'=>'decerebrate rigidity','category'=>'Inspection'],
            ['name'=>'peri-orbital echymosis','category'=>'Inspection'],
            ['name'=>'Sclera blood','category'=>'Inspection'],
            ['name'=>'aniosocoria','category'=>'Inspection'],
            ['name'=>'Normal Texture','category'=>'Palpation'],
            ['name'=>'Abnormal texture','category'=>'Palpation'],
            ['name'=>'Weat hand','category'=>'Palpation'],
            ['name'=>'Dry hand','category'=>'Palpation'],
            ['name'=>'Dry skin','category'=>'Palpation'],
            ['name'=>'Normal skin turgor','category'=>'Palpation'],
            ['name'=>'Enlarged lymphnode','category'=>'Palpation'],
            ['name'=>'tenderness,','category'=>'Palpation'],
            ['name'=>'non tender','category'=>'Palpation'],
            ['name'=>'collapsing pulse','category'=>'Palpation'],
            ['name'=>'synchronous pulse','category'=>'Palpation'],
            ['name'=>'venous pulsation','category'=>'Palpation'],
            ['name'=>'radial pulse normal','category'=>'Palpation'],
            ['name'=>'radiating pulse','category'=>'Palpation'],
            ['name'=>'wide pulse','category'=>'Palpation'],
            ['name'=>'tender renal angles','category'=>'Palpation'],
            ['name'=>'loss of anal sphincter tone','category'=>'Palpation'],
            ['name'=>'normal anal sphincter tone','category'=>'Palpation'],
            ['name'=>'hemorrhoids','category'=>'Palpation'],
            ['name'=>'anal fissures','category'=>'Palpation'],
            ['name'=>'anal polyp ','category'=>'Palpation'],
            ['name'=>'anal mass','category'=>'Palpation'],
            ['name'=>'warm joint','category'=>'Palpation'],
            ['name'=>'positive patella balotment test','category'=>'Palpation'],
            ['name'=>'positive straight leg raising test','category'=>'Palpation'],
            ['name'=>'negative straight leg raising test','category'=>'Palpation'],
            ['name'=>'positive macmurray test','category'=>'Palpation'],
            ['name'=>'negative macmurray test','category'=>'Palpation'],
            ['name'=>'obturator test positive','category'=>'Palpation'],
            ['name'=>'obturator test negative','category'=>'Palpation'],
            ['name'=>'positive psoas test','category'=>'Palpation'],
            ['name'=>'negative psoas test','category'=>'Palpation'],
            ['name'=>'spalding test positive','category'=>'Palpation'],
            ['name'=>'spalding test negative','category'=>'Palpation'],
            ['name'=>'positive jackson test','category'=>'Palpation'],
            ['name'=>'negative jackson test','category'=>'Palpation'],
            ['name'=>'tenderness on the adnexa','category'=>'Palpation'],
            ['name'=>'palpable prostate','category'=>'Palpation'],
            ['name'=>'irregular prostate','category'=>'Palpation'],
            ['name'=>'firm prostate','category'=>'Palpation'],
            ['name'=>'enlarged, hard, nodulated prostate','category'=>'Palpation'],
            ['name'=>'brast lump','category'=>'Palpation'],
            ['name'=>'positive tactile vocal fremitus','category'=>'Palpation'],
            ['name'=>'pericardial heave','category'=>'Palpation'],
            ['name'=>'apical impulse','category'=>'Palpation'],
            ['name'=>'cant go above the mass','category'=>'Palpation'],
            ['name'=>'positive translumination test','category'=>'Palpation'],
            ['name'=>'positive cough impulse','category'=>'Palpation'],
            ['name'=>'oblitareted inguinal canal','category'=>'Palpation'],
            ['name'=>'can go above the swelling ','category'=>'Palpation'],
            ['name'=>'size','category'=>'Palpation'],
            ['name'=>'loss of sensation','category'=>'Palpation'],
            ['name'=>'sustained clonus','category'=>'Palpation'],
            ['name'=>'equivocal planter reflex','category'=>'Palpation'],
            ['name'=>'extended planter reflex','category'=>'Palpation'],
            ['name'=>'flexed planter reflex ','category'=>'Palpation'],
            ['name'=>'loss of muscle power','category'=>'Palpation'],
            ['name'=>'muscle power grade 1','category'=>'Palpation'],
            ['name'=>'muscle power grade 2','category'=>'Palpation'],
            ['name'=>'muscle power grade 3','category'=>'Palpation'],
            ['name'=>'muscle power grade 4','category'=>'Palpation'],
            ['name'=>'muscle power grade 5','category'=>'Palpation'],
            ['name'=>'muscle power grade 0','category'=>'Palpation'],
            ['name'=>'normal range of joint movement','category'=>'Palpation'],
            ['name'=>'abnormal range of joint ','category'=>'Palpation'],
            ['name'=>'decreased muscle tone','category'=>'Palpation'],
            ['name'=>'normal muscle tone','category'=>'Palpation'],
            ['name'=>'movement','category'=>'Palpation'],
            ['name'=>'tachycardia','category'=>'Palpation'],
            ['name'=>'palpable liver ','category'=>'Palpation'],
            ['name'=>'Palplabe spleen','category'=>'Palpation'],
            ['name'=>'Palpable kidney,','category'=>'Palpation'],
            ['name'=>'Palpable gall bladder','category'=>'Palpation'],
            ['name'=>'Palpable urinary bladder','category'=>'Palpation'],
            ['name'=>'Palpable uterus','category'=>'Palpation'],
            ['name'=>'liver span','category'=>'Palpation'],
            ['name'=>'tenderness','category'=>'Palpation'],
            ['name'=>'muscle rigidity','category'=>'Palpation'],
            ['name'=>'rebound tenerness','category'=>'Palpation'],
            ['name'=>'intra abdominal masses','category'=>'Palpation'],
            ['name'=>'pulsations','category'=>'Palpation'],
            ['name'=>'resonance chest','category'=>'Percussion'],
            ['name'=>'hyperesonance chest','category'=>'Percussion'],
            ['name'=>'dull abdomen','category'=>'Percussion'],
            ['name'=>'tympanic abdomen','category'=>'Percussion'],
            ['name'=>'tympanic abdomen','category'=>'Percussion'],
            ['name'=>'dull','category'=>'Percussion'],
            ['name'=>'positive fluid thrill','category'=>'Percussion'],
            ['name'=>'positive succussion splash test','category'=>'Percussion'],
            ['name'=>'increased pattella tendon reflex','category'=>'Percussion'],
            ['name'=>'increased bicepsy tendon reflex','category'=>'Percussion'],
            ['name'=>'absent deep tendon reflex','category'=>'Percussion'],
            ['name'=>'bruits','category'=>'Auscultation'],
            ['name'=>'fluid thrill','category'=>'Auscultation'],
            ['name'=>'positive succusion splash test','category'=>'Auscultation'],
            ['name'=>'pericardial murmur','category'=>'Auscultation'],
            ['name'=>'apical murmur','category'=>'Auscultation'],
            ['name'=>'gallop rythim','category'=>'Auscultation'],
            ['name'=>'metallic bowel sound','category'=>'Auscultation'],
            ['name'=>'vesicular breathing sound','category'=>'Auscultation'],
            ['name'=>'bronchial breathing sound','category'=>'Auscultation'],
            ['name'=>'crackles sounds ','category'=>'Auscultation'],
            ['name'=>'wheezes sounds','category'=>'Auscultation'],
            ['name'=>'rhonchi sounds ','category'=>'Auscultation'],
            ['name'=>'pleural rub sound','category'=>'Auscultation'],
            ['name'=>'decreased bowel sound','category'=>'Auscultation'],
            ['name'=>'high pitched bowel sound','category'=>'Auscultation'],
            ['name'=>'bowel sound in the scrotum','category'=>'Auscultation'],
            ['name'=>'name','category'=>'category'],
            ['name'=>'Facial symmetry ','category'=>'Inspection'],
            ['name'=>'Facial assymetry','category'=>'Inspection'],
            ['name'=>'Chest symetry ','category'=>'Inspection'],
            ['name'=>'Wasted','category'=>'Inspection'],
            ['name'=>'cachexic','category'=>'Inspection'],
            ['name'=>'Obese','category'=>'Inspection'],
            ['name'=>'Jaundiced','category'=>'Inspection'],
            ['name'=>'Pale','category'=>'Inspection'],
            ['name'=>'Not pale','category'=>'Inspection'],
            ['name'=>'dyspnea','category'=>'Inspection'],
            ['name'=>'Not dyspnic ','category'=>'Inspection'],
            ['name'=>'flat abdomen','category'=>'Inspection'],
            ['name'=>'distended abdomen','category'=>'Inspection'],
            ['name'=>'scaphoid abdomen','category'=>'Inspection'],
            ['name'=>'tachicardia','category'=>'Inspection'],
            ['name'=>'traditional therapeutic marks','category'=>'Inspection'],
            ['name'=>'scratching marks','category'=>'Inspection'],
            ['name'=>'unkept','category'=>'Inspection'],
            ['name'=>'smelling alcohol','category'=>'Inspection'],
            ['name'=>'drousy','category'=>'Inspection'],
            ['name'=>'chest wall indrawing','category'=>'Inspection'],
            ['name'=>'abnormal spine curvatures','category'=>'Inspection'],
            ['name'=>'flair nose','category'=>'Inspection'],
            ['name'=>'normal Hair distribution','category'=>'Inspection'],
            ['name'=>'sparcly hair distribution','category'=>'Inspection'],
            ['name'=>'protruded eyes','category'=>'Inspection'],
            ['name'=>'inverted umbilicus','category'=>'Inspection'],
            ['name'=>'deformed leg','category'=>'Inspection'],
            ['name'=>'deformed foot','category'=>'Inspection'],
            ['name'=>'bleeding wound','category'=>'Inspection'],
            ['name'=>'lethargic','category'=>'Inspection'],
            ['name'=>'sweating','category'=>'Inspection'],
            ['name'=>'anxious ','category'=>'Inspection'],
            ['name'=>'convulsing','category'=>'Inspection'],
            ['name'=>'neck swelling','category'=>'Inspection'],
            ['name'=>'everted umbilicus','category'=>'Inspection'],
            ['name'=>'finger clubbing','category'=>'Inspection'],
            ['name'=>'Macule','category'=>'Inspection'],
            ['name'=>'Papule','category'=>'Inspection'],
            ['name'=>'Echymosis','category'=>'Inspection'],
            ['name'=>'Erytherma','category'=>'Inspection'],
            ['name'=>'Skin lesions','category'=>'Inspection'],
            ['name'=>'Abnormal contour','category'=>'Inspection'],
            ['name'=>'precordial activity','category'=>'Inspection'],
            ['name'=>'engogade neck vessal','category'=>'Inspection'],
            ['name'=>'distended abdominal vessals','category'=>'Inspection'],
            ['name'=>'inguinal s ','category'=>'Inspection'],
            ['name'=>'dilated veins','category'=>'Inspection'],
            ['name'=>'crotal swelling','category'=>'Inspection'],
            ['name'=>'umbilical swelling','category'=>'Inspection'],
            ['name'=>'sister maries nodules','category'=>'Inspection'],
            ['name'=>'scrotal swelling','category'=>'Inspection'],
            ['name'=>'Ear discharge','category'=>'Inspection'],
            ['name'=>'Nose discharge','category'=>'Inspection'],
            ['name'=>'Bruises','category'=>'Inspection'],
            ['name'=>'Oral thrush','category'=>'Inspection'],
            ['name'=>'Hyperemic oral mucosa','category'=>'Inspection'],
            ['name'=>'Hyperemic anal mucosal','category'=>'Inspection'],
            ['name'=>'Hyperemic vagina','category'=>'Inspection'],
            ['name'=>'Peri anal tags','category'=>'Inspection'],
            ['name'=>'Anal warts','category'=>'Inspection'],
            ['name'=>'Fungating mass','category'=>'Inspection'],
            ['name'=>'Angular stomatitis ','category'=>'Inspection'],
            ['name'=>'muscle atrophy','category'=>'Inspection'],
            ['name'=>'fasciculations','category'=>'Inspection'],
            ['name'=>'Laceration','category'=>'Inspection'],
            ['name'=>'Abbration','category'=>'Inspection'],
            ['name'=>'Retracted niple','category'=>'Inspection'],
            ['name'=>'Niple discharge','category'=>'Inspection'],
            ['name'=>'Peudeorange breast','category'=>'Inspection'],
            ['name'=>'Enlarged breast','category'=>'Inspection'],
            ['name'=>'Breast ulcer','category'=>'Inspection'],
            ['name'=>'Anal mass','category'=>'Inspection'],
            ['name'=>'Uterine prolapse','category'=>'Inspection'],
            ['name'=>'Anal prolapse','category'=>'Inspection'],
            ['name'=>'Vescial vaginal fistula','category'=>'Inspection'],
            ['name'=>'Liking perinium','category'=>'Inspection'],
            ['name'=>'Proptosis','category'=>'Inspection'],
            ['name'=>'ptosis','category'=>'Inspection'],
            ['name'=>'Scars','category'=>'Inspection'],
            ['name'=>'Striae','category'=>'Inspection'],
            ['name'=>'Visible masses','category'=>'Inspection'],
            ['name'=>'Discoloration','category'=>'Inspection'],
            ['name'=>'Swelling','category'=>'Inspection'],
            ['name'=>'Tremor','category'=>'Inspection'],
            ['name'=>'cyanosis, ','category'=>'Inspection'],
            ['name'=>'clubbing','category'=>'Inspection'],
            ['name'=>'Edema','category'=>'Inspection'],
            ['name'=>'Pus discharging sinus','category'=>'Inspection'],
            ['name'=>'Bowed knee','category'=>'Inspection'],
            ['name'=>'Bowed elbow','category'=>'Inspection'],
            ['name'=>'Straight back','category'=>'Inspection'],
            ['name'=>'Deformed back','category'=>'Inspection'],
            ['name'=>'spider nevi,','category'=>'Inspection'],
            ['name'=>'petechiae','category'=>'Inspection'],
            ['name'=>'normal posture','category'=>'Inspection'],
            ['name'=>'ulcer','category'=>'Inspection'],
            ['name'=>'abnormal posture','category'=>'Inspection'],
            ['name'=>'wide gait','category'=>'Inspection'],
            ['name'=>'trendelberg gait','category'=>'Inspection'],
            ['name'=>'ataxic gait','category'=>'Inspection'],
            ['name'=>'decorticate rigidity','category'=>'Inspection'],
            ['name'=>'decerebrate rigidity','category'=>'Inspection'],
            ['name'=>'peri-orbital echymosis','category'=>'Inspection'],
            ['name'=>'Sclera blood','category'=>'Inspection'],
            ['name'=>'aniosocoria','category'=>'Inspection'],
            ['name'=>'Normal Texture','category'=>'Palpation'],
            ['name'=>'Abnormal texture','category'=>'Palpation'],
            ['name'=>'Weat hand','category'=>'Palpation'],
            ['name'=>'Dry hand','category'=>'Palpation'],
            ['name'=>'Dry skin','category'=>'Palpation'],
            ['name'=>'Normal skin turgor','category'=>'Palpation'],
            ['name'=>'Enlarged lymphnode','category'=>'Palpation'],
            ['name'=>'tenderness,','category'=>'Palpation'],
            ['name'=>'non tender','category'=>'Palpation'],
            ['name'=>'collapsing pulse','category'=>'Palpation'],
            ['name'=>'synchronous pulse','category'=>'Palpation'],
            ['name'=>'venous pulsation','category'=>'Palpation'],
            ['name'=>'radial pulse normal','category'=>'Palpation'],
            ['name'=>'radiating pulse','category'=>'Palpation'],
            ['name'=>'wide pulse','category'=>'Palpation'],
            ['name'=>'tender renal angles','category'=>'Palpation'],
            ['name'=>'loss of anal sphincter tone','category'=>'Palpation'],
            ['name'=>'normal anal sphincter tone','category'=>'Palpation'],
            ['name'=>'hemorrhoids','category'=>'Palpation'],
            ['name'=>'anal fissures','category'=>'Palpation'],
            ['name'=>'anal polyp ','category'=>'Palpation'],
            ['name'=>'anal mass','category'=>'Palpation'],
            ['name'=>'warm joint','category'=>'Palpation'],
            ['name'=>'positive patella balotment test','category'=>'Palpation'],
            ['name'=>'positive straight leg raising test','category'=>'Palpation'],
            ['name'=>'negative straight leg raising test','category'=>'Palpation'],
            ['name'=>'positive macmurray test','category'=>'Palpation'],
            ['name'=>'negative macmurray test','category'=>'Palpation'],
            ['name'=>'obturator test positive','category'=>'Palpation'],
            ['name'=>'obturator test negative','category'=>'Palpation'],
            ['name'=>'positive psoas test','category'=>'Palpation'],
            ['name'=>'negative psoas test','category'=>'Palpation'],
            ['name'=>'spalding test positive','category'=>'Palpation'],
            ['name'=>'spalding test negative','category'=>'Palpation'],
            ['name'=>'positive jackson test','category'=>'Palpation'],
            ['name'=>'negative jackson test','category'=>'Palpation'],
            ['name'=>'tenderness on the adnexa','category'=>'Palpation'],
            ['name'=>'palpable prostate','category'=>'Palpation'],
            ['name'=>'irregular prostate','category'=>'Palpation'],
            ['name'=>'firm prostate','category'=>'Palpation'],
            ['name'=>'enlarged, hard, nodulated prostate','category'=>'Palpation'],
            ['name'=>'brast lump','category'=>'Palpation'],
            ['name'=>'positive tactile vocal fremitus','category'=>'Palpation'],
            ['name'=>'pericardial heave','category'=>'Palpation'],
            ['name'=>'apical impulse','category'=>'Palpation'],
            ['name'=>'cant go above the mass','category'=>'Palpation'],
            ['name'=>'positive translumination test','category'=>'Palpation'],
            ['name'=>'positive cough impulse','category'=>'Palpation'],
            ['name'=>'oblitareted inguinal canal','category'=>'Palpation'],
            ['name'=>'can go above the swelling ','category'=>'Palpation'],
            ['name'=>'size','category'=>'Palpation'],
            ['name'=>'loss of sensation','category'=>'Palpation'],
            ['name'=>'sustained clonus','category'=>'Palpation'],
            ['name'=>'equivocal planter reflex','category'=>'Palpation'],
            ['name'=>'extended planter reflex','category'=>'Palpation'],
            ['name'=>'flexed planter reflex ','category'=>'Palpation'],
            ['name'=>'loss of muscle power','category'=>'Palpation'],
            ['name'=>'muscle power grade 1','category'=>'Palpation'],
            ['name'=>'muscle power grade 2','category'=>'Palpation'],
            ['name'=>'muscle power grade 3','category'=>'Palpation'],
            ['name'=>'muscle power grade 4','category'=>'Palpation'],
            ['name'=>'muscle power grade 5','category'=>'Palpation'],
            ['name'=>'muscle power grade 0','category'=>'Palpation'],
            ['name'=>'normal range of joint movement','category'=>'Palpation'],
            ['name'=>'abnormal range of joint ','category'=>'Palpation'],
            ['name'=>'decreased muscle tone','category'=>'Palpation'],
            ['name'=>'normal muscle tone','category'=>'Palpation'],
            ['name'=>'movement','category'=>'Palpation'],
            ['name'=>'tachycardia','category'=>'Palpation'],
            ['name'=>'palpable liver ','category'=>'Palpation'],
            ['name'=>'Palplabe spleen','category'=>'Palpation'],
            ['name'=>'Palpable kidney,','category'=>'Palpation'],
            ['name'=>'Palpable gall bladder','category'=>'Palpation'],
            ['name'=>'Palpable urinary bladder','category'=>'Palpation'],
            ['name'=>'Palpable uterus','category'=>'Palpation'],
            ['name'=>'liver span','category'=>'Palpation'],
            ['name'=>'tenderness','category'=>'Palpation'],
            ['name'=>'muscle rigidity','category'=>'Palpation'],
            ['name'=>'rebound tenerness','category'=>'Palpation'],
            ['name'=>'intra abdominal masses','category'=>'Palpation'],
            ['name'=>'pulsations','category'=>'Palpation'],
            ['name'=>'resonance chest','category'=>'Percussion'],
            ['name'=>'hyperesonance chest','category'=>'Percussion'],
            ['name'=>'dull abdomen','category'=>'Percussion'],
            ['name'=>'tympanic abdomen','category'=>'Percussion'],
            ['name'=>'tympanic abdomen','category'=>'Percussion'],
            ['name'=>'dull','category'=>'Percussion'],
            ['name'=>'positive fluid thrill','category'=>'Percussion'],
            ['name'=>'positive succussion splash test','category'=>'Percussion'],
            ['name'=>'increased pattella tendon reflex','category'=>'Percussion'],
            ['name'=>'increased bicepsy tendon reflex','category'=>'Percussion'],
            ['name'=>'absent deep tendon reflex','category'=>'Percussion'],
            ['name'=>'bruits','category'=>'Auscultation'],
            ['name'=>'fluid thrill','category'=>'Auscultation'],
            ['name'=>'positive succusion splash test','category'=>'Auscultation'],
            ['name'=>'pericardial murmur','category'=>'Auscultation'],
            ['name'=>'apical murmur','category'=>'Auscultation'],
            ['name'=>'gallop rythim','category'=>'Auscultation'],
            ['name'=>'metallic bowel sound','category'=>'Auscultation'],
            ['name'=>'vesicular breathing sound','category'=>'Auscultation'],
            ['name'=>'bronchial breathing sound','category'=>'Auscultation'],
            ['name'=>'crackles sounds ','category'=>'Auscultation'],
            ['name'=>'wheezes sounds','category'=>'Auscultation'],
            ['name'=>'rhonchi sounds ','category'=>'Auscultation'],
            ['name'=>'pleural rub sound','category'=>'Auscultation'],
            ['name'=>'decreased bowel sound','category'=>'Auscultation'],
            ['name'=>'high pitched bowel sound','category'=>'Auscultation'],
            ['name'=>'bowel sound in the scrotum','category'=>'Auscultation'],
            ['name'=>'Milk Protein','category'=>'Allergy'],
            ['name'=>'Sulphur Drugs','category'=>'Allergy'],
            ['name'=>'Dust','category'=>'Allergy'],
            ['name'=>'Pollen','category'=>'Allergy'],
            ['name'=>'Pollen','category'=>'Allergy'],
            ['name'=>'Diabetic','category'=>'Past Medical History'],
            ['name'=>'Cancer','category'=>'Past Medical History'],
            ['name'=>'Rubella','category'=>'Immunisation'],
            ['name'=>'Polio','category'=>'Immunisation'],
            ['name'=>'Tetanas','category'=>'Immunisation'],
            ['name'=>'Toxoplasmosis','category'=>'Immunisation'],
            ['name'=>'Hepatatis B','category'=>'Immunisation'],
            ['name'=>'Medical Ward','category'=>'Admission History'],
            ['name'=>'Surgical Ward','category'=>'Admission History'],
            ['name'=>'Psychiatric Ward','category'=>'Admission History'],
            ['name'=>'TB Ward','category'=>'Admission History'],
        );

        $sub_payment_categories= array(
             ['id'=>1,'sub_category_name' => 'REFERRAL','pay_cat_id'=>1],
             ['id'=>2,'sub_category_name' => 'SELF REFERRAL','pay_cat_id'=>1],
            ['id'=>3,'sub_category_name' => 'TEMPORARY EXEMPTION','pay_cat_id'=>3],
            ['id'=>4,'sub_category_name' => 'NHIF','pay_cat_id'=>2],
            ['id'=>5,'sub_category_name' => 'UNDER FIVE','pay_cat_id'=>3],
            ['id'=>6,'sub_category_name' => 'ABOVE 60','pay_cat_id'=>3],
            ['id'=>7,'sub_category_name' => 'GBV/VAC','pay_cat_id'=>3],
            ['id'=>8,'sub_category_name' => 'WAFUNGWA','pay_cat_id'=>3],
            ['id'=>9,'sub_category_name' => 'CHRONIC DISEASE','pay_cat_id'=>3],
            ['id'=>10,'sub_category_name' => 'HOSPITAL SHOP','pay_cat_id'=>1],
            ['id'=>50,'sub_category_name' => 'CHF','pay_cat_id'=>2],
        );


        $item_category= array(
            ['id'=>1,'item_category_name' => 'WARD'],
            ['id'=>2,'item_category_name' => 'PROCEDURE'],
            ['id'=>3,'item_category_name' => 'Medication'],
            ['id'=>4,'item_category_name' => 'SERVICE'],
            ['id'=>5,'item_category_name' => 'TEST'],
            ['id'=>6,'item_category_name' => 'MORTUARY'],
            ['id'=>7,'item_category_name' => 'SOLUTION'],
            ['id'=>8,'item_category_name' => 'SPECIALISED PROCEDURES'],
            ['id'=>9,'item_category_name' => 'MAJOR PROCEDURES'],
            ['id'=>10,'item_category_name' => 'MINOR PROCEDURES'],
            ['id'=>11,'item_category_name' => 'Medical Supplies'],

        );



        $tbl_tb_treatment_types= array(

            ['type' => '2RHZE/4RH,2RHZ/4RH'],
            ['type' => '2SRHZE/1RHZE/5RHE'],
            ['type' => '2HRZE/10RH'],
            ['type' => '3RHZE/5RHE'],


        );





        $tbl_transaction_types= array(

            ['transaction_type' => 'Normal','adjustment' => 'plus'],
            ['transaction_type' => 'Grant','adjustment' => 'plus'],
            ['transaction_type' => 'Clinical Return','adjustment' => 'plus'],
            ['transaction_type' => 'Aid','adjustment' => 'plus'],
            ['transaction_type' => 'Stolen','adjustment' => 'minus'],
            ['transaction_type' => 'Expired','adjustment' => 'minus'],
            ['transaction_type' => 'Broken','adjustment' => 'minus'],

        );



        $vitals = array(
            ['vital_name' => 'Body Weight','maximum'=> 0,'minimum'=> 0,'si_unit' => 'Kg'],
            ['vital_name' => 'Height or Length','maximum'=> 0,'minimum'=> 0,'si_unit' => 'cm'],
            ['vital_name' => 'Body Temperature','maximum'=> 38.1,'minimum'=> 36.6,'si_unit' => '℃'],
            ['vital_name' => 'Systolic Pressure','maximum'=> 180,'minimum'=> 75,'si_unit' => 'mmHg'],
            ['vital_name' => 'Diastolic Pressure','maximum'=> 50,'minimum'=> 120,'si_unit' => 'mmHg'],
            ['vital_name' => 'Respiratory Rate','maximum'=> 16,'minimum'=> 46,'si_unit' => 'bpm'],
            ['vital_name' => 'Pulse Rate','maximum'=> 50,'minimum'=> 140,'si_unit' => 'bpm'],
            ['vital_name' => 'Oxygen Saturation','maximum'=> 80,'minimum'=> 15,'si_unit' => '%'],
        );



		$tbl_vaccination_registers= array(

            ['id'=>1,'vaccination_name' => 'PEPOPUNDA','vaccination_type' => 'child','dose'=>null],
            ['id'=>2,'vaccination_name' => 'BCG','vaccination_type' => 'child','dose'=>null],
            ['id'=>3,'vaccination_name' => 'BCG','vaccination_type' => 'child','dose'=>null],
            ['id'=>4,'vaccination_name' => 'POLIO DOSE 0','vaccination_type' => 'child','dose'=>0],
            ['id'=>5,'vaccination_name' => 'POLIO DOSE 1','vaccination_type' => 'child','dose'=>1],
            ['id'=>6,'vaccination_name' => 'POLIO DOSE 2','vaccination_type' => 'child','dose'=>2],
            ['id'=>7,'vaccination_name' => 'POLIO DOSE 2','vaccination_type' => 'child','dose'=>3],
            ['id'=>8,'vaccination_name' => 'Rota','vaccination_type' => 'child','dose'=>null],
            ['id'=>9,'vaccination_name' => 'Rota  DOSE 1','vaccination_type' => 'child','dose'=>1],
            ['id'=>10,'vaccination_name' => 'Rota  DOSE 2','vaccination_type' => 'child','dose'=>2],
            ['id'=>11,'vaccination_name' => 'PENTA  DOSE 1','vaccination_type' => 'child','dose'=>1],
            ['id'=>12,'vaccination_name' => 'PENTA  DOSE 2','vaccination_type' => 'child','dose'=>2],
            ['id'=>13,'vaccination_name' => 'PENTA  DOSE 3','vaccination_type' => 'child','dose'=>3],
            ['id'=>14,'vaccination_name' => 'Pneumococcal (PCV13)  dose 1','vaccination_type' => 'child','dose'=>1],
            ['id'=>15,'vaccination_name' => 'Pneumococcal (PCV13)  dose 2','vaccination_type' => 'child','dose'=>2],
            ['id'=>16,'vaccination_name' => 'Pneumococcal (PCV13)  dose 3','vaccination_type' => 'child','dose'=>3],
            ['id'=>17,'vaccination_name' => 'Surua/ Rubela dose 1','vaccination_type' => 'child','dose'=>1],
            ['id'=>18,'vaccination_name' => 'Surua/ Rubela dose 2','vaccination_type' => 'child','dose'=>2],
            ['id'=>19,'vaccination_name' => 'Surua/ Rubela dose 3','vaccination_type' => 'child','dose'=>3],

        );

		$occupations = array(
			   ['occupation_name'=>'ACCOUNTANT'],
				['occupation_name'=>'BUSINESS'],
				['occupation_name'=>'CAPENTER'],
				['occupation_name'=>'CHANCELLOR'],
				['occupation_name'=>'CHILD'],
				['occupation_name'=>'DANCER'],
				['occupation_name'=>'DEVELOPER'],
				['occupation_name'=>'DJ'],
				['occupation_name'=>'DOCTOR'],
				['occupation_name'=>'DRIVER'],
				['occupation_name'=>'ELECTRICIAN'],
				['occupation_name'=>'ENGINEER'],
				['occupation_name'=>'ESTATE OFFICER'],
				['occupation_name'=>'EXECUTIVE DIRECTOR'],
				['occupation_name'=>'HUMAN RESOURCE OFFICER'],
				['occupation_name'=>'FARMER'],
				['occupation_name'=>'LAB TECHNICIAN'],
				['occupation_name'=>'LAWYER'],
				['occupation_name'=>'LECTURER'],
				['occupation_name'=>'MAMA WA NYUMBANI'],
				['occupation_name'=>'NONE'],
				['occupation_name'=>'NURSE'],
				['occupation_name'=>'OTHERS'],
				['occupation_name'=>'PEASANT'],
				['occupation_name'=>'PHARMACIST'],
				['occupation_name'=>'PLUMBER'],
				['occupation_name'=>'POLICE'],
				['occupation_name'=>'PROCUREMENT OFFICER'],
				['occupation_name'=>'PUPIL/STUDENT'],
				['occupation_name'=>'RADIOLOGIST'],
				['occupation_name'=>'SECRETARY'],
				['occupation_name'=>'SOFTWARE MAKER'],
				['occupation_name'=>'SOLDIER'],
				['occupation_name'=>'STUDENT'],
				['occupation_name'=>'SYSTEMS ADMINISTRATOR'],
				['occupation_name'=>'TEACHER'],
				['occupation_name'=>'VICE CHANCELLOR'],

		);

		$tribes= array(
			array('id' => '1','tribe_name' => 'AKIE','created_at' => '2017-11-30 09:24:25','updated_at' => '2017-11-30 09:24:25'),
			array('id' => '2','tribe_name' => 'AKIEK','created_at' => '2017-11-30 09:24:25','updated_at' => '2017-11-30 09:24:25'),
			array('id' => '3','tribe_name' => 'ALAGWA','created_at' => '2017-11-30 09:24:25','updated_at' => '2017-11-30 09:24:25'),
			array('id' => '4','tribe_name' => 'ARUSHA','created_at' => '2017-11-30 09:24:25','updated_at' => '2017-11-30 09:24:25'),
			array('id' => '5','tribe_name' => 'ASSA','created_at' => '2017-11-30 09:24:25','updated_at' => '2017-11-30 09:24:25'),
			array('id' => '6','tribe_name' => 'BARABAIG','created_at' => '2017-11-30 09:24:26','updated_at' => '2017-11-30 09:24:26'),
			array('id' => '7','tribe_name' => 'BEMBE','created_at' => '2017-11-30 09:24:26','updated_at' => '2017-11-30 09:24:26'),
			array('id' => '8','tribe_name' => 'BENA','created_at' => '2017-11-30 09:24:26','updated_at' => '2017-11-30 09:24:26'),
			array('id' => '9','tribe_name' => 'BENDE','created_at' => '2017-11-30 09:24:26','updated_at' => '2017-11-30 09:24:26'),
			array('id' => '10','tribe_name' => 'BONDEI','created_at' => '2017-11-30 09:24:26','updated_at' => '2017-11-30 09:24:26'),
			array('id' => '11','tribe_name' => 'BUNGU','created_at' => '2017-11-30 09:24:26','updated_at' => '2017-11-30 09:24:26'),
			array('id' => '12','tribe_name' => 'BURUNGE','created_at' => '2017-11-30 09:24:26','updated_at' => '2017-11-30 09:24:26'),
			array('id' => '13','tribe_name' => 'CHAGGA','created_at' => '2017-11-30 09:24:26','updated_at' => '2017-11-30 09:24:26'),
			array('id' => '14','tribe_name' => 'DATOGA','created_at' => '2017-11-30 09:24:26','updated_at' => '2017-11-30 09:24:26'),
			array('id' => '15','tribe_name' => 'DHAISO','created_at' => '2017-11-30 09:24:26','updated_at' => '2017-11-30 09:24:26'),
			array('id' => '16','tribe_name' => 'DIGO','created_at' => '2017-11-30 09:24:26','updated_at' => '2017-11-30 09:24:26'),
			array('id' => '17','tribe_name' => 'DOE','created_at' => '2017-11-30 09:24:26','updated_at' => '2017-11-30 09:24:26'),
			array('id' => '18','tribe_name' => 'FIPA','created_at' => '2017-11-30 09:24:26','updated_at' => '2017-11-30 09:24:26'),
			array('id' => '19','tribe_name' => 'GOGO','created_at' => '2017-11-30 09:24:26','updated_at' => '2017-11-30 09:24:26'),
			array('id' => '20','tribe_name' => 'GOMA','created_at' => '2017-11-30 09:24:26','updated_at' => '2017-11-30 09:24:26'),
			array('id' => '21','tribe_name' => 'GOROWA','created_at' => '2017-11-30 09:24:27','updated_at' => '2017-11-30 09:24:27'),
			array('id' => '22','tribe_name' => 'GWENO','created_at' => '2017-11-30 09:24:27','updated_at' => '2017-11-30 09:24:27'),
			array('id' => '23','tribe_name' => 'WAHA','created_at' => '2017-11-30 09:24:27','updated_at' => '2017-11-30 09:24:27'),
			array('id' => '24','tribe_name' => 'HADZABE','created_at' => '2017-11-30 09:24:27','updated_at' => '2017-11-30 09:24:27'),
			array('id' => '25','tribe_name' => 'HANGAZA','created_at' => '2017-11-30 09:24:27','updated_at' => '2017-11-30 09:24:27'),
			array('id' => '26','tribe_name' => 'HAYA','created_at' => '2017-11-30 09:24:27','updated_at' => '2017-11-30 09:24:27'),
			array('id' => '27','tribe_name' => 'HEHE','created_at' => '2017-11-30 09:24:27','updated_at' => '2017-11-30 09:24:27'),
			array('id' => '28','tribe_name' => 'HINDA','created_at' => '2017-11-30 09:24:27','updated_at' => '2017-11-30 09:24:27'),
			array('id' => '29','tribe_name' => 'HUTU','created_at' => '2017-11-30 09:24:27','updated_at' => '2017-11-30 09:24:27'),
			array('id' => '30','tribe_name' => 'IKIZU','created_at' => '2017-11-30 09:24:27','updated_at' => '2017-11-30 09:24:27'),
			array('id' => '31','tribe_name' => 'IKOMA','created_at' => '2017-11-30 09:24:27','updated_at' => '2017-11-30 09:24:27'),
			array('id' => '32','tribe_name' => 'IRAQW','created_at' => '2017-11-30 09:24:27','updated_at' => '2017-11-30 09:24:27'),
			array('id' => '33','tribe_name' => 'ISANZU','created_at' => '2017-11-30 09:24:27','updated_at' => '2017-11-30 09:24:27'),
			array('id' => '34','tribe_name' => 'JALUO','created_at' => '2017-11-30 09:24:27','updated_at' => '2017-11-30 09:24:27'),
			array('id' => '35','tribe_name' => 'JIJI','created_at' => '2017-11-30 09:24:27','updated_at' => '2017-11-30 09:24:27'),
			array('id' => '36','tribe_name' => 'JITA','created_at' => '2017-11-30 09:24:27','updated_at' => '2017-11-30 09:24:27'),
			array('id' => '37','tribe_name' => 'KABWA','created_at' => '2017-11-30 09:24:27','updated_at' => '2017-11-30 09:24:27'),
			array('id' => '38','tribe_name' => 'KAGURU','created_at' => '2017-11-30 09:24:27','updated_at' => '2017-11-30 09:24:27'),
			array('id' => '39','tribe_name' => 'KAHE','created_at' => '2017-11-30 09:24:28','updated_at' => '2017-11-30 09:24:28'),
			array('id' => '40','tribe_name' => 'KAMI','created_at' => '2017-11-30 09:24:28','updated_at' => '2017-11-30 09:24:28'),
			array('id' => '41','tribe_name' => 'KARA','created_at' => '2017-11-30 09:24:28','updated_at' => '2017-11-30 09:24:28'),
			array('id' => '42','tribe_name' => 'KEREWE','created_at' => '2017-11-30 09:24:28','updated_at' => '2017-11-30 09:24:28'),
			array('id' => '43','tribe_name' => 'KIMBU','created_at' => '2017-11-30 09:24:28','updated_at' => '2017-11-30 09:24:28'),
			array('id' => '44','tribe_name' => 'KINGA','created_at' => '2017-11-30 09:24:28','updated_at' => '2017-11-30 09:24:28'),
			array('id' => '45','tribe_name' => 'KISANKASA','created_at' => '2017-11-30 09:24:28','updated_at' => '2017-11-30 09:24:28'),
			array('id' => '46','tribe_name' => 'KISI','created_at' => '2017-11-30 09:24:28','updated_at' => '2017-11-30 09:24:28'),
			array('id' => '47','tribe_name' => 'KONONGO','created_at' => '2017-11-30 09:24:28','updated_at' => '2017-11-30 09:24:28'),
			array('id' => '48','tribe_name' => 'KURIA','created_at' => '2017-11-30 09:24:28','updated_at' => '2017-11-30 09:24:28'),
			array('id' => '49','tribe_name' => 'KUTU','created_at' => '2017-11-30 09:24:29','updated_at' => '2017-11-30 09:24:29'),
			array('id' => '50','tribe_name' => 'KWADZA','created_at' => '2017-11-30 09:24:29','updated_at' => '2017-11-30 09:24:29'),
			array('id' => '51','tribe_name' => 'KWAVI','created_at' => '2017-11-30 09:24:29','updated_at' => '2017-11-30 09:24:29'),
			array('id' => '52','tribe_name' => 'KWAYA','created_at' => '2017-11-30 09:24:29','updated_at' => '2017-11-30 09:24:29'),
			array('id' => '53','tribe_name' => 'KWERE','created_at' => '2017-11-30 09:24:29','updated_at' => '2017-11-30 09:24:29'),
			array('id' => '54','tribe_name' => 'KWIFA','created_at' => '2017-11-30 09:24:29','updated_at' => '2017-11-30 09:24:29'),
			array('id' => '55','tribe_name' => 'LAMBYA','created_at' => '2017-11-30 09:24:29','updated_at' => '2017-11-30 09:24:29'),
			array('id' => '56','tribe_name' => 'LUGURU','created_at' => '2017-11-30 09:24:29','updated_at' => '2017-11-30 09:24:29'),
			array('id' => '57','tribe_name' => 'LUNGU','created_at' => '2017-11-30 09:24:29','updated_at' => '2017-11-30 09:24:29'),
			array('id' => '58','tribe_name' => 'MACHINGA','created_at' => '2017-11-30 09:24:29','updated_at' => '2017-11-30 09:24:29'),
			array('id' => '59','tribe_name' => 'MAGOMA','created_at' => '2017-11-30 09:24:29','updated_at' => '2017-11-30 09:24:29'),
			array('id' => '60','tribe_name' => 'MAHANJI','created_at' => '2017-11-30 09:24:29','updated_at' => '2017-11-30 09:24:29'),
			array('id' => '61','tribe_name' => 'MAKONDE','created_at' => '2017-11-30 09:24:29','updated_at' => '2017-11-30 09:24:29'),
			array('id' => '62','tribe_name' => 'MAKUA','created_at' => '2017-11-30 09:24:29','updated_at' => '2017-11-30 09:24:29'),
			array('id' => '63','tribe_name' => 'MAKWE','created_at' => '2017-11-30 09:24:29','updated_at' => '2017-11-30 09:24:29'),
			array('id' => '64','tribe_name' => 'MALILA','created_at' => '2017-11-30 09:24:29','updated_at' => '2017-11-30 09:24:29'),
			array('id' => '65','tribe_name' => 'MAMBWE','created_at' => '2017-11-30 09:24:29','updated_at' => '2017-11-30 09:24:29'),
			array('id' => '66','tribe_name' => 'MANDA','created_at' => '2017-11-30 09:24:29','updated_at' => '2017-11-30 09:24:29'),
			array('id' => '67','tribe_name' => 'MANYEMA','created_at' => '2017-11-30 09:24:30','updated_at' => '2017-11-30 09:24:30'),
			array('id' => '68','tribe_name' => 'MASAI','created_at' => '2017-11-30 09:24:30','updated_at' => '2017-11-30 09:24:30'),
			array('id' => '69','tribe_name' => 'MATENGO','created_at' => '2017-11-30 09:24:30','updated_at' => '2017-11-30 09:24:30'),
			array('id' => '70','tribe_name' => 'MATUMBI','created_at' => '2017-11-30 09:24:30','updated_at' => '2017-11-30 09:24:30'),
			array('id' => '71','tribe_name' => 'MAVIHA','created_at' => '2017-11-30 09:24:30','updated_at' => '2017-11-30 09:24:30'),
			array('id' => '72','tribe_name' => 'MBUGWE','created_at' => '2017-11-30 09:24:30','updated_at' => '2017-11-30 09:24:30'),
			array('id' => '73','tribe_name' => 'MBUNGA','created_at' => '2017-11-30 09:24:30','updated_at' => '2017-11-30 09:24:30'),
			array('id' => '74','tribe_name' => 'MERU','created_at' => '2017-11-30 09:24:30','updated_at' => '2017-11-30 09:24:30'),
			array('id' => '75','tribe_name' => 'MOSIRO','created_at' => '2017-11-30 09:24:30','updated_at' => '2017-11-30 09:24:30'),
			array('id' => '76','tribe_name' => 'MPOTO','created_at' => '2017-11-30 09:24:30','updated_at' => '2017-11-30 09:24:30'),
			array('id' => '77','tribe_name' => 'MWERA','created_at' => '2017-11-30 09:24:30','updated_at' => '2017-11-30 09:24:30'),
			array('id' => '78','tribe_name' => 'NDALI','created_at' => '2017-11-30 09:24:30','updated_at' => '2017-11-30 09:24:30'),
			array('id' => '79','tribe_name' => 'NDAMBA','created_at' => '2017-11-30 09:24:30','updated_at' => '2017-11-30 09:24:30'),
			array('id' => '80','tribe_name' => 'NDENDEULE','created_at' => '2017-11-30 09:24:30','updated_at' => '2017-11-30 09:24:30'),
			array('id' => '81','tribe_name' => 'NDENGEREKO','created_at' => '2017-11-30 09:24:30','updated_at' => '2017-11-30 09:24:30'),
			array('id' => '82','tribe_name' => 'NDONDE','created_at' => '2017-11-30 09:24:31','updated_at' => '2017-11-30 09:24:31'),
			array('id' => '83','tribe_name' => 'NENA','created_at' => '2017-11-30 09:24:31','updated_at' => '2017-11-30 09:24:31'),
			array('id' => '84','tribe_name' => 'NGASA','created_at' => '2017-11-30 09:24:31','updated_at' => '2017-11-30 09:24:31'),
			array('id' => '85','tribe_name' => 'NGINDO','created_at' => '2017-11-30 09:24:31','updated_at' => '2017-11-30 09:24:31'),
			array('id' => '86','tribe_name' => 'NGONI','created_at' => '2017-11-30 09:24:31','updated_at' => '2017-11-30 09:24:31'),
			array('id' => '87','tribe_name' => 'NGULU','created_at' => '2017-11-30 09:24:31','updated_at' => '2017-11-30 09:24:31'),
			array('id' => '88','tribe_name' => 'NGURIMI','created_at' => '2017-11-30 09:24:31','updated_at' => '2017-11-30 09:24:31'),
			array('id' => '89','tribe_name' => 'NYIRAMBA','created_at' => '2017-11-30 09:24:31','updated_at' => '2017-11-30 09:24:31'),
			array('id' => '90','tribe_name' => 'NINDI','created_at' => '2017-11-30 09:24:31','updated_at' => '2017-11-30 09:24:31'),
			array('id' => '91','tribe_name' => 'NYAKYUSA','created_at' => '2017-11-30 09:24:31','updated_at' => '2017-11-30 09:24:31'),
			array('id' => '92','tribe_name' => 'NYAMBO','created_at' => '2017-11-30 09:24:31','updated_at' => '2017-11-30 09:24:31'),
			array('id' => '93','tribe_name' => 'NYAMWANGA','created_at' => '2017-11-30 09:24:31','updated_at' => '2017-11-30 09:24:31'),
			array('id' => '94','tribe_name' => 'NYAMWEZI','created_at' => '2017-11-30 09:24:31','updated_at' => '2017-11-30 09:24:31'),
			array('id' => '95','tribe_name' => 'NYANYEMBE','created_at' => '2017-11-30 09:24:31','updated_at' => '2017-11-30 09:24:31'),
			array('id' => '96','tribe_name' => 'NYASA','created_at' => '2017-11-30 09:24:31','updated_at' => '2017-11-30 09:24:31'),
			array('id' => '97','tribe_name' => 'NYATURU','created_at' => '2017-11-30 09:24:32','updated_at' => '2017-11-30 09:24:32'),
			array('id' => '98','tribe_name' => 'NYIHA','created_at' => '2017-11-30 09:24:32','updated_at' => '2017-11-30 09:24:32'),
			array('id' => '99','tribe_name' => 'OKIEK','created_at' => '2017-11-30 09:24:32','updated_at' => '2017-11-30 09:24:32'),
			array('id' => '100','tribe_name' => 'PANGWA','created_at' => '2017-11-30 09:24:32','updated_at' => '2017-11-30 09:24:32'),
			array('id' => '101','tribe_name' => 'PARE','created_at' => '2017-11-30 09:24:32','updated_at' => '2017-11-30 09:24:32'),
			array('id' => '102','tribe_name' => 'PIMBWE','created_at' => '2017-11-30 09:24:32','updated_at' => '2017-11-30 09:24:32'),
			array('id' => '103','tribe_name' => 'POGOLO','created_at' => '2017-11-30 09:24:32','updated_at' => '2017-11-30 09:24:32'),
			array('id' => '104','tribe_name' => 'RANGI','created_at' => '2017-11-30 09:24:32','updated_at' => '2017-11-30 09:24:32'),
			array('id' => '105','tribe_name' => 'RUFIJI','created_at' => '2017-11-30 09:24:32','updated_at' => '2017-11-30 09:24:32'),
			array('id' => '106','tribe_name' => 'RUNGI','created_at' => '2017-11-30 09:24:32','updated_at' => '2017-11-30 09:24:32'),
			array('id' => '107','tribe_name' => 'RUNGU','created_at' => '2017-11-30 09:24:32','updated_at' => '2017-11-30 09:24:32'),
			array('id' => '108','tribe_name' => 'RUNGWA','created_at' => '2017-11-30 09:24:32','updated_at' => '2017-11-30 09:24:32'),
			array('id' => '109','tribe_name' => 'WARWA','created_at' => '2017-11-30 09:24:32','updated_at' => '2017-11-30 09:24:32'),
			array('id' => '110','tribe_name' => 'SAFWA','created_at' => '2017-11-30 09:24:32','updated_at' => '2017-11-30 09:24:32'),
			array('id' => '111','tribe_name' => 'SAGARA','created_at' => '2017-11-30 09:24:33','updated_at' => '2017-11-30 09:24:33'),
			array('id' => '112','tribe_name' => 'SANDAWE','created_at' => '2017-11-30 09:24:33','updated_at' => '2017-11-30 09:24:33'),
			array('id' => '113','tribe_name' => 'SANGU','created_at' => '2017-11-30 09:24:33','updated_at' => '2017-11-30 09:24:33'),
			array('id' => '114','tribe_name' => 'SEGEJU','created_at' => '2017-11-30 09:24:33','updated_at' => '2017-11-30 09:24:33'),
			array('id' => '115','tribe_name' => 'SAMBAA','created_at' => '2017-11-30 09:24:33','updated_at' => '2017-11-30 09:24:33'),
			array('id' => '116','tribe_name' => 'SHIRAZI','created_at' => '2017-11-30 09:24:33','updated_at' => '2017-11-30 09:24:33'),
			array('id' => '117','tribe_name' => 'SHUBI','created_at' => '2017-11-30 09:24:33','updated_at' => '2017-11-30 09:24:33'),
			array('id' => '118','tribe_name' => 'SIZAKI','created_at' => '2017-11-30 09:24:33','updated_at' => '2017-11-30 09:24:33'),
			array('id' => '119','tribe_name' => 'WASUBA','created_at' => '2017-11-30 09:24:33','updated_at' => '2017-11-30 09:24:33'),
			array('id' => '120','tribe_name' => 'SUKUMA','created_at' => '2017-11-30 09:24:33','updated_at' => '2017-11-30 09:24:33'),
			array('id' => '121','tribe_name' => 'SUMBWA','created_at' => '2017-11-30 09:24:33','updated_at' => '2017-11-30 09:24:33'),
			array('id' => '122','tribe_name' => 'SWAHILI','created_at' => '2017-11-30 09:24:33','updated_at' => '2017-11-30 09:24:33'),
			array('id' => '123','tribe_name' => 'TAVETA','created_at' => '2017-11-30 09:24:33','updated_at' => '2017-11-30 09:24:33'),
			array('id' => '124','tribe_name' => 'TEMI','created_at' => '2017-11-30 09:24:33','updated_at' => '2017-11-30 09:24:33'),
			array('id' => '125','tribe_name' => 'TONGWE','created_at' => '2017-11-30 09:24:34','updated_at' => '2017-11-30 09:24:34'),
			array('id' => '126','tribe_name' => 'TUMBUKA','created_at' => '2017-11-30 09:24:34','updated_at' => '2017-11-30 09:24:34'),
			array('id' => '127','tribe_name' => 'VIDUNDA','created_at' => '2017-11-30 09:24:34','updated_at' => '2017-11-30 09:24:34'),
			array('id' => '128','tribe_name' => 'VINZA','created_at' => '2017-11-30 09:24:34','updated_at' => '2017-11-30 09:24:34'),
			array('id' => '129','tribe_name' => 'WANDA','created_at' => '2017-11-30 09:24:34','updated_at' => '2017-11-30 09:24:34'),
			array('id' => '130','tribe_name' => 'WANJI','created_at' => '2017-11-30 09:24:34','updated_at' => '2017-11-30 09:24:34'),
			array('id' => '131','tribe_name' => 'WARE','created_at' => '2017-11-30 09:24:34','updated_at' => '2017-11-30 09:24:34'),
			array('id' => '132','tribe_name' => 'YAO','created_at' => '2017-11-30 09:24:34','updated_at' => '2017-11-30 09:24:34'),
			array('id' => '133','tribe_name' => 'ZANAKI','created_at' => '2017-11-30 09:24:34','updated_at' => '2017-11-30 09:24:34'),
			array('id' => '134','tribe_name' => 'ZARAMO','created_at' => '2017-11-30 09:24:34','updated_at' => '2017-11-30 09:24:34'),
			array('id' => '135','tribe_name' => 'ZIGULA','created_at' => '2017-11-30 09:24:34','updated_at' => '2017-11-30 09:24:34'),
			array('id' => '136','tribe_name' => 'ZINZA','created_at' => '2017-11-30 09:24:34','updated_at' => '2017-11-30 09:24:34'),
			array('id' => '137','tribe_name' => 'ZYOBA','created_at' => '2017-11-30 09:24:34','updated_at' => '2017-11-30 09:24:34')
		);

		$eyes = array(
            ['description' =>'Normal','category' =>'Extraocular'],
            ['description' =>'Eye deviating outward - Exotropia','category' =>'Extraocular'],
            ['description' =>'Eye deviating inward - Esotropia','category' =>'Extraocular'],
            ['description' =>'Involuntary oscillation of the eye - Nystagmus','category' =>'Extraocular'],

            ['description' =>'Normal','category' =>'Eyelid'],
            ['description' =>'Inability to open  the eye fully - Ptosis','category' =>'Eyelid'],
            ['description' =>'Inability to close the eyelid - Lagopthalmos','category' =>'Eyelid'],
            ['description' =>'Outward turning of the eyelid margin - Exotropion','category' =>'Eyelid'],
            ['description' =>'Inward turning of the eyelid margin - Entropion','category' =>'Eyelid'],
            ['description' =>'Inward turning of the eyelashes rubbing the globe - Trichiasis','category' =>'Eyelid'],
            ['description' =>'Localized abscess of an eyelash follicle - stye','category' =>'Eyelid'],
            ['description' =>'Abscess of meibomian gland - chalazion','category' =>'Eyelid'],
            ['description' =>'Chronic infection of the eyelid margins - blepharitis','category' =>'Eyelid'],
            ['description' =>'Overflow tearing - epiphora','category' =>'Eyelid'],
            ['description' =>'Obstruction of lacrimal drainage - Nasal lacrimal duct obstruction','category' =>'Eyelid'],
            ['description' =>'Inflamation of lacrimal sac - Dacryocystitis','category' =>'Eyelid'],
            ['description' =>'Inflamation of lacrimal gland - Dacryoddenitis','category' =>'Eyelid'],

            ['description' =>'Normal','category' =>'Conjuctival/Sclera'],
            ['description' =>'Infected or inflammation of the conjuctiva','category' =>'Conjuctival/Sclera'],
            ['description' =>'Whitish degenerative mass usually next limbus - pingueculum','category' =>'Conjuctival/Sclera'],
            ['description' =>'Wedge shaped conjuctival tissue growing over the cornea - pterygium','category' =>'Conjuctival/Sclera'],
            ['description' =>'Adhesions','category' =>'Conjuctival/Sclera'],
            ['description' =>'Follicles','category' =>'Conjuctival/Sclera'],
            ['description' =>'New growth','category' =>'Conjuctival/Sclera'],
            ['description' =>'Inflammed Sclera','category' =>'Conjuctival/Sclera'],

            ['description' =>'Normal','category' =>'Cornea'],
            ['description' =>'Opacity - staining with fluoresseine','category' =>'Cornea'],
            ['description' =>'Opacity - not staining with fluoresseine','category' =>'Cornea'],
            ['description' =>'Foreign body','category' =>'Cornea'],
            ['description' =>'Conical shape','category' =>'Cornea'],
            ['description' =>'Ring opacification of the peripheral cornea - Arcus senilis','category' =>'Cornea'],
            ['description' =>'Brow ( pigmented) benign lession usually on the bulbar conjuctiva - naems','category' =>'Cornea'],
            ['description' =>'Keratic precipitate','category' =>'Cornea'],
            ['description' =>'Megalocornea','category' =>'Cornea'],
            ['description' =>'Microcornea','category' =>'Cornea'],

            ['description' =>'Deep','category' =>'Anterior Chamber'],
            ['description' =>'Shallow','category' =>'Anterior Chamber'],
            ['description' =>'Aqueous cells grade 5-10 cells (+1)','category' =>'Anterior Chamber'],
            ['description' =>'Aqueous cells grade 11-20 cells (+2)','category' =>'Anterior Chamber'],
            ['description' =>'Aqueous cells grade 21-50 cells (+3)','category' =>'Anterior Chamber'],
            ['description' =>'Aqueous cells grade >50 cells (+4)','category' =>'Anterior Chamber'],
            ['description' =>'Aqueous flare','category' =>'Anterior Chamber'],
            ['description' =>'Pus cells - hypopion','category' =>'Anterior Chamber'],
            ['description' =>'Blood hyphaena','category' =>'Anterior Chamber'],
            ['description' =>'Abnormal vessels growing - angle neovascularisation - Rubeosis','category' =>'Anterior Chamber'],
            ['description' =>'A thickened schwalbe line - posterior embryotoxon','category' =>'Anterior Chamber'],
            ['description' =>'Foreign body','category' =>'Anterior Chamber'],
            ['description' =>'Angle closure','category' =>'Anterior Chamber'],

            ['description' =>'Round and reactive (normal)','category' =>'Pupil'],
            ['description' =>'Constricted pupil(pin point pupil) miotic','category' =>'Pupil'],
            ['description' =>'White pupil - retinoblastoma','category' =>'Pupil'],
            ['description' =>'White pupil - persistent pupillary membrane','category' =>'Pupil'],
            ['description' =>'Sluggish','category' =>'Pupil'],
            ['description' =>'Dilated - semi dilated','category' =>'Pupil'],
            ['description' =>'Dilated - full dilated','category' =>'Pupil'],
            ['description' =>'Irregular','category' =>'Pupil'],
            ['description' =>'Irregular with synaechia - posterior','category' =>'Pupil'],
            ['description' =>'Irregular with synaechia - anterior','category' =>'Pupil'],

            ['description' =>'Clear','category' =>'Lens'],
            ['description' =>'No lens - Aphakia','category' =>'Lens'],
            ['description' =>'Artificial lens -(IOL) Pseudophakia','category' =>'Lens'],
            ['description' =>'Nuclear opacity - nuclear cataract','category' =>'Lens'],
            ['description' =>'entire lens opacity - mature cataract','category' =>'Lens'],
            ['description' =>'Cortical or spokelike opacity - cortical cataract','category' =>'Lens'],
            ['description' =>'some parts of the lens remain clear - immature cat','category' =>'Lens'],
            ['description' =>'Posterior capsule opacity','category' =>'Lens'],
            ['description' =>'Swollen lens milky white- intumescent','category' =>'Lens'],
            ['description' =>'Lens cortex become liquid - hypermature cataract','category' =>'Lens'],
            ['description' =>'Dislocated lens','category' =>'Lens'],
            ['description' =>'Sub-luxated lens','category' =>'Lens'],

            ['description' =>'Normal - clear','category' =>'Vitreous'],
            ['description' =>'Cells','category' =>'Vitreous'],
            ['description' =>'Blood','category' =>'Vitreous'],
            ['description' =>'Opacities','category' =>'Vitreous'],
            ['description' =>'Strands','category' =>'Vitreous'],
            ['description' =>'Detatchment','category' =>'Vitreous'],

            ['description' =>'Detatchment','category' =>'Blood vessels'],
            ['description' =>'Coper wiring or silver wiring','category' =>'Blood vessels'],
            ['description' =>'Vascular artenuation','category' =>'Blood vessels'],
            ['description' =>'Haemorrhages','category' =>'Blood vessels'],
            ['description' =>'Arteriolar narrowing','category' =>'Blood vessels'],

            ['description' =>'Lipid deposit ','category' =>'Exudates'],
            ['description' =>'Normal','category' =>'Macular'],
            ['description' =>'Hole','category' =>'Macular'],
            ['description' =>'Scar','category' =>'Macular'],
            ['description' =>'Oedema','category' =>'Macular'],

            ['description' =>'Pale','category' =>'Disc'],
            ['description' =>'Oedema','category' =>'Disc'],
            ['description' =>'New blood vessels','category' =>'Disc'],
            ['description' =>'Cupped','category' =>'Disc'],
            ['description' =>'Coloboma','category' =>'Disc'],
            ['description' =>'Normal','category' =>'Disc'],
        );

		
		//mtuha
		
		$OPDMtuhaDiagnoses = array(
			['id'=> 1,'description' => 'Acute Flacid Paralysis'],
			['id'=> 2,'description' => 'Cholera'],
			['id'=> 3,'description' => 'Dysentery'],
			['id'=> 4,'description' => 'Measles'],
			['id'=> 5,'description' => 'Meningitis'],
			['id'=> 6,'description' => 'Neonatal Tetanus'],
			['id'=> 7,'description' => 'Plague'],
			['id'=> 8,'description' => 'Relapsing Fever (Louse Borne Typhus)'],
			['id'=> 9,'description' => 'Yellow Fever'],
			['id'=> 10,'description' => 'Influenza'],
			['id'=> 11,'description' => 'Typhoid'],
			['id'=> 12,'description' => 'Rabies/Suspected Rabies Bites'],
			['id'=> 13,'description' => 'Onchocerciasis'],
			['id'=> 14,'description' => 'Trypanosomiasis'],
			['id'=> 15,'description' => 'Viral Haemorrhagic Fevers'],
			['id'=> 16,'description' => 'Diarrhea With No Dehydration'],
			['id'=> 17,'description' => 'Diarrhea With Some Dehydration'],
			['id'=> 18,'description' => 'Diarrhea With Severe Dehydration'],
			['id'=> 19,'description' => 'Schistosomiasis'],
			['id'=> 20,'description' => 'Malaria BS Positive'],
			['id'=> 21,'description' => 'Malaria mRDT Positive'],
			['id'=> 22,'description' => 'Malaria Clinical (No Test)'],
			['id'=> 23,'description' => 'Malaria in Pregnancy'],
			['id'=> 24,'description' => 'STI Genital Discharge Syndrome (GDS)'],
			['id'=> 25,'description' => 'STI Genital Ulcer Diseases (GUD)'],
			['id'=> 26,'description' => 'STI Pelvic Inflammatory Diseases (PID)'],
			['id'=> 27,'description' => 'Sexually Transmitted Infections, Other'],
			['id'=> 28,'description' => 'Tuberculosis'],
			['id'=> 29,'description' => 'Leprosy'],
			['id'=> 30,'description' => 'Intestinal Worms'],
			['id'=> 31,'description' => 'Anaemia, Mild / Moderate'],
			['id'=> 32,'description' => 'Anaemia, Severe'],
			['id'=> 33,'description' => 'Sickle Cell Disease'],
			['id'=> 34,'description' => 'Ear Infection, Acute'],
			['id'=> 35,'description' => 'Ear Infection, Chronic'],
			['id'=> 36,'description' => 'Eye Diseases, Infectious'],
			['id'=> 37,'description' => 'Eye Diseases, Non infectious'],
			['id'=> 38,'description' => 'Eye Diseases, Injuries'],
			['id'=> 39,'description' => 'Skin Infection, Non-Fungal'],
			['id'=> 40,'description' => 'Skin Infection, Fungal'],
			['id'=> 41,'description' => 'Skin Diseases, Non-infectious'],
			['id'=> 42,'description' => 'Fungal Infection, Non-skin'],
			['id'=> 43,'description' => 'Osteomyelitis'],
			['id'=> 44,'description' => 'Neonatal Sepsis'],
			['id'=> 45,'description' => 'Low Birth Weight and Prematurity  Complications'],
			['id'=> 46,'description' => 'Birth Asphyxia'],
			['id'=> 47,'description' => 'Pneumonia, Non-Severe'],
			['id'=> 48,'description' => 'Pneumonia, Severe'],
			['id'=> 49,'description' => 'Upper Respiratory Infections  (Pharyngitis, Tonsillitis, Rhinitis)'],
			['id'=> 50,'description' => 'Cerebral Palsy'],
			['id'=> 51,'description' => 'Urinary Tract Infections (UTI)'],
			['id'=> 52,'description' => 'Gynaecological Diseases, Other'],
			['id'=> 53,'description' => 'Kwashiorkor'],
			['id'=> 54,'description' => 'Marasmus'],
			['id'=> 55,'description' => 'Marasmic Kwashiorkor'],
			['id'=> 56,'description' => 'Moderate Malnutrition'],
			['id'=> 57,'description' => 'Vitamin A Deficiency'],
			['id'=> 58,'description' => 'Other Nutritional Disorders'],
			['id'=> 59,'description' => 'Caries'],
			['id'=> 60,'description' => 'Periodontal Diseases'],
			['id'=> 61,'description' => 'Dental Emergency Care'],
			['id'=> 62,'description' => 'Dental Conditions, Other'],
			['id'=> 63,'description' => 'Fractures / Dislocations'],
			['id'=> 64,'description' => 'Burn'],
			['id'=> 65,'description' => 'Poisoning'],
			['id'=> 66,'description' => 'Road Traffic Accidents'],
			['id'=> 67,'description' => 'Pregnancy Complications'],
			['id'=> 68,'description' => 'Abortion'],
			['id'=> 69,'description' => 'Snake and Insect Bites'],
			['id'=> 70,'description' => 'Animal Bite (Suspected Rabies)'],
			['id'=> 71,'description' => 'Animal Bite (No Suspected Rabies)'],
			['id'=> 72,'description' => 'Emergencies, Other'],
			['id'=> 73,'description' => 'Surgical Conditions, Other'],
			['id'=> 74,'description' => 'Epilepsy'],
			['id'=> 75,'description' => 'Psychoses'],
			['id'=> 76,'description' => 'Neuroses'],
			['id'=> 77,'description' => 'Substance Abuse'],
			['id'=> 78,'description' => 'Hypertension'],
			['id'=> 79,'description' => 'Rheumatic Fever'],
			['id'=> 80,'description' => 'Cardiovascular Diseases, Other'],
			['id'=> 81,'description' => 'Bronchial Asthma'],
			['id'=> 82,'description' => 'Peptic Ulcer, Site Unspecified'],
			['id'=> 83,'description' => 'GIT Diseases, Other Non-infectious'],
			['id'=> 84,'description' => 'Diabetes Mellitus'],
			['id'=> 85,'description' => 'Rheumatoid and Joint Diseases'],
			['id'=> 86,'description' => 'Thyroid Diseases'],
			['id'=> 87,'description' => 'Neoplasm/Cancer'],
			['id'=> 88,'description' => 'Ill Defined Symptoms (No Diagnosis)']			
		);
		
		
		$IPDMtuhaDiagnoses = array(
			['id'=> 1,'description' => 'Acute Flacid Paralysis'],
			['id'=> 2,'description' => 'Cholera'],
			['id'=> 3,'description' => 'Dysentery'],
			['id'=> 4,'description' => 'Measles'],
			['id'=> 5,'description' => 'Meningitis'],
			['id'=> 6,'description' => 'Neonatal Tetanus'],
			['id'=> 7,'description' => 'Plague'],
			['id'=> 8,'description' => 'Relapsing Fever (Louse Borne Typhus)'],
			['id'=> 9,'description' => 'Typhoid'],
			['id'=> 10,'description' => 'Diarrhea, Acute (< 14 Days)'],
			['id'=> 11,'description' => 'Diarrhea, Chronic (> or = 14 Days)'],
			['id'=> 12,'description' => 'Positive (BS/mRDT)'],
			['id'=> 13,'description' => 'Clinical (No Test)'],
			['id'=> 14,'description' => 'Schistosomiasis'],
			['id'=> 15,'description' => 'STI Genital Discharge Syndrome (GDS)'],
			['id'=> 16,'description' => 'STI Genital Ulcer Diseases (GUD)'],
			['id'=> 17,'description' => 'STI Pelvic Inflammatory Diseases (PID)'],
			['id'=> 18,'description' => 'Sexually Transmitted Infection, Other'],
			['id'=> 19,'description' => 'Ectopic Pregnancy'],
			['id'=> 20,'description' => 'Abortion Complications'],
			['id'=> 21,'description' => 'Gynaecological Diseases, Other'],
			['id'=> 22,'description' => 'Tuberculosis'],
			['id'=> 23,'description' => 'Leprosy'],
			['id'=> 24,'description' => 'Diabetes'],
			['id'=> 25,'description' => 'Kwashiorkor'],
			['id'=> 26,'description' => 'Marasmus'],
			['id'=> 27,'description' => 'Marasmic - Kwashiorkor'],
			['id'=> 28,'description' => 'Moderate Malnutrition'],
			['id'=> 29,'description' => 'Nutritional Disorders, Other'],
			['id'=> 30,'description' => 'Thyroid Diseases'],
			['id'=> 31,'description' => 'Sickle Cell Disease'],
			['id'=> 32,'description' => 'Haematological Disorder'],
			['id'=> 33,'description' => 'Anaemia, Mild / Moderate'],
			['id'=> 34,'description' => 'Anaemia, Severe'],
			['id'=> 35,'description' => 'Psychoses'],
			['id'=> 36,'description' => 'Neuroses'],
			['id'=> 37,'description' => 'Substance Abuse'],
			['id'=> 38,'description' => 'Cerebral Palsy'],
			['id'=> 39,'description' => 'Epilepsy'],
			['id'=> 40,'description' => 'Ear Diseases, Non-Infectious'],
			['id'=> 41,'description' => 'Ear Infection, Acute'],
			['id'=> 42,'description' => 'Ear Infection, Chronic'],
			['id'=> 43,'description' => 'Eye Diseases, Infectious'],
			['id'=> 44,'description' => 'Eye Diseases, Non-Infectious'],
			['id'=> 45,'description' => 'Eye Diseases, Injuries'],
			['id'=> 46,'description' => 'Cardiac Failure'],
			['id'=> 47,'description' => 'Hypertension, Severe'],
			['id'=> 48,'description' => 'Other Febrile Illnesses'],
			['id'=> 49,'description' => 'Cardiovascular Disorders, Other'],
			['id'=> 50,'description' => 'Bronchial Asthma, Severe'],
			['id'=> 51,'description' => 'Upper Respiratory Infections  (Pharyngitis, Tonsillitis, Rhinitis)'],
			['id'=> 52,'description' => 'Pneumonia'],
			['id'=> 53,'description' => 'Pneumonia, Severe'],
			['id'=> 54,'description' => 'Peptic Ulcers'],
			['id'=> 55,'description' => 'Liver Diseases, Non-infectious'],
			['id'=> 56,'description' => 'Gastrointerstinal Diseases, Other Non-infectious'],
			['id'=> 57,'description' => 'Urinary Tract Infections (UTI)'],
			['id'=> 58,'description' => 'Nephrotic Syndrome'],
			['id'=> 59,'description' => 'Acute Glumerulomephritis'],
			['id'=> 60,'description' => 'Renal Failure'],
			['id'=> 61,'description' => 'Skin Infections'],
			['id'=> 62,'description' => 'Skin Diseases, Non-Infectious'],
			['id'=> 63,'description' => 'Osteomyelitis'],
			['id'=> 64,'description' => 'Rheumatoid and Joint Diseases'],
			['id'=> 65,'description' => 'Low Birth Weight and Prematurity Complications'],
			['id'=> 66,'description' => 'Birth Asphyxia'],
			['id'=> 67,'description' => 'Neonatal Septicaemia (Local Infections)'],
			['id'=> 68,'description' => 'Road Traffic Accidents'],
			['id'=> 69,'description' => 'Fractures/Dislocations'],
			['id'=> 70,'description' => 'Poisoning'],
			['id'=> 71,'description' => 'Burns'],
			['id'=> 72,'description' => 'Animal Bites (Suspected Rabies)'],
			['id'=> 73,'description' => 'Animal Bites (No Suspected Rabies)'],
			['id'=> 74,'description' => 'HIV Infection Symptomatic'],
			['id'=> 75,'description' => 'Congenital Disorders'],
			['id'=> 76,'description' => 'Hepatatis'],
			['id'=> 77,'description' => 'Neoplasm'],
			['id'=> 78,'description' => 'Soil Transmitted Helminthes'],
			['id'=> 79,'description' => 'Lymphatic Filairiasis'],
			['id'=> 80,'description' => 'Anthrax'],
			['id'=> 81,'description' => 'Viral Haemorrhagic Fevers']
		);
		
		$OPDMtuhaICDMapping = array(
			['opd_mtuha_diagnosis_id' =>1 , 'icd_block' =>  'G80'],
			['opd_mtuha_diagnosis_id' =>1 , 'icd_block' =>  'G81'],
			['opd_mtuha_diagnosis_id' =>1 , 'icd_block' =>  'G82'],
			['opd_mtuha_diagnosis_id' =>1 , 'icd_block' =>  'G83'],
			['opd_mtuha_diagnosis_id' =>2 , 'icd_block' =>  'A00'],
			['opd_mtuha_diagnosis_id' =>3 , 'icd_block' =>  'A09'],
			['opd_mtuha_diagnosis_id' =>4 , 'icd_block' =>  'B05'],
			['opd_mtuha_diagnosis_id' =>5 , 'icd_block' =>  'G03'],
			['opd_mtuha_diagnosis_id' =>6 , 'icd_block' =>  'A33'],
			['opd_mtuha_diagnosis_id' =>7 , 'icd_block' =>  'A20'],
			['opd_mtuha_diagnosis_id' =>8 , 'icd_block' =>  'A68'],
			['opd_mtuha_diagnosis_id' =>9 , 'icd_block' =>  'A95'],
			['opd_mtuha_diagnosis_id' =>10 , 'icd_block' =>  'J11'],
			['opd_mtuha_diagnosis_id' =>11 , 'icd_block' =>  'A01'],
			['opd_mtuha_diagnosis_id' =>12 , 'icd_block' =>  'A82'],
			['opd_mtuha_diagnosis_id' =>13 , 'icd_block' =>  'B66'],
			['opd_mtuha_diagnosis_id' =>13 , 'icd_block' =>  'B67'],
			['opd_mtuha_diagnosis_id' =>13 , 'icd_block' =>  'B68'],
			['opd_mtuha_diagnosis_id' =>13 , 'icd_block' =>  'B69'],
			['opd_mtuha_diagnosis_id' =>13 , 'icd_block' =>  'B70'],
			['opd_mtuha_diagnosis_id' =>13 , 'icd_block' =>  'B71'],
			['opd_mtuha_diagnosis_id' =>13 , 'icd_block' =>  'B72'],
			['opd_mtuha_diagnosis_id' =>13 , 'icd_block' =>  'B73'],
			['opd_mtuha_diagnosis_id' =>13 , 'icd_block' =>  'B74'],
			['opd_mtuha_diagnosis_id' =>13 , 'icd_block' =>  'B75'],
			['opd_mtuha_diagnosis_id' =>13 , 'icd_block' =>  'B76'],
			['opd_mtuha_diagnosis_id' =>13 , 'icd_block' =>  'B77'],
			['opd_mtuha_diagnosis_id' =>13 , 'icd_block' =>  'B78'],
			['opd_mtuha_diagnosis_id' =>13 , 'icd_block' =>  'B79'],
			['opd_mtuha_diagnosis_id' =>13 , 'icd_block' =>  'B80'],
			['opd_mtuha_diagnosis_id' =>13 , 'icd_block' =>  'B81'],
			['opd_mtuha_diagnosis_id' =>13 , 'icd_block' =>  'B83'],
			['opd_mtuha_diagnosis_id' =>14 , 'icd_block' =>  'B56'],
			['opd_mtuha_diagnosis_id' =>14 , 'icd_block' =>  'B57'],
			['opd_mtuha_diagnosis_id' =>15 , 'icd_block' =>  'A90'],
			['opd_mtuha_diagnosis_id' =>15 , 'icd_block' =>  'A91'],
			['opd_mtuha_diagnosis_id' =>15 , 'icd_block' =>  'A92'],
			['opd_mtuha_diagnosis_id' =>15 , 'icd_block' =>  'A93'],
			['opd_mtuha_diagnosis_id' =>15 , 'icd_block' =>  'A94'],
			['opd_mtuha_diagnosis_id' =>15 , 'icd_block' =>  'A96'],
			['opd_mtuha_diagnosis_id' =>15 , 'icd_block' =>  'A97'],
			['opd_mtuha_diagnosis_id' =>15 , 'icd_block' =>  'A98'],
			['opd_mtuha_diagnosis_id' =>15 , 'icd_block' =>  'A99'],
			['opd_mtuha_diagnosis_id' =>16 , 'icd_block' =>  NULL],
			['opd_mtuha_diagnosis_id' =>17 , 'icd_block' =>  'A09.0'],
			['opd_mtuha_diagnosis_id' =>18 , 'icd_block' =>  NULL],
			['opd_mtuha_diagnosis_id' =>19 , 'icd_block' =>  'B65'],
			['opd_mtuha_diagnosis_id' =>20 , 'icd_block' =>  NULL], //to be determined
			['opd_mtuha_diagnosis_id' =>21 , 'icd_block' =>  'B53'],
			['opd_mtuha_diagnosis_id' =>22 , 'icd_block' =>  'B54'],
			['opd_mtuha_diagnosis_id' =>23 , 'icd_block' =>  NULL],
			['opd_mtuha_diagnosis_id' =>24 , 'icd_block' =>  'A54'],
			['opd_mtuha_diagnosis_id' =>25 , 'icd_block' =>  'A53'],
			['opd_mtuha_diagnosis_id' =>26 , 'icd_block' =>  'N70'],
			['opd_mtuha_diagnosis_id' =>26 , 'icd_block' =>  'N71'],
			['opd_mtuha_diagnosis_id' =>26 , 'icd_block' =>  'N72'],
			['opd_mtuha_diagnosis_id' =>26 , 'icd_block' =>  'N73'],
			['opd_mtuha_diagnosis_id' =>26 , 'icd_block' =>  'N74'],
			['opd_mtuha_diagnosis_id' =>26 , 'icd_block' =>  'N75'],
			['opd_mtuha_diagnosis_id' =>26 , 'icd_block' =>  'N76'],
			['opd_mtuha_diagnosis_id' =>26 , 'icd_block' =>  'N77'],
			['opd_mtuha_diagnosis_id' =>27 , 'icd_block' =>  'N64'],
			['opd_mtuha_diagnosis_id' =>28 , 'icd_block' =>  'A15'],
			['opd_mtuha_diagnosis_id' =>28 , 'icd_block' =>  'A16'],
			['opd_mtuha_diagnosis_id' =>28 , 'icd_block' =>  'A17'],
			['opd_mtuha_diagnosis_id' =>28 , 'icd_block' =>  'A18'],
			['opd_mtuha_diagnosis_id' =>28 , 'icd_block' =>  'A19'],
			['opd_mtuha_diagnosis_id' =>29 , 'icd_block' =>  'A30'],
			['opd_mtuha_diagnosis_id' =>30 , 'icd_block' =>  'B82'],
			['opd_mtuha_diagnosis_id' =>31 , 'icd_block' =>  'D60'],
			['opd_mtuha_diagnosis_id' =>31 , 'icd_block' =>  'D61'],
			['opd_mtuha_diagnosis_id' =>31 , 'icd_block' =>  'D62'],
			['opd_mtuha_diagnosis_id' =>31 , 'icd_block' =>  'D63'],
			['opd_mtuha_diagnosis_id' =>32 , 'icd_block' =>  'D64'],
			['opd_mtuha_diagnosis_id' =>33 , 'icd_block' =>  'D57'],
			['opd_mtuha_diagnosis_id' =>34 , 'icd_block' =>  'H65'],
			['opd_mtuha_diagnosis_id' =>34 , 'icd_block' =>  'H67'],
			['opd_mtuha_diagnosis_id' =>34 , 'icd_block' =>  'H68'],
			['opd_mtuha_diagnosis_id' =>34 , 'icd_block' =>  'H69'],
			['opd_mtuha_diagnosis_id' =>34 , 'icd_block' =>  'H70'],
			['opd_mtuha_diagnosis_id' =>34 , 'icd_block' =>  'H71'],
			['opd_mtuha_diagnosis_id' =>34 , 'icd_block' =>  'H72'],
			['opd_mtuha_diagnosis_id' =>34 , 'icd_block' =>  'H73'],
			['opd_mtuha_diagnosis_id' =>34 , 'icd_block' =>  'H74'],
			['opd_mtuha_diagnosis_id' =>34 , 'icd_block' =>  'H75'],
			['opd_mtuha_diagnosis_id' =>35 , 'icd_block' =>  'H66'],
			['opd_mtuha_diagnosis_id' =>36 , 'icd_block' =>  'H10'],
			['opd_mtuha_diagnosis_id' =>36 , 'icd_block' =>  'H11'],
			['opd_mtuha_diagnosis_id' =>36 , 'icd_block' =>  'H12'],
			['opd_mtuha_diagnosis_id' =>36 , 'icd_block' =>  'H13'],
			['opd_mtuha_diagnosis_id' =>37 , 'icd_block' =>  'H25'],
			['opd_mtuha_diagnosis_id' =>37 , 'icd_block' =>  'H26'],
			['opd_mtuha_diagnosis_id' =>37 , 'icd_block' =>  'H27'],
			['opd_mtuha_diagnosis_id' =>37 , 'icd_block' =>  'H28'],
			['opd_mtuha_diagnosis_id' =>38 , 'icd_block' =>  'S05'],
			['opd_mtuha_diagnosis_id' =>39 , 'icd_block' =>  'L08'],
			['opd_mtuha_diagnosis_id' =>40 , 'icd_block' =>  'B35'],
			['opd_mtuha_diagnosis_id' =>40 , 'icd_block' =>  'B36'],
			['opd_mtuha_diagnosis_id' =>40 , 'icd_block' =>  'B37'],
			['opd_mtuha_diagnosis_id' =>40 , 'icd_block' =>  'B38'],
			['opd_mtuha_diagnosis_id' =>40 , 'icd_block' =>  'B39'],
			['opd_mtuha_diagnosis_id' =>40 , 'icd_block' =>  'B40'],
			['opd_mtuha_diagnosis_id' =>40 , 'icd_block' =>  'B41'],
			['opd_mtuha_diagnosis_id' =>40 , 'icd_block' =>  'B42'],
			['opd_mtuha_diagnosis_id' =>40 , 'icd_block' =>  'B43'],
			['opd_mtuha_diagnosis_id' =>40 , 'icd_block' =>  'B44'],
			['opd_mtuha_diagnosis_id' =>40 , 'icd_block' =>  'B45'],
			['opd_mtuha_diagnosis_id' =>40 , 'icd_block' =>  'B46'],
			['opd_mtuha_diagnosis_id' =>40 , 'icd_block' =>  'B47'],
			['opd_mtuha_diagnosis_id' =>40 , 'icd_block' =>  'B48'],
			['opd_mtuha_diagnosis_id' =>41 , 'icd_block' =>  'L80'],
			['opd_mtuha_diagnosis_id' =>41 , 'icd_block' =>  'L81'],
			['opd_mtuha_diagnosis_id' =>41 , 'icd_block' =>  'L82'],
			['opd_mtuha_diagnosis_id' =>41 , 'icd_block' =>  'L83'],
			['opd_mtuha_diagnosis_id' =>41 , 'icd_block' =>  'L84'],
			['opd_mtuha_diagnosis_id' =>41 , 'icd_block' =>  'L85'],
			['opd_mtuha_diagnosis_id' =>41 , 'icd_block' =>  'L86'],
			['opd_mtuha_diagnosis_id' =>41 , 'icd_block' =>  'L87'],
			['opd_mtuha_diagnosis_id' =>41 , 'icd_block' =>  'L88'],
			['opd_mtuha_diagnosis_id' =>41 , 'icd_block' =>  'L89'],
			['opd_mtuha_diagnosis_id' =>41 , 'icd_block' =>  'L90'],
			['opd_mtuha_diagnosis_id' =>41 , 'icd_block' =>  'L91'],
			['opd_mtuha_diagnosis_id' =>41 , 'icd_block' =>  'L92'],
			['opd_mtuha_diagnosis_id' =>41 , 'icd_block' =>  'L93'],
			['opd_mtuha_diagnosis_id' =>41 , 'icd_block' =>  'L94'],
			['opd_mtuha_diagnosis_id' =>41 , 'icd_block' =>  'L95'],
			['opd_mtuha_diagnosis_id' =>41 , 'icd_block' =>  'L96'],
			['opd_mtuha_diagnosis_id' =>41 , 'icd_block' =>  'L97'],
			['opd_mtuha_diagnosis_id' =>41 , 'icd_block' =>  'L98'],
			['opd_mtuha_diagnosis_id' =>41 , 'icd_block' =>  'L99'],
			['opd_mtuha_diagnosis_id' =>42 , 'icd_block' =>  'B49'],
			['opd_mtuha_diagnosis_id' =>43 , 'icd_block' =>  'M80'],
			['opd_mtuha_diagnosis_id' =>43 , 'icd_block' =>  'M81'],
			['opd_mtuha_diagnosis_id' =>43 , 'icd_block' =>  'M82'],
			['opd_mtuha_diagnosis_id' =>43 , 'icd_block' =>  'M83'],
			['opd_mtuha_diagnosis_id' =>43 , 'icd_block' =>  'M84'],
			['opd_mtuha_diagnosis_id' =>43 , 'icd_block' =>  'M85'],
			['opd_mtuha_diagnosis_id' =>43 , 'icd_block' =>  'M86'],
			['opd_mtuha_diagnosis_id' =>43 , 'icd_block' =>  'M87'],
			['opd_mtuha_diagnosis_id' =>43 , 'icd_block' =>  'M88'],
			['opd_mtuha_diagnosis_id' =>43 , 'icd_block' =>  'M89'],
			['opd_mtuha_diagnosis_id' =>43 , 'icd_block' =>  'M90'],
			['opd_mtuha_diagnosis_id' =>43 , 'icd_block' =>  'M91'],
			['opd_mtuha_diagnosis_id' =>43 , 'icd_block' =>  'M92'],
			['opd_mtuha_diagnosis_id' =>43 , 'icd_block' =>  'M93'],
			['opd_mtuha_diagnosis_id' =>43 , 'icd_block' =>  'M94'],
			['opd_mtuha_diagnosis_id' =>44 , 'icd_block' =>  'P36'],
			['opd_mtuha_diagnosis_id' =>45 , 'icd_block' =>  'P07'],
			['opd_mtuha_diagnosis_id' =>46 , 'icd_block' =>  'P21'],
			['opd_mtuha_diagnosis_id' =>47 , 'icd_block' =>  'J18'],
			['opd_mtuha_diagnosis_id' =>48 , 'icd_block' =>  'J18'],
			['opd_mtuha_diagnosis_id' =>49 , 'icd_block' =>  'J00'],
			['opd_mtuha_diagnosis_id' =>49 , 'icd_block' =>  'J01'],
			['opd_mtuha_diagnosis_id' =>49 , 'icd_block' =>  'J02'],
			['opd_mtuha_diagnosis_id' =>49 , 'icd_block' =>  'J03'],
			['opd_mtuha_diagnosis_id' =>49 , 'icd_block' =>  'J04'],
			['opd_mtuha_diagnosis_id' =>49 , 'icd_block' =>  'J05'],
			['opd_mtuha_diagnosis_id' =>49 , 'icd_block' =>  'J06'],
			['opd_mtuha_diagnosis_id' =>50 , 'icd_block' =>  'G80'],
			['opd_mtuha_diagnosis_id' =>51 , 'icd_block' =>  'N30'],
			['opd_mtuha_diagnosis_id' =>51 , 'icd_block' =>  'N31'],
			['opd_mtuha_diagnosis_id' =>51 , 'icd_block' =>  'N32'],
			['opd_mtuha_diagnosis_id' =>51 , 'icd_block' =>  'N33'],
			['opd_mtuha_diagnosis_id' =>51 , 'icd_block' =>  'N34'],
			['opd_mtuha_diagnosis_id' =>51 , 'icd_block' =>  'N35'],
			['opd_mtuha_diagnosis_id' =>51 , 'icd_block' =>  'N36'],
			['opd_mtuha_diagnosis_id' =>51 , 'icd_block' =>  'N37'],
			['opd_mtuha_diagnosis_id' =>51 , 'icd_block' =>  'N38'],
			['opd_mtuha_diagnosis_id' =>51 , 'icd_block' =>  'N39'],
			['opd_mtuha_diagnosis_id' =>52 , 'icd_block' =>  'N80'],
			['opd_mtuha_diagnosis_id' =>52 , 'icd_block' =>  'N81'],
			['opd_mtuha_diagnosis_id' =>52 , 'icd_block' =>  'N82'],
			['opd_mtuha_diagnosis_id' =>52 , 'icd_block' =>  'N83'],
			['opd_mtuha_diagnosis_id' =>52 , 'icd_block' =>  'N84'],
			['opd_mtuha_diagnosis_id' =>52 , 'icd_block' =>  'N85'],
			['opd_mtuha_diagnosis_id' =>52 , 'icd_block' =>  'N86'],
			['opd_mtuha_diagnosis_id' =>52 , 'icd_block' =>  'N87'],
			['opd_mtuha_diagnosis_id' =>52 , 'icd_block' =>  'N88'],
			['opd_mtuha_diagnosis_id' =>52 , 'icd_block' =>  'N89'],
			['opd_mtuha_diagnosis_id' =>52 , 'icd_block' =>  'N90'],
			['opd_mtuha_diagnosis_id' =>52 , 'icd_block' =>  'N91'],
			['opd_mtuha_diagnosis_id' =>52 , 'icd_block' =>  'N92'],
			['opd_mtuha_diagnosis_id' =>52 , 'icd_block' =>  'N93'],
			['opd_mtuha_diagnosis_id' =>52 , 'icd_block' =>  'N94'],
			['opd_mtuha_diagnosis_id' =>52 , 'icd_block' =>  'N95'],
			['opd_mtuha_diagnosis_id' =>52 , 'icd_block' =>  'N96'],
			['opd_mtuha_diagnosis_id' =>52 , 'icd_block' =>  'N97'],
			['opd_mtuha_diagnosis_id' =>52 , 'icd_block' =>  'N98'],
			['opd_mtuha_diagnosis_id' =>53 , 'icd_block' =>  'E40'],
			['opd_mtuha_diagnosis_id' =>54 , 'icd_block' =>  'E41'],
			['opd_mtuha_diagnosis_id' =>55 , 'icd_block' =>  'E42'],
			['opd_mtuha_diagnosis_id' =>56 , 'icd_block' =>  'E44'],
			['opd_mtuha_diagnosis_id' =>57 , 'icd_block' =>  'E50'],
			['opd_mtuha_diagnosis_id' =>58 , 'icd_block' =>  'E46'],
			['opd_mtuha_diagnosis_id' =>59 , 'icd_block' =>  'K00'],
			['opd_mtuha_diagnosis_id' =>59 , 'icd_block' =>  'K01'],
			['opd_mtuha_diagnosis_id' =>59 , 'icd_block' =>  'K02'],
			['opd_mtuha_diagnosis_id' =>59 , 'icd_block' =>  'K03'],
			['opd_mtuha_diagnosis_id' =>59 , 'icd_block' =>  'K04'],
			['opd_mtuha_diagnosis_id' =>59 , 'icd_block' =>  'K06'],
			['opd_mtuha_diagnosis_id' =>59 , 'icd_block' =>  'K07'],
			['opd_mtuha_diagnosis_id' =>59 , 'icd_block' =>  'K09'],
			['opd_mtuha_diagnosis_id' =>59 , 'icd_block' =>  'K10'],
			['opd_mtuha_diagnosis_id' =>59 , 'icd_block' =>  'K11'],
			['opd_mtuha_diagnosis_id' =>59 , 'icd_block' =>  'K12'],
			['opd_mtuha_diagnosis_id' =>59 , 'icd_block' =>  'K13'],
			['opd_mtuha_diagnosis_id' =>59 , 'icd_block' =>  'K14'],
			['opd_mtuha_diagnosis_id' =>60 , 'icd_block' =>  'K05'],
			['opd_mtuha_diagnosis_id' =>61 , 'icd_block' =>  'S03'],
			['opd_mtuha_diagnosis_id' =>62 , 'icd_block' =>  'K08'],
			['opd_mtuha_diagnosis_id' =>63 , 'icd_block' =>  'T08'],
			['opd_mtuha_diagnosis_id' =>63 , 'icd_block' =>  'T09'],
			['opd_mtuha_diagnosis_id' =>63 , 'icd_block' =>  'T10'],
			['opd_mtuha_diagnosis_id' =>63 , 'icd_block' =>  'T11'],
			['opd_mtuha_diagnosis_id' =>63 , 'icd_block' =>  'T12'],
			['opd_mtuha_diagnosis_id' =>63 , 'icd_block' =>  'T13'],
			['opd_mtuha_diagnosis_id' =>63 , 'icd_block' =>  'T14'],
			['opd_mtuha_diagnosis_id' =>64 , 'icd_block' =>  'T20'],
			['opd_mtuha_diagnosis_id' =>64 , 'icd_block' =>  'T21'],
			['opd_mtuha_diagnosis_id' =>64 , 'icd_block' =>  'T22'],
			['opd_mtuha_diagnosis_id' =>64 , 'icd_block' =>  'T23'],
			['opd_mtuha_diagnosis_id' =>64 , 'icd_block' =>  'T24'],
			['opd_mtuha_diagnosis_id' =>64 , 'icd_block' =>  'T25'],
			['opd_mtuha_diagnosis_id' =>64 , 'icd_block' =>  'T26'],
			['opd_mtuha_diagnosis_id' =>64 , 'icd_block' =>  'T27'],
			['opd_mtuha_diagnosis_id' =>64 , 'icd_block' =>  'T28'],
			['opd_mtuha_diagnosis_id' =>64 , 'icd_block' =>  'T29'],
			['opd_mtuha_diagnosis_id' =>64 , 'icd_block' =>  'T30'],
			['opd_mtuha_diagnosis_id' =>64 , 'icd_block' =>  'T31'],
			['opd_mtuha_diagnosis_id' =>64 , 'icd_block' =>  'T32'],
			['opd_mtuha_diagnosis_id' =>65 , 'icd_block' =>  'T51'],
			['opd_mtuha_diagnosis_id' =>65 , 'icd_block' =>  'T52'],
			['opd_mtuha_diagnosis_id' =>65 , 'icd_block' =>  'T53'],
			['opd_mtuha_diagnosis_id' =>65 , 'icd_block' =>  'T54'],
			['opd_mtuha_diagnosis_id' =>65 , 'icd_block' =>  'T55'],
			['opd_mtuha_diagnosis_id' =>65 , 'icd_block' =>  'T56'],
			['opd_mtuha_diagnosis_id' =>65 , 'icd_block' =>  'T57'],
			['opd_mtuha_diagnosis_id' =>65 , 'icd_block' =>  'T58'],
			['opd_mtuha_diagnosis_id' =>65 , 'icd_block' =>  'T59'],
			['opd_mtuha_diagnosis_id' =>65 , 'icd_block' =>  'T60'],
			['opd_mtuha_diagnosis_id' =>65 , 'icd_block' =>  'T61'],
			['opd_mtuha_diagnosis_id' =>65 , 'icd_block' =>  'T62'],
			['opd_mtuha_diagnosis_id' =>65 , 'icd_block' =>  'T63'],
			['opd_mtuha_diagnosis_id' =>65 , 'icd_block' =>  'T64'],
			['opd_mtuha_diagnosis_id' =>65 , 'icd_block' =>  'T65'],
			['opd_mtuha_diagnosis_id' =>66 , 'icd_block' =>  'V89'],
			['opd_mtuha_diagnosis_id' =>67 , 'icd_block' =>  'O20'],
			['opd_mtuha_diagnosis_id' =>67 , 'icd_block' =>  'O21'],
			['opd_mtuha_diagnosis_id' =>67 , 'icd_block' =>  'O22'],
			['opd_mtuha_diagnosis_id' =>67 , 'icd_block' =>  'O23'],
			['opd_mtuha_diagnosis_id' =>67 , 'icd_block' =>  'O24'],
			['opd_mtuha_diagnosis_id' =>67 , 'icd_block' =>  'O25'],
			['opd_mtuha_diagnosis_id' =>67 , 'icd_block' =>  'O26'],
			['opd_mtuha_diagnosis_id' =>67 , 'icd_block' =>  'O27'],
			['opd_mtuha_diagnosis_id' =>67 , 'icd_block' =>  'O28'],
			['opd_mtuha_diagnosis_id' =>67 , 'icd_block' =>  'O29'],
			['opd_mtuha_diagnosis_id' =>68 , 'icd_block' =>  'O03'],
			['opd_mtuha_diagnosis_id' =>69 , 'icd_block' =>  'T63'],
			['opd_mtuha_diagnosis_id' =>70 , 'icd_block' =>  'W54'],
			['opd_mtuha_diagnosis_id' =>71 , 'icd_block' =>  'W54'],
			['opd_mtuha_diagnosis_id' =>72 , 'icd_block' =>  NULL],
			['opd_mtuha_diagnosis_id' =>73 , 'icd_block' =>  'N40'],
			['opd_mtuha_diagnosis_id' =>73 , 'icd_block' =>  'N41'],
			['opd_mtuha_diagnosis_id' =>73 , 'icd_block' =>  'N42'],
			['opd_mtuha_diagnosis_id' =>73 , 'icd_block' =>  'N43'],
			['opd_mtuha_diagnosis_id' =>73 , 'icd_block' =>  'N44'],
			['opd_mtuha_diagnosis_id' =>73 , 'icd_block' =>  'N45'],
			['opd_mtuha_diagnosis_id' =>73 , 'icd_block' =>  'N46'],
			['opd_mtuha_diagnosis_id' =>73 , 'icd_block' =>  'N47'],
			['opd_mtuha_diagnosis_id' =>73 , 'icd_block' =>  'N48'],
			['opd_mtuha_diagnosis_id' =>73 , 'icd_block' =>  'N49'],
			['opd_mtuha_diagnosis_id' =>73 , 'icd_block' =>  'N50'],
			['opd_mtuha_diagnosis_id' =>73 , 'icd_block' =>  'N51'],
			['opd_mtuha_diagnosis_id' =>73 , 'icd_block' =>  'K40'],
			['opd_mtuha_diagnosis_id' =>73 , 'icd_block' =>  'K41'],
			['opd_mtuha_diagnosis_id' =>73 , 'icd_block' =>  'K42'],
			['opd_mtuha_diagnosis_id' =>73 , 'icd_block' =>  'K43'],
			['opd_mtuha_diagnosis_id' =>73 , 'icd_block' =>  'K44'],
			['opd_mtuha_diagnosis_id' =>73 , 'icd_block' =>  'K45'],
			['opd_mtuha_diagnosis_id' =>73 , 'icd_block' =>  'K46'],
			['opd_mtuha_diagnosis_id' =>73 , 'icd_block' =>  'L03'],
			['opd_mtuha_diagnosis_id' =>73 , 'icd_block' =>  'E04'],
			['opd_mtuha_diagnosis_id' =>73 , 'icd_block' =>  'K56'],
			['opd_mtuha_diagnosis_id' =>73 , 'icd_block' =>  'K37'],
			['opd_mtuha_diagnosis_id' =>74 , 'icd_block' =>  'G40'],
			['opd_mtuha_diagnosis_id' =>75 , 'icd_block' =>  'F29'],
			['opd_mtuha_diagnosis_id' =>76 , 'icd_block' =>  'F40'],
			['opd_mtuha_diagnosis_id' =>76 , 'icd_block' =>  'F41'],
			['opd_mtuha_diagnosis_id' =>76 , 'icd_block' =>  'F42'],
			['opd_mtuha_diagnosis_id' =>76 , 'icd_block' =>  'F43'],
			['opd_mtuha_diagnosis_id' =>76 , 'icd_block' =>  'F44'],
			['opd_mtuha_diagnosis_id' =>76 , 'icd_block' =>  'F45'],
			['opd_mtuha_diagnosis_id' =>76 , 'icd_block' =>  'F46'],
			['opd_mtuha_diagnosis_id' =>76 , 'icd_block' =>  'F47'],
			['opd_mtuha_diagnosis_id' =>76 , 'icd_block' =>  'F48'],
			['opd_mtuha_diagnosis_id' =>77 , 'icd_block' =>  'F10'],
			['opd_mtuha_diagnosis_id' =>77 , 'icd_block' =>  'F11'],
			['opd_mtuha_diagnosis_id' =>77 , 'icd_block' =>  'F12'],
			['opd_mtuha_diagnosis_id' =>77 , 'icd_block' =>  'F13'],
			['opd_mtuha_diagnosis_id' =>77 , 'icd_block' =>  'F14'],
			['opd_mtuha_diagnosis_id' =>77 , 'icd_block' =>  'F15'],
			['opd_mtuha_diagnosis_id' =>77 , 'icd_block' =>  'F16'],
			['opd_mtuha_diagnosis_id' =>77 , 'icd_block' =>  'F17'],
			['opd_mtuha_diagnosis_id' =>77 , 'icd_block' =>  'F18'],
			['opd_mtuha_diagnosis_id' =>77 , 'icd_block' =>  'F19'],
			['opd_mtuha_diagnosis_id' =>78 , 'icd_block' =>  'I10'],
			['opd_mtuha_diagnosis_id' =>78 , 'icd_block' =>  'I11'],
			['opd_mtuha_diagnosis_id' =>78 , 'icd_block' =>  'I12'],
			['opd_mtuha_diagnosis_id' =>78 , 'icd_block' =>  'I13'],
			['opd_mtuha_diagnosis_id' =>78 , 'icd_block' =>  'I14'],
			['opd_mtuha_diagnosis_id' =>78 , 'icd_block' =>  'I15'],
			['opd_mtuha_diagnosis_id' =>79 , 'icd_block' =>  'I00'],
			['opd_mtuha_diagnosis_id' =>79 , 'icd_block' =>  'I01'],
			['opd_mtuha_diagnosis_id' =>79 , 'icd_block' =>  'I02'],
			['opd_mtuha_diagnosis_id' =>80 , 'icd_block' =>  'I20'],
			['opd_mtuha_diagnosis_id' =>80 , 'icd_block' =>  'I21'],
			['opd_mtuha_diagnosis_id' =>80 , 'icd_block' =>  'I22'],
			['opd_mtuha_diagnosis_id' =>80 , 'icd_block' =>  'I23'],
			['opd_mtuha_diagnosis_id' =>80 , 'icd_block' =>  'I24'],
			['opd_mtuha_diagnosis_id' =>80 , 'icd_block' =>  'I25'],
			['opd_mtuha_diagnosis_id' =>81 , 'icd_block' =>  'J45'],
			['opd_mtuha_diagnosis_id' =>82 , 'icd_block' =>  'K27'],
			['opd_mtuha_diagnosis_id' =>83 , 'icd_block' =>  'K90'],
			['opd_mtuha_diagnosis_id' =>83 , 'icd_block' =>  'K91'],
			['opd_mtuha_diagnosis_id' =>83 , 'icd_block' =>  'K92'],
			['opd_mtuha_diagnosis_id' =>83 , 'icd_block' =>  'K93'],
			['opd_mtuha_diagnosis_id' =>84 , 'icd_block' =>  'E10'],
			['opd_mtuha_diagnosis_id' =>84 , 'icd_block' =>  'E11'],
			['opd_mtuha_diagnosis_id' =>84 , 'icd_block' =>  'E12'],
			['opd_mtuha_diagnosis_id' =>84 , 'icd_block' =>  'E13'],
			['opd_mtuha_diagnosis_id' =>84 , 'icd_block' =>  'E14'],
			['opd_mtuha_diagnosis_id' =>85 , 'icd_block' =>  'M00'],
			['opd_mtuha_diagnosis_id' =>85 , 'icd_block' =>  'M01'],
			['opd_mtuha_diagnosis_id' =>85 , 'icd_block' =>  'M02'],
			['opd_mtuha_diagnosis_id' =>85 , 'icd_block' =>  'M03'],
			['opd_mtuha_diagnosis_id' =>85 , 'icd_block' =>  'M04'],
			['opd_mtuha_diagnosis_id' =>85 , 'icd_block' =>  'M05'],
			['opd_mtuha_diagnosis_id' =>85 , 'icd_block' =>  'M06'],
			['opd_mtuha_diagnosis_id' =>85 , 'icd_block' =>  'M07'],
			['opd_mtuha_diagnosis_id' =>85 , 'icd_block' =>  'M08'],
			['opd_mtuha_diagnosis_id' =>85 , 'icd_block' =>  'M09'],
			['opd_mtuha_diagnosis_id' =>85 , 'icd_block' =>  'M10'],
			['opd_mtuha_diagnosis_id' =>85 , 'icd_block' =>  'M11'],
			['opd_mtuha_diagnosis_id' =>85 , 'icd_block' =>  'M12'],
			['opd_mtuha_diagnosis_id' =>85 , 'icd_block' =>  'M13'],
			['opd_mtuha_diagnosis_id' =>85 , 'icd_block' =>  'M14'],
			['opd_mtuha_diagnosis_id' =>85 , 'icd_block' =>  'M15'],
			['opd_mtuha_diagnosis_id' =>85 , 'icd_block' =>  'M16'],
			['opd_mtuha_diagnosis_id' =>85 , 'icd_block' =>  'M17'],
			['opd_mtuha_diagnosis_id' =>85 , 'icd_block' =>  'M18'],
			['opd_mtuha_diagnosis_id' =>85 , 'icd_block' =>  'M19'],
			['opd_mtuha_diagnosis_id' =>85 , 'icd_block' =>  'M20'],
			['opd_mtuha_diagnosis_id' =>85 , 'icd_block' =>  'M21'],
			['opd_mtuha_diagnosis_id' =>85 , 'icd_block' =>  'M22'],
			['opd_mtuha_diagnosis_id' =>85 , 'icd_block' =>  'M23'],
			['opd_mtuha_diagnosis_id' =>85 , 'icd_block' =>  'M24'],
			['opd_mtuha_diagnosis_id' =>85 , 'icd_block' =>  'M25'],
			['opd_mtuha_diagnosis_id' =>86 , 'icd_block' =>  'E00'],
			['opd_mtuha_diagnosis_id' =>86 , 'icd_block' =>  'E01'],
			['opd_mtuha_diagnosis_id' =>86 , 'icd_block' =>  'E02'],
			['opd_mtuha_diagnosis_id' =>86 , 'icd_block' =>  'E03'],
			['opd_mtuha_diagnosis_id' =>86 , 'icd_block' =>  'E04'],
			['opd_mtuha_diagnosis_id' =>86 , 'icd_block' =>  'E05'],
			['opd_mtuha_diagnosis_id' =>86 , 'icd_block' =>  'E06'],
			['opd_mtuha_diagnosis_id' =>86 , 'icd_block' =>  'E07'],
			['opd_mtuha_diagnosis_id' =>87 , 'icd_block' =>  'C15'],
			['opd_mtuha_diagnosis_id' =>87 , 'icd_block' =>  'C16'],
			['opd_mtuha_diagnosis_id' =>87 , 'icd_block' =>  'C17'],
			['opd_mtuha_diagnosis_id' =>87 , 'icd_block' =>  'C18'],
			['opd_mtuha_diagnosis_id' =>87 , 'icd_block' =>  'C19'],
			['opd_mtuha_diagnosis_id' =>87 , 'icd_block' =>  'C20'],
			['opd_mtuha_diagnosis_id' =>87 , 'icd_block' =>  'C21'],
			['opd_mtuha_diagnosis_id' =>87 , 'icd_block' =>  'C22'],
			['opd_mtuha_diagnosis_id' =>87 , 'icd_block' =>  'C23'],
			['opd_mtuha_diagnosis_id' =>87 , 'icd_block' =>  'C24'],
			['opd_mtuha_diagnosis_id' =>87 , 'icd_block' =>  'C25'],
			['opd_mtuha_diagnosis_id' =>87 , 'icd_block' =>  'C26'],
			['opd_mtuha_diagnosis_id' =>87 , 'icd_block' =>  'C50'],
			['opd_mtuha_diagnosis_id' =>87 , 'icd_block' =>  'C51'],
			['opd_mtuha_diagnosis_id' =>87 , 'icd_block' =>  'C52'],
			['opd_mtuha_diagnosis_id' =>87 , 'icd_block' =>  'C53'],
			['opd_mtuha_diagnosis_id' =>87 , 'icd_block' =>  'C54'],
			['opd_mtuha_diagnosis_id' =>87 , 'icd_block' =>  'C55'],
			['opd_mtuha_diagnosis_id' =>87 , 'icd_block' =>  'C56'],
			['opd_mtuha_diagnosis_id' =>87 , 'icd_block' =>  'C57'],
			['opd_mtuha_diagnosis_id' =>87 , 'icd_block' =>  'C58'],
			['opd_mtuha_diagnosis_id' =>87 , 'icd_block' =>  'C60'],
			['opd_mtuha_diagnosis_id' =>87 , 'icd_block' =>  'C61'],
			['opd_mtuha_diagnosis_id' =>87 , 'icd_block' =>  'C62'],
			['opd_mtuha_diagnosis_id' =>87 , 'icd_block' =>  'C63'],
			['opd_mtuha_diagnosis_id' =>88 , 'icd_block' =>  NULL]			
		);
		
		$IPDMtuhaICDMapping = array(
			['ipd_mtuha_diagnosis_id' => 1 , 'icd_block' => 'G80'],
			['ipd_mtuha_diagnosis_id' => 1 , 'icd_block' => 'G81'],
			['ipd_mtuha_diagnosis_id' => 1 , 'icd_block' => 'G82'],
			['ipd_mtuha_diagnosis_id' => 1 , 'icd_block' => 'G83'],
			['ipd_mtuha_diagnosis_id' => 2 , 'icd_block' => 'A00'],
			['ipd_mtuha_diagnosis_id' => 3 , 'icd_block' => 'A09'],
			['ipd_mtuha_diagnosis_id' => 4 , 'icd_block' => 'B05'],
			['ipd_mtuha_diagnosis_id' => 5 , 'icd_block' => 'G03'],
			['ipd_mtuha_diagnosis_id' => 6 , 'icd_block' => 'A33'],
			['ipd_mtuha_diagnosis_id' => 7 , 'icd_block' => 'A20'],
			['ipd_mtuha_diagnosis_id' => 8 , 'icd_block' => 'A68'],
			['ipd_mtuha_diagnosis_id' => 9 , 'icd_block' => 'A01'],
			['ipd_mtuha_diagnosis_id' => 10 , 'icd_block' => 'A09'],
			['ipd_mtuha_diagnosis_id' => 11 , 'icd_block' => 'K52'],
			['ipd_mtuha_diagnosis_id' => 12 , 'icd_block' => 'O98.6'],
			['ipd_mtuha_diagnosis_id' => 13 , 'icd_block' => 'B54'],
			['ipd_mtuha_diagnosis_id' => 14 , 'icd_block' => 'B65'],
			['ipd_mtuha_diagnosis_id' => 15 , 'icd_block' => 'A54'],
			['ipd_mtuha_diagnosis_id' => 16 , 'icd_block' => 'A53'],
			['ipd_mtuha_diagnosis_id' => 17 , 'icd_block' => 'N70'],
			['ipd_mtuha_diagnosis_id' => 17 , 'icd_block' => 'N71'],
			['ipd_mtuha_diagnosis_id' => 17 , 'icd_block' => 'N72'],
			['ipd_mtuha_diagnosis_id' => 17 , 'icd_block' => 'N73'],
			['ipd_mtuha_diagnosis_id' => 17 , 'icd_block' => 'N74'],
			['ipd_mtuha_diagnosis_id' => 17 , 'icd_block' => 'N75'],
			['ipd_mtuha_diagnosis_id' => 17 , 'icd_block' => 'N76'],
			['ipd_mtuha_diagnosis_id' => 17 , 'icd_block' => 'N77'],
			['ipd_mtuha_diagnosis_id' => 18 , 'icd_block' => 'A64'],
			['ipd_mtuha_diagnosis_id' => 19 , 'icd_block' => 'O00'],
			['ipd_mtuha_diagnosis_id' => 20 , 'icd_block' => 'O01'],
			['ipd_mtuha_diagnosis_id' => 20 , 'icd_block' => 'O02'],
			['ipd_mtuha_diagnosis_id' => 20 , 'icd_block' => 'O03'],
			['ipd_mtuha_diagnosis_id' => 20 , 'icd_block' => 'O04'],
			['ipd_mtuha_diagnosis_id' => 20 , 'icd_block' => 'O05'],
			['ipd_mtuha_diagnosis_id' => 20 , 'icd_block' => 'O06'],
			['ipd_mtuha_diagnosis_id' => 20 , 'icd_block' => 'O07'],
			['ipd_mtuha_diagnosis_id' => 20 , 'icd_block' => 'O08'],
			['ipd_mtuha_diagnosis_id' => 21 , 'icd_block' => 'N80'],
			['ipd_mtuha_diagnosis_id' => 21 , 'icd_block' => 'N81'],
			['ipd_mtuha_diagnosis_id' => 21 , 'icd_block' => 'N82'],
			['ipd_mtuha_diagnosis_id' => 21 , 'icd_block' => 'N83'],
			['ipd_mtuha_diagnosis_id' => 21 , 'icd_block' => 'N84'],
			['ipd_mtuha_diagnosis_id' => 21 , 'icd_block' => 'N85'],
			['ipd_mtuha_diagnosis_id' => 21 , 'icd_block' => 'N86'],
			['ipd_mtuha_diagnosis_id' => 21 , 'icd_block' => 'N87'],
			['ipd_mtuha_diagnosis_id' => 21 , 'icd_block' => 'N88'],
			['ipd_mtuha_diagnosis_id' => 21 , 'icd_block' => 'N89'],
			['ipd_mtuha_diagnosis_id' => 21 , 'icd_block' => 'N90'],
			['ipd_mtuha_diagnosis_id' => 21 , 'icd_block' => 'N91'],
			['ipd_mtuha_diagnosis_id' => 21 , 'icd_block' => 'N92'],
			['ipd_mtuha_diagnosis_id' => 21 , 'icd_block' => 'N93'],
			['ipd_mtuha_diagnosis_id' => 21 , 'icd_block' => 'N94'],
			['ipd_mtuha_diagnosis_id' => 21 , 'icd_block' => 'N95'],
			['ipd_mtuha_diagnosis_id' => 21 , 'icd_block' => 'N96'],
			['ipd_mtuha_diagnosis_id' => 21 , 'icd_block' => 'N97'],
			['ipd_mtuha_diagnosis_id' => 21 , 'icd_block' => 'N98'],
			['ipd_mtuha_diagnosis_id' => 22 , 'icd_block' => 'A15'],
			['ipd_mtuha_diagnosis_id' => 22 , 'icd_block' => 'A16'],
			['ipd_mtuha_diagnosis_id' => 22 , 'icd_block' => 'A17'],
			['ipd_mtuha_diagnosis_id' => 22 , 'icd_block' => 'A18'],
			['ipd_mtuha_diagnosis_id' => 22 , 'icd_block' => 'A19'],
			['ipd_mtuha_diagnosis_id' => 23 , 'icd_block' => 'A30'],
			['ipd_mtuha_diagnosis_id' => 24 , 'icd_block' => 'E10'],
			['ipd_mtuha_diagnosis_id' => 24 , 'icd_block' => 'E11'],
			['ipd_mtuha_diagnosis_id' => 24 , 'icd_block' => 'E12'],
			['ipd_mtuha_diagnosis_id' => 24 , 'icd_block' => 'E13'],
			['ipd_mtuha_diagnosis_id' => 24 , 'icd_block' => 'E14'],
			['ipd_mtuha_diagnosis_id' => 25 , 'icd_block' => 'E40'],
			['ipd_mtuha_diagnosis_id' => 26 , 'icd_block' => 'E41'],
			['ipd_mtuha_diagnosis_id' => 27 , 'icd_block' => 'E42'],
			['ipd_mtuha_diagnosis_id' => 28 , 'icd_block' => 'E44'],
			['ipd_mtuha_diagnosis_id' => 29 , 'icd_block' => 'E46'],
			['ipd_mtuha_diagnosis_id' => 30 , 'icd_block' => 'E00'],
			['ipd_mtuha_diagnosis_id' => 30 , 'icd_block' => 'E01'],
			['ipd_mtuha_diagnosis_id' => 30 , 'icd_block' => 'E02'],
			['ipd_mtuha_diagnosis_id' => 30 , 'icd_block' => 'E03'],
			['ipd_mtuha_diagnosis_id' => 30 , 'icd_block' => 'E04'],
			['ipd_mtuha_diagnosis_id' => 30 , 'icd_block' => 'E05'],
			['ipd_mtuha_diagnosis_id' => 30 , 'icd_block' => 'E06'],
			['ipd_mtuha_diagnosis_id' => 30 , 'icd_block' => 'E07'],
			['ipd_mtuha_diagnosis_id' => 31 , 'icd_block' => 'D57'],
			['ipd_mtuha_diagnosis_id' => 32 , 'icd_block' => 'D70'],
			['ipd_mtuha_diagnosis_id' => 32 , 'icd_block' => 'D71'],
			['ipd_mtuha_diagnosis_id' => 32 , 'icd_block' => 'D72'],
			['ipd_mtuha_diagnosis_id' => 32 , 'icd_block' => 'D73'],
			['ipd_mtuha_diagnosis_id' => 32 , 'icd_block' => 'D74'],
			['ipd_mtuha_diagnosis_id' => 32 , 'icd_block' => 'D75'],
			['ipd_mtuha_diagnosis_id' => 32 , 'icd_block' => 'D76'],
			['ipd_mtuha_diagnosis_id' => 32 , 'icd_block' => 'D77'],
			['ipd_mtuha_diagnosis_id' => 33 , 'icd_block' => 'D60'],
			['ipd_mtuha_diagnosis_id' => 33 , 'icd_block' => 'D61'],
			['ipd_mtuha_diagnosis_id' => 33 , 'icd_block' => 'D62'],
			['ipd_mtuha_diagnosis_id' => 33 , 'icd_block' => 'D63'],
			['ipd_mtuha_diagnosis_id' => 34 , 'icd_block' => 'D64'],
			['ipd_mtuha_diagnosis_id' => 35 , 'icd_block' => 'F29'],
			['ipd_mtuha_diagnosis_id' => 36 , 'icd_block' => 'F40'],
			['ipd_mtuha_diagnosis_id' => 36 , 'icd_block' => 'F41'],
			['ipd_mtuha_diagnosis_id' => 36 , 'icd_block' => 'F42'],
			['ipd_mtuha_diagnosis_id' => 36 , 'icd_block' => 'F43'],
			['ipd_mtuha_diagnosis_id' => 36 , 'icd_block' => 'F44'],
			['ipd_mtuha_diagnosis_id' => 36 , 'icd_block' => 'F45'],
			['ipd_mtuha_diagnosis_id' => 36 , 'icd_block' => 'F46'],
			['ipd_mtuha_diagnosis_id' => 36 , 'icd_block' => 'F47'],
			['ipd_mtuha_diagnosis_id' => 36 , 'icd_block' => 'F48'],
			['ipd_mtuha_diagnosis_id' => 37 , 'icd_block' => 'F10'],
			['ipd_mtuha_diagnosis_id' => 37 , 'icd_block' => 'F11'],
			['ipd_mtuha_diagnosis_id' => 37 , 'icd_block' => 'F12'],
			['ipd_mtuha_diagnosis_id' => 37 , 'icd_block' => 'F13'],
			['ipd_mtuha_diagnosis_id' => 37 , 'icd_block' => 'F14'],
			['ipd_mtuha_diagnosis_id' => 37 , 'icd_block' => 'F15'],
			['ipd_mtuha_diagnosis_id' => 37 , 'icd_block' => 'F16'],
			['ipd_mtuha_diagnosis_id' => 37 , 'icd_block' => 'F17'],
			['ipd_mtuha_diagnosis_id' => 37 , 'icd_block' => 'F18'],
			['ipd_mtuha_diagnosis_id' => 37 , 'icd_block' => 'F19'],
			['ipd_mtuha_diagnosis_id' => 38 , 'icd_block' => 'G80'],
			['ipd_mtuha_diagnosis_id' => 39 , 'icd_block' => 'G40'],
			['ipd_mtuha_diagnosis_id' => 40 , 'icd_block' => 'H90'],
			['ipd_mtuha_diagnosis_id' => 40 , 'icd_block' => 'H91'],
			['ipd_mtuha_diagnosis_id' => 40 , 'icd_block' => 'H92'],
			['ipd_mtuha_diagnosis_id' => 40 , 'icd_block' => 'H93'],
			['ipd_mtuha_diagnosis_id' => 40 , 'icd_block' => 'H94'],
			['ipd_mtuha_diagnosis_id' => 40 , 'icd_block' => 'H95'],
			['ipd_mtuha_diagnosis_id' => 41 , 'icd_block' => 'H65'],
			['ipd_mtuha_diagnosis_id' => 41 , 'icd_block' => 'H67'],
			['ipd_mtuha_diagnosis_id' => 41 , 'icd_block' => 'H68'],
			['ipd_mtuha_diagnosis_id' => 41 , 'icd_block' => 'H69'],
			['ipd_mtuha_diagnosis_id' => 41 , 'icd_block' => 'H70'],
			['ipd_mtuha_diagnosis_id' => 41 , 'icd_block' => 'H71'],
			['ipd_mtuha_diagnosis_id' => 41 , 'icd_block' => 'H72'],
			['ipd_mtuha_diagnosis_id' => 41 , 'icd_block' => 'H73'],
			['ipd_mtuha_diagnosis_id' => 41 , 'icd_block' => 'H74'],
			['ipd_mtuha_diagnosis_id' => 41 , 'icd_block' => 'H75'],
			['ipd_mtuha_diagnosis_id' => 42 , 'icd_block' => 'H66'],
			['ipd_mtuha_diagnosis_id' => 43 , 'icd_block' => 'H10'],
			['ipd_mtuha_diagnosis_id' => 43 , 'icd_block' => 'H11'],
			['ipd_mtuha_diagnosis_id' => 43 , 'icd_block' => 'H12'],
			['ipd_mtuha_diagnosis_id' => 43 , 'icd_block' => 'H13'],
			['ipd_mtuha_diagnosis_id' => 44 , 'icd_block' => 'H25'],
			['ipd_mtuha_diagnosis_id' => 44 , 'icd_block' => 'H26'],
			['ipd_mtuha_diagnosis_id' => 44 , 'icd_block' => 'H27'],
			['ipd_mtuha_diagnosis_id' => 44 , 'icd_block' => 'H28'],
			['ipd_mtuha_diagnosis_id' => 45 , 'icd_block' => 'S05'],
			['ipd_mtuha_diagnosis_id' => 46 , 'icd_block' => 'I30'],
			['ipd_mtuha_diagnosis_id' => 46 , 'icd_block' => 'I31'],
			['ipd_mtuha_diagnosis_id' => 46 , 'icd_block' => 'I32'],
			['ipd_mtuha_diagnosis_id' => 46 , 'icd_block' => 'I33'],
			['ipd_mtuha_diagnosis_id' => 46 , 'icd_block' => 'I34'],
			['ipd_mtuha_diagnosis_id' => 46 , 'icd_block' => 'I35'],
			['ipd_mtuha_diagnosis_id' => 46 , 'icd_block' => 'I36'],
			['ipd_mtuha_diagnosis_id' => 46 , 'icd_block' => 'I37'],
			['ipd_mtuha_diagnosis_id' => 46 , 'icd_block' => 'I38'],
			['ipd_mtuha_diagnosis_id' => 46 , 'icd_block' => 'I39'],
			['ipd_mtuha_diagnosis_id' => 46 , 'icd_block' => 'I40'],
			['ipd_mtuha_diagnosis_id' => 46 , 'icd_block' => 'I41'],
			['ipd_mtuha_diagnosis_id' => 46 , 'icd_block' => 'I42'],
			['ipd_mtuha_diagnosis_id' => 46 , 'icd_block' => 'I43'],
			['ipd_mtuha_diagnosis_id' => 46 , 'icd_block' => 'I44'],
			['ipd_mtuha_diagnosis_id' => 46 , 'icd_block' => 'I45'],
			['ipd_mtuha_diagnosis_id' => 46 , 'icd_block' => 'I46'],
			['ipd_mtuha_diagnosis_id' => 46 , 'icd_block' => 'I47'],
			['ipd_mtuha_diagnosis_id' => 46 , 'icd_block' => 'I48'],
			['ipd_mtuha_diagnosis_id' => 46 , 'icd_block' => 'I49'],
			['ipd_mtuha_diagnosis_id' => 46 , 'icd_block' => 'I50'],
			['ipd_mtuha_diagnosis_id' => 46 , 'icd_block' => 'I51'],
			['ipd_mtuha_diagnosis_id' => 46 , 'icd_block' => 'I52'],
			['ipd_mtuha_diagnosis_id' => 47 , 'icd_block' => 'I10'],
			['ipd_mtuha_diagnosis_id' => 47 , 'icd_block' => 'I11'],
			['ipd_mtuha_diagnosis_id' => 47 , 'icd_block' => 'I12'],
			['ipd_mtuha_diagnosis_id' => 47 , 'icd_block' => 'I13'],
			['ipd_mtuha_diagnosis_id' => 47 , 'icd_block' => 'I14'],
			['ipd_mtuha_diagnosis_id' => 47 , 'icd_block' => 'I15'],
			['ipd_mtuha_diagnosis_id' => 48 , 'icd_block' => 'R50'],
			['ipd_mtuha_diagnosis_id' => 48 , 'icd_block' => 'R51'],
			['ipd_mtuha_diagnosis_id' => 48 , 'icd_block' => 'R52'],
			['ipd_mtuha_diagnosis_id' => 48 , 'icd_block' => 'R53'],
			['ipd_mtuha_diagnosis_id' => 48 , 'icd_block' => 'R54'],
			['ipd_mtuha_diagnosis_id' => 48 , 'icd_block' => 'R55'],
			['ipd_mtuha_diagnosis_id' => 48 , 'icd_block' => 'R56'],
			['ipd_mtuha_diagnosis_id' => 48 , 'icd_block' => 'R57'],
			['ipd_mtuha_diagnosis_id' => 48 , 'icd_block' => 'R58'],
			['ipd_mtuha_diagnosis_id' => 48 , 'icd_block' => 'R59'],
			['ipd_mtuha_diagnosis_id' => 48 , 'icd_block' => 'R60'],
			['ipd_mtuha_diagnosis_id' => 48 , 'icd_block' => 'R61'],
			['ipd_mtuha_diagnosis_id' => 48 , 'icd_block' => 'R62'],
			['ipd_mtuha_diagnosis_id' => 48 , 'icd_block' => 'R63'],
			['ipd_mtuha_diagnosis_id' => 48 , 'icd_block' => 'R64'],
			['ipd_mtuha_diagnosis_id' => 48 , 'icd_block' => 'R65'],
			['ipd_mtuha_diagnosis_id' => 48 , 'icd_block' => 'R66'],
			['ipd_mtuha_diagnosis_id' => 48 , 'icd_block' => 'R67'],
			['ipd_mtuha_diagnosis_id' => 48 , 'icd_block' => 'R68'],
			['ipd_mtuha_diagnosis_id' => 48 , 'icd_block' => 'R69'],
			['ipd_mtuha_diagnosis_id' => 49 , 'icd_block' => 'I20'],
			['ipd_mtuha_diagnosis_id' => 49 , 'icd_block' => 'I21'],
			['ipd_mtuha_diagnosis_id' => 49 , 'icd_block' => 'I22'],
			['ipd_mtuha_diagnosis_id' => 49 , 'icd_block' => 'I23'],
			['ipd_mtuha_diagnosis_id' => 49 , 'icd_block' => 'I24'],
			['ipd_mtuha_diagnosis_id' => 49 , 'icd_block' => 'I25'],
			['ipd_mtuha_diagnosis_id' => 50 , 'icd_block' => 'J45'],
			['ipd_mtuha_diagnosis_id' => 51 , 'icd_block' => 'J00'],
			['ipd_mtuha_diagnosis_id' => 51 , 'icd_block' => 'J01'],
			['ipd_mtuha_diagnosis_id' => 51 , 'icd_block' => 'J02'],
			['ipd_mtuha_diagnosis_id' => 51 , 'icd_block' => 'J03'],
			['ipd_mtuha_diagnosis_id' => 51 , 'icd_block' => 'J04'],
			['ipd_mtuha_diagnosis_id' => 51 , 'icd_block' => 'J05'],
			['ipd_mtuha_diagnosis_id' => 51 , 'icd_block' => 'J06'],
			['ipd_mtuha_diagnosis_id' => 52 , 'icd_block' => 'J18'],
			['ipd_mtuha_diagnosis_id' => 53 , 'icd_block' => 'J18'],
			['ipd_mtuha_diagnosis_id' => 54 , 'icd_block' => 'K27'],
			['ipd_mtuha_diagnosis_id' => 55 , 'icd_block' => 'K70'],
			['ipd_mtuha_diagnosis_id' => 55 , 'icd_block' => 'K71'],
			['ipd_mtuha_diagnosis_id' => 55 , 'icd_block' => 'K72'],
			['ipd_mtuha_diagnosis_id' => 55 , 'icd_block' => 'K73'],
			['ipd_mtuha_diagnosis_id' => 55 , 'icd_block' => 'K74'],
			['ipd_mtuha_diagnosis_id' => 55 , 'icd_block' => 'K75'],
			['ipd_mtuha_diagnosis_id' => 55 , 'icd_block' => 'K76'],
			['ipd_mtuha_diagnosis_id' => 55 , 'icd_block' => 'K77'],
			['ipd_mtuha_diagnosis_id' => 56 , 'icd_block' => 'K90'],
			['ipd_mtuha_diagnosis_id' => 56 , 'icd_block' => 'K91'],
			['ipd_mtuha_diagnosis_id' => 56 , 'icd_block' => 'K92'],
			['ipd_mtuha_diagnosis_id' => 56 , 'icd_block' => 'K93'],
			['ipd_mtuha_diagnosis_id' => 57 , 'icd_block' => 'N30'],
			['ipd_mtuha_diagnosis_id' => 57 , 'icd_block' => 'N31'],
			['ipd_mtuha_diagnosis_id' => 57 , 'icd_block' => 'N32'],
			['ipd_mtuha_diagnosis_id' => 57 , 'icd_block' => 'N33'],
			['ipd_mtuha_diagnosis_id' => 57 , 'icd_block' => 'N34'],
			['ipd_mtuha_diagnosis_id' => 57 , 'icd_block' => 'N35'],
			['ipd_mtuha_diagnosis_id' => 57 , 'icd_block' => 'N36'],
			['ipd_mtuha_diagnosis_id' => 57 , 'icd_block' => 'N37'],
			['ipd_mtuha_diagnosis_id' => 57 , 'icd_block' => 'N38'],
			['ipd_mtuha_diagnosis_id' => 57 , 'icd_block' => 'N39'],
			['ipd_mtuha_diagnosis_id' => 58 , 'icd_block' => 'N00'],
			['ipd_mtuha_diagnosis_id' => 58 , 'icd_block' => 'N01'],
			['ipd_mtuha_diagnosis_id' => 58 , 'icd_block' => 'N02'],
			['ipd_mtuha_diagnosis_id' => 58 , 'icd_block' => 'N03'],
			['ipd_mtuha_diagnosis_id' => 58 , 'icd_block' => 'N04'],
			['ipd_mtuha_diagnosis_id' => 58 , 'icd_block' => 'N05'],
			['ipd_mtuha_diagnosis_id' => 58 , 'icd_block' => 'N06'],
			['ipd_mtuha_diagnosis_id' => 58 , 'icd_block' => 'N07'],
			['ipd_mtuha_diagnosis_id' => 58 , 'icd_block' => 'N08'],
			['ipd_mtuha_diagnosis_id' => 59 , 'icd_block' => 'N00'],
			['ipd_mtuha_diagnosis_id' => 59 , 'icd_block' => 'N01'],
			['ipd_mtuha_diagnosis_id' => 59 , 'icd_block' => 'N02'],
			['ipd_mtuha_diagnosis_id' => 59 , 'icd_block' => 'N03'],
			['ipd_mtuha_diagnosis_id' => 59 , 'icd_block' => 'N04'],
			['ipd_mtuha_diagnosis_id' => 59 , 'icd_block' => 'N05'],
			['ipd_mtuha_diagnosis_id' => 59 , 'icd_block' => 'N06'],
			['ipd_mtuha_diagnosis_id' => 59 , 'icd_block' => 'N07'],
			['ipd_mtuha_diagnosis_id' => 59 , 'icd_block' => 'N08'],
			['ipd_mtuha_diagnosis_id' => 60 , 'icd_block' => 'N17'],
			['ipd_mtuha_diagnosis_id' => 60 , 'icd_block' => 'N18'],
			['ipd_mtuha_diagnosis_id' => 60 , 'icd_block' => 'N19'],
			['ipd_mtuha_diagnosis_id' => 61 , 'icd_block' => 'L00'],
			['ipd_mtuha_diagnosis_id' => 61 , 'icd_block' => 'L01'],
			['ipd_mtuha_diagnosis_id' => 61 , 'icd_block' => 'L02'],
			['ipd_mtuha_diagnosis_id' => 61 , 'icd_block' => 'L03'],
			['ipd_mtuha_diagnosis_id' => 61 , 'icd_block' => 'L04'],
			['ipd_mtuha_diagnosis_id' => 61 , 'icd_block' => 'L05'],
			['ipd_mtuha_diagnosis_id' => 61 , 'icd_block' => 'L06'],
			['ipd_mtuha_diagnosis_id' => 61 , 'icd_block' => 'L07'],
			['ipd_mtuha_diagnosis_id' => 61 , 'icd_block' => 'L08'],
			['ipd_mtuha_diagnosis_id' => 62 , 'icd_block' => 'L80'],
			['ipd_mtuha_diagnosis_id' => 62 , 'icd_block' => 'L81'],
			['ipd_mtuha_diagnosis_id' => 62 , 'icd_block' => 'L82'],
			['ipd_mtuha_diagnosis_id' => 62 , 'icd_block' => 'L83'],
			['ipd_mtuha_diagnosis_id' => 62 , 'icd_block' => 'L84'],
			['ipd_mtuha_diagnosis_id' => 62 , 'icd_block' => 'L85'],
			['ipd_mtuha_diagnosis_id' => 62 , 'icd_block' => 'L86'],
			['ipd_mtuha_diagnosis_id' => 62 , 'icd_block' => 'L87'],
			['ipd_mtuha_diagnosis_id' => 62 , 'icd_block' => 'L88'],
			['ipd_mtuha_diagnosis_id' => 62 , 'icd_block' => 'L89'],
			['ipd_mtuha_diagnosis_id' => 62 , 'icd_block' => 'L90'],
			['ipd_mtuha_diagnosis_id' => 62 , 'icd_block' => 'L91'],
			['ipd_mtuha_diagnosis_id' => 62 , 'icd_block' => 'L92'],
			['ipd_mtuha_diagnosis_id' => 62 , 'icd_block' => 'L93'],
			['ipd_mtuha_diagnosis_id' => 62 , 'icd_block' => 'L94'],
			['ipd_mtuha_diagnosis_id' => 62 , 'icd_block' => 'L95'],
			['ipd_mtuha_diagnosis_id' => 62 , 'icd_block' => 'L96'],
			['ipd_mtuha_diagnosis_id' => 62 , 'icd_block' => 'L97'],
			['ipd_mtuha_diagnosis_id' => 62 , 'icd_block' => 'L98'],
			['ipd_mtuha_diagnosis_id' => 62 , 'icd_block' => 'L99'],
			['ipd_mtuha_diagnosis_id' => 63 , 'icd_block' => 'M80'],
			['ipd_mtuha_diagnosis_id' => 63 , 'icd_block' => 'M81'],
			['ipd_mtuha_diagnosis_id' => 63 , 'icd_block' => 'M82'],
			['ipd_mtuha_diagnosis_id' => 63 , 'icd_block' => 'M83'],
			['ipd_mtuha_diagnosis_id' => 63 , 'icd_block' => 'M84'],
			['ipd_mtuha_diagnosis_id' => 63 , 'icd_block' => 'M85'],
			['ipd_mtuha_diagnosis_id' => 63 , 'icd_block' => 'M86'],
			['ipd_mtuha_diagnosis_id' => 63 , 'icd_block' => 'M87'],
			['ipd_mtuha_diagnosis_id' => 63 , 'icd_block' => 'M88'],
			['ipd_mtuha_diagnosis_id' => 63 , 'icd_block' => 'M89'],
			['ipd_mtuha_diagnosis_id' => 63 , 'icd_block' => 'M90'],
			['ipd_mtuha_diagnosis_id' => 63 , 'icd_block' => 'M91'],
			['ipd_mtuha_diagnosis_id' => 63 , 'icd_block' => 'M92'],
			['ipd_mtuha_diagnosis_id' => 63 , 'icd_block' => 'M93'],
			['ipd_mtuha_diagnosis_id' => 63 , 'icd_block' => 'M94'],
			['ipd_mtuha_diagnosis_id' => 64 , 'icd_block' => 'M00'],
			['ipd_mtuha_diagnosis_id' => 64 , 'icd_block' => 'M01'],
			['ipd_mtuha_diagnosis_id' => 64 , 'icd_block' => 'M02'],
			['ipd_mtuha_diagnosis_id' => 64 , 'icd_block' => 'M03'],
			['ipd_mtuha_diagnosis_id' => 64 , 'icd_block' => 'M04'],
			['ipd_mtuha_diagnosis_id' => 64 , 'icd_block' => 'M05'],
			['ipd_mtuha_diagnosis_id' => 64 , 'icd_block' => 'M06'],
			['ipd_mtuha_diagnosis_id' => 64 , 'icd_block' => 'M07'],
			['ipd_mtuha_diagnosis_id' => 64 , 'icd_block' => 'M08'],
			['ipd_mtuha_diagnosis_id' => 64 , 'icd_block' => 'M09'],
			['ipd_mtuha_diagnosis_id' => 64 , 'icd_block' => 'M10'],
			['ipd_mtuha_diagnosis_id' => 64 , 'icd_block' => 'M11'],
			['ipd_mtuha_diagnosis_id' => 64 , 'icd_block' => 'M12'],
			['ipd_mtuha_diagnosis_id' => 64 , 'icd_block' => 'M13'],
			['ipd_mtuha_diagnosis_id' => 64 , 'icd_block' => 'M14'],
			['ipd_mtuha_diagnosis_id' => 64 , 'icd_block' => 'M15'],
			['ipd_mtuha_diagnosis_id' => 64 , 'icd_block' => 'M16'],
			['ipd_mtuha_diagnosis_id' => 64 , 'icd_block' => 'M17'],
			['ipd_mtuha_diagnosis_id' => 64 , 'icd_block' => 'M18'],
			['ipd_mtuha_diagnosis_id' => 64 , 'icd_block' => 'M19'],
			['ipd_mtuha_diagnosis_id' => 64 , 'icd_block' => 'M20'],
			['ipd_mtuha_diagnosis_id' => 64 , 'icd_block' => 'M21'],
			['ipd_mtuha_diagnosis_id' => 64 , 'icd_block' => 'M22'],
			['ipd_mtuha_diagnosis_id' => 64 , 'icd_block' => 'M23'],
			['ipd_mtuha_diagnosis_id' => 64 , 'icd_block' => 'M24'],
			['ipd_mtuha_diagnosis_id' => 64 , 'icd_block' => 'M25'],
			['ipd_mtuha_diagnosis_id' => 65 , 'icd_block' => 'P07'],
			['ipd_mtuha_diagnosis_id' => 66 , 'icd_block' => 'P21'],
			['ipd_mtuha_diagnosis_id' => 67 , 'icd_block' => 'P36'],
			['ipd_mtuha_diagnosis_id' => 68 , 'icd_block' => 'V89'],
			['ipd_mtuha_diagnosis_id' => 69 , 'icd_block' => 'T08'],
			['ipd_mtuha_diagnosis_id' => 69 , 'icd_block' => 'T09'],
			['ipd_mtuha_diagnosis_id' => 69 , 'icd_block' => 'T10'],
			['ipd_mtuha_diagnosis_id' => 69 , 'icd_block' => 'T11'],
			['ipd_mtuha_diagnosis_id' => 69 , 'icd_block' => 'T12'],
			['ipd_mtuha_diagnosis_id' => 69 , 'icd_block' => 'T13'],
			['ipd_mtuha_diagnosis_id' => 69 , 'icd_block' => 'T14'],
			['ipd_mtuha_diagnosis_id' => 70 , 'icd_block' => 'T51'],
			['ipd_mtuha_diagnosis_id' => 70 , 'icd_block' => 'T52'],
			['ipd_mtuha_diagnosis_id' => 70 , 'icd_block' => 'T53'],
			['ipd_mtuha_diagnosis_id' => 70 , 'icd_block' => 'T54'],
			['ipd_mtuha_diagnosis_id' => 70 , 'icd_block' => 'T55'],
			['ipd_mtuha_diagnosis_id' => 70 , 'icd_block' => 'T56'],
			['ipd_mtuha_diagnosis_id' => 70 , 'icd_block' => 'T57'],
			['ipd_mtuha_diagnosis_id' => 70 , 'icd_block' => 'T58'],
			['ipd_mtuha_diagnosis_id' => 70 , 'icd_block' => 'T59'],
			['ipd_mtuha_diagnosis_id' => 70 , 'icd_block' => 'T60'],
			['ipd_mtuha_diagnosis_id' => 70 , 'icd_block' => 'T61'],
			['ipd_mtuha_diagnosis_id' => 70 , 'icd_block' => 'T62'],
			['ipd_mtuha_diagnosis_id' => 70 , 'icd_block' => 'T63'],
			['ipd_mtuha_diagnosis_id' => 70 , 'icd_block' => 'T64'],
			['ipd_mtuha_diagnosis_id' => 70 , 'icd_block' => 'T65'],
			['ipd_mtuha_diagnosis_id' => 71 , 'icd_block' => 'T20'],
			['ipd_mtuha_diagnosis_id' => 71 , 'icd_block' => 'T21'],
			['ipd_mtuha_diagnosis_id' => 71 , 'icd_block' => 'T22'],
			['ipd_mtuha_diagnosis_id' => 71 , 'icd_block' => 'T23'],
			['ipd_mtuha_diagnosis_id' => 71 , 'icd_block' => 'T24'],
			['ipd_mtuha_diagnosis_id' => 71 , 'icd_block' => 'T25'],
			['ipd_mtuha_diagnosis_id' => 71 , 'icd_block' => 'T26'],
			['ipd_mtuha_diagnosis_id' => 71 , 'icd_block' => 'T27'],
			['ipd_mtuha_diagnosis_id' => 71 , 'icd_block' => 'T28'],
			['ipd_mtuha_diagnosis_id' => 71 , 'icd_block' => 'T29'],
			['ipd_mtuha_diagnosis_id' => 71 , 'icd_block' => 'T30'],
			['ipd_mtuha_diagnosis_id' => 71 , 'icd_block' => 'T31'],
			['ipd_mtuha_diagnosis_id' => 71 , 'icd_block' => 'T32'],
			['ipd_mtuha_diagnosis_id' => 72 , 'icd_block' => 'W54'],
			['ipd_mtuha_diagnosis_id' => 73 , 'icd_block' => 'W54'],
			['ipd_mtuha_diagnosis_id' => 74 , 'icd_block' => 'B20'],
			['ipd_mtuha_diagnosis_id' => 74 , 'icd_block' => 'B21'],
			['ipd_mtuha_diagnosis_id' => 74 , 'icd_block' => 'B23'],
			['ipd_mtuha_diagnosis_id' => 74 , 'icd_block' => 'B24'],
			['ipd_mtuha_diagnosis_id' => 74 , 'icd_block' => 'B24'],
			['ipd_mtuha_diagnosis_id' => 75 , 'icd_block' => 'Q80'],
			['ipd_mtuha_diagnosis_id' => 75 , 'icd_block' => 'Q81'],
			['ipd_mtuha_diagnosis_id' => 75 , 'icd_block' => 'Q82'],
			['ipd_mtuha_diagnosis_id' => 75 , 'icd_block' => 'Q83'],
			['ipd_mtuha_diagnosis_id' => 75 , 'icd_block' => 'Q84'],
			['ipd_mtuha_diagnosis_id' => 75 , 'icd_block' => 'Q85'],
			['ipd_mtuha_diagnosis_id' => 75 , 'icd_block' => 'Q86'],
			['ipd_mtuha_diagnosis_id' => 75 , 'icd_block' => 'Q87'],
			['ipd_mtuha_diagnosis_id' => 75 , 'icd_block' => 'Q88'],
			['ipd_mtuha_diagnosis_id' => 75 , 'icd_block' => 'Q89'],
			['ipd_mtuha_diagnosis_id' => 76 , 'icd_block' => 'K70'],
			['ipd_mtuha_diagnosis_id' => 76 , 'icd_block' => 'K71'],
			['ipd_mtuha_diagnosis_id' => 76 , 'icd_block' => 'K72'],
			['ipd_mtuha_diagnosis_id' => 76 , 'icd_block' => 'K73'],
			['ipd_mtuha_diagnosis_id' => 76 , 'icd_block' => 'K74'],
			['ipd_mtuha_diagnosis_id' => 76 , 'icd_block' => 'K75'],
			['ipd_mtuha_diagnosis_id' => 76 , 'icd_block' => 'K76'],
			['ipd_mtuha_diagnosis_id' => 76 , 'icd_block' => 'K77'],
			['ipd_mtuha_diagnosis_id' => 77 , 'icd_block' => 'C15'],
			['ipd_mtuha_diagnosis_id' => 77 , 'icd_block' => 'C16'],
			['ipd_mtuha_diagnosis_id' => 77 , 'icd_block' => 'C17'],
			['ipd_mtuha_diagnosis_id' => 77 , 'icd_block' => 'C18'],
			['ipd_mtuha_diagnosis_id' => 77 , 'icd_block' => 'C19'],
			['ipd_mtuha_diagnosis_id' => 77 , 'icd_block' => 'C20'],
			['ipd_mtuha_diagnosis_id' => 77 , 'icd_block' => 'C21'],
			['ipd_mtuha_diagnosis_id' => 77 , 'icd_block' => 'C22'],
			['ipd_mtuha_diagnosis_id' => 77 , 'icd_block' => 'C23'],
			['ipd_mtuha_diagnosis_id' => 77 , 'icd_block' => 'C24'],
			['ipd_mtuha_diagnosis_id' => 77 , 'icd_block' => 'C25'],
			['ipd_mtuha_diagnosis_id' => 77 , 'icd_block' => 'C26'],
			['ipd_mtuha_diagnosis_id' => 77 , 'icd_block' => 'C50'],
			['ipd_mtuha_diagnosis_id' => 77 , 'icd_block' => 'C51'],
			['ipd_mtuha_diagnosis_id' => 77 , 'icd_block' => 'C52'],
			['ipd_mtuha_diagnosis_id' => 77 , 'icd_block' => 'C53'],
			['ipd_mtuha_diagnosis_id' => 77 , 'icd_block' => 'C54'],
			['ipd_mtuha_diagnosis_id' => 77 , 'icd_block' => 'C55'],
			['ipd_mtuha_diagnosis_id' => 77 , 'icd_block' => 'C56'],
			['ipd_mtuha_diagnosis_id' => 77 , 'icd_block' => 'C57'],
			['ipd_mtuha_diagnosis_id' => 77 , 'icd_block' => 'C58'],
			['ipd_mtuha_diagnosis_id' => 77 , 'icd_block' => 'C60'],
			['ipd_mtuha_diagnosis_id' => 77 , 'icd_block' => 'C61'],
			['ipd_mtuha_diagnosis_id' => 77 , 'icd_block' => 'C62'],
			['ipd_mtuha_diagnosis_id' => 77 , 'icd_block' => 'C63'],
			// ['ipd_mtuha_diagnosis_id' => 78 , 'icd_block' => 'B65'],
			['ipd_mtuha_diagnosis_id' => 78 , 'icd_block' => 'B66'],
			['ipd_mtuha_diagnosis_id' => 78 , 'icd_block' => 'B67'],
			['ipd_mtuha_diagnosis_id' => 78 , 'icd_block' => 'B68'],
			['ipd_mtuha_diagnosis_id' => 78 , 'icd_block' => 'B69'],
			['ipd_mtuha_diagnosis_id' => 78 , 'icd_block' => 'B70'],
			['ipd_mtuha_diagnosis_id' => 78 , 'icd_block' => 'B71'],
			['ipd_mtuha_diagnosis_id' => 78 , 'icd_block' => 'B72'],
			['ipd_mtuha_diagnosis_id' => 78 , 'icd_block' => 'B73'],
			['ipd_mtuha_diagnosis_id' => 78 , 'icd_block' => 'B75'],
			['ipd_mtuha_diagnosis_id' => 78 , 'icd_block' => 'B76'],
			['ipd_mtuha_diagnosis_id' => 78 , 'icd_block' => 'B77'],
			['ipd_mtuha_diagnosis_id' => 78 , 'icd_block' => 'B78'],
			['ipd_mtuha_diagnosis_id' => 78 , 'icd_block' => 'B79'],
			['ipd_mtuha_diagnosis_id' => 78 , 'icd_block' => 'B80'],
			['ipd_mtuha_diagnosis_id' => 78 , 'icd_block' => 'B81'],
			['ipd_mtuha_diagnosis_id' => 78 , 'icd_block' => 'B82'],
			['ipd_mtuha_diagnosis_id' => 78 , 'icd_block' => 'B83'],
			['ipd_mtuha_diagnosis_id' => 79 , 'icd_block' => 'B74'],
			['ipd_mtuha_diagnosis_id' => 80 , 'icd_block' => 'A20'],
			['ipd_mtuha_diagnosis_id' => 80 , 'icd_block' => 'A21'],
			['ipd_mtuha_diagnosis_id' => 80 , 'icd_block' => 'A22'],
			['ipd_mtuha_diagnosis_id' => 80 , 'icd_block' => 'A23'],
			['ipd_mtuha_diagnosis_id' => 80 , 'icd_block' => 'A24'],
			['ipd_mtuha_diagnosis_id' => 80 , 'icd_block' => 'A25'],
			['ipd_mtuha_diagnosis_id' => 80 , 'icd_block' => 'A26'],
			['ipd_mtuha_diagnosis_id' => 80 , 'icd_block' => 'A27'],
			['ipd_mtuha_diagnosis_id' => 80 , 'icd_block' => 'A28'],
			['ipd_mtuha_diagnosis_id' => 81 , 'icd_block' => 'A90'],
			['ipd_mtuha_diagnosis_id' => 81 , 'icd_block' => 'A91'],
			['ipd_mtuha_diagnosis_id' => 81 , 'icd_block' => 'A92'],
			['ipd_mtuha_diagnosis_id' => 81 , 'icd_block' => 'A93'],
			['ipd_mtuha_diagnosis_id' => 81 , 'icd_block' => 'A94'],
			['ipd_mtuha_diagnosis_id' => 81 , 'icd_block' => 'A96'],
			['ipd_mtuha_diagnosis_id' => 81 , 'icd_block' => 'A97'],
			['ipd_mtuha_diagnosis_id' => 81 , 'icd_block' => 'A98'],
			['ipd_mtuha_diagnosis_id' => 81 , 'icd_block' => 'A99'],
		);
		
		foreach ($IPDMtuhaDiagnoses as $diagnosis)
		{
		 Tbl_ipd_mtuha_diagnosis::create($diagnosis);
		}
		foreach ($OPDMtuhaDiagnoses as $diagnosis)
		{
		 Tbl_opd_mtuha_diagnosis::create($diagnosis);
		}
		
		foreach ($OPDMtuhaICDMapping as $mapping)
			Tbl_opd_mtuha_icd_block::create($mapping);
		
		
		foreach ($IPDMtuhaICDMapping as $mapping)
			Tbl_ipd_mtuha_icd_block::create($mapping);
	
		
		
        foreach ($eyes as $eye){
            Tbl_eye_examination::create($eye);
        }

		foreach ($tribes as $tribe)
		{
		 Tbl_tribe::create($tribe);
		}

		foreach ($occupations as $occupation)
		{
		 Tbl_occupation::create($occupation);
		}

		foreach ($tbl_tb_treatment_types as $tbl_tb_treatment_type)
		{
			Tbl_tb_treatment_type::create($tbl_tb_treatment_type);
		}



		foreach ($tbl_transaction_types as $tbl_transaction_type)
		{
			Tbl_transaction_type::create($tbl_transaction_type);
		}

        foreach ($tbl_vaccination_registers as $tbl_vaccination_register)
        {
            Tbl_vaccination_register::create($tbl_vaccination_register);
        }

        foreach ($systems as $system)
        {
            Tbl_body_system::create($system);
        }

		// Loop through each pharmacy and other setups  above and create the record for them in the database
        

		foreach ($country_zones as  $country_zone)
        {
            Tbl_country_zone::create($country_zone);
        }
        foreach ($payment_methods as  $payment_method)
        {
            Tbl_payment_method::create($payment_method);
        }
		
		$zones=Tbl_country_zone::all();
			$country_zone_1=$zones[0]->id;
			$countries= array(
                ['country_name' => 'TANZANIA','country_zone_id' =>$country_zone_1],
				);
		foreach ($countries as  $country)
        {
			
            Tbl_country::create($country);
        }
		
				
		foreach ($bed_types as  $bedtype)
        {
            Tbl_bed_type::create($bedtype);
        } 

		foreach ($departments as $department)
        {
            Tbl_department::create($department);
        }  


		foreach ($admission_statuses as $admission_status)
        {
            Tbl_admission_status::create($admission_status);
        }
		
		// Loop through each admission  above and create the record for them in the database
        foreach ($payment_statuses as $payment_status)
        {
            Tbl_payment_status::create($payment_status);
        } 
		
		
		foreach ($payments_categories as $payments_category)
        {
            Tbl_payments_category::create($payments_category);
        }

        foreach ($sub_payment_categories as $sub_payment_category)
        {
            Tbl_pay_cat_sub_category::create($sub_payment_category);
        }

		foreach ($proffesionals as $proffesional)
        {
            Tbl_proffesional::create($proffesional);
        }
		
		foreach ($marital_statuses as $marital_status)
        {
            Tbl_marital::create($marital_status);
        }
		

		
		
		
		foreach ($facility_types as $facility_type)
        {
            Tbl_facility_type::create($facility_type);
        }
		
		
		foreach ($roles as $role)
        {
            Tbl_role::create($role);
        }
		
		foreach ($permissions as $permission)
        {
            Tbl_permission::create($permission);
        }
		
	
		
		foreach ($permission_roles as $permission_role)
        {
            Tbl_permission_role::create($permission_role);
        }
		
		
		foreach ($observation_types as $observation_type)
        {
            Tbl_observation_type::create($observation_type);
        }
		
		foreach ($observations_output_types as $observations_output_type)
        {
            Tbl_observations_output_type::create($observations_output_type);
        }
		
		foreach ($glyphicons as $glyphicon)
        {
            Tbl_glyphicon::create($glyphicon);
        }

        foreach ($lab_departments as $lab_department)
        {
            Tbl_sub_department::create($lab_department);
        }


        foreach ($equipment_statuses as $equipment_status)
        {
            Tbl_equipment_status::create($equipment_status);
        }


		
		 foreach ($tbl_store_types as $tbl_store_type)
        {
            Tbl_store_type::create($tbl_store_type);
        }
 
        foreach ($tbl_store_request_statuses as $tbl_store_request_status)
        {
            Tbl_store_request_status::create($tbl_store_request_status);
        }
         foreach ($tbl_exemption_statuses as $tbl_exemption_status)
        {
            Tbl_exemption_status::create($tbl_exemption_status);
        }

        foreach ($tbl_violence_types as $tbl_violence_type)
        {
            Tbl_violence_type::create($tbl_violence_type);
        }

        foreach ($item_category as $item_category)
        {
            Tbl_item_category::create($item_category);
        }
       foreach ($nursing_diagnosises as $nursing_diagnos)
        {
            Tbl_nursing_diagnosise::create($nursing_diagnos);
        }

		foreach ($vitals as $vital)
		{
			Tbl_vital::create($vital);
		}


		foreach ($teeth_arrangements as $teeth_arrangement)
		{
            Tbl_teeth_arrangement::create($teeth_arrangement);
		}


        foreach ($tbl_sample_statuses as $tbl_sample_statuse)
        {
            Tbl_sample_status::create($tbl_sample_statuse);
        }
	
		foreach($relationships as $relationship){
			Tbl_relationship::create($relationship);
		}
		
		
		
		$icd10 = __DIR__ . '\ICD10.csv';
		if (file_exists($icd10) && is_readable($icd10)){
			$header = null;
			$data = array();
			if (($handle = fopen($icd10, 'r')) !== false)
			{
				while (($row = fgetcsv($handle, 1000, ',')) !== false)
				{
					if (!$header)
						$header = $row;
					else
						$data[] = array_combine($header, $row);
				}
				fclose($handle);
				
				foreach($data as $icd10)
				{
					Tbl_diagnosis_description::create($icd10);
				}
			}
		}
			
		
		foreach ($council_types as $council_type)
        {
            Tbl_council_type::create($council_type);
        }
	
		$region = __DIR__ . '\REGIONS.csv';
		if (file_exists($region) && is_readable($region)){
			$header = null;
			$data = array();
			if (($handle = fopen($region, 'r')) !== false)
			{
				while (($row = fgetcsv($handle, 1000, ',')) !== false)
				{
					if (!$header)
						$header = $row;
					else
						$data[] = array_combine($header, $row);
				}
				fclose($handle);
				foreach($data as $region)
				{
					Tbl_region::create($region);
				}
			}
		}
		
		$council = __DIR__ . '\COUNCILS.csv';
		if (file_exists($council) && is_readable($council)){
			$header = null;
			$data = array();
			if (($handle = fopen($council, 'r')) !== false)
			{
				while (($row = fgetcsv($handle, 1000, ',')) !== false)
				{
					if (!$header)
						$header = $row;
					else
						$data[] = array_combine($header, $row);
				}
				fclose($handle);
				
				foreach($data as $council)
				{
					Tbl_council::create($council);
				}
			}
		}

		
		$residence = __DIR__ . '\RESIDENCES.csv';
		if (file_exists($residence) && is_readable($residence)){
			$header = null;
			$data = array();
			if (($handle = fopen($residence, 'r')) !== false)
			{
				while (($row = fgetcsv($handle, 1000, ',')) !== false)
				{
					if (!$header)
						$header = $row;
					else
						$data[] = array_combine($header, $row);
				}
				fclose($handle);
				
				foreach($data as $residence)
				{
					Tbl_residence::create($residence);
				}
			}
		}
		
		$msd_catalog = __DIR__ . '\MSD.csv';
		if (file_exists($msd_catalog) && is_readable($msd_catalog)){
			$header = null;
			$data = array();
			if (($handle = fopen($msd_catalog, 'r')) !== false)
			{
				while (($row = fgetcsv($handle, 1000, ',')) !== false)
				{
					if (!$header)
						$header = $row;
					else
						$data[] = array_combine($header, $row);
				}
				fclose($handle);
				
				foreach($data as $msd_catalog)
				{
					Tbl_item::create($msd_catalog);
				}
			}
		}

		$item_refference_id=Tbl_item::all();
		$item_maps = __DIR__ . '\MSD_Maps.csv';
		if (file_exists($item_maps) && is_readable($item_maps)){
			$header = null;
			$data = array();
			if (($handle = fopen($item_maps, 'r')) !== false)
			{
				  $i=0;
				while (($row = fgetcsv($handle, 1000, ',')) !== false)
				{
					
					if (!$header)
						$header = $row;
					else
						$data[] = array_combine($header, $row);
				}
				fclose($handle);
				
				foreach($data as $item_maps)
				{
					if($i!=882){
						$item_id=$item_refference_id[$i]->id;
						$dataArray=['item_id'=>$item_id,'item_code'=>$item_maps['item_code'],'item_category'=>$item_maps['item_category']];
						Tbl_item_type_mapped::create($dataArray);
						$i++;
					}
				}
			}
		}
	
		$facilities= array(
                ['facility_code' => '201701',
                'facility_name' => 'DODOMA HQ',
                'facility_type_id' =>7,
                'address' =>'P.O.BOX 1923,SOKOINE HOUSE',
                'region_id' => 2908,
                'council_id' =>2910,                
                'mobile_number' =>'0262322848',                
                'email' =>'ps@tamisemi.go.tz'],                
                     );
	
		foreach ($facilities as $facility)
        {
            Tbl_facility::create($facility);
        }
		
		
		$facility_id=1;
		$proffessional_id=11;
		$item_id_1=1;
		$item_id_2=2;
		$registrar_services= array(
                ['service_id' => $item_id_1,'facility_id' => $facility_id],
                ['service_id' => $item_id_2,'facility_id' => $facility_id],
               
		
                     );	
				 
		
		foreach ($registrar_services as $registrar_service)
        {
            Tbl_registrar_service::create($registrar_service);
        }
		
		Tbl_item_price::create(array("item_id"=>1,
										"price"=>1000,
										"facility_id"=>$facility_id,
										"sub_category_id"=>2,
										"startingFinancialYear"=>'2017-01-01',
										"endingFinancialYear"=>'2017-01-01'));
		Tbl_item_price::create(array("item_id"=>2,
										"price"=>3000,
										"facility_id"=>$facility_id,
										"sub_category_id"=>2,
										"startingFinancialYear"=>'2017-01-01',
										"endingFinancialYear"=>'2017-01-01'));
										
		  $users = array(
                ['name' => 'SUPER ADMIN',
				'email' => 'admin@tamisemi.go.tz',
				'password' => Hash::make('12345678'),
				'mobile_number' => '0652576368',
				'gender' => 'MALE',
				'facility_id' => $facility_id,
				'proffesionals_id' =>$proffessional_id
				],

           ['name' => 'record',
				'email' => 'record@tamisemi.go.tz',
				'password' => Hash::make('12345678'),
				'mobile_number' => '0652576368',
				'gender' => 'MALE',
				'facility_id' => $facility_id,
				'proffesionals_id' =>$proffessional_id
				],
           ['name' => 'cashier',
				'email' => 'cashier@tamisemi.go.tz',
				'password' => Hash::make('12345678'),
				'mobile_number' => '0652576368',
				'gender' => 'MALE',
				'facility_id' => $facility_id,
				'proffesionals_id' =>$proffessional_id
				],

               ['name' => 'doctor',
				'email' => 'doctor@tamisemi.go.tz',
				'password' => Hash::make('12345678'),
				'mobile_number' => '0652576368',
				'gender' => 'MALE',
				'facility_id' => $facility_id,
				'proffesionals_id' =>$proffessional_id
				],
           ['name' => 'nurse',
				'email' => 'nurse@tamisemi.go.tz',
				'password' => Hash::make('12345678'),
				'mobile_number' => '0652576368',
				'gender' => 'MALE',
				'facility_id' => $facility_id,
				'proffesionals_id' =>$proffessional_id
				],

                ['name' => 'lab',
				'email' => 'lab@tamisemi.go.tz',
				'password' => Hash::make('12345678'),
				'mobile_number' => '0652576368',
				'gender' => 'MALE',
				'facility_id' => $facility_id,
				'proffesionals_id' =>$proffessional_id
				],

                ['name' => 'anaethetic',
				'email' => 'anaethetic@tamisemi.go.tz',
				'password' => Hash::make('12345678'),
				'mobile_number' => '0652576368',
				'gender' => 'MALE',
				'facility_id' => $facility_id,
				'proffesionals_id' =>$proffessional_id
				],
                ['name' => 'dispenser',
				'email' => 'dispenser@tamisemi.go.tz',
				'password' => Hash::make('12345678'),
				'mobile_number' => '0652576368',
				'gender' => 'MALE',
				'facility_id' => $facility_id,
				'proffesionals_id' =>$proffessional_id
				],

               ['name' => 'pharmacy',
               'email' => 'pharmacy@tamisemi.go.tz',
               'password' => Hash::make('12345678'),
               'mobile_number' => '0652576368',
               'gender' => 'MALE',
               'facility_id' => $facility_id,
               'proffesionals_id' =>$proffessional_id
           ],

               ['name' => 'ctc',
               'email' => 'ctc@tamisemi.go.tz',
               'password' => Hash::make('12345678'),
               'mobile_number' => '0652576368',
               'gender' => 'MALE',
               'facility_id' => $facility_id,
               'proffesionals_id' =>$proffessional_id
              ],


        );
		
		foreach ($users as $user)
        {
            User::create($user);
        }
		
		$user_admin=User::where('email','admin@tamisemi.go.tz')->get();
		
		$permission_users = array(
		          ['permission_id' =>52,'user_id' =>$user_admin[0]->id,'grant' =>1],
		             );
			
		foreach ($permission_users as $permission_user)
        {
            Tbl_permission_user::create($permission_user);
        }
		
		
		$tracer_medicines = array(
			['id' => '1', 'item_name' => 'DPT + HepB/ HiB vaccine for immunization', 'status' => '0' ],
			['id' => '2', 'item_name' => 'Vidonge vya ALU vya kumeza', 'status' => '0' ],
			['id' => '3', 'item_name' => 'Amoxycillin/ Cotrimoxazole ya maji', 'status' => '0' ],
			['id' => '4', 'item_name' => 'Amoxycillin/ Cotrimoxazole ya vidonge', 'status' => '0' ],
			['id' => '5', 'item_name' => 'Dawa za vidonge za minyoo Albendazole au Mebendazole', 'status' => '0' ],
			['id' => '6', 'item_name' => 'Dawa ya kuhara ya kuchanganya na maji (ORS)', 'status' => '0' ],
			['id' => '7', 'item_name' => 'Sindano ya Ergometrine au Oxytocin au Vidonge vya Misoprostol', 'status' => '0' ],
			['id' => '8', 'item_name' => 'Dawa ya sindano ya Uzazi wa mapngo (Depo)', 'status' => '0' ],
			['id' => '9', 'item_name' => 'Maji ya mishipa (Dextrose 5% au Sodium chloride+Dextrose)', 'status' => '0' ],
			['id' => '10', 'item_name' => 'Mabomba ya sindano kwa matumizi ya mara moja(Disposable)', 'status' => '0' ],
			['id' => '11', 'item_name' => 'Kipimo cha malaria cha haraka (MRDT) au vifaa vya kupimia katika Hadubini', 'status' => '0' ],
			['id' => '12', 'item_name' => 'Magnesium Sulphate Sindano', 'status' => '0' ],
			['id' => '13', 'item_name' => 'Zinc sulphate Vidonge', 'status' => '0' ],
			['id' => '14', 'item_name' => 'Paracetamol Tablets', 'status' => '0' ],
			['id' => '15', 'item_name' => 'Benzyl Penicilline Injection', 'status' => '0' ],
			['id' => '16', 'item_name' => 'Ferrous + Folic Acid Tablets', 'status' => '0' ],
			['id' => '17', 'item_name' => 'Metronidazole Tablets', 'status' => '0' ],
			['id' => '18', 'item_name' => 'CombineOral Contraceptives', 'status' => '0' ],
			['id' => '19', 'item_name' => 'Catgut Sutures', 'status' => '0' ],
			['id' => '20', 'item_name' => 'Nevirapine Oral solution', 'status' => '0' ],
			['id' => '21', 'item_name' => 'Tenofovir 300mg+Lamivudine 300mg+Efavirenz 600mg Tablets', 'status' => '0' ],
			['id' => '22', 'item_name' => 'Efavirenz 600mg Tablets', 'status' => '0' ],
			['id' => '23', 'item_name' => 'Zidovudine 60mg+Lamivudine 30mg+Nevirapine 50mg Tablets', 'status' => '0' ],
			['id' => '24', 'item_name' => 'UNIGOLD HIV 1/2', 'status' => '0' ],
			['id' => '25', 'item_name' => 'Determine HIV 1&2 / Determine/SD Bioline', 'status' => '0' ],
			['id' => '26', 'item_name' => 'FACS Count reagent', 'status' => '0' ],
			['id' => '27', 'item_name' => 'DBS', 'status' => '0' ],
			['id' => '28', 'item_name' => 'RHZE Rifampicin 150mg/Isoniazide 75mg/Pyrazinamide/Etdambutol Tablets', 'status' => '0' ],
			['id' => '29', 'item_name' => 'RH Rifampicin 150MG/Isoniazide 75mg Tablets', 'status' => '0' ],
			['id' => '30', 'item_name' => 'Sulphadoxine+pyrimetdamine tablets', 'status' => '0' ]
		);
		
		foreach($tracer_medicines as $tracer)
			Tbl_tracer_medicine::create($tracer);
			
		echo'AHSANTE KWA KUMALIZA KUWEKA DATA ZA KUANZIA,TAFADHALI FUNGUA MFUMO SASA';
		
        Model::reguard();
    }
}