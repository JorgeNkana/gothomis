/**
 * Created by japhari on 26/07/2017.
 */
(function() {
    'use strict';
    angular
        .module('authApp')
        .controller('cardiacController', cardiacController);
    function cardiacController($http, $auth, $rootScope,$state,$timeout,$interval,$location,$scope,$mdDialog,toastr, Helper ) {
        $scope.isNavCollapsed = false;
        var user_id=$rootScope.currentUser.id;
        var facility=$rootScope.currentUser.facility_id;
        var user=$rootScope.currentUser;
        var user_name=$rootScope.currentUser.id;
        var facility_id=$rootScope.currentUser.facility_id;
        angular.element(document).ready(function() {
            $http.post('/api/getCardiacPatients', {
                "facility_id": facility_id
            }).then(function(data) {
                $scope.patientDatas = data.data;
            });
            $http.post('/api/getCardiacPatientsFromDoctor', {
                "facility_id": facility_id
            }).then(function(data) {
                $scope.patientDataFromDoctor = data.data;
            });
            $http.post('/api/ongoingCardiac', {
                "facility_id": facility_id
            }).then(function(data) {
                $scope.cardiacOngoing = data.data;
            });

        });
        $scope.getAppointmentList = function (text) {
            return Helper.getAppointmentCardio(text,facility_id)
                .then(function (response) {
                    return response.data;
                });
        };
        $scope.getAppointmentCardioRefer = function (text) {
            return Helper.getAppointmentCardioRefer(text,facility_id)
                .then(function (response) {
                    return response.data;
                });
        };
        $scope.selectedPatientFromReception = function (patient) {
            $scope.patientData = patient;
            console.log(patient);
            if (typeof patient != 'undefined') {
                $mdDialog.show({
                    controller: function ($scope) {
                        $scope.appointmentSetup = function (date,cardiac) {
                            var refferal_id = cardiac.transfer_id;
                            var visit_id = cardiac.account_id;
                            var dob = date;
                            if (date instanceof Date) {
                                dob = moment(date).format('YYYY-MM-DD');
                            }
                            var setAppointmentCardiac = {
                                'refferal_id': refferal_id,
                                'visit_id': visit_id,
                                'next_visit': dob
                            };
                            $http.post('/api/setAppointmentCardiac', setAppointmentCardiac).then(function (data) {
                                console.log(data.data);
                                var message = data.data.message;
                                var status = data.data.status;
                                if (status == 0) {
                                    toastr.error(message);
                                }
                                else {
                                    toastr.success(message);
                                }
                            });
                        }
                        $scope.patientData = patient;
                        $scope.cancel = function () {
                            $mdDialog.hide();
                        };
                    },
                    templateUrl: '/views/modules/clinic/cardiac/showAppointment.html',
                    parent: angular.element(document.body),
                    clickOutsideToClose: false,
                    fullscreen: $scope.customFullscreen
                })
            }
        };
        $scope.selectedCardioDetails = function (patient) {
            if (typeof patient != 'undefined') {
                $mdDialog.show({
                    controller: function ($scope) {
                        var account_id = patient.account_id;
                        var patient_id = patient.patient_id;
                        var facility_id=$rootScope.currentUser.facility_id;
                        $scope.regex=/\s/g;
                        var user_id=$rootScope.currentUser.id;
                        $scope.patientData = patient;
                        $http.get('/api/getVitalsAccount/' + account_id).then(function (data) {
                            $scope.vitalsAccount = data.data;
                        });
                        $http.post('/api/previousVisitsVitals', {
                            "patient_id": patient_id
                        }).then(function(data) {
                            $scope.patientsVisitsVitals = data.data;
                            console.log(data.data);
                        });
                        $scope.vitalsDate= function (id) {
                            var date_attended = id.date_attended;
                            var patient_id = id.patient_id;
                            console.log(id);
                            console.log(patient_id);
                            console.log(date_attended);
                            $http.post('/api/prevVitalRecord', {
                                "patient_id": patient_id,
                                "date_attended": date_attended
                            }).then(function(data) {
                                console.log(data.data);
                                $scope.prevVitalRecords = data.data;
                            });
                        }
                        $scope.vitalRegister = function(visit_id) {
                            var VitalData = [];
                            console.log(VitalData);
                            var field_id;
                            $scope.Vitals.forEach(function (vital) {
                                field_id = vital.vital_name.replace($scope.regex, '_');
                                if ($('#' + field_id).val() != '') {
                                    VitalData.push({
                                        'vital_sign_id': vital.vital_id,
                                        'vital_sign_value': $('#' + field_id).val(),
                                        'patient_id':visit_id,
                                        'registered_by':user_id
                                    });
                                    $('#' + field_id).val('');
                                }
                            })
                            console.log(VitalData);
                            if (VitalData.length > 0) {
                                $http.post('/api/VitalSignRegister', VitalData).then(function (data) {
                                    var msg = data.data.msg;
                                    var notification = data.data.notification;
                                    var status = data.data.status;
                                    if (status == 0) {
                                        toastr.error(notification, msg);
                                    }
                                    else {
                                        toastr.success(notification, msg);
                                    }
                                });
                            }
                        }
                        $http.get('/api/getVitals').then(function(data) {
                            $scope.Vitals=data.data;
                        });
                        $scope.cancelDialogVital = function () {
                            $mdDialog.hide();
                        };
                        $scope.patientData = patient;
                    },
                    templateUrl: '/views/modules/clinic/cardiac/showAppointmentDetails.html',
                    parent: angular.element(document.body),
                    clickOutsideToClose: false,
                    fullscreen: $scope.customFullscreen
                })
            }
        };
        $scope.selectedPatientForAppointment = function (patient) {
            $scope.patientData = patient;
            console.log(patient);
            if (typeof patient != 'undefined') {
                $mdDialog.show({
                    controller: function ($scope) {
                        $scope.appointmentSetup = function (date,cardiac) {
                            var refferal_id = cardiac.transfer_id;
                            var visit_id = cardiac.account_id;
                            var dob = date;
                            if (date instanceof Date) {
                                dob = moment(date).format('YYYY-MM-DD');
                            }
                            var setAppointmentCardiac = {
                                'refferal_id': refferal_id,
                                'visit_id': visit_id,
                                'next_visit': dob
                            };
                            $http.post('/api/setAppointmentCardiac', setAppointmentCardiac).then(function (data) {
                                console.log(data.data);
                                var message = data.data.message;
                                var status = data.data.status;
                                if (status == 0) {
                                    toastr.error(message);
                                }
                                else {
                                    toastr.success(message);
                                }
                            });
                        }
                        $scope.patientData = patient;
                        $scope.cancel = function () {
                            $mdDialog.hide();
                        };
                    },
                    templateUrl: '/views/modules/clinic/cardiac/showAppointment.html',
                    parent: angular.element(document.body),
                    clickOutsideToClose: false,
                    fullscreen: $scope.customFullscreen
                })
            }
        };
        $scope.showAppointmentDetails = function (patient) {
            $scope.patientData = patient;
            console.log(patient);
            if (typeof patient != 'undefined') {
                $mdDialog.show({
                    controller: function ($scope) {
                        $scope.appointmentSetup = function (date,cardiac) {
                            var refferal_id = cardiac.transfer_id;
                            var visit_id = cardiac.account_id;
                            var dob = date;
                            if (date instanceof Date) {
                                dob = moment(date).format('YYYY-MM-DD');
                            }
                            var setAppointmentCardiac = {
                                'refferal_id': refferal_id,
                                'visit_id': visit_id,
                                'next_visit': dob
                            };
                            $http.post('/api/setAppointmentCardiac', setAppointmentCardiac).then(function (data) {
                                console.log(data.data);
                                var message = data.data.message;
                                var status = data.data.status;
                                if (status == 0) {
                                    toastr.error(message);
                                }
                                else {
                                    toastr.success(message);
                                }
                            });
                        }
                        $scope.patientData = patient;
                        $scope.cancel = function () {
                            $mdDialog.hide();
                        };
                    },
                    templateUrl: '/views/modules/clinic/cardiac/showAppointmentDetails.html',
                    parent: angular.element(document.body),
                    clickOutsideToClose: false,
                    fullscreen: $scope.customFullscreen
                })
            }
        };
        $scope.date = new Date();
        $scope.showPrompt = function (ev) {
            $mdDialog.show({
                controller: function ($scope) {
                    $http.get('/api/getloadedClinic').then(function(data) {
                        $scope.cardio=data.data;
                    });
                    $scope.cardiacCapacity = function () {
                        $http.get('/api/cardiacCapacity').then(function(data) {
                            $scope.cardioCapacity=data.data;
                        });
                    }
                    $scope.saveCardioSetup = function (setup) {
                    var clinic_id = setup.department.dept_id;
                    var department = setup.department.department;
                    var capacity = setup.capacity;
                        var cardioSetup = {
                            'clinic_id': clinic_id,
                            'department': department,
                            'capacity': capacity
                        };
                        console.log(cardioSetup);
                            $http.post('/api/saveCardioSetup', cardioSetup).then(function (data) {
                                console.log(data.data);
                                var message = data.data.message;
                                var status = data.data.status;
                                if (status == 0) {
                                    toastr.error(message);
                                }
                                else {
                                    toastr.success(message);
                                }
                            });
                    }
                    $scope.cancel = function () {
                        $mdDialog.hide();
                    };
                },
                templateUrl: '/views/modules/clinic/cardiac/cardiacSetup.html',
                parent: angular.element(document.body),
                clickOutsideToClose: false,
                fullscreen: $scope.customFullscreen
            })

        };
        $scope.editCardiac = function (person) {
            console.log(person);
            $mdDialog.show({
                controller: function ($scope) {
                    $http.get('/api/getloadedClinic').then(function(data) {
                        $scope.cardio=data.data;
                    });
                    $scope.updateCapacity = function (setup) {
                        var clinic_id = setup.department.dept_id;
                        var department = setup.department.department;
                        var capacity = setup.capacity;
                        var cardioSetup = {
                            'clinic_id': clinic_id,
                            'department': department,
                            'capacity': capacity
                        };
                        $http.post('/api/editCardioSetup', cardioSetup).then(function (data) {
                            console.log(data.data);
                            var message = data.data.message;
                            var status = data.data.status;
                            if (status == 0) {
                                toastr.error(message);
                            }
                            else {
                                toastr.success(message);
                            }
                        });
                    }
                    $scope.cancel = function () {
                        $mdDialog.hide();
                    };
                },
                templateUrl: '/views/modules/clinic/cardiac/editCardiac.html',
                parent: angular.element(document.body),
                clickOutsideToClose: false,
                fullscreen: $scope.customFullscreen
            })
        }
        $scope.showAppointment = function (patientData) {
            $mdDialog.show({
                controller: function ($scope) {
                    console.log(patientData);
                    $scope.patientData=patientData;
                    $scope.appointmentSetup = function (date,cardiac) {
                        var refferal_id = cardiac.transfer_id;
                        var visit_id = cardiac.account_id;
                        var dob = date;
                        if (date instanceof Date) {
                            dob = moment(date).format('YYYY-MM-DD');
                        }
                        var setAppointmentCardiac = {
                            'refferal_id': refferal_id,
                            'visit_id': visit_id,
                            'next_visit': dob
                        };
                        $http.post('/api/setAppointmentCardiac', setAppointmentCardiac).then(function (data) {
                           console.log(data.data);
                            var message = data.data.message;
                            var status = data.data.status;
                            if (status == 0) {
                                toastr.error(message);
                            }
                            else {
                                toastr.success(message);
                            }
                        });
                    }
                    $scope.cancel = function () {
                        $mdDialog.hide();
                    };
                },
                templateUrl: '/views/modules/clinic/cardiac/showAppointment.html',
                parent: angular.element(document.body),
                clickOutsideToClose: false,
                fullscreen: $scope.customFullscreen
            })

        };
		
		
		$scope.saveDiagnosis = function(objectData) {

			if (objectData == "") {

				swal("Oops Data not saved!", "Please search and select items then click 'Save' button to save data..");

				return;

			}

			$http.post('/api/postDiagnosis', objectData).then(function(data) {
				swal("Diagnosis data successfully Saved!", "", "success");
				var confirmedDiagnoses = [];
				objectData.forEach(function(disease){
					if(disease.status.toLowerCase() == 'confirmed')
						confirmedDiagnoses.push(disease);
				});
				
				
				var TallyRegister = {attempt:0, load: function(){
					if(confirmedDiagnoses.length == 0)
						return;
					TallyRegister.attempt++;
					$http.post('/api/countClinicDiagnosis',{facility_id:facility_id, dob: $scope.selectedPatient.dob,gender: $scope.selectedPatient.gender,concepts:confirmedDiagnoses, clinic_id: 0}).then(function(data){},function(data){if(TallyRegister.attempt < 5) TallyRegister.load();});
				}}
				TallyRegister.load();

			});

			$scope.diagnosisTemp = [];
		}
		
		$scope.tallyAttendance = function(attendance = 'reattendance'){
			var patient_id = $scope.selectedPatient.patient_id;
			var TallyRegister = {attempt:0, load: function(){
				TallyRegister.attempt++;
				$http.post('/api/'+(attendance.toLowerCase() == 'new' ? 'countNewAttendance' : 'countReattendance'),{facility_id:facility_id, dob: $scope.selectedPatient.dob,gender: $scope.selectedPatient.gender, clinic_id:0}).then(function(data){
					var Tally = {attempt:0, load: function(patient_id){
						Tally.attempt++;
						$http.post('/api/tallied',{patient_id: patient_id}).then(function(data){},function(data){if(Tally.attempt < 5) Tally.load(patient_id);});
					}};
					Tally.load(patient_id);					
				},function(data){if(TallyRegister.attempt < 5) TallyRegister.load();});
			}}
			TallyRegister.load();
		}
    }
})();