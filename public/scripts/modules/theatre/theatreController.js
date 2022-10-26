(function() {

 'use strict';
  var app = angular.module('authApp');
  app.controller('theatreController',
 ['$scope', '$http', '$rootScope','$mdDialog','$uibModal','Helper',function($scope, $http, $rootScope,$mdDialog,$uibModal,Helper ) {
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
	
	$scope.checkPatientStatus=function(patient){
	
	$mdDialog.show({
                controller: function ($scope) {
                    $scope.patientPaticulars=patient;
                    var patient_id=patient.patient_id;
                    var account_id=patient.account_id;
                    $scope.selectedPatient=patient;
                    $scope.beds=patient;
                    $scope.cancel = function () {
                        $mdDialog.hide();
                    };
                    $scope.getTodaysVitals = function (item) {
                        $http.post('/api/vitalsTime',{patient_id:patient_id,account_id:account_id}).then(function (data) {
                            $scope.vitalTime = data.data;
                        });
                    }
                    $scope.getPatientVitals = function (item) {
                        $http.post('/api/patientVitals',{patient_id:patient_id,account_id:account_id,time_taken:item.created_at}).then(function (data) {
                            $scope.vitalData = data.data;
                        });
                    }
                    $http.post('/api/previousVisits', {

                        "patient_id": patient_id

                    }).then(function(data) {

                        $scope.patientsVisits = data.data;

                    });

                    $http.post('/api/getResults', {

                        "patient_id": patient_id,

                        "dept_id": 3

                    }).then(function(data) {

                        $scope.radiology = data.data;

                    });

                    $http.post('/api/getResults', {

                        "patient_id": patient_id,

                        "dept_id": 2

                    }).then(function(data) {

                        $scope.labInvestigations = data.data;


                    });

                    $http.get('/api/getWards/' + facility_id).then(function(data) {

                        $scope.wards = data.data;

                    });

                    $http.get('/api/getSpecialClinics').then(function(data) {

                        $scope.clinics = data.data;

                    });

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
			
			$scope.deceased = function(item, corpse) {

                if (angular.isDefined(corpse) == false) {
                    swal("An error occurred", "Data not saved...Please write causes of death and click send to last office button", "error");
                    return;
                }

                var deceased = {
                    "first_name": item.first_name,
                    "middle_name": item.middle_name,
                    "last_name": item.last_name,
                    "patient_id": item.patient_id,
                    "residence_id": item.residence_id,
                    "death_certifier": user_id,
                    "user_id": user_id,
                    "storage_reason": 1,
                    "facility_id": facility_id,
                    "immediate_cause": corpse.immediate_cause,
                    "underlying_cause": corpse.underlying_cause,
                    "dept_id": 44
                };

                $http.post('/api/postDeceased', deceased).then(function(data) {

                    if (data.data.status == 0) {
                        swal(data.data.data, "", "error");
                    } else {
                        swal(item.first_name + ' ' + item.last_name + " sent to Last office", "", "success");
                    }
                });
            }
			
			$scope.patientAdmission = function(item, patient) {
                $mdDialog.cancel();
                if (angular.isDefined(patient) == false) {
                    swal("Oops! something went wrong..", "Please search and select Patient then click ward button to admit patient!");
                    return;
                }
                var object = angular.extend({}, item, patient);
                $scope.item = object;
                // $mdDialog.show({
                //     controller: 'admissionModal',
                //     templateUrl: '/views/modules/clinicalServices/admission.html',
                //     parent: angular.element(document.body),
                //     scope: $scope,
                //     clickOutsideToClose: false,
                //     fullscreen: true,
                // });
                var modalInstance = $uibModal.open({
                    templateUrl: '/views/modules/clinicalServices/admission.html',
                    size: 'lg',
                    animation: true,
                    controller: 'admissionModal',
                    resolve:{
                        object: function () {
                            return object;
                        }
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
            var visit_date_id=patient.account_id;
            var dataToPost={visit_date_id:visit_date_id,descriptions:description,history_type:"DESCRIPTION OF PROCEDURE","admission_id":admission_id,"patient_id":patient_id,"item_id":item_id,"user_id":user_id,"facility_id":facility_id};
                        Helper.overlay(true);
            $http.post('/api/saveGivenDrug',dataToPost).then(function(data) {
                Helper.overlay(false);
                if(data.data.status==1){
                    return sweetAlert(data.data.data, "", "success");
                }else if(data.data.status==0){
                    return sweetAlert(data.data.data, "", "error");
                }
            }, function(data){Helper.overlay(false);});
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
                    //Previous History



                    $scope.getPatientReport = function(item) {
                        $http.post('/api/prevHistory', {

                            "patient_id": item.patient_id,

                            "visit_date_id": item.account_id

                        }).then(function(data) {
                            $scope.prevHistory = data.data[0];
                            $scope.otherComplaints = data.data[1];
                            $scope.hpi = data.data[2];

                        });

                        $http.post('/api/getPrevDiagnosis', {

                            "patient_id": item.patient_id,

                            "visit_date_id": item.account_id

                        }).then(function(data) {

                            $scope.prevDiagnosis = data.data;

                        });

                        $http.post('/api/getPrevRos', {

                            "patient_id": item.patient_id,

                            "visit_date_id": item.account_id

                        }).then(function(data) {

                            $scope.prevRos = data.data[0];
                            $scope.prevRosSummary = data.data[1];

                        });

                        $http.post('/api/getPrevBirth', {

                            "patient_id": item.patient_id,

                            "date_attended": item.date_attended

                        }).then(function(data) {

                            $scope.prevBirth = data.data;

                        });
                        $http.post('/api/getPrevFamily', {

                            "patient_id": item.patient_id,

                            "date_attended": item.date_attended

                        }).then(function(data) {

                            $scope.prevFamily = data.data;

                        });
                        $http.post('/api/prevInvestigationResults', {

                            "patient_id": item.patient_id,

                            "visit_date_id": item.account_id,

                            "dept_id": 2

                        }).then(function(data) {

                            $scope.labInvestigationsz = data.data;

                        });

                        $http.post('/api/getInvestigationResults', {

                            "patient_id": item.patient_id,

                            "account_id": item.account_id,

                            "dept_id": 3

                        }).then(function(data) {

                            $scope.radiologyResults = data.data;

                        });

                        $http.post('/api/getPastMedicine', {

                            "patient_id": item.patient_id,

                            "visit_date_id": item.account_id,

                        }).then(function(data) {

                            $scope.prevMedicines = data.data;

                        });

                        $http.post('/api/getPastProcedures', {

                            "patient_id": item.patient_id,

                            "visit_date_id": item.account_id

                        }).then(function(data) {

                            $scope.pastProcedures = data.data;

                        });

                        $http.post('/api/getAllergies', {

                            "patient_id": item.patient_id,

                            "date_attended": item.date_attended,
                            "visit_date_id": item.account_id

                        }).then(function(data) {

                            $scope.allergies = data.data;

                        });
                        $http.post('/api/getPrevPhysical', {
                            "patient_id": item.patient_id,
                            "visit_date_id": item.account_id
                        }).then(function(data) {
                            $scope.prevSystemic = data.data[0];
                            $scope.prevGen = data.data[1];
                            $scope.prevLocal = data.data[2];
                            $scope.prevSummary = data.data[3];
                            $scope.prevOtherSystemic = data.data[4];
                        });
                    }
                    $scope.removeFromSelection = function(item, objectdata) {

                        var indexremoveobject = objectdata.indexOf(item);

                        objectdata.splice(indexremoveobject, 1);

                    }

                    $scope.addProcedure = function(patient,item, qty) {
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
                            "quantity": qty,
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

                $http.post('/api/getPatientServicesInTheatre', {
                    "search": searchKey,
                    "facility_id": facility_id,
                    "patient_category_id": pay_id
                }).then(function(data) {
                    procedureData = data.data;
                });
                return procedureData;
            }
		
		$scope.requestBlood = function (patient,item) {
                            var requests = {facility_id:facility_id,patient_id:patient.patient_id,visit_id:patient.account_id,requested_by:user_id,dept_id:1,
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
            var visit_date_id=patient.visit_id;
			$http.get('/api/getUsermenu/'+user_id ).then(function(data) {
            $scope.menu=data.data;
        });

      	var dataToPost={visit_date_id:visit_date_id,patient_id:patient_id,item_id:item_id,user_id:user_id,facility_id:facility_id};
            $http.post('/api/getProcessWork',dataToPost).then(function(data) {
                $scope.findings=data.data;
				
				
            });
        };
					
                },
                templateUrl: '/views/modules/theatre/theatre.html',
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


     $scope.TheatrePrintOutDetailsSpecific = function (pef) {

         var dataToPost={specificPatient:1,visit_date_id:pef};
         Helper.overlay(true);
         $http.post('/api/TheatrePrintOutDetails',dataToPost).then(function(data) {
             Helper.overlay(false);
             $scope.histories=data.data;

         }, function(data){Helper.overlay(false);});


     };
     $scope.TheatrePrintOutDetails = function (pef) {
         if(angular.isDefined(pef)==false ){
             return sweetAlert("You must select date range", "", "error");
         }

         var dataToPost={specificPatient:0,facility_id:facility_id,start_date:pef.start,end_date:pef.end};
         Helper.overlay(true);
         $http.post('/api/TheatrePrintOutDetails',dataToPost).then(function(data) {
             Helper.overlay(false);
             $scope.histories=data.data;

         }, function(data){Helper.overlay(false);});


     };
     var resdata=[];
     $scope.TheatrePatientSearch = function (pf) {

         $http.post('/api/TheatrePatientSearch',{mrn:pf}).then(function(data) {

             resdata=data.data;

         });

         return resdata;
     };
     $scope.loadVisitDates = function (patient_id) {

         $http.post('/api/loadVisitDates',{patient_id:patient_id}).then(function(data) {

             $scope.visits=data.data;

         });

     };
     $scope.TheatrePrintOut = function (pef) {
         if(angular.isDefined(pef)==false){
             return sweetAlert("You must select date range", "", "error");
         }

         var dataToPost={facility_id:facility_id,start_date:pef.start,end_date:pef.end};
         Helper.overlay(true);
         $http.post('/api/TheatrePrintOut',dataToPost).then(function(data) {
             Helper.overlay(false);
             $scope.results=data.data;

         }, function(data){Helper.overlay(false);});


     };
     $scope.TheatrePrintOutByCategory = function (pef) {
         if(angular.isDefined(pef)==false){
             return sweetAlert("You must select date range", "", "error");
         }

         var dataToPost={facility_id:facility_id,start_date:pef.start,end_date:pef.end};
         Helper.overlay(true);
         $http.post('/api/TheatrePrintOutByCategory',dataToPost).then(function(data) {
             Helper.overlay(false);
             $scope.resultsum=data.data;

         }, function(data){Helper.overlay(false);});


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
$scope.TheatrePrintOutPrint=function () {
         //location.reload();
         var DocumentContainer = document.getElementById('TheatrePrintOutId');
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
     $scope.TheatrePrintOutByCategoryPrint=function () {
         //location.reload();
         var DocumentContainer = document.getElementById('TheatrePrintOutByCategoryId');
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
     $scope.TheatrePrintOutDetailsPrint=function () {
         //location.reload();
         var DocumentContainer = document.getElementById('TheatrePrintOutDetailsId');
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
        ]);

}());