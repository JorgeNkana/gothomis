(function () {

    'use strict';

    var app = angular.module('authApp');

    app.controller('ctcPatientSupport',
        ['$scope','$http','$rootScope','$uibModal', '$uibModalInstance','toastr', 'object',
            function ($scope,$http,$rootScope,$uibModal,$uibModalInstance,toastr,object) {

                var facility_id =$rootScope.currentUser.facility_id;
                var user_id =$rootScope.currentUser.id;
                $scope.ctcQues=object.ctcQues;
                $scope.patient=object.patient;
                       $scope.cancel=function (){
                    //console.log('done and cleared');
                    $uibModalInstance.dismiss();

                }

                var resdata=[];
                $scope.showSearchResidences = function(searchKey) {

                    $http.get('/api/searchResidences/'+searchKey).then(function(data) {
                        resdata = data.data;
                    });
                    ////console.log(resdata);
                    return resdata;
                }

                $scope.closeAllModals=function (){
                   // //console.log('done and cleared');
                    $uibModalInstance.dismissAll();

                }

                    $scope.saveCTCPatientSupport=function (patient_support,ctcQues){
                        var patient_id=ctcQues.patient_id;
                        var last_name=ctcQues.last_name;
                        var visit_id =ctcQues.visit_id;
                        var  joined_organisation = 0;
                        if (angular.isDefined(patient_support)==false) {
                            return toastr.error('','ENTER NAME OF TREATMENT SUPPORTER  FOR '+last_name);
                        }
                        var treatment_supporter=patient_support.treatment_supporter;
                        var phone_supporter=patient_support.phone_supporter;
                        var patient_visit=patient_support.patient_visit;
                        var yes=patient_support.yes;
                        var no=patient_support.no;
                        if(angular.isDefined(no)==false){
                           var  joined_organisation =  1;
                        }
                        var organisation=patient_support.organisation;
                        var postData={"visit_date_id":visit_id,"on_off":1,"last_name":last_name,"patient_id":patient_id,"name_treatment_supporter":treatment_supporter,"telephone_number":phone_supporter,"visit_type_code":patient_visit,"joined_organisation":joined_organisation,"name_organisation":organisation,"facility_id":facility_id,"user_id":user_id};
                        //console.log(postData);
                        $http.post('/api/saveCTCPatientSupport',postData).then(function(data) {
                            if(data.data.status ==0){
                                toastr.error('',data.data.data);
                            }else{
                                toastr.success('',data.data.data);
                                $scope.cancel();


                            }


                        });

                }

            }]);





}());