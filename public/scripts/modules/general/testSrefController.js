(function () {
    'use strict';
    var app = angular.module('authApp');
    app.controller('testSrefController', testSrefController);
    testSrefController.$inject =
        ['$scope', '$mdDialog', '$state', '$rootScope', 'toastr','Laboratory','$stateParams','Sample','$mdEditDialog','$q','$timeout','Results','Verify'];

    function testSrefController
    ($scope, $mdDialog, $state, $rootScope, toastr,Laboratory,$stateParams,Sample,$mdEditDialog,$q,$timeout,Results,Verify) {
        $scope.selected = [];
        $scope.limitOptions = [5, 10, 15];
        $scope.options = {
            rowSelection: true,
            multiSelect: true,
            autoSelect: true,
            decapitate: false,
            largeEditDialog: false,
            boundaryLinks: false,
            limitSelect: true,
            pageSelect: true
        };

        $scope.query = {
            order: 'name',
            limit: 5,
            page: 1
        };

        $scope.editComment = function (event, dessert) {
            console.log(dessert);
            event.stopPropagation(); // in case autoselect is enabled
            var editDialog = {
                modelValue: dessert.comment,
                placeholder: 'Add a result',
                save: function (input) {
                    console.log(input.$modelValue);
                    console.log(dessert);
                    var labResults = {
                        'description': input.$modelValue,
                        'item_id': dessert.item_id,
                        'order_id': dessert.order_id,
                        'post_user': user,
                        'sample': dessert.sample_no,

                    };
                    console.log(labResults);
                    Results.save(labResults, function (response) {
                        if (response.status === 201) {
                            console.log('successfully created', response);
                            $scope.patientsSamples();
                            $scope.loadInvestigationVerify();
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
                    dessert.comment = input.$modelValue;

                },
                targetEvent: event,
                title: 'Add Results',
                validators: {
                    'md-maxlength': 100
                }
            };

            // console.log(labResults);
            //  if (typeof dessert.comment != 'undefined') {
            //     console.log(labResults);
            // }


            var promise;

            if($scope.options.largeEditDialog) {
                promise = $mdEditDialog.large(editDialog);
            } else {
                promise = $mdEditDialog.small(editDialog);
            }

            promise.then(function (ctrl) {
                var input = ctrl.getInput();

                input.$viewChangeListeners.push(function () {
                    input.$setValidity('test', input.$modelValue !== 'test');
                });
            });
        };

        $scope.toggleLimitOptions = function () {
            $scope.limitOptions = $scope.limitOptions ? undefined : [5, 10, 15];
        };

        $scope.loadStuff = function () {
            $scope.promise = $timeout(function () {
                $scope.patientsSamples();
                $scope.loadInvestigationVerify();
            }, 2000);
        }

        $scope.logOrder = function (order) {
            console.log('order: ', order);
        };

        $scope.logPagination = function (page, limit) {
            console.log('page: ', page);
            console.log('limit: ', limit);
        }


        console.log($stateParams);
        var user = $rootScope.currentUser.id;
        var facility_id = $rootScope.currentUser.facility_id;
        var patientsList=[];

        angular.element(document).ready(function () {
            $scope.patientsSamples();
            $scope.loadInvestigationVerify();
        })
        $scope.mrn = $stateParams.b;
        //SHOW TEST PER RESPECTIVE PATIENT
        $scope.patientsSamples = function () {
            var patient_id = $stateParams.a;
            console.log(patient_id);
            return Laboratory.test_per_patient(patient_id)
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
                    $scope.patientPaticulars= response.data.data.data;

                    $scope.patientsSample = response.data.data;
                    console.log(response.data.data)
                });
        };
        //LOAD INVESTIGATIONS TO BE VERIFIED
        $scope.loadInvestigationVerify = function () {
            $scope.loadedTesting=false;
            Laboratory.investigation_verify()
                .then(function (response) {
                    $scope.loadedTesting=true;
                    console.log('investigation_to_be_verified', response);
                    $scope.investigation_verified_sample = response.data[0];
                    $scope.investigation_panel_verified = response.data[1];
                    console.log($scope.investigation_verified_sample);
                    console.log($scope.investigation_panel_verified);

                });
        };
    //APPROVE LAB RESULTS
        $scope.aprovedLabResults = function (verify) {
            console.log(verify);
            var verifyData = {
                'order_id': verify.order_id,
                'item_id': verify.item_id,
                'confirmation_status': 1,
                'verify_user': user,
            };
            console.log(verifyData);
            console.log('verify_object', verifyData);
            Verify.save(verifyData, function (response) {
                $scope.loadInvestigationVerify();
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

    }
})();