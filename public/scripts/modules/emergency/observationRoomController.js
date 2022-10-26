/**
 * Created by japhari on 07/03/2017.
 */

(function() {

    'use strict';

    angular
        .module('authApp')

        .controller('observationRoomController', observationRoomController);

    function observationRoomController($http, $auth, $rootScope,$state,$timeout,$interval,$location,$scope,$uibModal) {

        $scope.isNavCollapsed = false;
        $scope.isCollapsed = true;
        $scope.isCollapsedHorizontal = true;
        var user=$rootScope.currentUser;
        var user_name=$rootScope.currentUser.id;
        var facility_id=$rootScope.currentUser.facility_id;
        var patientCategory =[];
        var patientService =[];
        var resdata =[];

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

        $scope.patient_urgent_registratione=function (patient) {
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
            var quick_registration=
                {
                    "first_name":first_name,
                    "middle_name":middle_name,
                    "last_name":last_name,
                    "dob":dob,
                    "gender":gender,
                    "mobile_number":mobile_number,
                    "residence_id":patient_residences,
                    "facility_id":facility_id,
                    "user_id":user_id
                }


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
        $scope.showSearchResidences = function(searchKey) {

            $http.get('/api/searchResidencesEm/'+searchKey).then(function(data) {
                resdata = data.data;
            });
            ////console.log(resdata);
            return resdata;
        }





        $scope.patientService = function() {
            var searchKey={'patient_category':$scope.encounter.payment_category.patient_category,'item_name':$scope.encounter};
            //console.log($scope.encounter);
            $http.post('/api/searchPatientServices',searchKey).then(function(data) {
                patientService = data.data;
            });
            //console.log(resdata);
            return patientService;
        }


        $scope.searchPatientCategory = function(searchKey) {

            $http.get('/api/searchPatientCategoryEm/'+searchKey).then(function(data) {
                patientCategory = data.data;
            });
            ////console.log(resdata);
            return patientCategory;
        }


        $scope.getPricedItems=function (patient_category_selected) {
            //console.log(patient_category_selected);
            $http.get('/api/getPricedItems/'+patient_category_selected).then(function(data) {
                $scope.services=data.data;
            });

        }




    }

})();