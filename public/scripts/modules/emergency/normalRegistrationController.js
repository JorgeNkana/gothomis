/**
 * Created by japhari on 07/03/2017.
 */

(function() {

    'use strict';

    angular
        .module('authApp').directive('ngFiles', ['$parse', function ($parse) {

        function fn_link(scope, element, attrs) {
            var onChange = $parse(attrs.ngFiles);
            element.on('change', function (event) {
                onChange(scope, { $files: event.target.files });
            });
        };

        return {
            link: fn_link
        }
    } ])
        .controller('normalRegistrationController', normalRegistrationController);

    function normalRegistrationController($http, $auth, $rootScope,$state,$timeout,$interval,$location,$scope,$uibModal) {
        var formdata = new FormData();

        $scope.getTheFiles = function ($files) {

            angular.forEach($files, function (value, key) {
                formdata.append(key, value);

            });

        };
        $scope.isNavCollapsed = false;
        $scope.isCollapsed = true;
        $scope.isCollapsedHorizontal = true;
        var user=$rootScope.currentUser;
        var user_name=$rootScope.currentUser.id;
        var facility_id=$rootScope.currentUser.facility_id;
        var patientCategory =[];
        var patientService =[];
        var resdata =[];
        var patientsList=[];
        //INSURANCES API
        $http.get('/api/getInsurances').then(function(data) {
            $scope.insurances=data.data;


        });

        $scope.verification = function (item) {
            $http.get('https://verification.nhif.or.tz/NHIFService/breeze/Verification/GetCard?CardNo='+item).then(function (data) {
                //console.log(data);
                if(data.data.StatusDescription=='Active'){

                    $scope.patient.first_name=data.data.FirstName;
                    $scope.patient.middle_name=data.data.MiddleName;
                    $scope.patient.last_name=data.data.LastName;
                    $scope.patient.gender=data.data.Gender;
                    $scope.patient.dob=data.data.DateOfBirth.substr(0,data.data.DateOfBirth.indexOf('T'));
                    $scope.nhif_patient = {"first_name":data.data.FirstName,
                        "middle_name":data.data.MiddleName,
                        "last_name":data.data.LastName,"gender":data.data.Gender,
                        "dob":data.data.DateOfBirth};

                    swal("CARD VALID") ;
                }
                else {

                    swal("kalipe pesa kijana");
                }
            });

        }
//END INSURANCE API
        //user menu
        $scope.printUserMenu=function (user_id) {

            $http.get('/api/getUsermenu/'+user_id ).then(function(data) {
                $scope.menu=data.data;
                //console.log($scope.menu);

            });

        };

        $http.get('/api/getUserImage/'+user_name).then(function(data) {
            $scope.photo='/uploads/'+data.data[0].photo_path;
            //console.log($scope.photo);

        });

        var user_id=$rootScope.currentUser.id;
        var facility=$rootScope.currentUser.facility_id;
        $scope.printUserMenu(user_id);


        //    TABS


        $scope.tabs = [

        ];






        $scope.getPatients = function (searchKey) {

            if (searchKey.length>3){
                $http.get('/api/getSearchedCasualty/'+searchKey).then(function(data) {
                    patientsList=data.data;
                    //console.log($scope.patients);

                });
            }
            //console.log(searchKey);
            return patientsList;
        }
        $scope.alertMe = function() {
            setTimeout(function() {
                $window.swal(
                    'Use proper ways to avoid Mistakes',
                    username,
                    'error'
                );
            });
        };

        $scope.model = {
            name: 'Tabs'
        };


        //    @END OF TABS

        //Accordion

        $scope.oneAtATime = true;




        $scope.status = {
            isCustomHeaderOpen: false,
            isFirstOpen: true,
            isFirstDisabled: false
        };
        //Accordion end
        //SEARCH RESIDENCES
        $scope.showSearchResidences = function(searchKey) {

            $http.get('/api/searchResidences/'+searchKey).then(function(data) {
                resdata = data.data;
            });
            ////console.log(resdata);
            return resdata;
        }
        //@END SEARCH RESIDENCES

        //SEARCH PATIENT SERVICES
        $scope.patientService = function() {
            var searchKey={'patient_category':$scope.encounter.payment_category.patient_category,'item_name':$scope.encounter};
            //console.log($scope.encounter);
            $http.post('/api/searchPatientServices',searchKey).then(function(data) {
                patientService = data.data;
            });
            //console.log(resdata);
            return patientService;
        }
        //@END SEARCH PATIENT SERVICES

        //PRICED ITEMS
        $scope.getPricedItems=function (patient_category_selected) {
            //console.log(patient_category_selected);
            $http.get('/api/getPricedItems/'+patient_category_selected).then(function(data) {
                $scope.services=data.data;
            });

        }
        //@END PRICED ITEMS

        //SEARCH PATIENT CATEGORIES
        $scope.searchPatientCategory = function(searchKey) {

            $http.get('/api/searchPatientCategory/'+searchKey).then(function(data) {
                patientCategory = data.data;
            });
            ////console.log(resdata);
            return patientCategory;
        }

        //@END SEARCH PATIENT CATEGORIES



        //MODAL FOR EMMERGENCE REGISTRATION

        $scope.viewItem = function (quick_registration) {
            $scope.quick_registration = quick_registration;

            //console.log(quick_registration.first_name);
            var modalInstance = $uibModal.open({
                templateUrl: '/views/modules/emergency/emergencyencounterModal.html',
                size: 'lg',
                animation: true,
                controller: 'emergencyModal',
                resolve:{
                    quick_registration: function () {
                        //console.log($scope.quick_registration);
                        return $scope.quick_registration ;
                    }
                }


            });

            modalInstance.result.then(function(quick_registration) {
                $scope.quick_reg = quick_registration;
                //console.log($scope.quick_reg);
            });
        }
//@END MODAL FOR EMMERGENCE REGISTRATION

        //OPEN DIALOG FOR SERVICES

        $scope.openDialogForServices = function (selectedPatient) {
            //console.log(selectedPatient);
            $scope.quick_registration =selectedPatient;


            //console.log($scope.quick_registration);
            var modalInstance = $uibModal.open({
                templateUrl: '/views/modules/emergency/emergencyencounterModal.html',
                size: 'lg',
                animation: true,
                controller: 'emergencyModal',
                resolve:{
                    quick_registration: function () {
                        $scope.cardDetails=$scope.quick_registration;
                        //console.log($scope.cardDetails);
                        return $scope.quick_registration ;
                    }
                }


            });

            modalInstance.result.then(function(quick_registration) {
                $scope.quick_reg = quick_registration;
                //console.log($scope.quick_reg);
            });
        }



        //OPEN DIALOG FOR URGENCY MODAL

        $scope.openDialogForUrgency = function (selectedPatient) {
            //console.log(selectedPatient);
            $scope.quick_registration =selectedPatient;


            //console.log($scope.quick_registration);
            var modalInstance = $uibModal.open({
                templateUrl: '/views/modules/emergency/emergencyencounterModal.html',
                size: 'lg',
                animation: true,
                controller: 'urgencyModal',
                resolve:{
                    quick_registration: function () {
                        $scope.cardDetails=$scope.quick_registration;
                        //console.log($scope.cardDetails);
                        return $scope.quick_registration ;
                    }
                }


            });

            modalInstance.result.then(function(quick_registration) {
                $scope.quick_reg = quick_registration;
                //console.log($scope.quick_reg);
            });
        }



        //@END OPEN DIALOG FOR SERVICES
        // Parse the resolve object
        function parseResolve(quick_registration) {
            if (typeof quick_registration === 'string') {
                return {
                    quick_registration: function() {
                        return quick_registration;
                    }
                }
            }
            else if (typeof quick_registration === 'object') {
                //var resolve = {};
                var resolve = $scope.quick_registration;
                angular.forEach(quick_registration, function(value, key) {
                    resolve[key] = function() {
                        ////console.log(value);
                        return value;
                    }
                })
                //console.log(resolve);
                return resolve;
            }
        }




        //REGISTER PATIENT
        $scope.patient_urgency_registration=function (patient) {
            var first_name=patient.first_name;
            var middle_name=patient.middle_name;
            var last_name=patient.last_name;
            var gender=patient.gender;
            var dob=patient.dob;
            var mobile_number=patient.mobile_number;


            if (angular.isDefined(first_name)==false) {
                return sweetAlert("Please Enter FIRST NAME before SAVING", "", "error");
            }

            else if (angular.isDefined(middle_name)==false) {
                return sweetAlert("Please Enter MIDDLE NAME before SAVING", "", "error");
            }

            else if (angular.isDefined(last_name)==false) {
                return sweetAlert("Please Enter LAST NAME before SAVING", "", "error");
            }
            else if (angular.isDefined(patient.resedence_id)==false) {
                return sweetAlert("Please type the Residence Name and choose from the suggestions", "", "error");
            }
            var patient_residences=patient.resedence_id.residence_id;
            var quick_registration={"first_name":first_name,"middle_name":middle_name,"last_name":last_name,"dob":dob,"gender":gender,"mobile_number":mobile_number,"residence_id":patient_residences,"facility_id":facility_id,"user_id":user_id}


            $http.post('/api/urgency_registration',quick_registration).then(function(data) {
                $scope.quick_registration=data.data;
                ////console.log(data.data);
                if(data.data.status ==0){

                    sweetAlert(data.data.data, "", "error");
                }else{
                    $scope.patient = null;
                    quick_registration=$scope.quick_registration;
                    $scope.viewItem(quick_registration);


                }
            });



        }

        //@END REGISTER PATIENT



        // exemptions======================================================


        $scope.exemption_type_list=function () {
            $http.get('/api/exemption_type_list').then(function(data) {
                $scope.exemption_types=data.data;


            });
        }

        $scope.exemption_type_list();

        $http.get('/api/getexemption_services/'+facility_id).then(function(data) {
            $scope.exemption_services=data.data;
        });


        $scope.exemption_registration=function (exempt,patientData) {

            //console.log(patientData,exempt)
            var status_id = 2;
            var reason_for_revoke = "..";



            if(exempt==undefined){
                swal(
                    'Feedback..',
                    'FILL ALL FIELDS',
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
                var patient=patientData.id;
                var patient_category=exempt.service.patient_category;
                var service_category=exempt.service;
                var service_id=exempt.service.service_id;
                var price_id=exempt.service.price_id;
                var item_type_id=exempt.service.item_type_id;
                var patient_id=patient;
                var facility_id=exempt.service.facility_id;
                var user_id=$rootScope.currentUser.id;
                var payment_filter=exempt.exemption_type_id;

                var bill_category_id=exempt.exemption_type_id;
                var main_category_id=3;

                var enterEncounter={'payment_filter':payment_filter,'item_type_id':item_type_id,'patient_category':patient_category,'main_category_id':main_category_id,'bill_id':bill_category_id,
                    'service_category':service_category,'service_id':service_id,'price_id':price_id,'patient_id':patient_id ,'facility_id':facility_id,'user_id':user_id};


                var status_id=2;

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

                    $http.post('/api/urgencyEncounter',enterEncounter).then(function(data) {
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






        // exemptions======================================================


    }

})();