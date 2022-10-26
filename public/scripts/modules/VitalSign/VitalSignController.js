/**
 * Created by japhari on 07/03/2017.
 */

(function() {
    'use strict';
    angular
        .module('authApp')
        .controller('VitalSignController', VitalSignController);
    function VitalSignController($http, $auth, $rootScope,$state,$timeout,$interval,$location,$uibModal,$scope,toastr,$mdDialog,Helper) {
        $scope.isNavCollapsed = false;
        $scope.isCollapsed = true;
        $scope.isCollapsedHorizontal = true;
        var user=$rootScope.currentUser;
        var user_name=$rootScope.currentUser.id;
        var facility_id=$rootScope.currentUser.facility_id;
        $scope.regex=/\s/g;
        angular.element(document).ready(function() {
            $http.post('/api/vitalSignsUsers', {
                "facility_id": facility_id
            }).then(function(data) {
                $scope.patientDataLoaded = data.data;
            });
        });
        $scope.getVitalsUsers = function (text) {
            return Helper.getVitalsUsers(text)
                .then(function (response) {
                    return response.data;
                });
        };

        $scope.getPatientToEncounter = function (text) {
            return Helper.getPatientToEncounter(text)
                .then(function (response) {
                    return response.data;
                });
        };
        $scope.selectedPatientForVital = function (patient) {
            $scope.patientData = patient;
            var visit_id = patient.patient_id;
            console.log(patient);
            if (typeof patient != 'undefined') {
                $mdDialog.show({
                    controller: function ($scope) {
                        $scope.vitalsDate= function (id) {
                            var date_attended = id.visit_date;
                            var patient_id = id.account_id;
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
                        $scope.cancelDialogVital = function () {
                            $mdDialog.hide();

                        };
                        var facility_id=$rootScope.currentUser.facility_id;
                        $scope.regex=/\s/g;
                        var user_id=$rootScope.currentUser.id;
                        $scope.vitalRegister = function(selectedPatient) {
                            var VitalData = [];
                            console.log(selectedPatient);
                            var field_id;
                            $scope.Vitals.forEach(function (vital) {
                                field_id = vital.vital_name.replace($scope.regex, '_');
                                if ($('#' + field_id).val() != '') {
                                    VitalData.push({
                                        'vital_sign_id': vital.vital_id,
                                        'vital_sign_value': $('#' + field_id).val(),
                                        'patient_id':selectedPatient.id,
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
                        $scope.patientData = patient;

                    },
                    templateUrl: '/views/modules/VitalSign/assignVitals.html',
                    parent: angular.element(document.body),
                    clickOutsideToClose: false,
                    fullscreen: $scope.customFullscreen
                })
            }
        };
        $scope.selectedPatientForVitalRecord = function (patient) {
            if (typeof patient != 'undefined') {
                $mdDialog.show({
                    controller: function ($scope) {
                        var account_id = patient.account_id;
                        var patient_id = patient.patient_id;
                        $scope.patientData = patient;
                        console.log(patient);
                        $http.get('/api/getVitalsAccount/' + patient.id).then(function (data) {
                            $scope.vitalsAccount = data.data;
                        });

                        $scope.vitalsDate= function (id) {
                            var date_attended = id.date_attended;
                            var patient_id = id.patient_id;
                            console.log(id);
                            console.log(patient_id);
                            console.log(date_attended);
                            $http.post('/api/prevVitalRecord', {
                                "visit_date_id": id.account_id,
                            }).then(function(data) {
                                $scope.prevVitalRecords = data.data;
                            });
                        }
                        $scope.cancelDialogVital = function () {
                            $mdDialog.hide();
                        };
                        var facility_id=$rootScope.currentUser.facility_id;
                        $scope.regex=/\s/g;
                        var user_id=$rootScope.currentUser.id;
                        $scope.patientData = patient;
                    },
                    templateUrl: '/views/modules/VitalSign/vitalSignRecords.html',
                    parent: angular.element(document.body),
                    clickOutsideToClose: false,
                    fullscreen: $scope.customFullscreen
                })
            }
        };

        $scope.assignVitals = function (patientData) {
            $mdDialog.show({
                controller: function ($scope) {
                    $http.get('/api/getVitals').then(function(data) {
                        $scope.Vitals=data.data;
                    });
                    $scope.cancelDialogVital = function () {
                        $mdDialog.hide();
                    };
                    $scope.getVitalPatient_id= function (id) {
                        var account_id = id.account_id;
                        $http.get('/api/getVitalsAccount/'+account_id).then(function(data) {
                            $scope.accountVital=data.data;
                        });
                    }
                    $scope.patientData = patientData;
                   var visit_id = patientData.id;
                    $scope.regex=/\s/g;
                    var user_id=$rootScope.currentUser.id;
                    $scope.vitalRegister = function() {
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

                    $scope.cancel = function () {
                        $mdDialog.hide();
                    };
                },
                templateUrl: '/views/modules/VitalSign/assignVitals.html',
                parent: angular.element(document.body),
                clickOutsideToClose: false,
                fullscreen: $scope.customFullscreen
            })

        };
        $http.get('/api/getVitals').then(function(data) {
            $scope.Vitals=data.data;
        });
        var user_id=$rootScope.currentUser.id;
        var facility=$rootScope.currentUser.facility_id;
        var patientData =[];
        $scope.showSearch = function(searchKey) {
            $http.post('/api/getVitalsPatients',{
                "search":searchKey,
                "facility_id":facility_id
            }).then(function(data)
            {
                patientData = data.data;
            });
            return patientData;
        }
        $scope.showPromptVitalQue = function(patient) {
            $mdDialog.show({
                controller: function ($scope) {
                    $scope.selectedPatient = patient;
                    console.log(patient);
                    $http.get('/api/getVitals').then(function(data) {
                        $scope.Vitals=data.data;
                    });

                    $scope.cancel = function () {
                        $mdDialog.hide();
                    };
                },
                templateUrl: '/views/modules/VitalSign/vitalRegister.html',
                parent: angular.element(document.body),
                clickOutsideToClose: false,
                fullscreen: $scope.customFullscreen
            })

        };

        $scope.getVitalModal = function(item) {
            $scope.selectedPatient = item;
            $mdDialog.show({
                controller: 'vitalModal',
                templateUrl: '/views/modules/VitalSign/vitalRegister.html',
                parent: angular.element(document.body),
                scope: $scope,
                clickOutsideToClose: false,
                fullscreen: true,
            });
        };


        $scope.VitalQue= function (patient_id) {
            var object = angular.extend({},patient_id);
            var modalInstance = $uibModal.open({
                templateUrl: '/views/modules/VitalSign/vitalModal.html',
                size: 'lg',
                animation: true,
                controller: 'vitalModal',
                windowClass: 'app-modal-window',
                resolve:{
                    object: function () {
                        return object;
                    }
                }
            });
        }
        $scope.getVitalPatient_id= function (id) {
            $http.get('/api/getVitalsAccount/'+id).then(function(data) {
                $scope.accountVital=data.data;
            });
        }
        $scope.vitalsDate= function (id) {
            $http.get('/api/getVitalsDate/'+id).then(function(data) {
                $scope.vitalDatas=data.data;
            });
        }
        $scope.oneAtATime = true;
        $scope.vitalRegister = function(selectedPatient) {
            var VitalData = [];
            console.log(selectedPatient);
            var field_id;
            $scope.Vitals.forEach(function (vital) {
                field_id = vital.vital_name.replace($scope.regex, '_');
                if ($('#' + field_id).val() != '') {
                    VitalData.push({
                        'vital_sign_id': vital.vital_id,
                        'vital_sign_value': $('#' + field_id).val(),
                        'patient_id':selectedPatient.id,
                        'registered_by':user_id
                    });
                    $('#' + field_id).val('');
                }
            })
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
        $scope.x_axis=[];
        $scope.y_axis=[];
        $scope.viewVitals = function(id){
            $http.get('/api/viewVitals/'+id).then(function(data) {
                $scope.vitalData=data.data;
                for(var i=0; i<$scope.vitalData.length; i++) {
                    $scope.x_axis.push(data.data[i].time_attended);
                    $scope.y_axis.push(data.data[i].Body_temperature);
                }
                $scope.labels=$scope.x_axis;
                $scope.data=$scope.y_axis;
            });
        };
        $scope.viewVitals = function(id){
            $http.get('/api/viewVitals/'+id).then(function(data) {
                $scope.vitalData=data.data;
                $scope.x_axis=[];
                $scope.y_axis=[];
                for(var i=0; i<$scope.vitalData.length; i++) {
                    $scope.x_axis.push(data.data[i].time_attended);
                    $scope.y_axis.push(data.data[i].Body_temperature);
                }
                $scope.labels=$scope.x_axis;
                $scope.data=$scope.y_axis;
            });
        };
        $scope.viewDiastolicPressure = function(id){
            $http.get('/api/viewDiastolicPressure/'+id).then(function(data) {
                $scope.vitalData=data.data;
                $scope.vitalData=data.data;
                $scope.valuex=[];
                $scope.valuey=[];
                for(var i=0; i<$scope.vitalData.length; i++) {
                    $scope.valuex.push(data.data[i].time_attended);
                    $scope.valuey.push(data.data[i].Diastolic_pressure);
                }
                $scope.labels=$scope.valuex;
                $scope.data=$scope.valuey;
            });
        };
        $scope.viewTemperature = function(id){
            $http.get('/api/viewTemperature/'+id).then(function(data) {
                $scope.vitalData=data.data;
                $scope.vitalData=data.data;
                $scope.valuex=[];
                $scope.valuey=[];
                for(var i=0; i<$scope.vitalData.length; i++) {
                    $scope.valuex.push(data.data[i].time_attended);
                    $scope.valuey.push(data.data[i].Body_temperature);

                }
                $scope.labels=$scope.valuex;
                $scope.data=$scope.valuey;
            });
        };
        $scope.viewPulseRate = function(id){
            $http.get('/api/viewPulseRate/'+id).then(function(data) {
                $scope.vitalData=data.data;
                $scope.vitalData=data.data;
                $scope.valuex=[];
                $scope.valuey=[];
                for(var i=0; i<$scope.vitalData.length; i++) {
                    $scope.valuex.push(data.data[i].time_attended);
                    $scope.valuey.push(data.data[i].Pulse_rate);
                }
                $scope.labels=$scope.valuex;
                $scope.data=$scope.valuey;
            });
        };

        $scope.patients=function () {
            $http.get('/api/vitalSignsUsers/' + facility_id).then(function (data) {
                $scope.observationuser = data.data;
            });
        }
        $scope.viewSystolicPressure = function(id){
            $http.get('/api/viewSystolicPressure/'+id).then(function(data) {
                $scope.vitalData=data.data;
                $scope.valueX=[];
                $scope.ValueY=[];
                for(var i=0; i<$scope.vitalData.length; i++) {
                    $scope.valueX.push(data.data[i].time_attended);
                    $scope.ValueY.push(data.data[i].Systolic_pressure);
                }
                $scope.labels=$scope.valueX;
                $scope.data=$scope.ValueY;
            });
        };
        var _selected;
        $scope.ngModelOptionsSelected = function(value) {
            if (arguments.length) {
                _selected = value;
            } else {
                return _selected;
            }
        };
        $scope.modelOptions = {
            debounce: {
                default: 500,
                blur: 250
            },
            getterSetter: true
        };
        $scope.status = {
            isCustomHeaderOpen: false,
            isFirstOpen: true,
            isFirstDisabled: false
        };


        $scope.vitalsreport= function (data) {
            $http.post('/api/vitalsreport',{facility_id:facility_id,data:data}).then(function(data) {
                $scope.vitals = data.data;

            });
        }

        $scope.print_vital=function () {
            //location.reload();
            var DocumentContainer = document.getElementById('id_vital');
            var WindowObject = window.open("", "PrintWindow",
                "width=900,height=700,top=50,left=450,toolbars=no,scrollbars=no,status=no,resizable=yes");
            WindowObject.document.title = "printout: GoT-HOMIS";
            WindowObject.document.writeln(DocumentContainer.innerHTML);
            WindowObject.document.close();

            setTimeout(function () {
                WindowObject.focus();
                WindowObject.print();
                WindowObject.close();
            });

        }
    }

})();