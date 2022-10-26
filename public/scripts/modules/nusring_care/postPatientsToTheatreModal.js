(function () {

    'use strict';

    var app = angular.module('authApp');

    app.controller('postPatientsToTheatreModal',

                ['$filter','$scope','$http','$rootScope','$uibModal', '$uibModalInstance', 'object',
        function ($filter,$scope,$http,$rootScope,$uibModal,$uibModalInstance,object) {
			      $scope.admissions=object;
				 
				   $scope.today = function() {
    $scope.dt = new Date();
  };
  $scope.today();

  $scope.clear = function() {
    $scope.dt = null;
  };

  $scope.options = {
    customClass: getDayClass,
    minDate: new Date(),
    showWeeks: true
  };

  // Disable weekend selection
  function disabled(data) {
    var date = data.date,
      mode = data.mode;
    return mode === 'day' && (date.getDay() === 0 || date.getDay() === 6);
  }

  $scope.toggleMin = function() {
    $scope.options.minDate = $scope.options.minDate ? null : new Date();
  };

  $scope.toggleMin();

  $scope.setDate = function(year, month, day) {
    $scope.dt = new Date(year, month, day);
  };

  var tomorrow = new Date();
  tomorrow.setDate(tomorrow.getDate() + 1);
  var afterTomorrow = new Date(tomorrow);
  afterTomorrow.setDate(tomorrow.getDate() + 1);
  $scope.events = [
    {
      date: tomorrow,
      status: 'full'
    },
    {
      date: afterTomorrow,
      status: 'partially'
    }
  ];

  function getDayClass(data) {
    var date = data.date,
      mode = data.mode;
    if (mode === 'day') {
      var dayToCheck = new Date(date).setHours(0,0,0,0);

      for (var i = 0; i < $scope.events.length; i++) {
        var currentDay = new Date($scope.events[i].date).setHours(0,0,0,0);

        if (dayToCheck === currentDay) {
          return $scope.events[i].status;
        }
      }
    }

    return '';
  }
				    
		$scope.enterTheatre=function (theatre_notes,admission_id,dt) {
                 				
				  if (angular.isDefined(theatre_notes)==false) {
                   return sweetAlert("Provide Doctor Prescriptions", "", "error");
                  }	
				  else if (angular.isDefined(admission_id)==false) {
                   return sweetAlert("Please Select Patient", "", "error");
                  }					  			 			  
				  	  
				  else{
                  var dateSelected = $filter('date')(dt,'yyyy-MM-dd');
                  var today = $filter('date')(new Date(),'yyyy-MM-dd');				  
				  var user_id=$rootScope.currentUser.id;		
				  var theatre_notes_given={'received':0,'posted_date':today,'nurse_id':user_id,
				  'confirm':1,'admission_id':admission_id,'prescriptions':theatre_notes,'operation_date':dateSelected};
				 
				 $http.post('public/api/enterTheatre',theatre_notes_given).then(function(data) {
						  
                  if(data.data.status ==0){
					 
					 sweetAlert(data.data.data, "", "error");
				  }else{
					sweetAlert(data.data.data, "", "success");  
					   					
				  }
				  
				  
					  });
				 
				 
					
		   }
			}
			
			$scope.cancel=function (){
				console.log('done and cleared');
			$uibModalInstance.dismiss();
			
			}
			
			
			$scope.closeAllModals=function (){
				console.log('done and cleared');
			$uibModalInstance.dismissAll();
			
			}

        }]);
		
		
		
		
		
}());