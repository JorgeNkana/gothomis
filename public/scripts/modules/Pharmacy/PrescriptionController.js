/**
 * Created by USER on 2017-04-16.
 */
/**
 * Created by Jeph on 2017-03-19.
 */
(function () {
    'use strict';
    var app = angular.module('authApp');
    app.controller('PrescriptionController',['$scope','$http','$rootScope','$uibModal', '$uibModalInstance', 'object',
        function ($scope,$http,$rootScope,$uibModal,$uibModalInstance,object) {
            $scope.prescriptions = object;
            console.log($scope.prescriptions);
 
            var user_id = $rootScope.currentUser.id;
            var facility_id=$rootScope.currentUser.facility_id;

            $http.get('/api/getUsermenu/'+user_id ).then(function(data) {
                $scope.menu=data.data;
            });

            $scope.date = new Date();

        }]);
})();