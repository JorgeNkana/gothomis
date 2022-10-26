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
    } ]) .controller('labController',labController);

    function labController($http, $auth, $rootScope,$state,$location,$scope,$uibModal,toastr,$mdDialog,$mdBottomSheet,$mdToast,Helper) {
		var facility_id =$rootScope.currentUser.facility_id;
		var user_id =$rootScope.currentUser.id;
		$scope.userName =$rootScope.currentUser.name;
		Helper.systemNotification(user_id);
		var loading = true;
		$scope.cancel = function () {
			$mdDialog.hide();
		};
		
		var formdata = new FormData();
		$scope.oneAtATime = true;
		
		$scope.getTheFiles = function ($files) {
			angular.forEach($files, function (value, key) {
				formdata.append(key, value);
			});
		};

		angular.element(document).ready(function () {
			if($state.current.name.toLowerCase() == 'samplecollection'){
				$scope.setTabSamples(1);
			}
		});
		
        $scope.activateOrDeactivateTestPrice = function (item_id,status) {

            $http.post('/api/activateOrDeactivateTestPrice',{item_id:item_id,status:status,dept_id:2}).then(function (response) {
                $scope.activiness = response.data;

            });
        }
        $scope.labItemsList = function () {

            $http.get('/api/labItemsList/'+2).then(function (response) {
                $scope.activiness = response.data;

            });
        }
  $scope.cancel = function () {
                                $mdDialog.hide();
                            };

         $scope.sampleReject=function(samples_numbers,sample) {

                var sample_no=samples_numbers.sample_no;
                var last_name=samples_numbers.last_name;
                var sub_department_name=samples_numbers.sub_department_name;
                var request_id=samples_numbers.order_id;
                var reason=sample.reason;
                var sample_validator=samples_numbers.sample_validator;
                var sample_collector_id=samples_numbers.sample_collector_id;
                var message=sample.reason;

                if (angular.isDefined(request_id)==false) {
                    return sweetAlert("Select TEST to be done", "", "error");
                }
                else{
                     var dataPost={"sample_collector_id":sample_collector_id,"sample_validator":sample_validator,"message":message,"reason":reason,"sample_no":sample_no,"order_control":3,"order_validator_id":user_id,"last_name":last_name,"facility_id":facility_id,"request_id":request_id};

   console.log(dataPost);
                    $http.post('/api/sampleCancel',dataPost).then(function(data) {
                        if(data.data.status ==0){
                            sweetAlert(data.data.data, "", "error");
                        }
                        else{
                            $scope.cancel();
                             var msg=" Sample No."+sample_no+"  Was successfully Cancelled ";
                            //
                            sweetAlert(msg, "", "success");
                        }});
                }
            };


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

                              $scope.getCancelledSampleReason= function(){
                    $http.get('/api/getSampleStatus').then(function(data) {
                    $scope.sampleStatuses=data.data;

                                  });
                              };



                     $scope.confirmReject=function(getTestRequest) {
                           console.log(angular.isDefined(getTestRequest.admission_id));

                                      $mdDialog.show({
                        controller: function ($scope) {
                            $scope.getTestRequest =getTestRequest;
                             if (angular.isDefined(getTestRequest.admission_id)==true) {
                                  var admission_id=getTestRequest.admission_id;
                                   $http.get('/api/getInfoForAdmittedPatient/'+admission_id ).then(function(data) {
                                   $scope.admited=data.data;
                                  });

                               }
                          $http.get('/api/getSampleStatus').then(function(data) {
                                   $scope.sampleStatuses=data.data;

                                  });

                            $scope.cancel = function () {
                                $mdDialog.hide();
                            };
                        },
                        templateUrl: '/views/modules/laboratory/rejectSample.html',
                        parent: angular.element(document.body),
                        clickOutsideToClose: false,
                        fullscreen: $scope.customFullscreen // Only for -xs, -sm breakpoints.
                    });




            };


				     $scope.PrintContent=function () {
                         //location.reload();
                         var DocumentContainer = document.getElementById('divtoprint');
                         var WindowObject = window.open("", "PrintWindow",
                             "width=100,height=35,top=50,left=450,toolbars=no,scrollbars=no,status=no,resizable=yes");
                         WindowObject.document.title = "PRINT SAMPLE STICKER: GoT-HOMIS";
                         WindowObject.document.writeln(DocumentContainer.innerHTML);
                         WindowObject.document.close();

                         setTimeout(function () {
                             WindowObject.focus();
                             WindowObject.print();
                             WindowObject.close();
                         }, 5000);

                     }


  $http.get('/api/getUsermenu/'+user_id ).then(function(data) {
            $scope.menu=data.data;
        });



        $scope.getSms=function (mobile,smsText) {
            $http.get('http://kiuta.co.tz/oneapi-php-master/examples.php?mobile='+mobile+'&sms='+smsText).then(
                function (response) {
                    toastr.success('','SMS SENT TO NASSORO');
                },
                function (data,response) {
                    toastr.success('','SMS SENT TO '+mobile);
                })
        }

// GET EQUIPMENTS INFO..
        $scope.getEquipmentInfo= function (equipementList) {

                //console.log(equipementList);
                var object =angular.extend(equipementList);
                var modalInstance = $uibModal.open({
                    templateUrl: '/views/modules/laboratory/equipmentsInfo.html',
                    size: 'lg',
                    animation: true,
                    controller: 'equipmentsInfo',
                    resolve:{
                        object: function () {
                            return object;
                        }
                    }
                });





        }

		$scope.setTabEquipments = function(newTab){
            $scope.tab = newTab;
            $scope.getEquipementList();
			$http.get('/api/getEquipementStatus').then(function(data) {
            $scope.getEquipementStatuses=data.data;
                $http.get('/api/getLabDepartments').then(function(data) {
                    $scope.LabDepartments=data.data;
                });

            });
        };


        $scope.setTabPendingResults = function(newTab){
            $scope.tab = newTab;
        };

	$scope.setTabBloodBank = function(newTab){
            $scope.tab = newTab;
            $scope.getEquipementList();
			$http.get('/api/getEquipementStatus').then(function(data) {
            $scope.getEquipementStatuses=data.data;
                $http.get('/api/getLabDepartments').then(function(data) {
                    $scope.LabDepartments=data.data;
                });

            });
        };

        $scope.setTabSamples = function(newTab){
            $scope.getLabTestRequests();
        };

        $scope.setTabRejectSample = function(newTab) {
            $scope.getCancelledSample();
            $scope.tab = newTab;
        }

        $scope.setTabSamplesProcessor = function(newTab){
            $scope.tab = newTab;
           // $scope.getCollectedSample();
           // $scope.getCancelledSample();
            $scope.getCollectedSampleDepartments();
           // $scope.getLabResults();
           // $scope.getApprovedResults();
			// $http.get('/api/getEquipementStatus').then(function(data) {
           //  $scope.getEquipementStatuses=data.data;
           //      $http.get('/api/getLabDepartments').then(function(data) {
           //          $scope.LabDepartments=data.data;
           //      });
           //
           //  });
        };

  $scope.setTabCollected = function(newTab){
            $scope.tab = newTab;
            $scope.getCollectedSample();
                 };

  $scope.setTabTestPrice = function(newTab){
            $scope.tab = newTab;
                  };

        $scope.setTabResultsAprrove = function(newTab){
            	$http.get('/api/getLabResults').then(function(data) {
                    $scope.LabResults = data.data;
                              });
        };

        $scope.setTabLabTests = function(newTab){
            $scope.tab = newTab;

			$http.get('/api/getEquipementStatus').then(function(data) {
            $scope.getEquipementStatuses=data.data;
                $http.get('/api/getLabDepartments').then(function(data) {
                    $scope.LabDepartments=data.data;
                });

            });
        };



		$scope.getLabDepartments = function(){
			$http.get('/api/getLabDepartments').then(function(data) {
            $scope.labDepartments=data.data;
            //console.log($scope.labDepartments);
            });
        };

		$scope.getLabTestPerMachine = function(test){
			if(angular.isDefined(test)==true){
			var equipment_id=test.equipment_id
			}else{
				var equipment_id=null;
			}
			var postData={equipment_id:equipment_id,facility_id:facility_id};
			loading = true;
			$http.post('/api/getLabTestPerMachine',postData).then(function(data) {
				$scope.registeredTests=data.data;
			});
			loading = false;
        };

        

          $scope.rejectedResultsFromMachines = function(pef){
       if(pef==undefined){
                var postData={facility_id:facility_id,start_date:null,end_date:null};
            }else{
               var postData={facility_id:facility_id,start_date:pef.start,end_date:pef.end};
            }

           
      $http.post('/api/rejectedResultsFromMachines',postData).then(function(data) {
            $scope.Machinerejects=data.data;
              });
        };
		$scope.getUnavailableTests = function(pef){
			 if(angular.isDefined(pef)==false){
                return sweetAlert("You must select date range", "", "error");
            }

            var postData={facility_id:facility_id,start_date:pef.start,end_date:pef.end};
			$http.post('/api/getUnavailableTests',postData).then(function(data) {
            $scope.unAvailableTests=data.data;
              });
        };


		$scope.getCollectedSampleDepartments = function(){
			$http.get('/api/getCollectedSampleDepartments/'+user_id).then(function(data) {

                    $scope.labDepartmentCollectedSamples = data.data;

            });
        };

		$scope.getLabResults = function(){
			$http.get('/api/getLabResults').then(function(data) {
                    $scope.LabResults = data.data;
                              });
        }
        //Get All test per specific order..
        $scope.validateLabResultsPerOrder = function(sub_department_id){
			$http.get('/api/validateLabResultsPerOrder/'+sub_department_id).then(function(data) {
                    $scope.validateLabResults = data.data;
                              });
        }


        $scope.validateLabResultsPerRequest= function(orders){
			var items = {order_id:orders.order_id,item_id:orders.item_id};

                            //approve results....


                $mdDialog.show({
                        controller: function ($scope) {

                            $http.post('/api/validateLabResultsPerRequest',items).then(function(data) {
                                $scope.singleTests = data.data[0];
                                $scope.PanelsTests =data.data[1];
                                $scope.patientPaticulars =data.data[2];
                                $scope.result_time= new Date();
                                $scope.approver=$rootScope.currentUser.name;
                                $http.get('/api/getUsermenu/'+user_id ).then(function(data) {
                                    $scope.menu=data.data;
                                });

                            });
                                    $scope.cancel = function () {
                                $mdDialog.hide();
                            };

                            $scope.getAttachedDocument=function(getTestRequest,resultEdited){


                                var sample_no = getTestRequest.sample_no;
                                var last_name = getTestRequest.full_name;
                                var sub_department_name = getTestRequest.sub_department_name;
                                var mobile_number = getTestRequest.mobile_number;
                                var msg = "Salamu ndugu " + last_name + ", majibu kutoka maabara yametumwa kwa daktari.";
                                var order_id = getTestRequest.order_id;
                               var results = resultEdited;
                                if (angular.isDefined(results) == false) {
                                    return sweetAlert("Enter Results for Sample# " + sample_no + " before Approving", "", "error");
                                }
                                else {
                                    var dataPost = {
                                        "results": resultEdited,
                                        "sample_no": sample_no,
                                        "order_control": 3,
                                        "verified_by": user_id
                                        ,
                                        "last_name": last_name,
                                        "facility_id": facility_id,
                                        "order_id": order_id,
                                        "ref_id":getTestRequest.id,
                                        "item_id":getTestRequest.item_id
                                    };
                                    $http.post('/api/approveLabResult', dataPost).then(function (data) {
                                        if (data.data.status == 0) {
                                            sweetAlert(data.data.data, "", "error");
                                        }
                                        else {
                                            var msg = "Results for  Sample No." + sample_no + "  Was successfully Approved";
                                            sweetAlert(msg, "", "success");
                                            $scope.timeApproved=new Date();
                                        }
                                    });

                                    if(mobile_number){
                                        $http.get('/api/processMobileNumber/'+mobile_number).then(function(data) {
                                            var mobile=data.data;
                                            $scope.getSms(mobile,msg);

                                        });
                                    }


                                }
                                // $mdDialog.show({
                                //               controller: function ($scope) {
                                //                     var sample=patient.sample_no;
                                //
                                //            console.log(patient);
                                //                     var uploadedFile="/labresults/"+sample+".pdf";
                                //                     $scope.patientInfo =patient;
                                //                     $scope.resultsFile=uploadedFile;
                                //                      $scope.cancel = function () {
                                //                      $scope.selectedPatient=null;
                                //                      $mdDialog.hide();
                                //                  };
                                //
                                //                   $scope.approveTestResults=function(getTestRequest) {
                                //
                                //                       var sample_no = getTestRequest.sample_no;
                                //                       var last_name = getTestRequest.full_name;
                                //                       var sub_department_name = getTestRequest.sub_department_name;
                                //                       var mobile_number = getTestRequest.mobile_number;
                                //                       var msg = "Salamu ndugu " + last_name + ", majibu kutoka maabara yametumwa kwa daktari.";
                                //                       var order_id = getTestRequest.order_id;
                                //                       var results = 'Patient Result Approved';
                                //                       if (angular.isDefined(results) == false) {
                                //                           return sweetAlert("Enter Results for Sample# " + sample_no + " before Approving", "", "error");
                                //                       }
                                //                       else {
                                //                           var dataPost = {
                                //                               "results": results,
                                //                               "sample_no": sample_no,
                                //                               "order_control": 3,
                                //                               "verified_by": user_id
                                //                               ,
                                //                               "last_name": last_name,
                                //                               "facility_id": facility_id,
                                //                               "order_id": order_id,
                                //                               "ref_id":getTestRequest.id,
                                //                               "item_id":getTestRequest.item_id
                                //                           };
                                //                           $http.post('/api/approveLabResult', dataPost).then(function (data) {
                                //                               if (data.data.status == 0) {
                                //                                   sweetAlert(data.data.data, "", "error");
                                //                               }
                                //                               else {
                                //                                   var items = {order_id:getTestRequest.order_id,item_id:getTestRequest.item_id};
                                //                                   $scope.dataLoading = true;
                                //                                   $http.post('/api/validateLabResultsPerRequest',items).then(function(data) {
                                //
                                //                                       $scope.singleTests = data.data[0];
                                //                                       $scope.PanelsTests =data.data[1];
                                //                                       $scope.patientPaticulars =data.data[2];
                                //                                       var msg = "Results for  Sample No." + sample_no + "  Was successfully Approved";
                                //                                       sweetAlert(msg, "", "success");
                                //                                   });
                                //
                                //                               }
                                //                           });
                                //
                                //                           if(mobile_number){
                                //                               $http.get('/api/processMobileNumber/'+mobile_number).then(function(data) {
                                //                                   var mobile=data.data;
                                //                                   $scope.getSms(mobile,msg);
                                //
                                //                               });
                                //                           }
                                //                       }
                                //
                                //                   }
                                //              },
                                //              templateUrl: '/views/modules/laboratory/readPdfResults.html',
                                //              parent: angular.element(document.body),
                                //              clickOutsideToClose: false,
                                //              fullscreen: $scope.customFullscreen // Only for -xs, -sm breakpoints.
                                //          });
                            }

                            $scope.print=function () {
                                //location.reload();
                                var DocumentContainer = document.getElementById('print_id');
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

                        },
                        templateUrl: '/views/modules/laboratory/getResultsToVerify.html',
                        parent: angular.element(document.body),
                        clickOutsideToClose: false,
                        fullscreen: $scope.customFullscreen // Only for -xs, -sm breakpoints.
                    });







        };

$scope.showUploadForm= function (patientInfo, order_id) {

                 $mdDialog.show({
                        controller: function ($scope) {
                            $scope.patientInfo =patientInfo;
                            $scope.order_id =order_id;
                              $scope.cancel = function () {
                                $scope.selectedPatient=null;
                                $mdDialog.hide();
                            };

                            // NOW UPLOAD LAB FILE.
                            $scope.uploadLabResults = function (patientInfo,explanation) {
                                var full_name =patientInfo.first_name+" "+patientInfo.middle_name+" "+patientInfo.last_name;
                                var sample_no=patientInfo.sample_no;
                                var order_id=patientInfo.order_id;
                                var item_id=patientInfo.item_id;
                                var file_name=sample_no+".pdf";
                                //console.log(id_user);
                                formdata.append('description', explanation.explanation);
                                formdata.append('full_name', full_name);
                                formdata.append('item_id', item_id);
                                formdata.append('sample_no', sample_no);
                                formdata.append('post_user', user_id);
                                formdata.append('order_id', order_id);
                                formdata.append('attached_image', file_name);




                                var request = {
                                    method: 'POST',
                                    url: '/api/' + 'uploadLabResults',
                                    data: formdata,
                                    headers: {
                                        'Content-Type': undefined
                                    }

                                };


                                swal({
                                    title: full_name,
                                    text: "Results for sample #"+sample_no+" Will be uploaded",
                                    type: 'warning',
                                    showCancelButton: true,
                                    confirmButtonColor: '#3085d6',
                                    cancelButtonColor: '#d33',
                                    confirmButtonText: 'Yes, send it!'
                                }).then(function () {
                                    $http(request).then(function (data) {
                                        //console.log(request);

                                        $scope.cancel();
                                        swal({
                                            title: '',
                                            html: $('<div>')
                                                .addClass('some-class')
                                                .text('' + data.data + ''),
                                            animation: false,
                                            customClass: 'animated tada'
                                        });


                                    })
                                        .then(function () {
                                        });
                                })


                            };


                        },
                        templateUrl: '/views/modules/laboratory/uploadLabResults.html',
                        parent: angular.element(document.body),
                        clickOutsideToClose: false,
                        fullscreen: $scope.customFullscreen // Only for -xs, -sm breakpoints.
                    });
};

  // NOW UPLOAD LAB FILE.
        $scope.uploadLabResults = function (patientInfo,explanation) {
            var full_name =patientInfo.first_name+" "+patientInfo.middle_name+" "+patientInfo.last_name;
            var sample_no=patientInfo.sample_no;
            var order_id=patientInfo.order_id;
            var item_id=patientInfo.item_id;
            var file_name=sample_no+".pdf";
             //console.log(id_user);
            formdata.append('description', explanation.explanation);
            formdata.append('full_name', full_name);
            formdata.append('item_id', item_id);
            formdata.append('sample_no', sample_no);
            formdata.append('post_user', user_id);
            formdata.append('order_id', order_id);
            formdata.append('attached_image', file_name);




         var request = {
                                method: 'POST',
                                url: '/api/' + 'uploadLabResults',
                                data: formdata,
                                headers: {
                                    'Content-Type': undefined
                                }

                            };


                            swal({
                                title: full_name,
                                text: "Results for sample #"+sample_no+" Will be uploaded",
                                type: 'warning',
                                showCancelButton: true,
                                confirmButtonColor: '#3085d6',
                                cancelButtonColor: '#d33',
                                confirmButtonText: 'Yes, send it!'
                            }).then(function () {
                                $http(request).then(function (data) {
                                    //console.log(request);

                                    $scope.cancel();
                                    swal({
                                        title: '',
                                        html: $('<div>')
                                            .addClass('some-class')
                                            .text('' + data.data + ''),
                                        animation: false,
                                        customClass: 'animated tada'
                                    });


                                })
                                    .then(function () {
                                    });
                            })


        };

        $scope.getLabResultsVerify = function(){
			$http.get('/api/validateLabResults').then(function(data) {
                    $scope.verifyLabResults = data.data;
                              });
        }

		$scope.getLabCollectedSample = function(sub_department_id){
            $scope.dataLoading = true;
            //console.log(sub_department_id);
            $http.get('/api/getLabCollectedSample/'+sub_department_id).then(function(data) {
                $scope.getLabCollectedSamples=data.data;

            }).finally(function () {
                $scope.dataLoading = false;
            });
                    };

		$scope.getLabTestRequests= function(){
			$http.get('/api/LabTestRequest/'+facility_id).then(function(data) {
            $scope.LabTestRequests=data.data;
            });
        };
		$scope.getCollectedSample= function(){
			$http.get('/api/getCollectedSample').then(function(data) {
            $scope.collectedSamples=data.data;

            });
        };
		$scope.getCancelledSample= function(){
			$http.get('/api/getCancelledSample').then(function(data) {
            $scope.cancelledSamples=data.data;

            });
        };

		$scope.changeEquip= function(test){
			var item_id=test.item_id;
			var item_name=test.item_name;
			$mdDialog.show({

                        controller: function ($scope) {
                           $scope.item_name=item_name;
                           $scope.item_id=item_id;
						   $scope.saveEquipChanges= function(equipement_id){
				       var postData={item_id:$scope.item_id,equipement_id:equipement_id};
			           $http.post('/api/saveEquipChanges',postData).then(function(data) {
                          if(data.data.status ==0){
                    sweetAlert(data.data.data, "", "error");
                             }
						  else{
							  $scope.cancel();
                    sweetAlert(data.data.data, "", "success");
                             }

                            });
                             };

                            $scope.cancel = function () {
                                $mdDialog.hide();
                            };
                        },
                        templateUrl: '/views/modules/laboratory/change_machine.html',
                        parent: angular.element(document.body),
                        clickOutsideToClose: false,
                        fullscreen: $scope.customFullscreen // Only for -xs, -sm breakpoints.
                    });
        };




		$scope.getUsersFromLabSections=function(section_id){


					  $mdDialog.show({

                    controller: function ($scope) {
						var postData={section_id:section_id};
			       $http.post('/api/getUsersFromLab',postData).then(function(data) {
				  $scope.labSectionUsers=data.data;

			       			});

							$scope.selectedSubDeptId=section_id;


				 $scope.labTechnologists= function (text) {
                return Helper.searchLabTechnologist(text)
                .then(function (response) {
                    return response.data;
                });
        };

			$scope.selectedLabTechnologist= function (user,event) {
               if(angular.isDefined(user)==true){
                $scope.technologist_id = user.id;
			   }

        };

		$scope.changeLabSections= function (labSectionUser,switch_status) {
            console.log(switch_status);
            console.log(labSectionUser);
			var section_id=labSectionUser.section_id;
			var technologist_id=labSectionUser.id;
			var isAllowed=switch_status;

  var DataToUpdate={section_id:section_id,technologist_id:technologist_id,isAllowed:isAllowed};
		    $http.post('/api/changeAccess',DataToUpdate).then(function(firstData) {
			var postData={section_id:section_id};
		      $http.post('/api/getUsersFromLab',postData).then(function(returnedData) {
				  $scope.labSectionUsers=returnedData.data;

			       			});

			       			});

        };

		$scope.saveLabTechnologists= function (section_id) {
               if(angular.isDefined($scope.technologist_id)==false){
            return sweetAlert("Please search lab technologist again", "", "error");
        		   }

               var DataToSave={section_id:section_id,technologist_id:$scope.technologist_id,isAllowed:true};
		    $http.post('/api/saveLabTechnologists',DataToSave).then(function(firstData) {

				var postData={section_id:section_id};
		      $http.post('/api/getUsersFromLab',postData).then(function(returnedData) {
				  $scope.labSectionUsers=returnedData.data;

			       			});

			       			});




        };




						    $scope.cancel = function () {
                            $scope.selectedPatient=null;
                            $mdDialog.hide();
                        };
                    },
                    templateUrl: '/views/modules/laboratory/allocate_to_sections.html',
                    parent: angular.element(document.body),
                    clickOutsideToClose: false,
                    fullscreen: $scope.customFullscreen // Only for -xs, -sm breakpoints.
                });





		};


$scope.previewRequest= function(LabTestRequest){
		     var order_id=LabTestRequest.order_id;
			 var item_id=LabTestRequest.item_id;
			 var postData={order_id:order_id,item_id:item_id};
			$http.get('/api/LabTestRequestPatient',postData).then(function(data) {
			        var patientPaticulars=data.data[0];
			        var getTestRequest=data.data[0];
			        var singleTests=data.data[0];

                $mdDialog.show({
                    locals: {'patientPaticulars':patientPaticulars,'getTestRequest':getTestRequest,'singleTests':singleTests
                    },
                    controller: function ($scope) {
                        $scope.patientPaticulars =patientPaticulars;
                        $scope.getTestRequest =getTestRequest;
                        var admission_id=$scope.patientPaticulars.admission_id;
                        $http.get('/api/patientWardBed/'+admission_id).then(function(data) {
                        $scope.getAdmisionInfos = data.data[0];

                            //console.log($scope.getAdmisionInfos);
                        });
                        $scope.singleTests =singleTests;
                        $scope.cancel = function () {
                            $scope.selectedPatient=null;
                            $mdDialog.hide();
                        };
                    },
                    templateUrl: '/views/modules/laboratory/labtestpreview.html',
                    parent: angular.element(document.body),
                    clickOutsideToClose: false,
                    fullscreen: $scope.customFullscreen // Only for -xs, -sm breakpoints.
                });

            });
        };

		$scope.getTestRequestAll= function(LabTestRequest){
			var filter = {
						order_id:LabTestRequest.order_id,
						item_id:LabTestRequest.item_id
					};

			$http.post('/api/LabTestRequestPatient',filter).then(function(data) {
                $mdDialog.show({
                    controller: function ($scope) {
                        $scope.tests=data.data;
						$scope.patientInfo=LabTestRequest;
                        var admission_id=LabTestRequest.admission_id;
                        $http.get('/api/patientWardBed/'+admission_id).then(function(data) {
							$scope.admisionInfo = data.data[0];
                        });
                        $scope.cancel = function () {
                            $scope.selectedPatient=null;
                            $mdDialog.hide();
                        };
						
						$scope.generateSampleNumber=function(test_name,sample_type,last_name,sub_department_name,request_id) {
							var dataPost={test_name:test_name,"sample_type":sample_type,"order_control":null,"order_validator_id":user_id,"last_name":last_name,"facility_id":facility_id,"request_id":request_id,sub_department_name:sub_department_name, visit_date_id:$scope.patientInfo.visit_date_id};

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


        $scope.saveTestResults=function(getTestRequest,results) {
            console.log(getTestRequest);
            var sample_no=getTestRequest.sample_no;
            var last_name=getTestRequest.last_name;
            var sub_department_name=getTestRequest.sub_department_name;
            var request_id=getTestRequest.request_id;
            var order_id=getTestRequest.order_id;
            var item_id=getTestRequest.item_id;
            var results=results;
            if (angular.isDefined(results)==false) {
                return sweetAlert("Enter Results for Sample# "+sample_no, "", "error");
            }
            else{
                 var dataPost={"item_id":item_id,"order_id":order_id,"results":results,"sample_no":sample_no,"order_control":3,"verified_by":user_id,"last_name":last_name,"facility_id":facility_id,"request_id":request_id};
                $http.post('/api/sendLabResult',dataPost).then(function(data) {
                    if(data.data.status ==0){
                        sweetAlert(data.data.data, "", "error");
                    }
                    else{
						$scope.getCollectedSampleDepartments(user_id);
                         var msg="Results for  Sample No."+sample_no+"  Was successfully Saved";
                              sweetAlert(msg, "", "success");
                    }});
            }
        }
        $scope.approveTestResults=function(getTestRequest) {

            var sample_no = getTestRequest.sample_no;
            var last_name = getTestRequest.full_name;
            var sub_department_name = getTestRequest.sub_department_name;
            var mobile_number = getTestRequest.mobile_number;
            var msg = "Salamu ndugu " + last_name + ", majibu kutoka maabara yametumwa kwa daktari.";
            var order_id = getTestRequest.order_id;
            var results = 'Patient Result Approved';
            if (angular.isDefined(results) == false) {
                return sweetAlert("Enter Results for Sample# " + sample_no + " before Approving", "", "error");
            }
            else {
                 var dataPost = {
                    "results": results,
                    "sample_no": sample_no,
                    "order_control": 3,
                    "verified_by": user_id
                     ,
                    "last_name": last_name,
                    "facility_id": facility_id,
                    "order_id": order_id,
					"ref_id":getTestRequest.id,
					"item_id":getTestRequest.item_id
                };
                $http.post('/api/approveLabResult', dataPost).then(function (data) {
                    if (data.data.status == 0) {
                        sweetAlert(data.data.data, "", "error");
                    }
                    else {
                        var items = {order_id:getTestRequest.order_id,item_id:getTestRequest.item_id};
                        $scope.dataLoading = true;
                        $http.post('/api/validateLabResultsPerRequest',items).then(function(data) {

                            $scope.singleTests = data.data[0];
                            $scope.PanelsTests =data.data[1];
                            $scope.patientPaticulars =data.data[2];
                        var msg = "Results for  Sample No." + sample_no + "  Was successfully Approved";
                        sweetAlert(msg, "", "success");
                    });

                }
                });

                if(mobile_number){
					$http.get('/api/processMobileNumber/'+mobile_number).then(function(data) {
                    var mobile=data.data;
                    $scope.getSms(mobile,msg);

                });
				}
            }

        }
                 //validateLabResultsPerRequest
            $scope.showResultsToVerify=function(){
                $http.post('/api/showResultsToVerify').then(function(data) {
                         $scope.singleTests = data.data[0];
                         $scope.PanelsTests = data.data[1];
                 });

              }


		$scope.getSamplesToTest= function(sample){
			var item ={item_id:sample.item_id,order_id:sample.order_id};
            $scope.dataLoading = true;
			$http.post('/api/getLabCollectedSamplePerOrderNumber',item).then(function(data) {

                $mdDialog.show({
                     controller: function ($scope) {

                         $scope.singleTests = data.data[0];
                         $scope.PanelsTests = data.data[1];
                         $scope.patientPaticulars = data.data[2];

                         $scope.cancel = function () {
                            $scope.selectedPatient=null;
                            $mdDialog.hide();
                        };
                    },
                    templateUrl: '/views/modules/laboratory/getSamplesToTest.html',
                    parent: angular.element(document.body),
                    clickOutsideToClose: false,
                    fullscreen: $scope.customFullscreen // Only for -xs, -sm breakpoints.
                });


            }).finally(function () {
                $scope.dataLoading = false;
            });



        };

        $scope.getTestComponents=function(panel,order_id) {
            $mdDialog.show({
                controller: function ($scope) {
                    var postData={"order_id":order_id,"panel_name":panel};
                    $http.post('/api/getPanelComponets',postData).then(function(data) {
                        $scope.getPanelComponets = data.data
                    });
                    $scope.cancel = function () {
                           $mdDialog.hide();
                    };

                    $scope.saveComponent = function(getPanelComponet) { //this is called with the submit
                        var componentsResults=[];
                        var field_id;
                        $scope.getPanelComponets.forEach(function (getPanelComponet) {
                            field_id = getPanelComponet.panel_compoent_name.replace($scope.regex, '_');
                            if ($('#' + field_id).val() != '') {
                                componentsResults.push({
                                    'panel_name': getPanelComponet.panel,
                                    'component_name': getPanelComponet.panel_compoent_name,
                                    'component_id': getPanelComponet.id,
                                    'order_id': getPanelComponet.order_id,
                                    'item_id': getPanelComponet.item_id,
                                    'minimum_limit': getPanelComponet.minimum_limit,
                                    'maximum_limit': getPanelComponet.maximum_limit,
                                    'si_units': getPanelComponet.si_units,
                                    'sample_no': getPanelComponet.sample_no,
                                    'user_id': user_id,
                                    'component_name_value':$('#'+field_id).val()
                                });

                            }


                        });

                        if(componentsResults.length != $scope.getPanelComponets.length){
                            return toastr.error('','All fields are required.');
                        }

                        $http.post('/api/saveComponentsResults',componentsResults).then(function(data) {
                            if(data.status==0){
                                toastr.error('',data.data.data);
                            }else{
                                toastr.success('','Results Saved ');
                            }
                            $scope.closeModal();
                        });
                    };

                },
                templateUrl: '/views/modules/laboratory/enterComponentsResults.html',
                parent: angular.element(document.body),
                clickOutsideToClose: false,
                fullscreen: $scope.customFullscreen // Only for -xs, -sm breakpoints.




            });
        };

        $scope.getAttachedDocument=function(getTestRequest){


                var sample_no = getTestRequest.sample_no;
                var last_name = getTestRequest.full_name;
                var sub_department_name = getTestRequest.sub_department_name;
                var mobile_number = getTestRequest.mobile_number;
                var msg = "Salamu ndugu " + last_name + ", majibu kutoka maabara yametumwa kwa daktari.";
                var order_id = getTestRequest.order_id;
                var results = 'Patient Result Approved';
                if (angular.isDefined(results) == false) {
                    return sweetAlert("Enter Results for Sample# " + sample_no + " before Approving", "", "error");
                }
                else {
                    var dataPost = {
                        "results": results,
                        "sample_no": sample_no,
                        "order_control": 3,
                        "verified_by": user_id
                        ,
                        "last_name": last_name,
                        "facility_id": facility_id,
                        "order_id": order_id,
                        "ref_id":getTestRequest.id,
                        "item_id":getTestRequest.item_id
                    };
                    $http.post('/api/approveLabResult', dataPost).then(function (data) {
                        if (data.data.status == 0) {
                            sweetAlert(data.data.data, "", "error");
                        }
                        else {
                            var items = {order_id:getTestRequest.order_id,item_id:getTestRequest.item_id};
                            $scope.dataLoading = true;
                            $http.post('/api/validateLabResultsPerRequest',items).then(function(data) {

                                $scope.singleTests = data.data[0];
                                $scope.PanelsTests =data.data[1];
                                $scope.patientPaticulars =data.data[2];
                                var msg = "Results for  Sample No." + sample_no + "  Was successfully Approved";
                                sweetAlert(msg, "", "success");
                            });

                        }
                    });

                    if(mobile_number){
                        $http.get('/api/processMobileNumber/'+mobile_number).then(function(data) {
                            var mobile=data.data;
                            $scope.getSms(mobile,msg);

                        });
                    }


            }
       // $mdDialog.show({
       //               controller: function ($scope) {
       //                     var sample=patient.sample_no;
       //
       //            console.log(patient);
       //                     var uploadedFile="/labresults/"+sample+".pdf";
       //                     $scope.patientInfo =patient;
       //                     $scope.resultsFile=uploadedFile;
       //                      $scope.cancel = function () {
       //                      $scope.selectedPatient=null;
       //                      $mdDialog.hide();
       //                  };
       //
       //                   $scope.approveTestResults=function(getTestRequest) {
       //
       //                       var sample_no = getTestRequest.sample_no;
       //                       var last_name = getTestRequest.full_name;
       //                       var sub_department_name = getTestRequest.sub_department_name;
       //                       var mobile_number = getTestRequest.mobile_number;
       //                       var msg = "Salamu ndugu " + last_name + ", majibu kutoka maabara yametumwa kwa daktari.";
       //                       var order_id = getTestRequest.order_id;
       //                       var results = 'Patient Result Approved';
       //                       if (angular.isDefined(results) == false) {
       //                           return sweetAlert("Enter Results for Sample# " + sample_no + " before Approving", "", "error");
       //                       }
       //                       else {
       //                           var dataPost = {
       //                               "results": results,
       //                               "sample_no": sample_no,
       //                               "order_control": 3,
       //                               "verified_by": user_id
       //                               ,
       //                               "last_name": last_name,
       //                               "facility_id": facility_id,
       //                               "order_id": order_id,
       //                               "ref_id":getTestRequest.id,
       //                               "item_id":getTestRequest.item_id
       //                           };
       //                           $http.post('/api/approveLabResult', dataPost).then(function (data) {
       //                               if (data.data.status == 0) {
       //                                   sweetAlert(data.data.data, "", "error");
       //                               }
       //                               else {
       //                                   var items = {order_id:getTestRequest.order_id,item_id:getTestRequest.item_id};
       //                                   $scope.dataLoading = true;
       //                                   $http.post('/api/validateLabResultsPerRequest',items).then(function(data) {
       //
       //                                       $scope.singleTests = data.data[0];
       //                                       $scope.PanelsTests =data.data[1];
       //                                       $scope.patientPaticulars =data.data[2];
       //                                       var msg = "Results for  Sample No." + sample_no + "  Was successfully Approved";
       //                                       sweetAlert(msg, "", "success");
       //                                   });
       //
       //                               }
       //                           });
       //
       //                           if(mobile_number){
       //                               $http.get('/api/processMobileNumber/'+mobile_number).then(function(data) {
       //                                   var mobile=data.data;
       //                                   $scope.getSms(mobile,msg);
       //
       //                               });
       //                           }
       //                       }
       //
       //                   }
       //              },
       //              templateUrl: '/views/modules/laboratory/readPdfResults.html',
       //              parent: angular.element(document.body),
       //              clickOutsideToClose: false,
       //              fullscreen: $scope.customFullscreen // Only for -xs, -sm breakpoints.
       //          });
        }

        $scope.getTestComponentsResults=function(sample_no,item_id,panel,order_id) {
            var postData={"sample_no":sample_no,"item_id":item_id,"order_id":order_id,"panel_name":panel};
            //console.log(postData);
            $http.post('/api/getPanelComponetsResults',postData).then(function(data) {
                $scope.getPanelComponets = data.data;
                var object ={"getPanelComponets":$scope.getPanelComponets};


                    $mdDialog.show({
                     controller: function ($scope) {

                         $scope.getPanelComponets = data.data;
                           $scope.results = {
                          'sections': []
                         };
                          $scope.results.sections =   $scope.getPanelComponets;
                          $scope.getPanelComponets =  $scope.getPanelComponets;
                         $scope.regex=/\s/g;


                         $scope.cancel = function () {
                            $scope.selectedPatient=null;
                            $mdDialog.hide();
                        };
                    },
                    templateUrl: '/views/modules/laboratory/verifyComponentsResults.html',
                    parent: angular.element(document.body),
                    clickOutsideToClose: false,
                    fullscreen: $scope.customFullscreen // Only for -xs, -sm breakpoints.
                });


            });
        };


        $scope.getSamplesToTestUsingSampleNo= function(sample_number){
            $scope.dataLoading = true;
			$http.get('/api/getLabCollectedSamplePerSampleNumber/'+sample_number).then(function(data) {

                var singleTests = data.data[0];
                var PanelsTests = data.data[1];
                var patientPaticulars = data.data[2];

                var object ={"patientPaticulars":patientPaticulars,"singleTests":singleTests,"PanelsTests":PanelsTests};
			        var modalInstance = $uibModal.open({
                    templateUrl: '/views/modules/laboratory/getSamplesToTest.html',
                    size: 'lg',
                    animation: true,
                    controller: 'SampleTestingModal',
                    resolve:{
                        object: function () {
                            return object;
                        }
                    }
                });
            }).finally(function () {
                $scope.dataLoading = false;
            });



        };

        $scope.getApprovedResults= function(){
            $scope.dataLoading = true;
			$http.get('/api/getApprovedResults').then(function(data) {
                $scope.approvedResults=data.data;

            }).finally(function () {
                $scope.dataLoading = false;
            });
        };

		$scope.setDefaultOffMachine= function(){

		   $http.post('/api/setDefaultMachine').then(function(data) {
                toastr.success('','Successfully Excuted Default machine');

            });
        };

		$scope.switchTestOffOn= function(test,switched){
			var postData={item_id:test.item_id, switched:switched, equipment_id:test.equipment_id};
			$http.post('/api/setTestOff',postData).then(function(data) {
				toastr.success('',data.data);
			});
        };

         $scope.approveComponent = function(getPanelComponet) { //this is called with the submit
              var componentsResults=[];
              var field_id;
              $scope.getPanelComponets.forEach(function (getPanelComponet) {
                       componentsResults.push({
                          'panel_name':getPanelComponet.panel,
                          'component_name':getPanelComponet.panel_compoent_name,
                          'component_id':getPanelComponet.id,
                          'order_id':getPanelComponet.order_id,
                          'item_id':getPanelComponet.item_id,
                          'minimum_limit':getPanelComponet.minimum_limit,
                          'maximum_limit':getPanelComponet.maximum_limit,
                          'si_units':getPanelComponet.si_units,
                          'sample_no':getPanelComponet.sample_no,
                          'user_id':user_id,
                          'component_name_value':getPanelComponet.component_name_value
                      });

              });

                if(componentsResults.length != $scope.getPanelComponets.length){
               return toastr.error('','All fields are required.');
                }

                 $http.post('/api/approveComponentsResults',componentsResults).then(function(data) {
                  if(data.status==0){
                     toastr.error('',data.data.data);
                  }else{
                      toastr.success('','Results Approveds');
                  }
                     $scope.closeModal();
                });
                };

    $scope.viewResults=function(tests){
	    $mdDialog.show({
                     controller: function ($scope) {
                       $scope.selectedTests=tests;
                       $scope.patientPaticulars=tests;

                         $scope.cancel = function () {
                             $mdDialog.hide();
                        };
 $http.get('/api/getUsermenu/'+user_id ).then(function(data) {
                                    $scope.menu=data.data;
                                });
$scope.PrintviewResults=function () {
            //location.reload();
            var DocumentContainer = document.getElementById('remote');
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
			$scope.reportElectonically = function (report) {
			var dataPost={report:report,user_id:user_id,facility_id:facility_id};
            $http.post('/api/reportElectonically',dataPost).then(function(data) {
                  if(data.data.status==0){
                     toastr.error('',data.data.data);
                  }else{
                      toastr.success('','Results were successfully reported');
                  }
							});
                        };

                    },
                    templateUrl: '/views/modules/laboratory/remoteReport.html',
                    parent: angular.element(document.body),
                    clickOutsideToClose: false,
                    fullscreen: $scope.customFullscreen // Only for -xs, -sm breakpoints.
                });

                 };

		$scope.reCollectTestRequests= function(cancelledSamples){
		      var order_id=cancelledSamples.order_id;
		      //console.log(order_id);


                $mdDialog.show({
                     controller: function ($scope) {
                        	$http.get('/api/getCanceledTest/'+order_id).then(function(data) {

                        $scope.patientPaticulars=data.data;
                        $scope.getTestRequest=data.data;
                        $scope.tests=data.data;

                        var admission_id=$scope.patientPaticulars.admission_id;
		
			$scope.generateSampleNumber=function(test_name,sample_type,last_name,sub_department_name,request_id) {


            var dataPost={test_name:test_name,"sample_type":sample_type,"order_control":null,"order_validator_id":user_id,"last_name":last_name,"facility_id":facility_id,"request_id":request_id,sub_department_name:sub_department_name, visit_date_id:  $scope.patientPaticulars.visit_date_id};

            $http.post('/api/generateSampleNumber',dataPost).then(function(data) {
                if(data.data.status ==0){
                    sweetAlert(data.data.data, "", "error");
                }
                else{

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

                }});
        };
                        $http.get('/api/patientWardBed/'+admission_id).then(function(data) {
                        $scope.getAdmisionInfos = data.data;
        });

         });
                         $scope.cancel = function () {
                            $scope.selectedPatient=null;
                            $mdDialog.hide();
                        };
                    },
                    templateUrl: '/views/modules/laboratory/LabTestRequestPatient.html',
                    parent: angular.element(document.body),
                    clickOutsideToClose: false,
                    fullscreen: $scope.customFullscreen // Only for -xs, -sm breakpoints.
                });




        };





		$scope.getEquipementList = function(){
			loading = true;
			$http.get('/api/getEquipementList').then(function(data) {
				$scope.equipementLists=data.data;
            });
			loading = false;
        };

		$scope.changeEquipStatus = function(equipement,on_off){
			if(loading == true)
				return;
			
			var dataPost={on_off:on_off,equipment_id:equipement.id};
			$http.post('/api/changeEquipmentStatus',dataPost).then(function(data) {
				$scope.getEquipementList();
            });

        };

		$scope.getTestPanels = function(){
			$http.get('/api/getTestPanel').then(function(data) {
            $scope.testPanels=data.data;

            });
        };

		$scope.getTestsAvailable= function(){
			$http.get('/api/LabTests').then(function(data) {
            $scope.LabTests=data.data;

            });
        };

		$scope.setTabObservation = function(newTab){
            $scope.tab = newTab;
			//$scope.setTabAdmission(1);
			//.. i need to pass ward ID LATER TO RESTRICT
			$http.get('/api/getAprovedAdmissionList').then(function(data) {
            $scope.admitted=data.data;

            });
        };

		$scope.setTabNursingCare = function(newTab){
            $scope.tab = newTab;
			//$scope.setTabAdmission(1);
			//.. i need to pass ward ID LATER TO RESTRICT
			$http.get('/api/getAprovedAdmissionList').then(function(data) {
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

			$http.get('/api/getAprovedAdmissionList').then(function(data) {
            $scope.admitted=data.data;

            });
        };


		$scope.getSampleStatus=function(pef){
			    var dataToPost = {user_id:user_id,facility_id: facility_id, start_date: pef.start, end_date: pef.end};
            $scope.start_date = pef.start;
            $scope.end_date = pef.end;

            $http.post('/api/getsampleReport', dataToPost).then(function (data) {
                $scope.sampleStatuses = data.data;

            });

		};

		$scope.getPermanceAtLab=function(pef){
			    var dataToPost = {user_id:user_id,facility_id: facility_id, start_date: pef.start, end_date: pef.end};
            $scope.start_date = pef.start;
            $scope.end_date = pef.end;

            $http.post('/api/getPermanceAtLab', dataToPost).then(function (data) {
                $scope.staffPerformances = data.data;

            });

		};

		$scope.getPermanceAtLabForSpecificStaff=function(pef){
			    var dataToPost = {post_user:user_id,facility_id: facility_id, start_date: pef.start, end_date: pef.end};
            $scope.start_date = pef.start;
            $scope.end_date = pef.end;

            $http.post('/api/getPermanceAtLab', dataToPost).then(function (data) {
                $scope.staffPerformances = data.data;

            });

		};

		$scope.rePrintResults=function(pef){
			    var dataToPost = {post_user:user_id,facility_id: facility_id, start_date: pef.start, end_date: pef.end};
            $scope.start_date = pef.start;
            $scope.end_date = pef.end;

            $http.post('/api/rePrintResults', dataToPost).then(function (data) {
                $scope.printResults = data.data;

            });

		};

		$scope.reportRemoteResults=function(pef){
			    var dataToPost = {post_user:user_id,facility_id: facility_id, start_date: pef.start, end_date: pef.end};
            $scope.start_date = pef.start;
            $scope.end_date = pef.end;

            $http.post('/api/reportResultsRemotely', dataToPost).then(function (data) {
                $scope.remoteResults = data.data;

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
	$http.post('/api/saveWardTypes',{"ward_type_name":wards.ward_type}).then(function(data) {

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





        $scope.addDevices= function (device) {
	             if (angular.isDefined(device)==false) {
                   return sweetAlert("Please Enter DEVICE ", "", "error");
                 }
                     else    if (angular.isDefined(device.equip_status)==false) {
                   return sweetAlert("Please Enter DEVICE Status ", "", "error");
                 }

				 else    if (angular.isDefined(device.subdepartment)==false) {
             return sweetAlert("Please Enter Department for this Device", "", "error");
                 }


						 else{

	             var dataPost={"eraser":0,"reagents":device.reagents,"equipment_name":device.equip_name,
                         "equipment_status_id":device.equip_status,"sub_department_id":device.subdepartment,"facility_id":facility_id,"user_id":user_id};

                 $http.post('/api/addDevices',dataPost).then(function(data) {
			     if(data.data.status ==0){
					 sweetAlert(data.data.data, "", "error");
				  }
			      else{
                     device.reagents= null;
                     device.equip_name= null;
                    sweetAlert(data.data.data, "", "success");
				  }});
                  }
        }


       $scope.addLabTest= function (test) {
	             if (angular.isDefined(test)==false) {
                   return sweetAlert("Please Enter TEST Name", "", "error");
                 }
                            else{

	             var dataPost={"erasor":0,"equipment_id":test.equipment_id,"minimum_limit":test.minimum_limit,"panel_compoent_name":test.name,
                         "si_units":test.si_units,"maximum_limit":test.maximum_limit,"user_id":user_id};

                 $http.post('/api/addLabTest',dataPost).then(function(data) {
			     if(data.data.status ==0){
					 sweetAlert(data.data.data, "", "error");
				  }
			      else{
                     test.name= null;
                     test.si_units= null;
                    sweetAlert(data.data.data, "", "success");
				  }});
                  }
        }




        $scope.addLabTestPanel= function (testPanel) {
	             if (angular.isDefined(testPanel)==false) {
                   return sweetAlert("Please Enter TEST PANEL ", "", "error");
                 }
                 else{
                     var item_id=testPanel.panel.item_id;
                     var equipment_id=testPanel.panel.equipment_id;

	             var dataPost={"erasor":0,"item_id":item_id,"equipment_id":equipment_id,"minimum_limit":testPanel.minimum_limit,"panel_compoent_name":testPanel.name,
                         "si_units":testPanel.si_units,"maximum_limit":testPanel.maximum_limit,"user_id":user_id};
	             //console.log(dataPost);
                 $http.post('/api/addLabTestPanel',dataPost).then(function(data) {
			     if(data.data.status ==0){
					 sweetAlert(data.data.data, "", "error");
				  }
			      else{
                     testPanel.name= null;
                     sweetAlert(data.data.data, "", "success");
				  }});
                  }
        }

        $scope.allocateTestToLab= function (equipementList,test) {
	             if (angular.isDefined(test)==false) {
                   return sweetAlert("Please Enter Test ", "", "error");
                 }

                 else{

                     var item_id= test.selectedItem.id;
                     var item_name= test.selectedItem.item_name;
                     var equipment_id=test.equipment_id;
					 var test_category=test.category;
					 var test_component=item_name;
					 if (angular.isDefined(test.name)==true) {
                       test_component=test.name;
                    }

	var dataPost={"item_name":item_name,"test_category":test_category,"erasor":0,"item_id":item_id,"equipment_id":equipment_id,"minimum_limit":test.minimum_limit,"panel_compoent_name":test_component,
                         "si_units":test.si_units,"maximum_limit":test.maximum_limit,"user_id":user_id};
	             $http.post('/api/addLabTestPanel',dataPost).then(function(data) {
			     if(data.data.status ==0){
					 sweetAlert(data.data.data, "", "error");
				  }
			      else{
                     test.name= null;
                     sweetAlert(data.data.data, "", "success");
				  }});
                  }
        }


        $scope.addLabPanel= function (test) {
	             if (angular.isDefined(test)==false) {
                   return sweetAlert("Please Enter LAB TEST PANEL", "", "error");
                 }
                            else{

	             var dataPost={"erasor":0,"equipment_id":test.equipment_id,"panel_name":test.name,
                        "user_id":user_id};

                 $http.post('/api/addLabPanel',dataPost).then(function(data) {
			     if(data.data.status ==0){
					 sweetAlert(data.data.data, "", "error");
				  }
			      else{
                     test.name= null;
                     test.si_units= null;
                    sweetAlert(data.data.data, "", "success");
				  }});
                  }
        };


		$scope.labQuickSettUp= function () {
			var postData={facility_id:facility_id,user_id:user_id};
			 $http.post('/api/quickLabSettings',postData).then(function(data) {
			     if(data.data.status ==0){
					 sweetAlert(data.data.data, "", "error");
				  }
			      else{
                         sweetAlert(data.data.data, "", "success");
				  }});
		       };


			$scope.addSingleTest= function (test) {




	             if (angular.isDefined(test)==false) {
                   return sweetAlert("Please Enter LAB TEST PANEL", "", "error");
                 }
                            else{

	             var dataPost={"erasor":0,"equipment_id":test.equipment_id,"panel_name":test.name,
                        "user_id":user_id};

                 $http.post('/api/addSingleTest',dataPost).then(function(data) {
			     if(data.data.status ==0){
					 sweetAlert(data.data.data, "", "error");
				  }
			      else{
                     test.name= null;
                     test.si_units= null;
                    sweetAlert(data.data.data, "", "success");
				  }});
                  }
        };


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

             var ward_types=[];
		$scope.showSearchWardTypes= function (searchKey) {

            $http.get('/api/searchWardTypes/'+searchKey).then(function(data) {
            ward_types=data.data;

            });
			return ward_types;
        }

        //lab reports kigamboni hc starts


        $scope.getLabReports = function (item,dates) {

            $http.post('/api/reportsPerTest',{item_id:item.id,start:dates.start,end:dates.end,facility_id:facility_id}).then(function (response) {
                $scope.testsReport = response.data;

            });
        }
        var labTests = [];
        $scope.showSearchTests = function (searchKey) {
            $http.post('/api/getTeststo',{searchKey:searchKey}).then(function (response) {
                labTests = response.data;
            });
            return labTests;
        }
        $scope.printResReport = function () {
            var DocumentContainer = document.getElementById('divtoprintrep');
            var WindowObject = window.open("", "PrintWindow",
                "width=900,height=700,top=50,left=450,toolbars=no,scrollbars=no,status=no,resizable=yes");
            WindowObject.document.title = "printout: GoT-HoMIS";
            WindowObject.document.writeln(DocumentContainer.innerHTML);
            WindowObject.document.close();

            setTimeout(function () {
                WindowObject.focus();
                WindowObject.print();
                WindowObject.close();
            });

        };

        var getPanels=[];
		$scope.showSearchPanels= function (searchKey) {
		    $http.get('/api/getPanels/'+searchKey).then(function(data) {
            getPanels=data.data;

            });
			return getPanels;
        }

		var ward_classes=[];
		$scope.showSearchWardClass= function (searchKey) {

            $http.get('/api/getWardClasses/'+searchKey).then(function(data) {
            ward_classes=data.data;

            });
			return ward_classes;
        }


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
		 $scope.getWardDetails= function (ward_id) {

			 $http.get('/api/getWardOneInfo/'+ward_id).then(function(data){
             wards=data.data;
					          });
		$http.get('/api/getBedsNumber/'+ward_id).then(function(data){
             beds_number=data.data;
					          });

            $http.get('/api/getBeds/'+ward_id).then(function(data) {
              beds=data.data;
              ////console.log(beds_number);
             var object =angular.extend({},wards, beds_number);
             ////console.log(beds_number);
			 var modalInstance = $uibModal.open({
				  templateUrl: '/views/modules/nursing_care/manageWardBeds.html',
				  size: 'lg',
				  animation: true,
				  controller: 'wardManagementModal',
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

		 $scope.getPatientsSentToTheatre= function () {

			$http.get('/api/getPatientSentToTheatre').then(function(data){
            $scope.patientsSentToTheatres=data.data;
			   });
		 }



			 $scope.getAdmission= function (patient,ward_id,admission_id) {



	$http.post('/api/getInstructions',{"patient_id":patient,"ward_id":ward_id}).then(function(data) {
            $scope.AdmissionNotes=data.data;
			patapata=$scope.AdmissionNotes;
			console.dir($scope.AdmissionNotes);
			          });

            $http.get('/api/getPatientInfo/'+patient).then(function(data) {
            $scope.quick_registration=data.data;


             var object = angular.extend({}, $scope.quick_registration, patapata);
			 var modalInstance = $uibModal.open({
				  templateUrl: '/views/modules/nursing_care/bedAllocation.html',
				  size: 'lg',
				  animation: true,
				  controller: 'nursingCareModal',
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



		//blood bank section start

        $scope.Issue_blood_request = function (item) {


            $mdDialog.show({
                controller: function ($scope) {
                    $scope.selectedPatient = item;

                    $scope.Blood_request_queue=function () {


                        $http.get('/api/Blood_request_queue/' + facility_id).then(function (data) {
                            $scope.blood_requests = data.data;

                        });
                    }



                    $scope.cancel = function () {
                        $scope.Blood_request_queue();
                        $mdDialog.hide();

                    };

                    $scope.Issue_blood_request = function (request) {
                        if (request.blood == undefined) {

                            swal(
                                'Error',
                                'Please Select Blood Group',
                                'error'
                            );
                            return;
                        }
                        if (request.unit == undefined) {

                            swal(
                                'Error',
                                'Please Fill  Required Units As per Request',
                                'error'
                            );
                            return;
                        }

                        swal({
                            title: 'Are you sure You Want Issue Blood Group  '+ request.blood+ ' To '+ request.medical_record_number+' ? ',

                            text: "This May no be easy to Reverse",
                            type: 'warning',
                            showCancelButton: true,
                            confirmButtonColor: '#3085d6',
                            cancelButtonColor: '#d33',
                            confirmButtonText: 'Yes!  ',
                            cancelButtonText: 'No, cancel!',
                            confirmButtonClass: 'btn btn-success',
                            cancelButtonClass: 'btn btn-danger',
                            buttonsStyling: false
                        }).then(function () {

                            var dataa={facility_id:facility_id,patient_id:request.patient_id,bag_no:request.bag_no,user_id:user_id,id:request.id,unit_issued:request.unit,unit_requested:request.unit_requested,blood_group_requested:request.blood_group,blood_group:request.blood};
                            if(request.blood_group !=undefined && request.blood_group !=request.blood){
                                swal({
                                    title: ' Blood Group Requested Is '+ request.blood_group+ ' And Not '+' '+ request.blood +' Continue any way ? ',

                                    text: "This May no be easy to Reverse",
                                    type: 'warning',
                                    showCancelButton: true,
                                    confirmButtonColor: '#3085d6',
                                    cancelButtonColor: '#d33',
                                    confirmButtonText: 'Yes!  ',
                                    cancelButtonText: 'No, cancel!',
                                    confirmButtonClass: 'btn btn-success',
                                    cancelButtonClass: 'btn btn-danger',
                                    buttonsStyling: false
                                }).then(function () {

                                    $http.post('/api/Issue_blood_request',dataa).then(function (data) {

                                        var sending = data.data.msg;

                                        if (data.data.status == 0) {
                                            swal(
                                                'Error',
                                                sending,
                                                'error'
                                            )
                                        }
                                        else {
                                            swal(
                                                'Success',
                                                sending,
                                                'success'
                                            )
                                        }


                                    })


                                }, function (dismiss) {
                                    // dismiss can be 'cancel', 'overlay',
                                    // 'close', and 'timer'
                                    if (dismiss === 'cancel') {

                                    }
                                })
                            }
                            else{

                                $http.post('/api/Issue_blood_request',dataa).then(function (data) {

                                    var sending = data.data.msg;

                                    if (data.data.status == 0) {
                                        swal(
                                            'Error',
                                            sending,
                                            'error'
                                        )
                                    }
                                    else {
                                        swal(
                                            'Success',
                                            sending,
                                            'success'
                                        )
                                    }


                                })
                            }



                        }, function (dismiss) {
                            // dismiss can be 'cancel', 'overlay',
                            // 'close', and 'timer'
                            if (dismiss === 'cancel') {

                            }
                        })

                    }


                },
                templateUrl: '/views/modules/BloodBank/BloodBank_requestModel.html',
                parent: angular.element(document.body),
                clickOutsideToClose: true,
                fullscreen: false,
            });
        }
        $scope.Blood_request_queue=function () {


            $http.get('/api/Blood_request_queue/' + facility_id).then(function (data) {
                $scope.blood_requests = data.data;

            });
        }
        $scope.Blood_request_queue();
        $scope.blood_bank_screening=function (screen,patient) {
            if (patient == undefined) {

                swal(
                    'Error',
                    'Please Select Client',
                    'error'
                );
                return;
            }
            if (screen == undefined) {

                swal(
                    'Error',
                    'Please Fill All Required Fields',
                    'error'
                );
                return;
            }
            var blood_screen={assay_type:screen.assay_type,blood_group:screen.blood_group,rh:screen.rh,rpr:screen.rpr,hbsag:screen.hbsag,hcv:screen.hcv,hiv:screen.hiv,patient_id:patient.patient_id,facility_id:facility_id,user_id:user_id};
            console.log(blood_screen);

            $http.post('/api/blood_bank_screening',blood_screen).then(function (data) {
                $scope.screened = data.data;
                var msg = data.data.msg;
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
            });
        }
        $scope.blood_stocks_balance=function () {

            $http.get('/api/blood_stock_balance/' +facility_id).then(function (data) {
                $scope.blood_stocks = data.data;
                $scope.Total_unit_balance=Total_unit_balance($scope.blood_stocks)

                $scope.xs = [];
                $scope.ys = [];

                for(var i=0;i< $scope.blood_stocks.length; i++){
                    $scope.xs.push($scope.blood_stocks[i].blood_group);
                    $scope.ys.push($scope.blood_stocks[i].available_unit);
                }

                $scope.labels=$scope.xs ;
                $scope.data =  $scope.ys;
            });
        }
        $scope.getBloodScreening=function (item) {
            var records={facility_id:facility_id,user_id:user_id,start_date:item.start_date,end_date:item.end_date}
            $http.post('/api/getBloodScreening',records).then(function (data) {
                $scope.screenings = data.data;

            });
        }
        $scope.NumberOfBloodUnitCollected=function (item) {
            var records={facility_id:facility_id,user_id:user_id,start_date:item.start_date,end_date:item.end_date}
            $http.post('/api/NumberOfBloodUnitCollected',records).then(function (data) {
                $scope.bloods = data.data;

            });
        }
        $scope.outreach='patient';
        $scope.blood_stocks_issued=function (item) {
            var records={facility_id:facility_id,user_id:user_id,start_date:item.start_date,end_date:item.end_date}
            $http.post('/api/blood_stock_issued',records).then(function (data) {
                $scope.blood_stock_issues = data.data;

                $scope.Total_unit_issued=Total_unit_issued($scope.blood_stock_issues)
                $scope.Total_unit_issued_out=Total_unit_issued_out($scope.blood_stock_issues)
                $scope.xs = [];
                $scope.ys = [];

                for(var i=0;i< $scope.blood_stock_issues[0].length; i++){
                    $scope.xs.push($scope.blood_stock_issues[0][i].blood_group);
                    $scope.ys.push($scope.blood_stock_issues[0][i].unit_issued);
                }

                $scope.labels1=$scope.xs ;
                $scope.data1 =  $scope.ys;
            });
        }
        $scope.blood_arrays=[];
        $scope.Add_blood_stock = function (stock) {

            for (var i = 0; i < $scope.blood_arrays.length; i++) {


                if ($scope.blood_arrays[i].blood_group == stock.blood_group) {

                    return;
                }
            }
            if (stock == undefined) {
                swal('Error', 'Fill all Fields', 'error')
            }
            else if (stock.blood_group == undefined) {
                swal('Error', 'Please Choose Blood Group', 'error')
            }
            else if (stock.unit == undefined) {
                swal('Error', 'Please enter Number of Blood Unit', 'error')
            }
            else {
                $scope.blood_arrays.push({
                    blood_group: stock.blood_group,
                    unit: stock.unit,
                    facility_id: facility_id,
                    user_id: user_id,
                    control: 'l',
                    control_in: 'r'
                });
                $scope.Total_unit=Total_unit_cal($scope.blood_arrays)

            }
        }
        var Total_unit_cal = function () {
            var TotalUnit = 0;
            for (var i = 0; i < $scope.blood_arrays.length; i++) {
                TotalUnit -= -(($scope.blood_arrays[i].unit));
            }

            return TotalUnit;

        }
        var Total_unit_balance = function () {
            var TotalUnitbalance = 0;
            for (var i = 0; i < $scope.blood_stocks.length; i++) {
                TotalUnitbalance -= -(($scope.blood_stocks[i].available_unit));
            }

            return TotalUnitbalance;

        }
        var Total_unit_issued = function () {
            var TotalUnitissued = 0;
            for (var i = 0; i < $scope.blood_stock_issues[0].length; i++) {
                TotalUnitissued -= -(($scope.blood_stock_issues[0][i].unit_issued));
            }

            return TotalUnitissued;

        }
        var Total_unit_issued_out = function () {
            var TotalUnitissued = 0;
            for (var i = 0; i < $scope.blood_stock_issues[1].length; i++) {
                TotalUnitissued -= -(($scope.blood_stock_issues[1][i].unit_issued));
            }

            return TotalUnitissued;

        }
        $scope.removeBloodArray = function (x) {

            $scope.blood_arrays.splice(x, 1);


        }
        $scope.blood_stock=function () {
            $http.post('/api/blood_stock',$scope.blood_arrays).then(function (data) {
                var msg = data.data.msg;
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
                    $scope.blood_stocks_balance();
                    $scope.blood_arrays=[];
                }
            });
        }
        $scope.blood_stock_issuing=function (issue,selectedPatient) {


            if (issue == undefined) {
                swal('Error', 'Fill all Fields', 'error')
                return;
            }
            else if (issue.blood_group == undefined) {
                swal('Error', 'Please Choose Blood Group', 'error')
                return;
            }
            else if (issue.unit == undefined) {
                swal('Error', 'Please enter Number of Blood Unit', 'error')
                return;
            }
            if(issue.unit_issued_out !=undefined){

                var  unit_issued_out=issue.unit_issued_out;
                var  patient_id=null;
            }
            if(selectedPatient !=undefined){
                var  unit_issued_out=null;
                var  patient_id=selectedPatient.patient_id;
            }
            var issued_blood={blood_group:issue.blood_group,unit_issued:issue.unit,facility_id: facility_id,
                user_id: user_id,control: 'l',patient_id:patient_id,unit_issued_out:unit_issued_out,out:issue.outt};
            $http.post('/api/blood_stock_issuing',issued_blood).then(function (data) {
                var msg = data.data.msg;
                var statuss = data.data.status;
                $scope.blood_stocks_balance();
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
            $scope.issue={};
            $('#out').val('');
            $('#unit_issued_out').val('');
            $('#blood_group').val('');
            $('#unit').val('');
        }



        $scope.startup = function(){
			$scope.getCollectedSampleDepartments();
		}
		$scope.startup();

 $scope.TaTReport=function(pef){
            var dataToPost = {post_user:user_id,facility_id: facility_id, start_date: pef.start, end_date: pef.end};
            $scope.start_date = pef.start;
            $scope.end_date = pef.end;

            $http.post('/api/TaTReport', dataToPost).then(function (data) {
                $scope.tats = data.data;

            });

        };
        
        
        $scope.print_tat=function () {
            //location.reload();
            var DocumentContainer = document.getElementById('tat_id');
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
        $scope.getSampleTesttedCount=function(pef){
            var dataToPost = {post_user:user_id,facility_id: facility_id, start_date: pef.start, end_date: pef.end};
            $scope.start_date = pef.start;
            $scope.end_date = pef.end;

            $http.post('/api/getSampleTesttedCount', dataToPost).then(function (data) {
                $scope.sampleCounts = data.data;

            });

        };

        $scope.getSampleTesttedCountprint=function () {
            //location.reload();
            var DocumentContainer = document.getElementById('labtestcoutnt');
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

        $http.get('/api/savedTbLeprosyRequestData').then(function (data) {
            $scope.tb_leps=data.data[0];
            $scope.tb_leps_result=data.data[1];

        });
        $http.get('/api/getLoginUserDetails/' + user_id).then(function (data) {
            $scope.menu = data.data;

        });
        $scope.saveTbLeprosyRequest=function (item) {
            if (item ==undefined) {
                swal("Please fill in all required fields","","error");    return;}
            if (item.reason_for_examination ==undefined) {swal("Please fill Reason for examination","","error"); return;
            }
            if (item.hiv_status ==undefined) {swal("Please fill HIV Status","","error");return;}
            if (item.pre_tb_treatment ==undefined) {swal("Please fill Previously treated for TB ","","error");return;}
            if (item.specimen_type ==undefined) {swal("Please fill Specimen type ","","error");return;}
            if (item.test_requested ==undefined) {swal("Please fill Test(s) requested ","","error");return;}
            var payloaded={
                dtlc_email:item.dtlc_email,
                dtlc_name:item.dtlc_name,
                hiv_status: item.hiv_status,
                pre_tb_treatment:item.pre_tb_treatment,
                reason_for_examination:item.reason_for_examination,
                rtlc_email:item.rtlc_email,
                rtlc_name:item.rtlc_name,
                specimen_type: item.specimen_type,
                test_requested:item.test_requested,
                user_id:user_id,
                visit_id:"visit_id",
                patient_id:"patient_id",


            }
            $http.post('/api/saveTbLeprosyRequest',payloaded).then(function (data) {

                swal("Request saved","","success");
            });
        }


        $scope.savedTbLeprosyRequestData=function () {

            $http.get('/api/savedTbLeprosyRequestData').then(function (data) {
                $scope.tb_leps=data.data[0];
                $scope.tb_leps_result=data.data[1];

            });
        }


        $scope.getTbLeprosyForm=function(pt){
            $mdDialog.show({
                controller: function ($scope) {
                    var user_id = $rootScope.currentUser.id;
                    var facility_id = $rootScope.currentUser.facility_id;
                    $http.get('/api/getLoginUserDetails/'+user_id )
                        .then(function(data) {
                            $scope.facilityIn=data.data[0];
                        });
                    $http.get('/api/getpatientAddress/'+pt.residence_id )
                        .then(function(data) {
                            $scope.address=data.data[0];
                        });

                    $scope.patientIfor = pt;
                    $scope.dtlc_email=pt.dtlc_email;
                    $scope.dtlc_name=pt.dtlc_name;
                    $scope.hiv_status= pt.hiv_status;
                    $scope.pre_tb_treatment=pt.pre_tb_treatment;
                    $scope.reason_for_examination=pt.reason_for_examination;
                    $scope.rtlc_email=pt.rtlc_email;
                    $scope.rtlc_name=pt.rtlc_name;
                    $scope.month_on_treatment=pt.month_on_treatment;
                    $scope.specimen_type=pt.specimen_type;
                    $scope.test_requested=pt.test_requested,
                        $scope.visit_id=  pt.visit_id;

                    $scope.saveTbLeprosyResult=function (item) {
                        if (item ==undefined) {
                            swal("Please fill in all required fields","","error");    return;}
                        if (item.reception_date ==undefined) {swal("Please fill Date and time of Reception","","error"); return;
                        }
                        if (item.specimen ==undefined) {swal("Please fill Specimen","","error");return;}
                        if (item.result ==undefined) {swal("Please fill Result ","","error");return;}
                        var payloaded={
                            comment:item.comment,
                            ear_lobe:item.ear_lobe,
                            laboratory_serial_no:item.laboratory_serial_no,
                            lesion: item.lesion,
                            reception_date:item.reception_date,
                            result:item.result,
                            specimen:item.specimen,
                            zn_fm: item.zn_fm,
                            user_id:user_id,
                            visit_id: $scope.visit_id,
                            patient_id: pt.patient_id,
                            appearance:item.appearance,
                            request_id:pt.id,
                            status:"posted"
                        }
                        $http.post('/api/saveTbLeprosyResult',payloaded).then(function (data) {
                            swal(data.data.msg,"","success");

                        });
                    }

                    $scope.cancel = function () {
                        $mdDialog.hide();

                    };



                },
                templateUrl: '/views/modules/Exemption/tb_leprosy.html',
                parent: angular.element(document.body),
                clickOutsideToClose: true,
                fullscreen: false,
            });

        }
        $scope.getTbLeprosyAproveForm=function(pt){
            $mdDialog.show({
                controller: function ($scope) {
                    var user_id = $rootScope.currentUser.id;
                    var facility_id = $rootScope.currentUser.facility_id;
                    $http.get('/api/getLoginUserDetails/'+user_id )
                        .then(function(data) {
                            $scope.facilityIn=data.data[0];
                        });
                    $http.get('/api/gettb_leprosyResultToApprove/'+pt.id )
                        .then(function(data) {
                            var resultss=data.data[0];
                            $scope.appearance=resultss.appearance;
                            $scope.comment=resultss.comment;
                            $scope.ear_lobe=resultss.ear_lobe;
                            $scope.laboratory_serial_no=resultss.laboratory_serial_no;
                            $scope.lesion=resultss.lesion;
                            $scope.reception_date=resultss.reception_date;
                            $scope.result=resultss.result;
                            $scope.specimen=resultss.specimen;
                            $scope.zn_fm=resultss.zn_fm;
                            $scope.reviewed_date=resultss.reviewed_date;
                            $scope.reviewed_time=resultss.reviewed_time;
                            $scope.reviewed_by=resultss.reviewed_by;
                        });
                    $scope.ProveTbLeprosyResult=function (zn_fm,laboratory_serial_no,reception_date,specimen,appearance,result,ear_lobe,lesion,comment) {
                        var payloadedap={
                            comment:comment,
                            ear_lobe:ear_lobe,
                            laboratory_serial_no:laboratory_serial_no,
                            lesion:lesion,
                            reception_date:reception_date,
                            result:result,
                            specimen:specimen,
                            zn_fm:zn_fm,
                            user_id:user_id,
                            visit_id: pt.visit_id,
                            patient_id: pt.patient_id,
                            appearance:appearance,
                            request_id:pt.id,
                            status:"verified"
                        }

                        $http.post('/api/ProveTbLeprosyResult',payloadedap).then(function (data) {
                            swal(data.data.msg,"","success");

                        });
                    }

                    $http.get('/api/getpatientAddress/'+pt.residence_id )
                        .then(function(data) {
                            $scope.address=data.data[0];
                        });
                    $scope.patientIfor = pt;
                    $scope.dtlc_email=pt.dtlc_email;
                    $scope.dtlc_name=pt.dtlc_name;
                    $scope.hiv_status= pt.hiv_status;
                    $scope.pre_tb_treatment=pt.pre_tb_treatment;
                    $scope.reason_for_examination=pt.reason_for_examination;
                    $scope.rtlc_email=pt.rtlc_email;
                    $scope.rtlc_name=pt.rtlc_name;
                    $scope.month_on_treatment=pt.month_on_treatment;
                    $scope.specimen_type=pt.specimen_type;
                    $scope.test_requested=pt.test_requested,
                        $scope.visit_id=  pt.visit_id;

                    $scope.saveTbLeprosyResult=function (item) {
                        if (item ==undefined) {
                            swal("Please fill in all required fields","","error");    return;}
                        if (item.reception_date ==undefined) {swal("Please fill Date and time of Reception","","error"); return;
                        }
                        if (item.specimen ==undefined) {swal("Please fill Specimen","","error");return;}
                        if (item.result ==undefined) {swal("Please fill Result ","","error");return;}
                        var payloaded={
                            comment:item.comment,
                            ear_lobe:item.ear_lobe,
                            laboratory_serial_no:item.laboratory_serial_no,
                            lesion: item.lesion,
                            reception_date:item.reception_date,
                            result:item.result,
                            specimen:item.specimen,
                            zn_fm: item.zn_fm,
                            user_id:user_id,
                            visit_id: $scope.visit_id,
                            patient_id: pt.patient_id,
                            appearance:item.appearance,
                            request_id:pt.id,
                            status:"posted"
                        }
                        $http.post('/api/saveTbLeprosyResult',payloaded).then(function (data) {
                            swal(data.data.msg,"","success");

                        });
                    }

                    $scope.cancel = function () {
                        $mdDialog.hide();

                    };



                },
                templateUrl: '/views/modules/Exemption/tb_leprosy_result.html',
                parent: angular.element(document.body),
                clickOutsideToClose: true,
                fullscreen: false,
            });

        }
$scope.lab_test_life=function (days) {
            if (days==undefined){
                swal("Enter number of Days for test To exists in Lab for testing","","info");
                return;
            }
            $http.post('/api/lab_test_life',{days:days.days,facility_id:facility_id,description:"lab life time"}).then(function (data) {
                swal(data.data.msg,"","success");

            });

        }

        $scope.printlabMonthlyReport=function () {
            //location.reload();
            var DocumentContainer = document.getElementById('monthly_id');
            var WindowObject = window.open("", "PrintWindow",
                "width=900,height=700,top=50,left=450,toolbars=no,scrollbars=no,status=no,resizable=yes");
            WindowObject.document.title = "printout: GoT-HOMIS";
            WindowObject.document.writeln(DocumentContainer.innerHTML);
            WindowObject.document.close();

            setTimeout(function () {
                WindowObject.focus();
                WindowObject.print();
                WindowObject.close();
            },2);
        }
        $scope.loadgroup_control_list=function(){


        $http.get('/api/getLabReportingControlList' ).then(function(data) {
            $scope.group_control_lists=data.data;
            $http.get('/api/indicator_groups').then(function (data) {
                $scope.groups = data.data;
            });
        });
            }

        $scope.loadgroup_control_list();
        $scope.mappings = [];
        $scope.addMapping = function(item){
            $scope.mappings.push(item);
        }

        $scope.addDispedGroup = function(item){
            $scope.mappings.push(item);
        }

        $scope.removeMapping = function(index){
            $scope.mappings.splice(index,1);
        }
        $scope.removeDispensedGroup = function(index){
            $scope.mappings.splice(index,1);
        }
        $scope.saveTracerStatus = function(){
            for(var i=0; i< $scope.group_control_lists.length; i++)
                $scope.group_control_lists[i].status = $('#stat_'+$scope.group_control_lists[i].id).val();

            Helper.overlay(true);
            $http.post('/api/labInticatorMapping',{save_status:$scope.group_control_lists}).then(function(data) {
                Helper.overlay(false);
                swal(data.data.msg,'','info');
            }, function(data){Helper.overlay(false);});

        }
        $scope.saveDispensedGroup = function(tracer_id, mappings){
            for(var i=0; i< mappings.length; i++)
                mappings[i]['lab_indicator_id'] = tracer_id;


            Helper.overlay(true);
            $http.post('/api/labInticatorMapping',{save_mapping:mappings}).then(function(data) {
                Helper.overlay(false);
                $scope.mappings = [];
                $scope.mappings.selectedItem = '';
                $scope.loadgroup_control_list();
                swal(data.data.msg,'','info');
            }, function(data){Helper.overlay(false);});
        }
        $scope.removeFromDispensedGroupMapping = function(index,mapping){
            Helper.overlay(true);
            $http.post('/api/removeFromLabIndicatorGroupMapping',mapping).then(function(data) {
                Helper.overlay(false);
                $scope.loadgroup_control_list();
                swal(data.data.msg,'','info');
            }, function(data){Helper.overlay(false);});
        }
        $scope.labMonthlyReport = function(df){
            Helper.overlay(true);
            $http.post('/api/labMonthlyReport',df).then(function(data) {
                Helper.overlay(false);
$scope.labss=data.data;
                swal(data.data.msg,'','info');
            }, function(data){Helper.overlay(false);});
        }

        $scope.getDetailedReportsdepartmentally = function (item) {
            $http.post('/api/getDetailedReportsdepartmentally', {
                "start": item.start,
                "end": item.end,
                "dept_id": 2,
                "facility_id": facility_id
            }).then(function (data) {
                $scope.cashdetailedData = data.data[0];
                $scope.insurancedetailedData = data.data[1];
                $scope.exemptiondetailedData = data.data[2];

                $scope.cashdetailedTotal = $scope.cashsum();
                $scope.exemptiondetailedTotal = $scope.exemptionsum();
                $scope.insurancedetailedTotal = $scope.insurancesum();

                $scope.selData = function (d, idx) {
                    $scope.selectedData = d;
                    $scope.selIdx = idx;
                };
                var report_generated_on = new Date() + "";
                $scope.report_generated_on = report_generated_on.substring(0, 24);


                $scope.isSelData = function (d) {
                    return $scope.selectedData === d;
                }
            });
        };
        $scope.cashsum = function () {
            var total = 0;
            for (var i = 0; i < $scope.cashdetailedData.length; i++) {
                total -= -($scope.cashdetailedData[i].sub_total);
            }
            return total;
        }
        $scope.insurancesum = function () {
            var total = 0;
            for (var i = 0; i < $scope.insurancedetailedData.length; i++) {
                total -= -($scope.insurancedetailedData[i].sub_total);
            }
            return total;
        }
        $scope.exemptionsum = function () {
            var total = 0;
            for (var i = 0; i < $scope.exemptiondetailedData.length; i++) {
                total -= -($scope.exemptiondetailedData[i].sub_total);
            }
            return total;
        }


        $scope.pharmacashprint=function () {

            //location.reload();
            var DocumentContainer = document.getElementById('pharmcash_id');
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

        var patientOpdPatients = [];
        $scope.showSearch = function(searchKey) {
            $http.post('/api/getAllOpdPatients', {
                "searchKey": searchKey,
                "facility_id": facility_id
            }).then(function(data) {
                patientOpdPatients = data.data;
            });
            return patientOpdPatients;
        }
		
		$scope.getLabResults=function(patient_id){
		$http.post('/api/getResults', {

                                "patient_id": patient_id,

                                "dept_id": 2

                            }).then(function(data) {

                                $scope.labInvestigations = data.data;
                 

                            });
}

$scope.getLabResultsDetails = function(item) {
                            var results = {

                                "patient_id": item.patient_id,

                                "account_id": item.account_id,

                                "dept_id": item.dept_id

                            };

                            $http.post('/api/getInvestigationResults', results).then(function(data) {

                                $scope.labResults = data.data;
                 

                            });

                        }

                        $scope.saveAmmendedResult=function(item){
                          console.log(item);
                          var dataa={
                            item_id:item.item_id,
                            sample_no:item.sample_no,
                            patient_id:item.patient_id,
                            verified_by:user_id,
                            description:item.ammended_result+ "  "+" ( AMMENDED RESULT)"
                          }
                           $http.post('/api/saveAmmendedResult', dataa).then(function(data) {

                               //$scope.labResults = data.data;
                if(data.data.status==1){
swal("Feedback",data.data.data,"success")
                }else{
swal("Feedback",Failed,"error")
                }

                            });
                        }

    }

})();