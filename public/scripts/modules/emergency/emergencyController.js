/**
 * Created by japhari on 07/03/2017.
 */
(function () {
    'use strict';
    angular
        .module('authApp').directive('ngFiles', ['$parse', function ($parse) {
        function fn_link(scope, element, attrs) {
            var onChange = $parse(attrs.ngFiles);
            element.on('change', function (event) {
                onChange(scope, {$files: event.target.files});
            });
        };
        return {
            link: fn_link
        }
    }])

        .controller('emergencyController', emergencyController);

    function emergencyController($http, $auth, $rootScope, $state, $timeout, $interval, $location, $scope, $uibModal, toastr, Helper, $mdDialog) {
        var formdata = new FormData();
        $scope.today = new Date();
        $scope.patient = {};
        $scope.getTheFiles = function ($files) {
            angular.forEach($files, function (value, key) {
                formdata.append(key, value);
            });
        };
        var user_id = $rootScope.currentUser.id;
        var facility = $rootScope.currentUser.facility_id;
        var facility_id = $rootScope.currentUser.facility_id;
        var resdata = [];
        var maritals = [];
        var tribe = [];
        var occupation = [];
        var country = [];
        var relationships = [];
        var patientCategory = [];
        var patientService = [];
        var kin_residence = [];
        var patientsList = [];
        var residence_id;
        $scope.isOpen = false;
          //Residence
        $scope.getResidence = function (text) {
            return Helper.getResidence(text)
                .then(function (response) {
                    return response.data;
                });
        };
        $scope.getPatients = function (searchKey) {
            //////console.log(searchKey);
            var dataToPost = {searchKey: searchKey};
            $http.post('/api/getSeachedPatients', dataToPost).then(function (data) {
                patientsList = data.data;

            });

            return patientsList;
        }

        $scope.openDialog = function (selectedPatient,first_name,middle_name,last_name,medical_record_number,facility_id) {
console.log("PATIENTDA:::::::"+selectedPatient);
            if (typeof selectedPatient != 'undefined') {

                var patient_id = selectedPatient;
                var residence_id = selectedPatient.residence_id;
                var postData = {
                    "patient_id": selectedPatient,
                    "residence_id": residence_id,
                    "facility_id": facility_id
                };

                $scope.quick_registration = selectedPatient;

                $http.post('/api/getPatientRegistrationStatus', postData).then(function (data) {

                    if (data) {
                        console.log(data.data);
                        $scope.patientData1 = {
                            'id':selectedPatient,
                            'first_name':first_name,
                            'middle_name':middle_name,
                            'last_name':last_name,
                            "facility_id": facility_id,
                            "fullname": first_name+"  "+middle_name+' '+last_name,

                            'medical_record_number':medical_record_number,
                        };
                        var accounts_number = data.data[1][0];
                        var residences = data.data[2][0];
                        var getLastVisit = data.data[3];
                        var object = {
                            'patientData': $scope.patientData1,
                            'accounts_number': accounts_number,
                            'residences': residences,
                            'getLastVisit': getLastVisit
                        };


                        $scope.selectedPatient = null;

                        $mdDialog.show({
                            locals: {
                                'patientData': $scope.patientData1,
                                'accounts_number': accounts_number,
                                'residences': residences,
                                'getLastVisit': getLastVisit
                            },
                            controller: function ($scope) {
                                $http.get('/api/searchPatientCategory/'+facility_id).then(function (data) {

                                    $scope.patientCategory = data.data;
                                });
                                $scope.getPricedItems = function (patient_category_selected) {
                                    //console.log(patient_category_selected);
                                    var postData={facility_id:facility_id,patient_category:patient_category_selected};
                                    $http.post('/api/getPricedItems', postData).then(function (data) {
                                        $scope.services = data.data;
                                    });

                                }
                                $scope.loadEmergency = function () {
                                    $http.get('/api/emergency_type_list').then(function (data) {
                                        $scope.emergency_list = data.data;
                                    });
                                };
                                $scope.state = [
                                    {
                                        "id": "1",
                                        "department_name":"OUT PATIENT DEPARTMENT (OPD)"
                                    }
                                ];
                                $scope.getDepartment = function () {
                                    $http.get('/api/getClinic').then(function (data) {
                                        $scope.departments = data.data;
                                    })
                                };
$scope.enterEncounter = function (patient, residences, encounter, id, facility_id, account) {
                                    var patientData1 = patient;
                                     console.log("PATIENT DATA  "+patientData1);
                                    console.log("PATIENT DATA  "+patient);

                                    var residences = residences;
                                    
                                    
                                    console.log(encounter);
                                    var bill_category_id = encounter.payment_services.patient_category_id

                                    var emergency_type_id = encounter.emergency_name.id;
                                    var dept_id = encounter.department.id;
                                    if (angular.isDefined(encounter.payment_category) == false) {
                                        return sweetAlert("Please Type the Payment Category", "", "error");
                                    } else if (angular.isDefined(encounter.payment_services) == false) {
                                        return sweetAlert("Please Select Service", "", "error");
                                    } else {

                                        var patient_category=encounter.payment_category;
                                        console.log(patient_category);
                                        var service_category = encounter.payment_services;
                                        var service_id = encounter.payment_services.service_id;
                                        var price_id = encounter.payment_services.price_id;
                                        var item_type_id = encounter.payment_services.item_type_id;
                                        
                                        //var facility_id = facility_id;
                                        var user_id = $rootScope.currentUser.id;
                                        var facility_id = $rootScope.currentUser.facility_id;
                                        var payment_filter=encounter.payment_services.patient_category_id;
                                        var bill_category_id = encounter.payment_services.patient_category_id;
                                        var main_category_id = encounter.payment_services.patient_main_category_id;

                                        var enterEncounter = {
                                            'dept_id': dept_id,
                                            'payment_filter': payment_filter,
                                            'item_type_id': item_type_id,
                                            'patient_category': patient_category,
                                            'main_category_id': main_category_id,
                                            'bill_id': bill_category_id,
                                            'service_category': service_category,
                                            'service_id': service_id,
                                            'price_id': price_id,
                                            'patient_id': selectedPatient,
                                            'account_id': account,
                                            'emergency_type_id': emergency_type_id,
                                            'facility_id': facility_id,
                                            'user_id': user_id
                                        };
                                        console.log(enterEncounter)
    $http.post('/api/enterEncounterEmergency', enterEncounter).then(function (data) {
                                            $scope.registrationReport = data.data;
                                            if (data.data.status == 0) {

                                                sweetAlert(data.data.data, "", "error");
                                            } else {
                                                 $mdDialog.hide();
$state.reload();
                                               // console.log(medical_record_number);
  swal(
                                            'DATA SERVED',
                                            medical_record_number,
                                            'success'
                                        );


                                     //            $mdDialog.show({
                                     //                controller: function ($scope) {
                                     //                    $scope.patientData1 = $scope.patientData1;
                                     //                    $scope.patientResidences = residences;
                                     // $scope.cancel = function () {
                                     //    $state.reload();
                                     //                        $mdDialog.hide();
                                     //                    };
                                     //                },
                                     //     templateUrl: '/views/modules/emergency/emergencyprintCard.html',
                                     //                parent: angular.element(document.body),
                                     //                clickOutsideToClose: false,
                                     //                fullscreen: $scope.customFullscreen
                                     //            })
                                            }
                                        });
                                    }
                                }

                               // $scope.patientData = patientData[0];
                                //$scope.mtu = patientData[0];

                                console.log("SELECTEDPATIENT"+$scope.selectedPatient);
                                //console.log(patientData);
                                $scope.accounts_number = accounts_number;
                                $scope.residences = residences;
                                $scope.getLastVisit = getLastVisit;


                                $scope.cancel = function () {
                                    $state.reload();
                                    $scope.selectedPatient = null;
                                    $mdDialog.hide();
                                };
                            },
                            templateUrl: '/views/modules/emergency/reattendency.html',
                            parent: angular.element(document.body),
                            clickOutsideToClose: false,
                            fullscreen: $scope.customFullscreen // Only for -xs, -sm breakpoints.
                        });
                    }
                });
            }
        };
        $scope.editResidence = function (text) {
            return Helper.getResidence(text)
                .then(function (response) {
                    return response.data;
                });
        };
        $scope.getVitalsUsers = function (text) {
            return Helper.getVitalsUsers(text)
                .then(function (response) {
                    return response.data;
                });
        };
        $scope.seachTribes = function(searchKey) {
            $http.post('/api/getTribes', {
                "search": searchKey
            }).then(function(data) {
                tribe = data.data;
            });
            return tribe;
        }
           $scope.selectedResidence = function (residence) {
            if (typeof residence != 'undefined') {
                 residence_id = residence.residence_id;
                 console.log(residence_id);
            }
           $scope.residence = residence;
        }
        $scope.reportRecord = function (item) {
            var reportData = {
                start:item.start,
                end:item.end};
            $http.post('/api/getReportedCasualty',reportData).then(function (data) {
                $scope.reportData = data.data;
            });
            $http.post('/api/reportsCasualty',reportData).then(function (data) {
                $scope.reports = data.data;
                console.log(data.data);
                $scope.totalAcident=Calculateaccident($scope.reports)
            });
        }

        var Calculateaccident=function () {
          var total=0;
          for(var i=0; i<$scope.reports.length;i++){
              total -=(-$scope.reports[i].total);
          }
          return total;
        }

        $scope.patient = {};
        //age calculation
        $scope.exemption_calculateAge = function (patient, source) {

            var dob = patient.dob;


            if (patient.dob instanceof Date) {
                dob = patient.dob.toISOString();
            }
            if (patient.dob == undefined && patient.age == undefined) {
                return;
            }


            if (dob != '' && source == 'date' && ((new Date()).getFullYear() < parseInt(dob.substring(0, 4)) ||
                ((new Date()).getFullYear() == parseInt(dob.substring(0, 4)) && ((new Date()).getMonth() + 1) < parseInt(dob.substring(dob.indexOf("-") + 1, 7))) ||
                ((new Date()).getFullYear() == parseInt(dob.substring(0, 4)) && ((new Date()).getMonth() + 1) == parseInt(dob.substring(dob.indexOf("-") + 1, 7)) && ((new Date()).getDate()) < parseInt(dob.substring(dob.lastIndexOf("-") + 1, 10))))) {
                $scope.patient.dob = undefined;
                $scope.patient.age_unit = "";
                $scope.patient.age = "";
                swal('Future dates not allowed!', '', 'warning');
                return;
            }

            if (source == 'age') {
                $scope.patient.dob = new Date((new Date().getFullYear() - patient.age) + '-01-01');
                $scope.patient.age_unit = 'Years';

            } else if (source == 'date') {
                $scope.patient.dob = dob.replace(/\//g, '-');
                var days = Math.floor(((new Date()) - new Date(dob.substring(0, 4) + '-' + dob.substring(dob.indexOf("-") + 1, 7) + '-' + dob.substring(dob.lastIndexOf("-") + 1, 10))) / (1000 * 60 * 60 * 24));
                if (days > 365) {
                    $scope.patient.age = Math.floor(days / 365);
                    $scope.patient.age_unit = 'Years';
                } else if (days > 30) {
                    $scope.patient.age = Math.floor(days / 30);
                    $scope.patient.age_unit = 'Months';
                } else {
                    $scope.patient.age = days;
                    $scope.patient.age_unit = 'Days';
                }
            } else {
                if (patient.age_unit == 'Years')
                    $scope.exemption_calculateAge('age');
                else if (patient.age_unit == 'Months') {
                    if (((new Date()).getMonth() + 1) >= (patient.age % 12))
                        $scope.patient.dob = ((new Date()).getFullYear() - ~~(patient.age / 12)) + '-' + ((((new Date()).getMonth() + 1) - (patient.age % 12)).toString().length == 2 ? '' : '0') + (((new Date()).getMonth() + 1) - (patient.age % 12)) + '-01';
                    else
                        $scope.patient.dob = ((new Date()).getFullYear() - 1 - ~~(patient.age / 12)) + '-' + (((12 + ((new Date()).getMonth() + 1)) - (patient.age % 12)).toString().length == 2 ? '' : '0') + ((12 + ((new Date()).getMonth() + 1)) - (patient.age % 12)) + '-01';
                } else {
                    if (((new Date()).getDate()) >= (patient.age % 30))
                        $scope.patient.dob = ((new Date()).getFullYear() - ~~(patient.age / 365)) + '-' + ((((new Date()).getMonth() + 1) - ~~(patient.age / 30)).toString().length == 2 ? '' : '0') + (((new Date()).getMonth() + 1) - ~~(patient.age / 30)) + '-' + (patient.age.toString().length == 2 ? '' : '0') + patient.age.toString();
                    else
                        $scope.patient.dob = ((new Date()).getFullYear() - ~~(patient.age / 365)) + '-' + ((((new Date()).getMonth()) - ~~(patient.age / 30)).toString().length == 2 ? '' : '0') + (((new Date()).getMonth()) - ~~(patient.age / 30)) + '-' + (((30 + ((new Date()).getDate())) - (patient.age % 30)).toString().length == 2 ? '' : '0') + ((30 + ((new Date()).getDate())) - (patient.age % 30));
                }
            }
        };
        $scope.patientInformation = {};
        $scope.calculateAge = function(patient,source) {

            var dob = patient.dob;

            if (patient.dob instanceof Date) {
                dob = patient.dob.toISOString();
            }
            if (patient.dob == undefined && patient.age == undefined) {
                return;
            }


            if (dob != '' && source == 'date' && ((new Date()).getFullYear() < parseInt(dob.substring(0, 4)) ||
                ((new Date()).getFullYear() == parseInt(dob.substring(0, 4)) && ((new Date()).getMonth() + 1) < parseInt(dob.substring(dob.indexOf("-") + 1, 7))) ||
                ((new Date()).getFullYear() == parseInt(dob.substring(0, 4)) && ((new Date()).getMonth() + 1) == parseInt(dob.substring(dob.indexOf("-") + 1, 7)) && ((new Date()).getDate()) < parseInt(dob.substring(dob.lastIndexOf("-") + 1, 10))))) {
                $scope.patient.dob = undefined;
                $scope.patient.age_unit = "";
                $scope.patient.age = "";
                swal('Future dates not allowed!', '', 'warning');
                return;
            }

            if (source == 'age') {
                $scope.patient.dob = new Date((new Date().getFullYear() - patient.age) + '-07-01');
                $scope.patient.age_unit = 'Years';

            } else if (source == 'date') {
                $scope.patient.dob = dob.replace(/\//g, '-');
                var days = Math.floor(((new Date()) - new Date(dob.substring(0, 4) + '-' + dob.substring(dob.indexOf("-") + 1, 7) + '-' + dob.substring(dob.lastIndexOf("-") + 1, 10))) / (1000 * 60 * 60 * 24));
                if (days > 365) {
                    $scope.patient.age = Math.floor(days / 365);
                    $scope.patient.age_unit = 'Years';
                } else if (days > 30) {
                    $scope.patient.age = Math.floor(days / 30);
                    $scope.patient.age_unit = 'Months';
                } else {
                    $scope.patient.age = days;
                    $scope.patient.age_unit = 'Days';
                }
            } else {
                if (patient.age_unit == 'Years') {

                    $scope.calculateAge(patient, 'age');
                }
                else if (patient.age_unit == 'Months') {
                    if (((new Date()).getMonth() + 1) >= (patient.age % 12))
                        $scope.patient.dob = ((new Date()).getFullYear() - ~~(patient.age / 12)) + '-' + ((((new Date()).getMonth() + 1) - ($scope.patient.age % 12)).toString().length == 2 ? '' : '0') + (((new Date()).getMonth() + 1) - ($scope.patient.age % 12)) + '-01';
                    else
                        $scope.patient.dob = ((new Date()).getFullYear() - 1 - ~~(patient.age / 12)) + '-' + (((12 + ((new Date()).getMonth() + 1)) - ($scope.patient.age % 12)).toString().length == 2 ? '' : '0') + ((12 + ((new Date()).getMonth() + 1)) - ($scope.patient.age % 12)) + '-01';
                } else {
                    var leo = (new Date()).getDate();
                    if (leo >= patient.age % 30) {
                        console.log(patient.age);
                        console.log(leo);
                        //console.log(patient.age % 30);
                        var dob_days = ((new Date()).getFullYear() - ~~(patient.age / 365)) +
                            '-' + ((((new Date()).getMonth() + 1) -
                            ~~($scope.patient.age / 30)).toString().length == 2 ? '' : '0')
                            + (((new Date()).getMonth() + 1) - ~~($scope.patient.age / 30)) + '-'
                            + ($scope.patient.age.toString().length == 2 ? '' : '0')
                            + $scope.patient.age.toString();

                        console.log(dob_days);
                        $scope.patient.dob =  dob_days;
                    }else{
                        $scope.patient.dob = ((new Date()).getFullYear() - ~~(patient.age / 365)) + '-' + ((((new Date()).getMonth()) - ~~($scope.patient.age / 30)).toString().length == 2 ? '' : '0') + (((new Date()).getMonth()) - ~~($scope.patient.age / 30)) + '-' + (((30 + ((new Date()).getDate())) - ($scope.patient.age % 30)).toString().length == 2 ? '' : '0') + ((30 + ((new Date()).getDate())) - ($scope.patient.age % 30));
                }    }
            }
        };
        $scope.showPrompt = function (ev) {
            $mdDialog.show({
                controller: function ($scope) {
                    $scope.cancel = function () {
                        $state.reload();
                        $mdDialog.hide();
                    };
                    $scope.emergency_type = function (emmergency) {
                        console.log(emmergency);
                        var type = emmergency.type;
                        var name = emmergency.name;
                        if (angular.isDefined(type) == false) {
                            return sweetAlert("Please Enter Emergency Name", "", "error");
                        } else if (angular.isDefined(name) == false) {
                            return sweetAlert("Please Enter Emergency Name", "", "error");
                        }
                        var emergency_type = {
                            "emergency_type": type,
                            "emergency_name": name
                        }
                        $http.post('/api/emergency_type', emergency_type).then(function (data) {
                            var message = data.data.message;
                            var status = data.data.status;
                            if (status == 1) {
                                Helper.alert('EMERGENCY NAME ' + ' ' + message);
                            }
                            else if (status == 0) {
                                Helper.alert('EMERGENCY NAME ' + ' ' + message);
                            }
                            name = null;

                        });

                    }
                },
                templateUrl: '/views/modules/emergency/emergencyRegister.html',
                parent: angular.element(document.body),
                clickOutsideToClose: false,
                fullscreen: $scope.customFullscreen
            })

        };
        $scope.configCasualtry = function () {
            var config_casualty = {
                "id": 14,
                "department_name":"CASUALTY"
            }
            console.log(config_casualty);
            $http.post('/api/configCasualtry', config_casualty).then(function (data) {
                var message = data.data.message;
                var status = data.data.status;
                if (status == 1) {
                    Helper.alert('CASUALTY ' + ' ' + message);
                }
                else if (status == 0) {
                    Helper.alert('CASUALTY ' + ' ' + message);
                }

            });

        }

        $scope.getResidents = function (text) {
            $http.get('/api/searchResidences/' + text).then(function (data) {
                resdata = data.data;
            });
            return resdata;
        }
        $scope.patient_quick_registration = function (patient,residence) {
            var residence_id=residence.residence_id;
             console.log(residence_id);
            var first_name = patient.first_name;
            var middle_name = patient.middle_name;
            var last_name = patient.last_name;
            var gender = patient.gender;
            var dob = patient.dob;

            if (patient.dob instanceof Date) {
                dob = moment(patient.dob).format('YYYY-MM-DD');
            }
            console.log(dob);
            var mobile_number = patient.mobile_number;
            if (angular.isDefined(first_name) == false) {
                return sweetAlert("Please Enter FIRST NAME before SAVING", "", "error");
            } else if (angular.isDefined(middle_name) == false) {
                return sweetAlert("Please Enter MIDDLE NAME before SAVING", "", "error");
            } else if (angular.isDefined(last_name) == false) {
                return sweetAlert("Please Enter LAST NAME before SAVING", "", "error");
            } else if (angular.isDefined(residence_id) == false) {
                return sweetAlert("Please type the Residence Name and choose from the suggestions", "", "error");
            }
            var patient_residences = residence_id;
            var quick_registration = {
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

            $http.post('/api/quick_registrationEm', quick_registration).then(function (data) {
                $scope.quick_registration = data.data;
                console.log(data.data);
                if (data.data.status == 0) {
                    sweetAlert(data.data.data, "", "error");
                } else {
                    var patientData = data.data[0];
                    var accounts_number = data.data[1][0];
                    var residences = data.data[2][0];
                    var getLastVisit = data.data[3];
                    var object = {
                        'patientData': patientData,
                        'accounts_number': accounts_number,
                        'residences': residences,
                        'getLastVisit': getLastVisit
                    };
                    $scope.patient = null;
                    console.log(patientData);

                    $scope.item = object;
                    $mdDialog.show({
                        controller: function ($scope) {
                            var patientData = data.data[0];
                            var accounts_number = data.data[1][0];
                            var residences = data.data[2][0];
                            var getLastVisit = data.data[3];
                            console.log(accounts_number);
                            var object = {
                                'patientData': patientData,
                                'accounts_number': accounts_number,
                                'residences': residences,
                                'getLastVisit': getLastVisit
                            };
                            $scope.patient = null;
                            console.log(patientData);
                            $scope.patientData = patientData;
                            $scope.accounts_number = accounts_number;
                            $scope.residences = residences;
                            $scope.getLastVisit = getLastVisit;
                            $scope.item = object;
                            $http.get('/api/getexemption_services/' + facility_id).then(function (data) {
                                $scope.exemption_services = data.data;

                                $http.get('/api/exemption_type_list/' + user_id).then(function (data) {
                                    $scope.exemption_types = data.data;

                                });
                            });
                            $scope.getDepartment = function () {
                                    $http.get('/api/getClinic').then(function (data) {
                                        $scope.departments = data.data;
                                    })   
                            };
                            $http.get('/api/searchPatientCategory/'+facility_id).then(function (data) {

                                $scope.patientCategory = data.data;
                          });
                            $scope.getPricedItems = function (patient_category_selected) {
                                console.log(patient_category_selected);
                                var postData={facility_id:facility_id,patient_category:patient_category_selected};
                                $http.post('/api/getPricedItems', postData).then(function (data) {
                                    $scope.services = data.data;
                                });

                            }

                            $scope.state = [
                                {
                                    "id": "1",
                                    "department_name":"OUT PATIENT DEPARTMENT (OPD)"
                                }
                            ];
                            $scope.loadEmergency = function () {
                                    $http.get('/api/emergency_type_list').then(function (data) {
                                        $scope.emergency_list = data.data;
                                    });
                            };
                            $scope.enterEncounter = function (patient, residences, encounter, id, facility_id, account) {
                                var patientData = patient;
                                var residences = residences;
                                console.log(encounter);
                                var bill_category_id = encounter.payment_services.patient_category_id

                                var emergency_type_id = encounter.emergency_name.id;
                                var dept_id = encounter.department.id;
                                 if (angular.isDefined(encounter.payment_category) == false) {
                                    return sweetAlert("Please Type the Payment Category", "", "error");
                                } else if (angular.isDefined(encounter.payment_services) == false) {
                                    return sweetAlert("Please Select Service", "", "error");
                                } else {

                                    var patient_category=encounter.payment_category;
                                     console.log(patient_category);
                                    var service_category = encounter.payment_services;
                                    var service_id = encounter.payment_services.service_id;
                                    var price_id = encounter.payment_services.price_id;
                                    var item_type_id = encounter.payment_services.item_type_id;
                                    var patient_id = patient.id;
                                    var facility_id = facility_id;
                                    var user_id = $rootScope.currentUser.id;
                                    var payment_filter=encounter.payment_services.patient_category_id;
                                    var bill_category_id = encounter.payment_services.patient_category_id;
                                    var main_category_id = encounter.payment_services.patient_main_category_id;

                                    var enterEncounter = {
                                        'dept_id': dept_id,
                                        'payment_filter': payment_filter,
                                        'item_type_id': item_type_id,
                                        'patient_category': patient_category,
                                        'main_category_id': main_category_id,
                                        'bill_id': bill_category_id,
                                        'service_category': service_category,
                                        'service_id': service_id,
                                        'price_id': price_id,
                                        'patient_id': patient_id,
                                        'account_id': account,
                                        'emergency_type_id': emergency_type_id,
                                        'facility_id': facility_id,
                                        'user_id': user_id
                                    };
                                    console.log(enterEncounter)
                                    $http.post('/api/enterEncounterEmergency', enterEncounter).then(function (data) {
                                        $scope.registrationReport = data.data;
                                        if (data.data.status == 0) {

                                            sweetAlert(data.data.data, "", "error");
                                        } else {


                                            $mdDialog.show({
                                                controller: function ($scope) {
                                                    $scope.patientData = patientData;
                                                    $scope.patientResidences = residences;
                                                    $scope.cancel = function () {
                                                       $state.reload();
                                                        $mdDialog.hide();
                                                    };
                                                },
                                                templateUrl: '/views/modules/emergency/emergencyprintCard.html',
                                                parent: angular.element(document.body),
                                                clickOutsideToClose: false,
                                                fullscreen: $scope.customFullscreen
                                            })
                                        }
                                    });
                                }
                            }
                            $scope.enter_emergency_Exemption = function (exempt, patientData, account) {
                                var account_id = account;
                                var status_id = 2;
                                var quantity = 1;
                                var change = false;
                                var item_id = exempt.service.service_id;
                                var item_price_id = exempt.service.price_id;
                                var item_type_id = exempt.service.item_type_id;
                                var exemption_type_id = exempt.exemption_type_id.id;
                                var main_category_id = exempt.exemption_type_id.pay_cat_id;
                                var user_id = $rootScope.currentUser.id;
                                var facility_id = $rootScope.currentUser.facility_id;
                                var patient_id = patientData.id;
                                var bill_id = exempt.exemption_type_id.id;
                                var description = exempt.exemption_reason;
                                var emergency_type_id = exempt.emergency_name.id;
                                var patient_exemption = {
                                    'item_id': item_id,
                                    'emergency_type_id': emergency_type_id,
                                    'account_id': account_id,
                                    'change': change,
                                    'quantity': quantity,
                                    'status_id': status_id,
                                    'item_price_id': item_price_id,
                                    'item_type_id': item_type_id,
                                    'exemption_type_id': exemption_type_id,
                                    'user_id': user_id,
                                    'main_category_id': main_category_id,
                                    'exemption_reason': description,
                                    'facility_id': facility_id,
                                    'bill_id': bill_id,
                                    'patient_id': patient_id
                                };
                                console.log(patient_exemption);
                                $http.post('/api/patient_exemption_emergency', patient_exemption).then(function (data) {
                                    var msg = data.data.msg;
                                    $scope.ok = data.data.status;
                                    var statuss = data.data.status;
                                    if (statuss == 0) {
                                        swal(
                                            'Error',
                                            msg,
                                            'error'
                                        );
                                    }
                                    else {
                                        swal(
                                            'Success',
                                            msg,
                                            'success'
                                        );
                                    }
                                });
                            }
                            $scope.searchPatientCategory = function (searchKey) {

                                $http.get('/api/searchPatientCategory/' + searchKey).then(function (data) {
                                    patientCategory = data.data;
                                });
                                return patientCategory;
                            }
                            $scope.getPricedItems = function (patient_category_selected) {
                                console.log(patient_category_selected);
                                var postData={facility_id:facility_id,patient_category:patient_category_selected};
                                $http.post('/api/getPricedItems', postData).then(function (data) {
                                    $scope.services = data.data;
                                });

                            }
                            $scope.cancel = function () {
                                $state.reload();
                                $scope.searchText = {};
                                $mdDialog.hide();
                            };
                        },
                        templateUrl: '/views/modules/emergency/emergencyencounterModal.html',
                        parent: angular.element(document.body),
                        clickOutsideToClose: false,
                        fullscreen: $scope.customFullscreen
                    })

                }
            });
        };

        $scope.selectedQuick = function (patient) {
            if (typeof patient != 'undefined') {
                $mdDialog.show({
                    controller: function ($scope) {

                        $scope.patientData = patientData;
                        $scope.accounts_number = accounts_number;
                        $scope.residences = residences;
                        $scope.getLastVisit = getLastVisit;
                        $scope.item = object;
                        $http.get('/api/getexemption_services/' + facility_id).then(function (data) {
                            $scope.exemption_services = data.data;

                            $http.get('/api/exemption_type_list/' + user_id).then(function (data) {
                                $scope.exemption_types = data.data;

                            });
                        });
                        $scope.getDepartment = function () {
                            $http.get('/api/getClinic').then(function (data) {
                                $scope.departments = data.data;
                            })
                        };
                        $http.get('/api/searchPatientCategory/'+facility_id).then(function (data) {

                            $scope.patientCategory = data.data;
                        });
                        $scope.getPricedItems = function (patient_category_selected) {
                            console.log(patient_category_selected);
                            var postData={facility_id:facility_id,patient_category:patient_category_selected};
                            $http.post('/api/getPricedItems', postData).then(function (data) {
                                $scope.services = data.data;
                            });

                        }

                        $scope.state = [
                            {
                                "id": "1",
                                "department_name":"OUT PATIENT DEPARTMENT (OPD)"
                            }
                        ];
                        $scope.loadEmergency = function () {
                            $http.get('/api/emergency_type_list').then(function (data) {
                                $scope.emergency_list = data.data;
                            });
                        };
                        $scope.enterEncounter = function (patient, residences, encounter, id, facility_id, account) {
                            var patientData = patient;
                            var residences = residences;
                            console.log(encounter);
                            var bill_category_id = encounter.payment_services.patient_category_id

                            var emergency_type_id = encounter.emergency_name.id;
                            var dept_id = encounter.department.id;
                            if (angular.isDefined(encounter.payment_category) == false) {
                                return sweetAlert("Please Type the Payment Category", "", "error");
                            } else if (angular.isDefined(encounter.payment_services) == false) {
                                return sweetAlert("Please Select Service", "", "error");
                            } else {

                                var patient_category=encounter.payment_category;
                                console.log(patient_category);
                                var service_category = encounter.payment_services;
                                var service_id = encounter.payment_services.service_id;
                                var price_id = encounter.payment_services.price_id;
                                var item_type_id = encounter.payment_services.item_type_id;
                                var patient_id = patient.id;
                                var facility_id = facility_id;
                                var user_id = $rootScope.currentUser.id;
                                var payment_filter=encounter.payment_services.patient_category_id;
                                var bill_category_id = encounter.payment_services.patient_category_id;
                                var main_category_id = encounter.payment_services.patient_main_category_id;

                                var enterEncounter = {
                                    'dept_id': dept_id,
                                    'payment_filter': payment_filter,
                                    'item_type_id': item_type_id,
                                    'patient_category': patient_category,
                                    'main_category_id': main_category_id,
                                    'bill_id': bill_category_id,
                                    'service_category': service_category,
                                    'service_id': service_id,
                                    'price_id': price_id,
                                    'patient_id': patient_id,
                                    'account_id': account,
                                    'emergency_type_id': emergency_type_id,
                                    'facility_id': facility_id,
                                    'user_id': user_id
                                };
                                console.log(enterEncounter)
                                $http.post('/api/enterEncounterEmergency', enterEncounter).then(function (data) {
                                    $scope.registrationReport = data.data;
                                    if (data.data.status == 0) {

                                        sweetAlert(data.data.data, "", "error");
                                    } else {


                                        $mdDialog.show({
                                            controller: function ($scope) {
                                                $scope.patientData = patientData;
                                                $scope.patientResidences = residences;
                                                $scope.cancel = function () {
                                                    $state.reload();
                                                    $mdDialog.hide();
                                                };
                                            },
                                            templateUrl: '/views/modules/emergency/emergencyprintCard.html',
                                            parent: angular.element(document.body),
                                            clickOutsideToClose: false,
                                            fullscreen: $scope.customFullscreen
                                        })
                                    }
                                });
                            }
                        }
                        $scope.enter_emergency_Exemption = function (exempt, patientData, account) {
                            var account_id = account;
                            var status_id = 2;
                            var quantity = 1;
                            var change = false;
                            var item_id = exempt.service.service_id;
                            var item_price_id = exempt.service.price_id;
                            var item_type_id = exempt.service.item_type_id;
                            var exemption_type_id = exempt.exemption_type_id.id;
                            var main_category_id = exempt.exemption_type_id.pay_cat_id;
                            var user_id = $rootScope.currentUser.id;
                            var facility_id = $rootScope.currentUser.facility_id;
                            var patient_id = patientData.id;
                            var bill_id = exempt.exemption_type_id.id;
                            var description = exempt.exemption_reason;
                            var emergency_type_id = exempt.emergency_name.id;
                            var patient_exemption = {
                                'item_id': item_id,
                                'emergency_type_id': emergency_type_id,
                                'account_id': account_id,
                                'change': change,
                                'quantity': quantity,
                                'status_id': status_id,
                                'item_price_id': item_price_id,
                                'item_type_id': item_type_id,
                                'exemption_type_id': exemption_type_id,
                                'user_id': user_id,
                                'main_category_id': main_category_id,
                                'exemption_reason': description,
                                'facility_id': facility_id,
                                'bill_id': bill_id,
                                'patient_id': patient_id
                            };
                            console.log(patient_exemption);
                            $http.post('/api/patient_exemption_emergency', patient_exemption).then(function (data) {
                                var msg = data.data.msg;
                                $scope.ok = data.data.status;
                                var statuss = data.data.status;
                                if (statuss == 0) {
                                    swal(
                                        'Error',
                                        msg,
                                        'error'
                                    );
                                }
                                else {
                                    swal(
                                        'Success',
                                        msg,
                                        'success'
                                    );
                                }
                            });
                        }
                        $scope.searchPatientCategory = function (searchKey) {

                            $http.get('/api/searchPatientCategory/' + searchKey).then(function (data) {
                                patientCategory = data.data;
                            });
                            return patientCategory;
                        }
                        $scope.getPricedItems = function (patient_category_selected) {
                            console.log(patient_category_selected);
                            var postData={facility_id:facility_id,patient_category:patient_category_selected};
                            $http.post('/api/getPricedItems', postData).then(function (data) {
                                $scope.services = data.data;
                            });

                        }
                        $scope.cancel = function () {
                            $state.reload();
                            $scope.searchText = {};
                            $mdDialog.hide();
                        };
                    },
                    templateUrl: '/views/modules/emergency/emergencyencounterModal.html',
                    parent: angular.element(document.body),
                    clickOutsideToClose: false,
                    fullscreen: $scope.customFullscreen
                })
            }
        };

        $scope.getPatients = function (text) {
            return Helper.getPatients(text,facility_id)
                .then(function (response) {
                    console.log(response.data);
                    return response.data;
                });
        };
        $scope.selectedResidencekin = function (residence) {
            if (typeof residence != 'undefined') {
                kin_residence = residence.residence_id;
                console.log(kin_residence)
            }
            $scope.residence = residence;
        }
        $scope.selectedPatient = function (patient, ev) {
            var info = {};
            if (typeof  patient != 'undefined') {
                var id = patient.id;
                console.log(id);
            }
            $scope.patient = patient;
            if (typeof patient != 'undefined') {
                $mdDialog.show({
                    controller: function ($scope) {
                        $scope.patientLoaded = patient;
                        $scope.infos = info;
                        var id = patient.id;
                        $http.get('/api/patientsInformation/' + id).then(function (data) {
                            $scope.infos = data.data[0];
                            console.log($scope.infos)
                        });
                        $scope.cancel = function () {
                            $state.reload();
                            $scope.searchText = {};
                            $mdDialog.hide();
                        };
                        $scope.patient_edit = function (patient,residence) {
                            console.log(residence)
                            var resident_id =  residence.residence_id;
                            console.log(resident_id);
                            var marital_id = patient.marital;
                            var country_id = patient.country_name.id;
                            var occupation_id = patient.occupation.id;
                            var tribe_id = patient.tribe.id;
                            console.log(patient);
                            console.log(country_id);
                            console.log(occupation_id);
                            console.log(marital_id);
                            console.log(tribe_id);
                            var first_name = patient.first_name;
                            var gender = patient.gender;
                            var patient_id = patient.id;
                            var middle_name = patient.middle_name;
                            var last_name = patient.last_name;
                            var dob = patient.dob;
                            if (patient.dob instanceof Date) {
                                dob = moment(patient.dob).format('YYYY-MM-DD');
                            }
                            var mobile_number = patient.mobile_number;
                            var edit_patient = {
                                'first_name': first_name,
                                'middle_name': middle_name,
                                'mobile_number': mobile_number,
                                'last_name': last_name,
                                'dob': dob,
                                'patient_id': patient_id,
                                'gender': gender,
                                'marital_id': marital_id,
                                'country_id': country_id,
                                'occupation_id': occupation_id,
                                'tribe_id': tribe_id,
                                'residence_id': resident_id,
                                'user_id': user_id
                            };
                            $http.post('/api/patient_edit', edit_patient).then(function (data) {
                                var message = data.data.data;
                                var status = data.data.status;
                                if (status == 1) {
                                    swal(
                                        message,
                                        'Successfully',
                                        'success'
                                    )

                                }
                            });
                        }
                        $scope.edit_all_data = function (patient, others) {
                            console.log(patient);
                            console.log(others);
                            var first_name = patient.first_name;
                            var gender = patient.gender;
                            var patient_id = patient.id;
                            var middle_name = patient.middle_name;
                            var last_name = patient.last_name;
                            var dob = patient.dob;
                            var mobile_number = patient.mobile_number;
                            var country_name = others.country_name.id;
                            var marital_status = others.marital_status.id;
                            var next_kin_residence = others.next_kin_residence.residence_id;
                            var residence_id = others.resedence_id.residence_id;
                            var occupation_name = others.occupation_name.id;
                            var relationship = others.relationship.relationship;
                            var tribe_name = others.tribe_name.id;
                            var next_of_kin_name = others.next_of_kin_name;
                            var mobile_number_next_kin = others.mobile_number_next_kin;
                            var edit_all_data = {
                                'first_name': first_name,
                                'middle_name': middle_name,
                                'mobile_number': mobile_number,
                                'last_name': last_name,
                                'dob': dob,
                                'patient_id': patient_id,
                                'gender': gender,
                                'country_id': country_name,
                                'marital_id': marital_status,
                                'next_residence_id': next_kin_residence,
                                'resident_id': residence_id,
                                'occupation_id': occupation_name,
                                'relationship': relationship,
                                'tribe_id': tribe_name,
                                'next_of_kin_name': next_of_kin_name,
                                'kin_mobile_number': mobile_number_next_kin,
                                'user_id': user_id
                            };
                            console.log(edit_all_data);
                            $http.post('/api/edit_all_data', edit_all_data).then(function (data) {
                                var message = data.data.message;
                                var status = data.data.status;
                                if (status == 1) {
                                    Helper.alert('PATIENT ' + ' ' + message);
                                }
                            });
                        }
                        $scope.FirstForm = function () {
                            $scope.firstForm = true;
                        }
                        $scope.FirstForm();
                        $scope.NextForm = function () {
                            console.log(patient);
                            $scope.firstForm = false;
                            $scope.secondForm = true;
                        }
                        $http.get('/api/getRelationships').then(function(data) {
                            $scope.relationships = data.data;
                        });
                        $http.get('/api/getMaritalStatus').then(function(data) {
                            $scope.maritals = data.data;
                        });

                        $scope.getResidents = function (text) {

                            $http.get('/api/searchResidences/' + text).then(function (data) {
                                resdata = data.data;
                            });
                            return resdata;
                        }
                        $scope.showSearchMarital = function (text) {
                            console.log(text);
                            $http.get('/api/getMaritalStatus/' + text).then(function (data) {
                                maritals = data.data;
                            });
                            return maritals;
                        }
                        $scope.showSearchTribe = function (text) {
                            $http.get('/api/getTribe/' + text).then(function (data) {
                                tribe = data.data;
                            });
                            return tribe;
                        }
                        $scope.showSearchOccupation = function (text) {
                            $http.get('/api/getOccupation/' + text).then(function (data) {
                                occupation = data.data;
                            });
                            return occupation;
                        }
                        $scope.getCountry = function (text) {
                            $http.get('/api/getCountry/' + text).then(function (data) {
                                country = data.data;
                            });
                            return country;
                        }
                        $scope.getRelationships = function (text) {
                            $http.get('/api/getRelationships/' + text).then(function (data) {
                                relationships = data.data;
                            });
                            return relationships;
                        }
                    },
                    templateUrl: '/views/modules/emergency/updatePatient.html',
                    parent: angular.element(document.body),
                    clickOutsideToClose: false,
                    fullscreen: $scope.customFullscreen // Only for -xs, -sm breakpoints.
                })
            }
        };

        //REGISTER PATIENT
        $scope.patient_emergency_registration = function (patient) {
            var first_name = patient.first_name;
            var middle_name = patient.middle_name;
            var last_name = patient.last_name;
            var gender = patient.gender.gender;
            var dob = patient.dob;
            var mobile_number = patient.mobile_number;


            if (angular.isDefined(first_name) == false) {
                return sweetAlert("Please Enter FIRST NAME before SAVING", "", "error");
            }

            else if (angular.isDefined(middle_name) == false) {
                return sweetAlert("Please Enter MIDDLE NAME before SAVING", "", "error");
            }

            else if (angular.isDefined(last_name) == false) {
                return sweetAlert("Please Enter LAST NAME before SAVING", "", "error");
            }
            else if (angular.isDefined(patient.resedence_id) == false) {
                return sweetAlert("Please type the Residence Name and choose from the suggestions", "", "error");
            }
            var patient_residences = patient.resedence_id.residence_id;
            var quick_registration = {
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

            $http.post('/api/patient_emergence_registration', quick_registration).then(function (data) {
                $scope.quick_registration = data.data;
                ////console.log(data.data);
                if (data.data.status == 0) {

                    sweetAlert(data.data.data, "", "error");
                } else {
                    $scope.patient = null;
                    quick_registration = $scope.quick_registration;
                    $scope.viewItem(quick_registration);
                }
            });

        }
        $scope.printEm=function () {
                                //location.reload();
                                var DocumentContainer = document.getElementById('em');
                                var WindowObject = window.open("", "PrintWindow",
                                    "width=900,height=700,top=50,left=450,toolbars=no,scrollbars=no,status=no,resizable=yes");
                                WindowObject.document.title = " GoT-HOMIS";
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