/**
 * Created by USER on 2017-02-13.
 */
(function() {

    'use strict';

    angular
        .module('authApp')
        .controller('tribeController', tribeController);

    function tribeController($http, $auth, $rootScope,$state,$location,$scope) {
		
		//Tribe Get Data From the database
        $scope.gettribe_name=function () {
            $http.get('/api/gettribe_name').then(function(data) {
				$scope.tribename=data.data;
            });
        }
				
		//Tribe Registrations
        $scope.tribe_registration=function (tribe) {
		var comit=confirm('Are you sure you want to Register '+tribe.tribe_name);
		if(comit) {
		$http.post('/api/tribe_registration',tribe).then(function(data) {
			$scope.gettribe_name();
		});
		 }
		 
        }
		
		
		//Tribe Update
        $scope.updatetribe=function (tribe) {
		
		var comit=confirm('Are you sure you want to Update '+tribe.tribe_name);
		if(comit) {
		$http.post('/api/updatetribe', tribe).then(function (data) {
			$scope.good = tribe.tribe_name + " " + 'Successful Updated';
			$scope.gettribe_name();
		})
		}
		else{
			return false;
		}
        }
		
		//Tribe delete
        $scope.deletetribe=function (tribe) {
		var comit=confirm('Are you sure you want to delete '+tribe.tribe_name);
		if(comit){
		$http.get('/api/deletetribe/'+tribe.id).then(function(data) {
		$scope.deleted=tribe.id+" "+'Successful Deleted';
		$scope.gettribe_name();
		})
		}
		else {
		return false;
		}
		}
		$scope.gettribe_name();
    }

})();