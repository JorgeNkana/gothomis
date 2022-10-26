/**
 * Created by USER on 2017-02-13.
 */
(function() {

    'use strict';

    angular
        .module('authApp')
        .controller('nursingCareController',nursingCareController);

    function nursingCareController($http, $auth, $rootScope,$state,$location,$scope,$uibModal, $mdDialog, $mdBottomSheet,Helper,$filter,ClinicalServices) {
        var facility_id =$rootScope.currentUser.facility_id;
        var staff_name =$rootScope.currentUser.name;
        var user_id =$rootScope.currentUser.id;
		   $scope.cancel = function () {
                                 $mdDialog.hide();
                            };

                            $scope.indicators=[{id:12,indicator_name:"Maternal Dealth"},
                            {id:13,indicator_name:"Delivery"},
                            {id:14,indicator_name:"FSB"},
                            {id:15,indicator_name:"MSB"},
                            {id:16,indicator_name:"Neonatal Dealth"},
                            ];
$scope.saveIndictors=function(indicator,admitted){

    var dataaa={admission_id:admitted.admission_id,admission_status_id:indicator,
        visit_date_id:admitted.visit_date_id,patient_id:admitted.patient_id,
        facility_id:facility_id,user_id:user_id,ward_id:admitted.ward_id};
        $http.post('/api/setIndictorsWardStatus',dataaa).then(function(data) {
        var msg=data.data.msg;   
        var status=data.data.status; 
        if(status==200)  {
          
              swal(msg,"","success") ;    
        }
        else{
           swal(msg,"","error") ;  
        }
              
        });   
        
                   
         
}

        $scope.mytime1 = new Date();
        $scope.mytime2 = new Date();
        $scope.regex=/\s/g;
        $scope.AvailablePrintOuts=[{"id":1,"TemplateName":"Drug Sheet"},{"id":2,"TemplateName":"Input Output Forms"},{"id":3,"TemplateName":"Observation Chart"},{"id":4,"TemplateName":"Turning Chart"}];
        $scope.AvailablePrintOutsTheatre=[{"id":1,"TemplateName":"Anaesthesia"},{"id":2,"TemplateName":"Doctor Report/indication"}];
        $scope.wardStatus=[{"id":1,"TemplateName":"List waiting for Operation"},{"id":2,"TemplateName":"List of Patients Discharged"},{"id":3,"TemplateName":"List of Patients with Drugs Dosage"}];
        $scope.bedStates=[{"id":9,"TemplateName":"Previous Visit History"},{"id":2,"TemplateName":"Transfer to Another Bed"},{"id":3,"TemplateName":"Transfer to Another Ward"},{"id":4,"TemplateName":"Deceased"},{"id":5,"TemplateName":"Service Ordered"},{"id":6,"TemplateName":"Abscondee from the ward"}
		,{"id":7,"TemplateName":"Serous Patient"}
		,{"id":8,"TemplateName":"DAMA"}
		];
        $scope.chartsReserved=[{"id":1,"TemplateName":"Input Form"},{"id":2,"TemplateName":"Turning Chart"},{"id":3,"TemplateName":"Output Form"},{"id":4,"TemplateName":"Treatment Chart"}];
		
		
		$scope.printForm = function () {
            //location.reload();
            var DocumentContainer = document.getElementById('divtoprint');
            var WindowObject = window.open("", "PrintWindow",
                "width=900,height=700,top=50,left=450,toolbars=no,scrollbars=no,status=no,resizable=yes");
            WindowObject.document.title = "PRINT WARD REPORT: GoT-HOMIS";
            WindowObject.document.writeln(DocumentContainer.innerHTML);
            WindowObject.document.close();

            setTimeout(function () {
                WindowObject.focus();
                WindowObject.print();
                WindowObject.close();
            }, 0);

        };

        $scope.VitalSigns=[{"id":1,"TemplateName":"Pulse Rate"},
            {"id":2,"TemplateName":"Systolic Pressure"},
            {"id":3,"TemplateName":"Diastolic Pressure"},
            {"id":4,"TemplateName":"Oxygen Satulation"}];
        this.topDirections = ['left', 'up'];
      this.bottomDirections = ['down', 'right'];

      this.isOpen = false;

      this.availableModes = ['md-fling', 'md-scale'];
      this.selectedMode = 'md-fling';

      this.availableDirections = ['up', 'down', 'left', 'right'];
      this.selectedDirection = 'up';

       $scope.getListAwaitingAnaethesia=function(){     
        $http.get('/api/getAnaethesiaList/'+facility_id).then(function(data) {     
                   $scope.waitingLists=data.data;
        });

 }; 

   $scope.selectedSearchedItem=function(item,SelectedPatient){
	  $scope.searchedItem=item;	   
	  $scope.SelectedPatient=SelectedPatient;	   
   };

   
    $scope.saveItemServiced=function(dataToPost){
	            if(angular.isDefined(dataToPost)==false){
	          return sweetAlert("You must write frequency of the service given", "", "error");
                 }
	  			 var dataToPost= {selectedService:dataToPost};
					 
				 $http.post('/api/saveWardBill',dataToPost).then(function(data) {     
                   if(data.data.status==1){
				var postData={facility_id:facility_id,patient_id:data.data.patient_id};
				$http.post('/api/getServicesGivenWard',postData).then(function(data) {   
                   $scope.getServices=data.data;
				   $scope.cancel();
        });   
		
                   return sweetAlert(data.data.data, "", "success");
        
                   }else if(data.data.status==0){
                    return sweetAlert(data.data.data, "", "error");
                   }
               });	 
	     
   };

        $scope.recordAnaethesiaRecord= function (patient) {
            $mdDialog.show({
                controller: function ($scope) {
                    $scope.patientPaticulars=patient;
                    $scope.admissionInfoData=patient;
                    $scope.beds=patient;
                    $scope.cancel = function () {
                        $mdDialog.hide();
                    };
                },
                templateUrl: '/views/modules/nursing_care/record_anaethesia.html',
                parent: angular.element(document.body),
                clickOutsideToClose: false,
                fullscreen: $scope.customFullscreen // Only for -xs, -sm breakpoints.
            });

        };

        $scope.doctorTheatreReport= function (patient) {
            $mdDialog.show({
                controller: function ($scope) {
                    $scope.patientPaticulars=patient;
                    $scope.admissionInfoData=patient;
                    $scope.beds=patient;
                    $scope.cancel = function () {
                        $mdDialog.hide();
                    };
                },
                templateUrl: '/views/modules/nursing_care/report_theatre.html',
                parent: angular.element(document.body),
                clickOutsideToClose: false,
                fullscreen: $scope.customFullscreen // Only for -xs, -sm breakpoints.
            });

        };


        $scope.recordIntraOperations= function (patient) {
            $mdDialog.show({
                controller: function ($scope) {
                    $scope.patientPaticulars=patient;
                    $scope.admissionInfoData=patient;
                    $scope.beds=patient;
                    $scope.cancel = function () {
                        $mdDialog.hide();
                    };
                },
                templateUrl: '/views/modules/nursing_care/record_intra_operation.html',
                parent: angular.element(document.body),
                clickOutsideToClose: false,
                fullscreen: $scope.customFullscreen // Only for -xs, -sm breakpoints.
            });

        };


        $scope.recoveryFromOperations= function (patient) {
            $mdDialog.show({
                controller: function ($scope) {
                    $scope.patientPaticulars=patient;
                    $scope.admissionInfoData=patient;
                    $scope.beds=patient;
                    $scope.cancel = function () {
                        $mdDialog.hide();
                    };
                },
                templateUrl: '/views/modules/nursing_care/recoveryFromOperations.html',
                parent: angular.element(document.body),
                clickOutsideToClose: false,
                fullscreen: $scope.customFullscreen // Only for -xs, -sm breakpoints.
            });

        };


        $scope.postAnaestheticOperations= function (patient) {
            $mdDialog.show({
                controller: function ($scope) {
                    $scope.patientPaticulars=patient;
                    $scope.admissionInfoData=patient;
                    $scope.beds=patient;
                    $scope.cancel = function () {
                        $mdDialog.hide();
                    };
                },
                templateUrl: '/views/modules/nursing_care/postAnaestheticOperations.html',
                parent: angular.element(document.body),
                clickOutsideToClose: false,
                fullscreen: $scope.customFullscreen // Only for -xs, -sm breakpoints.
            });

        };




        var searchedItem;
        //search service
        $scope.searchItemToServiceInWard = function (searchKey,selectPatient) {
			//console.log(selectPatient);
            return Helper.searchItemToServiceInWard(searchKey,selectPatient,facility_id)
                .then(function (response) {
                     searchedItem=response.data;
					 //console.log(searchedItem);
					 return searchedItem;
                });
        };	  
		//searchItem
		$scope.getSelectedIv=function(item){
			$scope.itemIv=item;
			
		};
		$scope.getSelectedOral=function(item){
			$scope.itemOral=item;
			
		};
		
		$scope.searchItem = function (searchKey) {
            return Helper.searchItemObservations(searchKey)
                .then(function (response) {
                     searchedItem=response.data;
					 //console.log(searchedItem);
					 return searchedItem;
                });
        };	
		
      $scope.cancelSheet = function() {
            $mdBottomSheet.hide();
        };

			$scope.saveObservationChart = function(input_output,SelectedPatient) {
			var amount_iv ;
				var amount_oral;
				var type_iv;
				var type_oral;
			 if (angular.isDefined(input_output)==false) {
                return sweetAlert("Enter all values", "", "error");
             }
			 else if(angular.isDefined($scope.itemIv)==false && angular.isDefined($scope.itemOral)==false ){
				return sweetAlert("Search of the IV or Oral Type", "", "error"); 
				 
			 }
			 else if(angular.isDefined($scope.itemOral)==true && input_output.amount_oral==undefined ){
				return sweetAlert("Search of the Oral Amount", "", "error"); 
				 
			 }
			 else if(angular.isDefined($scope.itemIv)==true && input_output.amount_iv==undefined ){
				return sweetAlert("Search of the Input Amount", "", "error"); 
				 
			 }
			 if(angular.isDefined($scope.itemIv)==true && angular.isDefined(input_output.amount_iv)==true){
				 amount_iv= input_output.amount_iv ;
				 type_iv=$scope.itemIv.item_id;
				
			 }
			 if(angular.isDefined($scope.itemOral)==true && angular.isDefined(input_output.amount_oral)==true){
				 amount_oral= input_output.amount_oral ;
				 type_oral=$scope.itemOral.item_id;
				
			 }
				 
			var visit_date_id=SelectedPatient.visit_date_id;
			var admission_id=SelectedPatient.admission_id;
			var dataToPost={facility_id:facility_id,user_id:user_id,type_iv:type_iv,type_oral:type_oral,visit_date_id:visit_date_id,admission_id:admission_id,
			amount_iv:amount_iv,
			amount_oral:amount_oral,
			date_recorded:moment(input_output.date_input).format('YYYY-MM-DD'),
			time_recorded:input_output.time_input,
			
			};
			//console.log(dataToPost);
			$http.post('/api/saveInputs',dataToPost).then(function(data) {   
                   $scope.dataSaved=data.data;
				   
				   return sweetAlert("Successfully saved input type/IV or oral", "", "success"); 
				   
        });   
			
        };
	
 $scope.getProcedureDetails = function (patient) {

            $scope.cancelSheet();

            $mdDialog.show({
                controller: function ($scope) {


                    $scope.patient = patient;
                    $scope.cancel = function () {
                        $mdDialog.hide();
                    };
                },
                templateUrl: '/views/modules/nursing_care/operationStatus.html',
                parent: angular.element(document.body),
                clickOutsideToClose: false,
                fullscreen: $scope.customFullscreen // Only for -xs, -sm breakpoints.
            });

        };

$scope.bookOperation=function(patient,operation){
           if (angular.isDefined(operation)==false) {
                return sweetAlert("Confirm Date of Operation", "", "error");
             }
             var operation_date=operation.operation_date;
             var remarks=operation.comments;
             var admission_id=patient.admission_id;
             var patient_id=patient.patient_id;
             var item_id=patient.item_id;
             var patientName=patient.first_name+" "+patient.middle_name+" "+patient.last_name;              
             var dataToPost={"patientName":patientName,"status":1,"remarks":remarks,"operation_date":operation_date,"admission_id":admission_id,"patient_id":patient_id,"item_id":item_id,"user_id":user_id,"facility_id":facility_id};
               $http.post('/api/saveOperations',dataToPost).then(function(data) {     
                   $scope.cancelSheet();
                   if(data.data.status==1){
                   return sweetAlert(data.data.data, "", "success");
        
                   }else if(data.data.status==0){
                    return sweetAlert(data.data.data, "", "error");
                   }
               });


};
   $scope.wardRegistration=function(){

 $mdDialog.show({                 
                        controller: function ($scope) {
                                   $scope.cancel = function () {
                                 $mdDialog.hide();
                            };
                        },
                        templateUrl: '/views/modules/nursing_care/ward_registration_form.html',
                        parent: angular.element(document.body),
                        clickOutsideToClose: false,
                         fullscreen: $scope.customFullscreen // Only for -xs, -sm breakpoints.
                    });

   };


   $scope.addTreatment=function(treatment,itemsPrescribed){

         if (angular.isDefined(treatment)==false) {
                return sweetAlert("Enter date/time dose given", "", "error");
             }

            var admission_id=itemsPrescribed.admission_id;
            var item_id=itemsPrescribed.item_id;
            var patient_id=itemsPrescribed.patient_id;
            var date_dosage=treatment.date_dosage;
            var timedosage=treatment.timedosage;
            var remarks=treatment.remarks;
            var item_name=itemsPrescribed.item_name;
            var dataToPost={"item_name":item_name,"facility_id":facility_id,"user_id":user_id,"admission_id":admission_id,"item_id":item_id,"patient_id":patient_id,"date_dosage":date_dosage,"timedosage":timedosage,"remarks":remarks};
    $http.post('/api/prescribeNurse',dataToPost).then(function(data) {      
                    if(data.data.status ==0){
                        sweetAlert(data.data.data, "", "error");
                    }else{

                         $scope.cancel = function () {
                                 $mdDialog.hide();
                            };
             
                $scope.cancel();

                sweetAlert(data.data.data, "", "success");

                    }
                   });


  };

$scope.dispenseThisPatient=function(itemsPrescribed){
  $mdDialog.show({                 
                        controller: function ($scope) {
                                $scope.itemsPrescribed=itemsPrescribed;                             
                                $scope.cancel = function () {
                                 $mdDialog.hide();
                            };
                        },
                        templateUrl: '/views/modules/nursing_care/provide_drug.html',
                        parent: angular.element(document.body),
                        clickOutsideToClose: false,
                         fullscreen: $scope.customFullscreen // Only for -xs, -sm breakpoints.
                    });

};

$scope.nurseProvideEmergenceDrug=function(nursingcare,SelectedPatient){
//console.log(SelectedPatient);
   if (angular.isDefined(nursingcare)==false) {
                return sweetAlert("Enter date/time dose given", "", "error");
             }

      else if (angular.isDefined(nursingcare.drug)==false) {
                return sweetAlert("Select Drug From List", "", "error");
           }

            var admission_id=SelectedPatient.admission_id;
            var item_id=nursingcare.drug.item_id;
            var patient_id=SelectedPatient.patient_id;
            var date_dosage=nursingcare.date_nursing_care;
            var timedosage=nursingcare.time_nursing;
            var reason=nursingcare.reason;
            var item_name=nursingcare.drug.item_name;
            var dose=nursingcare.dose;
            var interval=nursingcare.interval;
            
            var dataToPost={visit_id:SelectedPatient.visit_date_id ,"frequency":interval,"dispensing_status":1,"nursePrescriber":1,"instruction":reason,"dose":dose,"quantity":dose,"dispenser_id":user_id,"prescriber_id":user_id,"item_name":item_name,"facility_id":facility_id,"user_id":user_id,"admission_id":admission_id,"item_id":item_id,"patient_id":patient_id,"start_date":date_dosage,"date_dosage":date_dosage,"timedosage":timedosage,"remarks":reason};
    $http.post('/api/prescribeNurse',dataToPost).then(function(data) {      
                    if(data.data.status ==0){
                        sweetAlert(data.data.data, "", "error");
                    }else{
                         $scope.cancel = function () {
                         $mdDialog.hide();
                            };
             
                $scope.cancel();

                sweetAlert(data.data.data, "", "success");

                    }
                   });


};  

  $scope.nursingTreatment=function(templateID,SelectedPatient){
      if(templateID==1){
                        $mdDialog.show({                 
                        controller: function ($scope) {
                                $scope.SelectedPatient=SelectedPatient;
                                $scope.cancel = function () {
                                 $mdDialog.hide();
                            };
                        },
                        templateUrl: '/views/modules/nursing_care/nurse_provide_drug.html',
                        parent: angular.element(document.body),
                        clickOutsideToClose: false,
                         fullscreen: $scope.customFullscreen // Only for -xs, -sm breakpoints.
                    });
             }
             else if(templateID==2){
                 var admission_id=SelectedPatient.admission_id;
                 var visit_date_id=SelectedPatient.visit_date_id;
                 var dataToPost={"templateID":2,"admission_id":admission_id,visit_date_id:visit_date_id};

                   $http.post('/api/getPrescribedItems',dataToPost).then(function(data) {      
                      //console.log(data.data);
                    $mdDialog.show({                 
                        controller: function ($scope) {
                                $scope.itemsPrescribeds=data.data;                             
                                $scope.cancel = function () {
                                 $mdDialog.hide();
                            };
                        },
                        templateUrl: '/views/modules/nursing_care/drug_sheet.html',
                        parent: angular.element(document.body),
                        clickOutsideToClose: false,
                         fullscreen: $scope.customFullscreen // Only for -xs, -sm breakpoints.
                    });
                   });
                 
             }
      }

	$scope.getLabTestRequests= function(){
			$http.get('/api/LabTestRequest/'+facility_id).then(function(data) {
				$scope.LabTestRequests=data.data;
            });
	};
	
	$scope.getTestRequestAll= function(LabTestRequest){
		    var order_id=LabTestRequest.order_id;
			var item_id=LabTestRequest.item_id;
			var dataToPost={order_id:order_id,item_id:item_id};
			$http.post('/api/LabTestRequestPatient',dataToPost).then(function(data) {
			        var tests=data.data;

                $mdDialog.show({
                    locals: {'tests':tests
                    },
                    controller: function ($scope) {
                        $scope.tests =tests;
						$scope.patientInfo=LabTestRequest;
                        $http.get('/api/patientWardBed/'+ $scope.tests[0][0].admission_id).then(function(data) {
							$scope.getAdmisionInfos = data.data[0];
                        });
                        $scope.cancel = function () {
                            $scope.selectedPatient=null;
                            $mdDialog.hide();
                        };
						
						$scope.generateSampleNumber=function(test_name,sample_type,last_name,sub_department_name,request_id) {
							var dataPost={test_name:test_name,"sample_type":sample_type,"order_control":null,"order_validator_id":user_id,"last_name":last_name,"facility_id":facility_id,"request_id":request_id,sub_department_name:sub_department_name, visit_date_id:$scope.tests[0][0].visit_date_id};

							$http.post('/api/generateSampleNumber',dataPost).then(function(data) {
								if(data.data.status ==0){
									sweetAlert(data.data.data, "", "error");
									return;
								}

								$scope.sampleResposes=data.data;

								var object_resp =  $scope.sampleResposes;
								
								var object ={item_name:object_resp.test_name,"time_generated":object_resp.time_generated,"last_name":object_resp.last_name,"sub_department_name":object_resp.sub_department_name,"sample_number":object_resp.sample_number,"image_code":object_resp.barcode};


								$mdDialog.show({

									controller: function ($scope) {
										$scope.last_name =object_resp.last_name;
										$scope.sub_department_name =object_resp.sub_department_name;
										$scope.test_name =object_resp.test_name;
										$scope.sample_number =object_resp.sample_number;
										$scope.image_code =object_resp.barcode;
										$scope.time_generated =object_resp.time_generated;
										$scope.cancel = function () {
											$scope.selectedPatient=null;
											$mdDialog.hide();
										};

									$scope.PrintBarcodeContent  = function () {
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
									templateUrl: '/views/modules/laboratory/barcode_print_out.html',
									parent: angular.element(document.body),
									clickOutsideToClose: false,
									fullscreen: $scope.customFullscreen // Only for -xs, -sm breakpoints.
								});

								var modalInstance = $uibModal.open({
									templateUrl: '/views/modules/laboratory/barcode_print_out.html',
									size: 'lg',
									animation: true,
									controller: 'barcodeModal',
									windowClass: 'app-modal-window',
									resolve:{
										object: function () {
											return object;
										}
									}
								});

							});
						};
                    },
                    templateUrl: '/views/modules/laboratory/LabTestRequestPatient.html',
                    parent: angular.element(document.body),
                    clickOutsideToClose: false,
                    fullscreen: $scope.customFullscreen // Only for -xs, -sm breakpoints.
                });

            });
        };


         $scope.wardSampleCollection=function(){                   
                   $http.get('/api/wardSampleCollection/'+user_id).then(function(data) {
                       $scope.sampleToCollects=data.data;              
                   });   

          };
		  
		  
		  
	$scope.patientDischarge= function (selectedPatient) {
		var dataPost={patient_id:selectedPatient.patient_id,facility_id:facility_id,visit_id:selectedPatient.account_id ,account_id:selectedPatient.account_id};
	   $http.post('/api/getPendingBills',dataPost).then(function(data) {
		  if(data.data.length>0){
			$mdDialog.show({
				controller: function ($scope) {
					$scope.bill=data.data; 
					$scope.totalCost=function(){
						var total = 0;
						data.data.forEach(function(bill){
							total += bill.price*bill.quantity - bill.discount;
						});
						return total;
					};
					$scope.selectedPatient=selectedPatient;
					$scope.staff_name=staff_name;
					$http.get('/api/getUsermenu/'+user_id).then(function(cardTitle){
						$scope.menu=cardTitle.data;
					});
						 
					$scope.cancel = function () {
						$mdDialog.hide();
					};
					
					$scope.printForm = function () {
						var DocumentContainer = document.getElementById('divtoprint');
						var WindowObject = window.open("", "PrintWindow","width=900,height=700,top=50,left=450,toolbars=no,scrollbars=no,status=no,resizable=yes");
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
					templateUrl: '/views/modules/nursing_care/ward_bills.html',
					parent: angular.element(document.body),
					clickOutsideToClose: false,
					fullscreen: $scope.customFullscreen // Only for -xs, -sm breakpoints.
					});
			}else{
			  $mdDialog.show({
                        controller: function ($scope) {
                            $scope.selectedPatient=	selectedPatient;
	                        var account_id=selectedPatient.account_id;
							var postData={account_id:account_id};
							 $scope.staff_name=staff_name;
							
							
	$http.post('/api/continuationNotes',postData).then(function(data) {
    $scope.continuationNotes=data.data;
				  });
					
 $scope.cancel = function () {
                                $mdDialog.hide();
                            };
					
											
$http.get('/api/getUsermenu/'+user_id).then(function(cardTitle){
							$scope.menu=cardTitle.data;
            $scope.menu=data.data;  
                           });	

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
						   
							
		$scope.addDischarge=function (selectedPatient) {
                		  			 			  
		 var DocumentContainer = document.getElementById('divtoprint');
				     var WindowObject = window.open("", "PrintWindow",
                "width=900,height=700,top=50,left=450,toolbars=no,scrollbars=no,status=no,resizable=yes");			  
				  var user_id=$rootScope.currentUser.id;
                  var bed_id=selectedPatient.bed_id;
                  var admission_id=selectedPatient.admission_id;
                  var patient_id=selectedPatient.patient_id;
                  var ward_id=selectedPatient.ward_id;
                  var account_id=selectedPatient.account_id;
                  var patient_maincategory_id=selectedPatient.main_category_id;
     				  
				  var dischargeNotes={ward_id:ward_id,account_id:account_id,patient_maincategory_id:patient_maincategory_id,admission_status_id:4,facility_id:facility_id,'patient_id':patient_id,'nurse_id':user_id,
				  'confirm':1,'bed_id':bed_id,'admission_id':admission_id};
				 
				 $http.post('/api/addDischargeNotes',dischargeNotes).then(function(data) {
						  $scope.dischargeNotes=data.data;
	
                  if(data.data.status ==0){
					 
					 sweetAlert(data.data.data, "", "error");
				  }else{
                   $scope.cancel(); 
				   $scope.getPendingDischarge();
			       
            WindowObject.document.title = "PRINT PATIENT DISCHARGE FORM: GoT-HOMIS";
            WindowObject.document.writeln(DocumentContainer.innerHTML);
            WindowObject.document.close(); 
					$scope.printForm();
					   					
				  }
				  
				  
					  });
				 
				 
					
		   
			};

      $http.get('/api/getUsermenu/'+user_id).then(function(cardTitle){
            $scope.menu=cardTitle.data;
          });

       $http.post('/api/getPatientAdmissionInfo',dataPost).then(function (data) {
                       $scope.patientAdmissionInfo = data.data;
                       $scope.discharge_summary = data.data[0][0];
                    });


			
			  $scope.getPendingDischarge = function(){
             //.. i need to pass ward ID LATER TO RESTRICT
            var postData={"nurse_id":user_id};
            $http.post('/api/getPendingDischarge',postData).then(function(data) {
                $scope.admitted=data.data;
               
            });
           
        };

                          
                            $scope.cancel = function () {
                                $mdDialog.hide();
                            };
                        },
                      templateUrl: '/views/modules/nursing_care/patientDischarge.html',
                        parent: angular.element(document.body),
                        clickOutsideToClose: false,
                        fullscreen: $scope.customFullscreen // Only for -xs, -sm breakpoints.
                    });
	  

	  }
						});
           
            
        }	;
		  
		  
		  
		  
            $scope.items = [];

            $scope.addItems = function(qty,SelectedPatient) {
								
			if(angular.isDefined($scope.searchedItem)==false){
				return sweetAlert("You must select service first", "", "error");
				}
			else if(angular.isDefined(qty)==false ){		
		    return sweetAlert("You must Enter quantity", "", "error");
    
	         }		
			   
                for (var i = 0; i < $scope.items.length; i++)
                    if ($scope.items[i].id == $scope.searchedItem.item_id) {
                swal($scope.searchedItem.item_name + ' ' + "already in your wish list!","","info");
                        return;
                    }
                $scope.items.push({					
					 user_id:user_id,
	                 facility_id:facility_id,					 
	                 item_type_id:$scope.searchedItem.item_type_id,					 
	                 item_price_id:$scope.searchedItem.price_id,					 
	                 status_id:1,					 
	                 quantity:qty,					 
	                 discount:0,					 
	                 discount_by:user_id,					 
	                 patient_category_id:$scope.searchedItem.patient_category_id,	 
	                 payment_filter:$scope.searchedItem.patient_category_id,			 
	                 item_name:$scope.searchedItem.item_name,			 
	                 patient_id:$scope.SelectedPatient.patient_id,	
	                 account_number_id:$scope.SelectedPatient.visit_date_id	
					  });
           //console.log(SelectedPatient);
            };
		  
		  
		  
		   $scope.removeSelectedService = function(item, items) {
                var indexremoveobject = items.indexOf(item);
                items.splice(indexremoveobject, 1);
            };
		  
		  
		  
      $scope.nursingCareOptions=function(templateID,selectedPatient){
		   var SelectedPatient=selectedPatient;
         
       if(templateID==1){
		         
                    $mdDialog.show({                 
                        controller: function ($scope) {
                                $scope.SelectedPatient=SelectedPatient;
                                $scope.cancel = function () {
                                 $mdDialog.hide();
                            };
							  $http.get('/api/getUsermenu/'+user_id).then(function(cardTitle){
							$scope.facility_address=cardTitle.data[0];
                              
                           });
                        },
                        templateUrl: '/views/modules/nursing_care/nursing_care_chart.html',
                        parent: angular.element(document.body),
                        clickOutsideToClose: false,
                         fullscreen: $scope.customFullscreen // Only for -xs, -sm breakpoints.
                    });
             }
             else if(templateID==2){
                 var admission_id=SelectedPatient.admission_id;

                   $http.get('/api/getListNursingCare/'+admission_id).then(function(data) {
              

                    $mdDialog.show({                 
                        controller: function ($scope) {
                                $scope.SelectedPatient=SelectedPatient;
                                $scope.nursingCares=data.data;
								
								  $http.get('/api/getUsermenu/'+user_id).then(function(cardTitle){
							$scope.facility_address=cardTitle.data[0];
                              
                           });
                             
                                $scope.cancel = function () {
                                 $mdDialog.hide();
                            };
                        },
                        templateUrl: '/views/modules/nursing_care/pre_view_nursing_care_chart.html',
                        parent: angular.element(document.body),
                        clickOutsideToClose: false,
                         fullscreen: $scope.customFullscreen // Only for -xs, -sm breakpoints.
                    });
                   });
                 
             }
      };
       
       $scope.saveNursingCare=function(nursingcare,SelectedPatient){
             if (angular.isDefined(nursingcare)==false) {
                return sweetAlert("Please Enter Nursing Diagnosis Details", "", "error");
             }
            else if(angular.isDefined(nursingcare.date_nursing_care)==false){
              return sweetAlert("Please Enter Date for  Nursing Diagnosis", "", "error");      
            }
            else if(angular.isDefined(nursingcare.time_nursing)==false){
             return sweetAlert("Please Enter Time for  Nursing Diagnosis", "", "error");       
            }
             else if(angular.isDefined(nursingcare.diagnosis)==false){
              return sweetAlert("Please Enter Nursing Diagnosis", "", "error");       
            }
             else if(angular.isDefined(nursingcare.objective)==false){
             return sweetAlert("Please Enter Objective of Care", "", "error");       
            }
            else if(angular.isDefined(nursingcare.implementation)==false){
              return sweetAlert("Please Enter Implementations of Care", "", "error");      
            }
            else if(angular.isDefined(nursingcare.evaluation)==false){
              return sweetAlert("Please Enter Evaluations of Care", "", "error");      
            }
            else {
              var postData={"admission_id":SelectedPatient.admission_id,"date_planned":nursingcare.date_nursing_care,"time_planned":nursingcare.time_nursing,"diagnosis_name":nursingcare.diagnosis,"objective":nursingcare.objective,"implementation":nursingcare.implementation ,"evaluation":nursingcare.evaluation,"facility_id":facility_id,"user_id":user_id};
                $http.post('/api/addNursingCare',postData).then(function(data) {
                  if(data.data.status ==0){
                        sweetAlert(data.data.data, "", "error");
                    }else{
                        nursingcare.implementation = null;
                        sweetAlert(data.data.data, "", "success");


                    }
  
                   });

            }

       };

       $scope.changePatientBed=function(new_bed_id,old_bed_id,patient_name,admission_id){

         

var postData={"patient_name":patient_name,"admission_id":admission_id,"old_bed_id":old_bed_id,"new_bed_id":new_bed_id};
 $http.post('/api/changePatientBed',postData).then(function(data) {
                 sweetAlert(data.data.data, "", "success");

 });
       };


         $scope.changePatientWard=function(selectedPatient,ward,old_bed_id,patient_name,admission_id,transferReason,patient_id){
               if(angular.isDefined(transferReason)==false){
              return sweetAlert("Please Enter Reason for transfer", "", "error");      
            }
            //console.log(selectedPatient);
          var postData={visit_date_id:selectedPatient.visit_date_id,old_ward_id:selectedPatient.ward_id,"facility_id":facility_id,ward_id:ward.ward_id,patient_id:selectedPatient.patient_id,"nurse_id":user_id,"transferReason":transferReason,"patient_name":patient_name,"admission_id":admission_id,"old_bed_id":old_bed_id};
          $http.post('/api/changePatientWard',postData).then(function(data) {
                 sweetAlert(data.data.data, "", "success");
        });
       };


       

        $scope.changedValue = function(TemplateName,SelectedPatient) {
             var templateID=TemplateName.id;
             var admission_id=SelectedPatient.admission_id;
            var postData={"admission_id":admission_id,"templateID":templateID};
            $http.post('/api/getPrescribedItems',postData).then(function(data) {
                $scope.patientsList=data.data;
             var object={"SelectedPatient":SelectedPatient,"PrescribedItems":$scope.patientsList};
              if(templateID==1){
                 var modalInstance = $uibModal.open({
                     templateUrl: '/views/modules/nursing_care/drug_sheet.html',
                     size: 'lg',
                     animation: true,
                     controller: 'nursePrintOuts',
                     resolve:{
                         object: function () {
                             return object;
                         }
                     }
                 });
             }
             else if(templateID==2){
                 var modalInstance = $uibModal.open({
                     templateUrl: '/views/modules/nursing_care/input_outputs.html',
                     size: 'lg',
                     animation: true,
                     controller: 'nursePrintOuts',
                     resolve:{
                         object: function () {
                             return object;
                         }
                     }
                 });
             }
            });
             };


        $scope.getReportBasedOnThisDate=function(pef){
			
				  $http.get('/api/getUsermenu/'+user_id ).then(function(data) {
            $scope.menu=data.data;
        });
		
		 $scope.staff_name=staff_name;

            if(angular.isDefined(pef)==false){
                return sweetAlert("You must select date range", "", "error");
            }

            var dataToPost={facility_id:facility_id,start_date:pef.start,end_date:pef.end};

            $http.post('/api/getWardReport',dataToPost).then(function(data) {
                $scope.results=data.data;
                //console.log($scope.results);
            });

        };



 $scope.getListFromTheatresReport=function(pef){

            if(angular.isDefined(pef)==false){
                return sweetAlert("You must select date range", "", "error");
            }

            var dataToPost={facility_id:facility_id,start_date:pef.start,end_date:pef.end};

            $http.post('/api/getListFromTheatresReport',dataToPost).then(function(data) {
                $scope.results=data.data;
                //console.log($scope.results);
            });

        };



        $scope.changedValueBed = function(TemplateName,SelectedPatient) {
					 $scope.SelectedPatient=SelectedPatient;
             var templateID=TemplateName.id;
             var admission_id=SelectedPatient.admission_id;
            var postData={"admission_id":admission_id,"templateID":templateID};
             
             if(templateID==9){

 $mdDialog.show({                 
                        controller: function ($scope) {
                                $scope.selectedPatient=SelectedPatient;
                                $scope.cancel = function () {
                                 $mdDialog.hide();
                            };
                            var patient_id=SelectedPatient.patient_id;
                             ClinicalServices.getPatientVisits({patient_id:patient_id}).then(function (response) {
                              $scope.patientsVisits = response.data;
                             });

  $scope.getPatientReport = function (item) {
    var account_id=item.account_id;
    var patient_id=item.patient_id;
    var lab = {patient_id:patient_id,dept_id:2,account_id:account_id};
    var rad = {patient_id:patient_id,dept_id:3,account_id:account_id};
    ClinicalServices.getPatientComplaints(item).then(function (data) {
    $scope.prevHistory = data.data[0];
    $scope.otherComplaints = data.data[1];
    $scope.hpi = data.data[2];
    });
    ClinicalServices.getPatientDiagnosis(item).then(function (data) {
    $scope.prevDiagnosis = data.data;
    });
    ClinicalServices.getPatientROS(item).then(function (data) {
    $scope.prevRos = data.data[0];
    $scope.prevRosSummary = data.data[1];
    });
    ClinicalServices.getPatientAllergies(item).then(function (data) {
    $scope.allergies = data.data;
    });
    ClinicalServices.getPatientPhysicalExams(item).then(function (data) {
    $scope.prevSystemic = data.data[0];
    $scope.prevGen = data.data[1];
    $scope.prevLocal = data.data[2];
    $scope.prevSummary = data.data[3];
    $scope.prevOtherSystemic = data.data[4];
    });
    ClinicalServices.getPatientInvestigationResults(lab).then(function (data) {
    $scope.labInvestigationsz = data.data;
    });
    ClinicalServices.getPatientInvestigationResults(rad).then(function (data) {
    $scope.radiologyResults = data.data;
    });
    ClinicalServices.getPatientPrescriptions(item).then(function (data) {
    $scope.prevMedicines = data.data;
    });
    ClinicalServices.getPatientProcedures(item).then(function (data) {
    $scope.pastProcedures = data.data;
    });
    }
                        },
                        templateUrl: '/views/modules/nursing_care/Previous_notes.html',
                        parent: angular.element(document.body),
                        clickOutsideToClose: false,
                         fullscreen: $scope.customFullscreen // Only for -xs, -sm breakpoints.
                    });

             }

               if(templateID==1){

 $mdDialog.show({                 
                        controller: function ($scope) {
                                $scope.SelectedPatient=SelectedPatient;
                                $scope.cancel = function () {
                                 $mdDialog.hide();
                            };
                        },
                        templateUrl: '/views/modules/nursing_care/drug_sheet.html',
                        parent: angular.element(document.body),
                        clickOutsideToClose: false,
                         fullscreen: $scope.customFullscreen // Only for -xs, -sm breakpoints.
                    });

             }
             else if(templateID==2){
                 var ward_id=SelectedPatient.ward_id;

               
                
                    $mdDialog.show({                 
                        controller: function ($scope) {
                                $scope.SelectedPatient=SelectedPatient;
                                   $http.get('/api/getBedsWithNoPatients/'+ward_id).then(function(data) {
                                       $scope.beds=data.data;
                                   });
          
                                $scope.cancel = function () {
                                 $mdDialog.hide();
                            };
                        },
                        templateUrl: '/views/modules/nursing_care/change_bed.html',
                        parent: angular.element(document.body),
                        clickOutsideToClose: false,
                         fullscreen: $scope.customFullscreen // Only for -xs, -sm breakpoints.
                    });


             }


               else if(templateID==3){
                 var ward_id=SelectedPatient.ward_id;

               
                
                    $mdDialog.show({                 
                        controller: function ($scope) {
                                $scope.SelectedPatient=SelectedPatient;
                                var dataToPost={"facility_id":facility_id,"ward_id":ward_id};
                                   $http.post('/api/getWardsToChange',dataToPost).then(function(data) {
                                       $scope.wards=data.data;
                                   });
          
                                $scope.cancel = function () {
                                 $mdDialog.hide();
                            };
                        },
                        templateUrl: '/views/modules/nursing_care/ward_bed.html',
                        parent: angular.element(document.body),
                        clickOutsideToClose: false,
                         fullscreen: $scope.customFullscreen // Only for -xs, -sm breakpoints.
                    });


             }

               else if(templateID==4){
                 var ward_id=SelectedPatient.ward_id;



                    $mdDialog.show({
                        controller: function ($scope) {
                                $scope.selectedPatient=SelectedPatient;
                                var dataToPost={"facility_id":facility_id,"ward_id":ward_id};
                                   $http.post('/api/getWardsToChange',dataToPost).then(function(data) {
                                       $scope.wards=data.data;
                                   });

                                $scope.cancel = function () {
                                 $mdDialog.hide();
                            };

                                $scope.saveDeathNotes = function (notes,selectedPatient) {

                                    var postData={admission_status_id:8,visit_date_id:selectedPatient.visit_date_id,ward_id:selectedPatient.ward_id,bed_id:selectedPatient.bed_id, patientName:selectedPatient.fullname,user_id:user_id,facility_id:facility_id,serious_notes:notes,admission_id:selectedPatient.admission_id,description:notes};
                                    $http.post('/api/saveDeathNotes',postData).then(function(
                                        data) {
                                        if (data.data.status == 0) {

                                            sweetAlert(data.data.data, "", "error");
                                        } else {
                                            sweetAlert(data.data.data, "", "success");

                                        }
                                    });

                                    };
                        },
                        templateUrl: '/views/modules/nursing_care/deceased.html',
                        parent: angular.element(document.body),
                        clickOutsideToClose: false,
                         fullscreen: $scope.customFullscreen // Only for -xs, -sm breakpoints.
                    });


             }

                else if(templateID==5){
                 var ward_id=SelectedPatient.ward_id;
    
                    $mdDialog.show({                 
                        controller: function ($scope) {
							
			var postData={visit_id: SelectedPatient.visit_date_id};
				$http.post('/api/getServicesGivenWard',postData).then(function(data) {   
                   $scope.getServices=data.data;
				  
        }); 
							
                                $scope.SelectedPatient=SelectedPatient;
                                var dataToPost={"facility_id":facility_id,"ward_id":ward_id};
                                   $http.post('/api/getWardsToChange',dataToPost).then(function(data) {
                                       $scope.wards=data.data;
                                   });
          
                                $scope.cancel = function () {
                                 $mdDialog.hide();
                            };
                        },
                        templateUrl: '/views/modules/nursing_care/pre_pare_for_theatres.html',
                        parent: angular.element(document.body),
                        clickOutsideToClose: false,
                         fullscreen: $scope.customFullscreen // Only for -xs, -sm breakpoints.
                    });


             }
			 
 else if(templateID==6){
                $scope.SelectedPatient=SelectedPatient;
				var selectedPatient= $scope.SelectedPatient;
			    $mdDialog.show({
                        controller: function ($scope) {
                           $scope.selectedPatient=selectedPatient;
						   
		$scope.addDischarge=function (discharge,selectedPatient,dt) {
                 				
				  if (angular.isDefined(discharge)==false) {
                   return sweetAlert("Provide Discharge Notes", "", "error");
                  }	
				  else if (angular.isDefined(selectedPatient)==false) {
                   return sweetAlert("Please Select Patient", "", "error");
                  }					  			 			  
				  	  
				  else{
                  var dateSelected = $filter('date')(dt,'yyyy-MM-dd');
                  var today = $filter('date')(new Date(),'yyyy-MM-dd');				  
				  var user_id=$rootScope.currentUser.id;
                  var bed_id=selectedPatient.bed_id;
                  var admission_id=selectedPatient.admission_id;
                  var patient_id=selectedPatient.patient_id;
                  var visit_date_id=selectedPatient.visit_date_id;
                  var ward_id=selectedPatient.ward_id;
     				  
			     var dischargeNotes={admission_status_id:7,facility_id:facility_id,'patient_id':patient_id,'permission_date':today,'nurse_id':user_id,'user_id':user_id,visit_date_id:visit_date_id,
				  'confirm':1,'bed_id':bed_id,'admission_id':admission_id,
				  ward_id:ward_id,'domestic_dosage':discharge,'followup_date':dateSelected};
				 
				 $http.post('/api/addDischargeNotes',dischargeNotes).then(function(data) {
						  $scope.dischargeNotes=data.data;
	
                  if(data.data.status ==0){
					 
					 sweetAlert(data.data.data, "", "error");
				  }else{
                   	sweetAlert(data.data.data, "", "success");  
					   					
				  }
				  
				  
					  });
				 
				 
					
		   }
			};
			
                          
                            $scope.cancel = function () {
                                $mdDialog.hide();
                            };
                        },
                      templateUrl: '/views/modules/nursing_care/patientAbscondee.html',
                        parent: angular.element(document.body),
                        clickOutsideToClose: false,
                        fullscreen: $scope.customFullscreen // Only for -xs, -sm breakpoints.
                    });
			   


             }

		 
 else if(templateID==7){
                $scope.SelectedPatient=SelectedPatient;
				var selectedPatient= $scope.SelectedPatient;
				//console.log(selectedPatient);
			    $mdDialog.show({
                        controller: function ($scope) {
                           $scope.selectedPatient=selectedPatient;
						  	
							$scope.saveNotes=function(notes,selectedPatient){
								var postData={patientName:selectedPatient.fullname,ward_id:selectedPatient.ward_id,user_id:user_id,facility_id:facility_id,serious_notes:notes,visit_date_id:selectedPatient.visit_date_id,admission_id:selectedPatient.admission_id,description:notes,admission_status_id:10};
					$http.post('/api/saveNotes',postData).then(function(
					data) {
                        if(data.data.status ==0){

                            sweetAlert(data.data.data, "", "error");
                        }else{
                            sweetAlert(data.data.data, "", "success");

                        }

					});
								
							};
                          
                            $scope.cancel = function () {
                                $mdDialog.hide();
                            };
                        },
                      templateUrl: '/views/modules/nursing_care/serious_patient.html',
                        parent: angular.element(document.body),
                        clickOutsideToClose: false,
                        fullscreen: $scope.customFullscreen // Only for -xs, -sm breakpoints.
                    });
			   


             }
			 
			else if(templateID==8){
                $scope.SelectedPatient=SelectedPatient;
				var selectedPatient= $scope.SelectedPatient;
				//console.log(selectedPatient);
			    $mdDialog.show({
                        controller: function ($scope) {
                           $scope.selectedPatient=selectedPatient;
						  	
							  $http.get('/api/getUsermenu/'+user_id ).then(function(data) {
            $scope.menu=data.data;
        });
		
		 $scope.staff_name=staff_name;
		
	var postData={account_id:selectedPatient.visit_date_id};				
	$http.post('/api/continuationNotes',postData).then(function(data) {
    $scope.continuationNotes=data.data;
				  });
							
							$scope.comitDama=function(selectedPatient){
								var postData={ward_id:selectedPatient.ward_id,bed_id:selectedPatient.bed_id,nurse_id:user_id,facility_id:facility_id,	
								account_id:selectedPatient.visit_date_id,
								patient_maincategory_id:selectedPatient.main_category_id,							
								admission_id:selectedPatient.admission_id,
								patient_id:selectedPatient.patient_id,		
								admission_status_id:9};
					$http.post('/api/addDischargeNotes',postData).then(function(
					data) {
                        if(data.data.status ==0){

                            sweetAlert(data.data.data, "", "error");
                        }else{
							$scope.cancel();
                            sweetAlert(data.data.data, "", "success");

                        }

					});
								
							};
                          
                            $scope.cancel = function () {
                                $mdDialog.hide();
                            };
                        },
                      templateUrl: '/views/modules/nursing_care/dama.html',
                        parent: angular.element(document.body),
                        clickOutsideToClose: false,
                        fullscreen: $scope.customFullscreen // Only for -xs, -sm breakpoints.
                    });
			   


             }







            };
            


            
                 $scope.getChats = function(TemplateName,SelectedPatient) {
                          var templateID=TemplateName.id;
                          var admission_id=SelectedPatient.admission_id;
                          var postData={"admission_id":admission_id,"templateID":templateID};
                      
                               if(templateID==1){
                                $mdDialog.show({                 
                                controller: function ($scope) {
                                $http.post('/api/getPrescribedItems',postData).then(function(data) {
                                $scope.itemsPrescribeds=data.data;
                                });
								
				  $http.get('/api/getUsermenu/'+user_id).then(function(cardTitle){
							$scope.facility_address=cardTitle.data[0];
                              
                           });

								
                                $scope.SelectedPatient=SelectedPatient;
                                $scope.cancel = function () {
                                $mdDialog.hide();
                                };
                                },
                         templateUrl: '/views/modules/nursing_care/input_outputs.html',
                         parent: angular.element(document.body),
                         clickOutsideToClose: false,
                         fullscreen: $scope.customFullscreen // Only for -xs, -sm breakpoints.
                        });

             }
             else if(templateID==2){
                
$mdDialog.show({                 
                        controller: function ($scope) {
                                $scope.SelectedPatient=SelectedPatient;
								$http.get('/api/getUsermenu/'+user_id).then(function(cardTitle){
							$scope.cardTitle=cardTitle.data[0];
                              
                           });

								
                                $scope.cancel = function () {
                                 $mdDialog.hide();
                            };
                        },
                        templateUrl: '/views/modules/nursing_care/turning_chat.html',
                        parent: angular.element(document.body),
                        clickOutsideToClose: false,
                         fullscreen: $scope.customFullscreen // Only for -xs, -sm breakpoints.
                    });


             }
                 else if(templateID==3){
             
                        $mdDialog.show({                 
                        controller: function ($scope) {
							$http.get('/api/getUsermenu/'+user_id).then(function(cardTitle){
							$scope.cardTitle=cardTitle.data[0];
                              
                           });

                                $scope.SelectedPatient=SelectedPatient;
                                $scope.cancel = function () {
                                 $mdDialog.hide();
                            };
                        },
                        templateUrl: '/views/modules/nursing_care/feeding_chat.html',
                        parent: angular.element(document.body),
                        clickOutsideToClose: false,
                         fullscreen: $scope.customFullscreen // Only for -xs, -sm breakpoints.
                    });


             }

                   else if(templateID==4){

                         var postData={"admission_id":admission_id};
                      
                $http.get('/api/getUsermenu/'+user_id).then(function(cardTitle){
							$scope.cardTitle=cardTitle.data[0];
                              
                           });

                        $mdDialog.show({                 
                        controller: function ($scope) {
                                $scope.itemsPrescribed=SelectedPatient;

                                 $http.post('/api/getTreatmentChart',postData).then(function(data) {
                                $scope.treatmentCharts=data.data;
                                });
                                $scope.cancel = function () {
                                 $mdDialog.hide();
                            };
                        },
                        templateUrl: '/views/modules/nursing_care/treatment_chat.html',
                        parent: angular.element(document.body),
                        clickOutsideToClose: false,
                         fullscreen: $scope.customFullscreen // Only for -xs, -sm breakpoints.
                    });


             }
                        
             };
			 
			 $scope.getPatientHistory=function(item){
				 //console.log(item);
				 var visit_date_id=item.visit_date_id;
				 var admission_id=item.admission_id;
				 $mdDialog.show({                 
                                controller: function ($scope) {
                              
								
				 $http.post('/api/prevHistory',{"patient_id":item.patient_id,"date_attended":item.date_attended}).then(function (data) {
                            $scope.prevHistory = data.data[0];
                            $scope.otherComplains = data.data[1];
                            $scope.hpis = data.data[2];
                        });
                        $http.post('/api/getPrevDiagnosis',{"patient_id":item.patient_id,"date_attended":item.date_attended}).then(function (data) {
                            $scope.prevDiagnosis = data.data;
                        });
                        $http.post('/api/getPrevRos',{"patient_id":item.patient_id,"date_attended":item.date_attended}).then(function (data) {
                            $scope.prevRos = data.data;
                        });
                        $http.post('/api/getPrevBirth',{"patient_id":item.patient_id,"date_attended":item.date_attended}).then(function (data) {
                            $scope.prevBirth = data.data;
                        });
                       
                        $http.post('/api/getPrevFamily',{"patient_id":item.patient_id,"date_attended":item.date_attended}).then(function (data) {
                            $scope.prevFamily = data.data;
                        });
                        
                        $http.post('/api/getInvestigationResults',{"patient_id":item.patient_id,"date_attended":item.date_attended,"dept_id":2}).then(function (data) {
                            $scope.labInvestigationsz = data.data;
                        });
                        $http.post('/api/getInvestigationResults',{"patient_id":item.patient_id,"date_attended":item.date_attended,"dept_id":3}).then(function (data) {
                            $scope.radiologyResults = data.data;
                        });
                        $http.post('/api/getPastMedicine',{"patient_id":item.patient_id,"date_attended":item.date_attended}).then(function (data) {
                            $scope.prevMedicines = data.data;
                        });
                        $http.post('/api/getPastProcedures',{"patient_id":item.patient_id,"date_attended":item.date_attended}).then(function (data) {
                            $scope.pastProcedures = data.data;
                        });
                        $http.post('/api/getAllergies',{"patient_id":item.patient_id,"date_attended":item.date_attended}).then(function (data) {
                            $scope.allergies = data.data;
                        });

								
                                $scope.SelectedPatient=item;
                                $scope.cancel = function () {
                                $mdDialog.hide();
                                };
                                },
                         templateUrl: '/views/modules/nursing_care/clerk_sheet.html',
                         parent: angular.element(document.body),
                         clickOutsideToClose: false,
                         fullscreen: $scope.customFullscreen // Only for -xs, -sm breakpoints.
                        });
				 
				 
			 };
			 

//recording different chartsReserved

            
                 $scope.getChating = function(TemplateName,SelectedPatient) {
                          var templateID=TemplateName.id;
                          var admission_id=SelectedPatient.admission_id;
                          var postData={"admission_id":admission_id,"templateID":templateID};
                      
                               if(templateID==1){
                                $mdDialog.show({                 
                                controller: function ($scope) {
                              
								
				  $http.get('/api/getUsermenu/'+user_id).then(function(cardTitle){
							$scope.facility_address=cardTitle.data[0];
                              
                           });
				  var dataPost={admission_id:admission_id}
                    $http.post('/api/getInputs',dataPost).then(function(inputs){
							$scope.Inputs=inputs.data;

                           });

								
                                $scope.SelectedPatient=SelectedPatient;
                                $scope.cancel = function () {
                                $mdDialog.hide();
                                };
                                },
                         templateUrl: '/views/modules/nursing_care/record_input_outputs.html',
                         parent: angular.element(document.body),
                         clickOutsideToClose: false,
                         fullscreen: $scope.customFullscreen // Only for -xs, -sm breakpoints.
                        });

             }
             else if(templateID==2){
                
$mdDialog.show({                 
                        controller: function ($scope) {
                                $scope.SelectedPatient=SelectedPatient;

                            var dataPost={admission_id:admission_id}
                            $http.post('/api/getTurningChart',dataPost).then(function(inputs){
                                $scope.turningCharts=inputs.data;

                            });

								$http.get('/api/getUsermenu/'+user_id).then(function(cardTitle){
							$scope.facility_address=cardTitle.data[0];
                              
                           });

								
                                $scope.cancel = function () {
                                 $mdDialog.hide();
                            };
                        },
                        templateUrl: '/views/modules/nursing_care/turning_chat.html',
                        parent: angular.element(document.body),
                        clickOutsideToClose: false,
                         fullscreen: $scope.customFullscreen // Only for -xs, -sm breakpoints.
                    });


             }
                 else if(templateID==3){
             
                        $mdDialog.show({                 
                        controller: function ($scope) {
							$http.get('/api/getUsermenu/'+user_id).then(function(cardTitle){
							$scope.cardTitle=cardTitle.data[0];
                              
                           });
						   
						   $http.post('/api/getOutputs',{admission_id:admission_id}).then(function(output){
							$scope.outputs=output.data;
                              
                           });

                                $scope.SelectedPatient=SelectedPatient;
                                $scope.cancel = function () {
                                 $mdDialog.hide();
                            };

							$scope.saveOutPutChart = function (output,SelectedPatient) {
                                  if (angular.isDefined(output)==false) {
                return sweetAlert("Enter all values for outputs", "", "error");
                                     }
			var type_of_output=output.output_type;
			var amount_output=output.amount;
            var visit_date_id=SelectedPatient.visit_date_id;
			var admission_id=SelectedPatient.admission_id;
			var dataToPost={facility_id:facility_id,user_id:user_id,type_of_output:type_of_output,visit_date_id:visit_date_id,admission_id:admission_id,
			amount_output:amount_output,
			date_recorded:moment(output.date_output).format('YYYY-MM-DD'),
			time_recorded:output.time_output,
			
			};
			$http.post('/api/saveOutputs',dataToPost).then(function(data) {   
                   $scope.dataSaved=data.data;
				   $http.post('/api/getOutputs',{admission_id:admission_id}).then(function(output){
							$scope.outputs=output.data;
                              
                           });
				   return sweetAlert("Successfully saved output type", "", "success"); 
				   
        });   
                            };
                        },
                        templateUrl: '/views/modules/nursing_care/record_outputs.html',
                        parent: angular.element(document.body),
                        clickOutsideToClose: false,
                         fullscreen: $scope.customFullscreen // Only for -xs, -sm breakpoints.
                    });


             }

                   else if(templateID==4){

                         var postData={"admission_id":admission_id};
                      
                $http.get('/api/getUsermenu/'+user_id).then(function(cardTitle){
							$scope.cardTitle=cardTitle.data[0];
                              
                           });

                        $mdDialog.show({                 
                        controller: function ($scope) {
                                $scope.itemsPrescribed=SelectedPatient;

                                 $http.post('/api/getTreatmentChart',postData).then(function(data) {
                                $scope.treatmentCharts=data.data;
                                });
                                $scope.cancel = function () {
                                 $mdDialog.hide();
                            };
                        },
                        templateUrl: '/views/modules/nursing_care/treatment_chat.html',
                        parent: angular.element(document.body),
                        clickOutsideToClose: false,
                         fullscreen: $scope.customFullscreen // Only for -xs, -sm breakpoints.
                    });


             }
                        
             };


			 
			 
			 
            


        $scope.changedValueTheatre = function(TemplateName,SelectedPatient) {
             var templateID=TemplateName.id;
             var admission_id=SelectedPatient.admission_id;
            var postData={"admission_id":admission_id,"templateID":templateID};
            $http.post('/api/getPrescribedItems',postData).then(function(data) {
                $scope.patientsList=data.data;
             var object={"SelectedPatient":SelectedPatient,"PrescribedItems":$scope.patientsList};
              if(templateID==1){
                 var modalInstance = $uibModal.open({
                     templateUrl: '/views/modules/nursing_care/anaesthesia.html',
                     size: 'lg',
                     animation: true,
                     controller: 'nursePrintOuts',
                     resolve:{
                         object: function () {
                             return object;
                         }
                     }
                 });
             }
             else if(templateID==2){
                 var modalInstance = $uibModal.open({
                     templateUrl: '/views/modules/nursing_care/input_outputs.html',
                     size: 'lg',
                     animation: true,
                     controller: 'nursePrintOuts',
                     resolve:{
                         object: function () {
                             return object;
                         }
                     }
                 });
             }
            });
             };



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
            $scope.setTabPendingAdmission(1);
        });

        var patientsList=[];
        $scope.searchAdmitted = function (searchKey) {
       var postData={"searchKey":searchKey,"facility_id":facility_id};
            $http.post('/api/seachForAdmittedPatients',postData).then(function(data) {
                patientsList=data.data;

            });
            return patientsList;
        }



  var drugs=[];
        $scope.searchDrug = function (searchKey) {
            $http.get('/api/searchDrugs/'+searchKey).then(function(data) {
                drugs=data.data;
            });
            return drugs;
        }




        $scope.searchAdmittedForPrintOuts= function (selectPatient) {
            var admission_id=selectPatient.admission_id;
            $http.get('/api/getSearchedAdmitted/'+admission_id).then(function(data) {
                $scope.admissions=data.data;
                //console.log(data.data);
                //console.log(admission_id);
                var object = $scope.admissions;
                var modalInstance = $uibModal.open({
                    templateUrl: '/views/modules/nursing_care/nursing_print_outs.html',
                    size: 'lg',
                    animation: true,
                    controller: 'nursingCareModal',
                    windowClass: 'app-modal-window',
                    resolve:{
                        object: function () {
                            return object;
                        }
                    }
                });


            });

        };


        $scope.searchOperatedForPrintOuts= function (selectPatient) {
            var admission_id=selectPatient.admission_id;
            $http.get('/api/getSearchedAdmitted/'+admission_id).then(function(data) {
                $scope.admissions=data.data;
                //console.log(data.data);
                //console.log(admission_id);
                var object = $scope.admissions;
                var modalInstance = $uibModal.open({
                    templateUrl: '/views/modules/nursing_care/nursing_theatre_print_outs.html',
                    size: 'lg',
                    animation: true,
                    controller: 'nursingCareModal',
                    windowClass: 'app-modal-window',
                    resolve:{
                        object: function () {
                            return object;
                        }
                    }
                });


            });

        };

 $scope.pickSearchedAdmittedForPrintOuts= function (selectPatient) {
           //console.log(selectPatient);
        };

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


        $scope.vitalRegister = function(selectedPatient) {
            var VitalData = [];
            var field_id;
            $scope.Vitals.forEach(function (vital) {
                field_id = vital.vital_name.replace($scope.regex, '_');
                if ($('#' + field_id).val() != '') {
                    VitalData.push({
                        'vital_sign_id': vital.vital_id,
                        'patient_id': selectedPatient.patient_id,
                        'vital_sign_value': $('#' + field_id).val(),
                        'registered_by':user_id
                    });
                    $('#' + field_id).val('');
                }
            })

          //console.log(VitalData);
            if (VitalData.length > 0) {
                $http.post('/api/nurseVitalSignRegister', VitalData).then(function (data) {
                    var msg = data.data.msg;
                    var notification = data.data.notification;
                    var status = data.data.status;
                    if (status == 0) {
                        return sweetAlert("", msg, "error");
                    }
                    else {
                        return sweetAlert("", msg, "success");

                    }
                });
            }
        };

        $scope.printVitals= function(selectedPatient) {
            if (angular.isDefined(selectedPatient)==false) {
                return sweetAlert("Select  Patient First", "", "error");
            }
            $scope.selectedPatient =selectedPatient;
             $http.get('/api/getVitals').then(function(data) {
                $scope.Vitals = data.data;
            });
        };
    $http.get('/api/getUsermenu/'+user_id ).then(function(data) {
            $scope.menu=data.data;
        });

        $scope.setTabAdmission = function(){
          //  $scope.tab = newTab;
           //.. i need to pass ward ID LATER TO RESTRICT
            var postData={"nurse_id":user_id};
            $http.post('/api/getPendingAdmissionList',postData).then(function(data) {
                $scope.admin=data.data;
                 $scope.getOrderedProcedures(user_id);
          
                //console.log($scope.admin);
            });
           
        };
		
        $scope.getPendingDischarge = function(newTab){
             //.. i need to pass ward ID LATER TO RESTRICT
            var postData={"nurse_id":user_id};
            $http.post('/api/getPendingDischarge',postData).then(function(data) {
                $scope.admitted=data.data;
                //console.log($scope.admitted);
            });
           
        };



     $scope.setTabNursingOfficer = function(newTab){
            $scope.tab = newTab;
            //.. i need to pass ward ID LATER TO RESTRICT
         var postData={"nurse_id":user_id};
            $http.post('/api/getPendingAdmissionList',postData).then(function(data) {
                $scope.admin=data.data;
                //console.log($scope.admin);

            });
              };


        $scope.setTabattendedPatientTheatre = function(newTab){
            $scope.tab = newTab;
            //.. i need to pass ward ID LATER TO RESTRICT
            $http.get('/api/attendPatientTheatre').then(function(data) {
                $scope.attendPatientTheatres=data.data;

            });


        };


        $scope.getDischargedPatients = function(){
            $http.get('/api/getDischargedLists/'+facility_id).then(function(data) {
                $scope.dischargedPatients=data.data;
            });
        }

        $scope.setTabWards = function(newTab){
         //   $scope.setTabAdmission(1);
            $http.get('/api/getWards/'+facility_id).then(function(data) {
                $scope.getWards=data.data;

            });
        };

        $scope.setTabDischarge = function(newTab){
            $scope.tab = newTab;
            $scope.getDischargedPatients();
            $http.get('/api/getWards/'+facility_id).then(function(data) {
                $scope.getWards=data.data;

            });
        };

        $scope.setTabObservation = function(newTab){
            $scope.tab = newTab;
            //$scope.setTabAdmission(1);
            //.. i need to pass ward ID LATER TO RESTRICT
            var postData={"nurse_id":user_id};

            $http.post('/api/getAprovedAdmissionList',postData).then(function(data) {
                $scope.admitted=data.data;

            });
        };


        $scope.getPatientAddmitedDetail = function(patient){

            var postData={"nurse_id":user_id,visit_id:patient.visit_date_id};

            $http.post('/api/getPatientAddmitedDetail',postData).then(function(data) {
                $scope.admitted=data.data;

            });
        };

        var dataset=[];
        $scope.SearchPatientAddmited = function(search){

            var postData={"nurse_id":user_id,sarchKey:search};

            $http.post('/api/SearchPatientAddmited',postData).then(function(data) {
                dataset=data.data;

            });
            return dataset;
        };

        $scope.SearchgetPendingDischarge = function(search){

            var postData={"nurse_id":user_id,sarchKey:search};
            $http.post('/api/SearchgetPendingDischarge',postData).then(function(data) {
                dataset=data.data;

            });
            return dataset;
        };
        $scope.LoadPendingDischargeData = function(patient){

            var postData={"nurse_id":user_id,visit_id:patient.account_id};
            $http.post('/api/LoadPendingDischargeData',postData).then(function(data) {
                $scope.admitted=data.data;

            });

        };
        $scope.SearchPendingAdmissionListData = function(search){

            var postData={"nurse_id":user_id,sarchKey:search};

            $http.post('/api/SearchPendingAdmissionListData',postData).then(function(data) {
                dataset=data.data;

            });
            return dataset;
        };

        $scope.SearchdoctorNotes = function(patient){

            var postData={"nurse_id":user_id,visit_id:patient.visit_date_id};
            $http.post('/api/SearchdoctorNotes',postData).then(function(data) {
                $scope.doctorNoteses=data.data;

            });
        };

        $scope.SearchPendingAdmissionList = function(patient){

            var postData={"nurse_id":user_id,visit_id:patient.visit_date_id};
            $http.post('/api/SearchPendingAdmissionList',postData).then(function(data) {
                $scope.admin=data.data;

            });
        };

		//waiting list for operation
		$scope.waitingProcedure = [];
        $scope.getOrderedProcedures=function(){
               $http.get('/api/getOrderedProcedures/'+user_id).then(function(data) {
                 $scope.waitingProcedure =data.data;
             });
        };
		
        $scope.setTabAdmitted = function(){
               var postData={"nurse_id":user_id};
             $http.post('/api/getAprovedAdmissionList',postData).then(function(data) {
                $scope.admitted=data.data;
  
            });
        };


         $scope.setTabPendingAdmission = function(newTab){
              var postData={"nurse_id":user_id};
            $http.post('/api/getPendingAdmissionList',postData).then(function(data) {
                $scope.admin=data.data;

            });
        };


        $scope.setTabNursingCare = function(newTab){
            $scope.tab = newTab;
            //$scope.setTabAdmission(1);
            //.. i need to pass ward ID LATER TO RESTRICT
            var postData={"nurse_id":user_id};
            $http.post('/api/getAprovedAdmissionList',postData).then(function(data) {
                $scope.admitted=data.data;

            });
        };

        $scope.setTabWardtypes = function(newTab){
            $scope.tab = newTab;

            $http.post('/api/getWardTypes').then(function(data) {
                $scope.getWardTypes=data.data;

            });
        };

        $scope.setTabTreatment = function(newTab){
            $scope.tab = newTab;
            var postData={"nurse_id":user_id};
            $http.post('/api/getAprovedAdmissionList',postData).then(function(data) {
                $scope.admitted=data.data;

            });
        };
		
		$scope.doctorNotes = function(){
           
		   var postData={"nurse_id":user_id};
            $http.post('/api/doctorNotes',postData).then(function(data) {
                $scope.doctorNoteses=data.data;

            });
        };

		$scope.getMoreNotes = function(selectedPatient){
			
			
			$mdDialog.show({                 
                        controller: function ($scope) {
                                $scope.SelectedPatient=selectedPatient;
                              
							   var postData={"nurse_id":user_id,showAll:1,account_id:selectedPatient.visit_date_id};
            $http.post('/api/doctorNotes',postData).then(function(data) {
                $scope.doctorNoteses=data.data;

            });
						
$scope.printForm = function () {
            //location.reload();
            var DocumentContainer = document.getElementById('divtoprint');
            var WindowObject = window.open("", "PrintWindow",
                "width=900,height=700,top=50,left=450,toolbars=no,scrollbars=no,status=no,resizable=yes");
            WindowObject.document.title = "PRINT WARD REPORT: GoT-HOMIS";
            WindowObject.document.writeln(DocumentContainer.innerHTML);
            WindowObject.document.close();

            setTimeout(function () {
                WindowObject.focus();
                WindowObject.print();
                WindowObject.close();
            }, 0);

        };						
								
								  $http.get('/api/getUsermenu/'+user_id).then(function(cardTitle){
							$scope.facility_address=cardTitle.data[0];
                              
                           });
                             
                                $scope.cancel = function () {
                                 $mdDialog.hide();
                            };
                        },
                        templateUrl: '/views/modules/nursing_care/get_doctor_continuation_notes.html',
                        parent: angular.element(document.body),
                        clickOutsideToClose: false,
                         fullscreen: $scope.customFullscreen // Only for -xs, -sm breakpoints.
                    });
			
			          
		  
        };

        $scope.isSet = function(tabNum){
            return $scope.tab === tabNum;
        }
        $scope.oneAtATime = true;




        $scope.addWardTypes= function (wards) {
            //var ward_type=wards.ward_type;
            if (angular.isDefined(wards)==false) {
                return sweetAlert("Please Enter WARD TYPE", "", "error");
            }else{
                $http.post('/api/saveWardTypes',{"ward_type_name":wards.ward_category}).then(function(data) {

                    if(data.data.status ==0){
                        $scope.wards = null;
                        sweetAlert(data.data.data, "", "error");
                    }else{
                        $scope.wards = null;
                        sweetAlert(data.data.data, "", "success");


                    }




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
                    }

                });
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


        };

        $scope.timeOperationStarted= function (operation_time,selectedPatient) {
            //var ward_type=wards.ward_type;
            if (angular.isDefined(operation_time)==false) {
                return sweetAlert("Enter Starting Operation Time", "", "error");
            }
            else if (angular.isDefined(selectedPatient)==false) {
                return sweetAlert("Please Enter Patient Selected", "", "error");
            } else{
                 var dataToPost={"erasor":0,"admission_id":selectedPatient.admission_id,"patient_id":selectedPatient.patient_id,
                    "start_time":operation_time.start,"end_time":operation_time.end,"nurse_id":user_id};
                $http.post('/api/saveOpTimer',dataToPost).then(function(data) {

                    if(data.data.status ==0){

                        sweetAlert(data.data.data, "", "error");
                    }else{
                         sweetAlert(data.data.data, "", "success");


                    }




                });

            }


        };


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

        var ward_types=[];
        $scope.showSearchWardTypes= function (searchKey) {

            $http.get('/api/searchWardTypes/'+searchKey).then(function(data) {
                ward_types=data.data;

            });
            return ward_types;
        }


        var ward_nurses=[];
        var nurse_name=[];

        $scope.nurseWard= function (searchKey) {
            var postData={"searchKey":searchKey,"facility_id":facility_id}
            $http.post('/api/searchWardNurses',postData).then(function(data) {
                ward_nurses=data.data;
            });
            return ward_nurses;
        }

        $scope.nurseName= function (searchKey) {
            var postData={"searchKey":searchKey,"facility_id":facility_id}
            $http.post('/api/searchNurseName',postData).then(function(data) {
                nurse_name=data.data;
            });
            return nurse_name;
        };
		
				
		$scope.selectedNurses= function (nurse) {
            $scope.selectedNurse=nurse;
			//console.log($scope.selectedNurse);
			var postData={nurse_id:nurse.user_id};
			
			$http.post('/api/selectedNurse',postData).then(function(data) {
				$scope.nurseAssignes=data.data;
                if(data.data.length ==0){
                    sweetAlert(nurse.name+" not assigned to any ward", "", "error");
                }
            });
			
        }; 

$scope.changeNurseStatus = function(nurseAssign){

			 var dataPost={user_id:nurseAssign.user_id,on_off:nurseAssign.deleted,id:nurseAssign.id,nurse_id:nurseAssign.nurse_id,ward_id:nurseAssign.ward_id};
			
			$http.post('/api/changeNurseStatus',dataPost).then(function(data) {

                var postData={nurse_id:nurseAssign.nurse_id};
                console.log(postData)
                $http.post('/api/selectedNurse',postData).then(function(data) {
                    $scope.nurseAssignes=data.data;


                });
            });

        };

		
        $scope.addNurse= function (ward) {
            var ward_id=ward.ward_id;
            var nurse_id=$scope.selectedNurse.user_id;
            var postData={"deleted":0,"ward_id":ward_id,"nurse_id":nurse_id,"facility_id":facility_id}
            $http.post('/api/addNurse',postData).then(function(data) {
                if(data.data.status ==0){
                    sweetAlert(data.data.data, "", "error");
                }else{
                     sweetAlert(data.data.data, "", "success");
    }
            });

        };

        var ward_classes=[];
        $scope.showSearchWardClass= function (searchKey) {

            $http.get('/api/getWardClasses/'+searchKey).then(function(data) {
                ward_classes=data.data;

            });
            return ward_classes;
        };


        var beds=[];
        $scope.showSearchBedTypes= function (searchKey) {
            $http.get('/api/searchBedTypes/'+searchKey).then(function(data) {
                beds=data.data;
            });
            return beds;
        }


        $scope.getOutPutTypes= function () {
            $http.get('/api/getOutPutTypes').then(function(data) {
                $scope.getOutPutTypes=data.data;

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
            //console.log(selectedPatient);
            var admission_id=selectedPatient.admission_id;
            var patient_id=selectedPatient.patient_id;
            var item_id=selectedPatient.item_id;
            var item_name=selectedPatient.item_name;
            var visit_date_id=selectedPatient.visit_date_id;
            var associateHistory={"item_name":item_name,"user_id":user_id,"facility_id":facility_id,"item_id":item_id,"visit_date_id":visit_date_id,"patient_id":patient_id,"history_type":'ASSOCIATE HISTORY',"admission_id":selectedPatient.admission_id,"request_id":selectedPatient.id,
                "medical":associate_history.medical,"surgical":associate_history.surgical,
                "anaesthetic":associate_history.anaesthetic,"nurse_id":user_id};
            $http.post('/api/saveAssociateHistory',associateHistory).then(function(data) {

                if(data.data.status ==0){

                    sweetAlert(data.data.data, "", "error");
                }else{

                    sweetAlert(data.data.data, "", "success");
                }
            });

        };


        $scope.savePastProceduresHistory= function (associate_history,selectedPatient) {
            if(angular.isDefined(selectedPatient)==false){

                return sweetAlert("Please Select the Patient From list on the Left panel", "", "error");
            }
            else if(angular.isDefined(associate_history)==false){

                return sweetAlert("Please Enter History Records or NILL", "", "error");
            }
            //console.log(selectedPatient);
            var admission_id=selectedPatient.admission_id;
            var patient_id=selectedPatient.patient_id;
            var item_id=selectedPatient.item_id;
            var item_name=selectedPatient.item_name;
            var visit_date_id=selectedPatient.visit_date_id;
            var anethetic=associate_history.anethetic;
            var associateHistory={"item_name":item_name,"user_id":user_id,"facility_id":facility_id,"item_id":item_id,"visit_date_id":visit_date_id,"patient_id":patient_id,"history_type":'PAST HISTORY',"admission_id":selectedPatient.admission_id,"request_id":selectedPatient.id,
                "medical":associate_history.medical,"surgical":associate_history.surgical,
                "anaesthetic":associate_history.anaesthetic,"nurse_id":user_id};
            $http.post('/api/saveAssociateHistory',associateHistory).then(function(data) {

                if(data.data.status ==0){

                    sweetAlert(data.data.data, "", "error");
                }else{

                    sweetAlert(data.data.data, "", "success");
                }
            });

        };

  $scope.saveFamilyHistory= function (associate_history,selectedPatient) {
            if(angular.isDefined(selectedPatient)==false){

                return sweetAlert("Please Select the Patient From list on the Left panel", "", "error");
            }
            else if(angular.isDefined(associate_history)==false){

                return sweetAlert("Please Enter History Records or NILL", "", "error");
            }
            //console.log(selectedPatient);
            var admission_id=selectedPatient.admission_id;
            var patient_id=selectedPatient.patient_id;
            var item_id=selectedPatient.item_id;
            var item_name=selectedPatient.item_name;
            var visit_date_id=selectedPatient.visit_date_id;
            var family_history=associate_history.family_history;
            var associateHistory={"item_name":item_name,"user_id":user_id,"facility_id":facility_id,"item_id":item_id,"visit_date_id":visit_date_id,"patient_id":patient_id,"history_type":'FAMILY HISTORY',"admission_id":selectedPatient.admission_id,"request_id":selectedPatient.id,
                "descriptions":family_history,
               "nurse_id":user_id};
            $http.post('/api/saveAssociateHistory',associateHistory).then(function(data) {

                if(data.data.status ==0){

                    sweetAlert(data.data.data, "", "error");
                }else{

                    sweetAlert(data.data.data, "", "success");
                }
            });

        };

  $scope.saveSocialAnetheticHistory= function (associate_history,selectedPatient) {
            if(angular.isDefined(selectedPatient)==false){

                return sweetAlert("Please Select the Patient From list on the Left panel", "", "error");
            }
            else if(angular.isDefined(associate_history)==false){

                return sweetAlert("Please Enter History Records or NILL", "", "error");
            }
            //console.log(selectedPatient);
            var admission_id=selectedPatient.admission_id;
            var patient_id=selectedPatient.patient_id;
            var item_id=selectedPatient.item_id;
            var item_name=selectedPatient.item_name;
            var visit_date_id=selectedPatient.visit_date_id;
            var social_history=associate_history.social_history;
            var associateHistory={"item_name":item_name,"user_id":user_id,"facility_id":facility_id,"item_id":item_id,"visit_date_id":visit_date_id,"patient_id":patient_id,"history_type":'SOCIAL HISTORY',"admission_id":selectedPatient.admission_id,"request_id":selectedPatient.id,
                "descriptions":social_history,
               "nurse_id":user_id};
            $http.post('/api/saveAssociateHistory',associateHistory).then(function(data) {

                if(data.data.status ==0){

                    sweetAlert(data.data.data, "", "error");
                }else{

                    sweetAlert(data.data.data, "", "success");
                }
            });

        };

  $scope.saveAllergiesAnetheticHistory= function (associate_history,selectedPatient) {
            if(angular.isDefined(selectedPatient)==false){
                return sweetAlert("Please Select the Patient From list on the Left panel", "", "error");
            }
            else if(angular.isDefined(associate_history)==false){
                return sweetAlert("Please Enter History Records or NILL", "", "error");
            }
            var admission_id=selectedPatient.admission_id;
            var patient_id=selectedPatient.patient_id;
            var item_id=selectedPatient.item_id;
            var item_name=selectedPatient.item_name;
            var visit_date_id=selectedPatient.visit_date_id;
            var allergies=associate_history.allergies;
            var associateHistory={"item_name":item_name,"user_id":user_id,"facility_id":facility_id,"item_id":item_id,"visit_date_id":visit_date_id,"patient_id":patient_id,"history_type":'ALLERGIES',"admission_id":selectedPatient.admission_id,"request_id":selectedPatient.id,
                "descriptions":allergies,"nurse_id":user_id};
            $http.post('/api/saveAssociateHistory',associateHistory).then(function(data) {

                if(data.data.status ==0){
                    sweetAlert(data.data.data, "", "error");
                }else{
                    sweetAlert(data.data.data, "", "success");
                }
            });

        };


  $scope.savePhysicalAnetheticHistory= function (associate_history,selectedPatient) {
            if(angular.isDefined(selectedPatient)==false){
                return sweetAlert("Please Select the Patient From list on the Left panel", "", "error");
            }
            else if(angular.isDefined(associate_history)==false){
                return sweetAlert("Please Selected Information Type", "", "error");
            }
            var admission_id=selectedPatient.admission_id;
            var patient_id=selectedPatient.patient_id;
            var item_id=selectedPatient.item_id;
            var item_name=selectedPatient.item_name;
            var visit_date_id=selectedPatient.visit_date_id;
            var descriptions=associate_history.descriptions;
            var system_type=associate_history.system_type;
            var associateHistory={"remarks":"PHYSICAL EXAMINATION","item_name":item_name,"user_id":user_id,"facility_id":facility_id,"item_id":item_id,"visit_date_id":visit_date_id,"patient_id":patient_id,"history_type":system_type,"admission_id":selectedPatient.admission_id,"request_id":selectedPatient.id,
                "descriptions":descriptions,"nurse_id":user_id};
            $http.post('/api/saveAssociateHistory',associateHistory).then(function(data) {

                if(data.data.status ==0){
                    sweetAlert(data.data.data, "", "error");
                }else{
                    sweetAlert(data.data.data, "", "success");
                }
            });

        };



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

        };

        $scope.saveLaboratoryAnetheticHistory= function (associate_history,selectedPatient) {
            if(angular.isDefined(selectedPatient)==false){
                return sweetAlert("Please Select the Patient From list on the Left panel", "", "error");
            }
            else if(angular.isDefined(associate_history)==false){
                return sweetAlert("Please Enter History Records or NILL", "", "error");
            }
            var admission_id=selectedPatient.admission_id;
            var patient_id=selectedPatient.patient_id;
            var item_id=selectedPatient.item_id;
            var item_name=selectedPatient.item_name;
            var visit_date_id=selectedPatient.visit_date_id;
            var laboratory_status=associate_history.laboratory_status;
            var associateHistory={"item_name":item_name,"user_id":user_id,"facility_id":facility_id,"item_id":item_id,"visit_date_id":visit_date_id,"patient_id":patient_id,"history_type":'Laboratory Status',"admission_id":selectedPatient.admission_id,"request_id":selectedPatient.id,
                "descriptions":laboratory_status,"nurse_id":user_id};
            $http.post('/api/saveAssociateHistory',associateHistory).then(function(data) {

                if(data.data.status ==0){

                    sweetAlert(data.data.data, "", "error");
                }else{

                    sweetAlert(data.data.data, "", "success");
                }
            });

        };

        $scope.saveNutritionAnetheticHistory= function (associate_history,selectedPatient) {
            if(angular.isDefined(selectedPatient)==false){
                return sweetAlert("Please Select the Patient From list on the Left panel", "", "error");
            }
            else if(angular.isDefined(associate_history)==false){
                return sweetAlert("Please Enter History Records or NILL", "", "error");
            }
            var admission_id=selectedPatient.admission_id;
            var patient_id=selectedPatient.patient_id;
            var item_id=selectedPatient.item_id;
            var item_name=selectedPatient.item_name;
            var visit_date_id=selectedPatient.visit_date_id;
            var nutrition_status=associate_history.nutrition_status;
            var associateHistory={"item_name":item_name,"user_id":user_id,"facility_id":facility_id,"item_id":item_id,"visit_date_id":visit_date_id,"patient_id":patient_id,"history_type":'Nutrition Status',"admission_id":selectedPatient.admission_id,"request_id":selectedPatient.id,
                "descriptions":nutrition_status,"nurse_id":user_id};
            $http.post('/api/saveAssociateHistory',associateHistory).then(function(data) {

                if(data.data.status ==0){

                    sweetAlert(data.data.data, "", "error");
                }else{

                    sweetAlert(data.data.data, "", "success");
                }
            });

        };

        $scope.savePhysicalStatusAnetheticHistory= function (associate_history,selectedPatient) {
            if(angular.isDefined(selectedPatient)==false){
                return sweetAlert("Please Select the Patient From list on the Left panel", "", "error");
            }
            else if(angular.isDefined(associate_history)==false){
                return sweetAlert("Please Enter History Records or NILL", "", "error");
            }
            var admission_id=selectedPatient.admission_id;
            var patient_id=selectedPatient.patient_id;
            var item_id=selectedPatient.item_id;
            var item_name=selectedPatient.item_name;
            var visit_date_id=selectedPatient.visit_date_id;
            var physical_status=associate_history.physical_status;
            var associateHistory={"item_name":item_name,"user_id":user_id,"facility_id":facility_id,"item_id":item_id,"visit_date_id":visit_date_id,"patient_id":patient_id,"history_type":'Physical Status',"admission_id":selectedPatient.admission_id,"request_id":selectedPatient.id,
                "descriptions":physical_status,"nurse_id":user_id};
            $http.post('/api/saveAssociateHistory',associateHistory).then(function(data) {

                if(data.data.status ==0){

                    sweetAlert(data.data.data, "", "error");
                }else{

                    sweetAlert(data.data.data, "", "success");
                }
            });

        };

        $scope.saveLastOralIntakeAnetheticHistory= function (associate_history,selectedPatient) {
            if(angular.isDefined(selectedPatient)==false){
                return sweetAlert("Please Select the Patient From list on the Left panel", "", "error");
            }
            else if(angular.isDefined(associate_history)==false){
                return sweetAlert("Please Enter History Records or NILL", "", "error");
            }
            var admission_id=selectedPatient.admission_id;
            var patient_id=selectedPatient.patient_id;
            var item_id=selectedPatient.item_id;
            var item_name=selectedPatient.item_name;
            var visit_date_id=selectedPatient.visit_date_id;
            var last_oral_intake=associate_history.last_oral_intake;
            var associateHistory={"item_name":item_name,"user_id":user_id,"facility_id":facility_id,"item_id":item_id,"visit_date_id":visit_date_id,"patient_id":patient_id,"history_type":'Last Oral Intake',"admission_id":selectedPatient.admission_id,"request_id":selectedPatient.id,
                "descriptions":last_oral_intake,"nurse_id":user_id};
            $http.post('/api/saveAssociateHistory',associateHistory).then(function(data) {

                if(data.data.status ==0){

                    sweetAlert(data.data.data, "", "error");
                }else{

                    sweetAlert(data.data.data, "", "success");
                }
            });

        };

        $scope.savePreAnaetheticOrder= function (associate_history,selectedPatient) {
            if(angular.isDefined(selectedPatient)==false){
                return sweetAlert("Please Select the Patient From list on the Left panel", "", "error");
            }
            else if(angular.isDefined(associate_history)==false){
                return sweetAlert("Please Enter History Records or NILL", "", "error");
            }
            var admission_id=selectedPatient.admission_id;
            var patient_id=selectedPatient.patient_id;
            var item_id=selectedPatient.item_id;
            var item_name=selectedPatient.item_name;
            var visit_date_id=selectedPatient.visit_date_id;
            var PreAnaetheticOrder=associate_history.PreAnaetheticOrder;
            var associateHistory={"item_name":item_name,"user_id":user_id,"facility_id":facility_id,"item_id":item_id,"visit_date_id":visit_date_id,"patient_id":patient_id,"history_type":'Pre Anaethetic Order',"admission_id":selectedPatient.admission_id,"request_id":selectedPatient.id,
                "descriptions":PreAnaetheticOrder,"nurse_id":user_id};
            $http.post('/api/saveAssociateHistory',associateHistory).then(function(data) {

                if(data.data.status ==0){
                    sweetAlert(data.data.data, "", "error");
                }else{
                    sweetAlert(data.data.data, "", "success");
                }
            });

        };

        $scope.saveanaetheticTechnique= function (associate_history,selectedPatient) {
            if(angular.isDefined(selectedPatient)==false){
                return sweetAlert("Please Select the Patient From list on the Left panel", "", "error");
            }
            else if(angular.isDefined(associate_history)==false){
                return sweetAlert("Please Enter History Records or NILL", "", "error");
            }
            var admission_id=selectedPatient.admission_id;
            var patient_id=selectedPatient.patient_id;
            var item_id=selectedPatient.item_id;
            var item_name=selectedPatient.item_name;
            var visit_date_id=selectedPatient.visit_date_id;
            var anaetheticTechnique=associate_history.anaetheticTechnique;
            var associateHistory={"item_name":item_name,"user_id":user_id,"facility_id":facility_id,"item_id":item_id,"visit_date_id":visit_date_id,"patient_id":patient_id,"history_type":'Anaethetic Technique',"admission_id":selectedPatient.admission_id,"request_id":selectedPatient.id,
                "descriptions":anaetheticTechnique,"nurse_id":user_id};
            $http.post('/api/saveAssociateHistory',associateHistory).then(function(data) {

                if(data.data.status ==0){
                    sweetAlert(data.data.data, "", "error");
                }else{
                    sweetAlert(data.data.data, "", "success");
                }
            });

        };



        //saveTeethStatus
        $scope.addDentalInfo= function (dental,selectedPatient,patientAdmited) {
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

               var teeth_status={"erasor":0,"admission_id":patientAdmited.admission_id,
                    "dental_id":selectedPatient.tooth_id,"dental_status":dental.dental_status,
                    "css_class":css_class,"other_information":information_category,"nurse_id":user_id};

                $http.post('/api/saveTeethStatus',teeth_status).then(function(data) {
                    if(data.data.status ==0){

                        sweetAlert(data.data.data, "", "error");
                    }else{
                         $scope.dentals=false;
                         sweetAlert(data.data.data, "", "success");
                    }

                });
            }

        };


        $scope.saveDentalStatus= function (tooth_id,tooth_number,selectedPatient) {
            if(angular.isDefined(selectedPatient)==false){
                return sweetAlert("Select the Patient Before ENTERING DENTAL STATUS", "", "error");
            }
            else{
                $scope.selectedPatient=selectedPatient;
                $scope.dentals={"tooth_id":tooth_id,"toothStatus":true,"tooth_number":tooth_number};

                //console.log($scope.dentals);
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
  //console.log(selectedPatient);
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
                var admission_id=selectedPatient.admission_id;
                $http.get('/api/getTeethStatusFromPatientAbove/'+admission_id).then(function(data) {
                    $scope.teeth_patientsAboves=data.data;

                });

                $http.get('/api/getTeethStatusFromPatientBelow/'+admission_id).then(function(data) {
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

        $scope.getIntakeFluid= function () {
            $http.get('/api/getIntakeSolutions').then(function(data) {
                $scope.getIntakeFluids=data.data;
                ////console.log($getVitals);
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


        $scope.addWard= function (wards,ward_class) {
            //var ward_type=wards.ward_type;
            ////console.log(ward_class);
            if (angular.isDefined(wards)==false) {
                return sweetAlert("Please Enter WARD NAME", "", "error");
            }
            else if (angular.isDefined(wards.ward_type)==false) {
                return sweetAlert("Please Enter WARD TYPE", "", "error");
            }

            else if (angular.isDefined(ward_class)==false) {
                return sweetAlert("Please Enter WARD CLASS", "", "error");
            }

            else if (angular.isDefined(ward_class.ward_class_id)==false) {
                return sweetAlert("Please Enter WARD CLASS", "", "error");
            }

            else{

                $http.post('/api/saveWards',{"ward_class_id":ward_class.ward_class_id.item_id,"ward_name":wards.ward_name,"ward_type_id":wards.ward_type.id,"ward_type_name":wards.ward_type.ward_type_name,"facility_id":facility_id}).then(function(data) {

                    if(data.data.status ==0){
                        $scope.wards = null;
                        sweetAlert(data.data.data, "", "error");
                    }else{
                        $scope.wards = null;
                        sweetAlert(data.data.data, "", "success");


                    }




                });

            }


        }


        $scope.addBed= function (wards,ward_id) {
            //var ward_type=wards.ward_type;
            if (angular.isDefined(wards)==false) {
                return sweetAlert("Please Enter BED NUMBER "+ward_id, "", "error");
            }
            else if (angular.isDefined(wards.bed_type)==false) {
                return sweetAlert("Please Enter BED TYPE "+ward_id, "", "error");
            }
            else{
                //console.log(wards.bed_type);
                $http.post('/api/saveBeds',{"bed_name":wards.bed_number,"bed_type_id":wards.bed_type.id,"ward_id":ward_id,"facility_id":facility_id,"eraser":1}).then(function(data) {
                    if(data.data.status ==0){

                        sweetAlert(data.data.data, "", "error");
                    }else{
                        // $scope.wards = null;
                        sweetAlert(data.data.data, "", "success");

                    }




                });

            }


        }



        var beds_number=0;
        var beds=[];
        var wards=[];
        $scope.getWardDetails= function (getWard) {
var ward_id=getWard.ward_id;
           
//console.log(getWard);
          

               
$mdDialog.show({                 
                        controller: function ($scope) {

                             $http.get('/api/getWardOneInfo/'+ward_id).then(function(data){
                $scope.wards=data.data[0];
                 $scope.beds=data.data[0];
                $scope.beds_number=data.data[1][0];
                
            });

                           $scope.ward_name=getWard.ward_name;
                            $scope.ward_id=getWard.ward_id;
                            $scope.cancel = function () {
                                 $mdDialog.hide();
                            };
                        },
                        templateUrl: '/views/modules/nursing_care/manageWardBeds.html',
                        parent: angular.element(document.body),
                        clickOutsideToClose: false,
                         fullscreen: $scope.customFullscreen // Only for -xs, -sm breakpoints.
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
                //console.dir($scope.AdmissionNotes);
            });

        }
        $scope.giveBed= function (admissionInfoData,patientPaticulars,bed_id,last_name,ward_id,admission_id,bed_available) {

            // sweetAlert(bed_id+' '+bed_available, "", "success");
            //console.log(admissionInfoData);

            $http.post('/api/giveBed',{user_id:user_id,facility_id:facility_id,visit_date_id:admissionInfoData.visit_date_id,"bed_id":bed_id,"ward_id":ward_id,admission_status_id:2,"admission_id":admission_id,"bed_available":bed_available}).then(function(data) {
                $scope.giveBeds=data.data;
                //console.log($scope.giveBeds);

                if(data.data.status ==0){
                    sweetAlert(data.data.data, "", "error");
                }else{
                     $scope.cancel = function () {
                                 $mdDialog.hide();
                            };
                    $scope.cancel();
                    var bed_details=bed_available+ ' SUCCESSFULLY ASSIGNED TO '+last_name;
                    sweetAlert(bed_details, "", "success");

                }


            });


        }


        $scope.getAdmPatient= function (admitted) {
            $scope.selectedPatient=admitted;
            //console.log($scope.selectedPatient);
        }

        $scope.getPatientsSentToTheatre= function () {

            $http.get('/api/getPatientSentToTheatre').then(function(data){
                $scope.patientsSentToTheatres=data.data;
            });
        }


      $scope.saveConsent=function(patientPaticulars,operation){
          if (angular.isDefined(operation)==false) {
                return sweetAlert("Please enter relative information", "", "error");
            }
            else if(angular.isDefined(operation.relationship)==false){
                return sweetAlert("Please enter relationship from the sugestions", "", "error");
             }
              else if(angular.isDefined(operation.signed_date)==false){
                return sweetAlert("Select date this consent signed", "", "error");
             }
             //console.log(patientPaticulars);
               var relationshipsID=operation.relationship.id;
               var next_kin=operation.next_of_kin;
               var dateSigned=operation.signed_date;
               var admission_id=patientPaticulars.admission_id;
               var visit_date_id=patientPaticulars.visit_date_id;
               var patient_id=patientPaticulars.patient_id;
               var item_id=patientPaticulars.item_id;
               var postData={consent:1,"item_id":item_id,"patient_id":patient_id,"visit_date_id":visit_date_id,"relative_name":next_kin,"relationshipsID":relationshipsID,"dateSigned":dateSigned,"user_id":user_id,"facility_id":facility_id,"admission_id":admission_id};
               $http.post('/api/saveConsent',postData).then(function(data) {  
                  if(data.data.status ==0){
					 sweetAlert(data.data.data, "", "error");
				  }
			      else{
                      $scope.operation=null;
                     sweetAlert(data.data.data, "", "success");
				  }                     
  
              });

      };
        $scope.transferDummyBed=function(bedName,patientPaticulars,admissions){
           if (angular.isDefined(bedName)==false) {
                return sweetAlert("Please Enter Dummy Bed number or Floor location", "", "error");
            }
            else{
             var ward_id=admissions.ward_id;
             var old_bed_id=admissions.bed_id;
             var bed_name=bedName;
             var bed_type_id=4;
             var admission_id=admissions.admission_id;
             var postData={"old_bed_id":old_bed_id,"changeBed":1,"facility_id":facility_id,"admission_id":admission_id,"ward_id":ward_id,"bed_name":bed_name,"bed_type_id":bed_type_id,"eraser":0};
              $http.post('/api/saveDummyBed',postData).then(function(data) {  
                  if(data.data.status ==0){
					 sweetAlert(data.data.data, "", "error");
				  }
			      else{
                       $scope.setTabAdmission();
                     sweetAlert(data.data.data, "", "success");
				  }                     
  
              });

            }
           
     };












     $scope.dummyBed=function(bedName,patientPaticulars,admissions){
           if (angular.isDefined(bedName)==false) {
                return sweetAlert("Please Enter Dummy Bed number or Floor location", "", "error");
            }
            else{
             var ward_id=admissions.ward_id;
             var bed_name=bedName;
             var bed_type_id=4;
             var admission_id=admissions.admission_id;
             var postData={visit_date_id:admissions.visit_date_id,admission_status_id:2,"facility_id":facility_id,user_id:user_id,"admission_id":admission_id,"ward_id":ward_id,"bed_name":bed_name,"bed_type_id":bed_type_id,"eraser":0};
              $http.post('/api/saveDummyBed',postData).then(function(data) {  
                  if(data.data.status ==0){
					 sweetAlert(data.data.data, "", "error");
				  }
			      else{
                     sweetAlert(data.data.data, "", "success");
				  }                     
  
              });

            }
           
     };

      $scope.getRelationships = function(searchKey) {

            $http.get('/api/getRelationships/' + searchKey).then(function(data) {
                relationships = data.data;
            });
            return relationships;
        }


     $scope.getFormForRelative=function(consent){
  $scope.showRelativeForm=false;
  if(angular.isDefined(consent)==false){
       $scope.showRelativeForm=true;            
  } else if(consent == false){    
       $scope.showRelativeForm=true;     
  } else{
       $scope.showRelativeForm=false;
     
  }
  
     };

        $scope.getAdmission= function (admissionInfoData,patient,ward_id,admission_id) {


   $http.post('/api/getInstructions',{visit_date_id:admissionInfoData.visit_date_id,"patient_id":admissionInfoData.patient_id,"ward_id":admissionInfoData.ward_id}).then(function(data) {
                  $mdDialog.show({                 
                        controller: function ($scope) {
                            $scope.patientPaticulars=data.data[0];
                               $scope.admissionInfoData=data.data[1][0];
                                $scope.beds=data.data[2];
                                $scope.cancel = function () {
                                 $mdDialog.hide();
                            };
                        },
                        templateUrl: '/views/modules/nursing_care/bedAllocation.html',
                        parent: angular.element(document.body),
                        clickOutsideToClose: false,
                         fullscreen: $scope.customFullscreen // Only for -xs, -sm breakpoints.
                    });


            });

        };

         $scope.data = [ "Item 1", "Item 2", "Item 3", "Item 4"]
  $scope.toggle = {};

        $scope.saveConfirmationForOperation= function (associate_history,selectedPatient) {
            if(angular.isDefined(selectedPatient)==false){

                return sweetAlert("Please Select the Patient From list on the Left panel", "", "error");
            }
            else if(angular.isDefined(associate_history)==false){

                return sweetAlert("Please Enter History Records or NILL", "", "error");
            }
            //console.log(selectedPatient);
            var admission_id=selectedPatient.admission_id;
            var patient_id=selectedPatient.patient_id;
            var item_id=selectedPatient.item_id;
            var item_name=selectedPatient.item_name;
            var visit_date_id=selectedPatient.visit_date_id;
            var confirmation=associate_history.confirmation;
            var associateHistory={"item_name":item_name,"user_id":user_id,"facility_id":facility_id,"item_id":item_id,"visit_date_id":visit_date_id,"patient_id":patient_id,"history_type":'CONFIRMATION',"admission_id":selectedPatient.admission_id,"request_id":selectedPatient.id,
                "descriptions":confirmation,
                "nurse_id":user_id};
            $http.post('/api/saveAssociateHistory',associateHistory).then(function(data) {

                if(data.data.status ==0){

                    sweetAlert(data.data.data, "", "error");
                }else{

                    sweetAlert(data.data.data, "", "success");
                }
            });

        };

        $scope.getAnaethesiaListApproved=function(){
            $http.get('/api/getAnaethesiaListApproved/'+facility_id).then(function(data) {
                $scope.getAnaethesiaListApproveds=data.data;
            });
        };


        $scope.saveGivenDrug=function (patient,nursingcare) {
            if (angular.isDefined(nursingcare)==false) {
                return sweetAlert("Provide Pre Medication Please", "", "error");
            }
            var drug=nursingcare.drug.item_name;
            var drug_time=nursingcare.time_given;
            var admission_id=patient.admission_id;
            var patient_id=patient.patient_id;
            var item_id=patient.item_id;
            var visit_date_id=patient.visit_date_id;
            var dataToPost={visit_date_id:visit_date_id,descriptions:drug,history_type:"PRE MEDICATION",remarks:drug_time,"admission_id":admission_id,"patient_id":patient_id,"item_id":item_id,"user_id":user_id,"facility_id":facility_id};
            $http.post('/api/saveGivenDrug',dataToPost).then(function(data) {
                if(data.data.status==1){
                    return sweetAlert(data.data.data, "", "success");
                }else if(data.data.status==0){
                    return sweetAlert(data.data.data, "", "error");
                }
            });
        };

        $scope.savePreOpCondition=function (patient,PreOPCondition) {
            if (angular.isDefined(PreOPCondition)==false) {
                return sweetAlert("Provide Pre Medication Please", "", "error");
            } else if (angular.isDefined(PreOPCondition.case)==false) {
                return sweetAlert("Provide Case Type for This Operation", "", "error");
            }
            else if (angular.isDefined(PreOPCondition.condition)==false) {
                return sweetAlert("Provide Pre Operation Condition", "", "error");
            }
            var case_op=PreOPCondition.case;
            var condition=PreOPCondition.condition;
            var admission_id=patient.admission_id;
            var patient_id=patient.patient_id;
            var item_id=patient.item_id;
            var visit_date_id=patient.visit_date_id;
            var dataToPost={visit_date_id:visit_date_id,descriptions:case_op,history_type:"PRE OPERATION",remarks:condition,"admission_id":admission_id,"patient_id":patient_id,"item_id":item_id,"user_id":user_id,"facility_id":facility_id};
            $http.post('/api/saveGivenDrug',dataToPost).then(function(data) {
                if(data.data.status==1){
                    return sweetAlert(data.data.data, "", "success");
                }else if(data.data.status==0){
                    return sweetAlert(data.data.data, "", "error");
                }
            });
        };

        $scope.getSelectedVitalSign=function (vitalSign) {
            $scope.vitalSignId=vitalSign.id;
        };

        $scope.getListFromTheatre=function () {
            $http.get('/api/getListFromTheatres/'+facility_id).then(function(data) {
                $scope.getListFromTheatres = data.data;
            });
        };
        
        $scope.saveVitalSigns=function (patient,vitalSignValue) {
            if (angular.isDefined(vitalSignValue)==false) {
                return sweetAlert("Provide Value for Vital Signs", "", "error");
            }
            var admission_id=patient.admission_id;
            var patient_id=patient.patient_id;
            var item_id=patient.item_id;
            var visit_date_id=patient.visit_date_id;
            var dataToPost={visiting_id:visit_date_id,vital_sign_value:vitalSignValue,registered_by:user_id,vital_sign_id:$scope.vitalSignId};
            $http.post('/api/saveVitalSigns',dataToPost).then(function(data) {
                if(data.data.status==1){
                    return sweetAlert(data.data.data, "", "success");
                }else if(data.data.status==0){
                    return sweetAlert(data.data.data, "", "error");
                }
            });
        };
        $scope.saveRespirationOperation=function (patient,Respiration) {
            if (angular.isDefined(Respiration)==false) {
                return sweetAlert("Provide Respiration Status", "", "error");
            }
            var Respiration=Respiration.Respiration;
            var admission_id=patient.admission_id;
            var patient_id=patient.patient_id;
            var item_id=patient.item_id;
            var visit_date_id=patient.visit_date_id;
            var dataToPost={visit_date_id:visit_date_id,descriptions:Respiration,history_type:"RESPIRATION",remarks:Respiration,"admission_id":admission_id,"patient_id":patient_id,"item_id":item_id,"user_id":user_id,"facility_id":facility_id};
            $http.post('/api/saveGivenDrug',dataToPost).then(function(data) {
                if(data.data.status==1){
                    return sweetAlert(data.data.data, "", "success");
                }else if(data.data.status==0){
                    return sweetAlert(data.data.data, "", "error");
                }
            });
        };

        $scope.saveDuration=function (patient,nursingcare) {
            if (angular.isDefined(nursingcare)==false) {
                return sweetAlert("Provide Duration for Anaethesia and Operation", "", "error");
            }
            else if (angular.isDefined(nursingcare.duration_anaesthesia)==false) {
                return sweetAlert("Provide Duration for Anaethesia", "", "error");
            }
            else if (angular.isDefined(nursingcare.duration_operartion)==false) {
                return sweetAlert("Provide Duration for Operation", "", "error");
            }
            var admission_id=patient.admission_id;
            var patient_id=patient.patient_id;
            var item_id=patient.item_id;
            var visit_date_id=patient.visit_date_id;
            var duration_operartion=nursingcare.duration_operartion;
            var duration_anaesthesia=nursingcare.duration_anaesthesia;
            var dataToPost={visit_date_id:visit_date_id,descriptions:duration_anaesthesia,history_type:"DURATION",remarks:duration_operartion,"admission_id":admission_id,"patient_id":patient_id,"item_id":item_id,"user_id":user_id,"facility_id":facility_id};
            $http.post('/api/saveGivenDrug',dataToPost).then(function(data) {
                if(data.data.status==1){
                    return sweetAlert(data.data.data, "", "success");
                }else if(data.data.status==0){
                    return sweetAlert(data.data.data, "", "error");
                }
            });
        };

        $scope.saveBloodLoss=function (patient,nursingcare) {
            if (angular.isDefined(nursingcare)==false) {
                return sweetAlert("Provide Remarks for Blood Loss", "", "error");
            }
            else if (angular.isDefined(nursingcare.blood_loss)==false) {
                return sweetAlert("Provide Remarks for Blood Loss", "", "error");
            }

            var admission_id=patient.admission_id;
            var patient_id=patient.patient_id;
            var item_id=patient.item_id;
            var visit_date_id=patient.visit_date_id;
            var blood_loss=nursingcare.blood_loss;
            var dataToPost={visit_date_id:visit_date_id,descriptions:blood_loss,history_type:"BLOOD LOSS","admission_id":admission_id,"patient_id":patient_id,"item_id":item_id,"user_id":user_id,"facility_id":facility_id};
            $http.post('/api/saveGivenDrug',dataToPost).then(function(data) {
                if(data.data.status==1){
                    return sweetAlert(data.data.data, "", "success");
                }else if(data.data.status==0){
                    return sweetAlert(data.data.data, "", "error");
                }
            });
        };


        $scope.saveFluidGiven=function (patient,nursingcare) {
            if (angular.isDefined(nursingcare)==false) {
                return sweetAlert("Provide Remarks for Blood Loss", "", "error");
            }
            else if (angular.isDefined(nursingcare.fluid_given)==false) {
                return sweetAlert("Provide Remarks for fluid Given", "", "error");
            }

            var admission_id=patient.admission_id;
            var patient_id=patient.patient_id;
            var item_id=patient.item_id;
            var visit_date_id=patient.visit_date_id;
            var fluid_given=nursingcare.fluid_given;
            var dataToPost={visit_date_id:visit_date_id,descriptions:fluid_given,history_type:"FLUID GIVEN","admission_id":admission_id,"patient_id":patient_id,"item_id":item_id,"user_id":user_id,"facility_id":facility_id};
            $http.post('/api/saveGivenDrug',dataToPost).then(function(data) {
                if(data.data.status==1){
                    return sweetAlert(data.data.data, "", "success");
                }else if(data.data.status==0){
                    return sweetAlert(data.data.data, "", "error");
                }
            });
        };


        $scope.saveComplications=function (patient,nursingcare) {
            if (angular.isDefined(nursingcare)==false) {
                return sweetAlert("Provide Remarks for Complications", "", "error");
            }
            else if (angular.isDefined(nursingcare.complication)==false) {
                return sweetAlert("Provide Remarks for complication", "", "error");
            }

            var admission_id=patient.admission_id;
            var patient_id=patient.patient_id;
            var item_id=patient.item_id;
            var visit_date_id=patient.visit_date_id;
            var complication=nursingcare.complication;
            var dataToPost={visit_date_id:visit_date_id,descriptions:complication,history_type:"COMPLICATIONS","admission_id":admission_id,"patient_id":patient_id,"item_id":item_id,"user_id":user_id,"facility_id":facility_id};
            $http.post('/api/saveGivenDrug',dataToPost).then(function(data) {
                if(data.data.status==1){
                    return sweetAlert(data.data.data, "", "success");
                }else if(data.data.status==0){
                    return sweetAlert(data.data.data, "", "error");
                }
            });
        };

        $scope.saveOPFindings=function (patient,nursingcare) {
            if (angular.isDefined(nursingcare)==false) {
                return sweetAlert("Provide Operation Findings", "", "error");
            }
            else if (angular.isDefined(nursingcare.operation_findings)==false) {
                return sweetAlert("Provide Remarks for Operation Findings", "", "error");
            }

            var admission_id=patient.admission_id;
            var patient_id=patient.patient_id;
            var item_id=patient.item_id;
            var visit_date_id=patient.visit_date_id;
            var operation_findings=nursingcare.operation_findings;
            var dataToPost={visit_date_id:visit_date_id,descriptions:operation_findings,history_type:"OPERATION FINDINGS","admission_id":admission_id,"patient_id":patient_id,"item_id":item_id,"user_id":user_id,"facility_id":facility_id};
            $http.post('/api/saveGivenDrug',dataToPost).then(function(data) {
                if(data.data.status==1){
                    return sweetAlert(data.data.data, "", "success");
                }else if(data.data.status==0){
                    return sweetAlert(data.data.data, "", "error");
                }
            });
        };


        $scope.saveAnaestheticCompliations=function (patient,nursingcare) {
            if (angular.isDefined(nursingcare)==false) {
                return sweetAlert("Provide Operation Findings", "", "error");
            }
            else if (angular.isDefined(nursingcare.anaethetic_complication)==false) {
                return sweetAlert("Write Anaesthetic Complications", "", "error");
            }

            var admission_id=patient.admission_id;
            var patient_id=patient.patient_id;
            var item_id=patient.item_id;
            var visit_date_id=patient.visit_date_id;
            var anaethetic_complication=nursingcare.anaethetic_complication;
            var dataToPost={visit_date_id:visit_date_id,descriptions:anaethetic_complication,history_type:"ANAESTHETIC COMPLICATIONS","admission_id":admission_id,"patient_id":patient_id,"item_id":item_id,"user_id":user_id,"facility_id":facility_id};
            $http.post('/api/saveGivenDrug',dataToPost).then(function(data) {
                if(data.data.status==1){
                    return sweetAlert(data.data.data, "", "success");
                }else if(data.data.status==0){
                    return sweetAlert(data.data.data, "", "error");
                }
            });
        };


        $scope.endSession=function (patient,nursingcare) {
            if (angular.isDefined(nursingcare)==false) {
                return sweetAlert("Provide Remarks for Discharge", "", "error");
            }
            else if (angular.isDefined(nursingcare.discharge_theatre)==false) {
                return sweetAlert("Provide Remarks for Discharge", "", "error");
            }

            var admission_id=patient.admission_id;
            var patient_id=patient.patient_id;
            var item_id=patient.item_id;
            var visit_date_id=patient.visit_date_id;
            var discharge_theatre=nursingcare.discharge_theatre;
            var dataToPost={visit_date_id:visit_date_id,descriptions:discharge_theatre,history_type:"END SESSION OPERATION","admission_id":admission_id,"patient_id":patient_id,"item_id":item_id,"user_id":user_id,"facility_id":facility_id};
            $http.post('/api/saveGivenDrug',dataToPost).then(function(data) {
                if(data.data.status==1){
                    return sweetAlert(data.data.data, "", "success");
                }else if(data.data.status==0){
                    return sweetAlert(data.data.data, "", "error");
                }
            });
        };

        $scope.endSessionPostFindings=function (patient,nursingcare) {
            if (angular.isDefined(nursingcare)==false) {
                return sweetAlert("Provide Remarks for Remove Patient from Post Operation List", "", "error");
            }
            else if (angular.isDefined(nursingcare.discharge_theatre)==false) {
                return sweetAlert("Provide Remarks for Remove Patient from Post Operation List", "", "error");
            }

            var admission_id=patient.admission_id;
            var patient_id=patient.patient_id;
            var item_id=patient.item_id;
            var visit_date_id=patient.visit_date_id;
            var discharge_theatre=nursingcare.discharge_theatre;
            var dataToPost={visit_date_id:visit_date_id,descriptions:discharge_theatre,history_type:"END SESSION POST OPERATION","admission_id":admission_id,"patient_id":patient_id,"item_id":item_id,"user_id":user_id,"facility_id":facility_id};
            $http.post('/api/saveGivenDrug',dataToPost).then(function(data) {
                if(data.data.status==1){
                    return sweetAlert(data.data.data, "", "success");
                }else if(data.data.status==0){
                    return sweetAlert(data.data.data, "", "error");
                }
            });
        };


        $scope.endSessionRecovery=function (patient,nursingcare) {
            if (angular.isDefined(nursingcare)==false) {
                return sweetAlert("Provide Remarks for exit recovery mode", "", "error");
            }
            else if (angular.isDefined(nursingcare.discharge_theatre)==false) {
                return sweetAlert("Provide Remarks for exit recovery mode", "", "error");
            }

            var admission_id=patient.admission_id;
            var patient_id=patient.patient_id;
            var item_id=patient.item_id;
            var visit_date_id=patient.visit_date_id;
            var discharge_theatre=nursingcare.discharge_theatre;
            var dataToPost={visit_date_id:visit_date_id,descriptions:discharge_theatre,history_type:"END SESSION RECOVERY","admission_id":admission_id,"patient_id":patient_id,"item_id":item_id,"user_id":user_id,"facility_id":facility_id};
            $http.post('/api/saveGivenDrug',dataToPost).then(function(data) {
                if(data.data.status==1){
                    return sweetAlert(data.data.data, "", "success");
                }else if(data.data.status==0){
                    return sweetAlert(data.data.data, "", "error");
                }
            });
        };



        $scope.saveEffects=function (patient,effect) {
            if (angular.isDefined(effect)==false) {
                return sweetAlert("Provide Effect Type", "", "error");
            }
            var effect=effect.effect;
            var admission_id=patient.admission_id;
            var patient_id=patient.patient_id;
            var item_id=patient.item_id;
            var visit_date_id=patient.visit_date_id;
            var dataToPost={visit_date_id:visit_date_id,descriptions:effect,history_type:"EFFECT","admission_id":admission_id,"patient_id":patient_id,"item_id":item_id,"user_id":user_id,"facility_id":facility_id};
            $http.post('/api/saveGivenDrug',dataToPost).then(function(data) {
                if(data.data.status==1){
                    return sweetAlert(data.data.data, "", "success");
                }else if(data.data.status==0){
                    return sweetAlert(data.data.data, "", "error");
                }
            });
        };

        $scope.saveDescriptionsProcedures=function (patient,descriptions) {
            if (angular.isDefined(descriptions)==false) {
                return sweetAlert("Provide Descriptions", "", "error");
            }
            var description=descriptions.description;
            var admission_id=patient.admission_id;
            var patient_id=patient.patient_id;
            var item_id=patient.item_id;
            var visit_date_id=patient.visit_date_id;
            var dataToPost={visit_date_id:visit_date_id,descriptions:description,history_type:"DESCRIPTION OF PROCEDURE","admission_id":admission_id,"patient_id":patient_id,"item_id":item_id,"user_id":user_id,"facility_id":facility_id};
            $http.post('/api/saveGivenDrug',dataToPost).then(function(data) {
                if(data.data.status==1){
                    return sweetAlert(data.data.data, "", "success");
                }else if(data.data.status==0){
                    return sweetAlert(data.data.data, "", "error");
                }
            });
        };

        $scope.savePostOperativeOrders=function (patient,descriptions) {
            if (angular.isDefined(descriptions)==false) {
                return sweetAlert("Provide Descriptions for post operative order", "", "error");
            }
            var description=descriptions.operativeOrder;
            var admission_id=patient.admission_id;
            var patient_id=patient.patient_id;
            var item_id=patient.item_id;
            var visit_date_id=patient.visit_date_id;
            var dataToPost={visit_date_id:visit_date_id,descriptions:description,history_type:"POST OPERATIVE ORDERS","admission_id":admission_id,"patient_id":patient_id,"item_id":item_id,"user_id":user_id,"facility_id":facility_id};
            $http.post('/api/saveGivenDrug',dataToPost).then(function(data) {
                if(data.data.status==1){
                    return sweetAlert(data.data.data, "", "success");
                }else if(data.data.status==0){
                    return sweetAlert(data.data.data, "", "error");
                }
            });
        };

        $scope.saveAtentions=function (patient,descriptions) {
            if (angular.isDefined(descriptions)==false) {
                return sweetAlert("Provide Any attentions", "", "error");
            }
            var attention=descriptions.attention;
            var admission_id=patient.admission_id;
            var patient_id=patient.patient_id;
            var item_id=patient.item_id;
            var visit_date_id=patient.visit_date_id;
            var dataToPost={visit_date_id:visit_date_id,descriptions:attention,history_type:"ATTENTION","admission_id":admission_id,"patient_id":patient_id,"item_id":item_id,"user_id":user_id,"facility_id":facility_id};
            $http.post('/api/saveGivenDrug',dataToPost).then(function(data) {
                if(data.data.status==1){
                    return sweetAlert(data.data.data, "", "success");
                }else if(data.data.status==0){
                    return sweetAlert(data.data.data, "", "error");
                }
            });
        };

        $scope.saveIntubationMethod=function (patient,intubation) {
            if (angular.isDefined(intubation)==false) {
                return sweetAlert("Provide Intubation Method", "", "error");
            }
            var intubation=intubation.intubation;
            var admission_id=patient.admission_id;
            var patient_id=patient.patient_id;
            var item_id=patient.item_id;
            var visit_date_id=patient.visit_date_id;
            var dataToPost={visit_date_id:visit_date_id,descriptions:intubation,history_type:"INTUBATION",remarks:intubation,"admission_id":admission_id,"patient_id":patient_id,"item_id":item_id,"user_id":user_id,"facility_id":facility_id};
            $http.post('/api/saveGivenDrug',dataToPost).then(function(data) {
                if(data.data.status==1){
                    return sweetAlert(data.data.data, "", "success");
                }else if(data.data.status==0){
                    return sweetAlert(data.data.data, "", "error");
                }
            });
        };

        $scope.getIntraOperations=function () {
            $http.get('/api/getIntraOperations/'+facility_id).then(function(data) {
                $scope.approvedOperations = data.data;
            });

        };

        $scope.getListFromRecovery=function () {
            $http.get('/api/getListFromRecovery/'+facility_id).then(function(data) {
                $scope.ListFromRecoveries = data.data;
            });

        }

        $scope.getListFromPostAnaesthetic=function () {
            $http.get('/api/getListFromPostAnaesthetic/'+facility_id).then(function(data) {
                $scope.getListFromPostAnaesthetics = data.data;
            });

        }



        $scope.addAnaethesiaRecord= function (patient) {
                   $mdDialog.show({                 
                        controller: function ($scope) {
                            $scope.patientPaticulars=patient;
                               $scope.admissionInfoData=patient;
                                $scope.beds=patient;
                                $scope.cancel = function () {
                                 $mdDialog.hide();
                            };
                        },
                        templateUrl: '/views/modules/nursing_care/informed_consent.html',
                        parent: angular.element(document.body),
                        clickOutsideToClose: false,
                         fullscreen: $scope.customFullscreen // Only for -xs, -sm breakpoints.
                    });

        };




        $scope.addWardGrade= function (wards) {
	             if (angular.isDefined(wards)==false) {
                   return sweetAlert("Please Enter Ward Grade", "", "error");
                 }
                else{

	             var dataPost={"erasor":0,"ward_class":wards.ward_class,
                        "user_id":user_id,"facility_id":facility_id};

                 $http.post('/api/addWardGrade',dataPost).then(function(data) {
			     if(data.data.status ==0){
					 sweetAlert(data.data.data, "", "error");
				  }
			      else{
                     wards.ward_class= null;
                     sweetAlert(data.data.data, "", "success");
				  }});
                  }
        };



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

        $scope.saveTurningChart=function (turning,selectedPatient) {
var PsotData={
    'position':turning.Position, 'remarks':turning.Remarks,
    'date_recorded':turning.date_input,'time_recorded':turning.time_input,
    'visit_date_id':selectedPatient.visit_date_id,
    'admission_id':selectedPatient.admission_id,

    facility_id:facility_id,user_id:user_id
};
            $http.post('/api/saveTurningChart',PsotData).then(function(data) {

                if(data.data){

                    sweetAlert('Turning chart Saved', "", "success");
                }
            });

        }

 $scope.getDischargedReport=function(ff){
            $http.post('/api/getDischargedReport',{nurse_id:user_id,start_date:ff.start,end_date:ff.end}).then(function(data) {
                $scope.pendingDischarged=data.data;
            });
        }
 $scope.PrintAdmited=function () {

            //location.reload();
            var DocumentContainer = document.getElementById('admitedid');
            var WindowObject = window.open("", "PrintWindow",
                "width=900,height=700,top=50,left=450,toolbars=no,scrollbars=no,status=no,resizable=yes");
            WindowObject.document.title = "printout: GoT-HOMIS";
            WindowObject.document.writeln(DocumentContainer.innerHTML);
            WindowObject.document.close();

            setTimeout(function () {
                WindowObject.focus();
                WindowObject.print();
                WindowObject.close();
            });

        }
        $scope.PrintDischarged=function () {

            //location.reload();
            var DocumentContainer = document.getElementById('dischargedid');
            var WindowObject = window.open("", "PrintWindow",
                "width=900,height=700,top=50,left=450,toolbars=no,scrollbars=no,status=no,resizable=yes");
            WindowObject.document.title = "printout: GoT-HOMIS";
            WindowObject.document.writeln(DocumentContainer.innerHTML);
            WindowObject.document.close();

            setTimeout(function () {
                WindowObject.focus();
                WindowObject.print();
                WindowObject.close();
            });

        }

		

    }

})();