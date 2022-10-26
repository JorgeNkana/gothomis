/**
 * Created by jeph on 10/06/2017.
 */
(function () {

    'use strict';

    var app = angular
        .module('authApp')

    app.controller('emergencyencounterModal',

        ['$scope','$http','$rootScope','$uibModal', '$uibModalInstance', 'object',
            function ($scope,$http,$rootScope,$uibModal,$uibModalInstance,object) {
                //console.log(object);
                $scope.patientData=object.patientData;
                $scope.accounts_number=object.accounts_number;
                $scope.residences=object.residences;
                $scope.getLastVisit=object.getLastVisit;

                $scope.enterEncounter=function (patientData,residences,encounter,patient,facility_id) {


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


                        var patient_category=encounter.payment_category.patient_category;
                        var service_category=encounter.payment_services;
                        var service_id=encounter.payment_services.service_id;
                        var price_id=encounter.payment_services.price_id;
                        var item_type_id=encounter.payment_services.item_type_id;
                        var patient_id=patient;
                        var facility_id=facility_id;
                        var user_id=$rootScope.currentUser.id;
                        var payment_filter=encounter.payment_category.patient_category_id;

                        var bill_category_id=encounter.payment_category.patient_category_id;
                        var main_category_id=encounter.payment_category.patient_main_category_id;

                        var enterEncounter={'payment_filter':payment_filter,'item_type_id':item_type_id,'patient_category':patient_category,'main_category_id':main_category_id,'bill_id':bill_category_id,
                            'service_category':service_category,'service_id':service_id,'price_id':price_id,'patient_id':patient_id ,'facility_id':facility_id,'user_id':user_id};
                        //console.log(enterEncounter)
                        $http.post('/api/enterEncounterEm',enterEncounter).then(function(data) {
                            $scope.registrationReport=data.data;

                            if(data.data.status ==0){

                                sweetAlert(data.data.data, "", "error");
                            }else{
                                var object={'patientsInfo':patientData,'residences':residences};
                                $scope.cancel();
                                var modalInstance = $uibModal.open({
                                    templateUrl: '/views/modules/registration/printCard.html',
                                    size: 'lg',
                                    animation: true,
                                    controller: 'printCard',
                                    resolve:{
                                        object: function () {
                                            ////console.log($scope.quick_registration);
                                            return object;
                                        }
                                    }


                                });

                                //sweetAlert(data.data.data, "", "success");
                                //enterEncounter='';
                            }


                        });



                    }
                }







                $scope.cancel=function (){
                    //console.log('done and cleared');
                    $uibModalInstance.dismiss();

                }


                $scope.closeAllModals=function (){
                    //console.log('done and cleared');
                    $uibModalInstance.dismissAll();

                }

            }]);





}());