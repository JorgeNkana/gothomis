/**
 * Created by USER on 2017-02-13.
 */
(function() {

    'use strict';

    angular
        .module('authApp')
        .controller('nursingCareController',nursingCareController);

    function patientController($http, $auth, $rootScope,$state,$location,$scope,$uibModal) {
				  var facility_id =$rootScope.currentUser.facility_id;
				  var user_id =$rootScope.currentUser.id;
				  
				  
				   var resdata =[];
				   var nextresdata =[];
				   var patientCategory =[];
				   var patientService =[];
				   var patientsList=[];
				   var maritals=[];
                   var tribe=[];
				   var occupation=[];				   
				   var country=[];				   
				   var relationships=[];				   
				   
				   
				   		   
		    $scope.showSearchMarital= function (searchKey) {
				
            $http.get('public/api/getMaritalStatus/'+searchKey).then(function(data) {
            maritals=data.data;
			
            });
			console.log(maritals);
			return maritals;
        }
		
				

    }

})();