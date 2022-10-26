/**
 * Created by Jeph on 2017-03-19.
 */
(function () {
    'use strict';
    var app = angular.module('authApp');
    app.controller('deviceModal',['$scope','$http','$rootScope','$uibModal', '$uibModalInstance', 'object',
        function ($scope,$http,$rootScope,$uibModal,$uibModalInstance,object) {
            $scope.deviceSetting = object;

            var user_id = $rootScope.currentUser.id;
            var facility_id=$rootScope.currentUser.facility_id;
            $http.get('/api/getEquipmentStatus').then(function(data) {
                $scope.equipments=data.data;

            });



        }]);
})();