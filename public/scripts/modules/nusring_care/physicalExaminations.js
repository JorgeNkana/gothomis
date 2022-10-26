(function () {

    'use strict';

    var app = angular.module('authApp');

    app.controller('physicalExaminations',

                ['$filter','$scope','$http','$rootScope','$uibModal', '$uibModalInstance', 'object',
        function ($filter,$scope,$http,$rootScope,$uibModal,$uibModalInstance,object) {
			      $scope.admissions=object;
				  console.log($scope.admissions);
				  var facility_id =$rootScope.currentUser.facility_id;
				  var user_id =$rootScope.currentUser.id;
				  
				  $scope.saveResipratorySystem= function (system,selectedPatient) {
			 if(angular.isDefined(system)==false){
					  
					  return sweetAlert("Please Enter value at least one field", "", "error");
				  }
			else{
				console.log(selectedPatient);
			var information_category='RESPIRATORY HISTORY';
			var resipratorySystem={"erasor":0,"admission_id":selectedPatient.admission_id,"request_id":selectedPatient.id,"inspection":system.	inspection,"palpation":system.palpation,"auscultation":system.auscultation,"percussion":system.percussion,"other_information":information_category,"nurse_id":user_id};
			
			console.log(resipratorySystem);
			
			$http.post('public/api/saveResipratorySystem',resipratorySystem).then(function(data) {
            if(data.data.status ==0){
					 
					 sweetAlert(data.data.data, "", "error");
				  }else{
					  $uibModalInstance.dismiss();
					    sweetAlert(data.data.data, "", "success");		 				
				  }	
		   
            });
		}
			
        }		


		$scope.addDentalInfo= function (system,selectedPatient) {
			 if(angular.isDefined(system)==false){					  
					  return sweetAlert("Please Enter value at least one field", "", "error");
				  }
			else{				
			var information_category='DENTAL STATUS';
			var resipratorySystem={"erasor":0,"admission_id":selectedPatient.admission_id,"request_id":selectedPatient.id,"inspection":system.	inspection,"palpation":system.palpation,"auscultation":system.auscultation,"percussion":system.percussion,"other_information":information_category,"nurse_id":user_id};
			
			console.log(resipratorySystem);
			
			$http.post('public/api/saveResipratorySystem',resipratorySystem).then(function(data) {
            if(data.data.status ==0){
					 
					 sweetAlert(data.data.data, "", "error");
				  }else{
					  $uibModalInstance.dismiss();
					    sweetAlert(data.data.data, "", "success");		 				
				  }	
		   
            });
		}
			
        }
		//saveTeethStatus
		$scope.addDentalInfo= function (dental,selectedPatient) {
			 if(angular.isDefined(dental)==false){					  
					  return sweetAlert("Please Select Dental Status", "", "error");
				  }
			else{				
			var information_category='DENTAL STATUS';
			var css_class='';
			if(dental.dental_status==2){
				var css_class='numberCircle';
			}else if(dental.dental_status==1){
			var css_class='missedDental';				
			}
			else if(dental.dental_status==3){
			var css_class='decayedDental';	
				
			}
			
			console.log(selectedPatient.tooth_id);
var teeth_status={"erasor":0,"admission_id":selectedPatient.admission_id,
"request_id":selectedPatient.id,"dental_id":selectedPatient.tooth_id,"dental_status":dental.dental_status,
"css_class":css_class,"other_information":information_category,"nurse_id":user_id};
					
			$http.post('public/api/saveTeethStatus',teeth_status).then(function(data) {
            if(data.data.status ==0){
					 
					 sweetAlert(data.data.data, "", "error");
				  }else{
					  $uibModalInstance.dismiss();
					    sweetAlert(data.data.data, "", "success");		 				
				  }	
		   
            });
		}
			
        }

			$scope.saveCardivascularSystem= function (system,selectedPatient) {
			 if(angular.isDefined(system)==false){
					  
					  return sweetAlert("Please Enter value at least one field", "", "error");
				  }
			else{
				console.log(selectedPatient);
			var information_category='CARDIVASCULAR SYSTEM';
			var resipratorySystem={"erasor":0,"admission_id":selectedPatient.admission_id,"request_id":selectedPatient.id,"inspection":system.	inspection,"palpation":system.palpation,"auscultation":system.auscultation,"percussion":system.percussion,"other_information":information_category,"nurse_id":user_id};
			
			console.log(resipratorySystem);
			
			$http.post('public/api/saveResipratorySystem',resipratorySystem).then(function(data) {
            if(data.data.status ==0){
					 
					 sweetAlert(data.data.data, "", "error");
				  }else{
					  $uibModalInstance.dismiss();
					    sweetAlert(data.data.data, "", "success");		 				
				  }	
		   
            });
		}
			
        }
	
	$scope.saveGastroIntestineExaminations= function (system,selectedPatient) {
			 if(angular.isDefined(system)==false){
					  
					  return sweetAlert("Please Enter value at least one field", "", "error");
				  }
			else{
				console.log(selectedPatient);
			var information_category='Gastro Intestine';
			var resipratorySystem={"erasor":0,"admission_id":selectedPatient.admission_id,"request_id":selectedPatient.id,"inspection":system.	inspection,"palpation":system.palpation,"auscultation":system.auscultation,"percussion":system.percussion,"other_information":information_category,"nurse_id":user_id};
			
			console.log(resipratorySystem);
			
			$http.post('public/api/saveResipratorySystem',resipratorySystem).then(function(data) {
            if(data.data.status ==0){
					 
					 sweetAlert(data.data.data, "", "error");
				  }else{
					  $uibModalInstance.dismiss();
					    sweetAlert(data.data.data, "", "success");		 				
				  }	
		   
            });
		}
			
        }
		
		$scope.saveCentralNervousSystemExaminations= function (system,selectedPatient) {
			 if(angular.isDefined(system)==false){
					  
					  return sweetAlert("Please Enter value at least one field", "", "error");
				  }
			else{
				console.log(selectedPatient);
			var information_category='CENTRAL NERVOUS SYSTEM';
			var resipratorySystem={"erasor":0,"admission_id":selectedPatient.admission_id,"request_id":selectedPatient.id,"inspection":system.	inspection,"palpation":system.palpation,"auscultation":system.auscultation,"percussion":system.percussion,"other_information":information_category,"nurse_id":user_id};
			
			console.log(resipratorySystem);
			
			$http.post('public/api/saveResipratorySystem',resipratorySystem).then(function(data) {
            if(data.data.status ==0){
					 
					 sweetAlert(data.data.data, "", "error");
				  }else{
					  $uibModalInstance.dismiss();
					    sweetAlert(data.data.data, "", "success");		 				
				  }	
		   
            });
		}
			
        }
				  
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
				  
				  
				  
				  
				  
				  
				  
				  
				  
			     
			   
				
           $scope.enterEncounter=function (encounter,patient,facility_id) {
			     	 
				
				  if (angular.isDefined(encounter)==false) {
                   return sweetAlert("Please Type the Payment Category", "", "error");
                  }	
				  else if (angular.isDefined(encounter.payment_category)==false) {
                   return sweetAlert("Please Type the Payment Category", "", "error");
                  }					  			 			  
				  else if (angular.isDefined(encounter.payment_services)==false) {
                   return sweetAlert("Please Select Service", "", "error");
                  }				  
				  else{
                   
				  console.log(encounter);
				  var patient_category=encounter.payment_category.patient_category;			  
				  var service_category=encounter.payment_services;
				  var service_id=encounter.payment_services.service_id;
				  var price_id=encounter.payment_services.price_id;		
				  var item_type_id=encounter.payment_services.item_type_id;		
				  var patient_id=patient;		
				  var facility_id=facility_id;		
				  var user_id=$rootScope.currentUser.id;		
				  var enterEncounter={'item_type_id':item_type_id,'patient_category':patient_category,
				  'service_category':service_category,'service_id':service_id,'price_id':price_id,'patient_id':patient_id ,'facility_id':facility_id,'user_id':user_id};
				 
				 $http.post('public/api/enterEncounter',enterEncounter).then(function(data) {
						  $scope.registrationReport=data.data;
	
                  if(data.data.status ==0){
					 
					 sweetAlert(data.data.data, "", "error");
				  }else{
					  
					   $http.get('public/api/getPatientInfo/'+patient_id).then(function(data) {
						  $scope.patientsInfo=data.data;					  
					   });
					  
					    var modalInstance = $uibModal.open({
				  templateUrl: 'public/views/modules/registration/printCard.html',
				  size: 'lg',
				  animation: true,
				  controller: 'printCard',
				  resolve:{
                  patientData: function () {
					  //console.log($scope.quick_registration);
                  return $scope.patientData;
                  }
                  }

				  
                  });
					  
					//sweetAlert(data.data.data, "", "success"); 
                    //enterEncounter='';					
				  }
				  
				  
					  });
				 
				 
					
		   }
			}   


			$scope.addDischarge=function (discharge,admission_id,dt) {
                 				
				  if (angular.isDefined(discharge)==false) {
                   return sweetAlert("Provide Discharge Notes", "", "error");
                  }	
				  else if (angular.isDefined(admission_id)==false) {
                   return sweetAlert("Please Select Patient", "", "error");
                  }					  			 			  
				  	  
				  else{
                  var dateSelected = $filter('date')(dt,'yyyy-MM-dd');
                  var today = $filter('date')(new Date(),'yyyy-MM-dd');				  
				  var user_id=$rootScope.currentUser.id;		
				  var dischargeNotes={'permission_date':today,'nurse_id':user_id,
				  'confirm':1,'admission_id':admission_id,'domestic_dosage':discharge,'followup_date':dateSelected};
				 
				 $http.post('public/api/addDischargeNotes',dischargeNotes).then(function(data) {
						  $scope.dischargeNotes=data.data;
	
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