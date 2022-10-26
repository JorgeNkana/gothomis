/**
/**
 * Created by Mazigo Jr on 2017-04-18.
 */
(function () {
    'use strict';
    angular.module('authApp').controller('insuranceController',insuranceController);

    function insuranceController($scope,$rootScope,$http,$uibModal) {
        var user_id = $rootScope.currentUser.id;
        var facility_id = $rootScope.currentUser.facility_id;
        $scope.setTab = function(newTab){
            $scope.tab = newTab;
        };
        $scope.isSet = function(tabNum){
            return $scope.tab === tabNum;
        }
        $http.get('/api/getUsermenu/'+user_id ).then(function(data) {
            $scope.menu=data.data;
            //////console.log($scope.menu);

        });
        $scope.getDates = function (item) {
            $http.post('/api/getNhifDates',{"start":item.start,"end":item.end,"facility_id":facility_id}).then(function (data) {
                $scope.insurance_attendence = data.data;
            });
        }
        $scope.getPatients = function (item) {
           $http.post('/api/getInsurancePatients',{"date_attended":item.date_attended,"facility_id":facility_id}).then(function (data) {
              $scope.patients = data.data;
			  console.log($scope.patients);
           });
        }
        $scope.getClaimForm = function (item) {
            var object = item;
            //console.log(item)
            var modalInstance = $uibModal.open({
                templateUrl: '/views/modules/insurance/claimForm.html',
                size: 'lg',
                animation: true,
                controller: 'claimsModal',
                windowClass: 'app-modal-window',
                resolve:{
                    object: function () {
                        return object;
                    }
                }
            });

        }
    }
})();