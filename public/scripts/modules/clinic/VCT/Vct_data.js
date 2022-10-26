/**
 * Created by USER on 2017-05-22.
 */
/**
 * Created by USER on 2017-05-20.
 */
/**
 * Created by USER on 2017-04-16.
 */
/**
 * Created by Jeph on 2017-03-19.
 */
(function () {
    'use strict';
    var app = angular.module('authApp');
    app.controller('Vct_data',['$scope','$http','$rootScope','$uibModal', '$uibModalInstance', 'object',
        function ($scope,$http,$rootScope,$uibModal,$uibModalInstance,object) {
            $scope.tbs = object.selectedPatient;
            $scope.deepartments = object.Department;

            $scope.patientAge = object.patientAge;
            $scope.year = object.year;
            $scope.month = object.month;
            $scope.day = object.day;

            var user_id = $rootScope.currentUser.id;
            var facility_id=$rootScope.currentUser.facility_id;

            $http.get('/api/department_list').then(function(data) {
                $scope.departments=data.data;
                //console.log( $scope.deepartments )

            });


            $http.get('/api/getLoginUserDetails/'+user_id ).then(function(data) {
                $scope.menudetalis=data.data;
            });
            $scope.closeModal=function (){

                $uibModalInstance.dismiss();

            }

        }]);
})();