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
                onChange(scope, {
                    $files: event.target.files
                });
            });
        };
        return {
            link: fn_link
        }
    }])
        .controller('patientController', patientController);

    function patientController($http, $auth, $rootScope, $state, $location, $scope, $uibModal, toastr, $mdDialog, $mdBottomSheet, $mdToast, Helper) {
        var formdata = new FormData();
        $scope.today_date = new Date();
        $scope.cancel = function () {
            $mdDialog.hide();
        };
		
		//NHIF
		$scope.verifyNhifCard=function(client) { 
           var postData={card_number:client.card_no,account_id:client.account_id};           
          $http.post('/api/verify-nhif-card', postData).then(function (response) {
                    $scope.verifiedCards = response.data;
                      if (response.data.StatusCode == 0) {
                        return toastr.error('', 'No Internet connection');
                    }
            else if (response.data.StatusCode == 500) {
           return sweetAlert('Please Enter Correct Card Number', "", "error");

                 }
            else if (response.data.AuthorizationStatus == 'REJECTED') {
                        var FullName = response.data.FullName;
                        var remarks = response.data.Remarks;
                        var message = FullName + " , " + remarks;

                        return sweetAlert(message, "", "error");
              }
            else if (response.data.AuthorizationStatus == 'ACCEPTED') {

              return  sweetAlert('Authorization Number : '+response.data.AuthorizationNo+', MembershipNo '+response.data.MembershipNo,response.data.FirstName+' '+response.data.MiddleName+' '+response.data.LastName,'success');

               
              }
                           
                            });
      
        };

        $scope.getNonVerified =function(pef) {
            if(angular.isDefined(pef)==false){
                return sweetAlert('Please Select Date Range for the Non verified Clients');
            }
            var start_date=pef.start;
            var end_date=pef.end;
            var postData={start_date:start_date,end_date:end_date};
             $http.post('/api/get-non-verified', postData).then(function (response) {
                                $scope.nonverifiedLists = response.data;
                            });
            };

             $scope.getNonCollectedCards =function(date_Range = {}) {
            
             $http.post('/api/get-non-collected-cards', date_Range).then(function (response) {
                                $scope.nonCardCollectedLists = response.data;
                            });
            };

            $scope.giveNhifCard =function(patient) {
                var account_id=patient.account_id;
                var postData={account_id:account_id};
             $http.post('/api/give-cards',postData).then(function (response) {
                               $scope.getNonCollectedCards();  
                        sweetAlert(response.data.Message,'',response.data.status);
                            });
            };
		//END NHIF
		
        $scope.getTheFiles = function ($files) {
            angular.forEach($files, function (value, key) {
                formdata.append(key, value);
            });
        };
        var residence;
        var residence_id;
        var resedence_id_kin;
        var facility_id = $rootScope.currentUser.facility_id;
        var user_id = $rootScope.currentUser.id;
        var self = this;
        //Residence
        $scope.getResidence = function (text) {
            return Helper.getResidence(text)
                .then(function (response) {
                    return response.data;
                });
        };
		
        $scope.getReferringFacilities = function (text) {
            return Helper.getReferringFacilities(text)
                .then(function (response) {
                    return response.data;
                });
        };
		
	$scope.mali=true;

  $scope.existsMali = function(mali) {
	  $scope.showMali=true;
	  if(angular.isDefined($scope.mali)==false || $scope.mali==false){
          $scope.showMali=false;
		 
		    }
	  
	
	  $scope.mali=mali;
  };
  
$scope.getMahudhuriOPDRegistration = function (pef) {

            if (angular.isDefined(pef) == false) {
                return sweetAlert("You must select date range", "", "error");
            }
            var facilityDetails=[];

            $http.get('/api/getLoginUserDetails/' + user_id).then(function (cardTitle) {
                $scope.facility_address = cardTitle.data[0];
                facilityDetails= cardTitle.data[0];
                console.log($scope.facility_address);

            });
            var dataToPost = {facility_id:facility_id, start_date: pef.start, end_date: pef.end};
            $scope.start_date = pef.start;
            $scope.end_date = pef.end;

            $http.post('/api/getRegistrationReports', dataToPost).then(function (data) {
                $scope.opd_mahudhurio = data.data[0][0];
                $scope.opd_mahudhurio_marudio = data.data[1][0];
                $scope.defaultValue = 0;

            });

        };
 	

$scope.getMahudhurioByArea = function (pef) {

            if (angular.isDefined(pef) == false) {
                return sweetAlert("You must select date range", "", "error");
            }
            var facilityDetails=[];

            $http.get('/api/getLoginUserDetails/' + user_id).then(function (cardTitle) {
                $scope.facility_address = cardTitle.data[0];
                facilityDetails= cardTitle.data[0];

            });
            var dataToPost = {facility_id:facility_id, start_date: pef.start, end_date: pef.end};
            $scope.start_date = pef.start;
            $scope.end_date = pef.end;

            $http.post('/api/getMahudhurioByArea', dataToPost).then(function (data) {
                $scope.area_attendances = data.data;
            });

        };
$scope.getMahudhurioChfByArea = function (pef) {

            if (angular.isDefined(pef) == false) {
                return sweetAlert("You must select date range", "", "error");
            }
            var facilityDetails=[];

            $http.get('/api/getLoginUserDetails/' + user_id).then(function (cardTitle) {
                $scope.facility_address = cardTitle.data[0];
                facilityDetails= cardTitle.data[0];

            });
            var dataToPost = {facility_id:facility_id, start_date: pef.start, end_date: pef.end};
            $scope.start_date = pef.start;
            $scope.end_date = pef.end;

            $http.post('/api/getMahudhurioChfByArea', dataToPost).then(function (data) {
                $scope.chf_area_attendances = data.data;
            });

        };
$scope.getMahudhurioByCategory = function (pef) {

            if (angular.isDefined(pef) == false) {
                return sweetAlert("You must select date range", "", "error");
            }
            var facilityDetails=[];

            $http.get('/api/getLoginUserDetails/' + user_id).then(function (cardTitle) {
                $scope.facility_address = cardTitle.data[0];
                facilityDetails= cardTitle.data[0];

            });
            var dataToPost = {facility_id:facility_id, start_date: pef.start, end_date: pef.end};
            $scope.start_date = pef.start;
            $scope.end_date = pef.end;

            $http.post('/api/getMahudhurioByCategory', dataToPost).then(function (data) {
                $scope.category_attendances = data.data;
            });

        };
$scope.getMahudhurioByNationality = function (pef) {

            if (angular.isDefined(pef) == false) {
                return sweetAlert("You must select date range", "", "error");
            }
            var facilityDetails=[];

            $http.get('/api/getLoginUserDetails/' + user_id).then(function (cardTitle) {
                $scope.facility_address = cardTitle.data[0];
                facilityDetails= cardTitle.data[0];

            });
            var dataToPost = {facility_id:facility_id, start_date: pef.start, end_date: pef.end};
            $scope.start_date = pef.start;
            $scope.end_date = pef.end;

            $http.post('/api/getMahudhurioByNationality', dataToPost).then(function (data) {
                $scope.nationality_attendances = data.data;
            });

        };

		$scope.print = function(){
			Helper.printHTML($('.to-print').html(),facility_id);
		}
		$scope.print1 = function(){
			Helper.printHTML($('.to-print1').html(),facility_id);
		}
		$scope.PrintContentnation = function(){
			Helper.printHTML($('.PrintContentnationId').html(),facility_id);
		}

		$scope.displayPatientInfo = function (selectedPatient) {
                       $scope.patients=selectedPatient;
        };
		
		$scope.assignCardNumber= function (cardNo) {
			if(angular.isDefined(cardNo)==false){
				 return sweetAlert('Please Enter Card Number', "", "error");

			}
                       var patient_id=$scope.patients.patient_id;
                       var first_name=$scope.patients.first_name;
                       var middle_name=$scope.patients.middle_name;
                       var last_name=$scope.patients.last_name;
                       var last_name=$scope.patients.last_name;
					   
					     $scope.dataLoading = true;
            var creditials = {
                "cardNo": cardNo,
                "FacilityCode": "011",
                "patient_id": patient_id,
                "UserName": "Got-homis"
            };
            $http.post('/api/authorizeCardFromMember', creditials).then(
                function (response) {
                    //////console.log(response.data);
                    if (response.data.StatusCode == 0) {
                        return toastr.error('', 'NHIF connection failed.check connectivity');

                    } else if (response.data.StatusCode == 101) {
						 var remarks = response.data.data;
                       
                        return sweetAlert(remarks, "", "info");
                        

                    } 
					
					else if (response.data.StatusCode == 102) {
						 var remarks = response.data.data;
                       
                        return sweetAlert(remarks, "", "success");
                        

                    } 
					
					else if (response.data.StatusCode == 500) {
                        return sweetAlert('Please Enter Correct Card Number', "", "error");

                    } 
					
					else if (response.data.StatusCode == 400) {
                        return sweetAlert('NHIF Server not reachable,Please contact NHIF for support', "", "info");
                    }

					
					
					
					else if (response.data.StatusCode == 200 && response.data.MembershipNo == null) {
                        var remarks = response.data.StatusDescription;
                        var AuthorizationStatus = response.data.AuthorizationStatus;
						var message=remarks+":"+AuthorizationStatus;
                        return sweetAlert(message, "", "error");
                    } else if (response.data.StatusCode == 200 && response.data.CardStatusID == 6) {
                        var remarks = response.data.StatusDescription;
                        var FullName = response.data.FullName;
                        var message = FullName + ", " + remarks;
                        return sweetAlert(message, "", "error");
                    } else if (response.data.StatusDescription == 'Active') {
                        //////console.log($scope.nhif_patient);

                        $scope.dataLoading = false;
						 $scope.patientData = {
                                    "first_name": response.data.FirstName,
                                    "middle_name": response.data.MiddleName,
                                    "last_name": response.data.LastName,
                                    "gender": response.data.Gender,
                                    "dob": response.data.DateOfBirth,
                                    "AuthorizationNo": response.data.AuthorizationNo,
                                    "membership_number": response.data.MembershipNo,
                                    "card_no": response.data.CardNo,
									"SchemeId": response.data.SchemeId
                                };
						

                        $mdDialog.show({
                            controller: function ($scope) {
                                //////console.log(response.data);
                               


                                $scope.cancel = function () {
                                    $scope.selectedPatient = null;
                                    $mdDialog.hide();
                                };
                            },
                            templateUrl: '/views/modules/registration/insuarance.html',
                            parent: angular.element(document.body),
                            clickOutsideToClose: false,
                            fullscreen: $scope.customFullscreen // Only for -xs, -sm breakpoints.
                        });


                    } else if (response.data.StatusDescription == 'Revoked') {
                        $scope.dataLoading = false;
                        var FullName = response.data.FullName;
                        var remarks = response.data.Remarks;
                        var message = FullName + " , " + remarks;

                        return sweetAlert(message, "", "error");
                    }
					else{
                       return sweetAlert('NHIF Server not reachable,Please contact NHIF for support', "", "info");
                    }


                },
                function (data) {
                    // Handle error here
                    toastr.error('', 'Card No. Not Found in Database!');
                }).finally(function () {
                $scope.dataLoading = false;
            });
					   
					   
					   
					   
					   
					   
					   
					   
					   
					   
        };
        $scope.getPatientToEdit = function (text) {
            return Helper.getPatientToEdit(text)
                .then(function (response) {
                    return response.data;
                });
        };
          
		$scope.getPatientToEncounter = function (text) {
            return Helper.getPatientToEncounter(text)
                .then(function (response) {
                    return response.data;
                });
        };
		
        $scope.selectedPatientToEdit = function (patient, ev) {
            var info = {};
            if (typeof  patient != 'undefined') {
                var id = patient.id;
                console.log(id);
            }
            $scope.patient = patient;
            if (typeof patient != 'undefined') {
                $mdDialog.show({
                    controller: function ($scope) {
                        $scope.patientLoaded = patient;
                        $scope.infos = info;
                        var id = patient.id;
                        $http.get('/api/patientsInformation/' + id).then(function (data) {
                            $scope.infos = data.data[0];
                            console.log($scope.infos)
                        });
                        $scope.cancel = function () {
                            $scope.searchText = {};
                            $mdDialog.hide();
                        };
                        $scope.patient_edit = function (patient, residence) {
                            console.log(residence)
                            var resident_id = residence.residence_id;
                            console.log(resident_id);
                            var marital_id = patient.marital;
                            var country_id = patient.country_name.id;
                            var occupation_id = patient.occupation.id;
                            var tribe_id = patient.tribe.id;
                            console.log(patient);
                            console.log(country_id);
                            console.log(occupation_id);
                            console.log(marital_id);
                            console.log(tribe_id);
                            var first_name = patient.first_name;
                            var gender = patient.gender;
                            var patient_id = patient.id;
                            var middle_name = patient.middle_name;
                            var last_name = patient.last_name;
                            var dob = patient.dob;
                            if (patient.dob instanceof Date) {
                                dob = moment(patient.dob).format('YYYY-MM-DD');
                            }
                            var mobile_number = patient.mobile_number;
                            var edit_patient = {
                                'first_name': first_name,
                                'middle_name': middle_name,
                                'mobile_number': mobile_number,
                                'last_name': last_name,
                                'dob': dob,
                                'patient_id': patient_id,
                                'gender': gender,
                                'marital_id': marital_id,
                                'country_id': country_id,
                                'occupation_id': occupation_id,
                                'tribe_id': tribe_id,
                                'residence_id': resident_id,
                                'user_id': user_id
                            };
                            $http.post('/api/patient_edit', edit_patient).then(function (data) {
                                var message = data.data.data;
                                var status = data.data.status;
                                if (status == 1) {
                                    swal(
                                        message,
                                        'Successfully',
                                        'success'
                                    )

                                }
                            });
                        }
                        $scope.edit_all_data = function (patient, others) {
                            console.log(patient);
                            console.log(others);
                            var first_name = patient.first_name;
                            var gender = patient.gender;
                            var patient_id = patient.id;
                            var middle_name = patient.middle_name;
                            var last_name = patient.last_name;
                            var dob = patient.dob;
                            var mobile_number = patient.mobile_number;
                            var country_name = others.country_name.id;
                            var marital_status = others.marital_status.id;
                            var next_kin_residence = others.next_kin_residence.residence_id;
                            var residence_id = others.resedence_id.residence_id;
                            var occupation_name = others.occupation_name.id;
                            var relationship = others.relationship.relationship;
                            var tribe_name = others.tribe_name.id;
                            var next_of_kin_name = others.next_of_kin_name;
                            var mobile_number_next_kin = others.mobile_number_next_kin;
                            var edit_all_data = {
                                'first_name': first_name,
                                'middle_name': middle_name,
                                'mobile_number': mobile_number,
                                'last_name': last_name,
                                'dob': dob,
                                'patient_id': patient_id,
                                'gender': gender,
                                'country_id': country_name,
                                'marital_id': marital_status,
                                'next_residence_id': next_kin_residence,
                                'resident_id': residence_id,
                                'occupation_id': occupation_name,
                                'relationship': relationship,
                                'tribe_id': tribe_name,
                                'next_of_kin_name': next_of_kin_name,
                                'kin_mobile_number': mobile_number_next_kin,
                                'user_id': user_id
                            };
                            console.log(edit_all_data);
                            $http.post('/api/edit_all_data', edit_all_data).then(function (data) {
                                var message = data.data.message;
                                var status = data.data.status;
                                if (status == 1) {
                                    Helper.alert('PATIENT ' + ' ' + message);
                                }
                            });
                        }
                        $scope.FirstForm = function () {
                            $scope.firstForm = true;
                        }
                        $scope.FirstForm();
                        $scope.NextForm = function () {
                            console.log(patient);
                            $scope.firstForm = false;
                            $scope.secondForm = true;
                        }
                        $http.get('/api/getRelationships').then(function (data) {
                            $scope.relationships = data.data;
                        });
                        $http.get('/api/getMaritalStatus').then(function (data) {
                            $scope.maritals = data.data;
                        });

                        $scope.getResidents = function (text) {

                            $http.get('/api/searchResidences/' + text).then(function (data) {
                                resdata = data.data;
                            });
                            return resdata;
                        }
                        $scope.showSearchMarital = function (text) {
                            console.log(text);
                            $http.get('/api/getMaritalStatus/' + text).then(function (data) {
                                maritals = data.data;
                            });
                            return maritals;
                        }
                        $scope.showSearchTribe = function (text) {
                            $http.get('/api/getTribe/' + text).then(function (data) {
                                tribe = data.data;
                            });
                            return tribe;
                        }
                        $scope.showSearchOccupation = function (text) {
                            $http.get('/api/getOccupation/' + text).then(function (data) {
                                occupation = data.data;
                            });
                            return occupation;
                        }
                        $scope.getCountry = function (text) {
                            $http.get('/api/getCountry/' + text).then(function (data) {
                                country = data.data;
                            });
                            return country;
                        }
                        $scope.getRelationships = function (text) {
                            $http.get('/api/getRelationships/' + text).then(function (data) {
                                relationships = data.data;
                            });
                            return relationships;
                        }
                    },
                    templateUrl: '/views/modules/emergency/updatePatient.html',
                    parent: angular.element(document.body),
                    clickOutsideToClose: false,
                    fullscreen: $scope.customFullscreen // Only for -xs, -sm breakpoints.
                })
            }
        };

        $scope.openDialog = function (selectedPatient, ev) {


            if (typeof selectedPatient != 'undefined') {

                var patient_id = selectedPatient.id;
                var residence_id = selectedPatient.residence_id;
                var postData = {
                    "patient_id": patient_id,
                    "residence_id": residence_id,
                    "facility_id": facility_id,
                };

                $scope.quick_registration = selectedPatient;

                $http.post('/api/getPatientRegistrationStatus', postData).then(function (data) {

                    if (data.data[0] == 0) {
                        var patientData = selectedPatient;
                        var accounts_number = data.data[1][0];
                        patientData['qualifiesFreeReattendance'] = accounts_number.qualifiesFreeReattendance;
						patientData['days'] = accounts_number.days;
                        var residences = data.data[2][0];
                        var getLastVisit = data.data[3];
                        var object = {
                            'patientData': patientData,
                            'accounts_number': accounts_number,
                            'residences': residences,
                            'getLastVisit': getLastVisit
                        };


                        $scope.selectedPatient = null;

                        $mdDialog.show({
                            locals: {
                                'patientData': patientData,
                                'accounts_number': accounts_number,
                                'residences': residences,
                                'getLastVisit': getLastVisit
                            },
                            controller: function ($scope) {
                                $scope.patientData = patientData;
                                $scope.accounts_number = accounts_number;
                                $scope.residences = residences;
                                $scope.getLastVisit = getLastVisit;
                                $scope.cancel = function () {
                                    $scope.selectedPatient = null;
                                    $mdDialog.hide();
                                };
								
								$scope.startup = function(){
									if($scope.patientData.qualifiesFreeReattendance == true){
										swal({
											  title: 'RE-ATTENDANCE',
											  html: 'This patient is within the <b>'+$scope.patientData.days+' days</b> allowed for free re-attendance since last visit.<hr />Do you want to directly start the visit?',
											  type: 'info',
											  showCancelButton: true,
											  confirmButtonColor: '#3085d6',
											  cancelButtonColor: '#d33',
											  confirmButtonText: 'Yes',
											  cancelButtonText: 'No'
										}).then(function () {
											$(".md-dialog-content").scope().showGridBottomSheetReattendence(
												$scope.patientData,
												$scope.residences,
												{'free_reattendance':true},
												$scope.patient
											);
										}, function(){ });
									}
								}
								$scope.startup();
                            },
							
                            templateUrl: '/views/modules/registration/encounterModalReattendence.html',
                            parent: angular.element(document.body),
                            clickOutsideToClose: false,
                            fullscreen: $scope.customFullscreen // Only for -xs, -sm breakpoints.
                        });
                    } else {

                        $mdDialog.show({
                            locals: {
                                selectedPatient: selectedPatient
                            },
                            controller: function ($scope) {
                                $scope.selectedPatient = selectedPatient;
                                $scope.cancel = function () {
                                    $scope.selectedPatient = null;
                                    $mdDialog.hide();
                                };
                            },
                            templateUrl: '/views/modules/registration/completeRegistrationModal.html',
                            parent: angular.element(document.body),
                            clickOutsideToClose: false,
                            fullscreen: $scope.customFullscreen // Only for -xs, -sm breakpoints.
                        });

                    }
                });
            }
        };

        function registrationModalController($scope, $mdDialog, object) {
            //console.log(object);
            $scope.patientData = object.patientData;
            $scope.accounts_number = object.accounts_number;
            $scope.residences = object.residences;
            $scope.getLastVisit = object.getLastVisit;
            $scope.quick_registration = object;
            $scope.hide = function () {
                $mdDialog.hide();
                $scope.selectedPatient = null;
            };

            $scope.cancel = function () {
                $mdDialog.cancel();
                $scope.selectedPatient = null;
            };

            $scope.answer = function (answer) {
                $mdDialog.hide(answer);
                $scope.selectedPatient = null;
            };
        }


        $scope.getItemListFromNhif = function (UserName) {
            $scope.dataLoading = true;
            var creditials = {
                "FacilityCode": "011",
                "UserName": "Got-homis"
            };
            $http.post('/api/getNHIFItemPrices', creditials).then(
                function (response) {
                    $scope.nhifLists = response.data;

                },
                function (data) {
                    // Handle error here
                    toastr.error('', 'Card No. Not Found in Database!');
                }).finally(function () {
                $scope.dataLoading = false;
            });
        }


        //age calculation
        $scope.calculateAge = function (source) {

            var dob = $scope.patient.dob;

            if ($scope.patient.dob instanceof Date) {
                dob = $scope.patient.dob.toISOString();
            }
            if ($scope.patient.dob == undefined && $scope.patient.age == undefined) {
                return;
            }


            if (dob != '' && source == 'date' && ((new Date()).getFullYear() < parseInt(dob.substring(0, 4)) ||
                ((new Date()).getFullYear() == parseInt(dob.substring(0, 4)) && ((new Date()).getMonth() + 1) < parseInt(dob.substring(dob.indexOf("-") + 1, 7))) ||
                ((new Date()).getFullYear() == parseInt(dob.substring(0, 4)) && ((new Date()).getMonth() + 1) == parseInt(dob.substring(dob.indexOf("-") + 1, 7)) && ((new Date()).getDate()) < parseInt(dob.substring(dob.lastIndexOf("-") + 1, 10))))) {
                $scope.patient.dob = undefined;
                $scope.patient.age_unit = "";
                $scope.patient.age = "";
                swal('Future dates not allowed!', '', 'warning');
                return;
            }

            if (source == 'age') {
                $scope.patient.dob = new Date((new Date().getFullYear() - $scope.patient.age) + '-07-01');
                $scope.patient.age_unit = 'Years';

            } else if (source == 'date') {
                $scope.patient.dob = dob.replace(/\//g, '-');
                var days = Math.floor(((new Date()) - new Date(dob.substring(0, 4) + '-' + dob.substring(dob.indexOf("-") + 1, 7) + '-' + dob.substring(dob.lastIndexOf("-") + 1, 10))) / (1000 * 60 * 60 * 24));
                if (days > 365) {
                    $scope.patient.age = Math.floor(days / 365);
                    $scope.patient.age_unit = 'Years';
                } else if (days > 30) {
                    $scope.patient.age = Math.floor(days / 30);
                    $scope.patient.age_unit = 'Months';
                } else {
                    $scope.patient.age = days;
                    $scope.patient.age_unit = 'Days';
                }
            } else {
                if ($scope.patient.age_unit == 'Years')
                    $scope.calculateAge('age');
                else if ($scope.patient.age_unit == 'Months') {
                    if (((new Date()).getMonth() + 1) >= ($scope.patient.age % 12))
                        $scope.patient.dob = ((new Date()).getFullYear() - ~~($scope.patient.age / 12)) + '-' + ((((new Date()).getMonth() + 1) - ($scope.patient.age % 12)).toString().length == 2 ? '' : '0') + (((new Date()).getMonth() + 1) - ($scope.patient.age % 12)) + '-01';
                    else
                        $scope.patient.dob = ((new Date()).getFullYear() - 1 - ~~($scope.patient.age / 12)) + '-' + (((12 + ((new Date()).getMonth() + 1)) - ($scope.patient.age % 12)).toString().length == 2 ? '' : '0') + ((12 + ((new Date()).getMonth() + 1)) - ($scope.patient.age % 12)) + '-01';
                } else {
                    if (((new Date()).getDate()) >= ($scope.patient.age % 30))
                        $scope.patient.dob = ((new Date()).getFullYear() - ~~($scope.patient.age / 365)) + '-' + ((((new Date()).getMonth() + 1) - ~~($scope.patient.age / 30)).toString().length == 2 ? '' : '0') + (((new Date()).getMonth() + 1) - ~~($scope.patient.age / 30)) + '-' + ($scope.patient.age.toString().length == 2 ? '' : '0') + $scope.patient.age.toString();
                    else
                        $scope.patient.dob = ((new Date()).getFullYear() - ~~($scope.patient.age / 365)) + '-' + ((((new Date()).getMonth()) - ~~($scope.patient.age / 30)).toString().length == 2 ? '' : '0') + (((new Date()).getMonth()) - ~~($scope.patient.age / 30)) + '-' + (((30 + ((new Date()).getDate())) - ($scope.patient.age % 30)).toString().length == 2 ? '' : '0') + ((30 + ((new Date()).getDate())) - ($scope.patient.age % 30));
                }
            }
        };

        $scope.getDepartment = function () {
            $http.get('/api/getClinic').then(function (data) {
                $scope.departments = data.data;
            });
        };

        $scope.getSms = function () {
            $http.get('http://kiuta.co.tz/oneapi-php-master/examples.php').then(
                function (response) {
                    //////console.log(response.status);
                    toastr.success('', 'SMS SENT TO NASSORO');
                },
                function (data) {
                    // Handle error here
                    toastr.error('', 'SMS MAY DELAY ');
                })
        };

		
		
        $scope.switchReattendedToNHIF = function (selectedPatient) {


            $mdDialog.show({
                controller: function ($scope) {
                    //////console.log(response.data);
                    $scope.patientData = {
                        "first_name": selectedPatient.first_name,
                        "middle_name": selectedPatient.middle_name,
                        "last_name": selectedPatient.last_name,
                        "gender": selectedPatient.gender,
                        "dob": selectedPatient.dob
                    };


                    $scope.cancel = function () {
                        $scope.selectedPatient = null;
                        $mdDialog.hide();
                    };
                },
                templateUrl: '/views/modules/registration/reattended_for_nhif.html',
                parent: angular.element(document.body),
                clickOutsideToClose: false,
                fullscreen: $scope.customFullscreen // Only for -xs, -sm breakpoints.
            });

        }


        $scope.verification = function (item) {
            $scope.dataLoading = true;
            var creditials = {
                "cardNo": item.nhif_card,
                "ReferralNo": item.refferal_no,
                "VisitTypeID": item.visitType             
            };
            $http.post('/api/authorizeCardFromMember', creditials).then(
                function (response) {
                    //////console.log(response.data);
                    if (response.data.StatusCode == 0) {
                        return toastr.error('', 'Check Internet connection');
                    }
			else if (response.data.StatusCode == 500) {
                     return sweetAlert('Please Enter Correct Card Number', "", "error");

                 } 					
					else if (response.data.StatusCode == 400) {
                        return sweetAlert('NHIF Server not reachable,Please contact NHIF for support', "", "info");
                    } 
					
					
					else if (response.data.StatusCode == 200 && response.data.MembershipNo == null) {
                        var remarks = response.data.StatusDescription;
                        var AuthorizationStatus = response.data.AuthorizationStatus;
						var message=remarks+":"+AuthorizationStatus;
                        return sweetAlert(message, "", "error");
                    }
					else if (response.data.AuthorizationStatus != 'ACCEPTED') {
                        var remarks = response.data.StatusDescription;
                        var FullName = response.data.FullName;
                        var message = FullName + ", " + remarks;
                        return sweetAlert(message, "", "error");
                    } else if (response.data.AuthorizationStatus == 'ACCEPTED') {
                        //////console.log($scope.nhif_patient);

                        $scope.dataLoading = false;

                        $mdDialog.show({
                            controller: function ($scope) {
								$scope.cancel = function () {
									$scope.selectedPatient = null;
									$mdDialog.hide();
								};
								
                                $scope.patientData = {
                                    "first_name": response.data.FirstName,
                                    "middle_name": response.data.MiddleName,
                                    "last_name": response.data.LastName,
                                    "gender": response.data.Gender,
                                    "dob": response.data.DateOfBirth,
                                    "AuthorizationNo": response.data.AuthorizationNo,
                                    "membership_number": response.data.MembershipNo,
                                    "card_no": response.data.CardNo,
                                    "SchemeID": response.data.SchemeID,
                                    "visit_type": item.visitType
                                };

								$scope.savePatientInsuarance = function (quick_registration, patient, residence) {
									var first_name = quick_registration.first_name;
									var middle_name = quick_registration.middle_name;
									var last_name = quick_registration.last_name;
									var gender = quick_registration.gender;
									var dob = quick_registration.dob;
									var authorization_number = quick_registration.AuthorizationNo;
									var membership_number = quick_registration.membership_number;
									var card_no = quick_registration.card_no;
									var scheme_id = quick_registration.SchemeID;
									var visit_type = quick_registration.visit_type;

									if (angular.isDefined(first_name) == false) {
										return sweetAlert("Please Enter FIRST NAME before SAVING", "", "error");
									} else if (angular.isDefined(middle_name) == false) {
										return sweetAlert("Please Enter MIDDLE NAME before SAVING", "", "error");
									} else if (angular.isDefined(last_name) == false) {
										return sweetAlert("Please Enter LAST NAME before SAVING", "", "error");
									} else if (quick_registration.dob == null) {
										var dob = patient.dob;
									}
									else if (angular.isDefined(residence) == false) {
										return sweetAlert("Please Enter Residence Name", "", "error");
									}

									
									var nida=patient.nida;
									var occupation_name = patient.occupation.occupation_name;

									var residence_name = residence.residence_name;
									var council_name = residence.council_name;


									var mobile_number = patient.mobile_number;
									var patient_residences = residence.residence_id;
									var patientservices = patient.payment_services.service_id;
									var occupation = patient.occupation.id;
									var price_id = patient.payment_services.price_id;
									var item_type_id = patient.payment_services.item_type_id;
									var patient_main_category_id = patient.payment_services.patient_main_category_id;
									var patient_category = patient.payment_services.patient_category_id;
									var payment_filter = patient.payment_services.patient_category_id;
									var insuaranceRegistration = {
										"card_no": card_no,
										"authorization_number": authorization_number,
										"membership_number": membership_number,
										"payment_filter": payment_filter,
										"occupation": occupation,
										"item_type_id": item_type_id,
										"price_id": price_id,
										"patient_main_category_id": patient_main_category_id,
										"patient_category": patient_category,
										"patientservices": patientservices,
										"first_name": first_name,
										"middle_name": middle_name,
										"last_name": last_name,
										  nida:nida,
										  scheme_id:scheme_id,
										  visit_type:visit_type,
										"dob": dob,
										"gender": gender,
										"mobile_number": mobile_number,
										"residence_id": patient_residences,
										"facility_id": facility_id,
										"user_id": user_id
									}
									$http.post('/api/insuaranceRegistration', insuaranceRegistration).then(function (response) {
										if (response.data.status == 400) {
											sweetAlert(response.data.errors, "", "error");
										} else {  
										  var returnedData=response.data[0][0];               
										  returnedData['residence_name']=response.data[2].residence_name;               
											$scope.cancel();
											$mdDialog.show({
												controller: function ($scope) {   

													$scope.patientData = returnedData;
													$http.get('/api/getUsermenu/' + user_id).then(function (cardTitle) {
														$scope.cardTitle = cardTitle.data[0];
													});
													
													$http.get('/api/getLoginUserDetails/' + user_id).then(function (cardTitle) {
														$scope.facility_address = cardTitle.data[0];
													});
													$scope.cancel = function () {
														$scope.selectedPatient = null;
														$mdDialog.hide();
													};
												},
												templateUrl: '/scripts/modules/registrations/views/printCard.html',
												parent: angular.element(document.body),
												clickOutsideToClose: false,
												fullscreen: $scope.customFullscreen // Only for -xs, -sm breakpoints.
											});


										}
									});
								};
                            },
                            templateUrl: '/views/modules/registration/insuarance.html',
                            parent: angular.element(document.body),
                            clickOutsideToClose: false,
                            fullscreen: $scope.customFullscreen // Only for -xs, -sm breakpoints.
                        });


                    } else if (response.data.StatusDescription == 'Revoked') {
                        $scope.dataLoading = false;
                        var FullName = response.data.FullName;
                        var remarks = response.data.Remarks;
                        var message = FullName + " , " + remarks;

                        return sweetAlert(message, "", "error");
                    }
					else{
                       return sweetAlert('NHIF Server not reachable,Please contact NHIF for support', "", "info");
                    }


                },
                function (data) {
                    // Handle error here
                    toastr.error('', 'Connection Timed out,check with NHIF support team');
                }).finally(function () {
                $scope.dataLoading = false;
            });
        };


        var resdata = [];
        var nextresdata = [];
        var patientCategory = [];
        var patientService = [];
        var patientsList = [];
        var maritals = [];
        var tribe = [];
        var occupation = [];
        var country = [];
        var relationships = [];
        var insuranceService = [];


        $scope.showSearchMarital = function (searchKey) {

            $http.get('/api/getMaritalStatus').then(function (data) {
                $scope.maritals = data.data;
            });

        }

        $scope.isSet = function (tabNum) {
            return $scope.tab === tabNum;
        }
        $scope.oneAtATime = true;


        $scope.showFirstForm = function (patient, others, residence) {
            $scope.others = others;
            $scope.residence = residence;
            $scope.firstFormShow = true;
            $scope.secondFormShow = false;
        }
        $scope.showFirstForm();
        $scope.showSearchTribe = function (searchKey) {
            $http.post('/api/getTribes', searchKey).then(function (data) {
                tribe = data.data;
            });
            return tribe;
        }
        $scope.seachTribes = function (searchKey) {
            $http.post('/api/getTribes', {
                "search": searchKey
            }).then(function (data) {
                tribe = data.data;
            });
            return tribe;
        }
        $scope.showSearchOccupation = function (searchKey) {
            $http.get('/api/getOccupation/' + searchKey).then(function (data) {
                occupation = data.data;
            });
            return occupation;
        }

        $scope.getCountry = function (searchKey) {
            $http.get('/api/getCountry/' + searchKey).then(function (data) {
                country = data.data;

            });
            return country;
        }

        $scope.getRelationships = function () {

            $http.get('/api/getRelationships').then(function (data) {
                $scope.relationships = data.data;
            });

        };
        $http.get('/api/getInsurances').then(function (data) {
            $scope.insurances = data.data;


        });

        $scope.getPatients = function (searchKey) {
            //////console.log(searchKey);
            var dataToPost = {searchKey: searchKey};
            $http.post('/api/getSeachedPatients', dataToPost).then(function (data) {
                patientsList = data.data;

            });

            return patientsList;
        };

		$scope.getCorpses = function (searchKey) {
            //////console.log(searchKey);
            var dataToPost = {searchKey: searchKey};
            $http.post('/api/getSeachedCorpses', dataToPost).then(function (data) {
                patientsList = data.data;

            });

            return patientsList;
        }

        $scope.getInsuarancePatients = function (searchKey) {
            $http.get('/api/getSeachedInsuarancePatients/' + searchKey).then(function (data) {
                patientsList = data.data;

            });
            return patientsList;
        }

        $scope.showSearchNextResidences = function (searchKey) {
            $http.post('/api/searchResidences', {searchKey: searchKey}).then(function (data) {
                resdata = data.data;
            });

            return resdata;
        }


        $scope.showSearchResidences = function (searchText) {

            var stored_data = window.localStorage.getItem("stored_residences");
            if (stored_data) {

                return JSONStream.parse(stored_data);
            }
            return $http
                .post('/api/searchResidences', {searchKey: searchText})
                .then(function (data) {
                    // Map the response object to the data object.
                    localStorage.setItem('stored_residences', JSON.stringify(data.data));
                    return data.data;
                });
        };

        $scope.patientService = function () {
			
            var searchKey = {
                'patient_category': $scope.encounter.payment_category.patient_category,
                'item_name': $scope.encounter,
				'facility_id':$rootScope.currentUser.facility_id
            };
            //////console.log($scope.encounter);
            $http.post('/api/searchPatientServices', searchKey).then(function (data) {
                patientService = data.data;
            });
            //////console.log(resdata);
            return patientService;
        };
        $scope.patientInsuaranceService = function (searchKey) {
            var searchKeyReceived = {
                'patient_category': 'NHIF',
                'item_name': searchKey,
				facility_id:$rootScope.currentUser.facility_id
            };
            //////console.log(searchKeyReceived);
            $http.post('/api/searchPatientServices', searchKeyReceived).then(function (data) {
                insuranceService = data.data;
            });
            return insuranceService;
        };
        $scope.searchPatientCategory = function () {
			var facility_id = $rootScope.currentUser.facility_id;
            $http.get('/api/searchPatientCategory/'+facility_id).then(function (data) {
                $scope.patientCategory = data.data;
            });

        };
        $scope.searchPatientCategory();

        $scope.viewItem = function (quick_registration) {
            $scope.quick_registration = quick_registration;
            var modalInstance = $uibModal.open({
                templateUrl: '/views/modules/registration/encounterModal.html',
                size: 'lg',
                animation: true,
                controller: 'registrationModal',
                resolve: {
                    quick_registration: function () {
                        return $scope.quick_registration;
                    }
                }
            });
            modalInstance.result.then(function (quick_registration) {
                $scope.quick_reg = quick_registration;
                //////console.log($scope.quick_reg);
            });
        };

        $scope.postCorpseMortuary = function (quick_registration) {
            $scope.quick_registration = quick_registration;
            //console.log($scope.quick_registration);
            $http.get('/api/getMortuary').then(function (data) {
                $scope.mortuaries = data.data;
                var object = angular.extend($scope.quick_registration, $scope.mortuaries);
                $mdDialog.show({
                    controller: 'registrationModalCorpse',
                    templateUrl: '/views/modules/registration/postCorpseToMortuary.html',
                    parent: angular.element(document.body),
                    clickOutsideToClose: true,
                    resolve: {
                        object: function () {
                            return object;
                        }
                    }
                });
            });
        };

        $scope.getMortuaryService = function () {
            var postData = {facility_id: facility_id};
            $http.post('/api/getMortuaryServices', postData).then(function (data) {
                $scope.mortuaryServices = data.data;
            });
        };

        $scope.cancelSheet = function () {
            $mdBottomSheet.hide();
        };

        $scope.cancel = function () {
            $scope.selectedPatient = null;
            $mdDialog.hide();
        };

		  $scope.getCorpseDetails = function (selectedCorpse) {
			  $scope.selectedCorpse=selectedCorpse;			  
		  };  
		  
		  $scope.getCorpseEdit = function (selectedCorpse) {
			    $scope.corpse_details=selectedCorpse;	
                					  
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
      
		 $scope.editCorpses = function (corpses,corpse_id) {
			
        	   var first_name=corpses.first_name;			  
               var middle_name=corpses.middle_name;			  
               var last_name=corpses.last_name;			  
               var gender=corpses.gender;			  
               var dob=corpses.dob;			  
               var dod=corpses.dod;			  
               
	       var postData={corpse_id:corpse_id,first_name:first_name,middle_name:middle_name,last_name:last_name,gender:gender,dob:dob,dod:dod,user_id:user_id

			  };
               
			 $http.post('/api/corpseEdit', postData).then(function (data) {
					if (data.data.status == 0) {
                    sweetAlert(data.data.data, "", "error");
                } else {
					 sweetAlert(data.data.data, "", "success");
				}
					
				});


			  
		  };
      
		
		
		
        $scope.openDialogForServices = function (selectedPatient, ev) {
            var patient_id = selectedPatient.patient_id;
            var residence_id = selectedPatient.residence_id;
            var postData = {
                "patient_id": patient_id,
                "residence_id": residence_id,
                "facility_id": facility_id
            };

            $scope.quick_registration = selectedPatient;

            $http.post('/api/getPatientRegistrationStatus', postData).then(function (data) {

                if (data.data[0] == 0) {
                    var patientData = selectedPatient;
                    var accounts_number = data.data[1][0];
                    var residences = data.data[2][0];
                    var getLastVisit = data.data[3];
                    var object = {
                        'patientData': patientData,
                        'accounts_number': accounts_number,
                        'residences': residences,
                        'getLastVisit': getLastVisit
                    };


                    $scope.selectedPatient = null;

                    $mdDialog.show({
                        locals: {
                            'patientData': patientData,
                            'accounts_number': accounts_number,
                            'residences': residences,
                            'getLastVisit': getLastVisit
                        },
                        controller: function ($scope) {
                            $scope.patientData = patientData;
                            $scope.accounts_number = accounts_number;
                            $scope.residences = residences;
                            $scope.getLastVisit = getLastVisit;
                            $scope.cancel = function () {
                                $scope.selectedPatient = null;
                                $mdDialog.hide();
                            };
                        },
                        templateUrl: '/views/modules/registration/encounterModal.html',
                        parent: angular.element(document.body),
                        clickOutsideToClose: false,
                        fullscreen: $scope.customFullscreen // Only for -xs, -sm breakpoints.
                    });
                } else {

                    $mdDialog.show({
                        locals: {
                            selectedPatient: selectedPatient
                        },
                        controller: function ($scope) {
                            $scope.selectedPatient = selectedPatient;
                            $scope.cancel = function () {
                                $scope.selectedPatient = null;
                                $mdDialog.hide();
                            };
                        },
                        templateUrl: '/views/modules/registration/completeRegistrationModal.html',
                        parent: angular.element(document.body),
                        clickOutsideToClose: false,
                        fullscreen: $scope.customFullscreen // Only for -xs, -sm breakpoints.
                    });

                }
            });
        }

        function quickRegistrationModal($scope, $mdDialog, object) {
            $scope.patientData = object.patientData;
            $scope.accounts_number = object.accounts_number;
            $scope.residences = object.residences;
            $scope.getLastVisit = object.getLastVisit;

            $scope.hide = function () {
                $mdDialog.hide();
                $scope.selectedPatient = null;
            };

            $scope.cancel = function () {
                $mdDialog.cancel();
                $scope.selectedPatient = null;
            };

            $scope.answer = function (answer) {
                $mdDialog.hide(answer);
                $scope.selectedPatient = null;
            };
        }

        function PrintCardDialogController($scope, $mdDialog, object) {
            $scope.patientData = object.patientsInfo;
            $scope.patientResidences = object.residences;
            $scope.hide = function () {
                $mdDialog.hide();
                $scope.selectedPatient = null;
            };

            $scope.cancel = function () {
                $mdDialog.cancel();
                $scope.selectedPatient = null;
            };

            $scope.answer = function (answer) {
                $mdDialog.hide(answer);
                $scope.selectedPatient = null;
            };
        }

        function registrationModalCorpse($scope, $mdDialog, object) {
            $scope.quick_registration = object;
            $scope.corpse = object;
            $scope.patientData = $scope.quick_registration;
            ////console.log($rootScope.currentUser);
            var last_visit = {
                'patient_id': $scope.quick_registration.id,
                'facility_id': $scope.quick_registration.facility_id
            };
            $scope.hide = function () {
                $mdDialog.hide();
                $scope.selectedPatient = null;
            };

            $scope.cancel = function () {
                $mdDialog.cancel();
                $scope.selectedPatient = null;
            };

            $scope.answer = function (answer) {
                $mdDialog.hide(answer);
                $scope.selectedPatient = null;
            };
        }

        function nhifRegistrationModal($scope, $mdDialog, object) {
            $scope.patientData = object;
            $scope.hide = function () {
                $mdDialog.hide();
                $scope.selectedPatient = null;
            };

            $scope.cancel = function () {
                $mdDialog.cancel();
                $scope.selectedPatient = null;
            };

            $scope.answer = function (answer) {
                $mdDialog.hide(answer);
                $scope.selectedPatient = null;
            };
        }

		
		$scope.downloadPdf=function(){
			 $http.get('/api/generate-pdf').then(function (data) {				 
				 $scope.res=data.data;
			 });
			
		};
		
		
        $scope.completeRegistration = function (patient, others, residence) {

            
            if (angular.isDefined(others) == false) {
                return sweetAlert("Please Enter concerned information", "", "error");
            }
            var marital_status = null;
            var occupation = null;
            var first_name = patient.first_name;
            var middle_name = patient.middle_name;
            var last_name = patient.last_name;
            var gender = patient.gender;
            var dob = patient.dob;
            var mobile_number = patient.mobile_number;
            var patient_id = patient.id;
            var residence_id = patient.residence_id;

            if (angular.isDefined(others.country) == false) {
                return sweetAlert("Please Enter Country and choose from the suggestions", "", "error");
            }
            else if (angular.isDefined(residence) == false) {
                return sweetAlert("Please Enter Next of kin Residences and choose from the suggestions", "", "error");
            }
            else if (angular.isDefined(others.next_of_kin_name) == false) {
                return sweetAlert("Please Enter Next of kin Name", "", "error");
            }
            else if (angular.isDefined(others.relationship) == false) {
                return sweetAlert("Please Enter Relationships and choose from the suggestions", "", "error");
            }
            if (angular.isDefined(others.marital) == true) {
                var marital_status = others.marital;
            }
            if (angular.isDefined(others.occupation) == true) {
                var occupation = others.occupation.id;
            }
            var country = others.country.id;
            var next_of_kin_name = others.next_of_kin_name;
            var next_of_kin_resedence_id = residence.residence_id;
            var relationship = others.relationship;
            var mobile_number_next_kin = others.mobile_number_next_kin;
            var complete_registration = {
                "residence_id": residence_id,
                "patient_id": patient_id,
                "first_name": first_name,
                "middle_name": middle_name,
                "last_name": last_name,
                "dob": dob,
                "gender": gender,
                "mobile_number": mobile_number,
                "facility_id": facility_id,
                "user_id": user_id,
                "marital_status": marital_status,
                "occupation_id": occupation,
                "country_id": country,
                "next_of_kin_name": next_of_kin_name,
                "next_of_kin_resedence_id": next_of_kin_resedence_id,
                "relationship": relationship,
                "mobile_number_next_kin": mobile_number_next_kin
            }


            $http.post('/api/complete_registration', complete_registration).then(function (data) {
                if (data.data.status == 0) {
                    sweetAlert(data.data.data, "", "error");
                } else {
                    console.log(data.data[0][0]);
                    var patientData = data.data[0][0];
                    var accounts_number = data.data[1][0];
                    patientData['qualifiesFreeReattendance'] = accounts_number.qualifiesFreeReattendance;
					patientData['days'] = accounts_number.days;
					var residences = data.data[2][0];
                    var getLastVisit = data.data[3];
                    var object = {
                        'patientData': patientData,
                        'accounts_number': accounts_number,
                        'residences': residences,
                        'getLastVisit': getLastVisit
                    };
                    $scope.cancel();
                    $scope.patient = null;
                    $scope.others = null;

                    $mdDialog.show({
                        controller: function ($scope) {
                            $scope.patientData = patientData;
                            $scope.accounts_number = accounts_number;
                            $scope.residences = residences;
                            $scope.getLastVisit = getLastVisit;
                            $scope.cancel = function () {
                                $scope.selectedPatient = null;
                                $mdDialog.hide();
                            };
							
							$scope.startup = function(){
									if($scope.patientData.qualifiesFreeReattendance == true){
										swal({
											  title: 'RE-ATTENDANCE',
											  html: 'This patient is within the <b>'+$scope.patientData.days+'</b> allowed for free re-attendance.<hr />Do you want to directly start the visit?',
											  type: 'info',
											  showCancelButton: true,
											  confirmButtonColor: '#3085d6',
											  cancelButtonColor: '#d33',
											  confirmButtonText: 'Yes',
											  cancelButtonText: 'No'
										}).then(function () {
											$(".md-dialog-content").scope().showGridBottomSheetReattendence(
												$scope.patientData,
												$scope.residences,
												{'free_reattendance':true},
												$scope.patient
											);
										}, function(){ });
									}
								}
								$scope.startup();
                        },
                        templateUrl: '/views/modules/registration/encounterModalReattendence.html',
                        parent: angular.element(document.body),
                        clickOutsideToClose: false,
                        fullscreen: $scope.customFullscreen // Only for -xs, -sm breakpoints.
                    });
                }
            });


        };

        $scope.PrintContent = function () {
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

        $scope.cancel = function () {
            $mdDialog.hide();
        };

   $scope.enterEncounter = function (patientData, residences, encounter, patient,dept_id) {
			var enterEncounter = {};
            var patient_id = patient;
			var facility_id = $rootScope.currentUser.facility_id;
			var user_id = $rootScope.currentUser.id;
			
			if(!encounter.free_reattendance) {
				if (angular.isDefined(encounter) == false) {
					return sweetAlert("Please Type the Payment Category", "", "error");
				}
				else if (angular.isDefined(encounter.payment_category) == false) {
					return sweetAlert("Please Type the Payment Category", "", "error");
				}
				else if (angular.isDefined(encounter.payment_services) == false) {
					return sweetAlert("Please Select Service", "", "error");
				}


				var patient_category = encounter.payment_category.patient_category;
				var service_category = encounter.payment_services;
				var service_id = encounter.payment_services.service_id;
				var price_id = encounter.payment_services.price_id;
				var item_type_id = encounter.payment_services.item_type_id;
				var payment_filter = encounter.payment_services.patient_category_id;

				var bill_category_id = encounter.payment_services.patient_category_id;
				var main_category_id = encounter.payment_services.patient_main_category_id;

				enterEncounter = {
					'payment_filter': payment_filter,
					'item_type_id': item_type_id,
					'patient_category': patient_category,
					'main_category_id': main_category_id,
					'bill_id': bill_category_id,
					'service_category': service_category,
					'service_id': service_id,
					'price_id': price_id,
					'patient_id': patient_id,
					'dept_id': dept_id,
					'facility_id': facility_id,
					'user_id': user_id,
					'is_referral':encounter.is_referral,
					'referring_facility_id':$scope.encounter.referring_facility_id
				};
			}else
				enterEncounter = {
					'free_reattendance': true,
					'patient_id': patient_id,
					'dept_id': dept_id,
					'facility_id': facility_id,
					'user_id': user_id,
					'is_referral':encounter.is_referral,
					'referring_facility_id':$scope.encounter.referring_facility_id
				};
			
			$http.post('/api/enterEncounter', enterEncounter).then(function (data) {
				$scope.registrationReport = data.data;
				if (data.data.status == 0) {

					return sweetAlert(data.data.data, "", "error");
				} else {
					$scope.cancelSheet();
					var ev = null;


					$mdDialog.show({

						controller: function ($scope) {
							console.log(patientData);
							$scope.patientData = patientData;
							$scope.patientResidences = residences;
							$scope.cardTitle = $scope.cardTitle;

							$http.get('/api/getUsermenu/' + user_id).then(function (cardTitle) {
								$scope.cardTitle = cardTitle.data[0];

							});
                            var patient_id = patientData.id;
                            var residence_id = patientData.residence_id;
                            var postData = {
                                "patient_id": patient_id,
                                "residence_id": residence_id,
                                "facility_id": facility_id
                            };
                            $http.post('/api/getPatientRegistrationStatus', postData).then(function (data) {
$scope.residence_name=data.data[2][0].residence_name;
$scope.occupation_name=data.data[4][0].occupation_name;

                            });

                                    //-----

							$scope.cancel = function () {
								$mdDialog.hide();
							};
						},
						templateUrl: '/views/modules/registration/printCard.html',
						parent: angular.element(document.body),

						clickOutsideToClose: false,
						fullscreen: $scope.customFullscreen // Only for -xs, -sm breakpoints.
					});

				}


			});
        };
		
        $scope.selectedResidence = function (residence) {
            if (typeof residence != 'undefined') {
                residence_id = residence.residence_id;
                //console.log(residence)
            }
            $scope.residence = residence;
        }
		
        $scope.selectedFacility = function (facility) {
            if (typeof facility != 'undefined') {
                if($scope.encounter){
					$scope.encounter.referring_facility_id = facility.id;
					$scope.encounter.is_referral = 1;
				}else
					swal('Please choose cost sharing type first');
            }
        }

        $scope.selectedResidencekin = function (residence) {
            if (typeof residence != 'undefined') {
                resedence_id_kin = residence.residence_id;
                //console.log(resedence_id_kin)
            }
            $scope.residence = residence;
        };

        $scope.patient_quick_registration = function (patient) {
            var first_name = patient.first_name;
            var middle_name = patient.middle_name;
            var last_name = patient.last_name;
            var gender = patient.gender;
            var mobile_number = patient.mobile_number;
            if (angular.isDefined(first_name) == false) {
                return sweetAlert("Please Enter FIRST NAME before SAVING", "", "error");
            }
            else if (angular.isDefined(middle_name) == false) {
                return sweetAlert("Please Enter MIDDLE NAME before SAVING", "", "error");
            }
            else if (angular.isDefined(last_name) == false) {
                return sweetAlert("Please Enter LAST NAME before SAVING", "", "error");
            }
            else if (!gender) {
                return sweetAlert("Please select the patient's gender.", "", "error");
            }
            else if (!patient.dob) {
                return sweetAlert("Please fill date of birth/details of the patient.", "", "error");
            }
            else if (angular.isDefined(residence_id) == false) {
                return sweetAlert("Please type the Residence Name and choose from the suggestions", "", "error");
            }

            var dob = moment(patient.dob).format("YYYY-MM-DD");

            if (!patient.tribe) {
                return sweetAlert("Patient's tribe is required, please fill in.", "", "error");
            }
            var tribe = patient.tribe.id;
            var quick_registration = {
                "tribe": tribe,
                "first_name": first_name,
                "middle_name": middle_name,
                "last_name": last_name,
                "dob": dob,
                "gender": gender,
                "mobile_number": mobile_number,
                "residence_id": residence_id,
                "facility_id": facility_id,
                "user_id": user_id
            };


            if($scope.confirmedNotDuplicate == undefined)
				quick_registration["confirmedNotDuplicate"]=0;
			

            $http.post('/api/quick_registration', quick_registration).then(function (data) {
				if(data.data.status == 'duplicate'){
					swal({
						  title: 'Possible Duplication.',
						  html: "<b style='color:red'>The patient may have been registered before with MRN "+data.data.mrn+".</b><br /><hr />If you are sure this is the first time visit, press PROCEED.",
						  type: 'warning',
						  showCancelButton: true,
						  confirmButtonColor: '#3085d6',
						  cancelButtonColor: '#d33',
						  confirmButtonText: 'Proceed'
						}).then(function () {					
							 $scope.confirmedNotDuplicate = 1;
							 $scope.patient_quick_registration(patient);
						}, function(){ $scope.confirmedNotDuplicate = undefined; return;});
				}else
					$scope.confirmedNotDuplicate = 0;
				
				//tricky block aided by the else above
				if($scope.confirmedNotDuplicate == undefined)
					return;
				
				$scope.confirmedNotDuplicate = undefined;
				// end tricky block
				
                $scope.quick_registration = data.data;
                ////console.log($scope.quick_registration);
                if (data.data.status == 0) {

                    sweetAlert(data.data.data, "", "error");
                } else {
                    var ev = null;
                    var patientData = data.data[0];
                    var accounts_number = data.data[1][0];
                    var residences = data.data[2][0];

                    //console.log(data.data[3]);

                    var getLastVisit = data.data[3];
                    $scope.patient.first_name = null;
                    $scope.patient.middle_name = null;
                    $scope.patient.last_name = null;
                    $scope.patient.age = null;
                    $scope.patient.tribe = null;
                    $scope.patient.gender = null;
                    $scope.patient.mobile_number = null;
                    $scope.patient.tribe = null;
                    $scope.residence = null;
                    $mdDialog.show({
                        controller: function ($scope) {
                            $scope.patientData = patientData;
                            
                            console.log($scope.patientData);
                            $scope.accounts_number = accounts_number;
                            $scope.residences = residences;
                            $scope.getLastVisit = getLastVisit;
                            $scope.cancel = function () {
                                $mdDialog.hide();
                            };
                        },
                        templateUrl: '/views/modules/registration/encounterModal.html',
                        parent: angular.element(document.body),
                        clickOutsideToClose: false,
                        fullscreen: $scope.customFullscreen // Only for -xs, -sm breakpoints.
                    });


                }
            });


        };


//function receive from nursing care..
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

        $scope.getItemsMortuary = function (corpse_service) {
            $scope.selectedService = corpse_service;
            console.log($scope.selectedService);
        };


        $scope.startSessionCorpse = function (corpse, corpse_service) {

            console.log(corpse_service);
            var dataToPost = {
                corpse_id: corpse.id,
                item_type_id: corpse_service.item_type_id,
                item_name: corpse_service.item_name,
                quantity: 1,
                item_type_id: corpse_service.item_type_id,
                status_id: 1,
                facility_id: facility_id,
                item_price_id: corpse_service.price_id,
                user_id: user_id,
                discount: 0,
                discount_by: user_id,
                payment_filter: 1
            };

            $http.post('/api/giveService', dataToPost).then(function (data) {
                if (data.data.status == 0) {
                    sweetAlert(data.data.data, "", "error");
                } else {
                    sweetAlert(data.data.data, "", "success");
                }
            });


        };
		
		$scope.whereCorpseFound=function(item){
			$scope.residenceFound=item;
			console.log($scope.residenceFound);
			
		};
 
	 $scope.verifyDetails=function(corpse_details,corpse_country){

			if (angular.isDefined(corpse_details) == false) {
                return sweetAlert("Enter Corpse Details", "", "error");
            }
		      
		    if (angular.isDefined(corpse_details.first_name) == false) {
                return sweetAlert("Please Enter FIRST NAME or UKNOWN before SAVING", "", "error");
            }
            else if (angular.isDefined(corpse_details.middle_name) == false) {
                return sweetAlert("Please Enter MIDDLE NAME or UKNOWN before SAVING", "", "error");
            }

            else if (angular.isDefined(corpse_details.last_name) == false) {
                return sweetAlert("Please Enter LAST NAME or UKNOWN", "", "error");
            }
   else if (angular.isDefined($scope.residenceFound.residence_id) == false) {
                return sweetAlert("Please type the Residence Name and choose from the suggestions", "", "error");
            }
			
	   $scope.whereFoundId=$scope.residenceFound.residence_id;
	   $scope.country_id=corpse_country.country.id;
	   $scope.dob=$scope.patient.dob;
	   $scope.dod=corpse_details.dod;
	   $scope.firstDetails=corpse_details;
           // if ((($scope.dod).getFullYear() < parseInt($scope.dob.substring(0, 4)) ||
           //     (($scope.dod).getFullYear() == parseInt($scope.dob.substring(0, 4)) && (($scope.dod).getMonth() + 1) < parseInt($scope.dob.substring($scope.dob.indexOf("-") + 1, 7))) ||
           //     (($scope.dod).getFullYear() == parseInt($scope.dob.substring(0, 4)) && (($scope.dod).getMonth() + 1) == parseInt($scope.dob.substring($scope.dob.indexOf("-") + 1, 7)) && (($scope.dod).getDate()) < parseInt($scope.dob.substring($scope.dob.lastIndexOf("-") + 1, 10))))) {
           //
           //     swal('Future Or Back dates not allowed, Check DOB and DOD dates!', '', 'warning');
           //     return;
           // }

    	return sweetAlert("Kamilisha taarifa za aliyeleta Maiti", "", "info");
		 
		   
		   
		   	   
	   };
		
		
        $scope.corpse_quick_registration = function (corpse, residence) {
            if (angular.isDefined(corpse) == false) {
                return sweetAlert("Enter Details ON the relative/supporter brought the corpse", "", "error");
            }

			//console.log(corpse);
            var first_name = $scope.firstDetails.first_name;
            var middle_name = $scope.firstDetails.middle_name;
            var last_name = $scope.firstDetails.last_name;
            var gender = $scope.firstDetails.gender;
            var dob = $scope.dob;
            var mobile_number = $scope.firstDetails.mobile_number;

           if (angular.isDefined(corpse.names) == false) {
        return sweetAlert("Please enter Names for the Supporter/Relative", "", "error");
          }
		  else if (angular.isDefined(residence) == false) {
                return sweetAlert("Please enter  the Residence Name and choose from the suggestions", "", "error");
            }
			
         else if (angular.isDefined(corpse.relationship) == false) {
              return sweetAlert("Please Select Relation to this corpse", "", "error");
            } 
   		else if (angular.isDefined(corpse.storage_reason) == false) {
              return sweetAlert("Please Select Reasons for Storage", "", "error");
            } 
			
			

            else if (angular.isDefined(middle_name) == false) {
                return sweetAlert("Please Enter MIDDLE NAME or UKNOWN before SAVING", "", "error");
            }

            else if (angular.isDefined(last_name) == false) {
                return sweetAlert("Please Enter LAST NAME or UKNOWN before SAVING", "", "error");
            }
          
         var patient_residences = residence.residence_id; // supporter residences
         var quick_registration = {
				corpse:corpse,
				corpse_details:$scope.firstDetails,
                residence_id: patient_residences,
                whereFoundId: $scope.whereFoundId,
                facility_id: facility_id,
                country_id: $scope.country_id,
                dob: $scope.dob,
                user_id: user_id
            }


            $http.post('/api/corpse_registration', quick_registration).then(function (data) {
                $scope.corpse_registration = data.data;
                ////console.log($scope.corpse_registration);
                if (data.data.status == 0) {
                    sweetAlert(data.data.data, "", "error");
                } else {

                    $mdDialog.show({
                        controller: function ($scope) {
                            $scope.patientData = data.data;
                            var postData = {facility_id: facility_id};
                            $http.post('/api/getMortuaryServices', postData).then(function (datac) {
                                $scope.mortuaryServices = datac.data;
                            });
                            $scope.cancel = function () {
                                $mdDialog.hide();
                            };
                        },
                        templateUrl: '/views/modules/registration/encounterCorpseModal.html',
                        parent: angular.element(document.body),
                        clickOutsideToClose: false,
                        fullscreen: $scope.customFullscreen // Only for -xs, -sm breakpoints.
                    });


                    //quick_registration=$scope.corpse_registration;
                    // $scope.postCorpseMortuary(quick_registration);
                }
            });
        };

        $scope.addEncounterInsuarance = function (registeredPatient, quick_registration) {

			var patient_id = registeredPatient.id;
			var facility_id = registeredPatient.facility_id;
			var user_id = $rootScope.currentUser.id;
			var enterEncounter = {};
			if(!encounter.free_reattendance) {
				$scope.patientsInfo = registeredPatient;
				var patient_category = registeredPatient.patient_category;
				var item_type_id = quick_registration.item_type_id;
				var price_id = quick_registration.price_id;
				var payment_filter = quick_registration.patient_main_category_id;
				var service_category = quick_registration.item_name;
				var service_id = quick_registration.patientservices;
				var price_id = quick_registration.price_id;
				var bill_category_id = quick_registration.patient_main_category_id;
				var main_category_id = quick_registration.patient_main_category_id;
				enterEncounter = {
					'payment_filter': payment_filter,
					'item_type_id': item_type_id,
					'patient_category': patient_category,
					'main_category_id': main_category_id,
					'bill_id': bill_category_id,
					'service_category': service_category,
					'service_id': service_id,
					'price_id': price_id,
					'patient_id': patient_id,
					'facility_id': facility_id,
					'user_id': user_id,
					'is_referral':encounter.is_referral,
					'referring_facility_id':$scope.encounter.referring_facility_id
				};
			}
			else
				enterEncounter = {
					'free_reattendance': true,
					'patient_id': patient_id,
					'dept_id': dept_id,
					'facility_id': facility_id,
					'user_id': user_id,
					'is_referral':encounter.is_referral,
					'referring_facility_id':$scope.encounter.referring_facility_id
				};

            $http.post('/api/enterEncounter', enterEncounter).then(function (data) {
                $scope.registrationReport = data.data;

                if (data.data.status == 0) {

                    sweetAlert(data.data.data, "", "error");
                } else {
                    var modalInstance = $uibModal.open({
                        templateUrl: '/views/modules/registration/printCardBima.html',
                        size: 'lg',
                        animation: true,
                        controller: 'printCardBima',
                        resolve: {
                            patientsInfo: function () {
                                //////console.log($scope.quick_registration);
                                return $scope.patientsInfo;
                            }
                        }


                    });

                    //sweetAlert(data.data.data, "", "success");
                    //enterEncounter='';
                }


            });


        }


        $scope.fullRegistration = function (patient, others) {


            var first_name = patient.first_name;
            var middle_name = patient.middle_name;
            var last_name = patient.last_name;
            var gender = patient.gender;

            var dob = moment(patient.dob).format("YYYY-MM-DD");
            var mobile_number = patient.mobile_number;
            var marital_status = null;
            var occupation = null;

            if (angular.isDefined(first_name) == false) {
                return sweetAlert("Please Enter FIRST NAME before SAVING", "", "error");
            } else if (angular.isDefined(middle_name) == false) {
                return sweetAlert("Please Enter MIDDLE NAME before SAVING", "", "error");
            } else if (angular.isDefined(last_name) == false) {
                return sweetAlert("Please Enter LAST NAME before SAVING", "", "error");
            } else if (angular.isDefined(residence_id) == false) {
                return sweetAlert("Please type the Residence Name and choose from the suggestions", "", "error");
            } else if (angular.isDefined(patient.tribe) == false) {
                return sweetAlert("Please Enter Tribe and choose from the suggestions", "", "error");
            } else if (angular.isDefined(others.country) == false) {
                return sweetAlert("Please Enter Country and choose from the suggestions", "", "error");
            }/* else if (angular.isDefined(resedence_id) == false) {
             return sweetAlert("Please Enter Next of kin Residences and choose from the suggestions", "", "error");
             }*/

            else if (angular.isDefined(others.relationship) == false) {
                return sweetAlert("Please Enter Relationships and choose from the suggestions", "", "error");
            }
            var marital_status = others.marital;

            if (angular.isDefined(others.occupation) == true) {
                var occupation = others.occupation.id;
            }

            ////console.log(others.next_kin_residence);
            var patient_residences = residence_id;

            var tribe = patient.tribe.id;
            var country = others.country.id;
            var next_of_kin_name = others.next_of_kin_name;
            var next_of_kin_resedence_id = resedence_id_kin;
            var relationship = others.relationship;
            var mobile_number_next_kin = others.mobile_number_next_kin;
            var full_registration = {
                "first_name": first_name,
                "middle_name": middle_name,
                "last_name": last_name,
                "dob": dob,
                "gender": gender,
                "mobile_number": mobile_number,
                "residence_id": patient_residences,
                "facility_id": facility_id,
                "user_id": user_id,
                "marital_status": marital_status,
                "occupation_id": occupation,
                "tribe": tribe,
                "country_id": country,
                "next_of_kin_name": next_of_kin_name,
                "next_of_kin_resedence_id": next_of_kin_resedence_id,
                "relationship": relationship,
                "mobile_number_next_kin": mobile_number_next_kin
            }

//console.log(full_registration);

            $http.post('/api/full_registration', full_registration).then(function (data) {
                if (data.data.status == 0) {
                    sweetAlert(data.data.data, "", "error");
                } else {

                    var patientData = data.data[0];
                    var accounts_number = data.data[1][0];
                    var residences = data.data[2][0];
                    var getLastVisit = data.data[3];
                    var object = {
                        'patientData': patientData,
                        'accounts_number': accounts_number,
                        'residences': residences,
                        'getLastVisit': getLastVisit
                    };
                    $scope.showFirstForm();
                     $scope.patient.first_name = null;
                    $scope.patient.middle_name = null;
                    $scope.patient.last_name = null;
                    $scope.patient.age = null;
                    $scope.patient.tribe = null;
                    $scope.patient.gender = null;
                    $scope.patient.mobile_number = null;
                    $scope.patient.tribe = null;
                    $scope.residence = null;
                
                    $scope.others = null;
                    var ev = null;
                    $mdDialog.show({
                        controller: quickRegistrationModal,
                        templateUrl: '/views/modules/registration/encounterModal.html',
                        parent: angular.element(document.body),
                        targetEvent: ev,
                        clickOutsideToClose: false,
                        resolve: {
                            object: function () {
                                return object;
                            }
                        }
                    }).then(function (answer) {
                        $scope.status = 'You said the information was "' + answer + '".';
                    }, function () {
                        $scope.status = 'You cancelled the dialog.';
                    });


                }
            });
        };


        $scope.viewItemFull = function (full_registration) {
            $scope.quick_registration = full_registration;

            var quick_registration = full_registration;
            ////console.log(full_registration.first_name);
            var modalInstance = $uibModal.open({
                templateUrl: '/views/modules/registration/encounterModal.html',
                size: 'lg',
                animation: true,
                controller: 'registrationModal',
                resolve: {
                    quick_registration: function () {
                        ////console.log($scope.quick_registration);
                        return $scope.quick_registration;
                    }
                }


            });

            modalInstance.result.then(function (quick_registration) {
                $scope.quick_reg = quick_registration;
                ////console.log($scope.quick_reg);
            });
        }

        $http.get('/api/getUsermenu/' + user_id).then(function (data) {
            $scope.menu = data.data;
            //////console.log($scope.menu);
        });


        $scope.getPricedItems = function (patient_category_selected) {
            //////console.log(patient_category_selected);
	var postData={facility_id:facility_id,patient_category:patient_category_selected};
    $http.post('/api/getPricedItems', postData).then(function (data) {
                $scope.services = data.data;
            });

        }

        $scope.getpatient = function () {

        };


        $scope.getReportBasedOnthisDate = function (dt_start, dt_end) {
            var reportsOPD = {"facility_id": facility_id, "start_date": dt_start, "end_date": dt_end};

            $http.post('/api/getMahudhurioOPD', reportsOPD).then(function (data) {

                var attendanceOpd = data.data[0][0];
                var newAttendanceOpd = data.data[1][0];
                var opd_mahudhurio_marudio = data.data[2][0];
                var opd_diagnosises = data.data[3];
                var object = {
                    'opd_diagnosises': opd_diagnosises,
                    'attendanceOpd': attendanceOpd,
                    'newAttendanceOpd': newAttendanceOpd,
                    'opd_mahudhurio_marudio': opd_mahudhurio_marudio
                };
                $scope.opd_mahudhurio = object.attendanceOpd;
                $scope.opd_mahudhurio_new = object.newAttendanceOpd;
                $scope.opd_mahudhurio_marudio = object.opd_mahudhurio_marudio;
                $scope.opd_diagnosises = object.opd_diagnosises;
                $scope.start_date = dt_start;
                $scope.end_date = dt_end;


            });
        }

        $scope.regReport = function () {
            var reportsOPD = {
                "facility_id": facility_id,
                "start_date": '2017-01-01',
                "end_date": '2017-07-07'
            };

            $http.post('/api/getMahudhurioOPD', reportsOPD).then(function (data) {

                var attendanceOpd = data.data[0][0];
                var newAttendanceOpd = data.data[1][0];
                var opd_mahudhurio_marudio = data.data[2][0];
                var opd_diagnosises = data.data[3];
                var object = {
                    'opd_diagnosises': opd_diagnosises,
                    'attendanceOpd': attendanceOpd,
                    'newAttendanceOpd': newAttendanceOpd,
                    'opd_mahudhurio_marudio': opd_mahudhurio_marudio
                };
                var modalInstance = $uibModal.open({
                    templateUrl: '/views/modules/reports/registration_report.html',
                    size: 'lg',
                    animation: true,
                    controller: 'opdMtuhaController',
                    windowClass: 'app-modal-window',
                    resolve: {
                        object: function () {
                            return object;
                        }
                    }
                });
            });
        }


        $scope.showGridBottomSheet = function (patientData, residences, encounter, patient) {


            $scope.alert = '';
            console.log(patientData);
            $scope.patient_id = patientData.id;
            if (angular.isDefined(encounter) == false) {
                return sweetAlert("Please Type the Payment Category", "", "error");
            }
            else if (angular.isDefined(encounter.payment_category) == false) {
                return sweetAlert("Please Type the Payment Category", "", "error");
            }
            else if (angular.isDefined(encounter.payment_services) == false) {
                return sweetAlert("Please Select Service", "", "error");
            }


            $scope.cancel = function () {
                $mdDialog.hide();
            };

            $scope.cancel();


            $mdDialog.show({
                templateUrl: '/views/modules/registration/clinic-template.html',

                controller: function ($scope, $rootScope) {
                    $scope.patientData = patientData;
                    console.log($scope.patientData);
                    $scope.residences = residences;
                    $scope.encounter = encounter;
                    $scope.patient = patient;
                    $http.get('/api/getClinic').then(function (data) {
                        $scope.departments = data.data;
                    });

                    $scope.listItemClick = function (department, patientData, residences, encounter, patient) {
                        var clickedItem = department.department_name;
                        var dept_id = department.id;
                        $scope.patient_id = patientData.patient_id;
                        $scope.facility_id = facility_id;
                        $mdBottomSheet.hide(clickedItem);
                    };
                    $scope.cancel = function () {
                        $mdDialog.hide();
                    };
                    $http.get('/api/getLoginUserDetails/' + user_id).then(function (data) {
                        $scope.loginUserFacilityDetails = data.data;
                    });
                },
                clickOutsideToClose: false
            }).then(function ($scope, clickedItem, dept_id) {
				var patient_id = $scope.patient_id;
				var facility_id = facility_id;
				var user_id = $rootScope.currentUser.id;
				var enterEncounter = {};
				if(!encounter.free_reattendance) {
					var patient_category = encounter.payment_category.patient_category;
					var service_category = encounter.payment_services;
					var service_id = encounter.payment_services.service_id;
					var price_id = encounter.payment_services.price_id;
					var item_type_id = encounter.payment_services.item_type_id;
					var payment_filter = encounter.payment_services.patient_category_id;
					var bill_category_id = encounter.payment_services.patient_category_id;
					var main_category_id = encounter.payment_services.patient_main_category_id;

					enterEncounter = {
						'dept_id': dept_id,
						'payment_filter': payment_filter,
						'item_type_id': item_type_id,
						'patient_category': patient_category,
						'main_category_id': main_category_id,
						'bill_id': bill_category_id,
						'service_category': service_category,
						'service_id': service_id,
						'price_id': price_id,
						'patient_id': patient_id,
						'facility_id': facility_id,
						'user_id': user_id,
						'is_referral':encounter.is_referral,
						'referring_facility_id':$scope.encounter.referring_facility_id
					};
				}
				else
					enterEncounter = {
						'free_reattendance': true,
						'patient_id': patient_id,
						'dept_id': dept_id,
						'facility_id': facility_id,
						'user_id': user_id,
						'is_referral':encounter.is_referral,
						'referring_facility_id':$scope.encounter.referring_facility_id
					};

                $http.post('/api/enterEncounter', enterEncounter).then(function (data) {
                    $scope.registrationReport = data.data;

                    if (data.data.status == 0) {

                        sweetAlert(data.data.data, "", "error");
                    } else {
                        var object = {'patientsInfo': patientData, 'residences': residences};
                        $scope.cancel();
                        var ev = null;
                        $mdToast.show(
                            $mdToast.simple()
                                .textContent(patientData.last_name + ' posted to ' + clickedItem + " Clinic")
                                .position('top right')
                                .hideDelay(1500)
                        );

                        $mdDialog.show({
                            controller: PrintCardDialogController,
                            templateUrl: '/views/modules/registration/printCard.html',
                            parent: angular.element(document.body),
                            targetEvent: ev,
                            clickOutsideToClose: true,
                            fullscreen: $scope.customFullscreen, // Only for -xs, -sm breakpoints.
                            resolve: {
                                object: function () {
                                    ////console.log($scope.quick_registration);
                                    return object;
                                }
                            }

                        }).then(function (answer) {
                            $scope.status = 'You said the information was "' + answer + '".';
                        }, function () {
                            $scope.status = 'You cancelled the dialog.';
                        });
                    }

                });


            }).catch(function (error) {
                // User clicked outside or hit escape
            });
        };

        $scope.attendPatient = function (patientData, residences, encounter,patient) {
            console.log(patientData);
            console.log(residences);
            console.log(encounter);
            console.log(patient);
            $scope.cancel = function () {
                $mdDialog.hide();
            };
            $mdDialog.show({
                controller: patientController,
                scope: $scope,
                preserveScope: true,
                templateUrl: '/views/modules/registration/clinic-modal.html',
                clickOutsideToClose: false,
                fullscreen: true
            });
        };

        $scope.showGridBottomSheetReattendence = function (patientData, residences, encounter,patient) {
			if(encounter.is_admitted && encounter.is_admitted == 1){
				swal({
					  title: 'MARKING ADMISSION',
					  html: 'Are you sure you want to mark this patient as admitted?',
					  type: 'info',
					  showCancelButton: true,
					  confirmButtonColor: '#3085d6',
					  cancelButtonColor: '#d33',
					  confirmButtonText: 'Yes',
					  cancelButtonText: 'No'
				}).then(function () {
					Helper.overlay(true);
					$http.post('/api/falseAdmit',{patient_id:$scope.accounts_number.patient_id, user_id:user_id,facility_id:facility_id,account_id:$scope.accounts_number.id})
                    .then(function (response) {
                        Helper.overlay(false);
						swal(response.data, '','info');
                    });
				}, function(){ });				
				return;
			}else{
				$scope.alert = '';
				$scope.patient_id = patientData.patient_id;
				if(!encounter.free_reattendance){
					if (angular.isDefined(encounter) == false) {
						return sweetAlert("Please Type the Payment Category", "", "error");
					}
					else if (angular.isDefined(encounter.payment_category) == false) {
						return sweetAlert("Please Type the Payment Category", "", "error");
					}
					else if (angular.isDefined(encounter.payment_services) == false) {
						return sweetAlert("Please Select Service", "", "error");
					}
				}


				$scope.cancel = function () {
					$mdDialog.hide();
				};

				$scope.cancel();


				$mdDialog.show({
					templateUrl: '/views/modules/registration/re-clinic-template.html',

					controller: function ($scope, $rootScope) {
						$scope.patientData = patientData;
						console.log($scope.patientData);
						$scope.residences = residences;
						$scope.encounter = encounter;
						$scope.patient = patient;
						$http.get('/api/getClinic').then(function (data) {
							$scope.departments = data.data;
						});

						$scope.listItemClick = function (department, patientData, residences, encounter, patient) {
							var clickedItem = department.department_name;
							var dept_id = department.id;
							$scope.patient_id = patientData.patient_id;
							$scope.facility_id =facility_id;
							$mdBottomSheet.hide(clickedItem);
						};
						$scope.cancel = function () {
							$mdDialog.hide();
						};
						$http.get('/api/getLoginUserDetails/' + user_id).then(function (data) {
							$scope.loginUserFacilityDetails = data.data;
						});
					},
					clickOutsideToClose: false
				}).then(function ($scope, clickedItem, dept_id) {

					var patient_id = $scope.patient_id;
					var facility_id = facility_id;
					var user_id = $rootScope.currentUser.id;
					var enterEncounter = {};
					if(!encounter.free_reattendance) {
						var patient_category = encounter.payment_category.patient_category;
						var service_category = encounter.payment_services;
						var service_id = encounter.payment_services.service_id;
						var price_id = encounter.payment_services.price_id;
						var item_type_id = encounter.payment_services.item_type_id;
						var payment_filter = encounter.payment_services.patient_category_id;
						var bill_category_id = encounter.payment_services.patient_category_id;
						var main_category_id = encounter.payment_services.patient_main_category_id;
						////
						
						
						enterEncounter = {
							'dept_id': dept_id,
							'payment_filter': payment_filter,
							'item_type_id': item_type_id,
							'patient_category': patient_category,
							'main_category_id': main_category_id,
							'bill_id': bill_category_id,
							'service_category': service_category,
							'service_id': service_id,
							'price_id': price_id,
							'patient_id': patient_id,
							'facility_id': facility_id,
							'user_id': user_id,
							'is_referral':encounter.is_referral,
							'referring_facility_id':$scope.encounter.referring_facility_id
						};
					}
					else
						enterEncounter = {
							'free_reattendance': true,
							'patient_id': patient_id,
							'dept_id': dept_id,
							'facility_id': facility_id,
							'user_id': user_id,
							'is_referral':encounter.is_referral,
							'referring_facility_id':$scope.encounter.referring_facility_id
						};
					
				   
					$http.post('/api/enterEncounter', enterEncounter).then(function (data) {
						$scope.registrationReport = data.data;

						if (data.data.status == 0) {

							sweetAlert(data.data.data, "", "error");
						} else {
							var object = {'patientsInfo': patientData, 'residences': residences};
							$scope.cancel();
							var ev = null;
							$mdToast.show(
								$mdToast.simple()
									.textContent(patientData.last_name + ' posted to ' + clickedItem + " Clinic")
									.position('top right')
									.hideDelay(1500)
							);

							$mdDialog.show({
								controller: PrintCardDialogController,
								templateUrl: '/views/modules/registration/printCard.html',
								parent: angular.element(document.body),
								targetEvent: ev,
								clickOutsideToClose: true,
								fullscreen: $scope.customFullscreen, // Only for -xs, -sm breakpoints.
								resolve: {
									object: function () {
										////console.log($scope.quick_registration);
										return object;
									}
								}

							}).then(function (answer) {
								$scope.status = 'You said the information was "' + answer + '".';
							}, function () {
								$scope.status = 'You cancelled the dialog.';
							});
						}

					});


				}).catch(function (error) {
					// User clicked outside or hit escape
				});
			}
        };


        $scope.regPerfomance = function () {
            var reportsOPD = {
                "user_id": user_id,
                "facility_id": facility_id
            };

            $http.post('/api/getStaffPerfomance', reportsOPD).then(function (data) {

                var specificStaff = data.data[0][0];
                var otherStaff = data.data[1][0];
                var startDate = data.data[2];
                var endDate = data.data[3];
                var object = {
                    'endDate': endDate,
                    'startDate': startDate,
                    'specificStaff': specificStaff,
                    'otherStaff': otherStaff
                };
                //////console.log(object);

                $mdDialog.show({
                    locals: {
                        'endDate': endDate,
                        'startDate': startDate,
                        'specificStaff': specificStaff,
                        'otherStaff': otherStaff
                    },
                    controller: function ($scope, $rootScope) {
                        $scope.endDate = endDate;
                        $scope.startDate = startDate;
                        $scope.specificStaff = specificStaff;
                        $scope.otherStaff = otherStaff;
                        $scope.getReportBasedOnthisDate = function (dt_start, dt_end) {
                            var reportsOPD = {
                                "user_id": user_id,
                                "facility_id": facility_id,
                                "start_date": dt_start,
                                "end_date": dt_end
                            };

                            $http.post('/api/getStaffPerfomance', reportsOPD).then(function (data) {

                                $scope.specificStaff = data.data[0][0];
                                $scope.otherStaff = data.data[1][0];
                                $scope.start_date = dt_start;
                                $scope.end_date = dt_end;


                            });
                        };
                        $scope.currentUser = $rootScope.currentUser;
                        $scope.cancel = function () {
                            $mdDialog.hide();
                        };
                        $http.get('/api/getLoginUserDetails/' + user_id).then(function (data) {
                            $scope.loginUserFacilityDetails = data.data;
                        });
                    },
                    templateUrl: '/views/modules/reports/regPerfomance.html',
                    parent: angular.element(document.body),
                    clickOutsideToClose: false,
                    fullscreen: $scope.customFullscreen // Only for -xs, -sm breakpoints.
                });
            });
        }
        $scope.getPricedMortuary = function () {
            $http.get('/api/getMortuary').then(function (data) {
                $scope.mortuaries = data.data;
                //////console.log($scope.mortuaries);
                //
                //
            });

        }
        $scope.getRelationships();

        //Patient Update
        $scope.updatepatient = function (patients) {
            var comit = confirm('Are you sure you want to Update ' + patients.first_name);
            if (comit) {
                $http.post('/api/updatepatient', patients).then(function (data) {
                })
                $scope.getpatient();
            } else {
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
        $scope.showSearchMarital();
        $scope.showNextForm = function (patient) {
            $scope.patient = patient;
            //////console.log($scope.patient)
            $scope.firstFormShow = false;
            $scope.secondFormShow = true;

        }


        $scope.getStaffPerfomance = function (dt_start, dt_end) {
            var reportsOPD = {
                "user_id": user_id,
                "facility_id": facility_id,
                "start_date": dt_start,
                "end_date": dt_end
            };

            $http.post('/api/getStaffPerfomance', reportsOPD).then(function (data) {

                $scope.results = data.data[0][0];
                $scope.otherStaff = data.data[1][0];
                $scope.start_date = dt_start;
                $scope.end_date = dt_end;


            });
        };


        //Patient Update Ame
        $scope.updatepatient = function (selectedpatient, residence, marital, occupation,
                                         tribe, country, gender) {
            var patient_info_toupdate = {
                "id": selectedpatient.id,
                "first_name": selectedpatient.first_name,
                "middle_name": selectedpatient.middle_name,
                "last_name": selectedpatient.last_name,
                "dob": selectedpatient.dob,
                "gender": gender.gender,
                "mobile_number": selectedpatient.mobile_number,
                "residence_id": residence.residence_id,
                "marital_id": marital.id,
                "occupation_id": occupation.id,
                "tribe_id": tribe.id,
                "country_id": country.id,
                "facility_id": $rootScope.currentUser.facility_id,
            }
            //////console.log(patient_info_toupdate);
            //////console.log(patient_info_toupdate)
            $http.post('/api/updatepatient', patient_info_toupdate).then(function (data) {
                $scope.patientss = data;
                //////console.log(patientss);
                swal("You have Succesfully Update" + $scope.patientss);
            });
        }


       // exemptions======================================================
 
        $scope.exemption_type_list = function () {
            $http.get('/api/exemption_type_list/' + user_id).then(function (data) {
                $scope.exemption_types = data.data;


            });
        }

        $scope.exemption_type_list();

        $http.get('/api/getexemption_services/' + facility_id).then(function (data) {
            $scope.exemption_services = data.data;
        });
 
        $http.get('/api/getSpecialClinics').then(function (data) {
            $scope.departments = data.data;

        });
        $scope.exemption_registration = function (exempt, selectedPatient) {

            var patient_id;
            if(selectedPatient.id==undefined){
                patient_id=selectedPatient.patient_id;
            }
            else{
                patient_id=selectedPatient.id;
            }


            $http.get('/api/patients_address_info/' + patient_id).then(function (data) {
                $scope.patients_address = data.data[0];
                
            });
            $scope.quick_registration=selectedPatient;
            var dept_id=1;
            if (exempt.referral_id != undefined) {
                dept_id = exempt.referral_id;
            }
            var reason_for_revoke = "..";
            var patient = selectedPatient.patient_id;

            if (patient_id == undefined ) {
                swal(
                    'Feedback..',
                    'Please Select Client from a Search Box above...',
                    'error'
                )

            }
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
            } else if (exempt.service == undefined) {
                swal(
                    'Feedback..',
                    'Please Choose service ',
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
                var patient = patient_id;
                var exemption_type_id = exempt.exemption_type_id.id;
                var main_category_id = exempt.exemption_type_id.pay_cat_id;
                var user_id = $rootScope.currentUser.id;
                var facility_id = $rootScope.currentUser.facility_id;
                var patient_id = patient_id;
                var bill_id = exempt.exemption_type_id.id;
                var status_id = status_id;
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
                formdata.append('dept_id', dept_id);
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
                        //console.log(data.data.status);
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
        $scope.PrintContentR=function () {
            //location.reload();
            var DocumentContainer = document.getElementById('divtoprintCArd');
            var WindowObject = window.open("", "PrintWindow",
                "width=900,height=700,top=50,left=450,toolbars=no,scrollbars=no,status=no,resizable=yes");
            WindowObject.document.title = "PRINT PATIENT CARD: GoT-HOMIS";
            WindowObject.document.writeln(DocumentContainer.innerHTML);
            WindowObject.document.close();

            setTimeout(function () {
                WindowObject.focus();
                WindowObject.print();
                WindowObject.close();
            });

        }



        $scope.PrintContentChf=function () {
            //location.reload();
            var DocumentContainer = document.getElementById('PrintContentChfid');
            var WindowObject = window.open("", "PrintWindow",
                "width=900,height=700,top=50,left=450,toolbars=no,scrollbars=no,status=no,resizable=yes");
            WindowObject.document.title = "PRINT PATIENT CARD: GoT-HOMIS";
            WindowObject.document.writeln(DocumentContainer.innerHTML);
            WindowObject.document.close();

            setTimeout(function () {
                WindowObject.focus();
                WindowObject.print();
                WindowObject.close();
            });


        }

        $scope.PrintContentnation=function () {
            //location.reload();
            var DocumentContainer = document.getElementById('PrintContentnationId');
            var WindowObject = window.open("", "PrintWindow",
                "width=900,height=700,top=50,left=450,toolbars=no,scrollbars=no,status=no,resizable=yes");
            WindowObject.document.title = "PRINT PATIENT CARD: GoT-HOMIS";
            WindowObject.document.writeln(DocumentContainer.innerHTML);
            WindowObject.document.close();

            setTimeout(function () {
                WindowObject.focus();
                WindowObject.print();
                WindowObject.close();
            });


        }
//changing patient_category

        $scope.getAllPatient = function (text) {
            return Helper.getAllPatient(text)
                .then(function (response) {
                    $scope.selectedPatient=response.data;
                    $scope.selectPatient=response.data;
                    return response.data;
                });

        };
        $scope.DisplayInfors = function (datee) {

            $scope.selectedPatientInf=datee;

        };
        $http.get('/api/payment_sub_category_list').then(function(data) {
            $scope.payment_sub_categories=data.data;

        });


        $scope.change_patient_category_receiption=function (category,selectedP) {
         // console.log(category,selectedP);
          var  datails={
              patient_id:selectedP.id,category_id:category.patient_category_id,
              main_category_id:category.patient_main_category_id};
            //console.log(datails);

            var patient_cat=category.patient_category;

            swal(
                'Success',
                'Patient Has Changed to <b>  '+ patient_cat+"</b>   ",
                'success'
            );
            

            swal({
                title: 'Are You Sure ?',

                text: 'Patient Will be Changed To '+ patient_cat,
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

                $http.post('/api/change_patient_category_receiption',datails).then(function(data) {


                    var msg = data.data.msg;
                    var statuss = data.data.status;
                    if (statuss == 0) {

                        swal(
                            'Error',
                            msg,
                            'error'
                        );

                    }
                    else{
                        swal(
                            'Success',
                            'Patient Has Changed  <b> '+ patient_cat+"</b>   ",
                            'success'
                        );
                    }

                });


            }, function (dismiss) {
                // dismiss can be 'cancel', 'overlay',
                // 'close', and 'timer'
                if (dismiss === 'cancel') {
                    swal(
                        'You have Cancelled',
                        '',
                        'error'
                    );

                }
            })
        }
        //changing patient_category

    }

})();