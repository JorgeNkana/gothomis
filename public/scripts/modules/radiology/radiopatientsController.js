/**
 * Created by japhari on 07/03/2017.
 */
(function() {
    'use strict';
    angular
        .module('authApp')
        .controller('radiopatientsController', radiopatientsController);
    function radiopatientsController($http, $auth, $rootScope,$state,$timeout,$interval,$location,$scope,$uibModal, Helper,$mdDialog) {
        $scope.isNavCollapsed = false;
        var user_id=$rootScope.currentUser.id;
        var user=$rootScope.currentUser;
        var facility_id=$rootScope.currentUser.facility_id;

        $scope.getPatientQueXrayNotInList = function (text) {
            return Helper.getRadiologyPatients(text,facility_id)
                .then(function (response) {
                    return response.data;
                });
        };
        angular.element(document).ready(function() {
            $http.post('/api/getPatientQueXray', {
                "facility_id": facility_id
            }).then(function(data) {
                $scope.patientXray = data.data;
            });
        });
        $scope.getRadiologyModal = function (patientInfo) {

            if (typeof patientInfo !=='undefined') {
                $mdDialog.show({
                    controller: function ($scope) {
                        console.log(patientInfo);
                        var patient_id = patientInfo.patient_id;
                        var date_attended = patientInfo.visited_date;
                        console.log(patient_id);
                        $http.post('/api/prevReqRecord', {
                            "patient_id": patient_id
                        }).then(function(data) {
                            $scope.patientsVisitsVitals = data.data;
                            console.log(data.data);
                        });

                        $scope.getRequestFormData= function (id) {
                            var date_attended = id.created_at;
                            var patient_id = id.patient_id;
                            console.log(patient_id);
                            console.log(date_attended);
                            $http.post('/api/getRequestFormData', {
                                "patient_id": patient_id,
                                "date_attended": date_attended
                            }).then(function(data) {
                                console.log(data.data);
                                $scope.requestRecords = data.data;
                            });
                            $http.post('/api/doctorRequest', {
                                "patient_id": patient_id,
                                "date_attended": date_attended

                            }).then(function(data) {
                                $scope.doctorRequest = data.data;
                                console.log(data.data);
                            });
                        }
                        $scope.radiologyFindings = function (id,mrn,visit_date) {
                            console.log(id);
                            console.log(mrn);
                            console.log(visit_date);
                            $http.post('/api/doctorRequest', {
                                "patient_id": id,
                                "date_attended": visit_date

                            }).then(function(data) {
                                $scope.findingsCheck = data.data[0];
                                console.log(data.data);
                            });
                        }
                        $scope.verifyPerPatients = function (id) {
                            $http.post('/api/verifyPerPatients', {
                                "patient_id": id

                            }).then(function(data) {
                                $scope.verified = data.data;
                                console.log(data.data);
                            });
                        }
                        $scope.verifyFindingsData = function (verified) {
                            var order_id = verified.order_id;
                            var patient_id = verified.patient_id;
                            console.log(verified);
                            $http.post('/api/verifyPerRequests', {
                                "patient_id": patient_id,
                                "verify_user": user_name,
                                "order_id": order_id

                            }).then(function(data) {
                                $scope.findingsCheck = data.data[0];
                                console.log(data.data);
                            });
                        }
                        $scope.SaveRadiologyFindings = function (explanation, order) {
                            console.log(explanation);
                            console.log(order);
                            if (explanation == undefined) {
                                swal(
                                    username,
                                    'Findings are Missed',
                                    'error'
                                )
                            }
                            else if (explanation == "") {
                                swal(
                                    username,
                                    'Findings are Missed',
                                    'error'
                                )
                            }
                            else {

                                var ImageData = {
                                    'order_id': order,
                                    'description': explanation,
                                    'post_user': user_name,
                                    'confirmation_status': 0,
                                    'eraser': 1
                                };
                                swal({
                                    title: username,
                                    text: "Are you sure you want to send this Findings",
                                    type: 'warning',
                                    showCancelButton: true,
                                    confirmButtonColor: '#3085d6',
                                    cancelButtonColor: '#d33',
                                    confirmButtonText: 'Yes, send it!'
                                }).then(function () {
                                    $http.post('/api/SaveImage', ImageData).then(function (data) {

                                        //console.log(data.data);
                                        swal(
                                            'Registered Successfully',
                                            'Findings sent!',
                                            'success'
                                        )

                                    });

                                })
                            }
                        }
                        $scope.cancel = function () {
                            $mdDialog.hide();
                        };
                        $scope.selectedPatient = patientInfo;
                    },
                    templateUrl: '/views/modules/radiology/radiopatientsModal.html',
                    parent: angular.element(document.body),
                    clickOutsideToClose: false,
                    fullscreen: $scope.customFullscreen
                })
            }
        };

    }
})();