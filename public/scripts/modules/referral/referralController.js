/**
 * Created by Mazigo Jr on 2017-04-10.
 */
(function () {
    'use strict';
    angular
        .module('authApp')
        .controller('referralController',referralController);
    function referralController($scope,$rootScope,$state,$http,$mdDialog) {
        var facility_id=$rootScope.currentUser.facility_id;
        var user_id=$rootScope.currentUser.id;
        $http.post('/api/incomingReferrals',{"facility_id":facility_id}).then(function (data) {
           $scope.referrals = data.data[0];
		            
        });	
		
		
		 $http.post('/api/outgoingReferrals',{"facility_id":facility_id}).then(function (data) {
           $scope.outReferrals = data.data;
		   if(data.data.ResponseStatus==101){
			 //return  sweetAlert(data.data.Message, "", "info");
				     
			   
		   }
        });
		
		setInterval(function(){
		 $http.post('/api/incomingReferrals',{"facility_id":facility_id}).then(function (data) {
           $scope.referrals = data.data[0];
          
        });	
			
 $http.post('/api/outgoingReferrals',{"facility_id":facility_id}).then(function (data) {
           $scope.outReferrals = data.data;
		   if(data.data.ResponseStatus==101){
			// return  sweetAlert(data.data.Message, "", "info");
				     
			   
		   }
        });
}, 120000)
		
		
        $scope.getReferrals = function (item,historyExaminations,allergies) {

            $http.post('/api/getReferrals',{"sender_facility_id":item.sender_facility_id}).then(function (data) {
               $scope.patients= data.data;
            });
        }


        $scope.acceptReferral = function (item) {
            $mdDialog.show({
                controller:function ($scope) {
                    $scope.cancel = function () {
                        $mdDialog.cancel();
                    };

					 $scope.printForm = function () {
            //location.reload();
            var DocumentContainer = document.getElementById('divtoprint');
            var WindowObject = window.open("", "PrintWindow",
                "width=900,height=700,top=50,left=450,toolbars=no,scrollbars=no,status=no,resizable=yes");
            WindowObject.document.title = "PRINT PATIENT CARD: GoT-HOMIS";
            WindowObject.document.writeln(DocumentContainer.innerHTML);
            WindowObject.document.close();

            setTimeout(function () {
                WindowObject.focus();
                WindowObject.print();
                WindowObject.close();
            }, 0);

        }

					
                    var formdata = new FormData();

                    $scope.getTheFiles = function ($files) {

                        angular.forEach($files, function (value, key) {
                            formdata.append(key, value);

                        });
                        console.log(formdata)
                    };
                          $scope.patientData=item;
					      var visit_id=item.visit_id;
					      var facility_id=item.facility_id;
						  var dataPost={facility_id:facility_id,visit_id:visit_id};
						  			 
                    $scope.getAssociatedDetails = function() {
				         $http.post('/api/getAssociatedDetails',dataPost).then(function(data) {
                            $scope.historyExaminations = data.data[0];
                            $scope.allergies = data.data[1];
                            $scope.remoteInvestigationResults = data.data[2];
                            $scope.otherComplaints = data.data[3];
                            $scope.hpi = data.data[4];
						  });

                    };
						  
					$scope.getAssociatedDetails();	  
						 
                    $scope.getPricedItems = function(patient_category_selected) {
				var dataPost={facility_id:facility_id,patient_category:patient_category_selected};
                        $http.post('/api/getPricedItems',dataPost).then(function(data) {
                            $scope.services = data.data;
                        });

                    }
                    $http.get('/api/getexemption_services/' + facility_id).then(function (data) {
                        $scope.exemption_services = data.data;

                        $http.get('/api/exemption_type_list/' + user_id).then(function (data) {
                            $scope.exemption_types = data.data;

                        });
                    });
					
					
					$scope.saveReferralStatus=function(refferal_status,remoteInvestigationResults,allergies,hpi,historyExaminations,otherComplaints,patientData){
						
						if(angular.isDefined(refferal_status)==false){
							return  sweetAlert("Please Select status and write remarks", "", "error");  								
						}
					else if(angular.isDefined(refferal_status.remarks)==false){
							
					return  sweetAlert("Please Write remarks", "", "error");
						}
						
				else if(angular.isDefined(refferal_status.patient_status)==false){
							
				return  sweetAlert("Please Select Patient Status", "", "error");
						}
						
						
						$scope.showPrintButton=false;
					$scope.visit_id=patientData.visit_id;
					
                    $http.post('/api/reportRefferal',{patientData:patientData,otherComplaints:otherComplaints,historyExaminations:historyExaminations,hpi:hpi,allergies:allergies,remoteInvestigationResults:remoteInvestigationResults,receiver_id:user_id,facility_id:facility_id,from_facility_id:patientData.sender_facility,
					remarks:refferal_status.remarks,patient_status:refferal_status.patient_status,refferal_status:refferal_status.service_status,"patient_id":patientData.patient_id,"sender_id":patientData.sender_id,"visit_id":patientData.visit_id}).then(function (data) {
						
                        if(data.data.status==1){
	                     return  sweetAlert(data.data.data, "", "success");
  						 $scope.showPrintButton=true;
                         }else{
						 return  sweetAlert(data.data.data, "", "error");
				   		   
					   }
                    });
					
					};
					
                    $scope.SendToEncounterReferral=function (item) {

                        var records={
							patient_id:item.patient_id,
							id:item.patient_id,
							first_name:item.first_name,
							middle_name:item.middle_name,
							last_name:item.last_name,
							gender:item.gender,
							dob:item.dob,
							medical_record_number:item.medical_record_number,
						    facility_id:item.facility_id,user_id:user_id,
                            main_category_id:item.payment_services.patient_main_category_id,
							visit_id:item.visit_id,
							residence_id:item.residence_id,
                            item_type_id:item.payment_services.item_type_id,
                            item_price_id:item.payment_services.price_id,
                            payment_filter:item.payment_services.patient_category_id,
                            bill_id:item.payment_services.patient_category_id,
                            status_id:1,
                            quantity:1,

                        };
                        $http.post('/api/PostReferalBill',records).then(function (data) {
                            var statuss = data.data.status;
                            var msg = data.data.msg;
                            if (statuss == 0) {

                                swal(
                                    'Error',
                                    msg,
                                    'error'
                                );

                            }
                            else {
                                swal(
                                    'Success',
                                    msg,
                                    'success'
                                );
                            }
                        });
                    }
                    $scope.getPricedItems = function(patient_category_selected) {
                        //////console.log(patient_category_selected);
	var dataPost ={facility_id:facility_id,patient_category:patient_category_selected};
                        $http.post('/api/getPricedItems',dataPost).then(function(data) {
                            $scope.services = data.data;
                        });

                    }
                    $scope.searchPatientCategory = function() {
                        $http.get('/api/searchPatientCategory/'+facility_id).then(function(data) {
                            $scope.patientCategory = data.data;
                        });

                    };
                    $scope.searchPatientCategory();
                    $scope.exemption_registration = function (exempt,selectedPatient) {


                        var reason_for_revoke = "..";

                            var patient = selectedPatient.patient_id;



                        if (exempt == undefined) {
                            swal(
                                'Feedback..',
                                'Please Fill all required fields ',
                                'error'
                            )
                        }
                        else if (exempt.exemption_type_id == undefined) {
                            swal(
                                'Feedback..',
                                'Please Select Exemption Category ',
                                'error'
                            )
                        }

                        else if (exempt.exemption_reason == undefined) {
                            swal(
                                'Feedback..',
                                'Please Fill  Reason(s) for This exemption ',
                                'error'
                            )
                        }


                        else {

                            var status_id = 2;
                            var change = false;
                            var price = exempt;
                            var item_id = exempt.service.service_id;
                            var item_price_id = exempt.service.price_id;
                            var item_type_id = exempt.service.item_type_id;
                            var patient = patient;
                            var exemption_type_id = exempt.exemption_type_id.id;
                            var main_category_id = exempt.exemption_type_id.pay_cat_id;
                            var user_id = $rootScope.currentUser.id;
                            var facility_id = $rootScope.currentUser.facility_id;
                            var patient_id = patient;
                            var bill_id = exempt.exemption_type_id.id;
                            var exemption_reason = exempt.exemption_reason;
                            var reason_for_revoke = reason_for_revoke;
                            var description = exempt.description;
                            formdata.append('change', change);
                            formdata.append('price', price);
                            formdata.append('item_id', item_id);
                            formdata.append('item_price_id', item_price_id);
                            formdata.append('item_type_id', item_type_id);
                            formdata.append('payment_filter', exemption_type_id);
                            formdata.append('quantity', 1);
                            formdata.append('main_category_id', main_category_id);
                            formdata.append('bill_id', bill_id);
                            formdata.append('exemption_type_id', exemption_type_id);
                            formdata.append('exemption_reason', exemption_reason);
                            formdata.append('user_id', user_id);
                            formdata.append('facility_id', facility_id);
                            formdata.append('patient_id', patient_id);
                            formdata.append('reason_for_revoke', reason_for_revoke);
                            formdata.append('status_id', status_id);
                            var request = {
                                method: 'POST',
                                url: '/api/' + 'patient_exemption',
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
                                if (statuss == 0) {

                                    swal(
                                        'Error',
                                        msg,
                                        'error'
                                    );

                                }
                                else {
                                    swal(
                                        'Success',
                                        msg,
                                        'success'
                                    );
                                }
                            })
                                .then(function () {
                                });


                        }
                    }

                },
                templateUrl: '/views/modules/referral/refferal_details.html',
                parent: angular.element(document.body),
                clickOutsideToClose: false,
                fullscreen: true,
            });

        }
    }
})();