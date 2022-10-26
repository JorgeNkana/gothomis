(function() {

    'use strict';

    var app = angular
        .module('authApp')

    app.controller('registrationModalCorpse',

        ['$scope', '$http', '$rootScope', '$uibModal', '$uibModalInstance', 'object',
            function($scope, $http, $rootScope, $uibModal, $uibModalInstance, object) {

                var facility_id = $rootScope.currentUser.facility_id;
                var user_id = $rootScope.currentUser.id;

                $scope.quick_registration = object;
                $scope.corpse = object;
                //console.log($scope.corpse.id);
                $scope.patientData = $scope.quick_registration;
                ////console.log($rootScope.currentUser);
                var last_visit = {
                    'patient_id': $scope.quick_registration.id,
                    'facility_id': $scope.quick_registration.facility_id
                };

                $scope.saveMortuary = function(quick_registration, mortuary) {
                    $scope.corpseInfo = quick_registration[1];
                    $scope.corpseData = mortuary;

                    //console.log($scope.corpseInfo);
                    //console.log($scope.corpseData);
                    var data = {
                        "corpse_id": $scope.corpseData.id,
                        "mortuary_id": $scope.corpseData[0].id,
                        "facility_id": facility_id,
                        "user_id": user_id,
                        "admission_status_id": 1
                    };
                    $http.post('/api/saveCorpseFromOutsideFacility', data).then(function(data) {

                        if (data.data.status == 1) {
                            $scope.cancel();
                            sweetAlert(data.data.data, "", "success");
                        }
                    });


                }

                $scope.enterEncounter = function(encounter, patient, facility_id) {


                    if (angular.isDefined(encounter) == false) {
                        return sweetAlert("Please Type the Payment Category", "", "error");
                    } else if (angular.isDefined(encounter.payment_category) == false) {
                        return sweetAlert("Please Type the Payment Category", "", "error");
                    } else if (angular.isDefined(encounter.payment_services) == false) {
                        return sweetAlert("Please Select Service", "", "error");
                    } else {


                        var patient_category = encounter.payment_category.patient_category;
                        var service_category = encounter.payment_services;
                        var service_id = encounter.payment_services.service_id;
                        var price_id = encounter.payment_services.price_id;
                        var item_type_id = encounter.payment_services.item_type_id;
                        var patient_id = patient;
                        var facility_id = facility_id;
                        var user_id = $rootScope.currentUser.id;
                        var payment_filter = encounter.payment_category.patient_category_id;

                        var bill_category_id = encounter.payment_category.patient_category_id;
                        var main_category_id = encounter.payment_category.patient_main_category_id;

                        var enterEncounter = {
                            'payment_filter': payment_filter,
                            'item_type_id': item_type_id,
                            'patient_category': patient_category,
                            'main_category_id': main_category_id,
                            'bill_id': bill_category_id,
                            'service_category': service_category,
                            'service_id': service_id,
                            'price_id': price_id,
                            'patient_id': patient_id,
                            'facility_id': facility_id,
                            'user_id': user_id
                        };
                        //console.log(enterEncounter)
                        $http.post('/api/enterEncounter', enterEncounter).then(function(data) {
                            $scope.registrationReport = data.data;

                            if (data.data.status == 0) {

                                sweetAlert(data.data.data, "", "error");
                            } else {

                                $http.get('/api/getPatientInfo/' + patient_id).then(function(data) {
                                    $scope.patientsInfo = data.data;
                                });

                                var modalInstance = $uibModal.open({
                                    templateUrl: '/views/modules/registration/printCard.html',
                                    size: 'lg',
                                    animation: true,
                                    controller: 'printCard',
                                    resolve: {
                                        patientData: function() {
                                            ////console.log($scope.quick_registration);
                                            return $scope.patientData;
                                        }
                                    }


                                });

                                //sweetAlert(data.data.data, "", "success"); 
                                //enterEncounter='';					
                            }


                        });



                    }
                }


                // exemptions===============================================================
                /*
                			$scope.exemption_registration=function (exempt,patient) {

                				//console.log(patient,exempt)
                				var status_id = 2;
                				var reason_for_revoke = "..";



                				if(exempt==undefined){
                					swal(
                						'Feedback..',
                						'FILL ALL FIELDS',
                						'error'
                					)

                				}
                				else if(patient==undefined){
                					swal(
                						'Feedback..',
                						'Please Select Client from a Search Box above...',
                						'error'
                					)

                				}
                				else if (exempt.exemption_type_id==undefined ){
                					swal(
                						'Feedback..',
                						'Please Select Exemption Category ',
                						'error'
                					)
                				}

                				else if (exempt.exemption_reason==undefined){
                					swal(
                						'Feedback..',
                						'Please Fill  Reason(s) for This exemption ',
                						'error'
                					)
                				}
                				else if (exempt.service==undefined){
                					swal(
                						'Feedback..',
                						'Please Choose Service ',
                						'error'
                					)
                				}



                				else{

                					var patient_category=exempt.service.patient_category;
                					var service_category=exempt.service;
                					var service_id=exempt.service.service_id;
                					var price_id=exempt.service.price_id;
                					var item_type_id=exempt.service.item_type_id;
                					var patient_id=patient;
                					var facility_id=exempt.service.facility_id;
                					var user_id=$rootScope.currentUser.id;

                					var bill_category_id=exempt.exemption_type_id;
                					var main_category_id=3;

                					var enterEncounter={'item_type_id':item_type_id,'patient_category':patient_category,'main_category_id':main_category_id,'bill_id':bill_category_id,
                						'service_category':service_category,'service_id':service_id,'price_id':price_id,'patient_id':patient_id ,'facility_id':facility_id,'user_id':user_id};


                					var status_id=2;
                					var patient= patient;
                					var exemption_type_id= exempt.exemption_type_id;
                					var exemption_reason= exempt.exemption_reason;
                					var user_id= $rootScope.currentUser.id;
                					var facility_id= $rootScope.currentUser.facility_id;
                					var patient_id= patient;
                					var status_id= status_id;
                					var exemption_type_id=exempt.exemption_type_id;
                					var exemption_reason= exempt.exemption_reason;
                					var reason_for_revoke= reason_for_revoke;
                					var description=exempt.description;

                					formdata.append('exemption_type_id',exemption_type_id);
                					formdata.append('exemption_reason',exemption_reason);
                					formdata.append('user_id',user_id);
                					formdata.append('facility_id',facility_id);
                					formdata.append('patient_id',patient_id);
                					formdata.append('reason_for_revoke',reason_for_revoke);
                					formdata.append('status_id',status_id);
                					var request = {
                						method: 'POST',
                						url: '/api/'+'patient_exemption',
                						data: formdata,
                						headers: {
                							'Content-Type': undefined
                						}

                					};

                					// SEND THE FILES.
                					$http(request).then(function (data) {

                							var msg = data.data.msg;
                							$scope.ok = data.data.status;
                							////console.log(data.data.status);
                							var statuss = data.data.status;

                							$http.post('/api/enterEncounter',enterEncounter).then(function(data) {
                								$scope.registrationReport=data.data;

                								if(data.data.status ==0){

                									sweetAlert(data.data.data, "", "error");
                								}else{

                									$http.get('/api/getPatientInfo/'+patient_id).then(function(data) {
                										$scope.patientsInfo=data.data;
                									});

                									var modalInstance = $uibModal.open({
                										templateUrl: '/views/modules/registration/printCard.html',
                										size: 'lg',
                										animation: true,
                										controller: 'printCard',
                										resolve:{
                											patientData: function () {
                												////console.log($scope.quick_registration);
                												return $scope.patientData;
                											}
                										}


                									});

                									//sweetAlert(data.data.data, "", "success");
                									//enterEncounter='';
                								}


                							});

                						})
                						.then(function () {
                						});



                				}
                			}





                			// exemptions===============================================================

                */





                $scope.cancel = function() {
                    //console.log('done and cleared');
                    $uibModalInstance.dismiss();

                }


                $scope.closeAllModals = function() {
                    //console.log('done and cleared');
                    $uibModalInstance.dismissAll();

                }

            }
        ]);





}());