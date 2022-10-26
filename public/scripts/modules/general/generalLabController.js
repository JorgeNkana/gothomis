(function () {
    'use strict';
    var app = angular.module('authApp');
    app.controller('generalLabController', generalLabController);
    generalLabController.$inject =
        ['$scope', '$mdDialog', '$state', '$rootScope', 'toastr','Laboratory'];

    function generalLabController
    ($scope, $mdDialog, $state, $rootScope, toastr,Laboratory) {

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
            $scope.getLabTestRequests();
        });
        //LOAD PATIENTS FOR SAMPLE COLLECTION
        $scope.getLabTestRequests = function () {
           // console.log(facility_id);
            return Laboratory.investigations(facility_id)
                .then(function (response) {
                    //console.log('all_tests',response);
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
                    $scope.laboratoryData = response.data.data;
                    console.log(response.data)
                });
        };
        //SEARCH PATIENTS IN SAMPLE COLLECTION
        $scope.searchLabPatients = function (text) {
            return Laboratory.getLabPatients(text)
                .then(function (response) {
                    console.log(response.data);
                    return   response.data;
                });
        };
        //SEARCH PATIENTS IN SAMPLE TESTING
        $scope.searchInvestigationPatients = function (text) {
            return Laboratory.getInvestigationPatients(text)
                .then(function (response) {
                    console.log(response.data);
                    return   response.data;
                });
        };
        //LOAD PATIENTS IN SAMPLE TESTING
        $scope.loadTesting = function () {
            $scope.loadedTesting=false;
            Laboratory.test_list()
                .then(function (response) {
                    $scope.loadedTesting=true;
                    console.log('investigation_testing_sample', response);
                    $scope.investigation_results = response.data.data;
                });
        };
        $scope.createRegion = function (region) {
            console.log('the region object', region);
            Region.save(region, function (response) {
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
                var errors = response.data.errors;
                $scope.message = response.data.message;
                $scope.status = response.data.status;
                if (apiResponseStatus == 400){
                    toastr.error(errors);
                }
            });
        };
        $scope.statusLists = function () {
            Status.get($scope.query, function (response) {
                console.log('all_status', response);
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
                $scope.status_lists = response.data;
                console.log(response.data)
            });
        };
        $scope.regionsLists = function () {
            $scope.loadedRegions = false;
            Region.get($scope.query, function (response) {
                console.log('all_region', response);
                var ApiResponseStatus = response.status;
                var ApiResponseData = response.data.data.length;
                $scope.loadedRegions = true;
                console.log(ApiResponseData);
                console.log(ApiResponseStatus);
                if (ApiResponseStatus !== 200) {
                    toastr.error("Something went wrong you may not get data");
                }

                else if (ApiResponseData == 0) {
                    toastr.error("No data set");
                }
                $scope.region_lists = response.data;
                console.log(response.data)
            });
        };
        $scope.showRegionUpdateDialog = function (id) {
            console.log(id);
            Region.get({id: id}, function (response) {
                $scope.region = response.data;
                console.log(response.data);
                $scope.cancel = function () {
                    $mdDialog.hide();
                };
                $mdDialog.show({
                    controller: generalLabController,
                    scope: $scope,
                    preserveScope: true,
                    templateUrl: 'scripts/modules/registrations/views/dialog/edit-region.html',
                    clickOutsideToClose: true,
                    fullscreen: true // Only for -xs, -sm breakpoints.
                });
            });
        };
        $scope.updateRegions = function (data) {
            console.log(data);
            Region.update(data, function (response) {
                var ApiResponseErrors = response.errors;
                var ApiResponseStatus = response.status;
                var ApiResponseMessage = response.message;
                console.log(ApiResponseErrors);
                console.log(ApiResponseStatus);
                if (response.status === 200) {
                    $scope.regionsLists();
                    $mdDialog.hide();
                    toastr.success(ApiResponseMessage);
                }
            });
        };
    }
})();