/**
 * Created by USER on 2017-02-13.
 */
(function() {

    'use strict';

    angular
        .module('authApp')
        .controller('maritalController', maritalController);

    function maritalController($http, $auth, $rootScope,$state,$location,$scope) {
				
		//Marital Status Registrations
        $scope.marital_registration=function (marital) {
		var comit=confirm('Are you sure you want to Register '+marital.marital_status);
		if(comit) {
		$http.post('/api/marital_registration',marital).then(function(data) {
		});
		 }
        }
		
		//Marital Get Data From the database
        $scope.getmarital_status=function () {
            $http.get('/api/getmarital_status').then(function(data) {
                $scope.maritalstatus=data.data;
            });
        } 
		
		//Marital Status Update
        $scope.updatemaritalstatus=function (maritalstatus) {
		var comit=confirm('Are you sure you want to Update '+maritalstatus.marital_status);
		if(comit) {
		$http.post('/api/updatemaritalstatus', maritalstatus).then(function (data) {
		})
		$scope.getmarital_status();
		}
		else{
			return false;
		}
        }
		$scope.getmarital_status();
		
		
		//Marital Status delete
        $scope.deletemaritalstatus=function (maritalstatus) {
		var comit=confirm('Are you sure you want to delete '+maritalstatus.marital_status);
		if(comit){
		$http.get('/api/deletemaritalstatus/'+maritalstatus.id).then(function(data) {
			$scope.getmarital_status();
		})
		}
		else {
		return false;
		}
		}
		$scope.getmarital_status();
					
	}
})();