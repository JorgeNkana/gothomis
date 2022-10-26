/**
 * Created by USER on 2017-02-13.
 */
(function() {

    'use strict';

    angular
        .module('authApp')
        .controller('occupationController', occupationController);

    function occupationController($http, $auth, $rootScope,$state,$location,$scope) {
				
		//Occupation Get Data From the database
        $scope.getoccupation=function () {
            $http.get('/api/getoccupation').then(function(data) {
                $scope.occupation=data.data;
            });
        }		
		
		//Occupation Registrations
        $scope.occupation_registration=function (occupati) {
			//var fr={'occupation_name':occupation.occupation_name};
			////console.log(fr);
			////console.log(occupation);
		 var comit=confirm('Are you sure you want to Register '+occupati.occupation_name);
		if(comit) {
		$http.post('/api/occupation_registration',occupati).then(function(data) {
		});
		 }
		 $scope.getoccupation(); 
        }
		
		//Occupation Update
        $scope.updateoccupation=function (occupation) {
		var comit=confirm('Are you sure you want to Update '+occupation.occupation_name);
		if(comit) {
		$http.post('/api/updateoccupation', occupation).then(function (data) {
		})
		$scope.getoccupation();
		}
		else{
			return false;
		}
        }
		$scope.getoccupation();
		
		
		//Occupation delete
        $scope.deleteoccupation=function (occupation) {
		var comit=confirm('Are you sure you want to delete '+occupation.occupation_name);
		if(comit){
		$http.get('/api/deleteoccupation/'+occupation.id).then(function(data) {
			$scope.getoccupation();
		})
		}
		else {
		return false;
		}
		}
		$scope.getoccupation();
			
	}

})();