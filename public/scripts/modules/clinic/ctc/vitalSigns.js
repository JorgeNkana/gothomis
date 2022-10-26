/**
 * Created by Jeph on 2017-07-19.
 */
(function () {
    'use strict';
    var app = angular.module('authApp');
    app.controller('vitalSigns',['$scope','$http','$rootScope','$uibModal', '$uibModalInstance', 'object',
        function ($scope,$http,$rootScope,$uibModal,$uibModalInstance,object) {
            $scope.vitalQue = object.vitalQue;
            $scope.Vitals = object.Vitals;
            var user_id = $rootScope.currentUser.id;
            var facility_id=$rootScope.currentUser.facility_id;





        }]);
})();