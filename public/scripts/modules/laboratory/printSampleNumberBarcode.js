(function () {

    'use strict';

    var app = angular.module('authApp');

    app.controller('printSampleNumberBarcode',

                ['$scope','$http','$rootScope','$uibModal', '$uibModalInstance', 'object',
        function ($scope,$http,$rootScope,$uibModal,$uibModalInstance,object) {
            var facility_id =$rootScope.currentUser.facility_id;
            var user_id =$rootScope.currentUser.id;

                   $scope.samples_numbers=object;


            $scope.getCancelledSampleReason= function(){
                $http.get('/api/getSampleStatus').then(function(data) {
                    $scope.sampleStatuses=data.data;

                });
            }

  $scope.saveComponent= function(getPanelComponet){
                $http.post('/api/saveComponentsResults').then(function(data) {
                    $scope.sampleStatuses=data.data;

                });
            }

            //console.log($scope.samples_numbers);




            $scope.getCancelledSampleReason();

//function to notify user if sample cancelled
            $scope.playAudio = function() {
                var audio = new Audio('/notification/thrown.mp3');
                audio.play();
            };


            $scope.sampleReject=function(samples_numbers,sample) {

                var sample_no=samples_numbers.sample_no;
                var last_name=samples_numbers.last_name;
                var sub_department_name=samples_numbers.sub_department_name;
                var request_id=samples_numbers.request_id;
                var reason=sample.reason;

                if (angular.isDefined(request_id)==false) {
                    return sweetAlert("Select TEST to be done", "", "error");
                }
                else{

                    var dataPost={"reason":reason,"sample_no":sample_no,"order_control":3,"order_validator_id":user_id,"last_name":last_name,"facility_id":facility_id,"request_id":request_id};


                    $http.post('/api/sampleCancel',dataPost).then(function(data) {
                        if(data.data.status ==0){
                            sweetAlert(data.data.data, "", "error");
                        }
                        else{

                            var msg=" Sample No."+sample_no+"  Was successfully Cancelled ";
                            //
                            sweetAlert(msg, "", "success");
                        }});
                }
            }


        $scope.resultReject=function(samples_numbers,sample) {

                var sample_no=samples_numbers.sample_no;
                var last_name=samples_numbers.last_name;
                var sub_department_name=samples_numbers.sub_department_name;
                var confirmation_status=samples_numbers.confirmation_status;
                var request_id=samples_numbers.request_id;


                if (angular.isDefined(sample)==false) {
                    return sweetAlert("Write Reason for rejecting Results for Sampl# "+sample_no, "", "error");
                }
                else{
                    var reason=sample.reason;
                    $scope.playAudio();
                    var dataPost={"cancel_reason":reason,"sample_no":sample_no,"confirmation_status":0,"order_control":3,"order_validator_id":user_id,"last_name":last_name,"facility_id":facility_id,"request_id":request_id};
                    $http.post('/api/resultsCancel',dataPost).then(function(data) {
                        if(data.data.status ==0){
                            sweetAlert(data.data.data, "", "error");
                        }
                        else{
                            $scope.playAudio();
                            var msg="Results for Sample No."+sample_no+"  Was successfully Rejected";
                            //
                            sweetAlert(msg, "", "success");
                        }});
                }
            }







	              }]);
		
		
		
		
		
}());