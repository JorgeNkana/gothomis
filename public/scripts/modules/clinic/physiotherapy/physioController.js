/**
 * Created by japhari on 26/07/2017.
 */
(function() {
    'use strict';
    angular
        .module('authApp')
        .controller('physioController', physioController);
    function physioController($http, $auth, $rootScope,$state,$timeout,$interval,$location,$scope,$mdDialog,toastr, Helper ) {
        $scope.isNavCollapsed = false;
        var user_id=$rootScope.currentUser.id;
        var facility=$rootScope.currentUser.facility_id;
        var user=$rootScope.currentUser;
        var user_name=$rootScope.currentUser.id;
        var facility_id=$rootScope.currentUser.facility_id;
        $scope.today_date = new Date();
        $scope.min_date = new Date();
        angular.element(document).ready(function() {
            $http.post('/api/getPhysioPatients', {
                "facility_id": facility_id
            }).then(function(data) {
                $scope.patientDatas = data.data;
            });
            $http.post('/api/getPhysioPatientsFromDoctor', {
                "facility_id": facility_id
            }).then(function(data) {
                $scope.patientDoctor = data.data;
            });
            $http.post('/api/ongoingPhysio', {
                "facility_id": facility_id
            }).then(function(data) {
                $scope.physioOngoing = data.data;
            });

        });

        $scope.getAppointmentList = function (text) {
            return Helper.getAppointmentPhysio(text,facility_id)
                .then(function (response) {
                    return response.data;
                });
        };
        $scope.getAppointmentPhysioRefer = function (text) {
            return Helper.getAppointmentPhysioRefer(text,facility_id)
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
        $scope.selectedPhysioDetails = function (patient) {
            if (typeof patient != 'undefined') {
                $mdDialog.show({
                    controller: function ($scope) {
                        $scope.appointmentPhysio = function (date,cardiac,notes) {
                            var refferal_id = cardiac.transfer_id;
                            var visit_id = cardiac.account_id;
                            var dob = date;
                            if (date instanceof Date) {
                                dob = moment(date).format('YYYY-MM-DD');
                            }
                            var setAppointmentCardiac = {
                                'refferal_id': refferal_id,
                                'visit_id': visit_id,
                                'follow_up_status': notes,
                                'next_visit': dob
                            };
                            $http.post('/api/setContinuePhysio', setAppointmentCardiac).then(function (data) {
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
                        $scope.patientData = patient;
                    },
                    templateUrl: '/views/modules/clinic/physiotherapy/clinicalNote.html',
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
                            $http.post('/api/setAppointmentPhysio', setAppointmentCardiac).then(function (data) {
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
                        console.log(date);
                        if (dob instanceof Date) {
                            dob = moment(date).format('YYYY-MM-DD');
                        }
                        var setAppointmentCardiac = {
                            'refferal_id': refferal_id,
                            'visit_id': visit_id,
                            'next_visit': dob
                        };
                        console.log(setAppointmentCardiac);
                        $http.post('/api/setAppointmentPhysio', setAppointmentCardiac).then(function (data) {
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
                templateUrl: '/views/modules/clinic/physiotherapy/showAppointment.html',
                parent: angular.element(document.body),
                clickOutsideToClose: false,
                fullscreen: $scope.customFullscreen
            })

        };
    }
})();