(function () {
    'use strict';
    var app = angular.module('authApp');
    app.controller('drPerformanceController', drPerformanceController);
    drPerformanceController.$inject =
        ['$scope', '$mdDialog', '$state', '$rootScope', 'toastr','Laboratory'];

    function drPerformanceController
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

        $scope.searchPerformance = function (search) {
            $scope.loadedPerformances=false;
            var seachPerformance = {
                "facility_id": facility_id,
                "start_date": search.start,
                "end_date": search.end
            };
            return Laboratory.doct_performances(seachPerformance)
                .then(function (response) {
                    $scope.loadedPerformances=true;
                    console.log('all_performances',response);
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
                    $scope.doctorPerformances = response.data.data;
                    console.log($scope.doctorPerformances)
                });
        };


    }
})();