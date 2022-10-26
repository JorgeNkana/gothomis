(function () {

    'use strict';

    var app = angular.module('authApp');

    app.controller('LabTestRequestPatient',

                ['$scope','$http','$rootScope','$uibModal', '$uibModalInstance', 'object',
        function ($scope,$http,$rootScope,$uibModal,$uibModalInstance,object) {
            var facility_id =$rootScope.currentUser.facility_id;
            var user_id =$rootScope.currentUser.id;
                $scope.singleTests=object.singleTests;
                $scope.PanelsTests=object.PanelsTests;
                $scope.patientPaticulars=object.getTestRequest;
                $scope.getTestRequests=object.getTestRequest;
                $scope.getTestRequest=object.getTestRequest;

                //console.log($scope.getTestRequests);

            $scope.cancel=function (){
                $uibModalInstance.dismiss();

            }

            $scope.getTestComponents=function(panel,order_id) {
                var postData={"order_id":order_id,"panel_name":panel};
                $http.post('/api/getPanelComponets',postData).then(function(data) {
                    $scope.getPanelComponets = data.data;
                    var object ={"getPanelComponets":$scope.getPanelComponets};
                    var modalInstance = $uibModal.open({
                        templateUrl: '/views/modules/laboratory/enterComponentsResults.html',
                        size: 'lg',
                        animation: true,
                        controller: 'LabPanelComponetsController',
                        resolve:{
                            object: function () {
                                return object;
                            }
                        }
                    });

                });
            }

            $scope.getTestComponentsResults=function(sample_no,item_id,panel,order_id) {
                var postData={"sample_no":sample_no,"item_id":item_id,"order_id":order_id,"panel_name":panel};
                //console.log(postData);
                $http.post('/api/getPanelComponetsResults',postData).then(function(data) {
                    $scope.getPanelComponets = data.data;
                    var object ={"getPanelComponets":$scope.getPanelComponets};
                    var modalInstance = $uibModal.open({
                        templateUrl: '/views/modules/laboratory/verifyComponentsResults.html',
                        size: 'lg',
                        animation: true,
                        controller: 'LabPanelComponetsController',
                        resolve:{
                            object: function () {
                                return object;
                            }
                        }
                    });

                });
            }






                var admission_id='';
            //patientWardBed/{admission_id}
            var admission_id=$scope.patientPaticulars.admission_id;
            $http.get('/api/patientWardBed/'+admission_id).then(function(data) {
                $scope.getAdmisionInfos = data.data[0];

                //console.log($scope.getAdmisionInfos);
            });

            $scope.saveSampleNumber=function(sub_department_name,request_id) {

                if (angular.isDefined(request_id)==false) {
                    return sweetAlert("Select TEST to be done", "", "error");
                }
                else{
                    return sweetAlert("TEST request "+request_id, "", "success");
                    var dataPost={"equipment_status_id":device.equip_status,"id":equipment.id};
                    $http.post('/api/saveNewDeviceStatus',dataPost).then(function(data) {
                        if(data.data.status ==0){
                            sweetAlert(data.data.data, "", "error");
                        }
                        else{
                            $http.get('/api/getEquipementList').then(function(data) {
                                $scope.equipementLists=data.data;

                            });
                        	var msg="Working status for "+equipment.equipment_name+" was successfully Changed";
                            $uibModalInstance.dismiss();
                            sweetAlert(msg, "", "success");
                        }});
                }
            }

            $scope.generateSampleNumber=function(sample_type,last_name,sub_department_name,request_id) {

                    var dataPost={"sample_type":sample_type,"order_control":null,"order_validator_id":user_id,"last_name":last_name,"facility_id":facility_id,"request_id":request_id};

                    $http.post('/api/generateSampleNumber',dataPost).then(function(data) {
                        if(data.data.status ==0){
                            sweetAlert(data.data.data, "", "error");
                        }
                        else{

                            $scope.sampleResposes=data.data;

                            var object_resp =  $scope.sampleResposes;

                            var htmlcontent ='<div id="printable"><div class="row"><div class="form-group col-md-9">'+sub_department_name+''+object_resp.barcode +''+object_resp.last_name +'-'+object_resp.sample_number+' </div></div></div>';


                     //  var barcode='<img src="data:image/png;base64,{{DNS1D::getBarcodePNG('11', 'C39')}}" alt="barcode" />';

                            var object ={"last_name":object_resp.last_name,"sub_department_name":sub_department_name,"sample_number":object_resp.sample_number,"image_code":object_resp.barcode};
                            var modalInstance = $uibModal.open({
                                templateUrl: '/views/modules/laboratory/barcode_print_out.html',
                                size: 'lg',
                                animation: true,
                                controller: 'barcodeModal',
                                windowClass: 'app-modal-window',
                                resolve:{
                                    object: function () {
                                        return object;
                                    }
                                }
                            });

                            /**  swal({
                                title:'',
                                text:'',
                                customClass: 'swal-wide',
                                html: htmlcontent
                            }); **/



                        }});
                }
//function to notify user if sample cancelled
            $scope.playAudio = function() {
                var audio = new Audio('/notification/thrown.mp3');
                audio.play();
            };








            $scope.reGenerateSampleNumber=function(last_name,sub_department_name,request_id) {

                if (angular.isDefined(request_id)==false) {
                    return sweetAlert("Select TEST to be done", "", "error");
                }
                else{
                    $scope.playAudio();
                    var dataPost={"order_control":1,"order_validator_id":user_id,"last_name":last_name,"facility_id":facility_id,"request_id":request_id};


                    $http.post('/api/generateSampleNumber',dataPost).then(function(data) {
                        if(data.data.status ==0){
                            sweetAlert(data.data.data, "", "error");
                        }
                        else{
                        	//var msg=sub_department_name+"<br>"+data.data.barcode+""+data.data.last_name+"-"+data.data.sample_number;
                           // $uibModalInstance.dismiss();
                            //sweetAlert(msg, "", "success");
                            $scope.samples=data.data;

                           var object =  $scope.samples;

                            var htmlcontent ='<div class="row"><div class="form-group col-md-9">'+sub_department_name+''+object.barcode +''+object.last_name +'-'+object.sample_number+' </div></div>';


                            swal({
                                title:'',
                                text:'',
                                customClass: 'swal-wide',
                                html: htmlcontent
                            });

                            /**
                            var modalInstance = $uibModal.open({
                                templateUrl: '/views/modules/laboratory/printBarcodeSampleNumber.html',
                                size: 'lg',
                                animation: true,
                                controller: 'printSampleNumberBarcode',
                                resolve:{
                                    object: function () {
                                        return object;
                                    }
                                }
                            });
**/


                        }});
                }
            }



            $scope.closeModal=function() {
                $uibModalInstance.dismiss();
            }





            $scope.confirmReject=function(getTestRequest) {

                var object=getTestRequest;

                var modalInstance = $uibModal.open({
                    templateUrl: '/views/modules/laboratory/printBarcodeSampleNumber.html',
                    size: 'lg',
                    animation: true,
                    controller: 'printSampleNumberBarcode',
                    resolve:{
                        object: function () {
                            return object;
                        }
                    }
                });


            }


 $scope.confirmRejectResults=function(getTestRequest) {

                var object=getTestRequest;

                var modalInstance = $uibModal.open({
                    templateUrl: '/views/modules/laboratory/confirmRejectResults.html',
                    size: 'lg',
                    animation: true,
                    controller: 'printSampleNumberBarcode',
                    resolve:{
                        object: function () {
                            return object;
                        }
                    }
                });


            }







		              }]);
		
		
		
		
		
}());