/**
 * Created by USER on 2017-05-22.
 */

/**
 * Created by USER on 2017-02-13.
 */
(function() {

    'use strict';

    angular
        .module('authApp')
        .controller('VctController', VctController);

    function VctController($http, $auth, $rootScope,$state,$location,$scope,$timeout,$uibModal,$mdDialog) {
        $scope.setTab = function(newTab){
            $scope.tab = newTab;
        };
        $scope.isSet = function(tabNum){
            return $scope.tab === tabNum;
        }
        $scope.oneAtATime=true;
        //loading menu
        var user_id=$rootScope.currentUser.id;
        var  facility_id=$rootScope.currentUser.facility_id;
        var resdata=[];
        $http.get('/api/getLoginUserDetails/'+user_id ).then(function(data) {
            $scope.menudetalis=data.data;
        });
        $http.get('/api/getUsermenu/'+user_id ).then(function(data) {
            $scope.menu=data.data;
        });



        $scope.department_list=function () {

            $http.get('/api/department_list').then(function(data) {
                $scope.departments=data.data;

            });
        }

        $scope.showSearch = function(searchKey) {


            $http.get('/api/searchClinicpatientFromDb/'+searchKey).then(function(data) {
                resdata = data.data;
            });


            return resdata;


        }
        $scope.ClinicQueue=function () {
            $http.get('/api/searchClinicpatientQueue/'+facility_id).then(function(data) {
                $scope.resdatas = data.data[0];


            });
        }
        $scope.ClinicQueue();

        $scope.vct_registration=function (vct,patient) {

            if(vct==undefined){
                swal(
                    'Error',
                    'Fill All Fields In This Section',
                    'error'
                )
            }
            else if(vct.attendance_type==undefined){
                swal(
                    'Error',
                    'Fill Client Attendance Type Column',
                    'error'
                )
            } else if(vct.client_from==undefined){
                swal(
                    'Error',
                    'Fill Place where Client is From',
                    'error'
                )
            }
            else{

                //console.log(patient.account_id)
var visit_id;
                if(patient.account_id == undefined){

                    visit_id=patient.visit_id;
                }
                else if(patient.visit_id ==undefined){
                        visit_id=patient.account_id;

                }

                var vcts={'user_id':user_id,'facility_id':facility_id,
                    'attendance_type':vct.attendance_type,
                    'serial_no':11,
                    'client_from':vct.client_from, 'client_from_other':vct.client_from_other,
                    'pregnancy_record':vct.pregnancy_record, 'referral_to':vct.referral_to,
                    'referral_to_other':vct.referral_to_other, 'counselling_type':vct.counselling_type,
                    'agreed_vvu_test':vct.agreed_vvu_test, 'counselling_after_test':vct.counselling_after_test,
                    'vvu_test_result':vct.vvu_test_result, 'participatory_test_result':vct.participatory_test_result,
                    'tb_test':vct.tb_test, 'tb_test_result':vct.tb_test_result,
                    'condom_given':vct.condom_given, 'comment':vct.comment,
                    'client_id':patient.patient_id,
                    'transfer_id':patient.transfer_id,
                    'visit_id':visit_id,
                };
                $http.post('/api/vct_registration',vcts ).then(function(data) {

                    var sending = data.data;
                    var msg = data.data.msg;
                    if (data.data.status == 0) {
                        swal(
                            'Error',
                            msg,
                            'error'
                        )
                    }
                    else {

                        swal(
                            'Success',
                            msg,
                            'success'
                        )

                    }
                });
            }
        }

        $scope.closeModal=function (){

            $uibModalInstance.dismiss();

        }

        $scope.getConsultationModal = function (item) {
            var object = item;
            var modalInstance = $uibModal.open({
                templateUrl: '/views/modules/clinic/VCT/Vct_care.html',
                size: 'lg',
                animation: true,
                controller: 'Vct_data',
                windowClass: 'app-modal-window',
                resolve:{
                    object: function () {
                        return object;
                    }
                }
            });
        }
        var patientAge;


        $scope.Vct_diagnosis=function (selectedPatient) {


            $scope.ClinicQueue();
            $http.get('/api/patientAge/'+selectedPatient.dob ).then(function(data) {
                patientAge=data.data;

                var object ={
                    patientAge:patientAge.age,

                    patientAgeDay:patientAge.day,
                    year:patientAge.year,
                    month:patientAge.month,
                    day:patientAge.day,
                    selectedPatient:selectedPatient,
                };


                $mdDialog.show({
                    controller:function ($scope) {

                        $scope.tbs = object.selectedPatient;
                        $http.get('/api/department_list').then(function(data) {
                            $scope.departments=data.data;
                            //console.log( $scope.deepartments )

                        });

                        $scope.patientAge = object.patientAge;
                        $scope.year = object.year;
                        $scope.month = object.month;
                        $scope.day = object.day;

                        $scope.cancel = function() {
                            $mdDialog.hide();

                        };

                    },
                    templateUrl: '/views/modules/clinic/VCT/Vct_care.html',
                    parent: angular.element(document.body),
                    clickOutsideToClose: true,
                    fullscreen: false,
                });



            });


        }


        $scope.getIncomingTransferConsultationModal = function (item) {
            var object = item;

            $http.post('/api/update_referral_Incomming',item).then(function(data) {
                var object ={

                    selectedPatient:item,
                };

                if(data.data){

                    $mdDialog.show({
                        controller:function ($scope) {

                            $scope.tbs = object.selectedPatient;
                            $http.get('/api/department_list').then(function(data) {
                                $scope.departments=data.data;
                                //console.log( $scope.deepartments )

                            });

                            $scope.patientAge = object.patientAge;
                            $scope.year = object.year;
                            $scope.month = object.month;
                            $scope.day = object.day;

                            $scope.cancel = function() {
                                $mdDialog.hide();

                            };

                        },
                        templateUrl: '/views/modules/clinic/VCT/Vct_care.html',
                        parent: angular.element(document.body),
                        clickOutsideToClose: true,
                        fullscreen: false,
                    });


                }
            });


        }




        $scope.Vct_report=function () {

            var object={name:user_id}
            var modalInstance = $uibModal.open({
                templateUrl: '/views/modules/clinic/VCT/Vct_report.html',
                size: 'lg',
                animation: true,
                controller: 'Vct_data',
                windowClass: 'app-modal-window',
                resolve:{
                    object: function () {
                        return object;
                    }
                }
            });



        }










    }

})();
/**
 * Created by USER on 2017-03-08.
 */