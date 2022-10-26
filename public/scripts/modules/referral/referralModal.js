/**
 * Created by Mazigo Jr on 2017-04-10.
 */
(function () {
    'use strict';
    var app = angular.module('authApp');
    app.controller('referralModal',['$scope','$http','$rootScope','$uibModal' ,'$uibModalInstance', 'object',
        function ($scope,$http,$rootScope,$uibModal, $uibModalInstance, object) {
            var facility_id = $rootScope.currentUser.facility_id;
            $scope.patientDetails = object;
            var patientCategory =[];
            $scope.searchPatientCategory = function(searchKey) {
                $http.get('/api/searchPatientCategory/'+searchKey).then(function(data) {
                    patientCategory = data.data;
                });
                return patientCategory;
            }
            $scope.getPricedItems=function (patient_category_selected) {
                //console.log(patient_category_selected);
	var dataPost={patient_category:patient_category_selected,facility_id:facility_id};
    $http.post('/api/getPricedItems',dataPost).then(function(data) {
                    $scope.services=data.data;
                });

            }

            $scope.enterEncounter=function (encounter,patient) {


                if (angular.isDefined(encounter)==false) {
                    return sweetAlert("Please Type the Payment Category", "", "error");
                }
                else if (angular.isDefined(encounter.payment_category)==false) {
                    return sweetAlert("Please Type the Payment Category", "", "error");
                }
                else if (angular.isDefined(encounter.payment_services)==false) {
                    return sweetAlert("Please Select Service", "", "error");
                }
                else{

                    //console.log(encounter);
                    //console.log(patient);
                    var patient_category=encounter.payment_category.patient_category;
                    var service_category=encounter.payment_services;
                    var service_id=encounter.payment_services.service_id;
                    var price_id=encounter.payment_services.price_id;
                    var item_type_id=encounter.payment_services.item_type_id;
                    var patient_id=patient.patient_id;
                    var user_id=$rootScope.currentUser.id;

                    var bill_category_id=encounter.payment_category.patient_category_id;
                    var main_category_id=encounter.payment_category.patient_main_category_id;

                    var enterEncounter={'item_type_id':item_type_id,'patient_category':patient_category,'main_category_id':main_category_id,'bill_id':bill_category_id,
                        'service_category':service_category,'service_id':service_id,'price_id':price_id,'patient_id':patient_id ,'facility_id':facility_id,'user_id':user_id};

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
                                     return $scope.patientDetails;
                                    }
                                }
                            });
                        }
                    });
                }
            }

        }]);

})();