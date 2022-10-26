/**
 * Created by USER on 2017-02-13.
 */
(function() {

    'use strict';

    angular
        .module('authApp')
        .controller('professionalController', professionalController);

    function professionalController($http, $auth, $rootScope,$state,$location,$scope) {


	  //Professioals Get Data From the database
        $scope.getprofessional=function () {

            $http.get('/api/professional_registration').then(function(data) {
                $scope.professional=data.data;
            });
        }
	
            //Professionals Registrations
        $scope.professional_registration=function (prof) {
			var comit=confirm('Are you sure you want to Register '+prof.prof_name);
            if(comit) {
            $http.post('/api/professional_registration',prof).then(function(data) {
                //$scope.prof.prof_name="";
				//$scope.region_list();
				$scope.getprofessional();			
            });
			 }
        }
		 
		
		//Professionals Update
        $scope.update=function (professional) {
            var comit=confirm('Are you sure you want to Update '+professional.prof_name);
            if(comit) {
                $http.post('/api/update_professional', professional).then(function (data) {
                    $scope.good = professional.prof_name + " " + 'Successful Updated';
                    $scope.kol = data.status;
                    //$scope.region_list();
					$scope.getprofessional();
                })
            }
            else{
                return false;
            }
        }
		
		//Professionals delete
        $scope.deleteprof=function (professional) {
            var comit=confirm('Are you sure you want to delete'+" "+professional.prof_name);
            if(comit){
                $http.get('/api/deleteprof/'+professional.id).then(function(data) {
                   $scope.getprofessional();
                })
            }
            else {
                return false;
            }
            }
		
    }

})();