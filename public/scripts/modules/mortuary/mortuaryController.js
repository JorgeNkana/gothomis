/**
 * Created by USER on 2017-02-13.
 */
(function() {

    'use strict';

    angular
        .module('authApp')
        .controller('mortuaryController',mortuaryController);

    function mortuaryController($http, $auth, $rootScope,$state,$location,$scope,$uibModal,$mdDialog, $mdBottomSheet,Helper,$filter) {
				  var facility_id =$rootScope.currentUser.facility_id;
				  var user_id =$rootScope.currentUser.id;
        $scope.mytime1 = new Date();
        $scope.mytime2 = new Date();

				  $scope.AdmissionNotes="";
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
				   
				     angular.element(document).ready(function () {
						  $scope.getMortuary();
                         $scope.getCabinetsLists();
                         $scope.getApprovedCorpses();
                         $scope.getCorpseServices();
                         $scope.getPendingOutsideCorpses();
         
                          });



        $scope.openModalTime = function (size) {

            var modalInstance = $uibModal.open({
                animation: $scope.animationsEnabled,
                templateUrl: '/views/modules/nursing_care/timepicker.html',
                controller: 'TimePicker',
                size: size,
                resolve: {
                    time: function () {
                        return $scope.mytime;
                    }
                }
            });

            modalInstance.result.then(function (selectedItem) {
                $scope.mytime = selectedItem;
            }, function () {
                //console.log('Modal dismissed at: ' + new Date());
            });
        };

        $scope.toggleAnimation = function () {
            $scope.animationsEnabled = !$scope.animationsEnabled;
        };
		
		var item_list=[];
   $scope.mortuaryGradeSearch=function (item) {
            $http.post('/api/mortuaryGradeSearch',{'search':item}).then(function(data) {
                item_list=data.data;

            });
            return item_list;
        };


 $scope.item_price_registration=function (item_price) {
	 
	  if(angular.isDefined(item_price)==false){
	 return sweetAlert("You must enter all details", "", "error");
      }
	  else if(angular.isDefined(item_price.price)==false){
	 return sweetAlert("You must enter price amount", "", "error");
      }
	 else if(angular.isDefined(item_price.selectedItem.id)==false){
	 return sweetAlert("You must select mortuary class first", "", "error");
      }
	  var prices = [];

                var priceset = {
                    'sub_category_id': 1,
                    'price': item_price.price,
                    'item_id': item_price.selectedItem.id,
                    'facility_id': facility_id,
                    'startingFinancialYear': item_price.startingFinancialYear,
                    'endingFinancialYear': item_price.endingFinancialYear
                };
				
				 prices.push(priceset);
	 $http.post('/api/item_price_registration', prices).then(function (data) {
                 var sending = data.data;
                 var msg = data.data.msg;
		         return sweetAlert(msg, "", "success"); 
              });
       }

				   

  $http.get('/api/getUsermenu/'+user_id ).then(function(data) {
            $scope.menu=data.data;
        });				   
				   
		
 $scope.getReportBasedOnThisDate=function(pef){
			
		 if(angular.isDefined(pef)==false){
	 return sweetAlert("You must select date range", "", "error");
      }
	  
	  var dataToPost={facility_id:facility_id,start_date:pef.start,end_date:pef.end};
		
 $http.post('/api/searchCorpseReports',dataToPost).then(function(data) {  
           $scope.results=data.data[0]; 
           $scope.dischargedResults=data.data[1]; 
		   
 });
		
		};		
		
		
		 
		 
		
		$scope.giveCorpseServices = function(approvedCorpse) {
					 $scope.SelectedPatient=approvedCorpse;
           

 $mdDialog.show({                 
                        controller: function ($scope) {
                                $scope.SelectedPatient=approvedCorpse;
                                
			var postData={facility_id:facility_id,corpse_id:approvedCorpse.corpse_id};
				$http.post('/api/getServicesGiven',postData).then(function(data) {     
                   $scope.getServices=data.data[0];
				   console.log($scope.getServices);
        });
								
                                $scope.cancel = function () {
                                 $mdDialog.hide();
                            };
							
						$http.get('/api/getMortuaryServises').then(function(data) {
         $scope.corpseServices=data.data;

     });	
	 
	     $scope.getSelectedService=function(corpseService){
			 
			 $scope.corpseService=corpseService;
			 
		 };
	 
	 

            $scope.corpseServicez = [];

            $scope.addCorpseServicez = function(qty,corpse) {
 if(angular.isDefined($scope.corpseService)==false){
	 return sweetAlert("You must select service first", "", "error");
      }
	else if(angular.isDefined(qty)==false ){		
		 return sweetAlert("You must Enter quantity", "", "error");
    
	}		
			
                if (corpse.corpse_id == null) {

                    swal("Ooops!! no corpse selected", "Please select corpse first..");
                    return;
                }
                for (var i = 0; i < $scope.corpseServicez.length; i++)
                    if ($scope.corpseServicez[i].id == $scope.corpseService.item_id) {
                swal($scope.corpseService.item_name + ' ' + "already in your wish list!","","info");
                        return;
                    }
                $scope.corpseServicez.push({
					
					 user_id:user_id,
	                 facility_id:facility_id,					 
	                 item_type_id:$scope.corpseService.item_type_id,					 
	                 item_price_id:$scope.corpseService.price_id,					 
	                 status_id:1,					 
	                 quantity:qty,					 
	                 discount:0,					 
	                 discount_by:user_id,					 
	                 patient_category_id:$scope.corpseService.patient_category_id,	 
	                 payment_filter:$scope.corpseService.patient_category_id,			 
	                 item_name:$scope.corpseService.item_name,			 
	                 corpse_id:corpse.corpse_id	
					  });
               console.log($scope.corpseServicez);
	   
            };
	 
	   $scope.removeSelectedService = function(item, corpseServicez) {

                var indexremoveobject = corpseServicez.indexOf(item);

                corpseServicez.splice(indexremoveobject, 1);

            }
	 
							
			 $scope.saveItemServiced=function(dataToPost){
	   if(angular.isDefined(dataToPost)==false){
	 return sweetAlert("You must write frequency of the service given", "", "error");
      }
	  			

                 var dataToPost= {selectedService:dataToPost};			
				 $http.post('/api/saveMortuaryBill',dataToPost).then(function(data) {     
                   if(data.data.status==1){
					   
					var postData={facility_id:facility_id,corpse_id:data.data.corpse_id};
				$http.post('/api/getServicesGiven',postData).then(function(data) {     
                   $scope.getServices=data.data[0];
        });   
		$scope.corpseServicez=null;
                   return sweetAlert(data.data.data, "", "success");
				   
				  	   
        
                   }else if(data.data.status==0){
                    return sweetAlert(data.data.data, "", "error");
                   }
               });	 
	  	   
   };
							
                        },
                        templateUrl: '/views/modules/mortuary/mortuaryServices.html',
                        parent: angular.element(document.body),
                        clickOutsideToClose: false,
                         fullscreen: $scope.customFullscreen // Only for -xs, -sm breakpoints.
                    });

             
		};
		
		$scope.giveGatePass = function(approvedCorpse) {
					 $scope.SelectedPatient=approvedCorpse;
           

 $mdDialog.show({                 
                        controller: function ($scope) {
                                $scope.SelectedPatient=approvedCorpse;
                                
			var postData={facility_id:facility_id,corpse_id:approvedCorpse.corpse_id};
				$http.post('/api/getServicesGiven',postData).then(function(data) {     
                   $scope.getServices=data.data[0];
                   $scope.corpseDetails=data.data[1];
        });
								
                                $scope.cancel = function () {
                                 $mdDialog.hide();
                            };
							
						$http.get('/api/getMortuaryServises').then(function(data) {
         $scope.corpseServices=data.data;

     });	
	 
	 
	 
	   
		 
							
			 $scope.giveDischargePermit=function(permit,corpse){
	   if(angular.isDefined(permit)==false){
	 return sweetAlert("You must write Permit Number and  remarks for this discharge", "", "error");
      }
      if(angular.isDefined(permit.permit_number)==false){
     return sweetAlert("You must write Permit Number  for this discharge", "", "error");
      }

       if(angular.isDefined(permit.remarks)==false){
     return sweetAlert("You must write  remarks for this discharge", "", "error");
      }
	  			

                 var dataToPost= {permit_number:permit.permit_number,descriptions:permit.remarks,permission_status:1,user_id:user_id,facility_id:facility_id,corpse_id:corpse.corpse_id};	
				 
		 $http.post('/api/givePermissionToCorpse',dataToPost).then(function(data) {     
                   if(data.data.status==1){
					   
					var postData={facility_id:facility_id,corpse_id:data.data.corpse_id};
				$http.post('/api/getServicesGiven',postData).then(function(data) {     
                   $scope.getServices=data.data[0];
        });   
		
		return sweetAlert(data.data.data, "", "success");
				   
				  	   
        
                   }else if(data.data.status==0){
                    return sweetAlert(data.data.data, "", "error");
                   }
               });	 
	  	   
   };
							
                        },
                        templateUrl: '/views/modules/mortuary/mortuaryGatePass.html',
                        parent: angular.element(document.body),
                        clickOutsideToClose: false,
                         fullscreen: $scope.customFullscreen // Only for -xs, -sm breakpoints.
                    });

             
		};
		
		
			 $scope.confirmDischarge=function(approvedCorpse){
	  			 var postData={facility_id:facility_id,corpse_id:approvedCorpse.corpse_id};
		
           $http.post('/api/checkIfPermittedDischarge',postData).then(function(data) { 
           console.log(data.data['paybill']) ; 
           if(data.data['paybill'] !='false'){
     return sweetAlert("CORPSE HAS OUTSTANDING BILL PLEASE CLEAR BEFORE DISCHARGE", "", "info");
      }  
				    if(data.data[0][0].permission_status==null ){
	 return sweetAlert("NO PERMISSION GIVEN FOR THIS CORPSE TO BE DISCHARGED", "", "error");
      }else if(data.data[0][0].permission_status==0){
		   return sweetAlert("PERMISSION RESTRICTED FOR THIS CORPSE TO BE DISCHARGED", "", "error");
	  }
	  else if(data.data[0][0].payment_status ==1){
		   return sweetAlert("PAYMENTS NOT COMPLETED FOR THIS CORPSE TO BE DISCHARGED", "", "error");
	  }
	  
	  else{
		 

 $mdDialog.show({                 
                        controller: function ($scope) {
                                $scope.SelectedCorpse=approvedCorpse;
								$scope.corpseDetails=data.data[1];
                               
			$http.get('/api/getUsermenu/'+user_id).then(function(cardTitle){
							$scope.facility_address=cardTitle.data[0];
                              
                           });					
                                $scope.cancel = function () {
                                 $mdDialog.hide();
                            };
							
							$scope.printForm = function () {
            //location.reload();
            var DocumentContainer = document.getElementById('divtoprint');
            var WindowObject = window.open("", "PrintWindow",
                "width=900,height=700,top=50,left=450,toolbars=no,scrollbars=no,status=no,resizable=yes");
            WindowObject.document.title = "PRINT CORPSE DISCHARGE CARD: GoT-HOMIS";
            WindowObject.document.writeln(DocumentContainer.innerHTML);
            WindowObject.document.close();

            setTimeout(function () {
                WindowObject.focus();
                WindowObject.print();
                WindowObject.close();
            }, 0);

        };
									
                        },
                        templateUrl: '/views/modules/mortuary/fomu-maiti.html',
                        parent: angular.element(document.body),
                        clickOutsideToClose: false,
                         fullscreen: $scope.customFullscreen // Only for -xs, -sm breakpoints.
                    });

		
		  
	  }
				   
				   
        });
					 
				
	  	   
   };
		
			
			
			$scope.dischargeRequest = function(approvedCorpse) {
					 $scope.SelectedPatient=approvedCorpse;
		
					

 $mdDialog.show({                 
                        controller: function ($scope) {
                                $scope.SelectedPatient=approvedCorpse;
                               
			var postData={facility_id:facility_id,corpse_id:approvedCorpse.corpse_id};
				$http.post('/api/getServicesGiven',postData).then(function(data) {     
                   $scope.getServices=data.data[0];
        });
								
                                $scope.cancel = function () {
                                 $mdDialog.hide();
                            };
		
							
                        },
                        templateUrl: '/views/modules/mortuary/dischargeStatus.html',
                        parent: angular.element(document.body),
                        clickOutsideToClose: false,
                         fullscreen: $scope.customFullscreen // Only for -xs, -sm breakpoints.
                    });

             
		};
		
		$scope.setTabDischarge = function(newTab){				
            $scope.tab = newTab;
			$http.get('/api/getWards/'+facility_id).then(function(data) {
            $scope.getWards=data.data;
					
            });			
        };
		
		
		
		$scope.setTabMortuaryClass = function(newTab){
            $scope.tab = newTab;
            $scope.getMortuaryClass();
		     };
		


		$scope.setTabMortuaryDisposition= function(newTab){
            $scope.tab = newTab;
             $scope.getPendingCorpses();
            $scope.getPendingOutsideCorpses();
            $scope.getApprovedCorpses();
		     };

		$scope.setTabMortuaryService= function(newTab){
            $scope.tab = newTab;
            $scope.getApprovedCorpses();
            $scope.getCorpseServices();
		     };
         $scope.setTabCorpseDischarge= function(newTab){
            $scope.tab = newTab;
            $scope.getApprovedCorpses();
		     };

        $scope.getDisposedCorpse= function (disposed) {
            $scope.disposedCorpse=disposed;
          }


 $scope.getCorpseServices= function () {
         $http.get('/api/getMortuaryServises').then(function(data) {
         $scope.corpseServices=data.data;

     });
 }

 $scope.addCorpseService= function (item_name,item_id,disposedCorpse) {
     //console.log(disposedCorpse);
     if (angular.isDefined(disposedCorpse)==false) {
         return sweetAlert("Please Select Corpse First", "", "error");
     }
     var dataPost={"item_name":item_name,"service_number":item_id,"corpse_admission_id":disposedCorpse.corpse_admission_id,"user_id":user_id};
     $http.post('/api/addCorpseService',dataPost).then(function(data) {

         if(data.data.status ==0){
             sweetAlert(data.data.data, "", "error");
         }
         else{
                 sweetAlert(data.data.data, "", "success");
         }

     });



 }

		$scope.setTabTreatment = function(newTab){				
            $scope.tab = newTab;
			
			$http.get('/api/getAprovedAdmissionList').then(function(data) {
            $scope.admitted=data.data;
			
            });	
        };
		
		
        $scope.isSet = function(tabNum){
            return $scope.tab === tabNum;
        }
        $scope.oneAtATime = true;
				   		   
						   
						

  $scope.addMortuaryClass= function (mortuary) {

                $scope.dataLoading = true;
	            if (angular.isDefined(mortuary)==false) {
                    $scope.dataLoading = false;
                   return sweetAlert("Enter MORTUARY Class ", "", "error");
                  }else{
	$http.post('/api/addMortuaryClass',{"mortuary_class":mortuary.class}).then(function(data){
			 if(data.data.status ==0){
								 sweetAlert(data.data.data, "", "error");
				  }else{
                     mortuary.class = null;
                    sweetAlert(data.data.data, "", "success");
			          }
			          }).finally(function () {
        $scope.dataLoading = false;
    });

                }
  }


        $scope.addTimesQue= function (PreMedication,selectedPatient) {
	             if (angular.isDefined(PreMedication)==false) {
                   return sweetAlert("Please Enter Pre-Medication ", "", "error");
                 }
                 else{
	             var PreMedicationTime=PreMedication.time+" "+PreMedication.time_day;
	             var information_category="PRE MEDICATION";
	             var dataPost={"erasor":0,"noted_value":PreMedicationTime,"admission_id":selectedPatient.admission_id,"request_id":selectedPatient.id,
                         "remarks":PreMedication.remarks,"information_category":information_category,"nurse_id":user_id};
                 $http.post('/api/addTimesQue',dataPost).then(function(data) {
			     if(data.data.status ==0){
					 sweetAlert(data.data.data, "", "error");
				  }
			      else{
			      	$scope.PreMedication = null;
                    sweetAlert(data.data.data, "", "success");
				  }});
                  }
        }





        $scope.PreOPCondition= function (PreOPCondition,selectedPatient) {
	             if (angular.isDefined(PreOPCondition)==false) {
                   return sweetAlert("Please Enter Pre Operation Condition", "", "error");
                 }
                 else if (angular.isDefined(selectedPatient)==false) {
                   return sweetAlert("Please Select Patient Before Proceed", "", "error");
                 }

                 else{

	             var information_category="PRE-OPERTION CONDITION";
	             var dataPost={"erasor":0,"noted_value":PreOPCondition.condition,"admission_id":selectedPatient.admission_id,"request_id":selectedPatient.id,
                         "remarks":PreOPCondition.case,"information_category":information_category,"nurse_id":user_id};
                 $http.post('/api/addTimesQue',dataPost).then(function(data) {
			     if(data.data.status ==0){
					 sweetAlert(data.data.data, "", "error");
				  }
			      else{
			      	$scope.PreOPCondition = null;
                    sweetAlert(data.data.data, "", "success");
				  }});
                  }
        }



        $scope.anaesthBloodLoss= function (pulse_rate,selectedPatient) {
	             if (angular.isDefined(pulse_rate)==false) {
                   return sweetAlert("Please Enter AMOUNT OF BLOOD COLLECTED", "", "error");
                 }
                 else if (angular.isDefined(selectedPatient)==false) {
                   return sweetAlert("Please Select Patient Before Proceed", "", "error");
                 }

                 else{

	             var information_category="BLOOD LOSS";
	             var dataPost={"erasor":0,"noted_value":pulse_rate.noted_amount,"admission_id":selectedPatient.admission_id,"request_id":selectedPatient.id,
                         "remarks":pulse_rate.noted_amount,"information_category":information_category,"nurse_id":user_id};
                 $http.post('/api/addTimesQue',dataPost).then(function(data) {
			     if(data.data.status ==0){
					 sweetAlert(data.data.data, "", "error");
				  }
			      else{
			      	$scope.PreOPCondition = null;
                    sweetAlert(data.data.data, "", "success");
				  }});
                  }
        }

        $scope.anaesthFluidGiven= function (pulse_rate,selectedPatient) {
	             if (angular.isDefined(pulse_rate)==false) {
                   return sweetAlert("Please Enter AMOUNT OF FLUID GIVEN", "", "error");
                 }
                 else if (angular.isDefined(selectedPatient)==false) {
                   return sweetAlert("Please Select Patient Before Proceed", "", "error");
                 }

                 else{

	             var information_category="FLUID GIVEN";
	             var dataPost={"erasor":0,"noted_value":pulse_rate.noted_amount_fluid,"admission_id":selectedPatient.admission_id,"request_id":selectedPatient.id,
                         "remarks":pulse_rate.noted_amount_fluid,"information_category":information_category,"nurse_id":user_id};
                 $http.post('/api/addTimesQue',dataPost).then(function(data) {
			     if(data.data.status ==0){
					 sweetAlert(data.data.data, "", "error");
				  }
			      else{
			      	$scope.PreOPCondition = null;
                    sweetAlert(data.data.data, "", "success");
				  }});
                  }
        }


 $scope.anaesthUrineOutput= function (pulse_rate,selectedPatient) {
	             if (angular.isDefined(pulse_rate)==false) {
                   return sweetAlert("Please Enter AMOUNT OF URINE OUTPUT", "", "error");
                 }
                 else if (angular.isDefined(selectedPatient)==false) {
                   return sweetAlert("Please Select Patient Before Proceed", "", "error");
                 }

                 else{

	             var information_category="URINE OUTPUT";
	             var dataPost={"erasor":0,"noted_value":pulse_rate.urine_output,"admission_id":selectedPatient.admission_id,"request_id":selectedPatient.id,
                         "remarks":pulse_rate.urine_output,"information_category":information_category,"nurse_id":user_id};
                 $http.post('/api/addTimesQue',dataPost).then(function(data) {
			     if(data.data.status ==0){
					 sweetAlert(data.data.data, "", "error");
				  }
			      else{
                     pulse_rate.urine_output = null;
                    sweetAlert(data.data.data, "", "success");
				  }});
                  }
        }


 $scope.anaesthComplications= function (pulse_rate,selectedPatient) {
	             if (angular.isDefined(pulse_rate)==false) {
                   return sweetAlert("Write any Compilcations found during Operations", "", "error");
                 }
                 else if (angular.isDefined(selectedPatient)==false) {
                   return sweetAlert("Please Select Patient Before Proceed", "", "error");
                 }
                 else{
	             var information_category="COMPLICATIONS";
	             var dataPost={"erasor":0,"noted_value":pulse_rate.complications,"admission_id":selectedPatient.admission_id,"request_id":selectedPatient.id,
                         "remarks":pulse_rate.complications,"information_category":information_category,"nurse_id":user_id};
                 $http.post('/api/addTimesQue',dataPost).then(function(data) {
			     if(data.data.status ==0){
					 sweetAlert(data.data.data, "", "error");
				  }
			      else{
                     pulse_rate.complications = null;
                    sweetAlert(data.data.data, "", "success");
				  }});
                  }
        }

        $scope.anaesthPulseRate= function (pulse_rate,selectedPatient) {
	             if (angular.isDefined(pulse_rate)==false) {
                   return sweetAlert("Please Enter Pulse Rate Associating Info", "", "error");
                 }
                 else if (angular.isDefined(selectedPatient)==false) {
                   return sweetAlert("Please Select Patient Before Proceed", "", "error");
                 }
                 else if (pulse_rate.am_pm==null) {
                     return sweetAlert("IS IT AM/PM ? ", "", "error");
                 }

                 else if (pulse_rate.hr==null) {
                     return sweetAlert("HOURS MISSED ", "", "error");
	             }
	             else if (pulse_rate.mins==null) {
                     return sweetAlert("MINUTES MISSED ", "", "error");
	             }
	             else if (pulse_rate.mins.length !=2) {
                     return sweetAlert("MINUTES MUST BE IN TWO DIGITS ", "", "error");
	             }
	             else if (pulse_rate.mins >=60) {
                     return sweetAlert("MINUTES MUST BE LESS THAN 60 ", "", "error");
	             }

	             else if (pulse_rate.mins < 0) {
                     return sweetAlert("MINUTES MUST BE GREATER THAN 0 ", "", "error");
	             }
                else if (pulse_rate.read==null) {
                     return sweetAlert("EMPTY VALUE IN READ BOX  ", "", "error");
	             }


                 else{

	             var information_category="PULSE RATE";

	             var dataPost={"erasor":0,"noted_value":pulse_rate.read,"admission_id":selectedPatient.admission_id,"request_id":selectedPatient.id,
                         "am_pm":pulse_rate.am_pm,"mins":pulse_rate.mins,"hr":pulse_rate.hr,"information_category":information_category,"nurse_id":user_id};
                 $http.post('/api/addPrBp',dataPost).then(function(data) {
			     if(data.data.status ==0){
					 sweetAlert(data.data.data, "", "error");
				  }
			      else{
			      	$scope.PreOPCondition = null;
                    sweetAlert(data.data.data, "", "success");
				  }});
                  }
        }


        $scope.anaesthSystolic= function (pulse_rate,selectedPatient) {
	             if (angular.isDefined(pulse_rate)==false) {
                   return sweetAlert("Please Enter Pulse Rate Associating Info", "", "error");
                 }
                 else if (angular.isDefined(selectedPatient)==false) {
                   return sweetAlert("Please Select Patient Before Proceed", "", "error");
                 }
                 else if (pulse_rate.am_pm==null) {
                     return sweetAlert("IS IT AM/PM ? ", "", "error");
                 }

                 else if (pulse_rate.hr==null) {
                     return sweetAlert("HOURS MISSED ", "", "error");
	             }
	             else if (pulse_rate.mins==null) {
                     return sweetAlert("MINUTES MISSED ", "", "error");
	             }
	             else if (pulse_rate.mins.length !=2) {
                     return sweetAlert("MINUTES MUST BE IN TWO DIGITS ", "", "error");
	             }
	             else if (pulse_rate.mins >=60) {
                     return sweetAlert("MINUTES MUST BE LESS THAN 60 ", "", "error");
	             }

	             else if (pulse_rate.mins < 0) {
                     return sweetAlert("MINUTES MUST BE GREATER THAN 0 ", "", "error");
	             }
                else if (pulse_rate.read==null) {
                     return sweetAlert("EMPTY VALUE IN READ BOX  ", "", "error");
	             }


                 else{

	             var information_category="SYSTOLIC PRESSURE";

	             var dataPost={"erasor":0,"noted_value":pulse_rate.read,"admission_id":selectedPatient.admission_id,"request_id":selectedPatient.id,
                         "am_pm":pulse_rate.am_pm,"mins":pulse_rate.mins,"hr":pulse_rate.hr,"information_category":information_category,"nurse_id":user_id};
                 $http.post('/api/addPrBp',dataPost).then(function(data) {
			     if(data.data.status ==0){
					 sweetAlert(data.data.data, "", "error");
				  }
			      else{
			      	pulse_rate.read = null;
                    sweetAlert(data.data.data, "", "success");
				  }});
                  }
        }

        $scope.anaesthDiastolic= function (pulse_rate,selectedPatient) {
	             if (angular.isDefined(pulse_rate)==false) {
                   return sweetAlert("Please Enter Pulse Rate Associating Info", "", "error");
                 }
                 else if (angular.isDefined(selectedPatient)==false) {
                   return sweetAlert("Please Select Patient Before Proceed", "", "error");
                 }
                 else if (pulse_rate.am_pm==null) {
                     return sweetAlert("IS IT AM/PM ? ", "", "error");
                 }

                 else if (pulse_rate.hr==null) {
                     return sweetAlert("HOURS MISSED ", "", "error");
	             }
	             else if (pulse_rate.mins==null) {
                     return sweetAlert("MINUTES MISSED ", "", "error");
	             }
	             else if (pulse_rate.mins.length !=2) {
                     return sweetAlert("MINUTES MUST BE IN TWO DIGITS ", "", "error");
	             }
	             else if (pulse_rate.mins >=60) {
                     return sweetAlert("MINUTES MUST BE LESS THAN 60 ", "", "error");
	             }

	             else if (pulse_rate.mins < 0) {
                     return sweetAlert("MINUTES MUST BE GREATER THAN 0 ", "", "error");
	             }
                else if (pulse_rate.read_diastolic==null) {
                     return sweetAlert("EMPTY VALUE IN READ BOX  ", "", "error");
	             }


                 else{

	             var information_category="DIASTOLIC PRESSURE";

	             var dataPost={"erasor":0,"noted_value":pulse_rate.read_diastolic,"admission_id":selectedPatient.admission_id,"request_id":selectedPatient.id,
                         "am_pm":pulse_rate.am_pm,"mins":pulse_rate.mins,"hr":pulse_rate.hr,"information_category":information_category,"nurse_id":user_id};
                 $http.post('/api/addPrBp',dataPost).then(function(data) {
			     if(data.data.status ==0){
					 sweetAlert(data.data.data, "", "error");
				  }
			      else{
			      	pulse_rate.read_diastolic = null;
                    sweetAlert(data.data.data, "", "success");
				  }});
                  }
        }


        $scope.anaesthIntubation= function (Intubation,selectedPatient) {
	             if (angular.isDefined(Intubation)==false) {
                   return sweetAlert("Please Enter INTUBATION INFO", "", "error");
                 }
                 else if (angular.isDefined(selectedPatient)==false) {
                   return sweetAlert("Please Select Patient Before Proceed", "", "error");
                 }

                 else{

	             var information_category="INTUBATION";
	             var dataPost={"erasor":0,"noted_value":Intubation.condition,"admission_id":selectedPatient.admission_id,"request_id":selectedPatient.id,
                         "remarks":Intubation.condition,"information_category":information_category,"nurse_id":user_id};
                 $http.post('/api/addTimesQue',dataPost).then(function(data) {
			     if(data.data.status ==0){
					 sweetAlert(data.data.data, "", "error");
				  }
			      else{
			      	$scope.PreOPCondition = null;
                    sweetAlert(data.data.data, "", "success");
				  }});
                  }
        }


        $scope.anaesthRespiration= function (Respiration,selectedPatient) {
	             if (angular.isDefined(Respiration)==false) {
                   return sweetAlert("Please Enter RESPIRATION INFO", "", "error");
                 }
                 else if (angular.isDefined(selectedPatient)==false) {
                   return sweetAlert("Please Select Patient Before Proceed", "", "error");
                 }

                 else{
	             var information_category="RESPIRATION";
	             var dataPost={"erasor":0,"noted_value":Respiration.condition,"admission_id":selectedPatient.admission_id,"request_id":selectedPatient.id,
                         "remarks":Respiration.condition,"information_category":information_category,"nurse_id":user_id};
                 $http.post('/api/addTimesQue',dataPost).then(function(data) {
			     if(data.data.status ==0){
					 sweetAlert(data.data.data, "", "error");
				  }
			      else{
			      	$scope.Respiration = null;
                    sweetAlert(data.data.data, "", "success");
				  }});
                  }
        }

        $scope.anaesthLocal= function (Local,selectedPatient) {
	             if (angular.isDefined(Local)==false) {
                   return sweetAlert("Please Enter LOCAL INFO", "", "error");
                 }
                 else if (angular.isDefined(selectedPatient)==false) {
                   return sweetAlert("Please Select Patient Before Proceed", "", "error");
                 }

                 else{
	             var information_category="LOCAL";
	             var dataPost={"erasor":0,"noted_value":Local.condition,"admission_id":selectedPatient.admission_id,"request_id":selectedPatient.id,
                         "remarks":Local.condition,"information_category":information_category,"nurse_id":user_id};
                 $http.post('/api/addTimesQue',dataPost).then(function(data) {
			     if(data.data.status ==0){
					 sweetAlert(data.data.data, "", "error");
				  }
			      else{
			      	$scope.Respiration = null;
                    sweetAlert(data.data.data, "", "success");
				  }});
                  }
        }

        $scope.anaesthPosition= function (Position,selectedPatient) {
	             if (angular.isDefined(Position)==false) {
                   return sweetAlert("Please Enter POSITION INFO", "", "error");
                 }
                 else if (angular.isDefined(selectedPatient)==false) {
                   return sweetAlert("Please Select Patient Before Proceed", "", "error");
                 }

                 else{
	             var information_category="POSITION";
	             var dataPost={"erasor":0,"noted_value":Position.condition,"admission_id":selectedPatient.admission_id,"request_id":selectedPatient.id,
                         "remarks":Position.condition,"information_category":information_category,"nurse_id":user_id};
                 $http.post('/api/addTimesQue',dataPost).then(function(data) {
			     if(data.data.status ==0){
					 sweetAlert(data.data.data, "", "error");
				  }
			      else{
			      	$scope.Respiration = null;
                    sweetAlert(data.data.data, "", "success");
				  }});
                  }
        }


        $scope.anaesthNeedle= function (Needle,selectedPatient) {
	             if (angular.isDefined(Needle)==false) {
                   return sweetAlert("Please Enter NEEDLE INFO", "", "error");
                 }
                 else if (angular.isDefined(selectedPatient)==false) {
                   return sweetAlert("Please Select Patient Before Proceed", "", "error");
                 }

                 else{
	             var information_category="NEEDLE";
	             var dataPost={"erasor":0,"noted_value":Needle.condition,"admission_id":selectedPatient.admission_id,"request_id":selectedPatient.id,
                         "remarks":Needle.condition,"information_category":information_category,"nurse_id":user_id};
                 $http.post('/api/addTimesQue',dataPost).then(function(data) {
			     if(data.data.status ==0){
					 sweetAlert(data.data.data, "", "error");
				  }
			      else{
			      	$scope.Needle = null;
                    sweetAlert(data.data.data, "", "success");
				  }});
                  }
        }

        $scope.anaesthEffect= function (Effect,selectedPatient) {
	             if (angular.isDefined(Effect)==false) {
                   return sweetAlert("Please Enter NEEDLE INFO", "", "error");
                 }
                 else if (angular.isDefined(selectedPatient)==false) {
                   return sweetAlert("Please Select Patient Before Proceed", "", "error");
                 }

                 else{
	             var information_category="EFFECT";
	             var dataPost={"erasor":0,"noted_value":Effect.condition,"admission_id":selectedPatient.admission_id,"request_id":selectedPatient.id,
                         "remarks":Effect.condition,"information_category":information_category,"nurse_id":user_id};
                 $http.post('/api/addTimesQue',dataPost).then(function(data) {
			     if(data.data.status ==0){
					 sweetAlert(data.data.data, "", "error");
				  }
			      else{
			      	$scope.Effect = null;
                    sweetAlert(data.data.data, "", "success");
				  }});
                  }
        }

		$scope.saveHb= function (hb,selectedPatient) {
	   //var ward_type=wards.ward_type;
	             if (angular.isDefined(hb)==false) {
                   return sweetAlert("Please Enter Laboratory Status", "", "error");
                  } 
				  else if (angular.isDefined(selectedPatient)==false) {
                   return sweetAlert("Please Enter Patient Selected", "", "error");
                  } else{
	             	var information_category='LABORATORY STATUS';
	             	var dataToPost={"erasor":0,"admission_id":selectedPatient.admission_id,"request_id":selectedPatient.id,
						"value_noted":hb.laboratory,"information_category":information_category,"nurse_id":user_id};
                     $http.post('/api/saveStatusAnaesthetic',dataToPost).then(function(data) {
           
			 if(data.data.status ==0){

					 sweetAlert(data.data.data, "", "error");
				  }else{
                     hb.laboratory = null;
                    sweetAlert(data.data.data, "", "success");            				  					
					 
							
				  }
			
			
			
			
			          });  
					  
				  } 
				           
			
        }


        $scope.savePreAnaestheticOrder= function (hb,selectedPatient) {
	   //var ward_type=wards.ward_type;
	             if (angular.isDefined(hb)==false) {
                   return sweetAlert("Please Enter PRE ANAESTHETIC ORDER", "", "error");
                  }
				  else if (angular.isDefined(selectedPatient)==false) {
                   return sweetAlert("Please Enter Patient Selected", "", "error");
                  } else{
	             	var information_category='PRE ANAESTHETIC ORDER';
	             	var dataToPost={"erasor":0,"admission_id":selectedPatient.admission_id,"request_id":selectedPatient.id,
						"value_noted":hb.pre_anaesthetic_order,"information_category":information_category,"nurse_id":user_id};
                     $http.post('/api/saveStatusAnaesthetic',dataToPost).then(function(data) {

			 if(data.data.status ==0){

					 sweetAlert(data.data.data, "", "error");
				  }else{
				    hb.pre_anaesthetic_order = null;
                    sweetAlert(data.data.data, "", "success");


				  }




			          });

				  }


        }


        $scope.saveAnaestheticTechniques= function (hb,selectedPatient) {
	   //var ward_type=wards.ward_type;
	             if (angular.isDefined(hb)==false) {
                   return sweetAlert("Please Enter ANAESTHETIC TECHNIQUES", "", "error");
                  }
				  else if (angular.isDefined(selectedPatient)==false) {
                   return sweetAlert("Please Enter Patient Selected", "", "error");
                  } else{
	             	var information_category='ANAESTHETIC TECHNIQUES';
	             	var dataToPost={"erasor":0,"admission_id":selectedPatient.admission_id,"request_id":selectedPatient.id,
						"value_noted":hb.anaesthetic_technique,"information_category":information_category,"nurse_id":user_id};
                     $http.post('/api/saveStatusAnaesthetic',dataToPost).then(function(data) {

			 if(data.data.status ==0){

					 sweetAlert(data.data.data, "", "error");
				  }else{
				    hb.anaesthetic_technique = null;
                    sweetAlert(data.data.data, "", "success");


				  }




			          });

				  }


        }

        $scope.savePhysicalStatus= function (hb,selectedPatient) {
	   //var ward_type=wards.ward_type;
	             if (angular.isDefined(hb)==false) {
                   return sweetAlert("Please Enter Physical Status", "", "error");
                  }
				  else if (angular.isDefined(selectedPatient)==false) {
                   return sweetAlert("Please Enter Patient Selected", "", "error");
                  } else{
	             	//console.log(hb);
	             	var information_category='PHYSICAL STATUS';
	             	var dataToPost={"erasor":0,"admission_id":selectedPatient.admission_id,"request_id":selectedPatient.id,
						"value_noted":hb.physical_status,"information_category":information_category,"nurse_id":user_id};
                     $http.post('/api/saveStatusAnaesthetic',dataToPost).then(function(data) {

			 if(data.data.status ==0){

					 sweetAlert(data.data.data, "", "error");
				  }else{
                     hb.laboratory = null;
                    sweetAlert(data.data.data, "", "success");


				  }




			          });

				  }


        }

        $scope.saveOral= function (hb,selectedPatient) {
	   //var ward_type=wards.ward_type;
	             if (angular.isDefined(hb)==false) {
                   return sweetAlert("Please Enter LAST ORAL INTAKE", "", "error");
                  }
				  else if (angular.isDefined(selectedPatient)==false) {
                   return sweetAlert("Please Enter Patient Selected", "", "error");
                  } else{
	             	//console.log(hb);
	             	var information_category='LAST ORAL INTAKE';
	             	var dataToPost={"erasor":0,"admission_id":selectedPatient.admission_id,"request_id":selectedPatient.id,
						"value_noted":hb.oral,"information_category":information_category,"nurse_id":user_id};
                     $http.post('/api/saveStatusAnaesthetic',dataToPost).then(function(data) {

			 if(data.data.status ==0){

					 sweetAlert(data.data.data, "", "error");
				  }else{
                     hb.oral = null;
                    sweetAlert(data.data.data, "", "success");


				  }




			          });

				  }


        }

        $scope.saveNutritional= function (hb,selectedPatient) {
	   //var ward_type=wards.ward_type;
	             if (angular.isDefined(hb)==false) {
                   return sweetAlert("Please Enter NUTRITIONAL STATUS", "", "error");
                  }
				  else if (angular.isDefined(selectedPatient)==false) {
                   return sweetAlert("Please Enter Patient Selected", "", "error");
                  } else{
	             	//console.log(hb);
	             	var information_category='NUTRITIONAL STATUS';
	             	var dataToPost={"erasor":0,"admission_id":selectedPatient.admission_id,"request_id":selectedPatient.id,
						"value_noted":hb.nutrional,"information_category":information_category,"nurse_id":user_id};
                     $http.post('/api/saveStatusAnaesthetic',dataToPost).then(function(data) {

			 if(data.data.status ==0){

					 sweetAlert(data.data.data, "", "error");
				  }else{
                    hb.nutrional = null;
                    sweetAlert(data.data.data, "", "success");


				  }




			          });

				  }


        }
			

		
		var mortuary_classes=[];
		$scope.showSearchMortuaryClass= function (searchKey) {
				if( searchKey.length > 3){
				
            $http.get('/api/getMortuaryClasses/'+searchKey).then(function(data) {
                mortuary_classes=data.data;
			
            });
			return mortuary_classes;
				}
        }	
			  

			  var beds=[];
		$scope.showSearchBedTypes= function (searchKey) {				
            $http.get('/api/searchBedTypes/'+searchKey).then(function(data) {
            beds=data.data;			
            });
			return beds;
        }	
		
		
		$scope.getPendingCorpses= function () {
			$http.get('/api/getPendingCorpses/'+facility_id).then(function(data) {
            $scope.pendingCorpses=data.data;

                //console.log($scope.pendingCorpses);
          			
            });
        }

        $scope.getPendingOutsideCorpses= function () {
			    var dataPost={facility_id:facility_id};
			$http.post('/api/getListOfCorpsesToStore',dataPost).then(function(data) {
            $scope.PendingOutsideCorpses=data.data;
             });
        }




        $scope.giveCorpseCabinet= function (getPendingOutside) {
            
			  $mdDialog.show({                 
                        controller: function ($scope) {
							 $scope.SelectedCorpse=getPendingOutside;
							 console.log($scope.SelectedCorpse);
							 
							 $scope.getCabinetsLists= function () {
			$http.get('/api/getCabinetsLists/'+facility_id).then(function(data) {
            $scope.cabinetsLists=data.data;
              });
		}
		
		$scope.giveCabinetCorpse= function (cabinet,SelectedCorpse) {
			var dataPost={corpse_admission_id:SelectedCorpse.id,
			              cabinet_id:cabinet.cabinet_id};
			$http.post('/api/giveCabinetCorpse',dataPost).then(function(data) {
				$scope.cancel();
				 $scope.getPendingOutsideCorpses= function () {
			    var dataPost={facility_id:facility_id};
			$http.post('/api/getListOfCorpsesToStore',dataPost).then(function(data) {
            $scope.PendingOutsideCorpses=data.data;
             });
        }
				$scope.getPendingOutsideCorpses();
                sweetAlert(data.data.data, "", "success");

              });
		}
	        
                                   $scope.cancel = function () {
                                 $mdDialog.hide();
                            };
                        },
                        templateUrl: '/views/modules/mortuary/cabinets_list.html',
                        parent: angular.element(document.body),
                        clickOutsideToClose: false,
                         fullscreen: $scope.customFullscreen // Only for -xs, -sm breakpoints.
                    }); 
			
			       

        }






        $scope.getApprovedCorpses= function () {
			$http.get('/api/getApprovedCorpses/'+facility_id).then(function(data) {
            $scope.approvedCorpses=data.data;

            });
        }

		$scope.getVital= function () {
			$http.post('/api/getVital').then(function(data) {
            $scope.getVitals=data.data;
            ////console.log($getVitals);			
            });
			
        }
		
		
		
		$scope.saveAssociateHistory= function (associate_history,selectedPatient) {
			 if(angular.isDefined(selectedPatient)==false){
					  
					  return sweetAlert("Please Select the Patient From list on the Left panel", "", "error");
				  }
				  else if(angular.isDefined(associate_history)==false){
					  
					  return sweetAlert("Please Enter History Records or NILL", "", "error");
				  }
			var admission_id=selectedPatient.admission_id;
			var information_category='ASSOCIATE HISTORY';
			var associateHistory={"erasor":0,"admission_id":selectedPatient.admission_id,"request_id":selectedPatient.id,
				"medical":associate_history.medical,"surgical":associate_history.surgical,
				"anaesthetic":associate_history.anaesthetic,"information_category":information_category,"nurse_id":user_id};
			$http.post('/api/saveAssociateHistory',associateHistory).then(function(data) {
            
          if(data.data.status ==0){
					 
					 sweetAlert(data.data.data, "", "error");
				  }else{
					  
                    sweetAlert(data.data.data, "", "success");		 				
				  }			
            });
			
        }
		
		$scope.savePastHistory= function (past_history,selectedPatient) {
			 if(angular.isDefined(selectedPatient)==false){
					  
					  return sweetAlert("Please Select the Patient From list on the Left panel", "", "error");
				  }
				  else if(angular.isDefined(past_history)==false){
					  
					  return sweetAlert("Please Enter History Records or NILL", "", "error");
				  }
			var admission_id=selectedPatient.admission_id;
			var information_category='PAST HISTORY';
			var associateHistory={"erasor":0,"admission_id":selectedPatient.admission_id,"request_id":selectedPatient.id,"medical":past_history.medical,"surgical":past_history.surgical,"anaesthetic":past_history.anaesthetic,"information_category":information_category,"nurse_id":user_id};
			$http.post('/api/savePastHistory',associateHistory).then(function(data) {
            
          if(data.data.status ==0){
					 
					 sweetAlert(data.data.data, "", "error");
				  }else{
					  
                    sweetAlert(data.data.data, "", "success");		 				
				  }			
            });
			
        }
		
		
		$scope.saveSocialSurgery= function (social,selectedPatient) {
			 if(angular.isDefined(selectedPatient)==false){
					  
					  return sweetAlert("Please Select the Patient From list on the Left panel", "", "error");
				  }
				  else if(angular.isDefined(social)==false){
					  
					  return sweetAlert("Please Enter Social History Records or NILL", "", "error");
				  }
			var admission_id=selectedPatient.admission_id;
			var information_category='SOCIAL AND FAMILY HISTORY';
			var socialHistory={"erasor":0,"admission_id":selectedPatient.admission_id,"request_id":selectedPatient.id,"chronic_illness":social.chronic_illness,"substance_abuse":social.substance_abuse,"adoption":social.adoption,"others":social.others,"other_information":information_category,"nurse_id":user_id};
			$http.post('/api/saveSocialHistory',socialHistory).then(function(data) {
            
          if(data.data.status ==0){
					 
					 sweetAlert(data.data.data, "", "error");
				  }else{
					  
                    sweetAlert(data.data.data, "", "success");		 				
				  }			
            });
			
        }
		
		$scope.saveRespiratoryExaminations= function (selectedPatient) {
			 if(angular.isDefined(selectedPatient)==false){					  
return sweetAlert("Select the Patient Before Adding Respiratory System", "", "error");
				  }
				  else{
				  
			$scope.selectedPatient=selectedPatient;
			 //console.log($scope.selectedPatient);
			 
			 ////console.log(beds_number);			  
             var object =$scope.selectedPatient; 
             ////console.log(beds_number);				 
			 var modalInstance = $uibModal.open({
				  templateUrl: '/views/modules/nursing_care/physical_examinations.html',
				  size: 'lg',
				  animation: true,
				  controller: 'physicalExaminations',
				  resolve:{
                  object: function () {
					         return object;
                  }
                  }				  
                  });
			
			
			
				  }
			
        }
		
		$scope.saveDentalStatus= function (tooth_id,tooth_number,selectedPatient) {
			 if(angular.isDefined(selectedPatient)==false){					  
return sweetAlert("Select the Patient Before ENTERING DENTAL STATUS", "", "error");
				  }
				  else{
				  
			$scope.selectedPatient=selectedPatient;
			 //console.log($scope.selectedPatient);
			 
			  $scope.dentals={"tooth_id":tooth_id,"tooth_number":tooth_number};
			 var object =angular.extend($scope.selectedPatient,$scope.dentals); 
             //console.log(object);				 
             //console.log($scope.dentals);				 
			 var modalInstance = $uibModal.open({
				  templateUrl: '/views/modules/nursing_care/dental_status.html',
				  size: 'lg',
				  animation: true,
				  controller: 'physicalExaminations',
				  resolve:{
                  object: function () {
					         return object;
                  }
                  }				  
                  });
			
			
			
				  }
			
        }
		
		$scope.saveCardivascularExaminations= function (selectedPatient) {
			 if(angular.isDefined(selectedPatient)==false){					  
return sweetAlert("Select the Patient Before Adding Cardivascular System", "", "error");
				  }
				  else{
				  
			$scope.selectedPatient=selectedPatient;
			 //console.log($scope.selectedPatient);
			 
			 ////console.log(beds_number);			  
             var object =$scope.selectedPatient; 
             ////console.log(beds_number);				 
			 var modalInstance = $uibModal.open({
				  templateUrl: '/views/modules/nursing_care/cardivascular_examinations.html',
				  size: 'lg',
				  animation: true,
				  controller: 'physicalExaminations',
				  resolve:{
                  object: function () {
					         return object;
                  }
                  }				  
                  });
			
			
			
				  }
			
        }	


		$scope.saveGastroIntestineExaminations= function (selectedPatient) {
			 if(angular.isDefined(selectedPatient)==false){					  
return sweetAlert("Select the Patient Before Adding Gastro Intestine System", "", "error");
				  }
				  else{
				  
			$scope.selectedPatient=selectedPatient;
			 //console.log($scope.selectedPatient);
			 
			 ////console.log(beds_number);			  
             var object =$scope.selectedPatient; 
             ////console.log(beds_number);				 
			 var modalInstance = $uibModal.open({
				  templateUrl: '/views/modules/nursing_care/gastrointestine.html',
				  size: 'lg',
				  animation: true,
				  controller: 'physicalExaminations',
				  resolve:{
                  object: function () {
					         return object;
                  }
                  }				  
                  });
			
			
			
				  }
			
        }
		
		$scope.saveCentralNervousSystem= function (selectedPatient) {
			 if(angular.isDefined(selectedPatient)==false){					  
return sweetAlert("Select the Patient Before Adding CENTRAL NERVOUS SYSTEM", "", "error");
				  }
				  else{
				  
			$scope.selectedPatient=selectedPatient;
			 //console.log($scope.selectedPatient);
			 
			 ////console.log(beds_number);			  
             var object =$scope.selectedPatient; 
             ////console.log(beds_number);				 
			 var modalInstance = $uibModal.open({
				  templateUrl: '/views/modules/nursing_care/centralNervousSystem.html',
				  size: 'lg',
				  animation: true,
				  controller: 'physicalExaminations',
				  resolve:{
                  object: function () {
					         return object;
                  }
                  }				  
                  });
			
			
			
				  }
			
        }
		
		$scope.getDiagnosis= function () {
			$http.get('/api/getDiagnosis').then(function(data) {
            $scope.getDiagnosises=data.data;
            ////console.log($getVitals);			
            });
			
        }
		
		$scope.getTeeth= function (selectedPatient) {
						
			$http.get('/api/getTeethAbove').then(function(data) {
            $scope.teethAboves=data.data;
			$http.get('/api/getTeethBelow').then(function(data) {
            $scope.teethBelows=data.data;
			//console.log($scope.teethBelows);
		});
			
			         });
					 
			if(angular.isDefined(selectedPatient)==false){					  
       return sweetAlert("Select the Patient Before SETTING DENTAL STATUS", "", "error");
			}else{
				var request_id=selectedPatient.id;
			$http.get('/api/getTeethStatusFromPatientAbove/'+request_id).then(function(data) {
            $scope.teeth_patientsAboves=data.data;
					
            });
			
			$http.get('/api/getTeethStatusFromPatientBelow/'+request_id).then(function(data) {
            $scope.teeth_patientsBelows=data.data;
			 			
            });
				
			}
			
			
			
			
        }
		
		
		
		$scope.getDrugs= function () {
			$http.get('/api/getDrugs').then(function(data) {
            $scope.getDrugs=data.data;
            ////console.log($getVitals);			
            });
			
        }

	$scope.getCabinetsLists= function () {
			$http.get('/api/getCabinetsLists/'+facility_id).then(function(data) {
            $scope.cabinetsLists=data.data;
              });
		}
		
		$scope.getCabinetsFromThisMortuary= function (mortuary) {
			
			 $mdDialog.show({                 
                        controller: function ($scope) {
							$scope.addCabinet= function (cabinets,mortuary_id) {
	             if (angular.isDefined(cabinets)==false) {
                   return sweetAlert("Please Enter CABINET NAME ", "", "error");
                  }	   
				   else if (angular.isDefined(mortuary_id)==false) {
                   return sweetAlert("Selected Mortuary Flushed in the Local storage Please Restart ", "", "error");
                   }
				  else{
	                 $http.post('/api/saveCabinets',{"cabinet_name":cabinets.cabinet_number,"capacity":cabinets.capacity,"mortuary_id":mortuary_id,"user_id":user_id,"eraser":1}).then(function(data) {
			 if(data.data.status ==0){
					 sweetAlert(data.data.data, "", "error");
				  }else{               
			$http.get('/api/getCabinetsPerMortuary/'+mortuary_id).then(function(data) {
            $scope.cabinetsLists=data.data;
			   });
					    sweetAlert(data.data.data, "", "success");
					 
				  }
					          });
					  
				  } 

        };
							
							
                                 $scope.cancel = function () {
                                 $mdDialog.hide();
                            };
							
							$scope.mortuary_name=mortuary.mortuary_name;
							var mortuary_id=mortuary.id;
							$scope.mortuary_id=mortuary.id;
			$http.get('/api/getCabinetsPerMortuary/'+mortuary_id).then(function(data) {
            $scope.cabinetsLists=data.data;
			   });
							
							
							  $http.get('/api/getUsermenu/'+user_id).then(function(cardTitle){
							$scope.facility_address=cardTitle.data[0];
                              
                           });
                        },
                        templateUrl: '/views/modules/mortuary/cabinets.html',
                        parent: angular.element(document.body),
                        clickOutsideToClose: false,
                         fullscreen: $scope.customFullscreen // Only for -xs, -sm breakpoints.
                    });  
			  
			  
			  
		}

		$scope.getMortuaryClass= function () {
			$http.get('/api/getMortuaryClassLists/'+facility_id).then(function(data) {
            $scope.getMortuaryClasses=data.data;
            });
			
        }
		
		$scope.addVitals= function (vitals,selectedPatient) {
			if (angular.isDefined(vitals)==false) {
                   return sweetAlert("Please Select the Vital Signs", "", "error");
                  }
				  else if(angular.isDefined(selectedPatient)==false){
					  
					  return sweetAlert("Please Select the Patient From list on the Left panel", "", "error");
				  }
			
			//console.log(selectedPatient);
			 var admission_id=selectedPatient.admission_id;
			var vitalsSigns={"observed_amount":vitals.amount,"observation_type_id":vitals.types,"admission_id":admission_id};
			$http.post('/api/addVitals',vitalsSigns).then(function(data) {
            $scope.getVitals=data.data;
			
          if(data.data.status ==0){
					 
					 sweetAlert(data.data.data, "", "error");
				  }else{
					  
                    sweetAlert(data.data.data, "", "success");            				  					
					 //$scope.getAdmPatient(selectedPatient);
							
				  }			
            });
			
        }
		
		$scope.addDrugs= function (drugs,selectedPatient) {
			if (angular.isDefined(drugs)==false) {
                   return sweetAlert("Please Select the DRUGS", "", "error");
                  }
				  else if(angular.isDefined(selectedPatient)==false){
					  
					  return sweetAlert("Please Select the Patient From list on the Left panel", "", "error");
				  }
			
			
			var admission_id=selectedPatient.admission_id;
			var drugsList={"how_often":drugs.drugs_given,"type_of_drugs_dosage_id":drugs.types,"admission_id":admission_id};
			$http.post('/api/addDrugs',drugsList).then(function(data) {
            
          if(data.data.status ==0){
					 
					 sweetAlert(data.data.data, "", "error");
				  }else{
					  
                    sweetAlert(data.data.data, "", "success");            				  					
					 //$scope.getAdmPatient(selectedPatient);
							
				  }			
            });
			
        }	
		
		$scope.addGoals= function (goals,selectedPatient) {
			if (angular.isDefined(goals)==false) {
                   return sweetAlert("Please Select Nursing Diagnosis", "", "error");
                  }
				  else if(angular.isDefined(selectedPatient)==false){
					  
					  return sweetAlert("Please Select the Patient From list on the Left panel", "", "error");
				  }
			
			//console.log(selectedPatient);
			 var admission_id=selectedPatient.admission_id;
			 var nursing_care_type="PATIENT GOALS";
		
  var goals={"targeted_plans":goals.patient_goals,
			            "nurse_diagnosis_id":goals.types,
			            "admission_id":admission_id,
			            "nursing_care_types":nursing_care_type,"nurse_id":user_id};
			$http.post('/api/addGoals',goals).then(function(data) {
           			
          if(data.data.status ==0){
					 
					 sweetAlert(data.data.data, "", "error");
				  }else{
					  
                    sweetAlert(data.data.data, "", "success");            				  					
					 //$scope.getAdmPatient(selectedPatient);
							
				  }			
            });
			
        }
		
		$scope.addImplementation= function (implementations,selectedPatient) {
			if (angular.isDefined(implementations)==false) {
                   return sweetAlert("Please Select Diagnosis for Implementations", "", "error");
                  }
				  else if(angular.isDefined(selectedPatient)==false){
					  
					  return sweetAlert("Please Select the Patient From list on the Left panel", "", "error");
				  }
			
			
			 var admission_id=selectedPatient.admission_id;
			 var nursing_care_type="IMPLEMENTATIONS";
		
  var implementations={"targeted_plans":implementations.implementation,
			            "nurse_diagnosis_id":implementations.getDiagnos,
			            "admission_id":admission_id,
			            "nursing_care_types":nursing_care_type,"nurse_id":user_id};
			$http.post('/api/addImplementations',implementations).then(function(data) {
           			
          if(data.data.status ==0){
					 
					 sweetAlert(data.data.data, "", "error");
				  }else{
					  
                    sweetAlert(data.data.data, "", "success");            				  					
					 //$scope.getAdmPatient(selectedPatient);
							
				  }			
            });
			
        }
		
		$scope.addEvaluations= function (evaluations,selectedPatient) {
			if (angular.isDefined(evaluations)==false) {
                   return sweetAlert("Please Select Diagnosis for Implementations", "", "error");
                  }
				  else if(angular.isDefined(selectedPatient)==false){
					  
					  return sweetAlert("Please Select the Patient From list on the Left panel", "", "error");
				  }
			
			
			 var admission_id=selectedPatient.admission_id;
			 var nursing_care_type="EVALUATIONS";
		
  var evaluations={"targeted_plans":evaluations.evaluation,
			            "nurse_diagnosis_id":evaluations.getDiagnos,
			            "admission_id":admission_id,
			            "nursing_care_types":nursing_care_type,"nurse_id":user_id};
			$http.post('/api/addEvaluations',evaluations).then(function(data) {
           			
          if(data.data.status ==0){
					 
					 sweetAlert(data.data.data, "", "error");
				  }else{
					  
                    sweetAlert(data.data.data, "", "success");            				  					
					 //$scope.getAdmPatient(selectedPatient);
							
				  }			
            });
			
        }	
		
		$scope.addTimes= function (times,selectedPatient) {
			if (angular.isDefined(times)==false) {
                   return sweetAlert("Please Select Diagnosis for TIMING", "", "error");
                  }
				  else if(angular.isDefined(selectedPatient)==false){
					  
					  return sweetAlert("Please Select the Patient From list on the Left panel", "", "error");
				  }
				  
				//console.log(times); 
			
			 var admission_id=selectedPatient.admission_id;
			 var nursing_care_type="TIME";
			 var daytime=times.time_day;
			 var resultsTime=times.time+' '+daytime;
		
  var times={"targeted_plans":resultsTime,
			       "nurse_diagnosis_id":times.getDiagnos,
			       "admission_id":admission_id,
			       "nursing_care_types":nursing_care_type,"nurse_id":user_id,"daytime":daytime};
				   //console.log(times);
				   
			$http.post('/api/addTimes',times).then(function(data) {
           			
          if(data.data.status ==0){
					 
					 sweetAlert(data.data.data, "", "error");
				  }else{
					  
                    sweetAlert(data.data.data, "", "success");            				  					
											
				  }			
            });
			
        }	
		
		$scope.patientDischarge= function (selectedPatient) {		
			 $scope.selectedPatient=selectedPatient;
			 //console.log($scope.selectedPatient);
			 
			 ////console.log(beds_number);			  
             var object =$scope.selectedPatient; 
             ////console.log(beds_number);				 
			 var modalInstance = $uibModal.open({
				  templateUrl: '/views/modules/nursing_care/patientDischarge.html',
				  size: 'lg',
				  animation: true,
				  controller: 'patientDischargedModal',
				  resolve:{
                  object: function () {
					         return object;
                  }
                  }				  
                  });
		 
		}
		
		
		
		
		
		$scope.addOutPuts= function (getOutPuts,selectedPatient) {
			if (angular.isDefined(getOutPuts)==false) {
                   return sweetAlert("Please Select the OUTPUT TYPES", "", "error");
                  }
				  else if(angular.isDefined(selectedPatient)==false){
					  
					  return sweetAlert("Please Select the Patient From list on the Left panel", "", "error");
				  }
			
			var admission_id=selectedPatient.admission_id;
			var getOutPuts={"amount":getOutPuts.amount,
							"observation_output_type_id":getOutPuts.types,
							"admission_id":admission_id,
							"si_units":getOutPuts.units,
							"nurse_id":user_id
							};
							//console.log(getOutPuts);
			$http.post('/api/addOutPuts',getOutPuts).then(function(data) {
            $scope.getOutPuts=data.data;
			
          if(data.data.status ==0){
					 
					 sweetAlert(data.data.data, "", "error");
				  }else{
					  
                    sweetAlert(data.data.data, "", "success");            				  					
					 //$scope.getAdmPatient(selectedPatient);
							
				  }			
            });
			
        }	
		
		
		
		$scope.addIntravenous= function (intravenous,selectedPatient) {
			if (angular.isDefined(intravenous)==false) {
                   return sweetAlert("Please Select the INTRAVENOUS FLUID", "", "error");
                  }
				  else if(angular.isDefined(selectedPatient)==false){
					  
					  return sweetAlert("Please Select the Patient From list on the Left panel", "", "error");
				  }
			
			
			 var admission_id=selectedPatient.admission_id;
			var intravenous={"intravenous_mils":intravenous.amount,"intravenous_types_id":intravenous.types,"admission_id":admission_id};
			$http.post('/api/addIntakeObservation',intravenous).then(function(data) {
            $scope.getIntravenous=data.data;
			
          if(data.data.status ==0){
					 
					 sweetAlert(data.data.data, "", "error");
				  }else{
					  
                    sweetAlert(data.data.data, "", "success");            				  					
					 //$scope.getAdmPatient(selectedPatient);
							
				  }			
            });
			
        }	
		
		
		$scope.addIntakeFluid= function (oral,selectedPatient) {
			if (angular.isDefined(oral)==false) {
                   return sweetAlert("Please Select the ORAL FLUID TYPE TAKEN", "", "error");
                  }
				  else if(angular.isDefined(selectedPatient)==false){
					  
					  return sweetAlert("Please Select the Patient From list on the Left panel", "", "error");
				  }						
			var admission_id=selectedPatient.admission_id;
			
var oral_mils={"oral_mils":oral.amount,"oral_types_id":oral.types,"admission_id":admission_id};
			$http.post('/api/addIntakeFluid',oral_mils).then(function(data) {
            $scope.getIntravenous=data.data;
			
          if(data.data.status ==0){
					 
					 sweetAlert(data.data.data, "", "error");
				  }else{
					  
                    sweetAlert(data.data.data, "", "success");		  					
												
				  }			
            });
			
        }	
			
			
			$scope.selectedGrade=function(item){				
				$scope.mortuaryClass=item;
			};
			
						   
		$scope.addMortuary= function (mortuary) {
	              if (angular.isDefined(mortuary)==false) {
                   return sweetAlert("Please Enter MORTUARY NAME BEFORE SAVING...", "", "error");
                  }

				   else if (angular.isDefined($scope.mortuaryClass)==false) {
                   return sweetAlert("Please Enter MORTUARY CLASS", "", "error");
                   }


				  else{
					 //console.log(mortuary);
					 //console.log(mortuary_class);
	$http.post('/api/addMortuary',{"mortuary_class_id":$scope.mortuaryClass.item_id,"mortuary_name":mortuary.mortuary_name,"user_id":user_id,"facility_id":facility_id}).then(function(data) {
           
			 if(data.data.status ==0){
					 $scope.mortuary = null;
					 sweetAlert(data.data.data, "", "error");
				  }else{
					   $scope.mortuary = null;
                    sweetAlert(data.data.data, "", "success");            				  					
					 
							
				  }
			
			
			
			
			          });  
					  
				  } 
				           
			
        }
	

		 $scope.getMortuaryDetails= function (mortuary_id) {

			     $http.get('/api/getMortuaryOneInfo/'+mortuary_id).then(function(data){
                 $scope.mortuary=data.data;
					          });


            $http.get('/api/getCabinets/'+mortuary_id).then(function(data) {
                $scope.Cabinets=data.data;
              var object =$scope.mortuary;
           	 var modalInstance = $uibModal.open({
				  templateUrl: '/views/modules/mortuary/manageMortuaryCabinates.html',
				  size: 'lg',
				  animation: true,
				  controller: 'mortuaryManagementModal',
				  resolve:{
                  object: function () {
					         return object;
                  }
                  }				  
                  });
				
				modalInstance.result.then(function(quick_registration) {
                $scope.quick_reg = quick_registration;
			    //console.log($scope.quick_reg);
                });	
			
            });
			
			  }	



		 $scope.getBedDetails=function (bed_id,ward_id,bed_available) {					
            $http.get('/api/OnThisBed/'+bed_id).then(function(data) {
              beds=data.data; 

            if(data.data.status ==0){
					
					 sweetAlert(data.data.data, "", "error");
				  }else{
					  var bed_details=bed_available+ ' TAKEN BY '+ data.data.data;
                    sweetAlert(bed_details, "", "success");  					
					 
				  }			  
           		
              });
			
			  }
		  
		  
		  
		  
		
		var patapata="";
		 $scope.getAdmissionNotes= function (patient) {				
            $http.post('/api/getInstructions',{"patient_id":patient}).then(function(data) {
            $scope.AdmissionNotes=data.data;
			patapata=$scope.AdmissionNotes;
			console.dir($scope.AdmissionNotes);
			          });
					   
            }

			$scope.giveBed= function (bed_id,last_name,ward_id,admission_id,bed_available) {
				
				 // sweetAlert(bed_id+' '+bed_available, "", "success"); 
				 //console.log(admission_id);
				
            $http.post('/api/giveBed',{"bed_id":bed_id,"ward_id":ward_id,"admission_id":admission_id,"bed_available":bed_available}).then(function(data) {
            $scope.giveBeds=data.data;
			//console.log($scope.giveBeds);
			
			if(data.data.status ==0){					
					 sweetAlert(data.data.data, "", "error");
				  }else{
					  var bed_details=bed_available+ ' SUCCESSFULLY ASSIGNED TO '+last_name;
                    sweetAlert(bed_details, "", "success");  					
					 
				  }	
			
			
			          });
					
					   
            }	

			
		 $scope.getAdmPatient= function (admitted) {
			 $scope.selectedPatient=admitted;
			 //console.log($scope.selectedPatient);
		 }
		 
		 $scope.getMortuary= function () {
			$http.get('/api/getMortuaryList').then(function(data){
            $scope.getMortuaryLists=data.data;
			   });
		 }
		 
		 
			 
			 $scope.getDisposed= function (pendingCorpse) {

             var object =pendingCorpse;
			 var modalInstance = $uibModal.open({
				  templateUrl: '/views/modules/mortuary/cabinetAllocation.html',
				  size: 'lg',
				  animation: true,
				  controller: 'corpseDisposedModal',
				  resolve:{
                  object: function () {
					         return object;
                  }
                  }				  
                  });
				
				modalInstance.result.then(function(quick_registration) {
                $scope.quick_reg = quick_registration;
			    //console.log($scope.quick_reg);
                });	
			

			
			  }	



			  $scope.assignToTheatre= function (patient,ward_id,admission_id) {
			 
						 
            		 
	$http.get('/api/getFullAdmitedPatientInfo/'+admission_id).then(function(data) {
            $scope.admissions=data.data;
			    //console.log(data.data);
				//console.log(admission_id);		
             var object = $scope.admissions;        
			 var modalInstance = $uibModal.open({
				  templateUrl: '/views/modules/nursing_care/postPatientsToTheatre.html',
				  size: 'lg',
				  animation: true,
				  controller: 'postPatientsToTheatreModal',
				  resolve:{
                  object: function () {
					         return object;
                  }
                  }				  
                  });
				
					
			
            });
			
			  }
			  
			 	 
	 var corpseData=[];
        $scope.showSearchCorpse= function (corpse) {
            $http.post('/api/showSearchCorpse',corpse).then(function(data){
                corpseData=data.data;
            });
            return corpseData;
        }


        $scope.CorpseDetailedReport=function(approvedCorpse){
            var postData={facility_id:facility_id,corpse_id:approvedCorpse.corpse_id};

            $http.post('/api/checkIfPermittedDischarge',postData).then(function(data) {





                    $mdDialog.show({
                        controller: function ($scope) {
                            $scope.SelectedCorpse={corpse_name:(approvedCorpse.first_name+' '+approvedCorpse.middle_name+' '+approvedCorpse.last_name),gender:approvedCorpse.gender,corpse_record_number:approvedCorpse.corpse_record_number};
                            $scope.corpseDetails=data.data[1];

                            $http.get('/api/getUsermenu/'+user_id).then(function(cardTitle){
                                $scope.facility_address=cardTitle.data[0];

                            });
                            $scope.cancel = function () {
                                $mdDialog.hide();
                            };

                            $scope.printForm = function () {
                                //location.reload();
                                var DocumentContainer = document.getElementById('divtoprint');
                                var WindowObject = window.open("", "PrintWindow",
                                    "width=900,height=700,top=50,left=450,toolbars=no,scrollbars=no,status=no,resizable=yes");
                                WindowObject.document.title = "PRINT CORPSE DISCHARGE CARD: GoT-HOMIS";
                                WindowObject.document.writeln(DocumentContainer.innerHTML);
                                WindowObject.document.close();

                                setTimeout(function () {
                                    WindowObject.focus();
                                    WindowObject.print();
                                    WindowObject.close();
                                }, 0);

                            };

                        },
                        templateUrl: '/views/modules/mortuary/fomu-maiti.html',
                        parent: angular.element(document.body),
                        clickOutsideToClose: false,
                        fullscreen: $scope.customFullscreen // Only for -xs, -sm breakpoints.
                    });






            });



        };



        $http.get('/api/getRelationships').then(function (data) {
            $scope.relationships = data.data;
        });
        $scope.getResidents = function (text) {

            $http.get('/api/searchResidences/' + text).then(function (data) {
                resdata = data.data;
            });
            return resdata;
        }

        $scope.getRelationships = function () {
            $http.get('/api/getRelationships').then(function (data) {
                $scope.relationships = data.data;
            });
        }();

        $scope.getResidence = function (text) {
            return Helper.getResidence(text).then(function (response) {
                return response.data;
            });
        };

        var residence_id = [];
        $scope.selectedResidence = function (residence) {
            console.log(residence);
            $scope.residence = residence;
            residence_id =$scope.residence.residence_id;
        };


        var patientsList=[];
        $scope.getCorpses = function (searchKey) {

            var dataToPost = {searchKey: searchKey};
            $http.post('/api/getSeachedCorpses', dataToPost).then(function (data) {
                patientsList = data.data;

            });

            return patientsList;
        }
        $scope.getCorpseDetails = function (selectedCorpse) {
            $scope.selectedCorpse=selectedCorpse;
        };

        $scope.selectedResidenceWhereFuneral = function (residence) {
            $scope.funeralSites=residence.residence_id;
        };

        $scope.corpseTakerResidence = function (residenceCorpse) {
            $scope.residenceCorpseTaker=residenceCorpse.residence_id;
            console.log($scope.residenceCorpseTaker);
        };

        $scope.corpseDischarge = function (corpseTaker) {
            $scope.corpseTaker=corpseTaker;
            var corpseTakerName=corpseTaker.names;
            var relationship=corpseTaker.relationship;
            var mobile_number=corpseTaker.mobile_number;
            var vehicle_number=corpseTaker.vehicle_number;
            var identityNumber=corpseTaker.identityNumber;
            var identityType=corpseTaker.identityType;
            var corpseID=$scope.selectedCorpse.id;
            var funeralSiteId=$scope.funeralSites;
            var residenceCorpseTakerId=$scope.residenceCorpseTaker;

            var postData={corpseTakerName:corpseTakerName,relationship:relationship,mobile_number:mobile_number,vehicle_number:vehicle_number,identityNumber:identityNumber,identityType:identityType,corpseID:corpseID,funeralSiteId:funeralSiteId,residenceCorpseTakerId:residenceCorpseTakerId,user_id:user_id,

            };

            $http.post('/api/corpseTaker', postData).then(function (data) {
                if (data.data.status == 0) {
                    sweetAlert(data.data.data, "", "error");
                } else {
                    sweetAlert(data.data.data, "", "success");
                }

            });



        };






    }

})();