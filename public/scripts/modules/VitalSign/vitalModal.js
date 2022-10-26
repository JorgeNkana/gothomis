/**
 * Created by Jeph on 2017-07-19.
 */
(function () {
    'use strict';
    var app = angular.module('authApp');
    app.controller('vitalModal',['$scope','$http','$rootScope','$uibModal', '$uibModalInstance', 'object',
        function ($scope,$http,$rootScope,$uibModal,$uibModalInstance,object) {
           //console.log(object);
            $scope.vitalQue = object;
            var user_id = $rootScope.currentUser.id;
            var facility_id=$rootScope.currentUser.facility_id;

            $http.get('/api/getVitals').then(function(data) {
                $scope.Vitals=data.data;

            });




        }]);
})();