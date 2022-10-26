(function () {

    'use strict';

    var app = angular.module('authApp');

    app.controller('mortuaryCareModal',

                ['$scope','$http','$rootScope','$uibModal', '$uibModalInstance', 'object',
        function ($scope,$http,$rootScope,$uibModal,$uibModalInstance,object) {
			      $scope.mortuaryPaticulars=object;
			      $scope.corpsePaticulars=object[0];
			    var mortuary_id= $scope.mortuaryPaticulars[0].mortuary_id;
				  
				  $http.get('/api/getCabintesWithNoCorpses/'+mortuary_id).then(function(data) {
                 $scope.cabinets_availables=data.data;
			     //console.log($scope.cabinets_availables);
			          }); 
			     
				//$scope.quick_registration=quick_registration;
				////console.log($scope.quick_registration);
				//$scope.patientData=$scope.quick_registration;
				////console.log($rootScope.currentUser);
				/**
				var last_visit={'patient_id':$scope.quick_registration.id,'facility_id':$scope.quick_registration.facility_id};
				
				$http.post('/api/printLastVisit',last_visit).then(function(data) {
						  $scope.patient_last_visit=data.data;
	
                      //registration_title
				  //console.log($scope.patient_last_visit);
					  });
				
				**/

                $scope.giveCabinet=function (cabinets_available,corpsePaticulars) {
                    var facility_id =$rootScope.currentUser.facility_id;
                    var user_id =$rootScope.currentUser.id;
                    var corpse_admission_id=corpsePaticulars.corpse_admission_id;
                    var cabinet_id=cabinets_available.id;
                    var user_id=$rootScope.currentUser.id;
                    var corpseDetails={'corpse_admission_id':corpse_admission_id,'cabinet_id':cabinet_id,'facility_id':facility_id,'user_id':user_id};

                    $http.post('/api/giveCabinetCorpse',corpseDetails).then(function(data) {
                         if (data.data.status ==1) {
                             $scope.cancel();
                            sweetAlert(data.data.data, "", "success");
                        }
                    });
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
                   
				  //console.log(encounter);
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
				 
				 $http.post('/api/enterEncounter',enterEncounter).then(function(data) {
						  $scope.registrationReport=data.data;
	
                  if(data.data.status ==0){
					 
					 sweetAlert(data.data.data, "", "error");
				  }else{
					  
					   $http.get('/api/getPatientInfo/'+patient_id).then(function(data) {
						  $scope.patientsInfo=data.data;					  
					   });
					  
					    var modalInstance = $uibModal.open({
				  templateUrl: '/views/modules/registration/printCard.html',
				  size: 'lg',
				  animation: true,
				  controller: 'printCard',
				  resolve:{
                  patientData: function () {
					  ////console.log($scope.quick_registration);
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
			
			$scope.cancel=function (){
				//console.log('done and cleared');
			$uibModalInstance.dismiss();
			
			}
			
			
			$scope.closeAllModals=function (){
				//console.log('done and cleared');
			$uibModalInstance.dismissAll();
			
			}

        }]);
		
		
		
		
		
}());