(function() {

 'use strict';
  var app = angular.module('authApp');
  app.controller('runnerController',
 ['$scope', '$http', '$rootScope','$mdDialog','$mdpTimePicker','$uibModal',function($scope, $http, $rootScope,$mdDialog,$mdpTimePicker,$uibModal ) {
 var facility_id = $rootScope.currentUser.facility_id;
 var user_id = $rootScope.currentUser.id;
               
	$scope.getTheatreRequests=function(){
	var postData={facility_id:facility_id};
	 $http.post('/api/getListTheatreQueues',postData).then(function(data) {
     $scope.theatreQues = data.data;
            });
	}; 

	$scope.assignProcedures = function(procedure) {
		
		if(angular.isDefined($scope.selectedItem)==false){
			  return sweetAlert("Please Search and select services from the sugestions", "", "error");
			
		}
		else if(angular.isDefined(procedure)==false){
			  return sweetAlert("Please fill all required", "", "error");
			
		}
		
				var item_id=$scope.selectedItem.id;
				var item_name=$scope.selectedItem.item_name;
				var procedure_location=procedure.location;
				var procedure_category=procedure.category;
	var postData={item_name:item_name,item_id:item_id,service_type:procedure_category,procedure_category:procedure_location,};
          $http.post('/api/assignTheatreServices', postData).then(function(data) {
                  if(data.data.status==1){
                    return sweetAlert(data.data.data, "", "success");
                   } 
				   else if(data.data.status==0){
                    return sweetAlert(data.data.data, "", "error");
                   }
                });
            };		
			
			$scope.showProcedures = function(procedure) {
		      	var procedure_location=procedure.location;
				var procedure_category=procedure.category;
	var postData={service_type:procedure_category,procedure_category:procedure_location,};
          $http.post('/api/showProcedures', postData).then(function(data) {
                  
				  $scope.procedureLists=data.data;
                });
            };		
	
        var searchItem=[];

     		
		 $scope.procedureSearch = function (searchKey) {
            //////console.log(searchKey);
            var dataToPost = {keyWord: searchKey};
            $http.post('/api/getProcedure', dataToPost).then(function (data) {
                searchItem = data.data;

            });

            return searchItem;
        };
		
		$scope.selectedItemSearch =function(item){
			console.log(item);
					    
                          $scope.selectedItem=item;						
					
				};
	
	    $scope.addSwabsInfo=function(patient){
	
	    $mdDialog.show({
                controller: function ($scope) {
                    $scope.patientPaticulars=patient;
                    $scope.selectedPatient=patient;
                    $scope.beds=patient;
                    $scope.cancel = function () {
                        $mdDialog.hide();
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
                    }
					
					
              $http.get('/api/getWards/' + facility_id).then(function(data) {
                    $scope.wards = data.data;
                });
					
					  $scope.templates = [{
                name: 'Admission',
                url: 'admission.html'
            },  {
                name: 'Deceased',
                url: 'deceased.html'
            }];
			
			$scope.saveSwabs = function(swabs,start_time,end_time,selectedPatient) {

                if (angular.isDefined(swabs) == false) {
                    swal("An error occurred", "Please write all required information", "error");
                    return;
                }
                else if (angular.isDefined(start_time) == false) {
                    swal("An error occurred", "Enter starting Time", "error");
                    return;
                }
				else if (angular.isDefined(end_time) == false) {
                    swal("An error occurred", "Enter End Time", "error");
                    return;
                }
		else if (swabs.given > swabs.used) {
           swal("An error occurred", "The given swabs types are greater in number than the used", "error");
                    return;
        }

                var postData = {
                    "start_time": start_time.twelve,
                    "end_time": end_time.twelve,
                    "material_id": $scope.swab.id,
                    "item_id": selectedPatient.item_id,
                    "visit_id": selectedPatient.account_id,
                    "user_id": user_id,
                    "used": swabs.used,
                    "given": swabs.given,
                    "drainage": swabs.drainage,
                    "tourniquet": swabs.tourniquet,
                    "implants": swabs.implants,
                    "implant_screws": swabs.implant_screws,
                    "comment": swabs.comment,
                    "pathology_specimen": swabs.pathology_specimen
                };

                $http.post('/api/postSwab', postData).then(function(data) {

                    if (data.data.status == 0) {
                        swal(data.data.data, "", "error");
                    } else {
                  swal("Swab type was successfully registered", "", "success");
                    }
                });
            }
			
			$scope.selectedSwab=function(swab){
				  if (angular.isDefined(swab) == false) {
                    return;
                }
				$scope.swab=swab;
				
			};
			
			var swabsLists=[];
			$scope.getSwabs = function(swab) {
                 if (angular.isDefined(swab) == false) {
                    return;
                }
				var postData={swab:swab};
            $http.post('/api/getSwabs', postData).then(function(data) {
                  swabsLists=data.data;
				});
               return swabsLists;
            };

			  $http.get('/api/getUsermenu/'+user_id).then(function(cardTitle){
							$scope.facility_address=cardTitle.data[0];
                              
                           });		
			$scope.getSwabsRecords=function (patient) {
            if (angular.isDefined(patient)==false) {
                return sweetAlert("Please select patient,before continue", "", "error");
            }
           
            var visit_date_id=patient.account_id;
            var item_id=patient.item_id;
            var dataToPost={item_id:item_id,visit_date_id:visit_date_id};
           $http.post('/api/getSwabsRecords',dataToPost).then(function(data) {
               $scope.swabsRecords=data.data;
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
            var visit_date_id=patient.account_id;
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
            var visit_date_id=patient.account_id;
            var dataToPost={visit_date_id:visit_date_id,descriptions:attention,history_type:"ATTENTION","admission_id":admission_id,"patient_id":patient_id,"item_id":item_id,"user_id":user_id,"facility_id":facility_id};
            $http.post('/api/saveGivenDrug',dataToPost).then(function(data) {
                if(data.data.status==1){
                    return sweetAlert(data.data.data, "", "success");
                }else if(data.data.status==0){
                    return sweetAlert(data.data.data, "", "error");
                }
            });
        };
		
		 //procedures

            var procedureData = [];

            $scope.procedures = [];
			$scope.prevMedics = function(item) {
                $http.post('/api/getPrevMedicine', {
                    "patient_id": item.patient_id
                }).then(function(data) {
                    $scope.prevMedicines = data.data;
                });
            };
			
			
			
			
			
				
				
				
			
			  $scope.getDefaultProcedures = function(patient) {

                var pay_id = patient.bill_id;
                if (pay_id == null) {
                    swal("Please search patient before searching procedures!");
                    return;
                }

                if (patient.main_category_id == 3) {
                    pay_id = 1;
                }

                $http.post('/api/getProcedures', {
                    "facility_id": facility_id,
                    "patient_category_id": pay_id
                }).then(function(data) {
                    $scope.defaultProcedures = data.data;
                });

            };
			
			 $scope.prevProcedure = function(item) {
                $http.post('/api/getPrevProcedures', {
                    "patient_id": item.patient_id
                }).then(function(data) {
                    $scope.prevProcedures = data.data;
                });
            };

            $scope.addProcedure = function(item, patient) {
                var filter = patient.bill_id;
                var status_id = 1;

                var main_category = patient.main_category_id;

                if (patient.patient_id == null) {
                    swal("Please search and select Patient to prescribe");
                    return;
                }

                if (item.item_id == null) {
                    swal("Please search and select Procedure!");
                    return;
                }

                for (var i = 0; i < $scope.procedures.length; i++)
                    if ($scope.procedures[i].item_id == item.item_id) {
                        swal(item.item_name + " already in your order list!", "", "info");
                        return;
                    }
                    if (main_category != 1 && item.exemption_status == 0) {
                    filter = patient.bill_id;
                }

                if (main_category == 3 && item.exemption_status == 1) {
                    filter = 1;
                }

                if (main_category == 2 && item.exemption_status == 1) {
                    filter = patient.bill_id;
                }

                $scope.procedures.push({
                    "payment_filter": filter,
                    "admission_id": '',
                    "facility_id": facility_id,
                    "item_type_id": item.item_type_id,
                    "item_price_id": item.price_id,
                    "quantity": 1,
                    "status_id": status_id,
                    "account_number_id": patient.account_id,
                    "patient_id": patient.patient_id,
                    "user_id": user_id,
                    "item_id": item.item_id,
                    "item_name": item.item_name
                });
                $('#procedures').val('');
            }

            $scope.saveProcedures = function(objectData) {
                $http.post('/api/postPatientProcedures', objectData).then(function(data) {

                });
                swal("Patient procedures successfully saved!", "", "success");
                $scope.procedures = [];
            }
			

            $scope.searchProcedures = function(searchKey, patient) {
                var pay_id = patient.bill_id;
                if (pay_id == null) {
                    swal("Please search patient before searching procedures!");
                    return;
                }

                if (patient.main_category_id == 3) {
                    pay_id = 1;
                }

                $http.post('/api/getPatientProcedures', {
                    "search": searchKey,
                    "facility_id": facility_id,
                    "patient_category_id": pay_id
                }).then(function(data) {
                    procedureData = data.data;
                });
                return procedureData;
            }
		
		$scope.requestBlood = function (patient,item) {
                            var requests = {facility_id:facility_id,patient_id:patient.patient_id,visit_id:patient.visit_id,requested_by:user_id,dept_id:1,
                                blood_group:item.blood_group,priority:item.priority,unit_requested:item.unit_requested,request_reason:item.request_reason
                            };
                            $http.post('/api/requestBlood', requests).then(function(data) {
                                var taa=data.data.msg;
                                swal('',taa,'success');
                            });
                        }
		
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
		
		
		$scope.saveDescriptionsCases=function (patient,descriptions) {
            if (angular.isDefined(descriptions)==false) {
                return sweetAlert("Provide Any attentions", "", "error");
            }
            var cases=descriptions.cases;
            var admission_id=patient.admission_id;
            var patient_id=patient.patient_id;
            var item_id=patient.item_id;
            var visit_date_id=patient.account_id;
            var dataToPost={visit_date_id:visit_date_id,descriptions:cases,history_type:"OPERATION CASE","admission_id":admission_id,"patient_id":patient_id,"item_id":item_id,"user_id":user_id,"facility_id":facility_id};
            $http.post('/api/saveGivenDrug',dataToPost).then(function(data) {
                if(data.data.status==1){
                    return sweetAlert(data.data.data, "", "success");
                }else if(data.data.status==0){
                    return sweetAlert(data.data.data, "", "error");
                }
            });
        };
		
		$scope.getFindings=function (patient) {
            var admission_id=patient.admission_id;
            var patient_id=patient.patient_id;
            var item_id=patient.item_id;
            var visit_date_id=patient.visit_date_id;
			$http.get('/api/getUsermenu/'+user_id ).then(function(data) {
            $scope.menu=data.data;
        });

      	var dataToPost={visit_date_id:visit_date_id,patient_id:patient_id,item_id:item_id,user_id:user_id,facility_id:facility_id};
            $http.post('/api/getProcessWork',dataToPost).then(function(data) {
                $scope.findings=data.data;
				
				
            });
        };
					
                },
                templateUrl: '/views/modules/theatre/swabs-theatre.html',
                parent: angular.element(document.body),
                clickOutsideToClose: false,
                fullscreen: $scope.customFullscreen // Only for -xs, -sm breakpoints.
            });
	};  
	
	$scope.changeSettings=function(item){
	
	$mdDialog.show({
                controller: function ($scope) {
                    $scope.selectedItem=item;
                    
					$scope.cancel = function () {
                        $mdDialog.hide();
                    };
					
		$scope.changeProcedures = function (procedure,selectedItem) {
			       var item_id = selectedItem.item_id;
			       var item_name = selectedItem.item_name;
			       var procedure_category = procedure.category;
			       var service_type = procedure.location;
				   var postData={item_name:item_name,item_id:item_id,procedure_category:procedure_category,service_type:service_type};
                        
          $http.post('/api/changeProcedures',postData).then(function(data) {
             
			  if(data.data.status==1){
				     $scope.cancel();
                    return sweetAlert(data.data.data, "", "success");
					
                }
			  else if(data.data.status==0){
                    return sweetAlert(data.data.data, "", "error");
                }
				
				     });

				   };
					
					
						
                },
                templateUrl: '/views/modules/theatre/edit_item_settings.html',
                parent: angular.element(document.body),
                clickOutsideToClose: false,
                fullscreen: $scope.customFullscreen // Only for -xs, -sm breakpoints.
            });
	};  
			
			 


			 angular.element(document).ready(function () {
                $scope.getTheatreRequests();                    

                });
			
			
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
			
			
	 $scope.getListFromTheatre=function () {
            $http.get('/api/getListFromTheatres/'+facility_id).then(function(data) {
                $scope.getListFromTheatres = data.data;
            });
        };
			
			
			
			$scope.getListFromTheatresReport=function(pef){

            if(angular.isDefined(pef)==false){
                return sweetAlert("You must select date range", "", "error");
            }

            var dataToPost={facility_id:facility_id,start_date:pef.start,end_date:pef.end};

            $http.post('/api/getListFromTheatresReport',dataToPost).then(function(data) {
                $scope.results=data.data;
                console.log($scope.results);
            });

        };
			
			
			
			   
			   
			   
            }
        ]);

}());