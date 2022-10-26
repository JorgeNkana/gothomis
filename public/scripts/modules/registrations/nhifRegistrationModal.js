(function() {

    'use strict';

    var app = angular
        .module('authApp')

    app.controller('nhifRegistrationModal',

        ['$scope', '$http', '$rootScope', '$uibModal', '$uibModalInstance', 'object',
            function($scope, $http, $rootScope, $uibModal, $uibModalInstance, object) {
                //console.log(object);
                $scope.patientData = object;
                var facility_id = $rootScope.currentUser.facility_id;
                var user_id = $rootScope.currentUser.id;



                $scope.cancel = function() {
                    //console.log('done and cleared');
                    $uibModalInstance.dismiss();

                }



                $scope.savePatientInsuarance = function(quick_registration, patient) {
                    var first_name = quick_registration.first_name;
                    var middle_name = quick_registration.middle_name;
                    var last_name = quick_registration.last_name;
                    var gender = quick_registration.gender;
                    var dob = quick_registration.dob;
                    var authorization_number = quick_registration.AuthorizationNo;
                    var membership_number = quick_registration.membership_number;
                    var card_no = quick_registration.card_no;

                    if (angular.isDefined(first_name) == false) {
                        return sweetAlert("Please Enter FIRST NAME before SAVING", "", "error");
                    } else if (angular.isDefined(middle_name) == false) {
                        return sweetAlert("Please Enter MIDDLE NAME before SAVING", "", "error");
                    } else if (angular.isDefined(last_name) == false) {
                        return sweetAlert("Please Enter LAST NAME before SAVING", "", "error");
                    } else if (quick_registration.dob == null) {
                        var dob = patient.dob;
                    }

                    var mobile_number = patient.mobile_number;
                    var patient_residences = patient.resedence_id.residence_id;
                    var patientservices = patient.payment_services.service_id;
                    var occupation = patient.occupation.id;
                    var price_id = patient.payment_services.price_id;
                    var item_type_id = patient.payment_services.item_type_id;
                    var patient_main_category_id = patient.payment_services.patient_main_category_id;
                    var patient_category = patient.payment_services.patient_category_id;
                    var payment_filter = patient.payment_services.patient_category_id;
                    var insuaranceRegistration = {
                        "card_no": card_no,
                        "authorization_number": authorization_number,
                        "membership_number": membership_number,
                        "payment_filter": payment_filter,
                        "occupation": occupation,
                        "item_type_id": item_type_id,
                        "price_id": price_id,
                        "patient_main_category_id": patient_main_category_id,
                        "patient_category": patient_category,
                        "patientservices": patientservices,
                        "first_name": first_name,
                        "middle_name": middle_name,
                        "last_name": last_name,
                        "dob": dob,
                        "gender": gender,
                        "mobile_number": mobile_number,
                        "residence_id": patient_residences,
                        "facility_id": facility_id,
                        "user_id": user_id
                    }
                    $http.post('/api/insuaranceRegistration', insuaranceRegistration).then(function(data) {
                        $scope.registeredPatient = data.data;
                        if (data.data.status == 0) {
                            sweetAlert(data.data.data, "", "error");
                        } else {

                            var patientData = data.data[0];
                            var accounts_number = data.data[1][0];
                            var residences = data.data[2][0];
                            var getLastVisit = data.data[3];
                            var object = {
                                'patientsInfo': patientData,
                                'accounts_number': accounts_number,
                                'residences': residences,
                                'getLastVisit': getLastVisit,
                                'revisit': false
                            };
                            $scope.patient = null;
                            $scope.cancel();
                            var modalInstance = $uibModal.open({
                                templateUrl: '/views/modules/registration/printCard.html',
                                size: 'lg',
                                animation: true,
                                controller: 'printCard',
                                resolve: {
                                    object: function() {
                                        ////console.log($scope.quick_registration);
                                        return object;
                                    }
                                }


                            });

                        }
                    });
                }



                $scope.closeAllModals = function() {
                    //console.log('done and cleared');
                    $uibModalInstance.dismissAll();

                }

            }
        ]);





}());