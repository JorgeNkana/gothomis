(function() {

    'use strict';

    var app = angular.module('authApp');

    app.controller('printCardBima',

        ['$scope', '$http', '$rootScope', '$uibModalInstance', 'patientsInfo',
            function($scope, $http, $rootScope, $uibModalInstance, patientsInfo) {
                $scope.registeredPatient = patientsInfo;
                ////console.log($scope.quick_registration);

                //console.log($scope.registeredPatient);

                $scope.cancel = function() {
                    //console.log('done and cleared');
                    $uibModalInstance.dismissAll();

                }

                $scope.enterEncounter = function(encounter, patient, facility_id) {


                    if (angular.isDefined(encounter) == false) {
                        return sweetAlert("Please Type the Payment Category", "", "error");
                    } else if (angular.isDefined(encounter.payment_category) == false) {
                        return sweetAlert("Please Type the Payment Category", "", "error");
                    } else if (angular.isDefined(encounter.payment_services) == false) {
                        return sweetAlert("Please Select Service", "", "error");
                    } else {

                        //console.log(encounter);
                        var patient_category = encounter.payment_category.patient_category;
                        var service_category = encounter.payment_services;
                        var service_id = encounter.payment_services.service_id;
                        var price_id = encounter.payment_services.price_id;
                        var item_type_id = encounter.payment_services.item_type_id;
                        var patient_id = patient;
                        var facility_id = facility_id;
                        var user_id = $rootScope.currentUser.id;
                        var enterEncounter = {
                            'item_type_id': item_type_id,
                            'patient_category': patient_category,
                            'service_category': service_category,
                            'service_id': service_id,
                            'price_id': price_id,
                            'patient_id': patient_id,
                            'facility_id': facility_id,
                            'user_id': user_id
                        };

                        $http.post('/api/enterEncounter', enterEncounter).then(function(data) {
                            $scope.registrationReport = data.data;

                            if (data.data.status == 0) {
                                sweetAlert(data.data.data, "", "error");
                            } else {
                                sweetAlert(data.data.data, "", "success");
                                enterEncounter = '';
                            }


                        });



                    }
                }

            }
        ]);





}());