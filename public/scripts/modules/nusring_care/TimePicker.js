(function () {

    'use strict';

    var app = angular.module('authApp');

    app.controller('TimePicker',

                ['$scope','$http','$rootScope','$uibModal', '$uibModalInstance', 'time',
        function ($scope,$http,$rootScope,$uibModal,$uibModalInstance,time) {

            $scope.mytime = new Date();

            $scope.hstep = 1;
            $scope.mstep = 15;
            $scope.mytime = '';

            $scope.options = {
                hstep: [1, 2, 3],
                mstep: [1, 5, 10, 15, 25, 30]
            };
            $scope.ismeridian = true;
            $scope.mytime = time;
            $scope.selected = {
                mytime: $scope.mytime
            };
            $scope.toggleMode = function() {
                $scope.ismeridian = ! $scope.ismeridian;
            };

            $scope.update = function() {
                var d = new Date();
                d.setHours( 14 );
                d.setMinutes( 0 );
                $scope.mytime = d;
            };

            $scope.changed = function () {
                console.log('Time changed to: ' + $scope.mytime);
            };

            $scope.clear = function() {
                $scope.mytime = null;
            };
            $scope.ok = function () {
                $uibModalInstance.close($scope.mytime);
            };

            $scope.cancel = function () {
                $uibModalInstance.dismiss('cancel');
            };


        }]);
		
		
		
		
		
}());