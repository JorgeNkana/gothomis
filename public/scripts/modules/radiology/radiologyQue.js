(function () {
    'use strict';
    var app =  angular.module('authApp');
    app.controller('radiologyQue',['$scope','$http','$state','$rootScope','$uibModal','$uibModalInstance','object',
        function ($scope,$http,$state,$rootScope,$uibModal,$uibModalInstance,object) {
            $scope.selectedPatient = object;
            //console.log($scope.selectedPatiet);
            var user_id = $rootScope.currentUser.id;
            var facility_id = $rootScope.currentUser.facility_id;
            var patient_id = object.patient_id;
            $scope.setTab = function(newTab){
                $scope.tab = newTab;
            };
            $scope.isSet = function(tabNum){
                return $scope.tab === tabNum;
            }

        }
    ]);
})();