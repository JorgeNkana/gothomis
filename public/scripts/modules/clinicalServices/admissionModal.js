/**
 * Created by Mazigo Jr on 2017-03-19.
 */
(function() {
    'use strict';
    var app = angular.module('authApp');
    app.controller('admissionModal', ['$scope', '$http', '$rootScope','$uibModal' ,'$uibModalInstance','object',
        function($scope, $http, $rootScope,$uibModal, $uibModalInstance,object) {

            $scope.admissionDetails = $scope.item;
            $scope.cancel = function() {
                $mdDialog.cancel();
                $state.reload();
            };

            $scope.admissionDetails = object;
            $scope.users = object;
            $scope.dischargeDetails = object;
            var user_id = $rootScope.currentUser.id;
            var facility_id = $rootScope.currentUser.facility_id;
            var main_category_id = object.main_category_id;
            var dept_id = object.dept_id;
            var visit_id = object.visit_id;
            var patient_id = object.patient_id;
            var sender_clinic_id = object.sender_clinic_id;

            angular.element(document).ready(function() {
                if (main_category_id == 3) {
                    object.bill_id = 1;
                }
                if(dept_id==null){
                    return;
                }
                $http.post('/api/getConsultation', {
                    "dept_id": dept_id,
                    "patient_category_id": object.bill_id,
                    "facility_id": facility_id
                }).then(function(data) {
                    $scope.consultations = data.data;
                });
            });
 var diag = [];

            $scope.showDiagnosis = function(search) {

                $http.post('/api/getDiagnosis', {

                    "search": search

                }).then(function(data) {

                    diag = data.data;

                });

                return diag;

            }

            $scope.admitPatient = function(item, notes) {
				console.log(item);
                if (notes == null) {
                    swal("Ooopss Sorry!", "Please write admission notes and prescription instructions", "error");
                    return;
                }
                var admissionData = {
                    "patient_id": item.patient_id,
                    "account_id": item.account_id,
                    "admission_status_id": 1,
                    "facility_id": item.facility_id,
                    "user_id": user_id,
                    "instructions": notes.instructions,
                    "ward_id": item.ward_id
                };
                $http.post('/api/admitPatient', admissionData).then(function(data) {
                    if (data.data.status == 0) {

                        swal(data.data.data, "", "error");
                    } else {
                        swal("Patient successfully admitted to " + item.ward_full_name, "", "success");
                    }
                });
				

                $uibModalInstance.dismiss();
            };

            //admission to specialized clinics
            $scope.internalTransfer = function(clinic, summary, item) {		
                var filter = clinic.bill_id;
                var status_id = 1;
                if (clinic.sender_clinic_id == clinic.id) {
                    swal('Oops you can not transfer this patient to ' + clinic.department_name, 'you can only transfer patients to other clinics, not your clinic', 'error');
                    return;
                }
		if(clinic.id == 26 && clinic.gender.toLowerCase() == 'male'){
		swal('Oooops!', 'You can only transfer women to '+clinic.department_name+' clinic', 'error');
                    return;
			}
                if (main_category_id != 1 && item.exemption_status == 0) {
                    filter = clinic.bill_id;
                }
                if (main_category_id == 3 && item.exemption_status == 1) {
                    filter = 1;
                }
                if (main_category_id == 2 && item.exemption_status == 1) {
                    filter = clinic.bill_id;
                }
                var clinicData = {
                    "sender_clinic_id": sender_clinic_id,
                    "doctor_requesting_id": user_id,
                    "summary": summary.instructions,
                    "consultation_id": item.item_id,
                    "received": 0,
                    "visit_id": visit_id,
                    "dept_id": dept_id,
                    "on_off": 0,
                    "quantity": 1,
                    "item_price_id": item.price_id,
                    "user_id": user_id,
                    "patient_id": patient_id,
                    "status_id": status_id,
                    "facility_id": facility_id,
                    "item_type_id": item.item_type_id,
                    "payment_filter": filter,
                    "account_number_id": visit_id
                };
                //console.log(clinicData);
                $http.post('/api/postToClinics', clinicData).then(function(data) {

                });
                $scope.notes = null;
                swal(clinic.first_name + ' ' + clinic.middle_name + ' ' + clinic.last_name + ' transferred to ' + clinic.department_name, '', 'success');
                $uibModalInstance.dismiss();
            };
            var diag = [];

            $scope.showDiagnosis = function(search) {

                $http.post('/api/getDiagnosis', {

                    "search": search

                }).then(function(data) {

                    diag = data.data;

                });

                return diag;

            }
            $scope.certifyCorpse = function (corpse,item,diag) {
                if (angular.isDefined(corpse) == false) {
					swal("An error occurred", "Data not saved...Please write causes of death and click send to last office button", "error");
					return;
				}

				var deceased = {
                    "id":corpse.id,
					"first_name": corpse.first_name,
					"middle_name": corpse.middle_name,
					"last_name": corpse.last_name,
					"patient_id": corpse.patient_id,
					"death_certifier": user_id,
					"diagnosis_id":diag.selectedDiagnosis.id,
					"diagnosis_code":diag.selectedDiagnosis.code,
					"user_id": user_id,
					"residence_id":corpse.residence_id,
					"facility_id": facility_id,
					"immediate_cause": item.immediate_cause,
					"underlying_cause": item.underlying_cause,
					"dept_id": 1
				};

				$http.post('/api/certifyCorpse', deceased).then(function(data) {

					if (data.data.status == 0) {
						swal(data.data.data, "", "error");
					} else {
						swal(corpse.first_name + ' ' + corpse.last_name + " sent to Last office", "", "success");
					}
				});
			}

        }
    ]);
})();