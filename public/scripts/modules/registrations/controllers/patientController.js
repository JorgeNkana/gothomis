(function () {
    'use strict';

    angular.module('authApp').controller('PatientController', PatientController);
    PatientController.$inject = ['$scope', '$rootScope', '$mdDialog', '$state', 'User', 'Facility', 'Professional', '$http', 'Patient', 'Helper', 'SearchPatients', 'toastr'];

    function PatientController($scope, $rootScope, $mdDialog, $state, User, Facility, Professional, $http, Patient, Helper, SearchPatients, toastr) {
        var facility_id = $rootScope.currentUser.facility_id;
        var user_id = $rootScope.currentUser.id;

        var formdata = new FormData();
        $scope.today_date = new Date();
        $scope.items = [
            { name: 'Share', icon: 'share' },
            { name: 'Upload', icon: 'upload' },
            { name: 'Copy', icon: 'copy' },
            { name: 'Print this page', icon: 'print' },
        ];

        $scope.verifyNhifCard = function (client) {
            var postData = {card_number: client.card_no, account_id: client.account_id};
            $http.post('/api/verify-nhif-card', postData).then(function (response) {
                $scope.verifiedCards = response.data;
                if (response.data.StatusCode == 0) {
                    return toastr.error('', 'No Internet connection');
                } else if (response.data.StatusCode == 500) {
                    return sweetAlert('Please Enter Correct Card Number', "", "error");

                } else if (response.data.AuthorizationStatus == 'REJECTED') {
                    var FullName = response.data.FullName;
                    var remarks = response.data.Remarks;
                    var message = FullName + " , " + remarks;

                    return sweetAlert(message, "", "error");
                } else if (response.data.AuthorizationStatus == 'ACCEPTED') {

                    return sweetAlert('Authorization Number : ' + response.data.AuthorizationNo + ', MembershipNo ' + response.data.MembershipNo, response.data.FirstName + ' ' + response.data.MiddleName + ' ' + response.data.LastName, 'success');


                }

            });

        };

        $scope.getNonVerified = function (pef) {
            if (angular.isDefined(pef) == false) {
                return sweetAlert('Please Select Date Range for the Non verified Clients');
            }
            var start_date = pef.start;
            var end_date = pef.end;
            var postData = {start_date: start_date, end_date: end_date};
            $http.post('/api/get-non-verified', postData).then(function (response) {
                $scope.nonverifiedLists = response.data;
            });
        };

        $scope.getNonCollectedCards = function () {

            $http.post('/api/get-non-collected-cards').then(function (response) {
                $scope.nonCardCollectedLists = response.data;
            });
        };

        $scope.giveNhifCard = function (patient) {
            var account_id = patient.account_id;
            var postData = {account_id: account_id};
            $http.post('/api/give-cards', postData).then(function (response) {

                return sweetAlert(response.data.Message, '', response.data.status);
            });
        };


        $scope.getTheFiles = function ($files) {
            angular.forEach($files, function (value, key) {
                formdata.append(key, value);
            });
        };

        $scope.limitOptions = [5, 10, 15, 20, 50, 100, 200, 500];
        $scope.selected = [];
        var tribe = [];
        var patientLists = [];
        var patientDetails = {};

        $scope.options = {
            rowSelection: false,
            multiSelect: true,
            autoSelect: true,
            decapitate: false,
            largeEditDialog: false,
            boundaryLinks: false,
            limitSelect: true,
            pageSelect: true
        };

        $scope.query = {
            per_page: 10,
            page: 1
        };


        $scope.whereCorpseFound = function (item) {
            $scope.residenceFound = item;

        };

        $scope.corpse_quick_registration = function (corpse, residence) {
            if (angular.isDefined(corpse) == false) {
                return sweetAlert("Enter Details ON the relative/supporter brought the corpse", "", "error");
            }

            var first_name = corpse.first_name;
            var middle_name = corpse.middle_name;
            var last_name = corpse.last_name;
            var gender = corpse.gender;
            var dob = corpse.dob;
            var dod = corpse.dod;
            var mobile_number = corpse.mobile_number;
            var time_death = corpse.time_death;
            var death_condition = corpse.death_condition;
            var identityNumber = corpse.identityNumber;
            var identityType = corpse.identityType;
            var country_id = corpse.country.id;


            if (angular.isDefined(corpse.names) == false) {
                return sweetAlert("Please enter Names for the Supporter/Relative", "", "error");
            } else if (angular.isDefined($scope.residenceFound) == false) {
                return sweetAlert("Please enter  the Residence Name and choose from the suggestions", "", "error");
            } else if (angular.isDefined(corpse.relationship) == false) {
                return sweetAlert("Please Select Relation to this corpse", "", "error");
            } else if (angular.isDefined(corpse.storage_reason) == false) {
                return sweetAlert("Please Select Reasons for Storage", "", "error");
            } else if (angular.isDefined(middle_name) == false) {
                return sweetAlert("Please Enter MIDDLE NAME or UKNOWN before SAVING", "", "error");
            } else if (angular.isDefined(last_name) == false) {
                return sweetAlert("Please Enter LAST NAME or UKNOWN before SAVING", "", "error");
            }

            var relation_id = corpse.relationship;
            var names = corpse.names;
            var storage_reason = corpse.storage_reason;
            var vehicle_number = corpse.vehicle_number;
            var description = corpse.description;
            var mobile_number_supporter = corpse.mobile_number;

            var corpse_residences = $scope.residenceFound.residence_id; // supporter residences
            var quick_registration = {
                relation_id: relation_id,
                names: names,
                mobile_number_supporter: mobile_number_supporter,
                corpse_residences: corpse_residences,
                storage_reason: storage_reason,
                vehicle_number: vehicle_number,
                description: description,
                first_name: first_name,
                middle_name: middle_name,
                last_name: last_name,
                gender: gender,
                dob: dob,
                dod: dod,
                mobile_number: mobile_number,
                time_death: time_death,
                death_condition: death_condition,
                identityNumber: identityNumber,
                identityType: identityType,
                country_id: country_id,
                whereFoundId: corpse_residences,
                facility_id: facility_id,
                country_id: country_id,
                user_id: user_id
            }


            $http.post('/api/corpse_registration', quick_registration).then(function (data) {
                $scope.corpse_registration = data.data;
                //console.log($scope.corpse_registration);
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

                            $scope.startSessionCorpse = function (corpse, corpse_service) {

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
                                        sweetAlert(data.data.message, "", "error");
                                    } else {
                                        $mdDialog.show({
                                            controller: function ($scope) {
                                                $scope.corpseDetails = data.data.data;

                                                $http.get('/api/getLoginUserDetails/' + user_id).then(function (cardTitle) {
                                                    $scope.facility_address = cardTitle.data[0];
                                                });
                                                $scope.cancel = function () {
                                                    $mdDialog.hide();
                                                };

                                                $scope.printForm = function () {
                                                    //location.reload();
                                                    var DocumentContainer = document.getElementById('divtoprint');
                                                    var WindowObject = window.open("", "PrintWindow",
                                                        "width=900,height=700,top=50,left=450,toolbars=no,scrollbars=no,status=no,resizable=yes");
                                                    WindowObject.document.title = "PRINT CORPSE CARD: GoT-HOMIS";
                                                    WindowObject.document.writeln(DocumentContainer.innerHTML);
                                                    WindowObject.document.close();

                                                    setTimeout(function () {
                                                        WindowObject.focus();
                                                        WindowObject.print();
                                                        WindowObject.close();
                                                    }, 0);

                                                }
                                            },
                                            templateUrl: '/scripts/modules/registrations/views/corpse-card.html',
                                            parent: angular.element(document.body),
                                            clickOutsideToClose: false,
                                            fullscreen: $scope.customFullscreen // Only for -xs, -sm breakpoints.

                                        });
                                    }
                                });


                            };
                        },
                        templateUrl: '/scripts/modules/registrations/views/encounterCorpseModal.html',
                        parent: angular.element(document.body),
                        clickOutsideToClose: false,
                        fullscreen: $scope.customFullscreen // Only for -xs, -sm breakpoints.
                    });


                    //quick_registration=$scope.corpse_registration;
                    // $scope.postCorpseMortuary(quick_registration);
                }
            });
        };

        $scope.showFirstForm = function (patient, others, residence) {
            $scope.others = others;
            $scope.residence = residence;
            $scope.firstFormShow = true;
            $scope.secondFormShow = false;
        }();

        $scope.FirstForm = function () {
            $scope.firstForm = true;
        }();

        $scope.resetUser = function () {
            $scope.user = {};
        };


        $scope.getMahudhuriOPDRegistration = function (pef) {

            if (angular.isDefined(pef) == false) {
                return sweetAlert("You must select date range", "", "error");
            }
            var facilityDetails = [];

            $http.get('/api/getLoginUserDetails/' + user_id).then(function (cardTitle) {
                $scope.facility_address = cardTitle.data[0];
                facilityDetails = cardTitle.data[0];
                console.log($scope.facility_address);

            });
            var dataToPost = {facility_id: facility_id, start_date: pef.start, end_date: pef.end};
            $scope.start_date = pef.start;
            $scope.end_date = pef.end;

            $http.post('/api/getRegistrationReports', dataToPost).then(function (data) {
                $scope.opd_mahudhurio = data.data[0][0];
                $scope.opd_mahudhurio_marudio = data.data[1][0];
                $scope.defaultValue = 0;

            });

        };


        $scope.verification = function (item) {
            $scope.item_parameters = item;
            $scope.dataLoading = true;
            var creditials = {
                "card_number": item.nhif_card,
                "referal_no": item.refferal_no,
                "visit_type": item.visitType,
                facility_id: facility_id
            };
            $http.post('/api/client-registration', creditials).then(
                function (response) {
                    //////console.log(response.data);
                    if (response.data[0].StatusCode == 0) {
                        return toastr.error('', 'No Internet connection');
                    } else if (response.data[0].StatusCode == 500) {
                        return sweetAlert(response.data[0].Message, "", "error");

                    } else if (response.data[0].AuthorizationStatus == 'REJECTED') {
                        var FullName = response.data[0].FullName;
                        var remarks = response.data[0].Remarks;
                        var message = FullName + " , " + remarks;

                        return sweetAlert(message, "", "error");
                    } else if (response.data[0].AuthorizationStatus == 'ACCEPTED') {
                        $scope.dataLoading = false;
                        $mdDialog.show({
                            controller: function ($scope) {

                                $scope.patientData = {
                                    "first_name": response.data[0].FirstName,
                                    "middle_name": response.data[0].MiddleName,
                                    "last_name": response.data[0].LastName,
                                    "gender": response.data[0].Gender,
                                    "dob": response.data[0].DateOfBirth,
                                    "AuthorizationNo": response.data[0].AuthorizationNo,
                                    "membership_number": response.data[0].MembershipNo,
                                    "card_no": response.data[0].CardNo,
                                    "SchemeID": response.data[0].SchemeID,
                                    "visit_type": response.data[1]
                                };

                                $scope.selectedResidence = function (residence) {
                                    $scope.residence = residence;
                                    residence_id = $scope.residence.residence_id;
                                };
                                var insuranceService = [];
                                $scope.patientInsuaranceService = function (searchKey, scheme) {
                                    console.log(scheme);
                                    var searchKeyReceived = {
                                        'patient_category': 'NHIF',
                                        'item_name': searchKey,
                                        'scheme_id': scheme.SchemeID,
                                        facility_id: $rootScope.currentUser.facility_id
                                    };
                                    $http.post('/api/consultation-service', searchKeyReceived).then(function (data) {
                                        insuranceService = data.data;
                                    });
                                    return insuranceService;
                                };

                                $scope.getResidence = function (text) {
                                    return Helper.getResidence(text).then(function (response) {
                                        return response.data;
                                    });
                                };
                                $scope.showSearchOccupation = function (text) {
                                    $http.get('/api/getOccupation/' + text).then(function (data) {
                                        occupation = data.data;
                                    });
                                    return occupation;
                                };

                                $scope.savePatientInsuarance = function (quick_registration, patient, residence) {
                                    console.log(patient);
                                    console.log(quick_registration);

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
                                    } else if (angular.isDefined(residence) == false) {
                                        return sweetAlert("Please Enter Residence Name", "", "error");
                                    }


                                    var nida = patient.nida;
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
                                        nida: nida,
                                        scheme_id: scheme_id,
                                        visit_type: visit_type,
                                        "dob": dob,
                                        "gender": gender,
                                        "mobile_number": mobile_number,
                                        "residence_id": patient_residences,
                                        "facility_id": facility_id,
                                        "user_id": user_id
                                    }
                                    $http.post('/api/insuaranceRegistration', insuaranceRegistration).then(function (response) {
                                        //console.log(response.data.data);

                                        if (response.data.status == 400) {
                                            sweetAlert(response.data.errors, "", "error");
                                        } else {
                                            var returnedData = response.data.data[0];
                                            $scope.cancel();
                                            $mdDialog.show({
                                                controller: function ($scope) {

                                                    $scope.patientData = returnedData;
                                                    $http.get('/api/getUsermenu/' + user_id).then(function (cardTitle) {
                                                        $scope.cardTitle = cardTitle.data[0];

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

                                $scope.cancel = function () {
                                    $scope.selectedPatient = null;
                                    $mdDialog.hide();
                                };
                            },
                            templateUrl: '/scripts/modules/registrations/views/insuarance.html',
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
                    } else {
                        return sweetAlert('NHIF Server not reachable,Please contact NHIF for support', "", "info");
                    }
                },
                function (data) {
                    // Handle error here
                    toastr.error('', 'Failed to connect to NHIF , ensure API credential is set properly');
                }).finally(function () {
                $scope.dataLoading = false;
            });
        };

        $scope.searchByReg = function (regNumber) {
            if (angular.isDefined(regNumber) == false) {
                return sweetAlert("Please enter registration number", "", "error");
            }
            var postData = {reg_number: regNumber};
            $http.post('/api/advancedSearch', postData).then(function (data) {
                $scope.regInfo = data.data;
            });
        };

        $scope.searchBySurname = function (surName) {
            if (angular.isDefined(surName) == false) {
                return sweetAlert("Please enter Surname /Last Name", "", "error");
            }
            var postData = {surName: surName};
            $http.post('/api/advancedSearch', postData).then(function (data) {
                $scope.regInfo = data.data;
            });
        };

        $scope.getPatientCategory = function (patient) {
            if (angular.isDefined(patient) == false) {
                return false;
            }
            var postData = {patient_id: patient.patient_id};
            $http.post('/api/getPatientCategory', postData).then(function (data) {
                $scope.patientCategories = data.data[0];
                $scope.userFeesPaymentsCategories = data.data[1];
            });
        };

        $scope.changeCategory = function (patientCat, patient) {
            if (angular.isDefined(patientCat) == false) {
                return sweetAlert("Please Select Payments Categories", "", "error");
            } else if (angular.isDefined(patient) == false) {
                return false;
            }
            var postData = {patientCat: patientCat, visit_id: patient.account_id};
            $http.post('/api/changePatientCategory', postData).then(function (data) {
                $scope.patientCategories = {};
                return sweetAlert("Patient Payment Categories were succesfully Changed", "", "success");

            });
        };


        $scope.searchByMobile = function (mob_num) {
            if (angular.isDefined(mob_num) == false) {
                return sweetAlert("Please enter Mobile number", "", "error");
            }
            var postData = {mob_num: mob_num};
            $http.post('/api/advancedSearch', postData).then(function (data) {
                $scope.regInfo = data.data;
            });
        };

        $scope.openDialog = function (selectedPatient, search_preferrence) {

            if (angular.isDefined(selectedPatient) == true) {
                SearchPatients.PatientRegistrationStatus(selectedPatient)
                    .then(function (response) {
                        if (response.data.data[0].occupation_id != null) {

                            $mdDialog.show({
                                controller: function ($scope) {
                                    $scope.patientData = response.data.data;
                                    $http.get('/api/searchPatientCategory/' + facility_id).then(function (data) {
                                        $scope.patientCategory = data.data;
                                    });

                                    $scope.showClinicLists = function (patient, encounter) {

                                        if (angular.isDefined(encounter) == false) {
                                            return sweetAlert("Please Type the Payment Category", "", "error");
                                        } else if (angular.isDefined(encounter.payment_category) == false) {
                                            return sweetAlert("Please Type the Payment Category", "", "error");
                                        } else if (angular.isDefined(encounter.payment_services) == false) {
                                            return sweetAlert("Please Select Service", "", "error");
                                        } else {
                                            $mdDialog.show({
                                                controller: function ($scope) {
                                                    $scope.patientData = patient;
                                                    $scope.encounter = encounter;
                                                    $scope.cancel = function () {
                                                        $mdDialog.hide();
                                                    };


                                                    $scope.enterEncounter = function (patientData, encounter, dept_id) {
                                                        var patient_category = encounter.payment_category.patient_category;
                                                        var service_category = encounter.payment_services;
                                                        var service_id = encounter.payment_services.service_id;
                                                        var price_id = encounter.payment_services.price_id;
                                                        var item_type_id = encounter.payment_services.item_type_id;
                                                        var patient_id = patientData.id;
                                                        var facility_id = $rootScope.currentUser.facility_id;
                                                        var user_id = $rootScope.currentUser.id;
                                                        var payment_filter = encounter.payment_services.patient_category_id;
                                                        var card_number = encounter.card_number;

                                                        var bill_category_id = encounter.payment_services.patient_category_id;
                                                        var main_category_id = encounter.payment_services.patient_main_category_id;

                                                        var enterEncounters = {
                                                            'dept_id': dept_id,
                                                            'time_created': patientData.created_at,
                                                            'gender': patientData.gender,
                                                            'dob': patientData.dob,
                                                            'facility_code': patientData.facility_code,
                                                            'payment_filter': payment_filter,
                                                            'item_type_id': item_type_id,
                                                            'patient_category': patient_category,
                                                            'main_category_id': main_category_id,
                                                            'bill_id': bill_category_id,
                                                            'service_category': service_category,
                                                            'service_id': service_id,
                                                            'price_id': price_id,
                                                            'card_number': card_number,
                                                            'patient_id': patient_id,
                                                            'facility_id': facility_id,
                                                            'user_id': user_id
                                                        };
                                                        $http.post('/api/enterEncounter', enterEncounters).then(function (data) {
                                                            $scope.registrationReport = data.data;
                                                            if (data.data.status == 0) {

                                                                return sweetAlert(data.data.data, "", "error");
                                                            } else {
                                                                $scope.cancel();
                                                                $mdDialog.show({

                                                                    controller: function ($scope) {
                                                                        $scope.patientData = patientData;
                                                                        $http.get('/api/getUsermenu/' + user_id).then(function (cardTitle) {
                                                                            $scope.cardTitle = cardTitle.data[0];

                                                                        });

                                                                        $scope.cancel = function () {
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
                                                    $http.get('gothomis/api/getClinic').then(function (data) {
                                                        $scope.departments = data.data;
                                                    });
                                                },
                                                templateUrl: '/scripts/modules/registrations/views/clinic-template.html',
                                                parent: angular.element(document.body),
                                                clickOutsideToClose: false,
                                                fullscreen: $scope.customFullscreen // Only for -xs, -sm breakpoints.
                                            });

                                        }
                                    };


                                    $scope.getPricedItems = function (patient_category_selected) {
                                        $scope.showCardNumber = false;
                                        if (patient_category_selected == 'NHIF') {
                                            $scope.showCardNumber = true;
                                        } else {
                                            $scope.showCardNumber = false;
                                        }

                                        var postData = {
                                            facility_id: facility_id,
                                            patient_category: patient_category_selected
                                        };
                                        $http.post('/api/getPricedItems', postData).then(function (data) {
                                            $scope.services = data.data;
                                        });

                                    };

                                    // exemptions======================================================


                                    $scope.exemption_type_list = function () {
                                        $http.get('/api/exemption-type-list/' + user_id).then(function (data) {
                                            $scope.exemption_types = data.data;


                                        });
                                    }();

                                    $http.get('/api/get-exemption-services/' + facility_id).then(function (data) {
                                        $scope.exemption_services = data.data;
                                    });

                                    $scope.exemption_registration = function (exempt, selectedPatient) {

                                        var reason_for_revoke = "..";
                                        if (selectedPatient.id == undefined) {
                                            var patient = selectedPatient.patient_id;
                                        } else {
                                            var patient = selectedPatient.id;
                                        }


                                        if (selectedPatient.patient_id == undefined && selectedPatient.id == undefined) {
                                            swal(
                                                'Feedback..',
                                                'Please Select Client from a Search Box above...',
                                                'error'
                                            )

                                        } else if (exempt == undefined) {
                                            swal(
                                                'Feedback..',
                                                'Please Fill all required fields ',
                                                'error'
                                            )
                                        } else if (exempt.exemption_type_id == undefined) {
                                            swal(
                                                'Feedback..',
                                                'Please Select exemption Category ',
                                                'error'
                                            )
                                        } else if (exempt.exemption_reason == undefined) {
                                            swal(
                                                'Feedback..',
                                                'Please Fill  Reason(s) for This exemption ',
                                                'error'
                                            )
                                        } else {


                                            var status_id = 2;
                                            var change = false;
                                            var price = exempt;
                                            var item_id = exempt.service.id;
                                            var item_price_id = exempt.service.price_id;
                                            var item_type_id = exempt.service.item_type_id;
                                            var patient = patient;
                                            var exemption_type_id = exempt.exemption_type_id.id;
                                            var main_category_id = exempt.exemption_type_id.pay_cat_id;
                                            var user_id = $rootScope.currentUser.id;
                                            var facility_id = $rootScope.currentUser.facility_id;
                                            var patient_id = patient;
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
                                            formdata.append('consultation_id', '');
                                            var request = {
                                                method: 'POST',
                                                url: '/api/' + 'patient-exemption',
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

                                                } else {
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
                                    $scope.cancel = function () {
                                        $scope.selectedPatient = null;
                                        $mdDialog.hide();
                                    };
                                },
                                templateUrl: '/scripts/modules/registrations/views/encounterModal.html',
                                parent: angular.element(document.body),
                                clickOutsideToClose: false,
                                fullscreen: $scope.customFullscreen // Only for -xs, -sm breakpoints.
                            });
                        } else {

                            $mdDialog.show({
                                controller: function ($scope) {
                                    $scope.selectedPatient = selectedPatient;
                                    $scope.cancel = function () {
                                        $scope.selectedPatient = null;
                                        $mdDialog.hide();
                                    };

                                    $scope.showSearchMarital = function () {
                                        $http.get('/api/getMaritalStatus').then(function (data) {
                                            $scope.maritals = data.data;
                                        });
                                    }();

                                    var occupation = [];
                                    var country = [];
                                    var relationships = [];
                                    $scope.showSearchOccupation = function (text) {
                                        $http.get('/api/getOccupation/' + text).then(function (data) {
                                            occupation = data.data;
                                        });
                                        return occupation;
                                    };

                                    $scope.getCountry = function (text) {
                                        $http.get('/api/getCountry/' + text).then(function (data) {
                                            country = data.data;
                                        });
                                        return country;
                                    };

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
                                        residence_id = $scope.residence.residence_id;
                                    };


                                    //SAVE COMPLETE REGISTRATION DATA
                                    $scope.completeRegistration = function (patient, others, residence) {

                                        console.log(residence)
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
                                        } else if (angular.isDefined(residence) == false) {
                                            return sweetAlert("Please Enter Next of kin Residences and choose from the suggestions", "", "error");
                                        } else if (angular.isDefined(others.next_of_kin_name) == false) {
                                            return sweetAlert("Please Enter Next of kin Name", "", "error");
                                        } else if (angular.isDefined(others.relationship) == false) {
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
                                        var next_of_kin_resedence_id = residence_id;
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


                                        $http.post('/api/complete_registration', complete_registration).then(function (response) {
                                            if (response.data.status == 0) {
                                                return sweetAlert(response.data.data, "", "error");
                                            }
                                            console.log(response.data);
                                            //OPEN DIALOG FOR RE-ATTENDANCE
                                            $mdDialog.show({
                                                controller: function ($scope) {
                                                    $scope.patientData = response.data.data;
                                                    $scope.cancel = function () {
                                                        $mdDialog.hide();
                                                    };
                                                    $http.get('/api/searchPatientCategory/' + facility_id).then(function (data) {
                                                        $scope.patientCategory = data.data;
                                                    });

                                                    $scope.getPricedItems = function (patient_category_selected) {
                                                        $scope.showCardNumber = false;
                                                        if (patient_category_selected == 'NHIF') {
                                                            $scope.showCardNumber = true;
                                                        } else {
                                                            $scope.showCardNumber = false;
                                                        }
                                                        var postData = {
                                                            facility_id: facility_id,
                                                            patient_category: patient_category_selected
                                                        };
                                                        $http.post('/api/getPricedItems', postData).then(function (data) {
                                                            $scope.services = data.data;
                                                        });

                                                    };

                                                    $scope.showClinicLists = function (patient, encounter) {

                                                        if (angular.isDefined(encounter) == false) {
                                                            return sweetAlert("Please Type the Payment Category", "", "error");
                                                        } else if (angular.isDefined(encounter.payment_category) == false) {
                                                            return sweetAlert("Please Type the Payment Category", "", "error");
                                                        } else if (angular.isDefined(encounter.payment_services) == false) {
                                                            return sweetAlert("Please Select Service", "", "error");
                                                        } else {
                                                            $mdDialog.show({
                                                                controller: function ($scope) {
                                                                    $scope.patientData = patient;
                                                                    $scope.encounter = encounter;
                                                                    console.log($scope.encounter);
                                                                    $scope.cancel = function () {
                                                                        $mdDialog.hide();
                                                                    };
                                                                    // exemptions======================================================


                                                                    $scope.exemption_type_list = function () {
                                                                        $http.get('/api/exemption-type-list/' + user_id).then(function (data) {
                                                                            $scope.exemption_types = data.data;


                                                                        });
                                                                    }();

                                                                    $http.get('/api/get-exemption-services/' + facility_id).then(function (data) {
                                                                        $scope.exemption_services = data.data;
                                                                    });

                                                                    $scope.exemption_registration = function (exempt, selectedPatient) {

                                                                        var reason_for_revoke = "..";
                                                                        if (selectedPatient.id == undefined) {
                                                                            var patient = selectedPatient.patient_id;
                                                                        } else {
                                                                            var patient = selectedPatient.id;
                                                                        }


                                                                        if (selectedPatient.patient_id == undefined && selectedPatient.id == undefined) {
                                                                            swal(
                                                                                'Feedback..',
                                                                                'Please Select Client from a Search Box above...',
                                                                                'error'
                                                                            )

                                                                        } else if (exempt == undefined) {
                                                                            swal(
                                                                                'Feedback..',
                                                                                'Please Fill all required fields ',
                                                                                'error'
                                                                            )
                                                                        } else if (exempt.exemption_type_id == undefined) {
                                                                            swal(
                                                                                'Feedback..',
                                                                                'Please Select exemption Category ',
                                                                                'error'
                                                                            )
                                                                        } else if (exempt.exemption_reason == undefined) {
                                                                            swal(
                                                                                'Feedback..',
                                                                                'Please Fill  Reason(s) for This exemption ',
                                                                                'error'
                                                                            )
                                                                        } else {


                                                                            var status_id = 2;
                                                                            var change = false;
                                                                            var price = exempt;
                                                                            var item_id = exempt.service.id;
                                                                            var item_price_id = exempt.service.price_id;
                                                                            var item_type_id = exempt.service.item_type_id;
                                                                            var patient = patient;
                                                                            var exemption_type_id = exempt.exemption_type_id.id;
                                                                            var main_category_id = exempt.exemption_type_id.pay_cat_id;
                                                                            var user_id = $rootScope.currentUser.id;
                                                                            var facility_id = $rootScope.currentUser.facility_id;
                                                                            var patient_id = patient;
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
                                                                            formdata.append('consultation_id', '');
                                                                            var request = {
                                                                                method: 'POST',
                                                                                url: '/api/' + 'patient-exemption',
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

                                                                                } else {
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

                                                                    $scope.enterEncounter = function (patientData, encounter, dept_id) {
                                                                        console.log(encounter);
                                                                        var patient_category = encounter.payment_category.patient_category;
                                                                        var service_category = encounter.payment_services;
                                                                        var card_number = encounter.card_number;
                                                                        var service_id = encounter.payment_services.service_id;
                                                                        var price_id = encounter.payment_services.price_id;
                                                                        var item_type_id = encounter.payment_services.item_type_id;
                                                                        var patient_id = patientData.id;
                                                                        var facility_id = $rootScope.currentUser.facility_id;
                                                                        var user_id = $rootScope.currentUser.id;
                                                                        var payment_filter = encounter.payment_services.patient_category_id;

                                                                        var bill_category_id = encounter.payment_services.patient_category_id;
                                                                        var main_category_id = encounter.payment_services.patient_main_category_id;

                                                                        var enterEncounters = {
                                                                            'dept_id': dept_id,
                                                                            'time_created': patientData.created_at,
                                                                            'gender': patientData.dob,
                                                                            'card_number': card_number,
                                                                            'dob': patientData.dob,
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
                                                                            'user_id': user_id
                                                                        };
                                                                        $http.post('/api/enterEncounter', enterEncounters).then(function (data) {
                                                                            $scope.registrationReport = data.data;
                                                                            if (data.data.status == 0) {
                                                                                return sweetAlert(data.data.data, "", "error");
                                                                            } else {
                                                                                $scope.cancel();
                                                                                $mdDialog.show({
                                                                                    controller: function ($scope) {
                                                                                        $scope.patientData = patientData;
                                                                                        $http.get('/api/getUsermenu/' + user_id).then(function (cardTitle) {
                                                                                            $scope.cardTitle = cardTitle.data[0];
                                                                                        });

                                                                                        $scope.cancel = function () {
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
                                                                    $http.get('gothomis/api/getClinic').then(function (data) {
                                                                        $scope.departments = data.data;
                                                                    });

                                                                },
                                                                templateUrl: '/scripts/modules/registrations/views/clinic-template.html',
                                                                parent: angular.element(document.body),
                                                                clickOutsideToClose: false,
                                                                fullscreen: $scope.customFullscreen // Only for -xs, -sm breakpoints.
                                                            });
                                                        }
                                                    };
                                                },
                                                templateUrl: '/scripts/modules/registrations/views/encounterModal.html',
                                                parent: angular.element(document.body),
                                                clickOutsideToClose: false,
                                                fullscreen: $scope.customFullscreen // Only for -xs, -sm breakpoints.
                                            });


                                        });


                                    };


                                },
                                templateUrl: '/scripts/modules/registrations/views/completeRegistrationModal.html',
                                parent: angular.element(document.body),
                                clickOutsideToClose: false,
                                fullscreen: $scope.customFullscreen // Only for -xs, -sm breakpoints.
                            });

                        }
                    });
            }
        };


        $scope.showSearchMarital = function (searchKey) {
            $http.get('/api/getMaritalStatus').then(function (data) {
                $scope.maritals = data.data;
            });
        }();

        $scope.showNextForm = function (patient) {
            $scope.patient = patient;
            $scope.firstFormShow = false;
            $scope.secondFormShow = true;

        };

        $scope.getUsers = function () {
            User.get($scope.query, function (response) {
                console.log('all users', response);
                $scope.user = response.data;
            });
        };

        $scope.getFacilities = function () {
            Facility.getAll()
                .then(function (response) {
                    console.log('all faciltieis', response);
                    $scope.facilities = response.data;
                });
        };

        $scope.searchRemotely = function (searchPatientKey) {
            SearchPatients.searchRemotely(searchPatientKey)
                .then(function (response) {

                    $mdDialog.show({
                        controller: function ($scope) {
                            $scope.patientData = response.data[0];

                            $scope.cancel = function () {
                                $mdDialog.hide();
                            };
                        },
                        templateUrl: '/scripts/modules/registrations/views/remote-registered.html',
                        parent: angular.element(document.body),

                        clickOutsideToClose: false,
                        fullscreen: $scope.customFullscreen // Only for -xs, -sm breakpoints.
                    });

                });

        };

        $scope.quickSearchPatients = function (searchPatientKey, searchPrefference) {
            if (angular.isDefined(searchPatientKey) == false) {
                return;
            }
            SearchPatients.quickSearchPatients(searchPatientKey, searchPrefference)
                .then(function (response) {
                    if (searchPrefference == 1) {
                        patientLists = response.data;

                    } else {
                        patientLists = response.data.Message;
                        //console.log(patientLists);
                    }

                });
            return patientLists;
        };

        $scope.getProffesions = function () {
            Professional.getAll()
                .then(function (response) {
                    console.log('all professionals', response);
                    $scope.professionals = response.data;
                });
        };
        var occupation = [];
        var country = [];
        var relationships = [];
        $scope.showSearchOccupation = function (text) {
            $http.get('/api/getOccupation/' + text).then(function (data) {
                occupation = data.data;
            });
            return occupation;
        };

        $scope.getCountry = function (text) {
            $http.get('/api/getCountry/' + text).then(function (data) {
                country = data.data;
            });
            return country;
        };

        $scope.getRelationships = function () {
            $http.get('/api/getRelationships').then(function (data) {
                $scope.relationships = data.data;
            });
        }();

        $scope.patientQuickRegistration = function (patient, residence) {
            if (angular.isDefined(patient) == false) {
                return sweetAlert("Please Enter Patient Details", "", "error");
            }
            var first_name = patient.first_name;
            var middle_name = patient.middle_name;
            var last_name = patient.last_name;
            var gender = patient.gender;
            var mobile_number = patient.mobile_number;
            var marital_id = patient.marital;
            if (angular.isDefined(patient.occupation) == true || angular.isDefined(patient.country) == true) {
                var occupation_id = patient.occupation.id;
                var country_id = patient.country.id;
            }

            var dob = moment(patient.dob).format("YYYY-MM-DD");
            if (!patient.tribe) {
                return sweetAlert("Patient's tribe is required, please fill in.", "", "error");
            }
            var tribe = patient.tribe.id;
            patientDetails = {
                "tribe": tribe,
                "first_name": first_name,
                "middle_name": middle_name,
                "last_name": last_name,
                "dob": dob,
                "gender": gender,
                "mobile_number": mobile_number,
                "residence_id": residence_id,
                "facility_id": facility_id,
                "user_id": user_id,
                marital_id: marital_id,
                country_id: country_id,
                occupation_id: occupation_id
            };
            Patient.save(patientDetails, function (response) {
                if (response.status == 200) {
                    $scope.patient = {};
                    $mdDialog.show({
                        controller: function ($scope) {
                            $scope.patientData = response.data;
                            $scope.cancel = function () {
                                $mdDialog.hide();
                            };
                            $http.get('/api/searchPatientCategory/' + facility_id).then(function (data) {
                                $scope.patientCategory = data.data;
                            });

                            $scope.getPricedItems = function (patient_category_selected) {
                                $scope.showCardNumber = false;
                                if (patient_category_selected == 'NHIF') {
                                    $scope.showCardNumber = true;
                                } else {
                                    $scope.showCardNumber = false;
                                }
                                var postData = {facility_id: facility_id, patient_category: patient_category_selected};
                                $http.post('/api/getPricedItems', postData).then(function (data) {
                                    $scope.services = data.data;
                                });

                            };

                            $scope.showClinicLists = function (patient, encounter) {

                                if (angular.isDefined(encounter) == false) {
                                    return sweetAlert("Please Type the Payment Category", "", "error");
                                } else if (angular.isDefined(encounter.payment_category) == false) {
                                    return sweetAlert("Please Type the Payment Category", "", "error");
                                } else if (angular.isDefined(encounter.payment_services) == false) {
                                    return sweetAlert("Please Select Service", "", "error");
                                } else {
                                    $mdDialog.show({
                                        controller: function ($scope) {
                                            $scope.patientData = patient;
                                            $scope.encounter = encounter;
                                            $scope.cancel = function () {
                                                $mdDialog.hide();
                                            };

                                            $scope.exemption_type_list = function () {
                                                $http.get('/api/exemption-type-list/' + user_id).then(function (data) {
                                                    $scope.exemption_types = data.data;
                                                });
                                            }();

                                            $http.get('/api/get-exemption-services/' + facility_id).then(function (data) {
                                                $scope.exemption_services = data.data;
                                            });

                                            $scope.exemption_registration = function (exempt, selectedPatient) {

                                                var reason_for_revoke = "..";
                                                if (selectedPatient.id == undefined) {
                                                    var patient = selectedPatient.patient_id;
                                                } else {
                                                    var patient = selectedPatient.id;
                                                }


                                                if (selectedPatient.patient_id == undefined && selectedPatient.id == undefined) {
                                                    swal(
                                                        'Feedback..',
                                                        'Please Select Client from a Search Box above...',
                                                        'error'
                                                    )

                                                } else if (exempt == undefined) {
                                                    swal(
                                                        'Feedback..',
                                                        'Please Fill all required fields ',
                                                        'error'
                                                    )
                                                } else if (exempt.exemption_type_id == undefined) {
                                                    swal(
                                                        'Feedback..',
                                                        'Please Select exemption Category ',
                                                        'error'
                                                    )
                                                } else if (exempt.exemption_reason == undefined) {
                                                    swal(
                                                        'Feedback..',
                                                        'Please Fill  Reason(s) for This exemption ',
                                                        'error'
                                                    )
                                                } else {


                                                    var status_id = 2;
                                                    var change = false;
                                                    var price = exempt;
                                                    var item_id = exempt.service.id;
                                                    var item_price_id = exempt.service.price_id;
                                                    var item_type_id = exempt.service.item_type_id;
                                                    var patient = patient;
                                                    var exemption_type_id = exempt.exemption_type_id.id;
                                                    var main_category_id = exempt.exemption_type_id.pay_cat_id;
                                                    var user_id = $rootScope.currentUser.id;
                                                    var facility_id = $rootScope.currentUser.facility_id;
                                                    var patient_id = patient;
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
                                                    formdata.append('consultation_id', '');
                                                    var request = {
                                                        method: 'POST',
                                                        url: '/api/' + 'patient-exemption',
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

                                                        } else {
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

                                            $scope.enterEncounter = function (patientData, encounter, dept_id) {
                                                var patient_category = encounter.payment_category.patient_category;
                                                var service_category = encounter.payment_services;
                                                var service_id = encounter.payment_services.service_id;
                                                var price_id = encounter.payment_services.price_id;
                                                var item_type_id = encounter.payment_services.item_type_id;
                                                var patient_id = patientData.id;
                                                var facility_id = $rootScope.currentUser.facility_id;
                                                var user_id = $rootScope.currentUser.id;
                                                var payment_filter = encounter.payment_services.patient_category_id;
                                                var card_number = encounter.card_number;
                                                var bill_category_id = encounter.payment_services.patient_category_id;
                                                var main_category_id = encounter.payment_services.patient_main_category_id;

                                                var enterEncounters = {
                                                    'dept_id': dept_id,
                                                    'time_created': patientData.created_at,
                                                    'gender': patientData.dob,
                                                    'dob': patientData.dob,
                                                    'payment_filter': payment_filter,
                                                    'item_type_id': item_type_id,
                                                    'patient_category': patient_category,
                                                    'main_category_id': main_category_id,
                                                    'bill_id': bill_category_id,
                                                    "card_number": card_number,
                                                    'service_category': service_category,
                                                    'service_id': service_id,
                                                    'price_id': price_id,
                                                    'patient_id': patient_id,
                                                    'facility_id': facility_id,
                                                    'user_id': user_id
                                                };
                                                $http.post('/api/enterEncounter', enterEncounters).then(function (data) {
                                                    $scope.registrationReport = data.data;
                                                    if (data.data.status == 0) {
                                                        return sweetAlert(data.data.data, "", "error");
                                                    } else {
                                                        $scope.cancel();
                                                        $mdDialog.show({
                                                            controller: function ($scope) {
                                                                $scope.patientData = patientData;
                                                                $http.get('/api/getUsermenu/' + user_id).then(function (cardTitle) {
                                                                    $scope.cardTitle = cardTitle.data[0];
                                                                });

                                                                $scope.cancel = function () {
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
                                            $http.get('gothomis/api/getClinic').then(function (data) {
                                                $scope.departments = data.data;
                                            });

                                        },
                                        templateUrl: '/scripts/modules/registrations/views/clinic-template.html',
                                        parent: angular.element(document.body),
                                        clickOutsideToClose: false,
                                        fullscreen: $scope.customFullscreen // Only for -xs, -sm breakpoints.
                                    });
                                }
                            };
                        },
                        templateUrl: '/scripts/modules/registrations/views/encounterModal.html',
                        parent: angular.element(document.body),
                        clickOutsideToClose: false,
                        fullscreen: $scope.customFullscreen // Only for -xs, -sm breakpoints.
                    });
                }
            }, function (response) {
                if (response.status === 409) {
                    console.log(response.status);
                    console.log(patientDetails);
                    $scope.confirmDuplicate(patientDetails);
                } else {
                    $mdDialog.show({
                        controller: function ($scope) {
                            $scope.errors = response.data.errors;
                            $scope.message = response.data.message;
                            $scope.cancel = function () {
                                $mdDialog.hide();
                            };
                        },
                        templateUrl: '/scripts/modules/registrations/views/errorsMessages.html',
                        parent: angular.element(document.body),
                        clickOutsideToClose: false
                    });
                }

            });
        };
        var residence_id = [];
        $scope.selectedResidence = function (residence) {
            console.log(residence);
            $scope.residence = residence;
            residence_id = $scope.residence.residence_id;
        };

        $scope.seachTribes = function (searchKey) {
            $http.post('/api/getTribes', {
                "search": searchKey
            }).then(function (data) {
                tribe = data.data;
            });
            return tribe;
        };

        $scope.getResidence = function (text) {
            return Helper.getResidence(text).then(function (response) {
                return response.data;
            });
        };

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

        $scope.delete = function (e, id) {
            var confirm = $mdDialog.confirm()
                .title('Deleting User')
                .content('The user will be deleted completely')
                .ok('Delete!')
                .cancel('Cancel')
                .targetEvent(e);

            $mdDialog.show(confirm).then(function () {
                User.remove({id: id}, function (response) {
                    if (response.status === 200) {
                        $state.reload();
                        console.log('successful', response);
                    }
                }, function () {
                    $scope.alert = 'Cancelled';
                });
            });
        };

        $scope.confirmDuplicate = function (patientDetails) {
            $mdDialog.show({
                controller: function ($scope) {
                    $scope.patientDetails = patientDetails;
                    $scope.cancel = function () {
                        $mdDialog.hide();
                    };
                    $scope.proceedAnyWay = function (patientDetails) {
                        $http.post('gothomis/api/proceedRegistration', patientDetails)
                            .then(function (response) {

                            });

                    };
                },
                templateUrl: '/scripts/modules/registrations/views/confirmDuplicate.html',
                parent: angular.element(document.body),
                clickOutsideToClose: false
            });
        };

        // Quite an ugly hack to get the tabs to default on the second index
        $scope.tabLoaded = function () {
            setTimeout(function () {
            }, 200);
        };

        $scope.showUpdateDialog = function (id) {
            User.get({id: id}, function (response) {
                $scope.user = response.data;
                $mdDialog.show({
                    ccontroller: PatientController,
                    scope: $scope,
                    preserveScope: true,
                    templateUrl: 'scripts/modules/user/views/edit-user.html',
                    clickOutsideToClose: false,
                    fullscreen: true // Only for -xs, -sm breakpoints.
                });
            });
        };

        $scope.update = function (data) {
            User.update(data, function (response) {
                if (response.status === 200) {
                    $mdDialog.hide();
                    $state.reload();
                } else {
                    $state.reload();
                }
            }, response => {
                $state.reload();
            });
        };
    }
})();