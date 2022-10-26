(function () {
    'use strict';
    var app = angular.module('authApp');
    app.controller('sampleSrefController', sampleSrefController);
    sampleSrefController.$inject =
        ['$scope', '$mdDialog', '$state', '$rootScope', 'toastr','Laboratory','$stateParams','Sample'];

    function sampleSrefController
    ($scope, $mdDialog, $state, $rootScope, toastr,Laboratory,$stateParams,Sample) {
        console.log($stateParams);
        $scope.limitOptions = [5, 10, 15, 20, 50, 100, 200, 500];
        $scope.selected = [];
        var user = $rootScope.currentUser.id;
        var facility_id = $rootScope.currentUser.facility_id;
        var patientsList=[];
        $scope.options = {
            rowSelection: false,
            multiSelect: true,
            autoSelect: true,
            decapitate: false,
            largeEditDialog: false,
            boundaryLinks: false,
            limitSelect: true,
            pageSelect: true
        };
        angular.element(document).ready(function () {
            $scope.patientsSamples();
        })
        $scope.mrn = $stateParams.b;
        $scope.patientsSamples = function () {
            var orderid = $stateParams.a;
            console.log(orderid);
            return Laboratory.sample(orderid)
                .then(function (response) {
                    console.log('all_orders',response);
                    $scope.loadedSample = true;
                    var ApiResponseStatus = response.status;
                    var ApiResponseData = response.data.length;
                    console.log(ApiResponseData);
                    console.log(ApiResponseStatus);
                    if (ApiResponseStatus !== 200) {
                        toastr.error("Something went wrong you may not get data");
                    }
                    else if (ApiResponseData == 0) {
                        toastr.error("No data set");
                    }
                    $scope.patientsSample = response.data.data;
                    console.log(response.data)
                });
        };
        $scope.createSample = function (sample,mrn) {
            console.log(sample);
            console.log(mrn);
            var sampleData = {
                'test_name': sample.item_name,
                'last_name': mrn,
                'order_control': null,
                'sample_type': sample.sample_type,
                'order_validator_id': user,
                'facility_id': facility_id,
                'request_id': sample.request_id,
                'sub_department': sample.sub_department_id,
            };
           console.log(sampleData);
            console.log('the sample object', sampleData);
            Sample.save(sampleData, function (response) {
                console.log(response);
                $scope.patientsSamples();
                $scope.sampleResposes=response;
                var object_resp =  $scope.sampleResposes;
                var htmlcontent ='<div id="printable"><div class="row"><div class="form-group col-md-9">'+mrn+''+object_resp.barcode +''+object_resp.last_name +'-'+object_resp.sample_number+' </div></div></div>';
                $mdDialog.show({
                    controller: function ($scope) {
                        $scope.last_name =object_resp.last_name;
                        $scope.sub_department_name =object_resp.sub_department_name;
                        $scope.test_name =object_resp.test_name;
                        $scope.sample_number =object_resp.sample_number;
                        $scope.image_code =object_resp.barcode;
                        $scope.time_generated =object_resp.time_generated;
                        $scope.cancel = function () {
                            $scope.selectedPatient=null;
                            $mdDialog.hide();
                            $scope.patientsSample();
                        };
                    },
                    templateUrl: '/views/modules/laboratory/barcode_print_out.html',
                    parent: angular.element(document.body),
                    clickOutsideToClose: false,
                    fullscreen: $scope.customFullscreen // Only for -xs, -sm breakpoints.
                });

                if (response.status === 201) {
                    console.log('successfully created', response);
                    var message = response.message;
                    toastr.success(message);
                    $scope.status = response.status;
                    $scope.message = response.message;
                }

            }, function (response) {
                console.log('there was an error', response.data.errors);
                var apiResponseStatus = response.status;
                $scope.errors = response.data.errors;
                var errors = response.data.errors;
                $scope.message = response.data.message;
                $scope.status = response.data.status;
                console.log(apiResponseStatus);
                if (apiResponseStatus == 400){
                    toastr.error(errors);
                }

            });
        };
        $scope.generateSampleNumber=function(test_name,sample_type,last_name,sub_department_name,request_id) {
            var dataPost={
                test_name:test_name,
                "sample_type":sample_type,
                "order_control":null,
                "order_validator_id":user_id,
                "last_name":last_name,
                "facility_id":facility_id,
                "request_id":request_id,
                sub_department_name:sub_department_name
            };

            $http.post('/api/generateSampleNumber',dataPost).then(function(data) {
                if(data.data.status ==0){
                    sweetAlert(data.data.data, "", "error");
                }
                else{

                    $scope.sampleResposes=data.data;

                    var object_resp =  $scope.sampleResposes;

                    var htmlcontent ='<div id="printable"><div class="row"><div class="form-group col-md-9">'+sub_department_name+''+object_resp.barcode +''+object_resp.last_name +'-'+object_resp.sample_number+' </div></div></div>';


                    //  var barcode='<img src="data:image/png;base64,{{DNS1D::getBarcodePNG('11', 'C39')}}" alt="barcode" />';

                    var object ={item_name:object_resp.test_name,"time_generated":object_resp.time_generated,"last_name":object_resp.last_name,"sub_department_name":object_resp.sub_department_name,"sample_number":object_resp.sample_number,"image_code":object_resp.barcode};


                    $mdDialog.show({

                        controller: function ($scope) {
                            $scope.last_name =object_resp.last_name;
                            $scope.sub_department_name =object_resp.sub_department_name;
                            $scope.test_name =object_resp.test_name;
                            $scope.sample_number =object_resp.sample_number;
                            $scope.image_code =object_resp.barcode;
                            $scope.time_generated =object_resp.time_generated;
                            $scope.cancel = function () {
                                $scope.selectedPatient=null;
                                $mdDialog.hide();
                                $scope.getLabTestRequests();
                            };
                        },
                        templateUrl: '/views/modules/laboratory/barcode_print_out.html',
                        parent: angular.element(document.body),
                        clickOutsideToClose: false,
                        fullscreen: $scope.customFullscreen // Only for -xs, -sm breakpoints.
                    });




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
                    $scope.getLabTestRequests();

                }});
        };
       $scope.loadedInvestigations = $stateParams.a;
        console.log($scope.loadedInvestigations);
        $scope.showRegionUpdateDialog = function (id) {
            console.log(id);
            Region.get({id: id}, function (response) {
                $scope.region = response.data;
                console.log(response.data);
                $scope.cancel = function () {
                    $mdDialog.hide();
                };
                $mdDialog.show({
                    controller: sampleSrefController,
                    scope: $scope,
                    preserveScope: true,
                    templateUrl: 'scripts/modules/registrations/views/dialog/edit-region.html',
                    clickOutsideToClose: true,
                    fullscreen: true // Only for -xs, -sm breakpoints.
                });
            });
        };

    }
})();