(function () {
    'use strict';
    angular.module('authApp')
    .controller('TraumaController', TraumaController);
        TraumaController.$inject=[ '$scope', '$http','$rootScope','$mdDialog','$state'];
        function TraumaController($scope,$http,$rootScope,$mdDialog,$state) {
            var user_id = $rootScope.currentUser.id;
            var facility_id = $rootScope.currentUser.facility_id;
		var patients = [];
        var diag = [];

                        $scope.getTraumaListSearched =function(searchKey){
            $http.post('/api/get-trauma-list',{searchKey:searchKey}).then(function (response) {
                $scope.trauma_lists = response.data;
                diag = response.data;
            }); 

                            return diag;

                        }
		$scope.searchTraumaPatients = function(searchKey){
            $http.post('/api/get-trauma-list', {searchKey:searchKey})
                .then(function (response) {
                    patients= response.data;
                });
            return patients;

		}
		/*$scope.searchTraumaPatients = function(searchKey){
            TraumaServices.getTraumaList(searchKey).then(function (response) {
				patients = response.data;
            });
            	return patients;
		}*/
            $scope.regex=/\s/g;
		$scope.consult = function(patient){
            $scope.selectedPatient = patient;
            //$scope.loadHistory(patient.client_id);
            $scope.cancel = function () {
                $mdDialog.hide();

            };
            $mdDialog.show({
                controller:TraumaController,
                scope: $scope,
                preserveScope: true,
                templateUrl: '/scripts/modules/trauma/views/trauma-form.html',
                clickOutsideToClose: false,
                fullscreen: true
            });
		}
		
        $scope.getTraumaList=function(){
			$http.post('/api/get-trauma-list').then(function (response) {
				$scope.trauma_lists = response.data;
            });      
		};
		

		$scope.cancel = function () {
			$mdDialog.hide();
		}

	        /*
	        trauma posting url
            chief complaints posting
             */
 $scope.saveChiefComplain=function(complain,patient){
     if (complain== undefined){
         swal({
             title: 'MISSING VALUE',
             html: 'Please write Chief Complaint for <u style="color: gray;font-family: Tunga">'+patient.surname+'</u>',
             type: 'warning',
             showCancelButton: false
         });
         return;
     }
     else{
         var records=[];
         records.push({client_id:patient.client_id,mass_casualty:complain.mass_casualty,complaint:complain.chief_complaint,dead_on_arrival:complain.dead_on_arrival,user_id:user_id,facility_id:facility_id});
         $http.post('/api/save-chief-complaint',records).then(function (response) {
                 swal({
                     title: 'CHIEF COMPLAINT POSTING',
                     html: response.data.text,
                     type: response.data.status,
                     showCancelButton: false
                 });
                 //$scope.loadHistory(patient.client_id);
                $('#chief_complaint').val('');

             },
             function(response){
                 swal({
                     title: 'CHIEF COMPLAINT POSTING',
                     html: 'Something seems to have gone wrong. Patient details not saved.',
                     type: 'warning',
                     showCancelButton: false
                 });
             }
         );
     }

 }

  $scope.oneAtATime = true;
                        $scope.templates = [{
                            name: 'Admission',
                            url: 'admission.html'
                        }, {
                            name: 'Internal Transfer',
                            url: 'internal.html'
                        }, {
                            name: 'External Referral',
                            url: 'referral.html'
                        }, {
                            name: 'Deceased',
                            url: 'deceased.html'
                        }];

$http.get('/api/getWards/' + facility_id).then(function(data) {

                                $scope.wards = data.data;

                            });
$http.get('/api/getSpecialClinics').then(function(data) {

                                $scope.clinics = data.data;

                            });

 $scope.saveAccidentLocation=function(location,patient){
     if (location== undefined){
         swal({
             title: 'MISSING VALUE',
             html: 'Fill Accident Location for <u style="color: gray;font-family: Tunga">'+patient.surname+'</u>',
             type: 'warning',
             showCancelButton: false
         });
         return;
     }
     else{
         var records=[];
         records.push({client_id:patient.client_id,
             ward:location.ward,street:location.street,
             common_name:location.common_name,road_name:location.road_name,
             house_namber:location.house_namber,head_of_household:location.head_of_household,
             user_id:user_id,facility_id:facility_id});
         $http.post('/api/save-accident-location',records).then(function (response) {
                 swal({
                     title: 'Accident Location',
                     html: response.data.text,
                     type: response.data.status,
                     showCancelButton: false
                 });
                 //$scope.loadHistory(patient.client_id);
                $('#ward').val('');
                $('#street').val('');
                $('#common_name').val('');
                $('#road_name').val('');
                $('#house_namber').val('');
                $('#head_of_household').val('');

             },
             function(response){
                 swal({
                     title: 'Accident Location',
                     html: 'Something seems to have gone wrong. Patient details not saved.',
                     type: 'warning',
                     showCancelButton: false
                 });
             }
         );
     }

 }

 //primary survey posting

 $scope.savePrimarySurvey=function(survey,patient){
     if (survey== undefined){
         swal({
             title: 'MISSING VALUE',
             html: 'Please Check from either of the given choice',
             type: 'warning',
             showCancelButton: false
         });
         return;
     }
     else{
         var records=[];
         records.push({
                 normal:survey.normal, angioedema:survey.angioedema,stridor:survey.stridor, voice_changes:survey.voice_changes,
             oral_airway_burns:survey.oral_airway_burns, tongue:survey.tongue,blood:survey.blood, secretion:survey.secretion,
             vomit:survey.vomit, foreign_body:survey.foreign_body,repostioning:survey.repostioning, suction:survey.suction,
             opa:survey.opa, npa:survey.npa,lma:survey.lma, bvm:survey.bvm,
             ett:survey.ett, none_needed:survey.none_needed,placed_before_arrival:survey.placed_before_arrival, placed_in_eu:survey.placed_in_eu,
             client_id:patient.client_id,
             user_id:user_id,facility_id:facility_id
         });
         $http.post('/api/save-airway-primary-survey',records).then(function (response) {
                 swal({
                     title: 'PRIMARY SURVEY POSTING',
                     html: response.data.text,
                     type: response.data.status,
                     showCancelButton: false
                 });
                 //$scope.loadHistory(patient.client_id);
                $('#chief_complaint').val('');

             },
             function(response){
                 swal({
                     title: 'PRIMARY SURVEY POSTING',
                     html: 'Something seems to have gone wrong. Patient details not saved.',
                     type: 'warning',
                     showCancelButton: false
                 });
             }
         );
     }

 }
 $scope.saveBreathingPrimarySurvey=function(survey,patient){
     if (survey== undefined){
         swal({
             title: 'MISSING VALUE',
             html: 'Please Check from either of the given choice',
             type: 'warning',
             showCancelButton: false
         });
         return;
     }
     else{

         var records=[];
         if(survey.normal==undefined ||survey.normal==false){
             records.push({
                 normal:survey.normal, nc:survey.nc, mask:survey.mask, nrb:survey.nrb,
                 bvm:survey.bvm, cpap_bipap:survey.cpap_bipap, ventilator:survey.ventilator, spontaneous_prespiration:survey.spontaneous_prespiration,
                 oxygen:survey.oxygen, chest_needle_left_size:survey.chest_needle_left_size, breath_sound:survey.breath_sound, breath_sound_description:survey.breath_sound_description,
                 chest_rise:survey.chest_rise, trachea:survey.trachea, chest_needle_left_depth:survey.chest_needle_left_depth, chest_needle_right_size:survey.chest_needle_right_size,
                 chest_needle_right_depth:survey.chest_needle_right_depth,
                 client_id:patient.client_id,
                 user_id:user_id,facility_id:facility_id
             });
         }
         if(survey.normal==true){
             records.push({
                 normal:survey.normal,
                 client_id:patient.client_id,
                 user_id:user_id,facility_id:facility_id
             });
         }




         $http.post('/api/save-breathing-primary-survey',records).then(function (response) {
                 swal({
                     title: 'PRIMARY SURVEY POSTING',
                     html: response.data.text,
                     type: response.data.status,
                     showCancelButton: false
                 });
                 //$scope.loadHistory(patient.client_id);
                $('#chief_complaint').val('');

             },
             function(response){
                 swal({
                     title: 'PRIMARY SURVEY POSTING',
                     html: 'Something seems to have gone wrong. Patient details not saved.',
                     type: 'warning',
                     showCancelButton: false
                 });
             }
         );
     }

 }
 $scope.saveCirculationPrimarySurvey=function(survey,patient){
     if (survey== undefined){
         swal({
             title: 'MISSING VALUE',
             html: 'Please Check from either of the given choice',
             type: 'warning',
             showCancelButton: false
         });
         return;
     }
     else{

         var records=[];
         if(survey.normal==undefined || survey.normal==false){
             records.push({
                 normal:survey.normal, warm:survey.warm, dry:survey.dry, cyanotic:survey.cyanotic,
                 pale:survey.pale, moist:survey.moist, cool:survey.cool, capillary_refill:survey.capillary_refill,
                 pulses:survey.pulses, asymmetric_value:survey.asymmetric_value, jvd:survey.jvd, bleeding_controlled:survey.bleeding_controlled,
                 iv_loc:survey.iv_loc, iv_size:survey.iv_size, cvl_loc:survey.cvl_loc, cvl_size:survey.cvl_size,
                 ic_loc:survey.ic_loc, ic_size:survey.ic_size, ivf:survey.ivf, ns:survey.ns,
                 lr:survey.lr, other:survey.other, blood_ordered:survey.blood_ordered, pelvic_blinder_placed:survey.pelvic_blinder_placed,
                 client_id:patient.client_id,
                 user_id:user_id,facility_id:facility_id
             });
         }
        else if(survey.normal==true){
             records.push({
                 normal:survey.normal,
                 client_id:patient.client_id,
                 user_id:user_id,facility_id:facility_id
             });
         }

 $http.post('/api/save-circulation-primary-survey',records).then(function (response) {
                 swal({
                     title: 'PRIMARY SURVEY POSTING',
                     html: response.data.text,
                     type: response.data.status,
                     showCancelButton: false
                 });
                 //$scope.loadHistory(patient.client_id);
                $('#chief_complaint').val('');

             },
             function(response){
                 swal({
                     title: 'PRIMARY SURVEY POSTING',
                     html: 'Something seems to have gone wrong. Patient details not saved.',
                     type: 'warning',
                     showCancelButton: false
                 });
             }
         );
     }

 }
            $scope.gcssummation='';
 $scope.gcsSum=function(gcs){
console.log(gcs);
 if(gcs){
     var gcs_e=gcs.gcs_e;
     var gcs_v=gcs.gcs_v;
     var gcs_m=gcs.gcs_m;

         $scope.gcssummation = -(-gcs_e- gcs_v - gcs_m);

 }


 }
 $scope.saveDisabilityPrimarySurvey=function(survey,patient,gcs){
     if (survey== undefined){
         swal({
             title: 'MISSING VALUE',
             html: 'Please Check from either of the given choice',
             type: 'warning',
             showCancelButton: false
         });
         return;
     }
     else{
         if(gcs){
             var gcs_e=gcs.gcs_e;
             var gcs_v=gcs.gcs_v;
             var gcs_m=gcs.gcs_m;
             if (gcs.gcs_e && gcs.gcs_v && gcs.gcs_m) {
                 $scope.gcssummation = -(-gcs_e- gcs_v - gcs_m);
             }
         }
         var records=[];
         if(survey.normal==undefined || survey.normal==false){
             records.push({
                 normal:survey.normal, blood_glucose_value:survey.blood_glucose_value, glucose:survey.glucose,
                 responsiveness_a:survey.responsiveness_a, responsiveness_v:survey.responsiveness_v,
                 responsiveness_p:survey.responsiveness_p, responsiveness_u:survey.responsiveness_u,
                 responsiveness_naloxone:survey.responsiveness_naloxone, gcs:$scope.gcssummation,
                 gcs_e:survey.gcs_e, gcs_v:survey.gcs_v,
                 gcs_m:survey.gcs_m, pupil_l_1:survey.pupil_l_1,
                 pupil_l_2:survey.pupil_l_2, pupil_r_1:survey.pupil_r_1,
                 pupil_r_2:survey.pupil_r_2, lue:survey.lue,
                 rue:survey.rue, lle:survey.lle,
                 rle:survey.rle, naloxone:survey.naloxone,
                   client_id:patient.client_id,
                 user_id:user_id,facility_id:facility_id
             });
         }
        else if(survey.normal==true){
             records.push({
                 normal:survey.normal,
                 client_id:patient.client_id,
                 user_id:user_id,facility_id:facility_id
             });
         }

 $http.post('/api/save-disability-primary-survey',records).then(function (response) {
                 swal({
                     title: 'PRIMARY SURVEY POSTING',
                     html: response.data.text,
                     type: response.data.status,
                     showCancelButton: false
                 });
                 //$scope.loadHistory(patient.client_id);
         $scope.gcssummation='';
         $('#chief_complaint').val('');

             },
             function(response){
                 swal({
                     title: 'PRIMARY SURVEY POSTING',
                     html: 'Something seems to have gone wrong. Patient details not saved.',
                     type: 'warning',
                     showCancelButton: false
                 });
             }
         );
     }

 }
 $scope.saveExposurePrimarySurvey=function(survey,patient){
     if (survey== undefined){
         swal({
             title: 'MISSING VALUE',
             html: 'Please Check from either of the given choice',
             type: 'warning',
             showCancelButton: false
         });
         return;
     }
     else{

         var records=[];
         if(survey.normal==undefined || survey.normal==false){
             records.push({
                 normal:survey.normal, exposed_completely:survey.exposed_completely,
                   client_id:patient.client_id,
                 user_id:user_id,facility_id:facility_id
             });
         }
        else if(survey.normal==true){
             records.push({
                 normal:survey.normal,
                 client_id:patient.client_id,
                 user_id:user_id,facility_id:facility_id
             });
         }

 $http.post('/api/save-exposure-primary-survey',records).then(function (response) {
                 swal({
                     title: 'PRIMARY SURVEY POSTING',
                     html: response.data.text,
                     type: response.data.status,
                     showCancelButton: false
                 });
                 //$scope.loadHistory(patient.client_id);
                $('#chief_complaint').val('');

             },
             function(response){
                 swal({
                     title: 'PRIMARY SURVEY POSTING',
                     html: 'Something seems to have gone wrong. Patient details not saved.',
                     type: 'warning',
                     showCancelButton: false
                 });
             }
         );
     }

 }
 $scope.saveFastPrimarySurvey=function(survey,patient){
     if (survey== undefined){
         swal({
             title: 'MISSING VALUE',
             html: 'Please Check from either of the given choice',
             type: 'warning',
             showCancelButton: false
         });
         return;
     }
     else{

         var records=[];
         if(survey.normal==undefined || survey.normal==false){
             records.push({
                 normal:survey.normal,
                 not_indicated:survey.not_indicated, peritoneum:survey.peritoneum,
                 free_fluid:survey.free_fluid, chest:survey.chest,
                 pericardial_effusion:survey.pericardial_effusion,
                 pneumothorax:survey.pneumothorax, pleural_fluid:survey.pleural_fluid,
                   client_id:patient.client_id,
                 user_id:user_id,facility_id:facility_id
             });
         }
        else if(survey.normal==true){
             records.push({
                 normal:survey.normal,
                 client_id:patient.client_id,
                 user_id:user_id,facility_id:facility_id
             });
         }

 $http.post('/api/save-fast-primary-survey',records).then(function (response) {
                 swal({
                     title: 'PRIMARY SURVEY POSTING',
                     html: response.data.text,
                     type: response.data.status,
                     showCancelButton: false
                 });
                 //$scope.loadHistory(patient.client_id);
                $('#chief_complaint').val('');

             },
             function(response){
                 swal({
                     title: 'PRIMARY SURVEY POSTING',
                     html: 'Something seems to have gone wrong. Patient details not saved.',
                     type: 'warning',
                     showCancelButton: false
                 });
             }
         );
     }

 }
 $scope.saveMedicalHistory=function(med,patient){
     if (med== undefined){
         swal({
             title: 'MISSING VALUE',
             html: 'Please Check from either of the given choice',
             type: 'warning',
             showCancelButton: false
         });
         return;
     }
     else{
         $scope.saved=0;

         var records=[];

             records.push({
                 medication:med.medication, past_medical_htn:med.past_medical_htn,
                 past_medical_diabetes:med.past_medical_diabetes, past_medical_copd:med.past_medical_copd,
                 past_medical_psychiatric:med.past_medical_psychiatric, past_medical_renal_disease:med.past_medical_renal_disease,
                 other_past_medical:med.other_past_medical, past_surgeries:med.past_surgeries,
                   client_id:patient.client_id,
                 user_id:user_id,facility_id:facility_id
             });


 $http.post('/api/save-past-medical-history',records).then(function (response) {
                 swal({
                     title: 'MEDICAL HISTORY',
                     html: response.data.text,
                     type: response.data.status,
                     showCancelButton: false
                 });
                 $('#medication').val("");
                 $('#past_medical_diabetes').val("");
                 $('#other_past_medical').val("");
                 $('#past_surgeries').val("");
                 //$scope.loadHistory(patient.client_id);
                $('#chief_complaint').val('');

             },
             function(response){
                 swal({
                     title: 'PRIMARY SURVEY POSTING',
                     html: 'Something seems to have gone wrong. Patient details not saved.',
                     type: 'warning',
                     showCancelButton: false
                 });
             }
         );
     }

 }
 $scope.saveAllergyHistory=function(med,patient){
     if (med== undefined){
         swal({
             title: 'MISSING VALUE',
             html: 'Please Check from either of the given choice',
             type: 'warning',
             showCancelButton: false
         });
         return;
     }
     else{
         $scope.saved=0;

         var records=[];

             records.push({
                 allergies:med.allergies, pregnant:med.pregnant,
                 vaccination_up_to_date:med.vaccination_up_to_date, vaccination_description:med.vaccination_description,
                 tobacco:med.tobacco, alcohol:med.alcohol,drugs:med.drugs, iv_drugs:med.iv_drugs,
                 last_menstrual_cycle:med.last_menstrual_cycle, last_menstrual_cycle_g:med.last_menstrual_cycle_g,last_menstrual_cycle_p:med.last_menstrual_cycle_p, save_home:med.save_home,
                   client_id:patient.client_id,
                 user_id:user_id,facility_id:facility_id
             });


 $http.post('/api/save-past-medical-allergy-history',records).then(function (response) {
                 swal({
                     title: 'MEDICAL HISTORY',
                     html: response.data.text,
                     type: response.data.status,
                     showCancelButton: false
                 });
                 $('#allergies').val("");
                 $('#vaccination_description').val("");
                 $('#last_menstrual_cycle').val("");
                 $('#last_menstrual_cycle_g').val("");
                 $('#last_menstrual_cycle_p').val("");
                 $('#save_home').val("");
                 //$scope.loadHistory(patient.client_id);
                $('#chief_complaint').val('');

             },
             function(response){
                 swal({
                     title: 'PRIMARY SURVEY POSTING',
                     html: 'Something seems to have gone wrong. Patient details not saved.',
                     type: 'warning',
                     showCancelButton: false
                 });
             }
         );
     }

 }
 $scope.saveHPI=function(hpi,patient){
     if (hpi== undefined){
         swal({
             title: 'MISSING VALUE',
             html: 'Please Check from either of the given choice',
             type: 'warning',
             showCancelButton: false
         });
         return;
     }
     else{
         $scope.saved=0;

         var records=[];

             records.push({
                 date_of_injury:hpi.date_of_injury, time_of_injury:hpi.time_of_injury,
                 place_of_injury:hpi.place_of_injury, prehospital_care:hpi.prehospital_care,
                 patient_activity_injury_time:hpi.patient_activity_injury_time,
                  client_id:patient.client_id,
                 user_id:user_id,facility_id:facility_id
             });


 $http.post('/api/save-trauma-hpi',records).then(function (response) {
                 swal({
                     title: 'MEDICAL HISTORY',
                     html: response.data.text,
                     type: response.data.status,
                     showCancelButton: false
                 });
                 $('#date_of_injury').val("");
                 $('#place_of_injury').val("");
                 $('#time_of_injury').val("");
                 $('#prehospital_care').val("");
                 $('#patient_activity_injury_time').val("");
                 //$scope.loadHistory(patient.client_id);


             },
             function(response){
                 swal({
                     title: 'PRIMARY SURVEY POSTING',
                     html: 'Something seems to have gone wrong. Patient details not saved.',
                     type: 'warning',
                     showCancelButton: false
                 });
             }
         );
     }

 }
 $scope.saveInjuryMechanism=function(injury,patient,opt){
     console.log(injury)
    var road_trafic_incident=false;
    var fall=false;
    var unknown=false;
    var none=false;
    var gunshot=false;
    var suffocation_choking_hanging=false;
    var sexual_assault=false;
     if (injury== undefined || opt==undefined){
         swal({
             title: 'MISSING VALUE',
             html: 'Please  fill both sides of Mechanism and Intent',
             type: 'warning',
             showCancelButton: false
         });
         return;
     }
     if ( opt==5 ) {
         gunshot = true
     }
     if ( opt==8 ) {
         suffocation_choking_hanging = true
     } if ( opt==6 ) {
         sexual_assault = true
     }
     if ( opt==13 ) {
         unknown = true
     }
     if ( opt==14 ) {
         none = true
     }
         if (opt==1 ){
         road_trafic_incident= true
     if ( injury.driver_passenger_pedestrian==undefined && injury.airbag_seatbelt_restraint==undefined){
         swal({
             title: 'MISSING VALUE',
             html: 'Please Check for Traffic Incidences Choice given',
             type: 'warning',
             showCancelButton: false
         });
         return;
     }

     }
     if ( opt==1 && injury.vehicle_involved==undefined ){
         swal({
             title: 'MISSING VALUE',
             html: 'Please Choose Vehicle Involved',
             type: 'warning',
             showCancelButton: false
         });
         return;
     }
     if ( opt==3 && injury.fall_from==undefined ){
          fall=true;
         swal({
             title: 'MISSING VALUE',
             html: 'Please specify Falling from ',
             type: 'warning',
             showCancelButton: false
         });
         return;
     }
     if (opt==3 && injury.hit_by_falling_object==undefined ){
         swal({
             title: 'MISSING VALUE',
             html: 'Please specify Hitting by falling Object ',
             type: 'warning',
             showCancelButton: false
         });
         return;
     } if (opt==4 && injury.stab_cut==undefined ){
         swal({
             title: 'MISSING VALUE',
             html: 'Please specify stab/cut ',
             type: 'warning',
             showCancelButton: false
         });
         return;
     }if (opt==7 && injury.other_bunt_force==undefined ){
         swal({
             title: 'MISSING VALUE',
             html: 'Please specify Other blunt force trauma(stuck/hit) ',
             type: 'warning',
             showCancelButton: false
         });
         return;
     }if (opt==9 && injury.drowning==undefined ){
         swal({
             title: 'MISSING VALUE',
             html: 'Please specify drowning ',
             type: 'warning',
             showCancelButton: false
         });
         return;
     } if (opt==11 && injury.burn_caused_by==undefined ){
         swal({
             title: 'MISSING VALUE',
             html: 'Please specify Burn Caused by',
             type: 'warning',
             showCancelButton: false
         });
         return;
     }if (opt==12 && injury.poisoning_toxic_exposure==undefined ){
         swal({
             title: 'MISSING VALUE',
             html: 'Please specify Poisoning/Toxic Exposure',
             type: 'warning',
             showCancelButton: false
         });
         return;
     }if ( injury.substance_six_hour_injury==undefined ){
         swal({
             title: 'MISSING VALUE',
             html: 'Please specify Substance use within 6 hours of injury',
             type: 'warning',
             showCancelButton: false
         });
         return;
     }if ( injury.loss_of_consciousness==undefined || injury.trauma==undefined ){
         swal({
             title: 'MISSING VALUE',
             html: 'Details of intent is MUST fill',
             type: 'warning',
             showCancelButton: false
         });
         return;
     }
     else{
         $scope.saved=0;

         var records=[];

             records.push({
                 other_vehicle_involved:injury.other_vehicle_involved,
                 driver_passenger_pedestrian:injury.driver_passenger_pedestrian,
                 airbag_seatbelt_restraint:injury.airbag_seatbelt_restraint,

                 fall:fall,
                 none_road_trafic_incident:none,
                 road_traffic_acident:road_trafic_incident, extricated:injury.extricated,
                 vehicle_involved:injury.vehicle_involved, ejected:injury.ejected,
                 crashed_with:injury.crashed_with, fall_from:injury.fall_from,
                 hit_by_falling_object:injury.hit_by_falling_object, stab_cut:injury.stab_cut,
                 gunshot:gunshot, sexual_assault:sexual_assault,
                 other_bunt_force:injury.other_bunt_force, suffocation_choking_hanging:suffocation_choking_hanging,
                 drowning:injury.drowning, flotation_device:injury.flotation_device,
                 burn_caused_by:injury.burn_caused_by, poisoning_toxic_exposure:injury.poisoning_toxic_exposure,
                 unknown:unknown, other:injury.other,
                 intent:injury.intent, assaulted_by:injury.assaulted_by,
                 hours_since_last_meal:injury.hours_since_last_meal, substance_six_hour_injury:injury.substance_six_hour_injury,
                 other_substance:injury.other_substance, loss_of_consciousness:injury.loss_of_consciousness,
                 trauma:injury.trauma,

                  client_id:patient.client_id,
                 user_id:user_id,facility_id:facility_id
             });


 $http.post('/api/save-trauma-injury-mechanism',records).then(function (response) {
                 swal({
                     title: 'INJURY MECHANISM',
                     html: response.data.text,
                     type: response.data.status,
                     showCancelButton: false
                 });
                 $('#date_of_injury').val("");
                 $('#place_of_injury').val("");
                 $('#time_of_injury').val("");
                 $('#prehospital_care').val("");
                 $('#patient_activity_injury_time').val("");
                 //$scope.loadHistory(patient.client_id);


             },
             function(response){
                 swal({
                     title: 'INJURY MECHANISM',
                     html: 'Something seems to have gone wrong. Patient details not saved.',
                     type: 'warning',
                     showCancelButton: false
                 });
             }
         );
     }

 }
 $scope.savePhysicalExam=function(physEx,patient){
     if (physEx== undefined){
         swal({
             title: 'MISSING VALUE',
             html: 'Please Check from either of the given choice',
             type: 'warning',
             showCancelButton: false
         });
         return;
     }
     else{

         $scope.saved=0;

         var records=[];

if (physEx.general_normal==undefined || physEx.general_normal==false) {
    if (physEx.general_normal==undefined || physEx.general_normal==false) {
        if (physEx.general_examination==undefined ) {
            swal({
                title: 'FILL ALL FIELDS',
                html: 'Please  <i style="color: red">click check box for NML or write something or write NONE in field area</i> ',
                type: 'warning',
                showCancelButton: false
            });
            return;
        }
    }
    if (physEx.heent_normal==undefined || physEx.heent_normal==false) {
        if (physEx.heent_examination==undefined ) {
            swal({
                title: 'FILL ALL FIELDS',
                html: 'Please  <i style="color: red">click check box for NML or write something or write NONE in field area</i> ',
                type: 'warning',
                showCancelButton: false
            });
            return;
        }
    }
    if (physEx.neuro_normal==undefined || physEx.neuro_normal==false) {
        if (physEx.neuro_examination==undefined ) {
            swal({
                title: 'FILL ALL FIELDS',
                html: 'Please  <i style="color: red">click check box for NML or write something or write NONE in field area</i> ',
                type: 'warning',
                showCancelButton: false
            });
            return;
        }
    }
    if (physEx.neck_normal==undefined || physEx.neck_normal==false) {
        if (physEx.neck_examination==undefined ) {
            swal({
                title: 'FILL ALL FIELDS',
                html: 'Please  <i style="color: red">click check box for NML or write something or write NONE in field area</i> ',
                type: 'warning',
                showCancelButton: false
            });
            return;
        }
    }
    if (physEx.pulm_chest_normal==undefined || physEx.pulm_chest_normal==false) {
        if (physEx.pulm_chest_examination==undefined ) {
            swal({
                title: 'FILL ALL FIELDS',
                html: 'Please  <i style="color: red">click check box for NML or write something or write NONE in field area</i> ',
                type: 'warning',
                showCancelButton: false
            });
            return;
        }
    }
    if (physEx.cardiac_normal==undefined || physEx.cardiac_normal==false) {
        if (physEx.cardiac_examination==undefined ) {
            swal({
                title: 'FILL ALL FIELDS',
                html: 'Please  <i style="color: red">click check box for NML or write something or write NONE in field area</i> ',
                type: 'warning',
                showCancelButton: false
            });
            return;
        }
    }
    if (physEx.abdominal_normal==undefined || physEx.abdominal_normal==false) {
        if (physEx.abdominal_examination==undefined ) {
            swal({
                title: 'FILL ALL FIELDS',
                html: 'Please  <i style="color: red">click check box for NML or write something or write NONE in field area</i> ',
                type: 'warning',
                showCancelButton: false
            });
            return;
        }
    }

    if (physEx.gu_rectal_normal==undefined || physEx.gu_rectal_normal==false) {
        if (physEx.gu_rectal_examination==undefined ) {
            swal({
                title: 'FILL ALL FIELDS',
                html: 'Please  <i style="color: red">click check box for NML or write something or write NONE in field area</i> ',
                type: 'warning',
                showCancelButton: false
            });
            return;
        }
    }
    if (physEx.back_normal==undefined || physEx.back_normal==false) {
        if (physEx.back_examination==undefined ) {
            swal({
                title: 'FILL ALL FIELDS',
                html: 'Please  <i style="color: red">click check box for NML or write something or write NONE in field area</i> ',
                type: 'warning',
                showCancelButton: false
            });
            return;
        }
    }
    if (physEx.msk_skin_normal==undefined || physEx.msk_skin_normal==false) {
        if (physEx.msk_skin_examination==undefined ) {
            swal({
                title: 'FILL ALL FIELDS',
                html: 'Please  <i style="color: red">click check box for NML or write something or write NONE in field area</i> ',
                type: 'warning',
                showCancelButton: false
            });
            return;
        }
    }
    if (physEx.msk_skin_normal==undefined || physEx.msk_skin_normal==false) {
        if (physEx.msk_skin_examination==undefined ) {
            swal({
                title: 'FILL ALL FIELDS',
                html: 'Please  <i style="color: red">click check box for NML or write something or write NONE in field area</i> ',
                type: 'warning',
                showCancelButton: false
            });
            return;
        }
    }
    records.push({
        general_normal:physEx.general_normal, general_examination:physEx.general_examination,
        heent_normal:physEx.heent_normal, heent_examination:physEx.heent_examination,
        neuro_normal:physEx.neuro_normal, neuro_examination:physEx.neuro_examination,
        neck_normal:physEx.neck_normal, neck_examination:physEx.neck_examination,
        pulm_chest_normal:physEx.pulm_chest_normal, pulm_chest_examination:physEx.pulm_chest_examination,
        cardiac_normal:physEx.cardiac_normal, cardiac_examination:physEx.cardiac_examination,
        abdominal_normal:physEx.abdominal_normal, abdominal_examination:physEx.abdominal_examination,
        gu_rectal_normal:physEx.abdominal_normal, gu_rectal_examination:physEx.gu_rectal_examination,
        back_normal:physEx.back_normal, back_examination:physEx.back_examination,
        msk_skin_normal:physEx.msk_skin_normal, msk_skin_examination:physEx.msk_skin_examination,
        client_id:patient.client_id,
        user_id:user_id,facility_id:facility_id
    });

}
else{
    records.push({
        general_normal:physEx.general_normal,
        client_id:patient.client_id,
        user_id:user_id,facility_id:facility_id
    });
}


 $http.post('/api/save-trauma-physical-exam',records).then(function (response) {
                 swal({
                     title: 'PHYSICAL EXAMINATION',
                     html: response.data.text,
                     type: response.data.status,
                     showCancelButton: false
                 });
         $('#general_examination').val("");
         $('#neck_examination').val("");
         $('#back_examination').val("");
         $('#abdominal_examination').val("");
         $('#heent_examination').val("");
         $('#neuro_examination').val("");
         $('#pulm_chest_examination').val("");
         $('#cardiac_examination').val("");
         $('#gu_rectal_examination').val("");
         $('#msk_skin_examination').val("");
                 //$scope.loadHistory(patient.client_id);


             },
             function(response){
                 swal({
                     title: 'INJURY MECHANISM',
                     html: 'Something seems to have gone wrong. Patient details not saved.',
                     type: 'warning',
                     showCancelButton: false
                 });
             }
         );
     }

 }
 $scope.saveLabResult=function(inv,patient){
     if (inv== undefined){
         swal({
             title: 'MISSING VALUE',
             html: 'Please Check from either of the given choice',
             type: 'warning',
             showCancelButton: false
         });
         return;
     }
     else{
if(inv.hgb){
    var hb=inv.hgb.replace(/,/g, '').replace(/[A-Za-z]/g, '');
    console.log(hb);
if(hb< 1 || hb>25 ){
    swal({
        title: 'HB RANGE',
        html: 'HB range is <b style="color: green">(1-25 )g/dl</b><p></p>' +
        'NOT <b style="color: red">'+inv.hgb+'</b>',
        type: 'warning',
        showCancelButton: false
    });
    return;
}

}
if(inv.blood_given=='yes' && inv.blood_type==undefined){

    swal({
        title: 'BLOOD TYPE MISSING',
        html: '<b style="color: red">Please indicate or choose blood type GIVEN</b>',
        type: 'warning',
        showCancelButton: false
    });
    return;


}
         $scope.saved=0;

         var records=[];

    records.push({
        upt:inv.upt, hgb:inv.hgb,
        result_pending:inv.result_pending,
        blood_type:inv.blood_type, other_lab_result:inv.other_lab_result,
        client_id:patient.client_id,
        user_id:user_id,facility_id:facility_id
    });



 $http.post('/api/save-trauma-lab-result',records).then(function (response) {
                 swal({
                     title: 'INVESTIGATION',
                     html: response.data.text,
                     type: response.data.status,
                     showCancelButton: false
                 });
         $('#other_lab_result').val("");
         $('#hgb').val("");

                 //$scope.loadHistory(patient.client_id);


             },
             function(response){
                 swal({
                     title: 'INJURY MECHANISM',
                     html: 'Something seems to have gone wrong. Patient details not saved.',
                     type: 'warning',
                     showCancelButton: false
                 });
             }
         );
     }

 }
 $scope.saveImagingResult=function(inv,patient){
     if (inv== undefined){
         swal({
             title: 'MISSING VALUE',
             html: 'Please Check from either of the given choice',
             type: 'warning',
             showCancelButton: false
         });
         return;
     }
     else{

         $scope.saved=0;

         var records=[];

    records.push({
        pneumothorax:inv.pneumothorax, pleural_fluid:inv.pleural_fluid,
        palmonary_opacity:inv.palmonary_opacity, c_spine_fracture:inv.c_spine_fracture,
        extremity_fracture:inv.extremity_fracture, pelvic_fracture:inv.pelvic_fracture,
        wide_mediastinum:inv.wide_mediastinum, other_image_result:inv.other_image_result,
        rib_fracture:inv.rib_fracture,
        blood_type:inv.blood_type, other_lab_result:inv.other_lab_result,
        client_id:patient.client_id,
        user_id:user_id,facility_id:facility_id
    });



 $http.post('/api/save-trauma-image-result',records).then(function (response) {
                 swal({
                     title: 'INVESTIGATION',
                     html: response.data.text,
                     type: response.data.status,
                     showCancelButton: false
                 });
         $('#pneumothorax').val("");
         $('#pleural_fluid').val("");
         $('#palmonary_opacity').val("");
         $('#c_spine_fracture').val("");
         $('#extremity_fracture').val("");
         $('#pelvic_fracture').val("");
         $('#wide_mediastinum').val("");
         $('#other_image_result').val("");

                 //$scope.loadHistory(patient.client_id);


             },
             function(response){
                 swal({
                     title: 'INJURY MECHANISM',
                     html: 'Something seems to have gone wrong. Patient details not saved.',
                     type: 'warning',
                     showCancelButton: false
                 });
             }
         );
     }

 }
 $scope.saveProcedure=function(inv,patient){
     if (inv== undefined){
         swal({
             title: 'MISSING VALUE',
             html: 'Please Check from either of the given choice',
             type: 'warning',
             showCancelButton: false
         });
         return;
     }
     else{

         $scope.saved=0;

         var records=[];

    records.push({
        cricothyroidotomy:inv.cricothyroidotomy, intubation:inv.intubation,
        chest_tube:inv.chest_tube, pericardiocentesis:inv.pericardiocentesis,
        open_thoracotomy:inv.open_thoracotomy, splinting:inv.splinting,
        fracture_red_pelvic_stab:inv.fracture_red_pelvic_stab, foreign_body_removal:inv.foreign_body_removal,
        simple_complex_lac_repair:inv.simple_complex_lac_repair, other_procedure:inv.other_procedure,
        client_id:patient.client_id,
        user_id:user_id,facility_id:facility_id
    });



 $http.post('/api/save-trauma-procedures',records).then(function (response) {
                 swal({
                     title: 'ADDITIONAL INVERVENTIONS',
                     html: response.data.text,
                     type: response.data.status,
                     showCancelButton: false
                 });
         $('#cricothyroidotomy').val("");
         $('#intubation').val("");
         $('#chest_tube').val("");
         $('#pericardiocentesis').val("");
         $('#open_thoracotomy').val("");
         $('#splinting').val("");
         $('#fracture_red_pelvic_stab').val("");
         $('#foreign_body_removal').val("");
         $('#simple_complex_lac_repair').val("");
         $('#other_procedure').val("");


                 //$scope.loadHistory(patient.client_id);


             },
             function(response){
                 swal({
                     title: 'INJURY MECHANISM',
                     html: 'Something seems to have gone wrong. Patient details not saved.',
                     type: 'warning',
                     showCancelButton: false
                 });
             }
         );
     }

 }
 $scope.saveFluidandMedication=function(inv,patient){
     if (inv== undefined){
         swal({
             title: 'MISSING VALUE',
             html: 'Please Check from either of the given choice',
             type: 'warning',
             showCancelButton: false
         });
         return;
     }
     else{
         if (inv.blood_products==true){
             if (inv.whole_blood==undefined && inv.prbc==undefined && inv.ffp==undefined && inv.platelets==undefined ) {
                 swal({
                     title: 'BLOOD PRODUCT MISSING',
                     html: 'Please indicate the <b style="color: red">blood product Given</b>',
                     type: 'warning',
                     showCancelButton: false
                 });
                 swal({
                     title: 'BLOOD PRODUCT MISSING',
                     html: 'Please indicate the <b style="color: red">blood product Given</b>',
                     type: 'warning',
                     showCancelButton: false
                 });
                 return;
             }
         }
         if (inv.oploid_analgesia==undefined || inv.other_analgesia==undefined || inv.sedation_paralytics==undefined || inv.antibiotics==undefined || inv.tetanus==undefined || inv.other==undefined ) {
             swal({
                 title: 'FILL ALL FIELDS',
                 html: 'Please Fill all fields or write <b style="color: red">NONE </b> if not applicable',
                 type: 'warning',
                 showCancelButton: false
             });

             return;
         }
         $scope.saved=0;

         var records=[];

    records.push({
        ivf:inv.ivf, ns:inv.ns,
        lr:inv.lr, other_fluid:inv.other_fluid,
        blood_products:inv.blood_products, whole_blood:inv.whole_blood,
        prbc:inv.prbc, ffp:inv.ffp,
        platelets:inv.platelets, oploid_analgesia:inv.oploid_analgesia,
        other_analgesia:inv.other_analgesia, sedation_paralytics:inv.sedation_paralytics,
        antibiotics:inv.antibiotics, tetanus:inv.tetanus,
        other:inv.other,
        prehospital_care:inv.prehospital_care,
        client_id:patient.client_id,
        user_id:user_id,facility_id:facility_id
    });



 $http.post('/api/save-trauma-fluid-medication',records).then(function (response) {
                 swal({
                     title: 'ADDITIONAL INVERVENTIONS',
                     html: response.data.text,
                     type: response.data.status,
                     showCancelButton: false
                 });
         $('#ivf').val("");
         $('#ns').val("");
         $('#lr').val("");
         $('#blood_products').val(false);
         $('#whole_blood').val("");
         $('#prbc').val("");
         $('#ffp').val("");
         $('#platelets').val("");
         $('#oploid_analgesia').val("");
         $('#other_fluid').val("");
         $('#other_analgesia').val("");
         $('#sedation_paralytics').val("");
         $('#antibiotics').val("");
         $('#tetanus').val("");
         $('#other').val("");



                 //$scope.loadHistory(patient.client_id);


             },
             function(response){
                 swal({
                     title: 'ADD. INTERVENTION',
                     html: 'Something seems to have gone wrong. Patient details not saved.',
                     type: 'warning',
                     showCancelButton: false
                 });
             }
         );
     }

 }
 $scope.saveAssesementandPlan=function(inv,patient){
     if (inv== undefined){
         swal({
             title: 'MISSING VALUE',
             html: 'Please Check from either of the given choice',
             type: 'warning',
             showCancelButton: false
         });
         return;
     }
     else{

         $scope.saved=0;

         var records=[];

    records.push({
        summary:inv.summary, consultant:inv.consultant,
        other_differential:inv.other_differential, imaging:inv.imaging,
        medication:inv.medication, intervention:inv.intervention,
        consults:inv.consults, other_plan:inv.other_plan,
        client_id:patient.client_id,
        user_id:user_id,facility_id:facility_id
    });



 $http.post('/api/save-trauma-client-assesment',{records:records,diagnosis:$scope.diagnosisTemp}).then(function (response) {
                 swal({
                     title: 'ADDITIONAL INVERVENTIONS',
                     html: response.data.text,
                     type: response.data.status,
                     showCancelButton: false
                 });
         $scope.diagnosisTemp=[];
         $('#summary').val("");
         $('#consultant').val("");
         $('#other_differential').val("");
         $('#imaging').val("");
         $('#medication').val("");
         $('#intervention').val("");
         $('#consults').val("");
         $('#other_plan').val("");

                 //$scope.loadHistory(patient.client_id);


             },
             function(response){
                 swal({
                     title: 'ASSESMENT',
                     html: 'Something seems to have gone wrong. Patient details not saved.',
                     type: 'warning',
                     showCancelButton: false
                 });
             }
         );
     }

 }
 $scope.saveReAssesementandPlan=function(inv,patient){
     if (inv== undefined){
         swal({
             title: 'MISSING VALUE',
             html: 'Please Check from either of the given choice',
             type: 'warning',
             showCancelButton: false
         });
         return;
     }  if (inv.condition_change== "Changed" && inv.condition_change_description==undefined || inv.condition_change_description==""){
         swal({
             title: 'MISSING VALUE',
             html: 'Please Write Description of Condition Change',
             type: 'warning',
             showCancelButton: false
         });
         return;
     } if (inv.re_assement_at== undefined || inv.re_assement_temp==undefined || inv.re_assement_bp==undefined || inv.re_assement_rr==undefined || inv.re_assement_spo2==undefined || inv.condition_change==undefined){
         swal({
             title: 'MISSING VALUE',
             html: 'Please fill all fields',
             type: 'warning',
             showCancelButton: false
         });
         return;
     }
     else{

         $scope.saved=0;

         var records=[];

    records.push({
        re_assement_at:inv.re_assement_at, re_assement_temp:inv.re_assement_temp,
        re_assement_bp:inv.re_assement_bp, re_assement_rr:inv.re_assement_rr,
        re_assement_spo2:inv.re_assement_spo2, condition_change:inv.condition_change,
        condition_change_description:inv.condition_change_description,
        client_id:patient.client_id,
        user_id:user_id,facility_id:facility_id
    });



 $http.post('/api/save-trauma-client-re-assesment',records).then(function (response) {
                 swal({
                     title: 'ADDITIONAL INVERVENTIONS',
                     html: response.data.text,
                     type: response.data.status,
                     showCancelButton: false
                 });
         $scope.diagnosisTemp=[];
         $('#re_assement_at').val("");
         $('#re_assement_temp').val("");
         $('#re_assement_bp').val("");
         $('#re_assement_rr').val("");
         $('#condition_change_description').val("");
         $('#re_assement_spo2').val("");

                 //$scope.loadHistory(patient.client_id);


             },
             function(response){
                 swal({
                     title: 'ASSESMENT',
                     html: 'Something seems to have gone wrong. Patient details not saved.',
                     type: 'warning',
                     showCancelButton: false
                 });
             }
         );
     }

 }
 $scope.saveDisposition=function(inv,patient,opt){
     console.log(inv,opt)
     if (inv== undefined && opt==undefined){
         swal({
             title: 'MISSING VALUE',
             html: 'Please Check from either of the given choice',
             type: 'warning',
             showCancelButton: false
         });
         return;
     }
     if(opt !=4){
         // if (inv.checklist_completed ==undefined || inv.ed_departure_date== undefined || inv.ed_departure_time==undefined ){
         //     swal({
         //         title: 'MISSING VALUE',
         //         html: 'Please FILL CHECKLIST',
         //         type: 'warning',
         //         showCancelButton: false
         //     });
         //     return;
         // }
     }
    if (opt==1 && (inv.admited_ward==undefined || inv.adminted_icu_ot==undefined)){
         swal({
             title: 'MISSING VALUE',
             html: 'Please Write Admission description or choose from option',
             type: 'warning',
             showCancelButton: false
         });
         return;
     }
     if ($scope.diagnosisTemp.length==0){
         swal({
             title: 'MISSING VALUE',
             html: 'Please FILL Diagnosis',
             type: 'warning',
             showCancelButton: false
         });
         return;
     } if (opt !=4 && inv.number_of_serious_injury==undefined){
         swal({
             title: 'MISSING VALUE',
             html: 'Please FILL Number of Serious Injuries',
             type: 'warning',
             showCancelButton: false
         });
         return;
     }if (opt !=4 && inv.disch && inv.disch== true && inv.plan_discussed_with_patient==undefined){
         swal({
             title: 'MISSING VALUE',
             html: 'Please Check if Plan Discussed with patient',
             type: 'warning',
             showCancelButton: false
         });
         return;
     }if (opt !=4 && inv.transfer && inv.transfer== true && inv.transfer_to==undefined){
         swal({
             title: 'MISSING VALUE',
             html: 'Please Write where Transfer has gone',
             type: 'warning',
             showCancelButton: false
         });
         return;
     }if (opt ==4 && inv.died_of==undefined){
         swal({
             title: 'MISSING VALUE',
             html: 'Please Write reason for death',
             type: 'warning',
             showCancelButton: false
         });
         return;
     }
     if (opt==4 && inv.death_date== true && inv.death_time==undefined){
         swal({
             title: 'MISSING VALUE',
             html: 'Please Write Death Date And Time',
             type: 'warning',
             showCancelButton: false
         });
         return;
     }
     else{

         $scope.saved=0;

         var records=[];

    records.push({
        checklist_completed:inv.checklist_completed, ed_departure_date:inv.ed_departure_date,ed_departure_time:inv.ed_departure_time,
        impressions:inv.impressions, number_of_serious_injury:inv.number_of_serious_injury,
        admited:inv.admit, admited_ward:inv.admited_ward,
        left_without_complete_treatment:inv.left_without_complete_treatment, discharge_notes:inv.discharge_notes,
        adminted_icu_ot:inv.adminted_icu_ot, discharged:inv.disch,
        plan_discussed_with_patient:inv.plan_discussed_with_patient, left_without_seen:inv.left_without_seen,
        transfer:inv.transfer, transfer_to:inv.transfer_to,
        accepting_provider:inv.accepting_provider, deceased:inv.deceased,
        died_of:inv.died_of,
        client_id:patient.client_id,
        user_id:user_id,facility_id:facility_id
    });

 $http.post('/api/save-trauma-client-disposition',{records:records,diagnosis:$scope.diagnosisTemp}).then(function (response) {
                 swal({
                     title: 'DISPOSITION',
                     html: response.data.text,
                     type: response.data.status,
                     showCancelButton: false
                 });
         $scope.diagnosisTemp=[];
         $('#died_of').val("");
         $('#accepting_provider').val("");
         $('#admited_ward').val("");
         $('#checklist_completed').val("");
         $('#accepting_provider').val("");
         $('#impressions').val("");
         $('#ed_departure_date').val("");
         $('#discharge_notes').val("");


                 //$scope.loadHistory(patient.client_id);


             },
             function(response){
                 swal({
                     title: 'DISPOSITION',
                     html: 'Something seems to have gone wrong. Patient details not saved.',
                     type: 'warning',
                     showCancelButton: false
                 });
             }
         );
     }

 }

 $scope.clearFields=function(med){
var vall=$('#general_examination').val();
     console.log(med);
     if (vall==undefined ||vall==undefined=="") {
         $('#general_examination').val("");
         $('#neck_examination').val("");
         $('#back_examination').val("");
         $('#abdominal_examination').val("");
         $('#heent_examination').val("");
         $('#neuro_examination').val("");
         $('#pulm_chest_examination').val("");
         $('#cardiac_examination').val("");
         $('#gu_rectal_examination').val("");
         $('#msk_skin_examination').val("");
     }
     else if(med=='msk_skin_normal'){

         $('#msk_skin__examination').val("");
     }
     else if(med=='neck_normal'){

         $('#neck_examination').val("");
     }
     else if(med=='abdominal_normal'){

         $('#abdominal_examination').val("");
     }
     else if(med=='heent_normal'){

         $('#heent_examination').val("");
     }
     else if(med=='back_normal'){

         $('#back_examination').val("");
     }
     else if(med=='neuro_normal'){

         $('#neuro_examination').val("");
     }
     else if(med=='pulm_chest_normal'){

         $('#pulm_chest_examination').val("");
     }
     else if(med=='cardiac_normal'){

         $('#cardiac_examination').val("");
     }
     else if(med=='gu_rectal_normal'){

         $('#gu_rectal_examination').val("");
     }
 }

            var diag = [];

            $scope.showDiagnosis = function(search) {

                $http.post('/api/getDiagnosis', {

                    "search": search

                }).then(function(data) {

                    diag = data.data;

                });

                return diag;

            }
            $scope.diagnosisTemp = [];

            $scope.addConf = function(item,client,type) {

                        $scope.diagnosisTemp.push({
                            "user_id":user_id,
                            "facility_id":facility_id,
                            "client_id":client.client_id,
                            "diagnosis_code": item.code,
                            "type":type,
                            "description": item.description,

                        });
                        $("#selectedConfirmed").val("");



            }
            $scope.removeFromSelection = function(item, objectdata) {

                var indexremoveobject = objectdata.indexOf(item);

                objectdata.splice(indexremoveobject, 1);

            }
 $scope.loadHistory=function(patient_id){
     $http.get('/api/get-chief-complaint/'+patient_id).then(function (response) {
             $scope.chief_complaints = response.data;
         }
     );

     $http.get('/api/get-client-vitals/'+patient_id).then(function (response) {
             $scope.clientvitals = response.data;
         }
     );
      $http.get('/api/getAccidentLocation/'+patient_id).then(function (response) {
             $scope.accloaction = response.data;
         });
         $http.get('/api/getAirwayPrimarySurvey/'+patient_id).then(function (response) {
             $scope.airway = response.data;
         });
         $http.get('/api/getBreathingPrimarySurvey/'+patient_id).then(function (response) {
             $scope.breath = response.data;
         });
          $http.get('/api/getCirculationPrimarySurvey/'+patient_id).then(function (response) {
             $scope.circulation = response.data;
         });
         $http.get('/api/getDisabilityPrimarySurvey/'+patient_id).then(function (response) {
             $scope.disability = response.data;
         });
         $http.get('/api/getExposurePrimarySurvey/'+patient_id).then(function (response) {
             $scope.exposure = response.data;
         });
         $http.get('/api/getFastPrimarySurvey/'+patient_id).then(function (response) {
             $scope.fast = response.data;
         });
         $http.get('/api/getPastMedicalHistory/'+patient_id).then(function (response) {
             $scope.pastmedical = response.data;
         });
          $http.get('/api/getPastMedicalAllergyHistory/'+patient_id).then(function (response) {
             $scope.allergies = response.data;
         });
           $http.get('/api/getTraumaHpi/'+patient_id).then(function (response) {
             $scope.hpi = response.data;
         });
            $http.get('/api/getPhysicalExamination/'+patient_id).then(function (response) {
             $scope.med = response.data;
         });
           $http.get('/api/getInjuryMechanism/'+patient_id).then(function (response) {
             $scope.mechanism = response.data;
         });
           $http.get('/api/getTraumaLabResults/'+patient_id).then(function (response) {
             $scope.lab= response.data;
         });
           $http.get('/api/getTraumaImageResults/'+patient_id).then(function (response) {
             $scope.image = response.data;
         });

           $http.get('/api/getTraumaassesment/'+patient_id).then(function (response) {
             $scope.ass = response.data;
         });
            $http.get('/api/getTraumareassesment/'+patient_id).then(function (response) {
             $scope.reass = response.data;
         });

            $http.get('/api/getTraumaFluid/'+patient_id).then(function (response) {
             $scope.fluid = response.data;
         });
            $http.get('/api/getTraumaProcedure/'+patient_id).then(function (response) {
             $scope.procedure = response.data;
         });
            
          

         
         
 }


        $scope.showCorpse = function (searchKey) {
            $http.post('/api/getCorpse',{search:searchKey,facility_id:facility_id}).then(function (data) {
               corpseData = data.data;
            });
            return corpseData;
        }
        $scope.getPerformance = function (item) {
            var perfData = {facility_id:facility_id,user_id:user_id,start:item.start,end:item.end};
            $http.post('/api/doctorsPerformance',perfData).then(function (data) {
                $scope.performanceRange = data.data[0];
                $scope.performanceThisMonth = data.data[1];
            });
        }
    $scope.getByCategory = function (item) {                    
                    if(item.bill_id =='all'){
                        $http.post('/api/getOpdPatients',{"facility_id":facility_id}).then(function(data) {
                            $scope.patientData = data.data;
                        });
                    }
                    else {
                        $http.post('/api/filterByCategory',{bill_id:item.bill_id}).then(function (data) {
                            $scope.patientData = data.data;
                        });
                    }
                }
        angular.element(document).ready(function() {
    
                $http.get('/api/getPatientCategories').then(function (data) {
                $scope.patientCategories = data.data;
                });
            $http.post('/api/getOpdPatients', {
                "facility_id": facility_id
            }).then(function(data) {
                $scope.patientData = data.data;
            });
            $http.post('/api/investigationList', {
                "facility_id": facility_id
            }).then(function(data) {
                $scope.patientInvData = data.data;
            });
            $http.post('/api/getCorpseList',{facility_id:facility_id}).then(function (data) {
                $scope.corpseData = data.data;
            });
        });
        var patientOpdPatients = [];
        $scope.showSearch = function(searchKey) {
            $http.post('/api/getAllOpdPatients', {
                "searchKey": searchKey,
                "facility_id": facility_id
            }).then(function(data) {
                patientOpdPatients = data.data;
            });
            return patientOpdPatients;
        }

        var patientInvPatients = [];
        $scope.showSearch2 = function(searchKey) {
                   $http.post('/api/getAllInvPatients', {
                "searchKey": searchKey,
                "facility_id": facility_id
            }).then(function(data) {
                patientInvPatients = data.data;
            });
            return patientInvPatients;
        };
        
        
        $scope.getConsultationModal = function(item) {
            $scope.item = item;
            $mdDialog.show({
                    controller: function ($scope) {

                        //Tb and leprosy form
                        var user_id = $rootScope.currentUser.id;
                        $scope.saveTbLeprosyRequest=function (item,clientInfo) {
                            if (item ==undefined) {
                                swal("Please fill in all required fields","","error");    return;}
                            if (item.reason_for_examination ==undefined) {swal("Please fill Reason for examination","","error"); return;
                            }
                            if (item.hiv_status ==undefined) {swal("Please fill HIV Status","","error");return;}
                            if (item.pre_tb_treatment ==undefined) {swal("Please fill Previously treated for TB ","","error");return;}
                            if (item.specimen_type ==undefined) {swal("Please fill Specimen type ","","error");return;}
                            if (item.test_requested ==undefined) {swal("Please fill Test(s) requested ","","error");return;}
                            var payloaded={
                                dtlc_email:item.dtlc_email,
                                dtlc_name:item.dtlc_name,
                                hiv_status: item.hiv_status,
                                pre_tb_treatment:item.pre_tb_treatment,
                                reason_for_examination:item.reason_for_examination,
                                rtlc_email:item.rtlc_email,
                                rtlc_name:item.rtlc_name,
                                specimen_type: item.specimen_type,
                                test_requested:item.test_requested,
                                month_on_treatment:item.month_on_treatment,
                                user_id:user_id,
                                visit_id:clientInfo.account_id,
                                patient_id:clientInfo.patient_id,


                            }
                            $http.post('/api/saveTbLeprosyRequest',payloaded).then(function (data) {
                                if(data.data.status==1){
                                    swal(data.data.msg,"","success");
                                }
                                else{
                                    swal(data.data.msg,"","error");
                                }

                            });
                        }
                        $scope.saveTbLeprosyResult=function (item) {
                            swal("Results (to be completed in Laboratory)","","info");
                        }


                        $http.get('/api/getLoginUserDetails/' + user_id).then(function (data) {
                            $scope.menu = data.data;

                        });
                        $scope.TB_leprosyResultsPerRequest=function(pt){
                            $mdDialog.show({
                                controller: function ($scope) {
                                    var user_id = $rootScope.currentUser.id;
                                    var facility_id = $rootScope.currentUser.facility_id;
                                    $http.get('/api/getLoginUserDetails/'+user_id )
                                        .then(function(data) {
                                            $scope.facilityIn=data.data[0];
                                        });

                                    $http.get('/api/TB_leprosyResultsPerRequest/'+pt.id )
                                        .then(function(data) {
                                            var resultss=data.data[0];
                                            $scope.appearance=resultss.appearance;
                                            $scope.comment=resultss.comment;
                                            $scope.ear_lobe=resultss.ear_lobe;
                                            $scope.laboratory_serial_no=resultss.laboratory_serial_no;
                                            $scope.lesion=resultss.lesion;
                                            $scope.reception_date=resultss.reception_date;
                                            $scope.result=resultss.result;
                                            $scope.specimen=resultss.specimen;
                                            $scope.zn_fm=resultss.zn_fm;
                                            $scope.reviewed_date=resultss.reviewed_date;
                                            $scope.reviewed_time=resultss.reviewed_time;
                                            $scope.reviewed_by=resultss.reviewed_by;
                                        });

                                    $http.get('/api/getpatientAddress/'+pt.residence_id )
                                        .then(function(data) {
                                            $scope.address=data.data[0];
                                        });
                                    $scope.patientIfor = pt;
                                    $scope.dtlc_email=pt.dtlc_email;
                                    $scope.dtlc_name=pt.dtlc_name;
                                    $scope.hiv_status= pt.hiv_status;
                                    $scope.pre_tb_treatment=pt.pre_tb_treatment;
                                    $scope.reason_for_examination=pt.reason_for_examination;
                                    $scope.rtlc_email=pt.rtlc_email;
                                    $scope.rtlc_name=pt.rtlc_name;
                                    $scope.month_on_treatment=pt.month_on_treatment;
                                    $scope.specimen_type=pt.specimen_type;
                                    $scope.test_requested=pt.test_requested,
                                        $scope.visit_id=  pt.visit_id;



                                    $scope.cancel = function () {
                                        $mdDialog.hide();

                                    };



                                },
                                templateUrl: '/views/modules/Exemption/tb_leprosy_result_todoctor.html',
                                parent: angular.element(document.body),
                                clickOutsideToClose: true,
                                fullscreen: false,
                            });

                        }


                        $http.post('/api/getStores',{facility_id:$rootScope.currentUser.facility_id}).then(function (data) {
                        $scope.stores = data.data;
                    });
                    $http.post('/api/getMedicalSuppliesList',{facility_id:$rootScope.currentUser.facility_id}).then(function (data) {
                        $scope.medicalSupplyList = data.data;
                    });
                    $http.post('/api/getProceduresList',{facility_id:$rootScope.currentUser.facility_id}).then(function (data) {
                        $scope.procedureList = data.data;
                    });
                    $scope.getByStore = function (item) {
                        $http.post('/api/getMedicineByStore',{store_id:item.id}).then(function (data) {
                            $scope.medicani = data.data;
                        });
                    }
                    $scope.loadIndex2 = 10;
                    $scope.loadIndexSup = 10;
                    $scope.loadIndexPro = 10;
                    $scope.showMoreMedicine = function() {
                        if ($scope.loadIndex2 < $scope.medicani.length) {
                            $scope.loadIndex2 += 6;
                        }
                    }
                    $scope.showMoreMedicalSupplies = function() {
                        if ($scope.loadIndexSup < $scope.medicalSupplyList.length) {
                            $scope.loadIndexSup += 6;
                        }
                    }
                    $scope.showMoreProcedure = function() {
                        if ($scope.loadIndexPro < $scope.procedureList.length) {
                            $scope.loadIndexPro += 6;
                        }
                    }
                        $scope.cancel = function() {
                            $mdDialog.cancel();
                        };
                    $scope.getTodaysVitals = function (item) {
                      $http.post('/api/vitalsTime',{patient_id:patient_id,account_id:account_id}).then(function (data) {
                          $scope.vitalTime = data.data;
                      });
                    }
                        $http.get('/api/TB_leprosyResultsPerPatient/'+item.patient_id).then(function (data) {
                            $scope.tb_leps_ques=data.data;

                        });
                    $scope.getPatientVitals = function (item) {
                      $http.post('/api/patientVitals',{patient_id:patient_id,account_id:account_id,time_taken:item.created_at}).then(function (data) {
                          $scope.vitalData = data.data;
                      });
                    }    
                        var object = $scope.item;
                        $scope.selectedPatient = item;
                        $scope.patientBills = object;
                        var user_id = $rootScope.currentUser.id;
                        var facility_id = $rootScope.currentUser.facility_id;
                        var patient_id = item.patient_id;
                        var account_id = item.account_id;
                           $scope.toto = function() {
                            var total = 0;
                            for (var i = 0; i < $scope.patientBills.length; i++) {
                                total += ($scope.patientBills[i].quantity * $scope.patientBills[i].price - $scope.patientBills[i].discount);
                            }
                            return total;
                        }

                        $scope.cancelBill = function(item) {
                            $http.post('/api/cancelBillItem', {
                                "id": item.id,
                                "patient_id": item.patient_id,
                                "facility_id": facility_id,
                                "user_id": user_id
                            }).then(function(data) {
                                for(var i = 0; i < $scope.patientBills.length; i++)
                                    if($scope.patientBills[i].id == item.id)
                                        $scope.patientBills.splice(i,1);
                                if($scope.patientBills.length == 0)
                                    $scope.cancel();
                            });
                        };

                        $scope.oneAtATime = true;
                        $scope.templates = [{
                            name: 'Admission',
                            url: 'admission.html'
                        }, {
                            name: 'Internal Transfer',
                            url: 'internal.html'
                        }, {
                            name: 'External Referral',
                            url: 'referral.html'
                        }, {
                            name: 'Deceased',
                            url: 'deceased.html'
                        }];



                        //get patients who have paid consultation fee/exempted/insurance

                        var patientData = [];

                        $scope.showSearch = function(searchKey) {
                            $http.post('/api/getOpdPatients', {
                                "facility_id": facility_id
                            }).then(function(data) {
                                patientData = data.data;
                            });
                            return patientData;
                        }

                        var patientInvData = [];

                        $scope.showSearch = function(searchKey) {
                            $http.post('/api/getOpdInvPatients', {
                                "facility_id": facility_id
                            }).then(function(data) {
                                patientInvData = data.data;
                            });
                            return patientInvData;
                        }

                        //Previous History

                        angular.element(document).ready(function() {
                        $http.post('/api/previousVisits', {

                                "patient_id": patient_id

                            }).then(function(data) {

                                $scope.patientsVisits = data.data;

                            });

                            $http.post('/api/getResults', {

                                "patient_id": patient_id,

                                "dept_id": 3

                            }).then(function(data) {

                                $scope.radiology = data.data;

                            });

                            $http.post('/api/getResults', {

                                "patient_id": patient_id,

                                "dept_id": 2

                            }).then(function(data) {

                                $scope.labInvestigations = data.data;
                                 

                            });

                            $http.get('/api/getWards/' + facility_id).then(function(data) {

                                $scope.wards = data.data;

                            });

                            $http.get('/api/getSpecialClinics').then(function(data) {

                                $scope.clinics = data.data;

                            });

                        });

                        $scope.getPatientReport = function(item) {
                            $http.post('/api/prevHistory', {

                                "patient_id": item.patient_id,

                                "visit_date_id": item.account_id

                            }).then(function(data) {
                                $scope.prevHistory = data.data[0];
                                $scope.otherComplaints = data.data[1];
                                $scope.hpi = data.data[2];

                            });

                            $http.post('/api/getPrevDiagnosis', {

                                "patient_id": item.patient_id,

                                "visit_date_id": item.account_id

                            }).then(function(data) {

                                $scope.prevDiagnosis = data.data;

                            });

                            $http.post('/api/getPrevRos', {

                                "patient_id": item.patient_id,

                                "visit_date_id": item.account_id

                            }).then(function(data) {

                                $scope.prevRos = data.data[0];
                                $scope.prevRosSummary = data.data[1];

                            });

                            $http.post('/api/getPrevBirth', {

                                "patient_id": item.patient_id,

                                "date_attended": item.date_attended

                            }).then(function(data) {

                                $scope.prevBirth = data.data;

                            });
                            $http.post('/api/getPrevFamily', {

                                "patient_id": item.patient_id,

                                "date_attended": item.date_attended

                            }).then(function(data) {

                                $scope.prevFamily = data.data;

                            });
                            $http.post('/api/prevInvestigationResults', {

                                "patient_id": item.patient_id,

                                "visit_date_id": item.account_id,

                                "dept_id": 2

                            }).then(function(data) {

                                $scope.labInvestigationsz = data.data;

                            });

                            $http.post('/api/getInvestigationResults', {

                                "patient_id": item.patient_id,

                                    "account_id": item.account_id,

                                "dept_id": 3

                            }).then(function(data) {

                                $scope.radiologyResults = data.data;

                            });

                            $http.post('/api/getPastMedicine', {

                                "patient_id": item.patient_id,

                                "visit_date_id": item.account_id,

                            }).then(function(data) {

                                $scope.prevMedicines = data.data;

                            });

                            $http.post('/api/getPastProcedures', {

                                "patient_id": item.patient_id,

                                "visit_date_id": item.account_id

                            }).then(function(data) {

                                $scope.pastProcedures = data.data;

                            });

                            $http.post('/api/getAllergies', {

                                "patient_id": item.patient_id,

                                "date_attended": item.date_attended,
                                "visit_date_id": item.account_id

                            }).then(function(data) {

                                $scope.allergies = data.data;

                            });
                            $http.post('/api/getPrevPhysical', {
                                "patient_id": item.patient_id,
                                "visit_date_id": item.account_id
                            }).then(function(data) {
                                $scope.prevSystemic = data.data[0];
                                $scope.prevGen = data.data[1];
                                $scope.prevLocal = data.data[2];
                                $scope.prevSummary = data.data[3];
                                $scope.prevOtherSystemic = data.data[4];
                            });
                        }
                        var patientVitals = [];

                        $scope.getVitalData = function(item, patient) {
                            $http.post('/api/patientVitals', {
                                "patient_id": patient.patient_id,
                                "time_attended": item.time_attended
                            }).then(function(data) {
                                patientVitals = data.data;
                                var object = angular.extend({}, patientVitals, patient);

                                var modalInstance = $uibModal.open({
                                    templateUrl: '/views/modules/ClinicalServices/vitalSigns.html',
                                    size: 'lg',
                                    animation: true,
                                    controller: 'admissionModal',
                                    resolve: {
                                        object: function() {
                                            return object;
                                        }
                                    }
                                });
                            });
                        }


                        //chief complaints
                        var chiefComplaints = [];
                        $scope.complaints = function(search) {
                            $http.post('/api/chiefComplaints', {
                                "search": search
                            }).then(function(data) {
                                chiefComplaints = data.data;
                            });
                            return chiefComplaints;
                        }

                        //review of systems

                        var cardiovascular = [];
                        var ENT = [];

                        $scope.showEnt = function(search) {
                            $http.post('/api/reviewOfSystems', {
                                "search": search,
                                "category": "ENT"
                            }).then(function(data) {
                                ENT = data.data;
                            });
                            return ENT;
                        }
                        $scope.showCardio = function(search) {
                            $http.post('/api/reviewOfSystems', {
                                "search": search,
                                "category": "Cardiovascular"
                            }).then(function(data) {
                                cardiovascular = data.data;
                            });
                            return cardiovascular;
                        }

                        var Respiratory = [];

                        $scope.showRespiratory = function(search) {
                            $http.post('/api/reviewOfSystems', {
                                "search": search,
                                "category": "Respiratory"
                            }).then(function(data) {
                                Respiratory = data.data;
                            });
                            return Respiratory;
                        }

                        var gatro = [];

                        $scope.showGastro = function(search) {
                            $http.post('/api/reviewOfSystems', {
                                "search": search,
                                "category": "Gastrointerstinal"
                            }).then(function(data) {
                                gatro = data.data;
                            });
                            return gatro;
                        }

                        var musculo = [];

                        $scope.showMusculo = function(search) {

                            $http.post('/api/reviewOfSystems', {

                                "search": search,

                                "category": "Musculoskeletal"

                            }).then(function(data) {

                                musculo = data.data;

                            });

                            return musculo;

                        }

                        var genito = [];

                        $scope.showGenito = function(search) {

                            $http.post('/api/reviewOfSystems', {

                                "search": search,

                                "category": "Genitourinary"

                            }).then(function(data) {

                                genito = data.data;

                            });

                            return genito;

                        }

                        var cns = [];

                        $scope.showCNS = function(search) {

                            $http.post('/api/reviewOfSystems', {

                                "search": search,

                                "category": "Central Nervous System"

                            }).then(function(data) {

                                cns = data.data;

                            });

                            return cns;

                        }

                        var endo = [];

                        $scope.showEndo = function(search) {

                            $http.post('/api/reviewOfSystems', {

                                "search": search,

                                "category": "Endocrine"

                            }).then(function(data) {

                                endo = data.data;

                            });

                            return endo;

                        }

                        var allergy = [];

                        $scope.showAllergy = function(search) {

                            $http.post('/api/reviewOfSystems', {

                                "search": search,

                                "category": "Allergy"

                            }).then(function(data) {

                                allergy = data.data;

                            });

                            return allergy;

                        }

                        var pastInsp = [];

                        $scope.showInspection = function(search) {

                            $http.post('/api/reviewOfSystems', {

                                "search": search,

                                "category": "Inspection"

                            }).then(function(data) {

                                pastInsp = data.data;

                            });

                            return pastInsp;

                        }

                        var pastPalp = [];

                        $scope.showPalpation = function(search) {

                            $http.post('/api/reviewOfSystems', {

                                "search": search,

                                "category": "Palpation"

                            }).then(function(data) {

                                pastPalp = data.data;

                            });

                            return pastPalp;

                        }

                        var pastPerc = [];

                        $scope.showPercussion = function(search) {

                            $http.post('/api/reviewOfSystems', {

                                "search": search,

                                "category": "Percussion"

                            }).then(function(data) {

                                pastPerc = data.data;

                            });

                            return pastPerc;

                        }

                        var pastAus = [];

                        $scope.showAuscultation = function(search) {

                            $http.post('/api/reviewOfSystems', {

                                "search": search,

                                "category": "Auscultation"

                            }).then(function(data) {

                                pastAus = data.data;

                            });

                            return pastAus;

                        }

                        var diag = [];

                        $scope.showDiagnosis = function(search) {

                            $http.post('/api/getDiagnosis', {

                                "search": search

                            }).then(function(data) {

                                diag = data.data;

                            });

                            return diag;

                        }

                        $scope.allergyChecker = function(item) {

                            $http.post('/api/getAllergy', {

                                "patient_id": item.patient_id

                            }).then(function(data) {

                                swal("This Patient is allergic to " + data.data[0].descriptions, "", "info");

                            });

                        }
                        $scope.stopMedication = function(prescription_id) {
                            if (prescription_id.prescription_id==undefined) {
                                swal("Something went wrong with this database setup, please contact for technical support", "", "error");
                                return;
                            }
                            $http.post('/api/stopMedication',{user_id:user_id,patient_id:prescription_id.patient_id,prescription_id:prescription_id.prescription_id}).then(function(data) {
                                $scope.prevMedicines = data.data;
                                swal("Patient prescription successfully stoped!", "", "success");
                            });


                        }
                        //Investigations
        $scope.orderedInvestigations = function (item) {
                        $http.post('/api/allOrderedInvestigations',{patient_id:item.patient_id,account_id:item.account_id}).then(function (data) {
                            $scope.doneInvestigations = data.data;
                        });
                    }

                        $scope.getSubDepts = function(item) {

                            $http.post('/api/getSubDepts', {

                                "department_id": item

                            }).then(function(data) {

                                $scope.subDepartments = data.data;

                            });

                        }

                        $scope.getTests = function(item, category) {

                            if (angular.isDefined(category) == false) {

                                swal("Please...select Patient first", "", "error");

                                return;

                            }

                            var category_id = category.bill_id;

                            if (category.main_category_id == 3) {

                                category_id = 1;

                            }

                            $http.post('/api/getPanels', {

                                "patient_category_id": category_id,

                                "sub_dept_id": item,

                                "facility_id": facility_id

                            }).then(function(data) {

                                $scope.panels = data.data;

                            });



                            $http.post('/api/getSingleTests', {

                                "patient_category_id": category_id,

                                "sub_dept_id": item,

                                "facility_id": facility_id
                            }).then(function(data) {
                                $scope.singleTests = data.data;
                            });
                            $http.post('/api/getTests', {
                                "patient_category_id": category_id,
                                "sub_dept_id": item,
                                "facility_id": facility_id
                            }).then(function(data) {

                                $scope.labTests = data.data;

                            });
                            
                            if($scope.inv) 
                                $scope.inv.priority='Routine'; 
                            else 
                                $scope.inv = {priority:'Routine'};

                        }

                        $scope.investigationOrders = [];

                        $scope.unavailableOrders = [];

                        $scope.getsumCHFBill = function () {
                            var sumCHFBill=0;
                            for(var i = 0; i < $scope.investigationOrders.length ; i++) {
                                sumCHFBill -= -($scope.investigationOrders[i].price);
                            }
                            return sumCHFBill;
                        }

                        $scope.orders = function(item, isChecked, patient) {
                            console.log(patient);
                            $scope.totalCHFBils="";
                            var status_id = 1;
                            var sub_category_name = patient.sub_category_name.toLowerCase();

                            var filter = patient.bill_id;
                            if(sub_category_name=='chf'){
                                $http.post('api/chfCheckBills',{patient_id:patient_id,account_id:account_id}).then(function (data) {
                                    $scope.totalCHFBils = data.data[0];
                                    $scope.chf_item = data.data[1][0];
                                    $scope.chf_ceiling = parseInt( data.data[2].original.chf_ceiling);
                                    var using = data.data[2].original.use_chf_settings;
                                    if(using==1){
                                        $scope.BillGenerated= $scope.getsumCHFBill($scope.investigationOrders);
                                    }


                                    var billed=$scope.totalCHFBils;
                                    var currentBill = $scope.BillGenerated;
                                    var ceiling = $scope.chf_ceiling;
                                    var totalBill = currentBill - (- billed);
                                    var difference = totalBill - ceiling;
                                    if (difference >0 && using==1){
                                        console.log('CHF TOP UP '+ difference);
                                        $scope.chf_top_up=difference;
                                        $scope.investigationOrders.push({

                                            "requesting_department_id": 1,

                                            "admission_id": '',

                                            "facility_id": facility_id,

                                            "item_type_id": $scope.chf_item.item_type_id,

                                            "item_price_id": $scope.chf_item.item_price_id,
                                            "price": $scope.chf_item.price,
                                            'quantity':$scope.chf_top_up/$scope.chf_item.price,
                                            "status_id": status_id,

                                            "account_number_id": patient.account_id,

                                            "patient_id": patient.patient_id,

                                            "user_id": user_id,

                                            "item_id": $scope.chf_item.item_id,

                                            "item_name": $scope.chf_item.item_name,

                                            "priority": '',

                                            "clinical_note": '',

                                            "payment_filter": filter

                                        });

                                    }
                                    else{
                                        $scope.investigationOrders.push({

                                            "requesting_department_id": 1,

                                            "admission_id": '',

                                            "facility_id": facility_id,

                                            "item_type_id": item.item_type_id,

                                            "item_price_id": item.item_price_id,
                                            "price": item.price,

                                            "status_id": status_id,

                                            "account_number_id": patient.account_id,
                                            "quantity":1,
                                            "patient_id": patient.patient_id,

                                            "user_id": user_id,

                                            "item_id": item.item_id,

                                            "item_name": item.item_name,

                                            "priority": '',

                                            "clinical_note": '',

                                            "payment_filter": filter

                                        });
                                    }



                                });

                            }


                            if (patient.patient_id == null) {

                                swal("Ooops!! no Patient selected", "Please search and select patient first..");

                                return;

                            }

                            if (isChecked == true) {

                                for (var i = 0; i < $scope.investigationOrders.length; i++)

                                    if ($scope.investigationOrders[i].item_id == item.item_id) {

                                        swal(item.item_name + ' ' + " already in your order list!");

                                        return;

                                    }

                                if (item.on_off == 1) {

                                    if (patient.main_category_id != 1) {

                                        filter = patient.bill_id;

                                    }

                                    $scope.investigationOrders.push({

                                        "requesting_department_id": 1,

                                        "admission_id": '',

                                        "facility_id": facility_id,

                                        "item_type_id": item.item_type_id,

                                        "item_price_id": item.item_price_id,
                                        "price": item.price,

                                        "status_id": status_id,

                                        "account_number_id": patient.account_id,
                                        "quantity":1,
                                        "patient_id": patient.patient_id,

                                        "user_id": user_id,

                                        "item_id": item.item_id,

                                        "item_name": item.item_name,

                                        "priority": '',

                                        "clinical_note": '',

                                        "payment_filter": filter

                                    });


                                } else {

                                    for (var i = 0; i < $scope.unavailableOrders.length; i++)

                                        if ($scope.unavailableOrders[i].item_id == item.item_id) {

                                            swal(item.item_name + ' ' + " already in your order list!");

                                            return;

                                        }



                                    $scope.unavailableOrders.push({

                                        "facility_id": facility_id,

                                        "visit_date_id": patient.account_id,

                                        "patient_id": patient.patient_id,

                                        "user_id": user_id,

                                        "item_id": item.item_id,

                                        "item_name": item.item_name

                                    });

                                    return;

                                }

                            }

                        }

                        $scope.saveInvestigation = function(item) {
                            if(angular.isDefined(item)== false){
                                swal('whoops sorry...','Please,choose priority then click save button','error');
                                return;
                            }
                            if ($scope.investigationOrders == "" && $scope.unavailableOrders == null) {

                                swal("You dont have Items to save!", "Please select Items first!");
                                return;
                            }
                    var details = {"admission_id":'',"patient_id": patient_id,"visit_date_id": account_id,
                        "user_id": user_id,"facility_id": facility_id,"requesting_department_id":1};

                    var invData = {details:details,clinicalData:$scope.investigationOrders,
                        priority:item.priority,clinical_note:item.clinical_note};
                    if ($scope.investigationOrders != "") {
                                $http.post('/api/postInvestigations', invData).then(function(data) {});
                                $scope.investigationOrders = [];
                                $('#clinical_note').val('');
                                $('#priority').val('');
                            }
                            $http.post('/api/postUnavailableInvestigations', $scope.unavailableOrders).then(function(data) {
                            });
                            swal("Investigation order successfully saved!", "", "success");
                            $scope.unavailableOrders = [];
                            $('#clinical_note').val('');
                            $('#priority').val('');
                        }
                        //Investigation results

                        $scope.getLabResults = function(item) {
                            var results = {

                                "patient_id": item.patient_id,

                                "account_id": item.account_id,

                                "dept_id": item.dept_id

                            };

                            $http.post('/api/getInvestigationResults', results).then(function(data) {

                                $scope.labResults = data.data;
                                 

                            });

                        }
                        $scope.getRadResults = function(item) {

                            var results = {

                                "patient_id": item.patient_id,

                                "account_id": item.account_id,

                                "dept_id": item.dept_id

                            };

                            $http.post('/api/getInvestigationResults', results).then(function(data) {
                                $scope.radResults = data.data;
                            });

                        }
                        //blood request options start
                        $scope.getVipimo = function (item) {
                            var results = {
                                "patient_id": item.patient_id,
                                "account_id": item.account_id,
                                "dept_id": 2
                            };
                            $http.post('/api/getInvestigationResults', results).then(function(data) {
                                $scope.latestLabResults = data.data;
                            });
                        }
                        $scope.requestBlood = function (item) {
                            var requests = {facility_id:facility_id,patient_id:patient_id,visit_id:account_id,requested_by:user_id,dept_id:1,
                                blood_group:item.blood_group,priority:item.priority,unit_requested:item.unit_requested,request_reason:item.request_reason
                            };
                            $http.post('/api/requestBlood', requests).then(function(data) {
                                var taa=data.data.msg;
                                swal('',taa,'success');
                            });
                        }
                        //blood request options end
                        //document reader starts
                        $scope.getAttachedDocument=function(documentData){
                            $mdDialog.show({
                                controller: function ($scope) {
                                    var sample= ""+ documentData.sample_no;
                                    var uploadedFile="/labresults/"+sample+".pdf";
                                    $scope.selectedPatient =documentData;
                                    $scope.resultsFile=uploadedFile;

                                    $scope.cancelPdf = function () {

                                        $mdDialog.hide();
                                    };
                                },
                                templateUrl: '/views/modules/clinicalServices/images.html',
                                parent: angular.element(document.body),
                                clickOutsideToClose: false,
                                fullscreen: $scope.customFullscreen // Only for -xs, -sm breakpoints.
                            });
                        }
                        //document reader ends
                        //posting from history and examinations

                        $scope.complaintz = [];

                        $scope.addComplaint = function(item,qty,unit, patient) {

                            if (patient.patient_id == null) {

                                swal("Ooops!! no Patient selected", "Please search and select patient first..");
                                return;
                            }
                            for (var i = 0; i < $scope.complaintz.length; i++)
                                if ($scope.complaintz[i].id == item.id) {
                                    swal(item.name + ' ' + "already in your wish list!","","info");
                                    return;
                                }

                            $scope.complaintz.push({

                                "admission_id": '',

                                "patient_id": patient.patient_id,

                                "facility_id": facility_id,

                                "visit_date_id": patient.account_id,

                                "user_id": user_id,

                                "description": item.name,

                                "id": item.id,

                                "duration": qty,
                                "other_complaints": '',

                                "duration_unit": unit,

                                "status": 'Chief Complaints'

                            });
                            $("#complaint").val('');
                            $("#dxn").val('');
                            $("#unit").val('');
                        }

                        $scope.saveComplaints = function(objectData, other_complaints,patient) {
                            var details = {
                                "admission_id": '',
                                "patient_id": patient_id,
                                "visit_date_id": account_id,
                                "user_id": user_id,
                                "facility_id": facility_id,
                            };
                            var varData = {otherData:other_complaints,complaints:objectData,details:details};

                            $http.post('/api/postHistory', varData).then(function(data) {

                            });
                            swal("Complaints  data successfully saved!", "", "success");
                            $("#other_complaints").val('');
                            $scope.complaintz = [];

                        }
                        $scope.saveHpi = function (item,patient) {
                            var hpi = {"admission_id": '',

                                "patient_id": patient.patient_id,

                                "facility_id": facility_id,

                                "visit_date_id": patient.account_id,

                                "user_id": user_id,
                                "hpi":item
                            };
                            $http.post('/api/postHpi',hpi).then(function (data) {

                            });
                            swal("History of presenting illness data successfully saved!", "", "success");
                            $('#hpi').val('');
                        }

                        $scope.rosTemp = [];

                        $scope.reviewENT = function(item, patient) {
                            if (patient.patient_id == null) {

                                swal("Ooops!! no Patient selected", "Please search and select patient first..");

                                return;
                            }
                            $scope.rosTemp.push({

                                "admission_id": '',
                                "patient_id": patient.patient_id,
                                "visit_date_id": patient.account_id,
                                "user_id": user_id,
                                "facility_id": facility_id,
                                "system_id": item.id,
                                "name": item.name,
                                "status": item.category
                            });

                            $("#ent").val('');

                        }
                        $scope.reviewOfSystems = function(item, patient) {
                            if (patient.patient_id == null) {

                                swal("Ooops!! no Patient selected", "Please search and select patient first..");

                                return;
                            }
                            $scope.rosTemp.push({

                                "admission_id": '',
                                "patient_id": patient.patient_id,
                                "visit_date_id": patient.account_id,
                                "user_id": user_id,
                                "facility_id": facility_id,
                                "system_id": item.id,
                                "name": item.name,
                                "status": item.category
                            });

                            $("#cardio").val('');

                        }

                        $scope.reviewOfSystems2 = function(item, patient) {

                            if (patient.patient_id == null) {

                                swal("Ooops!! no Patient selected", "Please search and select patient first..");

                                return;

                            }

                            $scope.rosTemp.push({

                                "admission_id": '',

                                "patient_id": patient.patient_id,

                                "visit_date_id": patient.account_id,

                                "user_id": user_id,

                                "facility_id": facility_id,

                                "system_id": item.id,

                                "name": item.name,

                                "status": item.category

                            });

                            $("#respiratory").val('');

                        }

                        $scope.reviewOfSystems3 = function(item, patient) {

                            if (patient.patient_id == null) {

                                swal("Ooops!! no Patient selected", "Please search and select patient first..");

                                return;

                            }

                            $scope.rosTemp.push({

                                "admission_id": '',

                                "patient_id": patient.patient_id,

                                "visit_date_id": patient.account_id,

                                "user_id": user_id,

                                "facility_id": facility_id,

                                "system_id": item.id,

                                "name": item.name,

                                "status": item.category

                            });

                            $("#gastrointestinal").val('');

                        }

                        $scope.reviewOfSystems4 = function(item, patient) {

                            if (patient.patient_id == null) {

                                swal("Ooops!! no Patient selected", "Please search and select patient first..");

                                return;

                            }

                            $scope.rosTemp.push({

                                "admission_id": '',

                                "patient_id": patient.patient_id,

                                "visit_date_id": patient.account_id,

                                "user_id": user_id,

                                "facility_id": facility_id,

                                "system_id": item.id,

                                "name": item.name,

                                "status": item.category

                            });

                            $("#musculoskeletal").val('');

                        }

                        $scope.reviewOfSystems5 = function(item, patient) {

                            if (patient.patient_id == null) {

                                swal("Ooops!! no Patient selected", "Please search and select patient first..");

                                return;

                            }

                            $scope.rosTemp.push({

                                "admission_id": '',

                                "patient_id": patient.patient_id,

                                "visit_date_id": patient.account_id,

                                "user_id": user_id,

                                "facility_id": facility_id,

                                "system_id": item.id,

                                "name": item.name,

                                "status": item.category

                            });

                            $("#genitourinary").val('');

                        }

                        $scope.reviewOfSystems6 = function(item, patient) {

                            if (patient.patient_id == null) {

                                swal("Ooops!! no Patient selected", "Please search and select patient first..");

                                return;

                            }

                            $scope.rosTemp.push({

                                "admission_id": '',

                                "patient_id": patient.patient_id,

                                "visit_date_id": patient.account_id,

                                "user_id": user_id,

                                "facility_id": facility_id,

                                "system_id": item.id,

                                "name": item.name,

                                "status": item.category

                            });

                            $("#cns").val('');

                        }

                        $scope.reviewOfSystems7 = function(item, patient) {

                            if (patient.patient_id == null) {

                                swal("Ooops!! no Patient selected", "Please search and select patient first..");

                                return;

                            }

                            $scope.rosTemp.push({

                                "admission_id": '',

                                "patient_id": patient.patient_id,

                                "visit_date_id": patient.account_id,

                                "user_id": user_id,

                                "facility_id": facility_id,

                                "system_id": item.id,

                                "name": item.name,

                                "status": item.category

                            });

                            $("#endocrine").val('');

                        }

                        $scope.saveRoS = function(objectData, rosSummary) {

                            var details = {
                                "admission_id": '',
                                "patient_id": patient_id,
                                "visit_date_id": account_id,
                                "user_id": user_id,
                                "facility_id": facility_id,
                            };
                            var varData = {otherData:rosSummary,ros:objectData,details:details};

                            $http.post('/api/postRoS', varData).then(function(data) {

                            });
                            swal("Review of systems data successfully Saved!", "", "success");
                            $scope.rosTemp = [];
                            $('#ent').val('');
                            $('#cardio').val('');
                            $('#respiratory').val('');
                            $('#gastrointestinal').val('');
                            $('#musculoskeletal').val('');
                            $('#genitourinary').val('');
                            $('#cns').val('');
                            $('#endocrine').val('');
                            $('#review_summary').val('');
                        }
                        //Past medical history
                        $scope.pastTemp = [];
                        $scope.pastMedicals = function(item, patient) {
                            if (patient.patient_id == null) {
                                swal("Ooops!! no Patient selected", "Please search and select patient first..");
                                return;
                            }
                            $scope.pastTemp.push({

                                "admission_id": '',

                                "patient_id": patient.patient_id,

                                "visit_date_id": patient.account_id,

                                "user_id": user_id,

                                "facility_id": facility_id,

                                "system_id": item.id,

                                "name": item.name,

                                "status": item.category

                            });

                        }
                        $scope.savePastMedical = function(objectData, other_past_medicals) {

                            var details = {
                                "admission_id": '',
                                "patient_id": patient_id,
                                "visit_date_id": account_id,
                                "user_id": user_id,
                                "facility_id": facility_id,
                            };

                            var varData = {otherData:other_past_medicals,allergy:objectData,details:details};
                            $http.post('/api/postPastMed',varData).then(function(data) {

                            });
                            swal("Past medical history data successfully Saved!", "", "success");
                            $('#surgeries').val('');
                            $('#admissions').val('');
                            $('#transfusion').val('');
                            $('#immunisation').val('');
                            $('#selectedAllergy').val('');
                            $scope.pastTemp = [];
                        }

                        $scope.saveBirthHistory = function(item, patient) {

                            if (patient.patient_id == null) {

                                swal("Ooops!! no Patient selected", "Please search and select patient first..");

                                return;

                            }

                            var child = {

                                "admission_id": '',

                                "patient_id": patient.patient_id,

                                "visit_date_id": patient.account_id,

                                "facility_id": facility_id,

                                "user_id": user_id,

                                "antenatal": item.antenatal,

                                "natal": item.natal,

                                "post_natal": item.post_natal,

                                "nutrition": item.nutrition,

                                "growth": item.growth,

                                "development": item.development

                            };

                            $http.post('/api/birthHistory', child).then(function(data) {

                                swal("Birth history data Successfully Saved!", "", "success");

                            });

                            $("#antenatal").val('');

                            $("#natal").val('');

                            $("#post_natal").val('');

                            $("#nutrition").val('');

                            $("#growth").val('');

                            $("#development").val('');

                        }

                        $scope.saveObsGyn = function(item, patient) {

                            if (patient.patient_id == null) {

                                swal("Ooops!! no Patient selected", "Please search and select patient first..");

                                return;

                            }

                            var obs = {

                                "admission_id": '',

                                "patient_id": patient.patient_id,

                                "visit_date_id": patient.account_id,

                                "facility_id": facility_id,

                                "user_id": user_id,

                                "menarche": item.menarche,

                                "menopause": item.menopause,

                                "menstrual_cycles": item.menstrual_cycles,

                                "pad_changes": item.pad_changes,

                                "recurrent_menstruation": item.recurrent_menstruation,

                                "contraceptives": item.contraceptives,

                                "pregnancy": item.pregnancy,

                                "lnmp": item.lnmp,

                                "gravidity": item.gravidity,

                                "parity": item.parity,

                                "living_children": item.living_children

                            };

                            $http.post('/api/postObs', obs).then(function(data) {

                                swal("Obstetrics and gynaecological data Successfully Saved!", "", "success");

                            });

                            $("#menarche").val('');

                            $("#menopause").val('');

                            $("#menstrual_cycles").val('');

                            $("#pads").val('');

                            $("#recurrent_menstruation").val('');

                            $("#contraceptives").val('');

                            $("#pregnancy").val('');

                            $("#lnmp").val('');

                            $("#gravidity").val('');

                            $("#parity").val('');

                            $("#living_children").val('');

                        }

                        //Physical Examinations

                        $scope.removeFromSelection = function(item, objectdata) {

                            var indexremoveobject = objectdata.indexOf(item);
                            $scope.getsumCHFBill();
                            objectdata.splice(indexremoveobject, 1);

                        }

                        $scope.physicalMusculoskeletal = [];

                        $scope.physicalRespiratory = [];

                        $scope.physicalCardiovascular = [];

                        $scope.physicalGastrointestinal = [];

                        $scope.physicalGenitourinary = [];

                        $scope.physicalCNS = [];

                        $scope.physicalEndocrine = [];



                        $scope.physicalMusculo = function(item, patient, system) {

                            if (patient.patient_id == null) {

                                swal("Ooops!! no Patient selected", "Please search and select patient first..");

                                return;

                            }

                            $scope.physicalMusculoskeletal.push({

                                "admission_id": '',

                                "patient_id": patient.patient_id,

                                "visit_date_id": patient.account_id,

                                "user_id": user_id,

                                "facility_id": facility_id,

                                "system": system,

                                "category": item.category,

                                "observation": item.name

                            });

                            $("#musc_inspect").val('');

                            $("#musc_palpate").val('');

                            $("#musc_percu").val('');

                            $("#musc_ausc").val('');

                        }

                        $scope.physicalResp = function(item, patient, system) {

                            if (patient.patient_id == null) {

                                swal("Ooops!! no Patient selected", "Please search and select patient first..");

                                return;

                            }

                            $scope.physicalRespiratory.push({

                                "admission_id": '',

                                "patient_id": patient.patient_id,

                                "visit_date_id": patient.account_id,

                                "user_id": user_id,

                                "facility_id": facility_id,

                                "system": system,

                                "category": item.category,

                                "observation": item.name

                            });

                            $("#resp_inspect").val('');

                            $("#resp_palpate").val('');

                            $("#resp_percus").val('');

                            $("#resp_aus").val('');

                        }

                        $scope.physicalCardio = function(item, patient, system) {

                            if (patient.patient_id == null) {

                                swal("Ooops!! no Patient selected", "Please search and select patient first..");

                                return;

                            }

                            $scope.physicalCardiovascular.push({

                                "admission_id": '',

                                "patient_id": patient.patient_id,

                                "visit_date_id": patient.account_id,

                                "user_id": user_id,

                                "facility_id": facility_id,

                                "system": system,

                                "category": item.category,

                                "observation": item.name

                            });

                            $("#cardio_inspect").val('');

                            $("#cardio_palpate").val('');

                            $("#cardio_percus").val('');

                            $("#cardio_aus").val('');

                        }

                        $scope.physicalGastro = function(item, patient, system) {

                            if (patient.patient_id == null) {

                                swal("Ooops!! no Patient selected", "Please search and select patient first..");

                                return;

                            }

                            $scope.physicalGastrointestinal.push({

                                "admission_id": '',

                                "patient_id": patient.patient_id,

                                "visit_date_id": patient.account_id,

                                "user_id": user_id,

                                "facility_id": facility_id,

                                "system": system,

                                "category": item.category,

                                "observation": item.name

                            });

                            $("#gastro_inspect").val('');

                            $("#gastro_palpate").val('');

                            $("#gastro_percus").val('');

                            $("#gastro_aus").val('');

                        }

                        $scope.physicalGenito = function(item, patient, system) {

                            if (patient.patient_id == null) {

                                swal("Ooops!! no Patient selected", "Please search and select patient first..");

                                return;

                            }

                            $scope.physicalGenitourinary.push({

                                "admission_id": '',

                                "patient_id": patient.patient_id,

                                "visit_date_id": patient.account_id,

                                "user_id": user_id,

                                "facility_id": facility_id,

                                "system": system,

                                "category": item.category,

                                "observation": item.name

                            });

                            $("#genito_inspect").val('');

                            $("#genito_palpate").val('');

                            $("#genito_percus").val('');

                            $("#genito_aus").val('');

                        }

                        $scope.physicalCns = function(item, patient, system) {

                            if (patient.patient_id == null) {

                                swal("Ooops!! no Patient selected", "Please search and select patient first..");

                                return;

                            }

                            $scope.physicalCNS.push({

                                "admission_id": '',

                                "patient_id": patient.patient_id,

                                "visit_date_id": patient.account_id,

                                "user_id": user_id,

                                "facility_id": facility_id,

                                "system": system,

                                "category": item.category,

                                "observation": item.name

                            });

                            $("#cns_inspect").val('');

                            $("#cns_palpate").val('');

                            $("#cns_percus").val('');

                            $("#cns_aus").val('');

                        }

                        $scope.physicalEndo = function(item, patient, system) {

                            if (patient.patient_id == null) {

                                swal("Ooops!! no Patient selected", "Please search and select patient first..");

                                return;

                            }

                            $scope.physicalEndocrine.push({

                                "admission_id": '',

                                "patient_id": patient.patient_id,

                                "visit_date_id": patient.account_id,

                                "user_id": user_id,

                                "facility_id": facility_id,

                                "system": system,

                                "category": item.category,

                                "observation": item.name

                            });

                            $("#endo_inspect").val('');

                            $("#endo_palpate").val('');

                            $("#endo_percus").val('');

                            $("#endo_aus").val('');

                        }

                        $scope.savePhysicalExamination = function(objectData) {

                            if (objectData == "") {
                                swal("Oops Data not saved!", "Please search and select items then click 'Save' button to save data..");

                                return;
                            }
                            $http.post('/api/postPhysical', objectData).then(function(data) {

                            });

                            swal(objectData[0].system + '  ' + "system data successfully Saved!", "", "success");

                            $scope.physicalMusculoskeletal = [];

                            $scope.physicalRespiratory = [];

                            $scope.physicalCardiovascular = [];

                            $scope.physicalGastrointestinal = [];

                            $scope.physicalGenitourinary = [];

                            $scope.physicalCNS = [];

                            $scope.physicalEndocrine = [];

                        }

                        $scope.saveLocalExams = function(patient, examData) {

                            if (examData == null) {
                                swal('Please write examination for this patient first', '', 'error');
                                return;
                            }
                            var local_examz = {

                                "admission_id": '',

                                "patient_id": patient.patient_id,

                                "visit_date_id": patient.account_id,

                                "user_id": user_id,

                                "facility_id": facility_id,

                                "local_examination": examData

                            }

                            $http.post('/api/postLocalPhysical', local_examz).then(function(data) {});



                            swal('Local Examination', 'data for this patient saved', 'success');

                            $('#local_examination').val('');

                        }
                        $scope.saveGenExams = function(patient, examData) {
                            if (examData == null) {
                                swal('Please write examination for this patient first', '', 'error');
                                return;
                            }
                            var gen_examz = {
                                "admission_id": '',
                                "patient_id": patient.patient_id,
                                "visit_date_id": patient.account_id,
                                "user_id": user_id,
                                "facility_id": facility_id,
                                "gen_examination": examData
                            }

                            $http.post('/api/postGenPhysical', gen_examz).then(function(data) {});

                            swal('General Examination', 'data for this patient saved', 'success');

                            $('#gen_examination').val('');

                        }
                        $scope.saveSummaryExams = function(patient, examData) {
                            if (examData == null) {
                                swal('Please write examination summary for this patient first', '', 'error');
                                return;                            }
                            var summary_examz = {
                                "admission_id": '',"patient_id": patient.patient_id,
                                "visit_date_id": patient.account_id,"user_id": user_id,
                                "facility_id": facility_id,"summary_examination": examData
                            }
                            $http.post('/api/postSummaryPhysical', summary_examz).then(function(data) {});
                            swal('Summary Examination', 'data for this patient saved', 'success');
                            $('#summary_examination').val('');
                        }
                        $scope.saveSystemicSummary = function(patient, examData,system) {
                            if (examData == null) {
                                swal('Please write system notes for this patient first', '', 'error');
                                return; }
                            var summary_examz = {
                                "admission_id": '',"patient_id": patient.patient_id,
                                "visit_date_id": patient.account_id,"user_id": user_id,
                                "facility_id": facility_id,
                                "system": system,
                                "other_systems_summary": examData
                            }
                            $http.post('/api/postOtherSummary', summary_examz).then(function(data) {
                                if(data.data.status==1){
                                    swal('',data.data.msg, 'success');
                                }
                            });
                            $('#endo').val('');
                            $('#central').val('');
                            $('#cardiov').val('');
                            $('#musc').val('');
                            $('#resp').val('');
                            $('#genital').val('');
                            $('#gastro').val('');
                        }

                        //family and social history

                        $scope.saveSocialCommunity = function(item, patient) {

                            if (patient.patient_id == null) {

                                swal("Ooops!! no Patient selected", "Please search and select patient first..");

                                return;

                            }

                            var child = {

                                "patient_id": patient.patient_id,

                                "visit_date_id": patient.account_id,

                                "facility_id": facility_id,

                                "user_id": user_id,

                                "chronic_illness": item.chronic_illness,

                                "substance_abuse": item.substance_abuse,

                                "adoption": item.adoption,

                                "others": item.others

                            };

                            $http.post('/api/familyHistory', child).then(function(data) {

                                swal("Family and social history data successfully Saved!", "", "success");

                            });

                            $("#chronic_illness").val('');

                            $("#substance_abuse").val('');

                            $("#adoption").val('');

                            $("#others").val('');

                        }

                        //Provisional , differential and confirmed diagnosis

                        $scope.diagnosisTemp = [];

                        $scope.addProv = function(item, patient, status) {



                            if (patient.patient_id == null) {

                                swal("Ooops!! no Patient selected", "Please search and select patient first..");

                                return;

                            }

                            $scope.diagnosisTemp.push({

                                "admission_id": '',

                                "patient_id": patient.patient_id,

                                "visit_date_id": patient.account_id,

                                "facility_id": facility_id,

                                "user_id": user_id,

                                "diagnosis_description_id": item.id,

                                "description": item.description,
                                "code":item.code,

                                "status": status

                            });

                        }

                        $scope.addDiff = function(item, patient, status) {



                            if (patient.patient_id == null) {

                                swal("Ooops!! no Patient selected", "Please search and select patient first..");

                                return;

                            }

                            $scope.diagnosisTemp.push({

                                "admission_id": '',

                                "patient_id": patient.patient_id,

                                "visit_date_id": patient.account_id,

                                "facility_id": facility_id,

                                "user_id": user_id,

                                "diagnosis_description_id": item.id,

                                "description": item.description,
                                "code":item.code,
                                "status": status

                            });

                        }

                        $scope.addConf = function(item, patient, status) {

                            if (patient.patient_id == null) {

                                swal("Ooops!! no Patient selected", "Please search and select patient first..");

                                return;

                            }

                            $scope.diagnosisTemp.push({

                                "admission_id": '',

                                "patient_id": patient.patient_id,

                                "visit_date_id": patient.account_id,

                                "facility_id": facility_id,

                                "user_id": user_id,
                                "code":item.code,
                                "diagnosis_description_id": item.id,

                                "description": item.description,

                                "status": status

                            });

                        }

                        $scope.saveDiagnosis = function(objectData,selectedPatient) {
                            if (objectData == "") {
                                swal("Empty", "Please search and select items then click 'Save' button to save data..");
                                return;
                            }
                            
                            var confirmedDiagnoses = [];
                            objectData.forEach(function(disease){
                                if(disease.status.toLowerCase() == 'confirmed')
                                    confirmedDiagnoses.push(disease);
                            }); 
                            
                            if(confirmedDiagnoses.length > 0){
                                $http.post('/api/checkPatientAttendance',{patient_id:$scope.selectedPatient.patient_id,
                            facility_id:facility_id}).then(function(data){
                                    if(data.data == 1){
                                        var inputOptions = new Promise(function (resolve) {
                                            setTimeout(function () {
                                                resolve({
                                                'new': 'New attendance',
                                                'reattendance': 'Reattendance',
                                                'NEW': 'Unknown'
                                                })
                                            },10);
                                        });

                                        swal({
                                            title: 'MTUHA TALLYING',
                                            input: 'radio',
                                            inputOptions: inputOptions,
                                            inputValidator: function (result) {
                                                                return new Promise(function (resolve, reject) {
                                                                if (result) {
                                                                    resolve()
                                                                } else {
                                                                    reject('Please, respond to the message to automatically tally the MTUHA register')
                                                                }
                                                            });
                                            }
                                        }).then(function (result) {
                                            $scope.tallyAttendance(result);
                                            $scope.postDiagnosis(objectData,confirmedDiagnoses);
                                        });
                                    }else
                                        $scope.postDiagnosis(objectData,confirmedDiagnoses);
                                });
                            }else{
                                    $scope.postDiagnosis(objectData,confirmedDiagnoses);
                            }
                        }
                        
                        $scope.postDiagnosis = function(objectData,confirmedDiagnoses){
                            $http.post('/api/postDiagnosis', objectData).then(function(data) {
                                swal("Diagnosis data successfully Saved!", "", "success");
                                $scope.tallyDiagnosis(confirmedDiagnoses);
                                $scope.diagnosisTemp = [];
                            });
                        }
                        
                        
                        $scope.tallyAttendance = function(attendance){
                            var patient_id = $scope.selectedPatient.patient_id;
                            var TallyRegister = {attempt:0, load: function(){
                                TallyRegister.attempt++;
                                $http.post('/api/'+(attendance.toLowerCase() == 'new' ? 'countNewAttendance' : 'countReattendance'),{facility_id:facility_id, dob: $scope.selectedPatient.dob,gender: $scope.selectedPatient.gender, clinic_id:1}).then(function(data){
                                    var Tally = {attempt:0, load: function(patient_id){
                                        Tally.attempt++;
                                        $http.post('/api/tallied',{patient_id: patient_id}).then(function(data){},function(data){if(Tally.attempt < 5) Tally.load(patient_id);});
                                    }};
                                    Tally.load(patient_id);                 
                                },function(data){if(TallyRegister.attempt < 5) TallyRegister.load();});
                            }}
                            TallyRegister.load();
                        }
        
                        $scope.tallyDiagnosis = function(confirmedDiagnoses){
                            if(confirmedDiagnoses.length == 0)
                                return;
                            
                            var data = {
                                        facility_id:facility_id, 
                                        dob: $scope.selectedPatient.dob,
                                        gender: $scope.selectedPatient.
                                        gender,
                                        concepts:confirmedDiagnoses
                                    };
                                    
                            var TallyRegister = {attempt:0, load: function(){
                                if(confirmedDiagnoses.length == 0)
                                    return;
                                
                                TallyRegister.attempt++;
                                $http.post('/api/countOPDDiagnosis',data).then(function(data){},function(data){if(TallyRegister.attempt < 5) TallyRegister.load();});
                            }};
                            TallyRegister.load();
                        }
                                
                        //Dispositions

                        var facilities = [];

                        $scope.showFacility = function(searchKey) {

                            $http.get('/api/getFacilities', {

                                "searchKey": searchKey

                            }).then(function(data) {



                                facilities = data.data;

                            });

                            return facilities;

                        }


                        
                         $scope.PrintContent1 = function() {
                             
                        var DocumentContainer = document.getElementById('refId');
                        var WindowObject = window.open("", "PrintWindow",
                            "width=900,height=700,top=50,left=450,toolbars=no,scrollbars=no,status=no,resizable=yes");
                        WindowObject.document.title = "printout: GoT-HOMIS";
                        WindowObject.document.writeln(DocumentContainer.innerHTML);
                        WindowObject.document.close();

                        setTimeout(function () {
                            WindowObject.focus();
                            WindowObject.print();
                            WindowObject.close();
                        });
                         }
$http.get('/api/getUsermenu/'+user_id).then(function(cardTitle){
            $scope.cardTitle=cardTitle.data[0];
        console.log($scope.cardTitle);
            });
        $http.get('/api/getLoginUserDetails/' + user_id).then(function (data) {
            $scope.loginUserFacilityDetails = data.data; 
            console.log(data.data);
             });
                        $scope.exReferral = function(patient, facility, ref) {

                            if (facility == null || ref == null) {

                                swal("Please fill all fields and click save", "", "error");

                                return;

                            }

                            var ext = {

                                "summary": ref.summary,
                                "referral_date":ref.referral_date,
                                "referral_time":ref.referral_time,
                                  "diagnosis":ref.diagnosis,
                                  "temperature":ref.temperature,
                                  "heart_rate":ref.heart_rate,
                                  "respiratory_rate":ref.respiratory_rate,
                                  "bp":ref.bp,
                                  "mental_status":ref.mental_status,
                                  "alert":ref.alert,
                                  "pertinent":ref.pertinent,
                                  "history":ref.history,
                                  "chronic_ediction":ref.chronic_ediction,
                                  "allergy":ref.allergy,
                                  "lab_result":ref.lab_result,
                                  "radiology_result":ref.radiology_result,
                                  "treatment":ref.treatment,
                                  "contact_person":ref.contact_person,
                                  "name":patient.first_name+" "+patient.middle_name+" "+patient.last_name,
                                  "age":patient.age,
                                  "gender":patient.gender,
                                  "reg":patient.medical_record_number,
                                "patient_id": patient.patient_id,
                                "account_id": patient.account_id,

                                "from_facility_id": facility_id,

                                "sender_id": user_id,

                                "to_facility_id": facility.id,

                                "referral_type": 1,

                                "status": 1

                            };

                            $http.post('/api/postReferral', ext).then(function(data) {

                                $scope.ref == null;
 $('#summary').val("");
 $scope.refdata=data.data.data;
 
                             $scope.msg=data.data.msg;
                            $scope.status=data.data.status;
                            if($scope.status==200){
                                  swal($scope.msg,"","success");

                            }
                            else{
  swal($scope.msg,"","error");
                            }

                            });



                        }



                        $scope.patientAdmission = function(item, patient) {
                            $mdDialog.cancel();
                            if (angular.isDefined(patient) == false) {
                                swal("Oops! something went wrong..", "Please search and select Patient then click ward button to admit patient!");
                                return;
                            }
                            var object = angular.extend({}, item, patient);
                            $scope.item = object;
                             
  var modalInstance = $uibModal.open({
    templateUrl: '/views/modules/ClinicalServices/admission.html',
                                size: 'lg',
                                animation: true,
                                controller: 'admissionModal',
                                resolve:{
                                    object: function () {
                                        return object;
                                    }
                                }
                            });
                        };

                        $scope.internalTransfer = function(clinic, patient) {



                            var patientDetails = {

                                "patient_id": patient.patient_id,

                                "main_category_id": patient.main_category_id,

                                "bill_id": patient.bill_id,

                                "sender_clinic_id": 1,

                                "first_name": patient.first_name,

                                "middle_name": patient.middle_name,

                                "last_name": patient.last_name,

                                "medical_record_number": patient.medical_record_number,

                                "gender": patient.gender,

                                "dept_id": clinic.id,

                                "dob": patient.dob,

                                "visit_id": patient.account_id

                            };

                            var object = angular.extend({}, clinic, patientDetails);

                            var modalInstance = $uibModal.open({

                                templateUrl: '/views/modules/ClinicalServices/internalTransfer.html',

                                size: 'lg',

                                animation: true,

                                controller: 'admissionModal',

                                resolve: {

                                    object: function() {

                                        return object;

                                    }

                                }

                            });
                            //$uibModalInstance.dismiss();

                        }

                        //Treatments:medication and procedures
                    $scope.getRejected = function (item) {
                        $http.post('/api/rejectedMedicines',{patient_id:item.patient_id,account_id:item.account_id}).then(function (data) {
                           $scope.rejectedMedicines = data.data;
                        });
                    }
                    $scope.updateMedicine = function (item) {
                        $http.post('/api/updateMedicines',item).then(function (data) {
                            if(data.data.status == 1){
                                swal(data.data.msg,'','success');
                            }
                        });
                    }
                        var mediData = [];

                        $scope.medicines = [];
                        $scope.medicinesOs = [];
                        $scope.searchItems = function(searchKey, patient) {
                            var pay_id = patient.bill_id;
                            if (pay_id == null) {
                                swal("Please search patient to be prescribed before searching Medicine!");
                                return;
                            }
                            if (patient.main_category_id == 3) {
                                pay_id = 1;
                            }
                            $http.post('/api/getMedicine', {
                                "search": searchKey,
                                "facility_id": facility_id,
                                "patient_category_id": pay_id
                            }).then(function(data) {
                                mediData = data.data;
                            });
                            return mediData;
                        }
                        var balance = [];
                         
                        //adding from list prescription starts
                        $scope.addMedicineFromList = function(item, patient, dawa,instructions) {
                            console.log(item);
                            console.log(patient);
                            console.log(dawa);
                            console.log(instructions);
                            var status_id = 1;

                            var filter = patient.bill_id;

                            var main_category = patient.main_category_id;

                            if (patient == null) {

                                swal("Please search and select Patient to prescribe");

                                return;

                            }

                            if (dawa == null) {

                                swal("Please search and select medicine!");

                                return;

                            }
                            if (!instructions) {
                              instructions = '';

                            }
                            for (var i = 0; i < $scope.medicines.length; i++)

                                if ($scope.medicines[i].item_id == dawa.item_id) {

                                    swal(dawa.item_name + " already in your order list!");

                                    return;

                                }

                            if (main_category != 1 && dawa.exemption_status == 0) {

                                filter = patient.bill_id;

                            }
                            if (main_category == 3 && dawa.exemption_status == 1) {

                                filter = 1;

                            }

                            if (main_category == 2 && dawa.exemption_status == 1) {

                                filter = patient.bill_id;

                            }

                            if (main_category == 3) {

                                main_category = 1;

                            }

                            $http.post('/api/balanceCheck', {

                                "main_category_id": main_category,

                                "item_id": dawa.item_id,

                                "facility_id": facility_id, "user_id": user_id

                            }).then(function(data) {

                                balance = data.data;

                                if (balance.length > 0 ) {
                                    $scope.medicines.push({

                                        "facility_id": facility_id,"item_type_id": dawa.item_type_id,"item_price_id": dawa.price_id,

                                        "quantity": '',"status_id": status_id,"dose": item.dose,"frequency": item.frequency,

                                        "duration": item.duration, "instructions":instructions,

                                        "out_of_stock": "", "payment_filter": filter,"account_number_id": patient.account_id,"visit_id": patient.account_id,

                                        "admission_id": '',"patient_id": patient.patient_id,"user_id": user_id,"item_id": dawa.item_id,

                                        "item_name": dawa.item_name,"dose_formulation": dawa.dose_formulation

                                    });
                                    $('#item_search').val('');
                                    $('#dose').val('');
                                    $('#frequency').val('');
                                    $('#duration').val('');
                                    $('#instruction').val('');

                                } else if (balance.length  < 1 ) {
                                    //new swal start
                                    swal({
                                        title: 'This item is not available in Store..Do you want to prescribe anyway?',
                                        text: "",
                                        type: 'warning',
                                        showCancelButton: true,
                                        confirmButtonColor: '#3085d6',
                                        cancelButtonColor: '#d33',
                                        confirmButtonText: 'Yes!  ',
                                        cancelButtonText: 'No, cancel!',
                                        confirmButtonClass: 'btn btn-success',
                                        cancelButtonClass: 'btn btn-danger',
                                        buttonsStyling: false
                                    }).then(function () {
                                        for (var i = 0; i < $scope.medicinesOs.length; i++)
                                            if ($scope.medicinesOs[i].item_id == dawa.item_id) {
                                                swal("Item already in your order list!");
                                                return;
                                            }

                                        $scope.medicinesOs.push({
                                            "facility_id": facility_id,
                                            "item_type_id": dawa.item_type_id,
                                            "item_price_id": dawa.price_id,
                                            "quantity": '',
                                            "status_id": status_id,
                                            "dose": item.dose,
                                            "frequency": item.frequency,
                                            "duration": item.duration,
                                            "instructions":instructions,
                                            "out_of_stock": "OS",
                                            "account_number_id": patient.account_id,
                                            "visit_id": patient.account_id,
                                            "admission_id": '',
                                            "patient_id": patient.patient_id,
                                            "user_id": user_id,
                                            "item_id": dawa.item_id,
                                            "item_name": dawa.item_name
                                        });
                                        swal("Item added under Out of Stock category", "", "success");
                                        $('#item_search').val('');
                                        $('#dose').val('');
                                        $('#frequency').val('');
                                        $('#duration').val('');
                                        $('#instruction').val('');
                                    }, function (dismiss) {
                                        if (dismiss === 'cancel') {
                                            swal("Canceled", "choose different Item for Prescription", "info");
                                            return;
                                        }
                                    })
                                    //new swal end
                                }
                            });

                        }
                        //adding from list prescription ends
                          $scope.checkDosage = function(item_id, patient_id,visit_date_id) {
                        var item_name = item_id.item_name;
                        $http.post('/api/getPrevDiagnosisConfirmed', {
                            "patient_id": item.patient_id,
                            "visit_date_id": visit_date_id

                        }).then(function(data) {

                             

                        });

                        $http.post('/api/dosageChecker', {
                            "item_id": item_id.item_id,
                            "patient_id": patient_id
                        }).then(function(data) {
                            if (data.data.length > 0) {
                                var diff = data.data[0].duration - data.data[0].days;
                                $scope.dosageCheck = data.data;
                                swal('ATTENTION', item_name + ' In Dosage Progress', 'info');


                            }

                        });
                    }
                    $scope.addMedicine = function(item, patient, dawa,instructions) {
                        var status_id = 1;

                        var filter = patient.bill_id;

                        var main_category = patient.main_category_id;

                        $http.post('/api/getPrevDiagnosisConfirmed', {
                            "patient_id": patient.patient_id,
                            "visit_date_id": patient.account_id

                        }).then(function(data) {

                            if (data.data.length <1) {

                                swal('ATTENTION',  ' Please Confirm Diagnosis for This Patient First', 'error');
                                return;
                            }
                            else{

                                if (patient == null) {

                                    swal("Please search and select Patient to prescribe");

                                    return;

                                }

                                if (dawa == null) {

                                    swal("Please search and select medicine!");

                                    return;

                                }
                                if (!instructions) {
                                    instructions = '';

                                }
                                for (var i = 0; i < $scope.medicines.length; i++)

                                    if ($scope.medicines[i].item_id == dawa.item_id) {

                                        swal(dawa.item_name + " already in your order list!");

                                        return;

                                    }

                                if (main_category != 1 && dawa.exemption_status == 0) {

                                    filter = patient.bill_id;

                                }
                                if (main_category == 3 && dawa.exemption_status == 1) {

                                    filter = 1;

                                }

                                if (main_category == 2 && dawa.exemption_status == 1) {

                                    filter = patient.bill_id;

                                }

                                if (main_category == 3) {

                                    main_category = 1;

                                }

                                $http.post('/api/balanceCheck', {

                                    "main_category_id": main_category,

                                    "item_id": dawa.item_id,

                                    "facility_id": facility_id, "user_id": user_id

                                }).then(function(data) {

                                    balance = data.data[0].balance;

                                    if (balance > 0 ) {
                                        $scope.medicines.push({

                                            "facility_id": facility_id,"item_type_id": dawa.item_type_id,"item_price_id": dawa.price_id,

                                            "quantity": '',"status_id": status_id,"dose": item.dose,"frequency": item.frequency,

                                            "duration": item.duration, "instructions":instructions,

                                            "out_of_stock": "", "payment_filter": filter,"account_number_id": patient.account_id,"visit_id": patient.account_id,

                                            "admission_id": '',"patient_id": patient.patient_id,"user_id": user_id,"item_id": dawa.item_id,

                                            "item_name": dawa.item_name,"dose_formulation": dawa.dose_formulation

                                        });
                                        $('#item_search').val('');
                                        $('#dose').val('');
                                        $('#frequency').val('');
                                        $('#duration').val('');
                                        $('#instruction').val('');

                                    } else if (balance < 1 ) {
                                        //new swal start
                                        swal({
                                            title: 'This item is not available in Store..Do you want to prescribe anyway?',
                                            text: "",
                                            type: 'warning',
                                            showCancelButton: true,
                                            confirmButtonColor: '#3085d6',
                                            cancelButtonColor: '#d33',
                                            confirmButtonText: 'Yes!  ',
                                            cancelButtonText: 'No, cancel!',
                                            confirmButtonClass: 'btn btn-success',
                                            cancelButtonClass: 'btn btn-danger',
                                            buttonsStyling: false
                                        }).then(function () {
                                            for (var i = 0; i < $scope.medicinesOs.length; i++)
                                                if ($scope.medicinesOs[i].item_id == dawa.item_id) {
                                                    swal("Item already in your order list!");
                                                    return;
                                                }

                                            $scope.medicinesOs.push({
                                                "facility_id": facility_id,
                                                "item_type_id": dawa.item_type_id,
                                                "item_price_id": dawa.price_id,
                                                "quantity": '',
                                                "status_id": status_id,
                                                "dose": item.dose,
                                                "frequency": item.frequency,
                                                "duration": item.duration,
                                                "instructions":instructions,
                                                "out_of_stock": "OS",
                                                "account_number_id": patient.account_id,
                                                "visit_id": patient.account_id,
                                                "admission_id": '',
                                                "patient_id": patient.patient_id,
                                                "user_id": user_id,
                                                "item_id": dawa.item_id,
                                                "item_name": dawa.item_name
                                            });
                                            swal("Item added under Out of Stock category", "", "success");
                                            $('#item_search').val('');
                                            $('#dose').val('');
                                            $('#frequency').val('');
                                            $('#duration').val('');
                                            $('#instruction').val('');
                                        }, function (dismiss) {
                                            if (dismiss === 'cancel') {
                                                swal("Canceled", "choose different Item for Prescription", "info");
                                                return;
                                            }
                                        })
                                        //new swal end
                                    }
                                });

                            }

                        });


                    }
                        $scope.saveMedicine = function() {
                            if ($scope.medicines == "" && $scope.medicinesOs == "") {
                                swal("No Items to Save,Please choose items..", "", "error");
                                return;
                            }

                            if ($scope.medicines != "") {
                                $http.post('/api/postMedicines', $scope.medicines).then(function(data) {

                                });
                                $scope.medicines = [];
                            }

                            $http.post('/api/outOfStockMedicine', $scope.medicinesOs).then(function(data) {

                            });
                            swal("Patient successfully prescribed!", "", "success");
                            $scope.medicinesOs = [];
                        }

                        $scope.prevMedics = function(item) {
                            $http.post('/api/getPrevMedicine', {
                                "patient_id": item.patient_id
                            }).then(function(data) {
                                $scope.prevMedicines = data.data;
                            });
                        }

                        $scope.prevProcedure = function(item) {
                            $http.post('/api/getPrevProcedures', {
                                "patient_id": item.patient_id
                            }).then(function(data) {
                                $scope.prevProcedures = data.data;
                            });
                        }

                        //medical supplies starts
                        var supplies = [];
                        var balance02 = [];
                        $scope.supplies = [];
                        $scope.suppliesOS = [];

                        $scope.searchMediSupplies = function(searchKey, patient) {
                            var pay_id = patient.bill_id;

                            if (pay_id == null) {
                                swal("Please search patient before searching medical supplies!");
                                return;
                            }

                            if (patient.main_category_id == 3) {
                                pay_id = 1;
                            }

                            $http.post('/api/getMedicalSupplies', {
                                "search": searchKey,
                                "facility_id": facility_id,
                                "patient_category_id": pay_id

                            }).then(function(data) {
                                supplies = data.data;
                            });
                            return supplies;
                        }

                        $scope.addSupplies = function(patient, qty, item) {
                            var status_id = 1;
                            var filter = patient.bill_id;
                            var main_category = patient.main_category_id;
                            var quantity = qty;
                            for (var i = 0; i < $scope.supplies.length; i++)
                                if ($scope.supplies[i].item_id == item.item_id) {
                                    swal(item.item_name + " already in your order list!", "", "info");
                                    return;
                                }
                            if (main_category != 1 && item.exemption_status == 0) {
                                filter = patient.bill_id;
                            }

                            if (main_category == 3 && item.exemption_status == 1) {
                                filter = 1;
                            }

                            if (main_category == 2 && item.exemption_status == 1) {
                                filter = patient.bill_id;
                            }

                            if (main_category == 3) {
                                main_category = 1;
                            }

                            $http.post('/api/balanceCheck', {
                                "main_category_id": main_category,
                                "item_id": item.item_id,
                                "facility_id": facility_id, "user_id": user_id
                            }).then(function(data) {
                                balance02 = data.data;
                                if (balance02.length < 1) {
                                    swal(item.item_name + ' is not available in store.', 'Contact store manager', 'info');
                                    return;
                                } else if (balance02.length > 0 && balance02[0].balance >= quantity) {
                                    $scope.supplies.push({
                                        "visit_id": patient.account_id,
                                        "admission_id": '',
                                        "out_of_stock": '',
                                        "payment_filter": filter,
                                        "facility_id": facility_id,
                                        "item_type_id": item.item_type_id,
                                        "item_price_id": item.price_id,
                                        "quantity": qty,
                                        "status_id": status_id,
                                        "account_number_id": patient.account_id,
                                        "patient_id": patient.patient_id,
                                        "user_id": user_id,
                                        "item_id": item.item_id,
                                        "item_name": item.item_name
                                    });
                                    $('#supplies').val('');
                                    $('#supply_qty').val('');
                                } else if (balance02.length < 1 || balance02[0].balance < quantity) {
                                    var conf = confirm("This Item is not available in Store..Do you want to select it anyway?", "", "info");
                                    if (conf == true) {
                                        for (var i = 0; i < $scope.suppliesOS.length; i++)
                                            if ($scope.suppliesOS[i].item_id == item.item_id) {
                                                swal(item.item_name + " already in your order list!");
                                                return;
                                            }

                                        $scope.suppliesOS.push({
                                            "visit_id": patient.account_id,
                                            "admission_id": '',
                                            "out_of_stock": 'OS',
                                            "payment_filter": filter,
                                            "facility_id": facility_id,
                                            "item_type_id": item.item_type_id,
                                            "item_price_id": item.price_id,
                                            "quantity": qty,
                                            "status_id": status_id,
                                            "account_number_id": patient.account_id,
                                            "patient_id": patient.patient_id,
                                            "user_id": user_id,
                                            "item_id": item.item_id,
                                            "item_name": item.item_name
                                        });
                                        $('#supplies').val('');
                                        $('#supply_qty').val('');
                                        swal("Item added under Out of Stock category", "", "success");
                                    } else {
                                        swal("canceled", "Choose different Item", "info");
                                        $('#supplies').val('');
                                        $('#supply_qty').val('');
                                        return;
                                    }
                                }
                            });

                        }

                        $scope.saveSupplies = function() {
                            if ($scope.supplies == "" && $scope.suppliesOS == "") {
                                swal("No Items to Save,Please choose items..", "", "error");
                                return;
                            }

                            if ($scope.supplies != "") {
                                $http.post('/api/postMedicalSupplies', $scope.supplies).then(function(data) {
                                    if(data.data.status == 1){
                                        swal("",data.data.msg, "success");
                                    }
                                });
                            }

                            $http.post('/api/outOfStockMedicalSupplies', $scope.suppliesOS).then(function(data) {
                            if(data.data.status == 1){
                                swal("",data.data.msg, "success");
                            }
                            });
                            $scope.supplies = [];
                            $scope.suppliesOS = [];
                        }

                        //medical supplies ends

                        //procedures

                        var procedureData = [];

                        $scope.procedures = [];

                        $scope.searchProcedures = function(searchKey, patient) {
                            var pay_id = patient.bill_id;
                            if (pay_id == null) {
                                swal("Please search patient before searching procedures!");
                                return;
                            }

                            if (patient.main_category_id == 3) {
                                pay_id = 1;
                            }

                            $http.post('/api/getPatientProcedures', {
                                "search": searchKey,
                                "facility_id": facility_id,
                                "patient_category_id": pay_id
                            }).then(function(data) {
                                procedureData = data.data;
                            });
                            return procedureData;
                        }

                        $scope.getDefaultProcedures = function(patient) {

                            var pay_id = patient.bill_id;
                            if (pay_id == null) {
                                swal("Please search patient before searching procedures!");
                                return;
                            }

                            if (patient.main_category_id == 3) {
                                pay_id = 1;
                            }

                            $http.post('/api/getProcedures', {
                                "facility_id": facility_id,
                                "patient_category_id": pay_id
                            }).then(function(data) {
                                $scope.defaultProcedures = data.data;
                            });

                        }

                        $scope.addProcedure = function(patient,item, qty) {
                            var filter = patient.bill_id;
                            var status_id = 1;
                            var sub_category_name = patient.sub_category_name.toLowerCase();
                            var main_category = patient.main_category_id;

                            if (patient.patient_id == null) {
                                swal("Please search and select Patient to prescribe");
                                return;
                            }

                            if (item.item_id == null) {
                                swal("Please search and select Procedure!");
                                return;
                            }

                            for (var i = 0; i < $scope.procedures.length; i++)
                                if ($scope.procedures[i].item_id == item.item_id) {
                                    swal(item.item_name + " already in your order list!", "", "info");
                                    return;
                                }
                            if (main_category != 1 && item.exemption_status == 0) {
                                filter = patient.bill_id;
                            }

                            if (main_category == 3 && item.exemption_status == 1) {
                                filter = 1;
                            }

                            if (main_category == 2 && item.exemption_status == 1) {
                                filter = patient.bill_id;
                            }
                            $scope.getsumCHFBill1 = function () {
                                var sumCHFBill=0;
                                for(var i = 0; i <  $scope.procedures.length ; i++) {
                                    sumCHFBill -= -( $scope.procedures[i].price);
                                }
                                return sumCHFBill;
                            }
                            if(sub_category_name=='chf') {
                                $scope.procedures.push({
                                    "payment_filter": filter,
                                    "admission_id": '',
                                    "facility_id": facility_id,
                                    "item_type_id": item.item_type_id,
                                    "item_price_id": item.price_id,
                                    "quantity": qty,
                                    "status_id": status_id,
                                    "account_number_id": patient.account_id,
                                    "patient_id": patient.patient_id,
                                    "user_id": user_id,
                                    "item_id": item.item_id,
                                    "item_name": item.item_name
                                });
                                $http.post('api/chfCheckBills', {
                                    patient_id: patient_id,
                                    account_id: account_id
                                }).then(function (data) {
                                    $scope.totalCHFBils = data.data[0];
                                    $scope.chf_item = data.data[1][0];
                                    $scope.chf_ceiling = parseInt(data.data[2].original.chf_ceiling);
                                    var using = data.data[2].original.use_chf_settings;
                                    if (using == 1) {
                                        $scope.BillGenerated = $scope.getsumCHFBill($scope.procedures);
                                    }


                                    var billed = $scope.totalCHFBils;
                                    var currentBill = $scope.BillGenerated;
                                    var ceiling = $scope.chf_ceiling;
                                    var totalBill = currentBill - (-billed);
                                    var difference = totalBill - ceiling;
                                    if (difference > 0 && using == 1) {
                                        console.log('CHF TOP UP ' + difference);
                                        $scope.chf_top_up = difference;

                                        $scope.procedures.push({
                                            "payment_filter": filter,
                                            "admission_id": '',
                                            "facility_id": facility_id,
                                            "item_type_id": $scope.chf_item.item_type_id,
                                            "item_price_id": $scope.chf_item.item_price_id,
                                            "price": $scope.chf_item.price,
                                            'quantity':$scope.chf_top_up/$scope.chf_item.price,
                                            "status_id": status_id,
                                            "account_number_id": patient.account_id,
                                            "patient_id": patient.patient_id,
                                            "user_id": user_id,
                                            "item_id": item.item_id,
                                            "item_name": $scope.chf_item.item_name,
                                        });
                                        $('#procedures').val('');
                                    }
                                });

                            }
                            else{
                                $scope.procedures.push({
                                    "payment_filter": filter,
                                    "admission_id": '',
                                    "facility_id": facility_id,
                                    "item_type_id": item.item_type_id,
                                    "item_price_id": item.price_id,
                                    "quantity": qty,
                                    "status_id": status_id,
                                    "account_number_id": patient.account_id,
                                    "patient_id": patient.patient_id,
                                    "user_id": user_id,
                                    "item_id": item.item_id,
                                    "item_name": item.item_name
                                });
                                $('#procedures').val('');
                            }

                        }

                        $scope.saveProcedures = function(objectData) {
                            $http.post('/api/postPatientProcedures', objectData).then(function(data) {

                            });
                            swal("Patient procedures successfully saved!", "", "success");
                            $scope.procedures = [];
                        }
                        $scope.saveConservative = function (patient,preData) {
                            var dt = {
                                patient_id:patient.patient_id,visit_id:patient.account_id,prescriber_id:user_id,conservatives:preData
                            };
                            $http.post('/api/conservatives',dt).then(function (data) {
                               if(data.data.status==1){
                                   swal('',data.data.msg,'success');
                               }
                               $('#conservation').val('');
                            });
                        }
                        $scope.deceased = function(item, corpse,diag) {

                            if (angular.isDefined(corpse) == false) {
                                swal("An error occurred", "Data not saved...Please write causes of death and click send to last office button", "error");
                                return;
                            }

                            var deceased = {
                                "first_name": item.first_name,
                                "middle_name": item.middle_name,
                                "last_name": item.last_name,
                                "patient_id": item.patient_id,
                                "death_certifier": user_id,
                                "diagnosis_id":diag.id,
                                "diagnosis_code":diag.code,
                                "user_id": user_id,
                "residence_id":item.residence_id,
                                "facility_id": facility_id,
                                "immediate_cause": corpse.immediate_cause,
                                "underlying_cause": corpse.underlying_cause,
                                "dept_id": 1
                            };

                            $http.post('/api/postDeceased', deceased).then(function(data) {

                                if (data.data.status == 0) {
                                    swal(data.data.data, "", "error");
                                } else {
                                    swal(item.first_name + ' ' + item.last_name + " sent to Last office", "", "success");
                                }
                            });
                        }
                    },
                    templateUrl: '/views/modules/ClinicalServices/consultationModal.html',
                    parent: angular.element(document.body),
                    clickOutsideToClose: false,
                    fullscreen: true,
                });
        };
        $scope.getCorpseModal = function(item) {
            var  object = item;
            var modalInstance = $uibModal.open({
                templateUrl: '/views/modules/ClinicalServices/deceased.html',
                size: 'lg',
                animation: true,
                controller: 'admissionModal',
                resolve: {
                    object: function() {
                        return object;
                    }
                }
            });
        }
        $scope.billsCancellation = function() {
            $http.post('/api/getBillList', {
                "facility_id": facility_id
            }).then(function(data) {
                $scope.patientBill = data.data;
            });
        }
        $scope.getBillModal = function(item) {
            $mdDialog.show({
                controller: function ($scope) {
                    $scope.cancel = function () {
                        $mdDialog.cancel();
                    };
                    $http.post('/api/cancelPatientBill', {
                    "patient_id": item.patient_id,
                    "receipt_number": item.receipt_number,
                    "facility_id": facility_id
                    }).then(function(data) {
                        $scope.patientBills=data.data;
                        $scope.toto = function() {
                            var total = 0;
                            for (var i = 0; i < $scope.patientBills.length; i++) {
                                total -= -($scope.patientBills[i].quantity * $scope.patientBills[i].price - $scope.patientBills[i].discount);
                            }
                            return total;
                        }
                    });
                    $scope.cancelBill = function(item) {
                        $http.post('/api/cancelBillItem', {
                            "id": item.id,
                            "patient_id": item.patient_id,
                            "facility_id": facility_id,
                            "user_id": user_id
                        }).then(function(data) {
                            for(var i = 0; i < $scope.patientBills.length; i++)
                                if($scope.patientBills[i].id == item.id)
                                    $scope.patientBills.splice(i);
                            if($scope.patientBills.length == 0)
                                $scope.cancel();
                        });
                    }

                },
                templateUrl: '/views/modules/ClinicalServices/billCancellationModal.html',
                parent: angular.element(document.body),
                clickOutsideToClose: false,
                fullscreen: true,
            });
        }
$scope.gbv_vac_panel = function (clientInfo) {
            if (clientInfo !=undefined) {



                $mdDialog.show({
                    controller: function ($scope) {

                        $scope.patientIfor=clientInfo;

                        $scope.SocialWelfareDataHistorory=function(){
                            $http.post('/api/SocialWelfareDataHistorory',{patient_id:clientInfo.patient_id}).then(function (data) {

                                $scope.socialHistories=data.data;

                            });
                        }
                        $scope.SocialWelfareDataHistorory();



                        $scope.UpdateSocialWelfareData=function(record){
                            if(record.event_date !=undefined){
                                var appointDate = record.event_date;


                                if (record.event_date instanceof Date) {
                                    appointDate = record.event_date.toISOString();
                                }
                                if (record.event_date == undefined) {
                                    return;
                                }


                                if (appointDate != '' && ((new Date()).getFullYear() < parseInt(appointDate.substring(0, 4)) ||
                                    ((new Date()).getFullYear() == parseInt(appointDate.substring(0, 4)) && ((new Date()).getMonth() + 1)< parseInt(appointDate.substring(appointDate.indexOf("-") + 1, 7))) ||
                                    ((new Date()).getFullYear() == parseInt(appointDate.substring(0, 4)) && ((new Date()).getMonth() + 1) == parseInt(appointDate.substring(appointDate.indexOf("-") + 1, 7)) && ((new Date()).getDate()) < parseInt(appointDate.substring(appointDate.lastIndexOf("-") + 1, 10))))) {


                                    swal('Future dates Restricted!', '', 'warning');
                                    return;
                                }

                            }
                            $http.post('/api/UpdateSocialWelfareData',record).then(function (data) {

                                $scope.social=data.data;
                                if (data.data.status==1) {
                                    swal("",data.data.msg,"success");
                                }
                                else{
                                    swal("","Failed","error");
                                }


                                $scope.SocialWelfareDataHistorory();

                            });
                        }
                        $scope.SocialWelfareData=function(record){
                            if (record==undefined) {
                                swal("","No any record found to save","info");
                                return;
                            }
                            if(record.event_date !=undefined){
                                var appointDate = record.event_date;


                                if (record.event_date instanceof Date) {
                                    appointDate = record.event_date.toISOString();
                                }
                                if (record.event_date == undefined) {
                                    return;
                                }


                                if (appointDate != '' && ((new Date()).getFullYear() < parseInt(appointDate.substring(0, 4)) ||
                                    ((new Date()).getFullYear() == parseInt(appointDate.substring(0, 4)) && ((new Date()).getMonth() + 1)< parseInt(appointDate.substring(appointDate.indexOf("-") + 1, 7))) ||
                                    ((new Date()).getFullYear() == parseInt(appointDate.substring(0, 4)) && ((new Date()).getMonth() + 1) == parseInt(appointDate.substring(appointDate.indexOf("-") + 1, 7)) && ((new Date()).getDate()) < parseInt(appointDate.substring(appointDate.lastIndexOf("-") + 1, 10))))) {


                                    swal('Future dates Restricted!', '', 'warning');
                                    return;
                                }

                            }

                            var records={"followup":record.followup,"vulnerable":record.vulnerable,"screening":record.screening,
                                "within_72_hrs":record.within_72_hrs,
                                "pt_result":record.pt_result,"hiv_result":record.hiv_result,"sti_result":record.sti_result,
                                "disability":record.disability,
                                "incoming_referral":record.incoming_referral,
                                "internal_referral":record.internal_referral,
                                "outgoing_referral":record.outgoing_referral,

                                "referred_to":record.referred_to,
                                "dept_name":record.dept_name,
                                "incoming_from":record.incoming_from,

                                "event_date":record.event_date,
                                "pv_violence":record.pv_violence,"sv_violence":record.sv_violence, "ev_violence":record.ev_violence,
                                "ng_violence":record.ng_violence, "fi_service":record.fi_service, "im_service":record.im_service,
                                "c_service":record.c_service, "pep_service":record.pep_service, "sti_service":record.sti_service,
                                "ec_service":record.ec_service, "fp_service":record.fp_service,"p_service":record.p_service,
                                "la_service":record.la_service,"sws_service":record.sws_service,
                                user_id:user_id,
                                "patient_id":clientInfo.patient_id,"dob":clientInfo.dob,
                                "residence_name":clientInfo.residence_name,"gender":clientInfo.gender,
                                "client_name":clientInfo.first_name+" "+clientInfo.middle_name+" "+clientInfo.last_name,
                                "medical_record_number":clientInfo.medical_record_number,"mobile_number":clientInfo.mobile,
                                "facility_id":facility_id};
                            $http.post('/api/SocialWelfareData',records).then(function (data) {

                                $scope.social=data.data;
                                if (data.data.status==1) {
                                    swal("",data.data.msg,"success");
                                }
                                else{
                                    swal("","Failed","error");
                                }

                                $scope.SocialWelfareDataHistorory();

                            });
                        }



                        $scope.cancel = function () {
                            $mdDialog.hide();

                        };




                    },
                    templateUrl: '/views/modules/Exemption/gbv_vac.html',
                    parent: angular.element(document.body),
                    clickOutsideToClose: true,
                    fullscreen: false,
                });

            }
        }

$scope.getAllPatient = function (text) {
            return Helper.getAllPatient(text)
                .then(function (response) {
                    $scope.selectedPatient=response.data;
                    $scope.selectPatient=response.data;
                    return response.data;
                });

        };
        $scope.DisplayInfors = function (datee) {

            $scope.selectedPatientInf=datee;
            if (datee.patient_id >0) {
                $scope.gbv_vac_panel(datee);
            }


        };
$scope.getPerformance = function (item) {
            var perfData = {facility_id:facility_id,user_id:user_id,start:item.start,end:item.end};
            $http.post('/api/doctorsPerformance',perfData).then(function (data) {
                $scope.performanceRange = data.data[0];
                $scope.performanceThisMonth = data.data[1];
            });
        }
		$scope.startup = function(){
			$scope.getTraumaList();

		}
		
		$scope.startup();
    }
})();