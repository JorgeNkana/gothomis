/**
 * Created by japhari on 07/03/2017.
 */

(function() {
    'use strict';
    angular
        .module('authApp')
        .controller('icd10Controller', icd10Controller);
    function icd10Controller($http, $auth, $rootScope,$state,$timeout,$interval,$location,$scope,$uibModal,Helper) {

        $scope.isNavCollapsed = false;
        $scope.isCollapsed = true;
        $scope.isCollapsedHorizontal = true;
        var user=$rootScope.currentUser;
        var user_name=$rootScope.currentUser.id;
        var facility_id=$rootScope.currentUser.facility_id;
        var patientCategory =[];
        var patientService =[];
        var resdata =[];

        $http.get('/api/icd10DiagnosisList').then(function (data) {
            $scope.icd10 = data.data;
            console.log(data.data);


        });

        $scope.icdSearch = function (text) {
            return Helper.searchDiagnosis(text)
                .then(function (response) {
                    return response.data;
                });
        };

    }

})();