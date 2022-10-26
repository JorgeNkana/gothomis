(function() {

    'use strict';
     var app = angular.module('authApp');
     app.controller('TriageController',
    ['$scope','$http','$rootScope','$mdDialog',
    function($scope,$http,$rootScope,$mdDialog) {
		var user_id = $rootScope.currentUser.id;
		var facility_id = $rootScope.currentUser.facility_id;

		$scope.showRegisterForm=function(){
			$mdDialog.show({
				scope: $scope,
				//controller: TriageController,
				controller: function(){

                     //age calculation
        $scope.exemption_calculateAge = function (patient, source) {

            var dob = patient.dob;


            if (patient.dob instanceof Date) {
                dob = patient.dob.toISOString();
            }
            if (patient.dob == undefined && patient.age == undefined) {
                return;
            }


            if (dob != '' && source == 'date' && ((new Date()).getFullYear() < parseInt(dob.substring(0, 4)) ||
                ((new Date()).getFullYear() == parseInt(dob.substring(0, 4)) && ((new Date()).getMonth() + 1) < parseInt(dob.substring(dob.indexOf("-") + 1, 7))) ||
                ((new Date()).getFullYear() == parseInt(dob.substring(0, 4)) && ((new Date()).getMonth() + 1) == parseInt(dob.substring(dob.indexOf("-") + 1, 7)) && ((new Date()).getDate()) < parseInt(dob.substring(dob.lastIndexOf("-") + 1, 10))))) {
                $scope.patient.dob = undefined;
                $scope.patient.age_unit = "";
                $scope.patient.age = "";
                swal('Future dates not allowed!', '', 'warning');
                return;
            }

            if (source == 'age') {
                $scope.patient.dob = new Date((new Date().getFullYear() - patient.age) + '-01-01');
                $scope.patient.age_unit = 'Years';

            } else if (source == 'date') {
                $scope.patient.dob = dob.replace(/\//g, '-');
                var days = Math.floor(((new Date()) - new Date(dob.substring(0, 4) + '-' + dob.substring(dob.indexOf("-") + 1, 7) + '-' + dob.substring(dob.lastIndexOf("-") + 1, 10))) / (1000 * 60 * 60 * 24));
                if (days > 365) {
                    $scope.patient.age = Math.floor(days / 365);
                    $scope.patient.age_unit = 'Years';
                } else if (days > 30) {
                    $scope.patient.age = Math.floor(days / 30);
                    $scope.patient.age_unit = 'Months';
                } else {
                    $scope.patient.age = days;
                    $scope.patient.age_unit = 'Days';
                }
            } else {
                if (patient.age_unit == 'Years')
                    $scope.exemption_calculateAge('age');
                else if (patient.age_unit == 'Months') {
                    if (((new Date()).getMonth() + 1) >= (patient.age % 12))
                        $scope.patient.dob = ((new Date()).getFullYear() - ~~(patient.age / 12)) + '-' + ((((new Date()).getMonth() + 1) - (patient.age % 12)).toString().length == 2 ? '' : '0') + (((new Date()).getMonth() + 1) - (patient.age % 12)) + '-01';
                    else
                        $scope.patient.dob = ((new Date()).getFullYear() - 1 - ~~(patient.age / 12)) + '-' + (((12 + ((new Date()).getMonth() + 1)) - (patient.age % 12)).toString().length == 2 ? '' : '0') + ((12 + ((new Date()).getMonth() + 1)) - (patient.age % 12)) + '-01';
                } else {
                    if (((new Date()).getDate()) >= (patient.age % 30))
                        $scope.patient.dob = ((new Date()).getFullYear() - ~~(patient.age / 365)) + '-' + ((((new Date()).getMonth() + 1) - ~~(patient.age / 30)).toString().length == 2 ? '' : '0') + (((new Date()).getMonth() + 1) - ~~(patient.age / 30)) + '-' + (patient.age.toString().length == 2 ? '' : '0') + patient.age.toString();
                    else
                        $scope.patient.dob = ((new Date()).getFullYear() - ~~(patient.age / 365)) + '-' + ((((new Date()).getMonth()) - ~~(patient.age / 30)).toString().length == 2 ? '' : '0') + (((new Date()).getMonth()) - ~~(patient.age / 30)) + '-' + (((30 + ((new Date()).getDate())) - (patient.age % 30)).toString().length == 2 ? '' : '0') + ((30 + ((new Date()).getDate())) - (patient.age % 30));
                }
            }
        };
					$scope.addClient = function(patient) {

       
						
						if (patient==undefined){
							swal({
								  title: 'MANDTATORY FIELDS',
								  html: 'Please, provide <span style="color:red">two names (surname, firstname)</span>',
								  type: 'warning',
								  showCancelButton: false
								});
							return;
						}
						if (patient.names.split(',').length != 2 ||  patient.names.split(',')[0] == '' || patient.names.split(',')[1] == ''){
							swal({
								  title: 'MANDTATORY FIELDS',
								  html: 'Please, provide <span style="color:red">two names (surname, firstname)</span>',
								  type: 'warning',
								  showCancelButton: false
								});
							return;
						}
                        if (patient.gender == undefined  ){
                            swal({
                                title: 'MANDTATORY FIELDS',
                                html: 'Please, provide <span style="color:red">Gender</span> ',
                                type: 'warning',
                                showCancelButton: false
                            });
                            return;
                        }
                        if (patient == undefined || patient.dob == undefined && patient.estimated_age == undefined && patient.estimated_age_group == undefined){
                            swal({
                                title: 'MANDTATORY FIELDS',
                                html: 'Please, provide <span style="color:red">DoB</span> or one of the age estimators',
                                type: 'warning',
                                showCancelButton: false
                            });
                            return;
                        }
                        if (patient.incident_location == undefined  ){
                            swal({
                                title: 'MANDTATORY FIELDS',
                                html: 'Please, provide <span style="color:red">Incident Location</span> ',
                                type: 'warning',
                                showCancelButton: false
                            });
                            return;
                        }
                        if (patient.arrival_date == undefined || patient.arrival_mode == undefined ){
                            swal({
                                title: 'MANDTATORY FIELDS',
                                html: 'Please, provide <span style="color:red">Arrival Mode and Date</span> ',
                                type: 'warning',
                                showCancelButton: false
                            });
                            return;
                        }
                        if (patient.contactperson == true && patient.next_kin_name == undefined && patient.next_kin_relation == undefined ){
                            swal({
                                title: 'MANDTATORY FIELDS',
                                html: 'Please, provide <span style="color:red">Contact Person names and Relationship</span> ',
                                type: 'warning',
                                showCancelButton: false
                            });
                            return;
                        }


                        var dob = patient.dob;

                        if (patient.dob instanceof Date) {
                            dob = patient.dob.toISOString();
                        }

                        if (patient.dob != undefined) {

                            if (dob != '' && ((new Date()).getFullYear() < parseInt(dob.substring(0, 4)) ||
                                ((new Date()).getFullYear() == parseInt(dob.substring(0, 4)) && ((new Date()).getMonth() + 1) < parseInt(dob.substring(dob.indexOf("-") + 1, 7))) ||
                                ((new Date()).getFullYear() == parseInt(dob.substring(0, 4)) && ((new Date()).getMonth() + 1) == parseInt(dob.substring(dob.indexOf("-") + 1, 7)) && ((new Date()).getDate()) < parseInt(dob.substring(dob.lastIndexOf("-") + 1, 10))))) {
                                patient.dob = undefined;
                                swal('Future dates For D.O.B not allowed!', '', 'warning');
                                return;
                            }
                        }
var arrivalDate = patient.arrival_date;

                        if (patient.arrival_date instanceof Date) {
                            arrivalDate = patient.arrival_date.toISOString();
                        }
                        if (patient.arrival_date == undefined) {
                            return;
                        }


                        if (arrivalDate != '' && ((new Date()).getFullYear() < parseInt(arrivalDate.substring(0, 4)) ||
                            ((new Date()).getFullYear() == parseInt(arrivalDate.substring(0, 4)) && ((new Date()).getMonth() + 1) < parseInt(arrivalDate.substring(arrivalDate.indexOf("-") + 1, 7))) ||
                            ((new Date()).getFullYear() == parseInt(arrivalDate.substring(0, 4)) && ((new Date()).getMonth() + 1) == parseInt(arrivalDate.substring(arrivalDate.indexOf("-") + 1, 7)) && ((new Date()).getDate()) < parseInt(arrivalDate.substring(arrivalDate.lastIndexOf("-") + 1, 10))))) {
                            patient.arrivalDate = undefined;
                            swal('Future dates For Arrival not allowed!', '', 'warning');
                            return;
                        }



					
                                var patientData={user_id: user_id,
                                    dob:"2000-10-10",
                                    gender: patient.gender,
                                    facility_id: facility_id,
                                    user_id:user_id,
                                    residence_id: 40,//patient.residenc_id.id,
                                    last_name: patient.names.split(',')[0].trim().toUpperCase(),
                                    middle_name: patient.names.split(',')[1].trim().toUpperCase(),
                                    first_name: patient.names.split(',')[1].trim().toUpperCase()
                                };
						


						$http.post('/api/quick_registration',patientData).then(function (response) {
							if(response.data[0][0].id){
                            console.log(response.data[0][0].id);
                        
    var demographics = [{ 
                                    surname: patient.names.split(',')[0].trim().toUpperCase(),
                                    first_name: patient.names.split(',')[1].trim().toUpperCase(),
                                    gender: patient.gender.toUpperCase(),
                                    dob: patient.dob,
                                    estimated_age: patient.estimated_age,
                                    estimated_age_group: patient.estimated_age_group,
                                    residence: patient.residence,
                                    incident_location: patient.incident_location,
                                    arrival_date: patient.arrival_date,
                                    arrival_mode: patient.arrival_mode,
                                    next_kin_name: patient.next_kin_name,
                                    next_kin_phone: patient.next_kin_phone,
                                    next_kin_relation: patient.next_kin_relation,
                                    patient_id: response.data[0][0].id,
                                    mrn: response.data[0][0].medical_record_number,
                                  marital_status: patient.marital_status,
                            level_of_education: patient.level_of_education,
                                  occupation_of_patient: patient.occupation_of_patient,
                                    facility_id: facility_id, 
                                    registered_by: user_id
                                    
                                }];
                                $http.post('/api/new-client',demographics).then(function (response) {
                                swal({
                                  title: 'PATIENT REGISTRATION',
                                  html: response.data.text,
                                  type: response.data.status,
                                  showCancelButton: false
                                });
                            $scope.patient = null;
                                $scope.getTraumaList();
                                });
}
								
							},
							 function(response){
								 swal({
								  title: 'PATIENT REGISTRATION',
								  html: 'Something seems to have gone wrong. Patient details not saved.',
								  type: 'warning',
								  showCancelButton: false
								});
							 }
						);
                        
					}
				},
				preserveScope:true,
				templateUrl: '/scripts/modules/trauma/views/add-client.html',
				clickOutsideToClose: false,
				fullscreen: $scope.customFullscreen
			});
		}
		$scope.openEditDialog=function(client){
			$mdDialog.show({
				scope: $scope,
				//controller: TriageController,
				controller: function(){

                    $http.post('/api/get-trauma-patient-edit',{searchKey:client.client_id}).then(function (data) {
$scope.patientInf=data.data;
                    });

                    //age calculation
        $scope.exemption_calculateAge = function (patient, source) {

            var dob = patient.dob;


            if (patient.dob instanceof Date) {
                dob = patient.dob.toISOString();
            }
            if (patient.dob == undefined && patient.age == undefined) {
                return;
            }


            if (dob != '' && source == 'date' && ((new Date()).getFullYear() < parseInt(dob.substring(0, 4)) ||
                ((new Date()).getFullYear() == parseInt(dob.substring(0, 4)) && ((new Date()).getMonth() + 1) < parseInt(dob.substring(dob.indexOf("-") + 1, 7))) ||
                ((new Date()).getFullYear() == parseInt(dob.substring(0, 4)) && ((new Date()).getMonth() + 1) == parseInt(dob.substring(dob.indexOf("-") + 1, 7)) && ((new Date()).getDate()) < parseInt(dob.substring(dob.lastIndexOf("-") + 1, 10))))) {
                $scope.patient.dob = undefined;
                $scope.patient.age_unit = "";
                $scope.patient.age = "";
                swal('Future dates not allowed!', '', 'warning');
                return;
            }

            if (source == 'age') {
                $scope.patient.dob = new Date((new Date().getFullYear() - patient.age) + '-01-01');
                $scope.patient.age_unit = 'Years';

            } else if (source == 'date') {
                $scope.patient.dob = dob.replace(/\//g, '-');
                var days = Math.floor(((new Date()) - new Date(dob.substring(0, 4) + '-' + dob.substring(dob.indexOf("-") + 1, 7) + '-' + dob.substring(dob.lastIndexOf("-") + 1, 10))) / (1000 * 60 * 60 * 24));
                if (days > 365) {
                    $scope.patient.age = Math.floor(days / 365);
                    $scope.patient.age_unit = 'Years';
                } else if (days > 30) {
                    $scope.patient.age = Math.floor(days / 30);
                    $scope.patient.age_unit = 'Months';
                } else {
                    $scope.patient.age = days;
                    $scope.patient.age_unit = 'Days';
                }
            } else {
                if (patient.age_unit == 'Years')
                    $scope.exemption_calculateAge('age');
                else if (patient.age_unit == 'Months') {
                    if (((new Date()).getMonth() + 1) >= (patient.age % 12))
                        $scope.patient.dob = ((new Date()).getFullYear() - ~~(patient.age / 12)) + '-' + ((((new Date()).getMonth() + 1) - (patient.age % 12)).toString().length == 2 ? '' : '0') + (((new Date()).getMonth() + 1) - (patient.age % 12)) + '-01';
                    else
                        $scope.patient.dob = ((new Date()).getFullYear() - 1 - ~~(patient.age / 12)) + '-' + (((12 + ((new Date()).getMonth() + 1)) - (patient.age % 12)).toString().length == 2 ? '' : '0') + ((12 + ((new Date()).getMonth() + 1)) - (patient.age % 12)) + '-01';
                } else {
                    if (((new Date()).getDate()) >= (patient.age % 30))
                        $scope.patient.dob = ((new Date()).getFullYear() - ~~(patient.age / 365)) + '-' + ((((new Date()).getMonth() + 1) - ~~(patient.age / 30)).toString().length == 2 ? '' : '0') + (((new Date()).getMonth() + 1) - ~~(patient.age / 30)) + '-' + (patient.age.toString().length == 2 ? '' : '0') + patient.age.toString();
                    else
                        $scope.patient.dob = ((new Date()).getFullYear() - ~~(patient.age / 365)) + '-' + ((((new Date()).getMonth()) - ~~(patient.age / 30)).toString().length == 2 ? '' : '0') + (((new Date()).getMonth()) - ~~(patient.age / 30)) + '-' + (((30 + ((new Date()).getDate())) - (patient.age % 30)).toString().length == 2 ? '' : '0') + ((30 + ((new Date()).getDate())) - (patient.age % 30));
                }
            }
        };
                    $scope.editClient = function(patient) {


                        if (patient==undefined){
                            swal({
                                title: 'MANDTATORY FIELDS',
                                html: 'Please, provide <span style="color:red">two names (surname, firstname)</span>',
                                type: 'warning',
                                showCancelButton: false
                            });
                            return;
                        }
                        if (patient.names.split(',').length != 2 ||  patient.names.split(',')[0] == '' || patient.names.split(',')[1] == ''){
                            swal({
                                title: 'MANDTATORY FIELDS',
                                html: 'Please, provide <span style="color:red">two names (surname, firstname)</span>',
                                type: 'warning',
                                showCancelButton: false
                            });
                            return;
                        }
                        if (patient.gender == undefined  ){
                            swal({
                                title: 'MANDTATORY FIELDS',
                                html: 'Please, provide <span style="color:red">Gender</span> ',
                                type: 'warning',
                                showCancelButton: false
                            });
                            return;
                        }
                        if (patient == undefined || patient.dob == undefined && patient.estimated_age == undefined && patient.estimated_age_group == undefined){
                            swal({
                                title: 'MANDTATORY FIELDS',
                                html: 'Please, provide <span style="color:red">DoB</span> or one of the age estimators',
                                type: 'warning',
                                showCancelButton: false
                            });
                            return;
                        }
                        if (patient.incident_location == undefined  ){
                            swal({
                                title: 'MANDTATORY FIELDS',
                                html: 'Please, provide <span style="color:red">Incident Location</span> ',
                                type: 'warning',
                                showCancelButton: false
                            });
                            return;
                        }
                        if (patient.arrival_date == undefined || patient.arrival_mode == undefined ){
                            swal({
                                title: 'MANDTATORY FIELDS',
                                html: 'Please, provide <span style="color:red">Arrival Mode and Date</span> ',
                                type: 'warning',
                                showCancelButton: false
                            });
                            return;
                        } if (patient.contactperson == true && patient.next_kin_name == undefined && patient.next_kin_relation == undefined ){
                            swal({
                                title: 'MANDTATORY FIELDS',
                                html: 'Please, provide <span style="color:red">Contact Person names and Relationship</span> ',
                                type: 'warning',
                                showCancelButton: false
                            });
                            return;
                        }

                        var dob = patient.dob;

                        if (patient.dob instanceof Date) {
                            dob = patient.dob.toISOString();
                        }

                        if (patient.dob != undefined) {

                            if (dob != '' && ((new Date()).getFullYear() < parseInt(dob.substring(0, 4)) ||
                                ((new Date()).getFullYear() == parseInt(dob.substring(0, 4)) && ((new Date()).getMonth() + 1) < parseInt(dob.substring(dob.indexOf("-") + 1, 7))) ||
                                ((new Date()).getFullYear() == parseInt(dob.substring(0, 4)) && ((new Date()).getMonth() + 1) == parseInt(dob.substring(dob.indexOf("-") + 1, 7)) && ((new Date()).getDate()) < parseInt(dob.substring(dob.lastIndexOf("-") + 1, 10))))) {
                                patient.dob = undefined;
                                swal('Future dates For D.O.B not allowed!', '', 'warning');
                                return;
                            }
                        }
                        var arrivalDate = patient.arrival_date;

                        if (patient.arrival_date instanceof Date) {
                            arrivalDate = patient.arrival_date.toISOString();
                        }
                        if (patient.arrival_date == undefined) {
                            return;
                        }


                        if (arrivalDate != '' && ((new Date()).getFullYear() < parseInt(arrivalDate.substring(0, 4)) ||
                            ((new Date()).getFullYear() == parseInt(arrivalDate.substring(0, 4)) && ((new Date()).getMonth() + 1) < parseInt(arrivalDate.substring(arrivalDate.indexOf("-") + 1, 7))) ||
                            ((new Date()).getFullYear() == parseInt(arrivalDate.substring(0, 4)) && ((new Date()).getMonth() + 1) == parseInt(arrivalDate.substring(arrivalDate.indexOf("-") + 1, 7)) && ((new Date()).getDate()) < parseInt(arrivalDate.substring(arrivalDate.lastIndexOf("-") + 1, 10))))) {
                            patient.arrivalDate = undefined;
                            swal('Future dates For Arrival not allowed!', '', 'warning');
                            return;
                        }

                        var demographics = {
                            surname: patient.names.split(',')[0].trim().toUpperCase(),
                            first_name: patient.names.split(',')[1].trim().toUpperCase(),
                            gender: patient.gender.toUpperCase(),
                            dob: patient.dob,
                            estimated_age: patient.estimated_age,
                            estimated_age_group: patient.estimated_age_group,
                            residence: patient.residence,
                            incident_location: patient.incident_location,
                            arrival_date: patient.arrival_date,
                            arrival_mode: patient.arrival_mode,
                            next_kin_name: patient.next_kin_name,
                            next_kin_phone: patient.next_kin_phone,
                            next_kin_relation: patient.next_kin_relation,
                            client_id: patient.client_id,
                            marital_status: patient.marital_status,
                            level_of_education: patient.level_of_education,
                            occupation_of_patient: patient.occupation_of_patient,
                            facility_id: facility_id,
                            registered_by: user_id
                        };

                        $http.post('/api/update-client',demographics).then(function (response) {
                                swal({
                                    title: 'PATIENT INFORMATION UPDATE',
                                    html: response.data.text,
                                    type: response.data.status,
                                    showCancelButton: false
                                });
                                $scope.patient = null;
                                $scope.getTraumaList();
                            },
                            function(response){
                                swal({
                                    title: 'PATIENT INFORMATION UPDATE',
                                    html: 'Something seems to have gone wrong. Patient details not updated.',
                                    type: 'warning',
                                    showCancelButton: false
                                });
                            }
                        );
                    }

				},
				preserveScope:true,
				templateUrl: '/scripts/modules/trauma/views/edit-client.html',
				clickOutsideToClose: false,
				fullscreen: $scope.customFullscreen
			});
		}

		$scope.triage = function(patient){
			$scope.selectedPatient = patient;
			
			$mdDialog.show({
				scope: $scope,
				//controller: TriageController,
				controller: function(){
					$scope.registerVitals = function(vitals, triage_category) {
                        if ($scope.selectedPatient == undefined || (vitals == undefined && triage_category == undefined)) {
                            swal({
                                title: 'MISSING VALUE',
                                html: 'Could not associate the details with known patient',
                                type: 'warning',
                                showCancelButton: false
                            });
                            return;
                        }

                        if (triage_category) {
                            $http.post('/api/set-acuity', {
                                client_id: $scope.selectedPatient.client_id,
                                triage_category: triage_category
                            }).then(function (response) {
                                swal({
                                    title: 'TRIAGE DETAILS',
                                    html: response.data.text,
                                    type: response.data.status,
                                    showCancelButton: false
                                });
                                $scope.getTraumaList();
                                $scope.triage_category = null;
                            });
                        }
                        console.log(vitals);
                        if (vitals.bp && vitals.bp !=undefined ){
                            if (vitals.bp.split('/').length != 2 || vitals.bp.split('/')[0] == '' || vitals.bp.split('/')[1] == '') {

                                swal({
                                    title: 'FIELD FORMAT',
                                    html: 'Please, provide <span style="color:red">BP in (dystolic / systolic) format e.g 60/110</span>',
                                    type: 'warning',
                                    showCancelButton: false
                                });
                                return;

                            }
                            else {
                                var dys = vitals.bp.split('/')[0].trim();
                                var syt = vitals.bp.split('/')[1].trim().toUpperCase();
                                if (dys < 0 || dys > 400 || syt < 0 || syt > 400) {
                                    swal({
                                        title: 'FIELD FORMAT AND RANGE',
                                        html: 'Oops! BP range<span style="color:green"> (0/0 - 400/400)<i>bits/min</i>, </span> <p></p>' +
                                        'NOT <span style="color:red">  (' + vitals.bp + ') </span> ',
                                        type: 'warning',
                                        showCancelButton: false
                                    });
                                    return;
                                }
                            }
                    }

                        if (vitals.hr && vitals.hr !=undefined ) {
                            if ( vitals.hr < 0 || vitals.hr > 300) {
                                swal({
                                    title: 'FIELD RANGE',
                                    html: 'Oops! Heart Rate range<span style="color:green"> (0 - 300)<i>bits/min</i>, </span> <p></p>' +
                                    'NOT <span style="color:red">  (' + vitals.hr + ')<i>bits/min</i> </span> ',
                                    type: 'warning',
                                    showCancelButton: false
                                });
                                return;
                            }
                        }
                        if (vitals.spo2 && vitals.spo2 !=undefined ) {
                            if ( vitals.spo2 < 0 || vitals.spo2 > 100) {
                                swal({
                                    title: 'FIELD RANGE',
                                    html: 'Oops! SpO2  range<span style="color:green"> (0 - 100)<i>(% on L)</i>, </span> <p></p>' +
                                    'NOT <span style="color:red">  (' + vitals.spo2 + ')<i>(% on L)</i> </span> ',
                                    type: 'warning',
                                    showCancelButton: false
                                });
                                return;
                            }
                        }
                        if (vitals.height && vitals.height !=undefined ) {
                            if ( vitals.height < 20 || vitals.height > 350) {
                                swal({
                                    title: 'FIELD RANGE',
                                    html: 'Oops! Height  range<span style="color:green"> (20- 350)<i>(cm)</i>, </span> <p></p>' +
                                    'NOT <span style="color:red">  (' + vitals.height + ')<i>(cm)</i> </span> ',
                                    type: 'warning',
                                    showCancelButton: false
                                });
                                return;
                            }
                        }
                        if (vitals.weight && vitals.weight !=undefined ) {
                            if ( vitals.weight < 20 || vitals.weight > 350) {
                                swal({
                                    title: 'FIELD RANGE',
                                    html: 'Oops! Weight  range<span style="color:green"> (0.1- 500)<i>(Kg)</i>, </span> <p></p>' +
                                    'NOT <span style="color:red">  (' + vitals.weight + ')<i>(Kg)</i> </span> ',
                                    type: 'warning',
                                    showCancelButton: false
                                });
                                return;
                            }
                        }
                        if (vitals.ps && vitals.ps !=undefined ) {
                            if ( vitals.ps < 0 || vitals.ps > 10) {
                                swal({
                                    title: 'FIELD RANGE',
                                    html: 'Oops! Pain score  range<span style="color:green"> (0- 10), </span> <p></p>' +
                                    'NOT <span style="color:red">  (' + vitals.ps + ')  </span> ',
                                    type: 'warning',
                                    showCancelButton: false
                                });
                                return;
                            }
                        }
                        if(vitals){
							vitals.client_id = $scope.selectedPatient.client_id;
							vitals.recorded_by = user_id;
							vitals.facility_id = facility_id;

							$http.post('/api/trauma-vitals',[vitals]).then(function (response) {
									swal({
									  title: 'TRIAGE DETAILS',
									  html: response.data.text,
									  type: response.data.status,
									  showCancelButton: false
									});
									$scope.vitals = null;
									$scope.getTraumaList();
								},
								 function(response){
									 swal({
									  title: 'TRIAGE DETAILS',
									  html: 'Something seems to have gone wrong. Triage details not saved.',
									  type: 'warning',
									  showCancelButton: false
									});
								 }
							);
						}
					}
				},
				preserveScope:true,
				templateUrl: '/scripts/modules/trauma/views/vitals.html',
				clickOutsideToClose: false,
				fullscreen: $scope.customFullscreen
			});
		}
		       
		$scope.getTraumaList=function(){
			$http.post('/api/get-trauma-list').then(function (response) {
				$scope.trauma_lists = response.data;
            });      
		};
var diag = [];

                        $scope.getTraumaListSearched =function(searchKey){
            $http.post('/api/get-trauma-list',{searchKey:searchKey}).then(function (response) {
                $scope.trauma_lists = response.data;
                diag = response.data;
            }); 

                            return diag;

                        }
                         
	
		$scope.getTriageCategories=function(){
			$http.post('/api/triage-categories').then(function (response) {
				$scope.triage_categories = response.data;
            });      
		};

	$scope.getTriageArrivalMode=function(){
			$http.post('/api/triage-arrival-modes').then(function (response) {
				$scope.arrival_modes = response.data;
            });
		};


		$scope.cancel = function () {
 //$state.reload();
			$mdDialog.hide();
            //$state.reload();
		};
		
		$scope.startup = function(){
			$scope.getTraumaList();
			$scope.getTriageCategories();
			$scope.getTriageArrivalMode();
		}
		
		$scope.startup();

        $scope.triageRegisteredReport=function(dd){
            $http.post('/api/triageRegisteredReport',dd).then(function (response) {
                $scope.triages = response.data[0];
            });
        };
var dd;
        $scope.triageRegisteredReport(dd);

                $scope.giveservice = function(patient){
            $scope.patientData = patient;
            
            $mdDialog.show({
                scope: $scope,
                //controller: TriageController,
                controller: function(){

                     $http.get('/api/searchPatientCategory/'+facility_id).then(function (data) {

                                    $scope.patientCategory = data.data;
                                });
                                $scope.getPricedItems = function (patient_category_selected) {
                                    //console.log(patient_category_selected);
                                    var postData={facility_id:facility_id,patient_category:patient_category_selected};
                                    $http.post('/api/getPricedItems', postData).then(function (data) {
                                        $scope.services = data.data;
                                    });

                                }
                                $scope.loadEmergency = function () {
                                    $http.get('/api/emergency_type_list').then(function (data) {
                                        $scope.emergency_list = data.data;
                                    });
                                };
                                $scope.state = [
                                    {
                                        "id": "1",
                                        "department_name":"OUT PATIENT DEPARTMENT (OPD)"
                                    }
                                ];
                                $scope.getDepartment = function () {
                                    $http.get('/api/getClinic').then(function (data) {
                                        $scope.departments = data.data;
                                    })
                                };
                                 var facilities = [];

                        $scope.showFacility = function(searchKey) {

                            $http.get('/api/getFacilities', {

                                "searchKey": searchKey

                            }).then(function(data) {



                                facilities = data.data;

                            });

                            return facilities;

                        }
                     $scope.enterEncounter=function(selectedPatient,encounter){
if (angular.isDefined(selectedPatient) == false) {
                                        return sweetAlert("Patient is not recognized ..", "", "error");
                                    }
                                    if (angular.isDefined(selectedPatient.patient_id) == false) {
                                        return sweetAlert("Patient is not recognized ..", "", "error");
                                    }
if (angular.isDefined(encounter) == false) {
                                        return sweetAlert("Please Type the Payment Category", "", "error");
                                    }
 
if (angular.isDefined(encounter.payment_category) == false) {
                                        return sweetAlert("Please Type the Payment Category", "", "error");
                                    } else if (angular.isDefined(encounter.payment_services) == false) {
                                        return sweetAlert("Please Select Service", "", "error");
                                    }
                                    else if (angular.isDefined(encounter.emergency_name) == false) {
                                        return sweetAlert("Please Select emergency Type", "", "error");
                                    }


                                    var service_category = encounter.payment_services;
                                        var service_id = encounter.payment_services.service_id;
                                        var price_id = encounter.payment_services.price_id;
                                        var item_type_id = encounter.payment_services.item_type_id;
                                        var dept_id = encounter.department.id;
                                        var emergency_type_id = encounter.emergency_name.id;
                                        
                                        //var facility_id = facility_id;
                                        var user_id = $rootScope.currentUser.id;
                                        var facility_id = $rootScope.currentUser.facility_id;
                                        var payment_filter=encounter.payment_services.patient_category_id;
                                        var bill_category_id = encounter.payment_services.patient_category_id;
                                        var main_category_id = encounter.payment_services.patient_main_category_id;
                                        var patient_category = encounter.payment_services.patient_category_id;
                                        var is_referral = encounter.is_referral;
                                        if(encounter.is_referral==1){
                                         var from_referral_id = encounter.selectedFacility.id;
   
                                     }else{
                                        var from_referral_id = null;

                                     }
                                        
if(facility_id==from_referral_id){
  return sweetAlert("Incoming referral can not be from the same facility", "", "error");
  return;
                                      
}
                                        var enterEncounter = {
                                            'is_referral':is_referral,
                                            'from_referral_id':from_referral_id,
                                            'dept_id': dept_id,
                                            'payment_filter': payment_filter,
                                            'item_type_id': item_type_id,
                                            'patient_category': patient_category,
                                            'main_category_id': main_category_id,
                                            'bill_id': bill_category_id,
                                            'service_id': service_id,
                                            'price_id': price_id,
                                            'patient_id': selectedPatient.patient_id,
                                            'emergency_type_id': emergency_type_id,
                                            'facility_id': facility_id,
                                            'user_id': user_id
                                        };
                                        console.log(enterEncounter)
       $http.post('/api/enterEncounterTriage', enterEncounter).then(function (data) {
                                            console.log(data)
                                            console.log(data.data)
                                            console.log(data.data.status)
                                            if (data.data.status == 0) {

                                                sweetAlert(data.data.data, "", "error");
                                            } else {
                                               
                                               // console.log(medical_record_number);
  swal(
                                            'DATA SERVED',
                                            "",
                                            'success'
                                        );
    $mdDialog.hide();
$state.reload();

                                    }

                                    })
                     }
                },
                preserveScope:true,
                templateUrl: '/scripts/modules/trauma/views/triage-service.html',
                clickOutsideToClose: false,
                fullscreen: $scope.customFullscreen
            });
        }

    }
]);

}());