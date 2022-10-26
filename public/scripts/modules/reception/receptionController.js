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
        .controller('receptionController', receptionController);
        receptionController.$inject =
        ['$http','$scope', '$mdDialog', '$state', '$rootScope', 'toastr','Helper','Reception','Tribe','Search'];
        function receptionController
        ($http,$scope, $mdDialog, $state, $rootScope, toastr,Helper,Reception,Tribe,Search) {
        var formdata = new FormData();
        $scope.today = new Date();
        $scope.patient = {};
        $scope.getTheFiles = function ($files) {
            angular.forEach($files, function (value, key) {
                formdata.append(key, value);
            });
        };
        $scope.showFirstForm = function (patient, others, residence) {
            $scope.others = others;
            $scope.residence = residence;
            $scope.firstFormShow = true;
            $scope.secondFormShow = false;
        }
        $scope.showFirstForm();
        $scope.showNextForm = function (patient) {
            $scope.patient = patient;
            $scope.firstFormShow = false;
            $scope.secondFormShow = true;

        }
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
            //SEARCH TRIBES
            $scope.searchClientTribes = function (text) {
                if(text.length>=3){
                    return Search.getLabPatients(text)
                        .then(function (response) {
                            console.log(response.data);
                            return   response.data;
                        });
                }
            };
            $scope.patient_quick_registration = function (patient) {
                var first_name = patient.first_name;
                var middle_name = patient.middle_name;
                var last_name = patient.last_name;
                var gender = patient.gender;
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
                else if (!gender) {
                    return sweetAlert("Please select the patient's gender.", "", "error");
                }
                else if (!patient.dob) {
                    return sweetAlert("Please fill date of birth/details of the patient.", "", "error");
                }
                else if (angular.isDefined(residence_id) == false) {
                    return sweetAlert("Please type the Residence Name and choose from the suggestions", "", "error");
                }

                var dob = moment(patient.dob).format("YYYY-MM-DD");

                if (!patient.tribe) {
                    return sweetAlert("Patient's tribe is required, please fill in.", "", "error");
                }
                var tribe = patient.tribe.id;
                var quick_registration = {
                    "tribe": tribe,
                    "first_name": first_name,
                    "middle_name": middle_name,
                    "last_name": last_name,
                    "dob": dob,
                    "gender": gender,
                    "mobile_number": mobile_number,
                    "residence_id": residence_id,
                    "facility_id": facility_id,
                    "user_id": user_id
                };


                if($scope.confirmedNotDuplicate == undefined)
                    quick_registration["confirmedNotDuplicate"]=0;


                $http.post('/api/client_registration', quick_registration).then(function (data) {
                    if(data.data.status == 'duplicate'){
                        swal({
                            title: 'Possible Duplication.',
                            html: "<b style='color:red'>The patient may have been registered before with MRN "+data.data.mrn+".</b><br /><hr />If you are sure this is the first time visit, press PROCEED.",
                            type: 'warning',
                            showCancelButton: true,
                            confirmButtonColor: '#3085d6',
                            cancelButtonColor: '#d33',
                            confirmButtonText: 'Proceed'
                        }).then(function () {
                            $scope.confirmedNotDuplicate = 1;
                            $scope.patient_quick_registration(patient);
                        }, function(){ $scope.confirmedNotDuplicate = undefined; return;});
                    }else
                        $scope.confirmedNotDuplicate = 0;

                    //tricky block aided by the else above
                    if($scope.confirmedNotDuplicate == undefined)
                        return;

                    $scope.confirmedNotDuplicate = undefined;
                    // end tricky block

                    $scope.quick_registration = data.data;
                    ////console.log($scope.quick_registration);
                    if (data.data.status == 0) {

                        sweetAlert(data.data.data, "", "error");
                    } else {
                        var ev = null;
                        var patientData = data.data[0];
                        var accounts_number = data.data[1][0];
                        var residences = data.data[2][0];

                        //console.log(data.data[3]);

                        var getLastVisit = data.data[3];
                        $scope.patient.first_name = null;
                        $scope.patient.middle_name = null;
                        $scope.patient.last_name = null;
                        $scope.patient.age = null;
                        $scope.patient.tribe = null;
                        $scope.patient.gender = null;
                        $scope.patient.mobile_number = null;
                        $scope.patient.tribe = null;
                        $scope.residence = null;
                        $mdDialog.show({
                            controller: function ($scope) {
                                $scope.patientData = patientData;

                                console.log($scope.patientData);
                                $scope.accounts_number = accounts_number;
                                $scope.residences = residences;
                                $scope.getLastVisit = getLastVisit;
                                $scope.cancel = function () {
                                    $mdDialog.hide();
                                };
                            },
                            templateUrl: '/views/modules/registration/encounterModal.html',
                            parent: angular.element(document.body),
                            clickOutsideToClose: false,
                            fullscreen: $scope.customFullscreen // Only for -xs, -sm breakpoints.
                        });


                    }
                });


            };

            //TRIBES LISTS
            $scope.tribeLists = function () {
                Tribe.get($scope.query, function (response) {
                    console.log('all_tribe', response);
                    var ApiResponseStatus = response.status;
                    var ApiResponseData = response.data.data.length;
                    console.log(ApiResponseData);
                    console.log(ApiResponseStatus);
                    if (ApiResponseStatus !== 200) {
                        toastr.error("Something went wrong you may not get data");
                    }

                    else if (ApiResponseData == 0) {
                        toastr.error("No data set");
                    }
                    $scope.tribes = response.data;
                    console.log(response.data)
                });
            };
        $scope.getResidence = function (text) {
            return Helper.getResidence(text)
                .then(function (response) {
                    return response.data;
                });
        };
        $scope.getPatients = function (searchKey) {
            var dataToPost = {searchKey: searchKey};
            $http.post('/api/getSeachedPatients', dataToPost).then(function (data) {
                patientsList = data.data;

            });
            return patientsList;
        }

        $scope.editResidence = function (text) {
            return Helper.getResidence(text)
                .then(function (response) {
                    return response.data;
                });
        };

        $scope.selectedResidence = function (residence) {
            if (typeof residence != 'undefined') {
                residence_id = residence.residence_id;
                console.log(residence_id);
            }
            $scope.residence = residence;
        }

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

        $scope.getResidents = function (text) {
            $http.get('/api/searchResidences/' + text).then(function (data) {
                resdata = data.data;
            });
            return resdata;
        }

    }

})();