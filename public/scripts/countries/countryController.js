/**
 * Created by USER on 2017-02-13.
 */
(function() {

    'use strict';

    angular
        .module('authApp')
        .controller('countryController', countryController);

    function countryController($http, $auth, $rootScope,$state,$location,$scope) {
		
		//country_zones Get Data From the database
        $scope.getcountry_zone=function () {
            $http.get('/api/getcountry_zone').then(function(data) {
                $scope.countryzones=data.data;
            });
        }
		
		//Country Zone Registrations
        $scope.country_zone_registration=function (countryzone) {
			////console.log(countryzone);
		var comit=confirm('Are you sure you want to Register '+countryzone.country_zone);
		if(comit) {
		$http.post('/api/country_zone_registration',countryzone).then(function(data) {
		});
		 }
        }		
				
		//country zone Update
        $scope.updatecountryzone=function (countryzone) {
		//console.log(countryzone);
		var comit=confirm('Are you sure you want to Update '+countryzone.country_zone);
		if(comit) {
		$http.post('/api/update_country_zone', countryzone).then(function (data) {
		$scope.getcountry_zone();
		})
		}
		else{
			return false;
		}
        }
		
		//country zone delete
        $scope.deletecountryzone=function (countryzones) {
		var comit=confirm('Are you sure you want to delete '+countryzones.country_zone);
		if(comit){
		$http.get('/api/deletecountryzone/'+countryzones.id).then(function(data) {
		$scope.getcountry_zone();
		})
		}
		else {
		return false;
		}
		}
		$scope.getcountry_zone();
			
		
		//country Get Data From the database
        $scope.getcountry=function () {
		$http.get('/api/getcountries').then(function(data) {
		$scope.country=data.data;
		});
        }
		
	   //Country Registrations
        $scope.country_name_registration=function (countr) {
		//console.log(countr);
		var comit=confirm('Are you sure you want to Register '+countr.country_name);
		if(comit) {
		$http.post('/api/country_name_registration',countr).then(function(data) {
			$scope.getcountry();
		});
		 } 
        }
		$scope.getcountry();
		
		
		
		//country Update
        $scope.updatecountry=function (country) {
		var comit=confirm('Are you sure you want to Update '+country.country_name);
		if(comit) {
		$http.post('/api/update_country_name', country).then(function (data) {
		$scope.getcountry();
		})
		}
		else{
		return false;
		}
        }
		$scope.getcountry();
		
		//Country delete
        $scope.deletecountry=function (country) {
		var comit=confirm('Are you sure you want to delete'+" "+country.country_name);
		if(comit){
		$http.get('/api/deletecountry/'+country.id).then(function(data) {
		$scope.getcountry();
		})
		}
		else {
		return false;
		}
		}
		$scope.getcountry();

    }

})();