(function() {
  'use strict';

 angular.module('authApp').controller('NhifController', NhifController);
 NhifController.$inject = ['$scope','$rootScope', '$mdDialog', '$state','$http','toastr','$mdpTimePicker'];
 
  function NhifController($scope,$rootScope, $mdDialog, $state,$http,toastr,$mdpTimePicker) {
	  var facility_id =$rootScope.currentUser.facility_id;
	  var user_id =$rootScope.currentUser.id;
	  var nhif_service  =[];
	  var nhif_service_restricted  =[];
	  var system_service=[];

    $scope.isEmpty = function (obj) {
      for (var i in obj) if (obj.hasOwnProperty(i)) return false;
      return true;
  };
	  $scope.start_time = {
                    twelve: new Date(),
                    twentyfour: new Date()
                    };
	
	                $scope.end_time = {
                    twelve: new Date(),
                    twentyfour: new Date()
                    };

                    $scope.message = {
                    hour: 'Hour is required',
                    minute: 'Minute is required',
                    meridiem: 'Meridiem is required'
                    };

      $scope.saveNHIF =function(facility_code) {
        if(angular.isDefined(facility_code)==false){
              return sweetAlert('Please Enter NHIF facility Code','','error');
         } else{
            var postData={facility_code:facility_code};
            $http.post('/api/map-facility-code', postData)
                    .then(function (response) {  
                    return sweetAlert(response.data.message,'',response.data.error);

                    });     
  }
};



                    

	$scope.openFiles = function (selectedPatient) {
        var postData={account_id:selectedPatient.account_id,facility_id:facility_id};
            $http.post('/api/generate-files', postData)
                    .then(function (response) {
						$mdDialog.show({
                                controller: function ($scope) {
                               
									$scope.applicant=response.data[0];
                                 
                                   $scope.cancel = function () {
									   $mdDialog.hide();
									};
								},
								templateUrl: '/views/modules/insurance/insuarance-files.html',              
								parent: angular.element(document.body),
											clickOutsideToClose: false,
											fullscreen: $scope.customFullscreen // Only for -xs, -sm breakpoints.
						   });

       });

                  };
				  
				  $scope.getClaimsReconciliation = function(pef){					 
					 
	  	  var postData={claim_year :pef.claim_year,claim_month:pef.claim_month};
          $http.post('/api/get-claim-reconciliation', postData)
                    .then(function (response) {
						$scope.claimsReconciliations=response.data;
					if(response.data.length ==0){
					  sweetAlert("No claim on month "+pef.claim_month+"/"+pef.claim_year+", not found, please click 'Reconcile with NHIF'","","info")
        				}
						});
	  };  


          $scope.createClaimsReconciliation = function(pef){					 
					 
	  	  var postData={claimYear :pef.claim_year, claimMonth:pef.claim_month};
          $http.post('/api/create-claim-reconciliation', postData)
                    .then(function (response) {
						 
					 
					  return sweetAlert("Claims reconciliation on month "+pef.claim_month+"/"+pef.claim_year+", completed'","","info");
        				 
						});
	  };  


 $scope.getClaimsByFolioNumber = function(pef){					 
					 
	  	  var postData={year_of_visit :pef.year_of_visit,month_of_visit:pef.month_of_visit,folio_number:pef.folio_number};
          $http.post('/api/get-nhif-claims-by-folio-number', postData)
                    .then(function (response) {
						$scope.NhifClaimsByFolioNumber=response.data;
					if(response.data.length ==0){
					  sweetAlert("This claim with  folio number "+pef.folio_number+" on month "+pef.month_of_visit+"/"+pef.year_of_visit+", not submitted","","info")
        				}
						});
	  };  

    $scope.openClaimForm = function (selectedPatient) {
    		var postData={account_id:selectedPatient.account_id,facility_id:facility_id};
			//$scope.openFiles(postData);
            $http.post('/api/generate-files', postData)
                    .then(function (response) {    
						if(response.data.statusCode != 200){
							swal(response.data.Message, '', 'info');
							return;
						}
						
						$mdDialog.show({
							controller: function ($scope) {
                                $scope.patientData    	=	selectedPatient;
                                $scope.consultations    =	response.data.consultations;	
                                $scope.investigations   =	response.data.investigations;
                                $scope.prescriptions    =	response.data.prescriptions;
                                $scope.admissions       =	response.data.admissions;
                                $scope.procedures       =	response.data.procedures;
                                $scope.diagnoses        =	response.data.diagnoses;
                                $scope.clinician        =	response.data.clinician;
                                $scope.serialNo        	=	response.data.serialNo;
								
								$scope.totalConsultations 	= 0;
								$scope.totalInvestigations 	= 0;
								$scope.totalPrescriptions 	= 0;						
								$scope.totalProcedures 		= 0;
								$scope.totalAdmissions 		= 0;
								$scope.grandTotal	 		= 0;
								
								$scope.totals  =  function(){
									if($scope.consultations != undefined)
										$scope.consultations.forEach(function(item){
											$scope.totalConsultations += parseFloat(item.item_price)*parseFloat(item.quantity);
										});
										
									if($scope.investigations != undefined)
										$scope.investigations.forEach(function(item){
											$scope.totalInvestigations += parseFloat(item.item_price)*parseFloat(item.quantity);
										});

										
									if($scope.prescriptions != undefined)
										$scope.prescriptions.forEach(function(item){
											$scope.totalPrescriptions += parseFloat(item.item_price)*parseFloat(item.quantity);
										});
									
									
									if($scope.admissions != undefined)
										$scope.admissions.forEach(function(item){
											$scope.totalAdmissions += parseFloat(item.item_price)*parseFloat(item.quantity);
										});
									
									
									if($scope.procedures != undefined)
										$scope.procedures.forEach(function(item){
											$scope.totalProcedures += parseFloat(item.item_price)*parseFloat(item.quantity);
										});
									$scope.grandTotal = $scope.totalConsultations 
											+ $scope.totalInvestigations  
											+ $scope.totalPrescriptions 
											+ $scope.totalProcedures
											+ $scope.totalAdmissions;
								};     
                                
								$scope.sendClaim = function(){
									var postData={user_id:user_id,patient_id:selectedPatient.patient_id,account_id:selectedPatient.account_id,facility_id:facility_id};
									$http.post('/api/getPostClaim', postData).then(function (response) {	
										if(response.data.StatusCode ==200){
											return sweetAlert(response.data.Message, "", "success");                  
										}else{
											  return sweetAlert(response.data.Message, "", "error");               
										}

									});
								};  

         
//saveSignature

$scope.saveSignature = function(patient) {
    //console.log(angular.element('#username').value); // undefined
	if (localStorage.getItem("clientSignature") === null) {
     return sweetAlert("Please let client sign form before saving!", "", "error");
     }
	const clientSignature = localStorage.getItem('clientSignature');
	var binaryData= "data:image/png;base64," +clientSignature;
	
	var postData={binaryData:binaryData,visitId:patient.account_id};
								
	$http.post('/api/client-signature-pad', postData).then(function (response) {         
										$scope.cancel();
										localStorage.removeItem('clientSignature');
										return sweetAlert(response.data.Message, "", response.data.status);
									});
};
								$scope.markAsVerified = function(patient){
									var postData={user_id:user_id,account_id:patient.account_id};
									$http.post('/api/mark-as-ok', postData).then(function (response) {         
										$scope.cancel();
										return sweetAlert(response.data.Message, "", response.data.status);
									});
								};  

								$scope.PrintContent = function () {
									var DocumentContainer = document.getElementById('divtoprint');
									var WindowObject = window.open("", "PrintWindow","width=900,height=700,top=50,left=450,toolbars=no,scrollbars=no,status=no,resizable=yes");
									WindowObject.document.title = "PRINT CLAIM FORM: NHIF";
									WindowObject.document.writeln(DocumentContainer.innerHTML);
									WindowObject.document.close();
									setTimeout(function () {
													WindowObject.focus();
													WindowObject.print();
													WindowObject.close();
												}, 0);

								};
					            
								$scope.cancel = function () {
                                   $mdDialog.hide();
                                };
								
								$scope.totals();
                            },
							templateUrl: '/scripts/modules/nhif/views/claim-form.html',
							parent: angular.element(document.body),
							clickOutsideToClose: false,
							fullscreen: $scope.customFullscreen // Only for -xs, -sm breakpoints.
						});
					});
				};


           $scope.getClaims = function(pef){
	  	  var postData={start_time :pef.start,end_time:pef.end};
          $http.post('/api/getNHIFclaims', postData)
                    .then(function (response) {
						$scope.NhifClaims=response.data;
					if(response.data.length ==0){
					  sweetAlert("No any claim from "+pef.start+" to "+pef.end,"","info")
        				}
						});
	  };  


     $scope.getVerifiedClaims = function(pef){
        var postData={start_time :pef.start,end_time:pef.end};
          $http.post('/api/verified-claims', postData)
                    .then(function (response) {
           $scope.verified_claims=response.data;
          
            });
    };  


$scope.sendBulkClaim = function(){
       
          $http.post('/api/send-bulk-claims')
                    .then(function (response) {
                      if(response.data.StatusCode ==200){
                        return sweetAlert(response.data.Message, "", "success");                  
                          }else{
                            return sweetAlert(response.data.Message, "", "error");               
                          }

          
          
            });
    };  
          		

    $scope.getSubmittedNhifClaims = function(pef){
	  	  var postData={start_time :pef.start,end_time:pef.end};
          $http.post('/api/getSubmittedNhifClaims', postData)
                    .then(function (response) {
						$scope.SubmittedNhifClaims=response.data;
						if(response.data.length ==0){
					  sweetAlert("No Submitted claim from "+pef.start+" to "+pef.end,"","info")
        				}
						});
	  };    

      $scope.getPatientsFiles = function(pef){
        var postData={start_time :pef.start,end_time:pef.end};
          $http.post('/api/getPatientsFiles', postData)
                    .then(function (response) {
            $scope.filesPatients=response.data;
            if(response.data.length ==0){
            sweetAlert("No Submitted claim from "+pef.start+" to "+pef.end,"","info")
                }
            });
    };    

	   $scope.getAmountsClaimed  = function(pef){
	  	  var postData={facility_id:facility_id,start_time :pef.start,end_time:pef.end};
          $http.post('/api/getAmountsClaimed', postData)
                    .then(function (response) {
						   $mdDialog.show({
                                controller: function ($scope) {
                               
                                 $scope.applicant=response.data[0];
                                 
                                   $scope.cancel = function () {
                                   $mdDialog.hide();
                                };
                            },
                templateUrl: '/views/modules/insurance/claim-summary.html',              
                parent: angular.element(document.body),
                            clickOutsideToClose: false,
                            fullscreen: $scope.customFullscreen // Only for -xs, -sm breakpoints.
                               });
						});
	  };        	



	  			

	  $scope.getNHIFprices = function(){
	  	  var postData={facility_id :facility_id};
        //   $http.post('/api/getNHIFprices', postData)
           $http.get('/api/nhif-item-price/create')
                    .then(function (response) {
						$scope.NhifItems=response.data;
                       // console.log($scope.NhifItems[0]);
                         sweetAlert("Price list was successfully loaded","","success");
                         
                        })
                        .then(function (error) {
                             sweetAlert("Failed to update price list, please check connectivity","","error");
                            });
      };

      $scope.changeNhifPrice = function(){
       //   $http.post('/api/getNHIFprices', postData)
       $http.post('/api/nhif-item-price')
                .then(function (response) {
                    $scope.NhifItems=response.data;
                   // console.log($scope.NhifItems[0]);
                     sweetAlert("Price list was successfully updated","","success");
                     
                    })
                    .then(function (error) {
                        sweetAlert("Price list was successfully updated","","success");
                   
                       //  sweetAlert("Failed to complete  price update, check connectivity and retry","","error");
                        });
  };

  $scope.saveApiCredential = function(api){
    if(!angular.isDefined(api)){
        return  sweetAlert("Please enter credential","","error");
        }
      else if(!angular.isDefined(api.username)){
      return  sweetAlert("Please enter username","","error");
      }
      else if(!angular.isDefined(api.password)){
       return  sweetAlert("Please enter password","","error");
      }
      else if(!angular.isDefined(api.FacilityCode)){
        return sweetAlert("Please enter facility code","","error");
      }
      $http.post('/api/api-credential',api)
             .then(function (response) {
                   sweetAlert("Credential was successfully added","","success");                  
                 });
               
            };
      $scope.getMappedPrices = function(){
        var postData={facility_id :facility_id};
      $http.post('/api/getMappedPrices', postData)
                .then(function (response) {
                    $scope.mappedItems=response.data;
                      });
      };
    
      $scope.getNhifServices = function(searchKey){
	  	      $http.get('/api/nhif-item/'+searchKey)
                    .then(function (response) {
						nhif_service=response.data;		
						});
            return nhif_service;
      };

    $scope.getNhifServicesWithPermit = function(searchKey){
      $http.get('/api/nhif-item-price/'+searchKey)
              .then(function (response) {
                nhif_service_restricted=response.data;		
      });
              return nhif_service_restricted;
};



      $scope.preApproval = function(service){
        var dataToPost={
               card_no:service.card_no,
               refference_no:service.refference_no,
               item_code:$scope.nhif_service_restricts.item_code
               }
        $http.post('/api/pre-approval-service',dataToPost)
                .then(function (response) {
                  if(response.data.status==200){
                    return sweetAlert(response.data.Message,"","success");	
                  }
                  return sweetAlert(response.data.Message,"","error");	
        });
              
      };
    

    //nhif_service_restricted
    

    $scope.selectedNhifServiceRestricted = function(nhifService){	  	  
      $scope.nhif_service_restricts=nhifService;

};

	  $scope.selectedNhifService = function(nhifService){	  	  
                    $scope.nhif_service=nhifService;

	  };

	  $scope.selectedSystemItem = function(syst_service){	  	  
                    $scope.system_service=syst_service;

	  };

	  $scope.getSystemService = function(searchKey){
	  	  var postData={searchKey :searchKey};
          $http.post('/api/getSystemServices', postData)
                    .then(function (response) {
						system_service=response.data;		
						});
                    return system_service;
	  };

	  $scope.mapService = function(){
		  if($scope.system_service == undefined || $scope.nhif_service == undefined){
			  swal('Please, select corresponding items on both fields','','warning');
			  return;
		  }
		  
	  	  var gothomis_item_id  = $scope.system_service.id;
	  	  var nhif_item_id      = $scope.nhif_service.id;
		  
	  	  var postData={gothomis_item_id :gothomis_item_id,nhif_item_id:nhif_item_id};
          $http.post('/api/mapServices', postData)
                    .then(function (response) {
						if(response.status == 200){
							system_service=[];
							nhif_service=[];
							$scope.system_service ={};
							$scope.nhif_service   ={};
							return toastr.success('',response.data.message);
						}
						else{
							return toastr.error('',response.data.message);			
						}

					});
	  };
   }
	   
})();