/**
 * Created by USER on 2017-02-13.
 */
(function() {

    'use strict';

    angular
		.module('authApp').directive('ngFiles', ['$parse', function ($parse) {

			function fn_link(scope, element, attrs) {
				var onChange = $parse(attrs.ngFiles);
				element.on('change', function (event) {
					onChange(scope, { $files: event.target.files });
				});
			};

			return {
				link: fn_link
			}
		} ])
        .controller('formController', formController);

    function formController($http, $auth, $rootScope,$state,$location,$scope,$uibModal) {


        // we will store all of our form data in this object
        $scope.formData = {};

        // function to process the form
        $scope.processForm = function() {
            alert('awesome!');
        };



        var formdata = new FormData();

		$scope.getTheFiles = function ($files) {

			angular.forEach($files, function (value, key) {
				formdata.append(key, value);

			});

		};
				  var facility_id =$rootScope.currentUser.facility_id;
				  var user_id =$rootScope.currentUser.id;

        $scope.verification = function (item) {
            $scope.dataLoading = true;
            $http.get('https://verification.nhif.or.tz/NHIFService/breeze/Verification/GetCard?CardNo='+item).then(function (data) {
                 if(data.data.StatusDescription=='Active'){

                    $scope.patient.date_of_birth=data.data.DateOfBirth.substr(0,data.data.DateOfBirth.indexOf('T'));
                    $scope.nhif_patient = {"first_name":data.data.FirstName,
                        "middle_name":data.data.MiddleName,
                        "last_name":data.data.LastName,"gender":data.data.Gender,
                        "dob":$scope.patient.date_of_birth,
						"AuthorizationNo":data.data.AuthorizationNo
                    };

                     $scope.dataLoading = false;
                     $scope.quick_registration= $scope.nhif_patient;
                     var modalInstance = $uibModal.open({
                         templateUrl: '/views/modules/registration/insuarance.html',
                         size: 'lg',
                         animation: true,
                         controller: 'registrationModal',
                         resolve:{
                             quick_registration: function () {
                                 //console.log($scope.quick_registration);
                                 return $scope.quick_registration ;
                             }
                         }


                     });


                }

                else if(data.data.StatusDescription=='Revoked'){
                     $scope.dataLoading = false;
                     swal("CARD NOT ACTIVE");
				 }
                else {
                     $scope.dataLoading = false;
                     return sweetAlert("CARD NUMBER "+item+" NOT FOUND IN NHIF DATABASE", "", "error");

                 }
            }).finally(function () {
                $scope.dataLoading = false;
                            });


        }


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
				   var insuranceService=[];

				   
				   		   
		    $scope.showSearchMarital= function (searchKey) {
				
            $http.get('/api/getMaritalStatus/'+searchKey).then(function(data) {
            maritals=data.data;
			
            });
			//console.log(maritals);
			return maritals;
        }

        $scope.isSet = function(tabNum){
            return $scope.tab === tabNum;
        }
        $scope.oneAtATime = true;



        $scope.showNextForm=function (newTab) {
            $scope.tab = newTab;
            alert('next form');
        }
		
		$scope.showSearchTribe= function (searchKey) {
				
            $http.get('/api/getTribe/'+searchKey).then(function(data) {
            tribe=data.data;
			
            });
			return tribe;
        }
		
		$scope.showSearchOccupation= function (searchKey) {
				
            $http.get('/api/getOccupation/'+searchKey).then(function(data) {
            occupation=data.data;
			
            });
			return occupation;
        }
		
		$scope.getCountry= function (searchKey) {
				
            $http.get('/api/getCountry/'+searchKey).then(function(data) {
            country=data.data;
			
            });
			return country;
        }
		
		$scope.getRelationships= function (searchKey) {
				
            $http.get('/api/getRelationships/'+searchKey).then(function(data) {
            relationships=data.data;		
            });
			return relationships;
        }
        $http.get('/api/getInsurances').then(function(data) {
            $scope.insurances=data.data;


        });
				   
		    $scope.getPatients = function (searchKey) {
				//console.log(searchKey);
            $http.get('/api/getSeachedPatients/'+searchKey).then(function(data) {
            patientsList=data.data;
			//console.log($scope.patients);
          
            });
			return patientsList;
        }
				   
            $scope.showSearchNextResidences= function(searchKey) {

             $http.get('/api/searchResidences/'+searchKey).then(function(data) {
                nextresdata= data.data;
             });
			 ////console.log(resdata);
             return nextresdata;
             }
			 
			 $scope.showSearchResidences = function(searchKey) {

             $http.get('/api/searchResidences/'+searchKey).then(function(data) {
                resdata = data.data;
             });
			 ////console.log(resdata);
             return resdata;
             }
			 
			 $scope.patientService = function() {
				 var searchKey={'patient_category':$scope.encounter.payment_category.patient_category,'item_name':$scope.encounter};
//console.log($scope.encounter);
             $http.post('/api/searchPatientServices',searchKey).then(function(data) {
               patientService = data.data;
            });
			 //console.log(resdata);
             return patientService;
             }



        $scope.patientInsuaranceService = function(searchKey) {
            var searchKeyReceived={'patient_category':'NHIF','item_name':searchKey};
            //console.log(searchKeyReceived);
            $http.post('/api/searchPatientServices',searchKeyReceived).then(function(data) {
                insuranceService = data.data;
            });
             return insuranceService;
        }
			
            $scope.searchPatientCategory = function(searchKey) {

             $http.get('/api/searchPatientCategory/'+searchKey).then(function(data) {
                patientCategory = data.data;
             });
			 ////console.log(resdata);
             return patientCategory;
             }
				  			  
				  $scope.viewItem = function (quick_registration) {
					   $scope.quick_registration = quick_registration;
					  
				  //console.log(quick_registration.first_name);
				  var modalInstance = $uibModal.open({
				  templateUrl: '/views/modules/registration/encounterModal.html',
				  size: 'lg',
				  animation: true,
				  controller: 'registrationModal',
				  resolve:{
                  quick_registration: function () {
					  //console.log($scope.quick_registration);
                  return $scope.quick_registration ;
                  }
                  }

				  
                  });
				
				modalInstance.result.then(function(quick_registration) {
                $scope.quick_reg = quick_registration;
			    //console.log($scope.quick_reg);
                });	
                }





					   $scope.postCorpseMortuary = function (quick_registration) {
					   $scope.quick_registration = quick_registration;
                      $http.get('/api/getMortuary').then(function(data) {
                          $scope.mortuaries=data.data;
                          //console.log($scope.mortuaries);


                  var object =angular.extend($scope.quick_registration ,$scope.mortuaries);
                      //console.log(object);

                  var modalInstance = $uibModal.open({
				  templateUrl: '/views/modules/registration/encounterMortuary.html',
				  size: 'lg',
				  animation: true,
				  controller: 'registrationModalCorpse',
				  resolve:{
                      object: function () {
					  return object;
                  }
                  }


                  });
                      });

                }




				
				$scope.openDialogForServices = function (selectedPatient) {
					//console.log(selectedPatient);
					   $scope.quick_registration =selectedPatient;
					   
				  
				  //console.log($scope.quick_registration);
				  var modalInstance = $uibModal.open({
				  templateUrl: '/views/modules/registration/encounterModal.html',
				  size: 'lg',
				  animation: true,
				  controller: 'registrationModal',
				  resolve:{
                  quick_registration: function () {
					  $scope.cardDetails=$scope.quick_registration;
					  //console.log($scope.cardDetails);
                  return $scope.quick_registration ;
                  }
                  }

				  
                  });
				
				modalInstance.result.then(function(quick_registration) {
                $scope.quick_reg = quick_registration;
			    //console.log($scope.quick_reg);
                });	
                }
				  
		// Parse the resolve object
    function parseResolve(quick_registration) {
        if (typeof quick_registration === 'string') {
            return {
                quick_registration: function() {
                    return quick_registration;
                }
            }
        }
        else if (typeof quick_registration === 'object') {
            //var resolve = {};
            var resolve = $scope.quick_registration;
            angular.forEach(quick_registration, function(value, key) {
                resolve[key] = function() {
					////console.log(value);
                    return value;
                }
            })
			//console.log(resolve);
            return resolve;
        }
    }		  
				  
				  
		
				  $scope.patient_quick_registration=function (patient) {
			      var first_name=patient.first_name;
			      var middle_name=patient.middle_name;
			      var last_name=patient.last_name;
			      var gender=patient.gender;
			      var dob=patient.dob;
			      var mobile_number=patient.mobile_number;
				  
				  
				  if (angular.isDefined(first_name)==false) {
                   return sweetAlert("Please Enter FIRST NAME before SAVING", "", "error");
                  } 
				  
				  else if (angular.isDefined(middle_name)==false) {
                   return sweetAlert("Please Enter MIDDLE NAME before SAVING", "", "error");
                  }
				  
				  else if (angular.isDefined(last_name)==false) {
                   return sweetAlert("Please Enter LAST NAME before SAVING", "", "error");
                  }
				  else if (angular.isDefined(patient.resedence_id)==false) {
                   return sweetAlert("Please type the Residence Name and choose from the suggestions", "", "error");
                   }
				  var patient_residences=patient.resedence_id.residence_id;
				  var quick_registration={"first_name":first_name,"middle_name":middle_name,"last_name":last_name,"dob":dob,"gender":gender,"mobile_number":mobile_number,"residence_id":patient_residences,"facility_id":facility_id,"user_id":user_id}
				  
				  
				 $http.post('/api/quick_registration',quick_registration).then(function(data) {
                 $scope.quick_registration=data.data;
				 ////console.log(data.data);
				 if(data.data.status ==0){
					 
					 sweetAlert(data.data.data, "", "error");
				  }else{	  
					  $scope.patient = null;
					  quick_registration=$scope.quick_registration;
			          $scope.viewItem(quick_registration);
					 
							
				  }
				});
			   
	
			   
					}

  $scope.corpse_quick_registration=function (patient) {
			      var first_name=patient.first_name;
			      var middle_name=patient.middle_name;
			      var last_name=patient.last_name;
			      var gender=patient.gender;
			      var dob=patient.dob;
			      var mobile_number=patient.mobile_number;


				  if (angular.isDefined(first_name)==false) {
                   return sweetAlert("Please Enter FIRST NAME or UKNOWN before SAVING", "", "error");
                  }

				  else if (angular.isDefined(middle_name)==false) {
                   return sweetAlert("Please Enter MIDDLE NAME or UKNOWN before SAVING", "", "error");
                  }

				  else if (angular.isDefined(last_name)==false) {
                   return sweetAlert("Please Enter LAST NAME or UKNOWN before SAVING", "", "error");
                  }
				  else if (angular.isDefined(patient.resedence_id)==false) {
                   return sweetAlert("Please type the Residence Name and choose from the suggestions", "", "error");
                   }
				  var patient_residences=patient.resedence_id.residence_id;
				  var quick_registration={"first_name":first_name,"middle_name":middle_name,"last_name":last_name,"dob":dob,"gender":gender,"mobile_number":mobile_number,"residence_id":patient_residences,"facility_id":facility_id,"user_id":user_id}


				 $http.post('/api/corpse_registration',quick_registration).then(function(data) {
                 $scope.corpse_registration=data.data;
				 ////console.log(data.data);
				 if(data.data.status ==0){

					 sweetAlert(data.data.data, "", "error");
				  }else{
					  $scope.patient = null;
					  quick_registration=$scope.corpse_registration;
			          $scope.postCorpseMortuary(quick_registration);


				  }
				});



					}




        $scope.savePatientInsuarance=function (quick_registration,patient) {
            var first_name=quick_registration.first_name;
            var middle_name=quick_registration.middle_name;
            var last_name=quick_registration.last_name;
            var gender=quick_registration.gender;
            var dob=quick_registration.dob;
              if (angular.isDefined(first_name)==false) {
                return sweetAlert("Please Enter FIRST NAME before SAVING", "", "error");
            }

            else if (angular.isDefined(middle_name)==false) {
                return sweetAlert("Please Enter MIDDLE NAME before SAVING", "", "error");
            }

            else if (angular.isDefined(last_name)==false) {
                return sweetAlert("Please Enter LAST NAME before SAVING", "", "error");
            }

            var mobile_number=patient.mobile_number;

            var patient_residences=patient.resedence_id.residence_id;
            var patientservices=patient.payment_services.service_id;
            var price_id=patient.payment_services.price_id;
            var item_type_id=patient.payment_services.item_type_id;
            var patient_main_category_id=patient.payment_services.patient_main_category_id;
            var patient_category=patient.payment_services.patient_category;
            var quick_registration={"item_type_id":item_type_id,"price_id":price_id,"patient_main_category_id":patient_main_category_id,"patient_category":patient_category,"patientservices":patientservices,"first_name":first_name,"middle_name":middle_name,"last_name":last_name,"dob":dob,"gender":gender,"mobile_number":mobile_number,"residence_id":patient_residences,"facility_id":facility_id,"user_id":user_id}
            $http.post('/api/quick_registration',quick_registration).then(function(data) {
                $scope.registeredPatient=data.data;
               if(data.data.status ==0){
                    sweetAlert(data.data.data, "", "error");
                }else{
                    $scope.patient = null;
                    $scope.addEncounterInsuarance($scope.registeredPatient,quick_registration);
                    //sweetAlert('successfully registered '+first_name, "", "success");
                     }
            });
     }

        $scope.addEncounterInsuarance=function (registeredPatient,quick_registration) {

            $scope.patientsInfo=registeredPatient;
            var patient_id=registeredPatient.id;
            var patient_category=registeredPatient.patient_category;
            var item_type_id=quick_registration.item_type_id;
            var price_id=quick_registration.price_id;
            var facility_id=registeredPatient.facility_id;
            var user_id=$rootScope.currentUser.id;
            var payment_filter=quick_registration.patient_main_category_id;
            var service_category=quick_registration.item_name;
            var service_id=quick_registration.patientservices;
            var price_id=quick_registration.price_id;
            var bill_category_id=quick_registration.patient_main_category_id;
            var main_category_id=quick_registration.patient_main_category_id;
            var enterEncounter={'payment_filter':payment_filter,'item_type_id':item_type_id,'patient_category':patient_category,'main_category_id':main_category_id,'bill_id':bill_category_id,
                'service_category':service_category,'service_id':service_id,'price_id':price_id,'patient_id':patient_id ,'facility_id':facility_id,'user_id':user_id};

            $http.post('/api/enterEncounter',enterEncounter).then(function(data) {
                $scope.registrationReport=data.data;

                if(data.data.status ==0){

                    sweetAlert(data.data.data, "", "error");
                }else{


                    //console.log($scope.patientsInfo);
                    var modalInstance = $uibModal.open({
                        templateUrl: '/views/modules/registration/printCardBima.html',
                        size: 'lg',
                        animation: true,
                        controller: 'printCardBima',
                        resolve:{
                            patientsInfo: function () {
                                ////console.log($scope.quick_registration);
                                return $scope.patientsInfo;
                            }
                        }


                    });

                    //sweetAlert(data.data.data, "", "success");
                    //enterEncounter='';
                }


            });



        }


        $scope.fullRegistration=function (patient,others) {
			      var first_name=patient.first_name;
			      var middle_name=patient.middle_name;
			      var last_name=patient.last_name;
			      var gender=patient.gender;
			      var dob=patient.dob;
			      var mobile_number=patient.mobile_number;
			      
				  
				  
				  if (angular.isDefined(first_name)==false) {
                   return sweetAlert("Please Enter FIRST NAME before SAVING", "", "error");
                  } 
				  
				  else if (angular.isDefined(middle_name)==false) {
                   return sweetAlert("Please Enter MIDDLE NAME before SAVING", "", "error");
                  }
				  
				  else if (angular.isDefined(last_name)==false) {
                   return sweetAlert("Please Enter LAST NAME before SAVING", "", "error");
                  }
				  else if (angular.isDefined(patient.resedence_id)==false) {
                   return sweetAlert("Please type the Residence Name and choose from the suggestions", "", "error");
                   }
				   
				   else if (angular.isDefined(others.marital)==false) {
                   return sweetAlert("Please Enter Marital Status and choose from the suggestions", "", "error");
                   }
				   
				   else if (angular.isDefined(others.occupation)==false) {
                   return sweetAlert("Please Enter Occupations and choose from the suggestions", "", "error");
                   }
				   
				   else if (angular.isDefined(others.tribe)==false) {
                   return sweetAlert("Please Enter Tribe and choose from the suggestions", "", "error");
                   }				   
				   else if (angular.isDefined(others.country)==false) {
                   return sweetAlert("Please Enter Country and choose from the suggestions", "", "error");
                   }
				   else if (angular.isDefined(others.next_kin_residence)==false) {
                   return sweetAlert("Please Enter Next of kin Residences and choose from the suggestions", "", "error");
                   }
				   else if (angular.isDefined(others.relationship)==false) {
                   return sweetAlert("Please Enter Relationships and choose from the suggestions", "", "error");
                   }
				   			   
				  			   
				   //console.log(others.next_kin_residence);
				  var patient_residences=patient.resedence_id.residence_id;
				  var marital_status=others.marital.id;
			      var occupation=others.occupation.id;
			      var tribe=others.tribe.id;
			      var country=others.country.id;
			      var next_of_kin_name=others.next_of_kin_name;
			      var next_of_kin_resedence_id=others.next_kin_residence.residence_id;
			      var relationship=others.relationship.relationship;
			      var mobile_number_next_kin=others.mobile_number_next_kin;				  
				  var full_registration={"first_name":first_name,"middle_name":middle_name,"last_name":last_name,"dob":dob,"gender":gender,"mobile_number":mobile_number,"residence_id":patient_residences,"facility_id":facility_id,"user_id":user_id,"marital_status":marital_status,"occupation_id":occupation,"tribe":tribe,"country_id":country,"next_of_kin_name":next_of_kin_name,"next_of_kin_resedence_id":next_of_kin_resedence_id,"relationship":relationship,"mobile_number_next_kin":mobile_number_next_kin}
				  
				
				  
				 $http.post('/api/full_registration',full_registration).then(function(data) {
                 $scope.full_registration=data.data;
                 ////console.log(data.data);
				 if(data.data.status ==0){
					 
					 sweetAlert(data.data.data, "", "error");
				  }else{
                    
                    				  
					  $scope.patient = null;
					  full_registration=$scope.full_registration;
			          $scope.viewItemFull(full_registration);
					 
							
				  }
				});
			   
	
			   
					}	
					
					  $scope.viewItemFull = function (full_registration) {
					   $scope.quick_registration =full_registration;
					   
					   var quick_registration=full_registration;
				  //console.log(full_registration.first_name);
				  var modalInstance = $uibModal.open({
				  templateUrl: '/views/modules/registration/encounterModal.html',
				  size: 'lg',
				  animation: true,
				  controller: 'registrationModal',
				  resolve:{
                  quick_registration: function () {
					  //console.log($scope.quick_registration);
                  return $scope.quick_registration ;
                  }
                  }

				  
                  });
				
				modalInstance.result.then(function(quick_registration) {
                $scope.quick_reg = quick_registration;
			    //console.log($scope.quick_reg);
                });	
                }
					
					
					
			
			$scope.getPricedItems=function (patient_category_selected) {
			//console.log(patient_category_selected);
				$http.get('/api/getPricedItems/'+patient_category_selected).then(function(data) {
			      $scope.services=data.data;
			});
					
			}


        $scope.getPricedMortuary=function () {
            $http.get('/api/getMortuary').then(function(data) {
                $scope.mortuaries=data.data;
                //console.log($scope.mortuaries);
            });

        }

			
			
			
			//Patient Get Data From the database
			$scope.getpatient=function () {
				/**
            $http.get('/api/getpatient').then(function(data) {
                $scope.patients=data.data;
            });
			
						swal({
  title: 'Error!',
  html: $('<div>')
    .addClass('some-class')
    .text('SAVED'),
  animation: false,
  customClass: 'animated tada'
})
		*/	
			}
			
			
			
			$scope.cancelEncounte=function () {
				var sms='You have canceled service for '+data.data.first_name;
								
						swal({
  title: 'SERVICE CANCELED',
  html: $('<div>')
    .addClass('some-class')
    .text(''+sms+''),
  animation: false,
  customClass: 'animated tada'
});
				
			}
			
			$scope.openModal = function(quick_registration){
            $(".modal").modal("show");
			}
			
			
			
		
			//Patient Update
			$scope.updatepatient=function (patients) {
			var comit=confirm('Are you sure you want to Update '+patients.first_name);
			if(comit) {
			$http.post('/api/updatepatient', patients).then(function (data) {
			})
			$scope.getpatient();
			}
			else{
				return false;
			}
			}
			$scope.getpatient();
			
			
			/*Patient delete
			$scope.deletepatient=function (patients) {
			var comit=confirm('Are you sure you want to delete '+patients.first_name);
			if(comit){
			$http.get('/api/deletepatient/'+patients.id).then(function(data) {
			$scope.getpatient();
			})
			}
			else {
			return false;
			}
			}
			$scope.getpatient();
			*/
			
			//Patient Update Ame
			$scope.updatepatient=function (selectedpatient,residence,marital,occupation,
			tribe,country,gender) {
			var patient_info_toupdate = {
			"id":selectedpatient.id,
			"first_name":selectedpatient.first_name,
			"middle_name":selectedpatient.middle_name,
			"last_name":selectedpatient.last_name,
			"dob":selectedpatient.dob,
			"gender":gender.gender,
			"mobile_number":selectedpatient.mobile_number,
			"residence_id":residence.residence_id,
			"marital_id":marital.id,
			"occupation_id":occupation.id,
			"tribe_id":tribe.id,
			"country_id":country.id,
			"facility_id":$rootScope.currentUser.facility_id,
			}
			//console.log(patient_info_toupdate);
			////console.log(patient_info_toupdate)
			$http.post('/api/updatepatient', patient_info_toupdate).then(function (data) {
			$scope.patientss=data;
			//console.log(patientss);
			swal("You have Succesfully Update" +$scope.patientss);
			});
			}



	// exemptions======================================================


		$scope.exemption_type_list=function () {
			$http.get('/api/exemption_type_list').then(function(data) {
				$scope.exemption_types=data.data;


			});
		}

		$scope.exemption_type_list();

		$http.get('/api/getexemption_services/'+facility_id).then(function(data) {
			$scope.exemption_services=data.data;
		});


		$scope.exemption_registration=function (exempt,patientData) {

			//console.log(patientData,exempt)
			var status_id = 2;
			var reason_for_revoke = "..";



			if(exempt==undefined){
				swal(
					'Feedback..',
					'FILL ALL FIELDS',
					'error'
				)

			}

			else if (exempt.exemption_type_id==undefined ){
				swal(
					'Feedback..',
					'Please Select Exemption Category ',
					'error'
				)
			}

			else if (exempt.exemption_reason==undefined){
				swal(
					'Feedback..',
					'Please Fill  Reason(s) for This exemption ',
					'error'
				)
			}
			else if (exempt.service==undefined){
				swal(
					'Feedback..',
					'Please Choose Service ',
					'error'
				)
			}



			else{
var patient=patientData.id;
				var patient_category=exempt.service.patient_category;
				var service_category=exempt.service;
				var service_id=exempt.service.service_id;
				var price_id=exempt.service.price_id;
				var item_type_id=exempt.service.item_type_id;
				var patient_id=patient;
				var facility_id=exempt.service.facility_id;
				var user_id=$rootScope.currentUser.id;
				var payment_filter=exempt.exemption_type_id;

				var bill_category_id=exempt.exemption_type_id;
				var main_category_id=3;

				var enterEncounter={'payment_filter':payment_filter,'item_type_id':item_type_id,'patient_category':patient_category,'main_category_id':main_category_id,'bill_id':bill_category_id,
					'service_category':service_category,'service_id':service_id,'price_id':price_id,'patient_id':patient_id ,'facility_id':facility_id,'user_id':user_id};


				var status_id=2;

				var exemption_type_id= exempt.exemption_type_id;
				var exemption_reason= exempt.exemption_reason;
				var user_id= $rootScope.currentUser.id;
				var facility_id= $rootScope.currentUser.facility_id;
				var patient_id= patient;
				var status_id= status_id;
				var exemption_type_id=exempt.exemption_type_id;
				var exemption_reason= exempt.exemption_reason;
				var reason_for_revoke= reason_for_revoke;
				var description=exempt.description;

				formdata.append('exemption_type_id',exemption_type_id);
				formdata.append('exemption_reason',exemption_reason);
				formdata.append('user_id',user_id);
				formdata.append('facility_id',facility_id);
				formdata.append('patient_id',patient_id);
				formdata.append('reason_for_revoke',reason_for_revoke);
				formdata.append('status_id',status_id);
				var request = {
					method: 'POST',
					url: '/api/'+'patient_exemption',
					data: formdata,
					headers: {
						'Content-Type': undefined
					}

				};

				// SEND THE FILES.
				$http(request).then(function (data) {

						var msg = data.data.msg;
						$scope.ok = data.data.status;
						////console.log(data.data.status);
						var statuss = data.data.status;

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

					})
					.then(function () {
					});



			}
		}






		// exemptions======================================================


    }

})();