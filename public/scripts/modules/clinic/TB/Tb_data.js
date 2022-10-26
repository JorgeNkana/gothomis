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
    app.controller('Tb_data',['$scope','$http','$rootScope',
        function ($scope,$http,$rootScope) {
            $scope.tbs =  $scope.item.selectedPatient;
            $scope.patientAge =  $scope.item.patientAge;
            $scope.year =  $scope.item.year;
            $scope.month = $scope.item.month;
            $scope.day =  $scope.item.day;

            var user_id = $rootScope.currentUser.id;
            var facility_id=$rootScope.currentUser.facility_id;

            $http.get('/api/getLoginUserDetails/'+user_id ).then(function(data) {
                $scope.menudetalis=data.data;
            });
            //$('#clientAge').val(user_id);



        }]);
})();