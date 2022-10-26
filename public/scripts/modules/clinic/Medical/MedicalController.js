/**
 * Created by Mazigo Jr on 2017-05-02.
 */

(function () {
    var app = angular.module('authApp');
    app.controller('MedicalController',['$scope','$http','$rootScope','$state','$uibModal','$window','$mdDialog',
        function ($scope,$http,$rootScope,$state,$uibModal,$window,$mdDialog) {

            var user_id = $rootScope.currentUser.id;
            var facility_id = $rootScope.currentUser.facility_id;
            var corpseData = [];
            $scope.showCorpse = function (searchKey) {
                $http.post('/api/getCorpse',{search:searchKey,facility_id:facility_id}).then(function (data) {
                    corpseData = data.data;
                });
                return corpseData;
            }

            $scope.ClinicQueue = function () {
                $http.get('/api/searchClinicpatientQueue/' + facility_id).then(function (data) {
                    $scope.patientData1 = data.data[13];

                });
            }
            $scope.ClinicQueue();
            $scope.getPerformance = function (item) {
                var perfData = {facility_id:facility_id,user_id:user_id,start:item.start,end:item.end};
                $http.post('/api/doctorsPerformance',perfData).then(function (data) {
                    $scope.performanceRange = data.data[0];
                    $scope.performanceThisMonth = data.data[1];
                });
            }
            angular.element(document).ready(function() {
                $http.post('/api/getOpdPatients', {
                    "facility_id": facility_id
                }).then(function(data) {
                    $scope.patientData = data.data;
                });
                $http.post('/api/investigationList', {
                    "facility_id": facility_id
                }).then(function(data) {
                    $scope.patientInvData = data.data;
                });
                $http.post('/api/getCorpseList',{facility_id:facility_id}).then(function (data) {
                    $scope.corpseData = data.data;
                });
            });
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

            var patientInvPatients = [];
            $scope.showSearch2 = function(searchKey) {
                $http.post('/api/getAllInvPatients', {
                    "searchKey": searchKey,
                    "facility_id": facility_id
                }).then(function(data) {
                    patientInvPatients = data.data;
                });
                return patientInvPatients;
            };

            $scope.checkAttendance = function(patient){
                $scope.selectedPatient = patient;
                $http.post('/api/checkPatientAttendance',{patient_id:patient.patient_id,
                    facility_id:facility_id}).then(function(data){
                    if(data.data == 1){
                        $scope.getAttendanceChecker(patient);
                    }else{
                        $scope.getConsultationModal(patient);
                    }
                });
            }
            $scope.getAttendanceChecker = function(patient){
                // inputOptions can be an object or Promise
                var inputOptions = new Promise(function (resolve) {
                    setTimeout(function () {
                        resolve({
                            'new': 'New attendance',
                            'reattendance': 'Reattendance',
                            'NEW': 'Unknown'
                        })
                    },10)
                })

                swal({
                    title: 'MTUHA TALLYING',
                    input: 'radio',
                    inputOptions: inputOptions,
                    inputValidator: function (result) {
                        return new Promise(function (resolve, reject) {
                            if (result) {
                                resolve()
                            } else {
                                reject('Please, respond to the message to automatically tally the MTUHA register')
                            }
                        })
                    }
                }).then(function (result) {
                    $scope.tallyAttendance(result);
                    $scope.getConsultationModal(patient);
                })


            }
            $scope.getConsultationModal = function(item) {
                $scope.item = item;
                $mdDialog.show({
                    controller: function ($scope) {

                        $scope.cancel = function() {
                            $mdDialog.cancel();
                        };

                        var object = $scope.item;
                        $scope.selectedPatient = item;
                        $scope.patientBills = object;
                        var user_id = $rootScope.currentUser.id;
                        var facility_id = $rootScope.currentUser.facility_id;
                        var patient_id = item.patient_id;
                        var account_id = item.account_id;
                        $scope.toto = function() {
                            var total = 0;
                            for (var i = 0; i < $scope.patientBills.length; i++) {
                                total += ($scope.patientBills[i].quantity * $scope.patientBills[i].price - $scope.patientBills[i].discount);
                            }
                            return total;
                        }
                        $http.post('/api/update_referral_Incomming', item).then(function (data) {

                            $scope.ClinicQueue();
                        });
                        $scope.cancelBill = function(item) {
                            $http.post('/api/cancelBillItem', {
                                "id": item.id,
                                "patient_id": item.patient_id,
                                "facility_id": facility_id,
                                "user_id": user_id
                            }).then(function(data) {
								for(var i = 0; i < $scope.patientBills.length; i++)
									if($scope.patientBills[i].id == item.id)
										$scope.patientBills.splice(i);
								if($scope.patientBills.length == 0)
									$scope.cancel();
							});
                        };

                        $scope.oneAtATime = true;
                        $scope.templates = [{
                            name: 'Admission',
                            url: 'admission.html'
                        }, {
                            name: 'Internal Transfer',
                            url: 'internal.html'
                        }, {
                            name: 'External Referral',
                            url: 'referral.html'
                        }, {
                            name: 'Deceased',
                            url: 'deceased.html'
                        }];



                        //get patients who have paid consultation fee/exempted/insurance

                        var patientData = [];

                        $scope.showSearch = function(searchKey) {
                            $http.post('/api/getOpdPatients', {
                                "facility_id": facility_id
                            }).then(function(data) {
                                patientData = data.data;
                            });
                            return patientData;
                        }

                        var patientInvData = [];

                        $scope.showSearch = function(searchKey) {
                            $http.post('/api/getOpdInvPatients', {
                                "facility_id": facility_id
                            }).then(function(data) {
                                patientInvData = data.data;
                            });
                            return patientInvData;
                        }

                        //Previous History

                        angular.element(document).ready(function() {
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

                        });

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

                                "visit_date_id": item.account_id,

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

                                "date_attended": item.date_attended

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
                            });
                        }
                        var patientVitals = [];

                        $scope.getVitalData = function(item, patient) {
                            $http.post('/api/patientVitals', {
                                "patient_id": patient.patient_id,
                                "time_attended": item.time_attended
                            }).then(function(data) {
                                patientVitals = data.data;
                                var object = angular.extend({}, patientVitals, patient);

                                var modalInstance = $uibModal.open({
                                    templateUrl: '/views/modules/clinicalServices/vitalSigns.html',
                                    size: 'lg',
                                    animation: true,
                                    controller: 'admissionModal',
                                    resolve: {
                                        object: function() {
                                            return object;
                                        }
                                    }
                                });
                            });
                        }


                        //chief complaints
                        var chiefComplaints = [];
                        $scope.complaints = function(search) {
                            $http.post('/api/chiefComplaints', {
                                "search": search
                            }).then(function(data) {
                                chiefComplaints = data.data;
                            });
                            return chiefComplaints;
                        }

                        //review of systems

                        var cardiovascular = [];
                        var ENT = [];

                        $scope.showEnt = function(search) {
                            $http.post('/api/reviewOfSystems', {
                                "search": search,
                                "category": "ENT"
                            }).then(function(data) {
                                ENT = data.data;
                            });
                            return ENT;
                        }
                        $scope.showCardio = function(search) {
                            $http.post('/api/reviewOfSystems', {
                                "search": search,
                                "category": "Cardiovascular"
                            }).then(function(data) {
                                cardiovascular = data.data;
                            });
                            return cardiovascular;
                        }

                        var Respiratory = [];

                        $scope.showRespiratory = function(search) {
                            $http.post('/api/reviewOfSystems', {
                                "search": search,
                                "category": "Respiratory"
                            }).then(function(data) {
                                Respiratory = data.data;
                            });
                            return Respiratory;
                        }

                        var gatro = [];

                        $scope.showGastro = function(search) {
                            $http.post('/api/reviewOfSystems', {
                                "search": search,
                                "category": "Gastrointerstinal"
                            }).then(function(data) {
                                gatro = data.data;
                            });
                            return gatro;
                        }

                        var musculo = [];

                        $scope.showMusculo = function(search) {

                            $http.post('/api/reviewOfSystems', {

                                "search": search,

                                "category": "Musculoskeletal"

                            }).then(function(data) {

                                musculo = data.data;

                            });

                            return musculo;

                        }

                        var genito = [];

                        $scope.showGenito = function(search) {

                            $http.post('/api/reviewOfSystems', {

                                "search": search,

                                "category": "Genitourinary"

                            }).then(function(data) {

                                genito = data.data;

                            });

                            return genito;

                        }

                        var cns = [];

                        $scope.showCNS = function(search) {

                            $http.post('/api/reviewOfSystems', {

                                "search": search,

                                "category": "Central Nervous System"

                            }).then(function(data) {

                                cns = data.data;

                            });

                            return cns;

                        }

                        var endo = [];

                        $scope.showEndo = function(search) {

                            $http.post('/api/reviewOfSystems', {

                                "search": search,

                                "category": "Endocrine"

                            }).then(function(data) {

                                endo = data.data;

                            });

                            return endo;

                        }

                        var allergy = [];

                        $scope.showAllergy = function(search) {

                            $http.post('/api/reviewOfSystems', {

                                "search": search,

                                "category": "Allergy"

                            }).then(function(data) {

                                allergy = data.data;

                            });

                            return allergy;

                        }

                        var pastInsp = [];

                        $scope.showInspection = function(search) {

                            $http.post('/api/reviewOfSystems', {

                                "search": search,

                                "category": "Inspection"

                            }).then(function(data) {

                                pastInsp = data.data;

                            });

                            return pastInsp;

                        }

                        var pastPalp = [];

                        $scope.showPalpation = function(search) {

                            $http.post('/api/reviewOfSystems', {

                                "search": search,

                                "category": "Palpation"

                            }).then(function(data) {

                                pastPalp = data.data;

                            });

                            return pastPalp;

                        }

                        var pastPerc = [];

                        $scope.showPercussion = function(search) {

                            $http.post('/api/reviewOfSystems', {

                                "search": search,

                                "category": "Percussion"

                            }).then(function(data) {

                                pastPerc = data.data;

                            });

                            return pastPerc;

                        }

                        var pastAus = [];

                        $scope.showAuscultation = function(search) {

                            $http.post('/api/reviewOfSystems', {

                                "search": search,

                                "category": "Auscultation"

                            }).then(function(data) {

                                pastAus = data.data;

                            });

                            return pastAus;

                        }

                        var diag = [];

                        $scope.showDiagnosis = function(search) {

                            $http.post('/api/getDiagnosis', {

                                "search": search

                            }).then(function(data) {

                                diag = data.data;

                            });

                            return diag;

                        }

                        $scope.allergyChecker = function(item) {

                            $http.post('/api/getAllergy', {

                                "patient_id": item.patient_id

                            }).then(function(data) {

                                swal("This Patient is allergic to " + data.data[0].descriptions, "", "info");

                            });

                        }

                        //Investigations

                        $scope.getSubDepts = function(item) {

                            $http.post('/api/getSubDepts', {

                                "department_id": item

                            }).then(function(data) {

                                $scope.subDepartments = data.data;

                            });

                        }

                        $scope.getTests = function(item, category) {

                            if (angular.isDefined(category) == false) {

                                swal("Please...select Patient first", "", "error");

                                return;

                            }

                            var category_id = category.bill_id;

                            if (category.main_category_id == 3) {

                                category_id = 1;

                            }

                            $http.post('/api/getPanels', {

                                "patient_category_id": category_id,

                                "sub_dept_id": item,

                                "facility_id": facility_id

                            }).then(function(data) {

                                $scope.panels = data.data;

                            });



                            $http.post('/api/getSingleTests', {

                                "patient_category_id": category_id,

                                "sub_dept_id": item,

                                "facility_id": facility_id
                            }).then(function(data) {
                                $scope.singleTests = data.data;
                            });
                            $http.post('/api/getTests', {
                                "patient_category_id": category_id,
                                "sub_dept_id": item,
                                "facility_id": facility_id
                            }).then(function(data) {

                                $scope.labTests = data.data;

                            });

                        }

                        $scope.investigationOrders = [];

                        $scope.unavailableOrders = [];

                         $scope.getsumCHFBill = function () {
                            var sumCHFBill=0;
                            for(var i = 0; i < $scope.investigationOrders.length ; i++) {
                                sumCHFBill -= -($scope.investigationOrders[i].price);
                            }
                            return sumCHFBill;
                        }

                        $scope.orders = function(item, isChecked, patient) {
                            console.log(patient);
                            $scope.totalCHFBils="";
                            var status_id = 1;
                            var sub_category_name = patient.sub_category_name.toLowerCase();

                            var filter = patient.bill_id;
                            if(sub_category_name=='chf'){
                                $http.post('api/chfCheckBills',{patient_id:patient_id,account_id:account_id}).then(function (data) {
                                    $scope.totalCHFBils = data.data[0];
                                    $scope.chf_item = data.data[1][0];
                                    $scope.chf_ceiling = parseInt( data.data[2].original.chf_ceiling);
                                    var using = data.data[2].original.use_chf_setting;
                                    if(using==1){
                                        $scope.BillGenerated= $scope.getsumCHFBill($scope.investigationOrders);
                                    }


                                    var billed=$scope.totalCHFBils;
                                    var currentBill = $scope.BillGenerated;
                                    var ceiling = $scope.chf_ceiling;
                                    var totalBill = currentBill - (- billed);
                                    var difference = totalBill - ceiling;
                                    if (difference >0 && using==1){
                                        console.log('CHF TOP UP '+ difference);
                                        $scope.chf_top_up=difference;
                                        $scope.investigationOrders.push({

                                            "requesting_department_id": 1,

                                            "admission_id": '',

                                            "facility_id": facility_id,

                                            "item_type_id": $scope.chf_item.item_type_id,

                                            "item_price_id": $scope.chf_item.item_price_id,
                                            "price": $scope.chf_item.price,
                                            'quantity':$scope.chf_top_up/$scope.chf_item.price,
                                            "status_id": status_id,

                                            "account_number_id": patient.account_id,

                                            "patient_id": patient.patient_id,

                                            "user_id": user_id,

                                            "item_id": $scope.chf_item.item_id,

                                            "item_name": $scope.chf_item.item_name,

                                            "priority": '',

                                            "clinical_note": '',

                                            "payment_filter": patientCategory

                                        });

                                    }
                                    else{
                                        $scope.investigationOrders.push({

                                            "requesting_department_id": 1,

                                            "admission_id": '',

                                            "facility_id": facility_id,

                                            "item_type_id": item.item_type_id,

                                            "item_price_id": item.item_price_id,
                                            "price": item.price,

                                            "status_id": status_id,

                                            "account_number_id": patient.account_id,
                                            "quantity":1,
                                            "patient_id": patient.patient_id,

                                            "user_id": user_id,

                                            "item_id": item.item_id,

                                            "item_name": item.item_name,

                                            "priority": '',

                                            "clinical_note": '',

                                            "payment_filter": filter

                                        });
                                    }



                                });

                            }


                            if (patient.patient_id == null) {

                                swal("Ooops!! no Patient selected", "Please search and select patient first..");

                                return;

                            }

                            if (isChecked == true) {

                                for (var i = 0; i < $scope.investigationOrders.length; i++)

                                    if ($scope.investigationOrders[i].item_id == item.item_id) {

                                        swal(item.item_name + ' ' + " already in your order list!");

                                        return;

                                    }

                                if (item.on_off == 1) {

                                    if (patient.main_category_id != 1) {

                                        filter = patient.bill_id;

                                    }

                                    $scope.investigationOrders.push({

                                        "requesting_department_id": 1,

                                        "admission_id": '',

                                        "facility_id": facility_id,

                                        "item_type_id": item.item_type_id,

                                        "item_price_id": item.item_price_id,
                                        "price": item.price,

                                        "status_id": status_id,

                                        "account_number_id": patient.account_id,
                                        "quantity":1,
                                        "patient_id": patient.patient_id,

                                        "user_id": user_id,

                                        "item_id": item.item_id,

                                        "item_name": item.item_name,

                                        "priority": '',

                                        "clinical_note": '',

                                        "payment_filter": filter

                                    });


                                } else {

                                    for (var i = 0; i < $scope.unavailableOrders.length; i++)

                                        if ($scope.unavailableOrders[i].item_id == item.item_id) {

                                            swal(item.item_name + ' ' + " already in your order list!");

                                            return;

                                        }



                                    $scope.unavailableOrders.push({

                                        "facility_id": facility_id,

                                        "visit_date_id": patient.account_id,

                                        "patient_id": patient.patient_id,

                                        "user_id": user_id,

                                        "item_id": item.item_id,

                                        "item_name": item.item_name

                                    });

                                    return;

                                }

                            }

                        }
                        $scope.saveInvestigation = function(item) {
                            if(angular.isDefined(item)== false){
                                swal('whoops sorry...','Please,choose priority then click save button','error');
                                return;
                            }
                            if ($scope.investigationOrders == "" && $scope.unavailableOrders == null) {

                                swal("You dont have Items to save!", "Please select Items first!");
                                return;
                            }
                            var details = {"admission_id":'',"patient_id": patient_id,"visit_date_id": account_id,
                                "user_id": user_id,"facility_id": facility_id,"requesting_department_id":1};

                            var invData = {details:details,clinicalData:$scope.investigationOrders,
                                priority:item.priority,clinical_note:item.clinical_note};
                            if ($scope.investigationOrders != "") {
                                $http.post('/api/postInvestigations', invData).then(function(data) {});
                                $scope.investigationOrders = [];
                                $('#clinical_note').val('');
                                $('#priority').val('');
                            }
                            $http.post('/api/postUnavailableInvestigations', $scope.unavailableOrders).then(function(data) {
                            });
                            swal("Investigation order successfully saved!", "", "success");
                            $scope.unavailableOrders = [];
                            $('#clinical_note').val('');
                            $('#priority').val('');
                        }
                        //Investigation results

                        $scope.getLabResults = function(item) {
                            var results = {

                                "patient_id": item.patient_id,

                                "account_id": item.account_id,

                                "dept_id": item.dept_id

                            };

                            $http.post('/api/getInvestigationResults', results).then(function(data) {

                                $scope.labResults = data.data;


                            });

                        }
                        $scope.getRadResults = function(item) {

                            var results = {

                                "patient_id": item.patient_id,

                                "account_id": item.account_id,

                                "dept_id": item.dept_id

                            };

                            $http.post('/api/getInvestigationResults', results).then(function(data) {
                                $scope.radResults = data.data;
                            });

                        }
                        //blood request options start
                        $scope.getVipimo = function (item) {
                            var results = {
                                "patient_id": item.patient_id,
                                "account_id": item.account_id,
                                "dept_id": 2
                            };
                            $http.post('/api/getInvestigationResults', results).then(function(data) {
                                $scope.latestLabResults = data.data;
                            });
                        }
                        $scope.requestBlood = function (item) {
                            var requests = {facility_id:facility_id,patient_id:patient_id,visit_id:account_id,requested_by:user_id,dept_id:1,
                                blood_group:item.blood_group,priority:item.priority,unit_requested:item.unit_requested,request_reason:item.request_reason
                            };
                            $http.post('/api/requestBlood', requests).then(function(data) {
                                var taa=data.data.msg;
                                swal('',taa,'success');
                            });
                        }
                        //blood request options end
                        //document reader starts
                        $scope.getAttachedDocument=function(documentData){
                            $mdDialog.show({
                                controller: function ($scope) {
                                    var sample= ""+ documentData.sample_no;
                                    var uploadedFile="/labresults/"+sample+".pdf";
                                    $scope.selectedPatient =documentData;
                                    $scope.resultsFile=uploadedFile;

                                    $scope.cancelPdf = function () {

                                        $mdDialog.hide();
                                    };
                                },
                                templateUrl: '/views/modules/clinicalServices/images.html',
                                parent: angular.element(document.body),
                                clickOutsideToClose: false,
                                fullscreen: $scope.customFullscreen // Only for -xs, -sm breakpoints.
                            });
                        }
                        //document reader ends
                        //posting from history and examinations

                        $scope.complaintz = [];

                        $scope.addComplaint = function(item,qty,unit, patient) {

                            if (patient.patient_id == null) {

                                swal("Ooops!! no Patient selected", "Please search and select patient first..");
                                return;
                            }
                            for (var i = 0; i < $scope.complaintz.length; i++)
                                if ($scope.complaintz[i].id == item.id) {
                                    swal(item.name + ' ' + "already in your wish list!","","info");
                                    return;
                                }

                            $scope.complaintz.push({

                                "admission_id": '',

                                "patient_id": patient.patient_id,

                                "facility_id": facility_id,

                                "visit_date_id": patient.account_id,

                                "user_id": user_id,

                                "description": item.name,

                                "id": item.id,

                                "duration": qty,
                                "other_complaints": '',

                                "duration_unit": unit,

                                "status": 'Chief Complaints'

                            });
                            $("#complaint").val('');
                            $("#dxn").val('');
                            $("#unit").val('');
                        }

                        $scope.saveComplaints = function(objectData, other_complaints,patient) {
                            var details = {
                                "admission_id": '',
                                "patient_id": patient_id,
                                "visit_date_id": account_id,
                                "user_id": user_id,
                                "facility_id": facility_id,
                            };
                            var varData = {otherData:other_complaints,complaints:objectData,details:details};

                            $http.post('/api/postHistory', varData).then(function(data) {

                            });
                            swal("Complaints  data successfully saved!", "", "success");
                            $("#other_complaints").val('');
                            $scope.complaintz = [];

                        }
                        $scope.saveHpi = function (item,patient) {
                            var hpi = {"admission_id": '',

                                "patient_id": patient.patient_id,

                                "facility_id": facility_id,

                                "visit_date_id": patient.account_id,

                                "user_id": user_id,
                                "hpi":item
                            };
                            $http.post('/api/postHpi',hpi).then(function (data) {

                            });
                            swal("History of presenting illness data successfully saved!", "", "success");
                            $('#hpi').val('');
                        }

                        $scope.rosTemp = [];

                        $scope.reviewENT = function(item, patient) {
                            if (patient.patient_id == null) {

                                swal("Ooops!! no Patient selected", "Please search and select patient first..");

                                return;
                            }
                            $scope.rosTemp.push({

                                "admission_id": '',
                                "patient_id": patient.patient_id,
                                "visit_date_id": patient.account_id,
                                "user_id": user_id,
                                "facility_id": facility_id,
                                "system_id": item.id,
                                "name": item.name,
                                "status": item.category
                            });

                            $("#ent").val('');

                        }
                        $scope.reviewOfSystems = function(item, patient) {
                            if (patient.patient_id == null) {

                                swal("Ooops!! no Patient selected", "Please search and select patient first..");

                                return;
                            }
                            $scope.rosTemp.push({

                                "admission_id": '',
                                "patient_id": patient.patient_id,
                                "visit_date_id": patient.account_id,
                                "user_id": user_id,
                                "facility_id": facility_id,
                                "system_id": item.id,
                                "name": item.name,
                                "status": item.category
                            });

                            $("#cardio").val('');

                        }

                        $scope.reviewOfSystems2 = function(item, patient) {

                            if (patient.patient_id == null) {

                                swal("Ooops!! no Patient selected", "Please search and select patient first..");

                                return;

                            }

                            $scope.rosTemp.push({

                                "admission_id": '',

                                "patient_id": patient.patient_id,

                                "visit_date_id": patient.account_id,

                                "user_id": user_id,

                                "facility_id": facility_id,

                                "system_id": item.id,

                                "name": item.name,

                                "status": item.category

                            });

                            $("#respiratory").val('');

                        }

                        $scope.reviewOfSystems3 = function(item, patient) {

                            if (patient.patient_id == null) {

                                swal("Ooops!! no Patient selected", "Please search and select patient first..");

                                return;

                            }

                            $scope.rosTemp.push({

                                "admission_id": '',

                                "patient_id": patient.patient_id,

                                "visit_date_id": patient.account_id,

                                "user_id": user_id,

                                "facility_id": facility_id,

                                "system_id": item.id,

                                "name": item.name,

                                "status": item.category

                            });

                            $("#gastrointestinal").val('');

                        }

                        $scope.reviewOfSystems4 = function(item, patient) {

                            if (patient.patient_id == null) {

                                swal("Ooops!! no Patient selected", "Please search and select patient first..");

                                return;

                            }

                            $scope.rosTemp.push({

                                "admission_id": '',

                                "patient_id": patient.patient_id,

                                "visit_date_id": patient.account_id,

                                "user_id": user_id,

                                "facility_id": facility_id,

                                "system_id": item.id,

                                "name": item.name,

                                "status": item.category

                            });

                            $("#musculoskeletal").val('');

                        }

                        $scope.reviewOfSystems5 = function(item, patient) {

                            if (patient.patient_id == null) {

                                swal("Ooops!! no Patient selected", "Please search and select patient first..");

                                return;

                            }

                            $scope.rosTemp.push({

                                "admission_id": '',

                                "patient_id": patient.patient_id,

                                "visit_date_id": patient.account_id,

                                "user_id": user_id,

                                "facility_id": facility_id,

                                "system_id": item.id,

                                "name": item.name,

                                "status": item.category

                            });

                            $("#genitourinary").val('');

                        }

                        $scope.reviewOfSystems6 = function(item, patient) {

                            if (patient.patient_id == null) {

                                swal("Ooops!! no Patient selected", "Please search and select patient first..");

                                return;

                            }

                            $scope.rosTemp.push({

                                "admission_id": '',

                                "patient_id": patient.patient_id,

                                "visit_date_id": patient.account_id,

                                "user_id": user_id,

                                "facility_id": facility_id,

                                "system_id": item.id,

                                "name": item.name,

                                "status": item.category

                            });

                            $("#cns").val('');

                        }

                        $scope.reviewOfSystems7 = function(item, patient) {

                            if (patient.patient_id == null) {

                                swal("Ooops!! no Patient selected", "Please search and select patient first..");

                                return;

                            }

                            $scope.rosTemp.push({

                                "admission_id": '',

                                "patient_id": patient.patient_id,

                                "visit_date_id": patient.account_id,

                                "user_id": user_id,

                                "facility_id": facility_id,

                                "system_id": item.id,

                                "name": item.name,

                                "status": item.category

                            });

                            $("#endocrine").val('');

                        }

                        $scope.saveRoS = function(objectData, rosSummary) {
                            var details = {
                                "admission_id": '',
                                "patient_id": patient_id,
                                "visit_date_id": account_id,
                                "user_id": user_id,
                                "facility_id": facility_id,
                            };
                            var varData = {otherData:rosSummary,ros:objectData,details:details};
                            $http.post('/api/postRoS', varData).then(function(data) {

                            });
                            swal("Review of systems data successfully Saved!", "", "success");
                            $scope.rosTemp = [];
                            $('#ent').val('');
                            $('#cardio').val('');
                            $('#respiratory').val('');
                            $('#gastrointestinal').val('');
                            $('#musculoskeletal').val('');
                            $('#genitourinary').val('');
                            $('#cns').val('');
                            $('#endocrine').val('');
                            $('#review_summary').val('');
                        }
                        //Past medical history
                        $scope.pastTemp = [];
                        $scope.pastMedicals = function(item, patient) {
                            if (patient.patient_id == null) {
                                swal("Ooops!! no Patient selected", "Please search and select patient first..");
                                return;
                            }
                            $scope.pastTemp.push({

                                "admission_id": '',

                                "patient_id": patient.patient_id,

                                "visit_date_id": patient.account_id,

                                "user_id": user_id,

                                "facility_id": facility_id,

                                "system_id": item.id,

                                "name": item.name,

                                "status": item.category

                            });

                        }
                        $scope.savePastMedical = function(objectData, other_past_medicals) {

                            var details = {
                                "admission_id": '',
                                "patient_id": patient_id,
                                "visit_date_id": account_id,
                                "user_id": user_id,
                                "facility_id": facility_id,
                            };

                            var varData = {otherData:other_past_medicals,allergy:objectData,details:details};
                            $http.post('/api/postPastMed',varData).then(function(data) {

                            });
                            swal("Past medical history data successfully Saved!", "", "success");
                            $('#surgeries').val('');
                            $('#admissions').val('');
                            $('#transfusion').val('');
                            $('#immunisation').val('');
                            $('#selectedAllergy').val('');
                            $scope.pastTemp = [];
                        }

                        $scope.saveBirthHistory = function(item, patient) {

                            if (patient.patient_id == null) {

                                swal("Ooops!! no Patient selected", "Please search and select patient first..");

                                return;

                            }

                            var child = {

                                "admission_id": '',

                                "patient_id": patient.patient_id,

                                "visit_date_id": patient.account_id,

                                "facility_id": facility_id,

                                "user_id": user_id,

                                "antenatal": item.antenatal,

                                "natal": item.natal,

                                "post_natal": item.post_natal,

                                "nutrition": item.nutrition,

                                "growth": item.growth,

                                "development": item.development

                            };

                            $http.post('/api/birthHistory', child).then(function(data) {

                                swal("Birth history data Successfully Saved!", "", "success");

                            });

                            $("#antenatal").val('');

                            $("#natal").val('');

                            $("#post_natal").val('');

                            $("#nutrition").val('');

                            $("#growth").val('');

                            $("#development").val('');

                        }

                        $scope.saveObsGyn = function(item, patient) {

                            if (patient.patient_id == null) {

                                swal("Ooops!! no Patient selected", "Please search and select patient first..");

                                return;

                            }

                            var obs = {

                                "admission_id": '',

                                "patient_id": patient.patient_id,

                                "visit_date_id": patient.account_id,

                                "facility_id": facility_id,

                                "user_id": user_id,

                                "menarche": item.menarche,

                                "menopause": item.menopause,

                                "menstrual_cycles": item.menstrual_cycles,

                                "pad_changes": item.pad_changes,

                                "recurrent_menstruation": item.recurrent_menstruation,

                                "contraceptives": item.contraceptives,

                                "pregnancy": item.pregnancy,

                                "lnmp": item.lnmp,

                                "gravidity": item.gravidity,

                                "parity": item.parity,

                                "living_children": item.living_children

                            };

                            $http.post('/api/postObs', obs).then(function(data) {

                                swal("Obstetrics and gynaecological data Successfully Saved!", "", "success");

                            });

                            $("#menarche").val('');

                            $("#menopause").val('');

                            $("#menstrual_cycles").val('');

                            $("#pads").val('');

                            $("#recurrent_menstruation").val('');

                            $("#contraceptives").val('');

                            $("#pregnancy").val('');

                            $("#lnmp").val('');

                            $("#gravidity").val('');

                            $("#parity").val('');

                            $("#living_children").val('');

                        }

                        //Physical Examinations

                        $scope.removeFromSelection = function(item, objectdata) {

                            var indexremoveobject = objectdata.indexOf(item);

                            objectdata.splice(indexremoveobject, 1);

                        }

                        $scope.physicalMusculoskeletal = [];

                        $scope.physicalRespiratory = [];

                        $scope.physicalCardiovascular = [];

                        $scope.physicalGastrointestinal = [];

                        $scope.physicalGenitourinary = [];

                        $scope.physicalCNS = [];

                        $scope.physicalEndocrine = [];



                        $scope.physicalMusculo = function(item, patient, system) {

                            if (patient.patient_id == null) {

                                swal("Ooops!! no Patient selected", "Please search and select patient first..");

                                return;

                            }

                            $scope.physicalMusculoskeletal.push({

                                "admission_id": '',

                                "patient_id": patient.patient_id,

                                "visit_date_id": patient.account_id,

                                "user_id": user_id,

                                "facility_id": facility_id,

                                "system": system,

                                "category": item.category,

                                "observation": item.name

                            });

                            $("#musc_inspect").val('');

                            $("#musc_palpate").val('');

                            $("#musc_percu").val('');

                            $("#musc_ausc").val('');

                        }

                        $scope.physicalResp = function(item, patient, system) {

                            if (patient.patient_id == null) {

                                swal("Ooops!! no Patient selected", "Please search and select patient first..");

                                return;

                            }

                            $scope.physicalRespiratory.push({

                                "admission_id": '',

                                "patient_id": patient.patient_id,

                                "visit_date_id": patient.account_id,

                                "user_id": user_id,

                                "facility_id": facility_id,

                                "system": system,

                                "category": item.category,

                                "observation": item.name

                            });

                            $("#resp_inspect").val('');

                            $("#resp_palpate").val('');

                            $("#resp_percus").val('');

                            $("#resp_aus").val('');

                        }

                        $scope.physicalCardio = function(item, patient, system) {

                            if (patient.patient_id == null) {

                                swal("Ooops!! no Patient selected", "Please search and select patient first..");

                                return;

                            }

                            $scope.physicalCardiovascular.push({

                                "admission_id": '',

                                "patient_id": patient.patient_id,

                                "visit_date_id": patient.account_id,

                                "user_id": user_id,

                                "facility_id": facility_id,

                                "system": system,

                                "category": item.category,

                                "observation": item.name

                            });

                            $("#cardio_inspect").val('');

                            $("#cardio_palpate").val('');

                            $("#cardio_percus").val('');

                            $("#cardio_aus").val('');

                        }

                        $scope.physicalGastro = function(item, patient, system) {

                            if (patient.patient_id == null) {

                                swal("Ooops!! no Patient selected", "Please search and select patient first..");

                                return;

                            }

                            $scope.physicalGastrointestinal.push({

                                "admission_id": '',

                                "patient_id": patient.patient_id,

                                "visit_date_id": patient.account_id,

                                "user_id": user_id,

                                "facility_id": facility_id,

                                "system": system,

                                "category": item.category,

                                "observation": item.name

                            });

                            $("#gastro_inspect").val('');

                            $("#gastro_palpate").val('');

                            $("#gastro_percus").val('');

                            $("#gastro_aus").val('');

                        }

                        $scope.physicalGenito = function(item, patient, system) {

                            if (patient.patient_id == null) {

                                swal("Ooops!! no Patient selected", "Please search and select patient first..");

                                return;

                            }

                            $scope.physicalGenitourinary.push({

                                "admission_id": '',

                                "patient_id": patient.patient_id,

                                "visit_date_id": patient.account_id,

                                "user_id": user_id,

                                "facility_id": facility_id,

                                "system": system,

                                "category": item.category,

                                "observation": item.name

                            });

                            $("#genito_inspect").val('');

                            $("#genito_palpate").val('');

                            $("#genito_percus").val('');

                            $("#genito_aus").val('');

                        }

                        $scope.physicalCns = function(item, patient, system) {

                            if (patient.patient_id == null) {

                                swal("Ooops!! no Patient selected", "Please search and select patient first..");

                                return;

                            }

                            $scope.physicalCNS.push({

                                "admission_id": '',

                                "patient_id": patient.patient_id,

                                "visit_date_id": patient.account_id,

                                "user_id": user_id,

                                "facility_id": facility_id,

                                "system": system,

                                "category": item.category,

                                "observation": item.name

                            });

                            $("#cns_inspect").val('');

                            $("#cns_palpate").val('');

                            $("#cns_percus").val('');

                            $("#cns_aus").val('');

                        }

                        $scope.physicalEndo = function(item, patient, system) {

                            if (patient.patient_id == null) {

                                swal("Ooops!! no Patient selected", "Please search and select patient first..");

                                return;

                            }

                            $scope.physicalEndocrine.push({

                                "admission_id": '',

                                "patient_id": patient.patient_id,

                                "visit_date_id": patient.account_id,

                                "user_id": user_id,

                                "facility_id": facility_id,

                                "system": system,

                                "category": item.category,

                                "observation": item.name

                            });

                            $("#endo_inspect").val('');

                            $("#endo_palpate").val('');

                            $("#endo_percus").val('');

                            $("#endo_aus").val('');

                        }

                        $scope.savePhysicalExamination = function(objectData) {

                            if (objectData == "") {
                                swal("Oops Data not saved!", "Please search and select items then click 'Save' button to save data..");

                                return;
                            }
                            $http.post('/api/postPhysical', objectData).then(function(data) {

                            });

                            swal(objectData[0].system + '  ' + "system data successfully Saved!", "", "success");

                            $scope.physicalMusculoskeletal = [];

                            $scope.physicalRespiratory = [];

                            $scope.physicalCardiovascular = [];

                            $scope.physicalGastrointestinal = [];

                            $scope.physicalGenitourinary = [];

                            $scope.physicalCNS = [];

                            $scope.physicalEndocrine = [];

                        }

                        $scope.saveLocalExams = function(patient, examData) {

                            if (examData == null) {
                                swal('Please write examination for this patient first', '', 'error');
                                return;
                            }
                            var local_examz = {

                                "admission_id": '',

                                "patient_id": patient.patient_id,

                                "visit_date_id": patient.account_id,

                                "user_id": user_id,

                                "facility_id": facility_id,

                                "local_examination": examData

                            }

                            $http.post('/api/postLocalPhysical', local_examz).then(function(data) {});



                            swal('Local Examination', 'data for this patient saved', 'success');

                            $('#local_examination').val('');

                        }
                        $scope.saveGenExams = function(patient, examData) {
                            if (examData == null) {
                                swal('Please write examination for this patient first', '', 'error');
                                return;
                            }
                            var gen_examz = {
                                "admission_id": '',
                                "patient_id": patient.patient_id,
                                "visit_date_id": patient.account_id,
                                "user_id": user_id,
                                "facility_id": facility_id,
                                "gen_examination": examData
                            }

                            $http.post('/api/postGenPhysical', gen_examz).then(function(data) {});

                            swal('General Examination', 'data for this patient saved', 'success');

                            $('#gen_examination').val('');

                        }

                        //family and social history

                        $scope.saveSocialCommunity = function(item, patient) {

                            if (patient.patient_id == null) {

                                swal("Ooops!! no Patient selected", "Please search and select patient first..");

                                return;

                            }

                            var child = {

                                "patient_id": patient.patient_id,

                                "visit_date_id": patient.account_id,

                                "facility_id": facility_id,

                                "user_id": user_id,

                                "chronic_illness": item.chronic_illness,

                                "substance_abuse": item.substance_abuse,

                                "adoption": item.adoption,

                                "others": item.others

                            };

                            $http.post('/api/familyHistory', child).then(function(data) {

                                swal("Family and social history data successfully Saved!", "", "success");

                            });

                            $("#chronic_illness").val('');

                            $("#substance_abuse").val('');

                            $("#adoption").val('');

                            $("#others").val('');

                        }

                        //Provisional , differential and confirmed diagnosis

                        $scope.diagnosisTemp = [];

                        $scope.addProv = function(item, patient, status) {



                            if (patient.patient_id == null) {

                                swal("Ooops!! no Patient selected", "Please search and select patient first..");

                                return;

                            }

                            $scope.diagnosisTemp.push({

                                "admission_id": '',

                                "patient_id": patient.patient_id,

                                "visit_date_id": patient.account_id,

                                "facility_id": facility_id,

                                "user_id": user_id,

                                "diagnosis_description_id": item.id,

                                "description": item.description,

                                "status": status

                            });

                        }

                        $scope.addDiff = function(item, patient, status) {



                            if (patient.patient_id == null) {

                                swal("Ooops!! no Patient selected", "Please search and select patient first..");

                                return;

                            }

                            $scope.diagnosisTemp.push({

                                "admission_id": '',

                                "patient_id": patient.patient_id,

                                "visit_date_id": patient.account_id,

                                "facility_id": facility_id,

                                "user_id": user_id,

                                "diagnosis_description_id": item.id,

                                "description": item.description,

                                "status": status

                            });

                        }

                        $scope.addConf = function(item, patient, status) {

                            if (patient.patient_id == null) {

                                swal("Ooops!! no Patient selected", "Please search and select patient first..");

                                return;

                            }

                            $scope.diagnosisTemp.push({

                                "admission_id": '',

                                "patient_id": patient.patient_id,

                                "visit_date_id": patient.account_id,

                                "facility_id": facility_id,

                                "user_id": user_id,

                                "diagnosis_description_id": item.id,

                                "description": item.description,

                                "status": status

                            });

                        }

                        $scope.saveDiagnosis = function(objectData,selectedPatient) {

                            if (objectData == "") {

                                swal("Oops Data not saved!", "Please search and select items then click 'Save' button to save data..");
                                return;
                            }

                            $http.post('/api/postDiagnosis', objectData).then(function(data) {
                                swal("Diagnosis data successfully Saved!", "", "success");
                                var confirmedDiagnoses = [];
                                objectData.forEach(function(disease){
                                    if(disease.status.toLowerCase() == 'confirmed')
                                        confirmedDiagnoses.push(disease);
                                });


                                var TallyRegister = {attempt:0, load: function(){
                                    if(confirmedDiagnoses.length == 0)
                                        return;
                                    TallyRegister.attempt++;
                                    $http.post('/api/countOPDDiagnosis',{facility_id:facility_id, dob: $scope.selectedPatient.dob,gender: $scope.selectedPatient.gender,concepts:confirmedDiagnoses}).then(function(data){},function(data){if(TallyRegister.attempt < 5) TallyRegister.load();});
                                }}
                                TallyRegister.load();

                            });

                            $scope.diagnosisTemp = [];

                        }

                        //Dispositions

                        var facilities = [];

                        $scope.showFacility = function(searchKey) {

                            $http.get('/api/getFacilities', {

                                "searchKey": searchKey

                            }).then(function(data) {



                                facilities = data.data;

                            });

                            return facilities;

                        }

                        $scope.exReferral = function(patient, facility, ref) {

                            if (facility == null || ref == null) {

                                swal("Please fill all fields and click save", "", "error");

                                return;

                            }

                            var ext = {

                                "summary": ref.summary,

                                "patient_id": patient.patient_id,

                                "from_facility_id": facility_id,

                                "sender_id": user_id,

                                "to_facility_id": facility.id,

                                "referral_type": 1,

                                "status": 1

                            };

                            $http.post('/api/postReferral', ext).then(function(data) {

                                $scope.ref == null;

                                swal("Patient Referred successfully", "", "success");

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

                        $scope.internalTransfer = function(clinic, patient) {



                            var patientDetails = {

                                "patient_id": patient.patient_id,

                                "main_category_id": patient.main_category_id,

                                "bill_id": patient.bill_id,

                                "sender_clinic_id": 1,

                                "first_name": patient.first_name,

                                "middle_name": patient.middle_name,

                                "last_name": patient.last_name,

                                "medical_record_number": patient.medical_record_number,

                                "gender": patient.gender,

                                "dept_id": clinic.id,

                                "dob": patient.dob,

                                "visit_id": patient.account_id

                            };

                            var object = angular.extend({}, clinic, patientDetails);

                            var modalInstance = $uibModal.open({

                                templateUrl: '/views/modules/clinicalServices/internalTransfer.html',

                                size: 'lg',

                                animation: true,

                                controller: 'admissionModal',

                                resolve: {

                                    object: function() {

                                        return object;

                                    }

                                }

                            });
                            //$uibModalInstance.dismiss();

                        }

                        //Treatments:medication and procedures
                        $scope.getRejected = function (item) {
                            $http.post('/api/rejectedMedicines',{patient_id:item.patient_id,account_id:item.account_id}).then(function (data) {
                                $scope.rejectedMedicines = data.data;
                            });
                        }
                        $scope.updateMedicine = function (item) {
                            $http.post('/api/updateMedicines',item).then(function (data) {
                                if(data.data.status == 1){
                                    swal(data.data.msg,'','success');
                                }
                            });
                        }
                        var mediData = [];

                        $scope.medicines = [];
                        $scope.medicinesOs = [];
                        $scope.searchItems = function(searchKey, patient) {
                            var pay_id = patient.bill_id;
                            if (pay_id == null) {
                                swal("Please search patient to be prescribed before searching Medicine!");
                                return;
                            }
                            if (patient.main_category_id == 3) {
                                pay_id = 1;
                            }
                            $http.post('/api/getMedicine', {
                                "search": searchKey,
                                "facility_id": facility_id,
                                "patient_category_id": pay_id
                            }).then(function(data) {
                                mediData = data.data;
                            });
                            return mediData;
                        }
                        var balance = [];
                        $scope.checkDosage = function(item_id, patient_id) {
                            var item_name = item_id.item_name;
                            $http.post('/api/dosageChecker', {
                                "item_id": item_id.item_id,
                                "patient_id": patient_id
                            }).then(function(data) {
                                if (data.data.length > 0) {
                                    var diff = data.data[0].duration - data.data[0].days;
                                    $scope.dosageCheck = data.data;
                                    swal('ATTENTION', item_name + ' In Dosage Progress', 'info');
                                }
                            });
                        }
                        $scope.addMedicine = function(item, patient, dawa,instructions) {
                            var status_id = 1;

                            var filter = patient.bill_id;

                            var main_category = patient.main_category_id;

                            if (patient == null) {

                                swal("Please search and select Patient to prescribe");

                                return;

                            }

                            if (dawa == null) {

                                swal("Please search and select medicine!");

                                return;

                            }
                            if (!instructions) {
                                instructions = '';

                            }
                            for (var i = 0; i < $scope.medicines.length; i++)

                                if ($scope.medicines[i].item_id == dawa.item_id) {

                                    swal(dawa.item_name + " already in your order list!");

                                    return;

                                }

                            if (main_category != 1 && dawa.exemption_status == 0) {

                                filter = patient.bill_id;

                            }
                            if (main_category == 3 && dawa.exemption_status == 1) {

                                filter = 1;

                            }

                            if (main_category == 2 && dawa.exemption_status == 1) {

                                filter = patient.bill_id;

                            }

                            if (main_category == 3) {

                                main_category = 1;

                            }

                            $http.post('/api/balanceCheck', {

                                "main_category_id": main_category,

                                "item_id": dawa.item_id,

                                "facility_id": facility_id, "user_id": user_id

                            }).then(function(data) {

                                balance = data.data;

                                if (balance.length > 0 ) {
                                    $scope.medicines.push({

                                        "facility_id": facility_id,"item_type_id": dawa.item_type_id,"item_price_id": dawa.price_id,

                                        "quantity": '',"status_id": status_id,"dose": item.dose,"frequency": item.frequency,

                                        "duration": item.duration, "instructions":instructions,

                                        "out_of_stock": "", "payment_filter": filter,"account_number_id": patient.account_id,"visit_id": patient.account_id,

                                        "admission_id": '',"patient_id": patient.patient_id,"user_id": user_id,"item_id": dawa.item_id,

                                        "item_name": dawa.item_name,"dose_formulation": dawa.dose_formulation

                                    });
                                    $('#item_search').val('');
                                    $('#dose').val('');
                                    $('#frequency').val('');
                                    $('#duration').val('');
                                    $('#instruction').val('');

                                } else if (balance.length < 1 ) {
                                    //new swal start
                                    swal({
                                        title: 'This item is not available in Store..Do you want to prescribe anyway?',
                                        text: "",
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
                                        for (var i = 0; i < $scope.medicinesOs.length; i++)
                                            if ($scope.medicinesOs[i].item_id == dawa.item_id) {
                                                swal("Item already in your order list!");
                                                return;
                                            }

                                        $scope.medicinesOs.push({
                                            "facility_id": facility_id,
                                            "item_type_id": dawa.item_type_id,
                                            "item_price_id": dawa.price_id,
                                            "quantity": '',
                                            "status_id": status_id,
                                            "dose": item.dose,
                                            "frequency": item.frequency,
                                            "duration": item.duration,
                                            "instructions":instructions,
                                            "out_of_stock": "OS",
                                            "account_number_id": patient.account_id,
                                            "visit_id": patient.account_id,
                                            "admission_id": '',
                                            "patient_id": patient.patient_id,
                                            "user_id": user_id,
                                            "item_id": dawa.item_id,
                                            "item_name": dawa.item_name
                                        });
                                        swal("Item added under Out of Stock category", "", "success");
                                        $('#item_search').val('');
                                        $('#dose').val('');
                                        $('#frequency').val('');
                                        $('#duration').val('');
                                        $('#instruction').val('');
                                    }, function (dismiss) {
                                        if (dismiss === 'cancel') {
                                            swal("Canceled", "choose different Item for Prescription", "info");
                                            return;
                                        }
                                    })
                                    //new swal end
                                }
                            });

                        }

                        $scope.saveMedicine = function() {
                            if ($scope.medicines == "" && $scope.medicinesOs == "") {
                                swal("No Items to Save,Please choose items..", "", "error");
                                return;
                            }

                            if ($scope.medicines != "") {
                                $http.post('/api/postMedicines', $scope.medicines).then(function(data) {

                                });
                                $scope.medicines = [];
                            }

                            $http.post('/api/outOfStockMedicine', $scope.medicinesOs).then(function(data) {

                            });
                            swal("Patient successfully prescribed!", "", "success");
                            $scope.medicinesOs = [];
                        }

                        $scope.prevMedics = function(item) {
                            $http.post('/api/getPrevMedicine', {
                                "patient_id": item.patient_id
                            }).then(function(data) {
                                $scope.prevMedicines = data.data;
                            });
                        }

                        $scope.prevProcedure = function(item) {
                            $http.post('/api/getPrevProcedures', {
                                "patient_id": item.patient_id
                            }).then(function(data) {
                                $scope.prevProcedures = data.data;
                            });
                        }

                        //medical supplies starts
                        var supplies = [];
                        var balance02 = [];
                        $scope.supplies = [];
                        $scope.suppliesOS = [];

                        $scope.searchMediSupplies = function(searchKey, patient) {
                            var pay_id = patient.bill_id;

                            if (pay_id == null) {
                                swal("Please search patient before searching medical supplies!");
                                return;
                            }

                            if (patient.main_category_id == 3) {
                                pay_id = 1;
                            }

                            $http.post('/api/getMedicalSupplies', {
                                "search": searchKey,
                                "facility_id": facility_id,
                                "patient_category_id": pay_id

                            }).then(function(data) {
                                supplies = data.data;
                            });
                            return supplies;
                        }

                        $scope.addSupplies = function(patient, qty, item) {
                            var status_id = 1;
                            var filter = patient.bill_id;
                            var main_category = patient.main_category_id;
                            var quantity = qty;
                            for (var i = 0; i < $scope.supplies.length; i++)
                                if ($scope.supplies[i].item_id == item.item_id) {
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

                            if (main_category == 3) {
                                main_category = 1;
                            }

                            $http.post('/api/balanceCheck', {
                                "main_category_id": main_category,
                                "item_id": item.item_id,
                                "facility_id": facility_id, "user_id": user_id
                            }).then(function(data) {
                                balance02 = data.data;
                                if (balance02.length < 1) {
                                    swal(item.item_name + ' is not available in store.', 'Contact store manager', 'info');
                                    return;
                                } else if (balance02.length > 0 && balance02[0].balance >= quantity) {
                                    $scope.supplies.push({
                                        "visit_id": patient.account_id,
                                        "admission_id": '',
                                        "out_of_stock": '',
                                        "payment_filter": filter,
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
                                    $('#supplies').val('');
                                    $('#supply_qty').val('');
                                } else if (balance02.length < 1 || balance02[0].balance < quantity) {
                                    var conf = confirm("This Item is not available in Store..Do you want to select it anyway?", "", "info");
                                    if (conf == true) {
                                        for (var i = 0; i < $scope.suppliesOS.length; i++)
                                            if ($scope.suppliesOS[i].item_id == item.item_id) {
                                                swal(item.item_name + " already in your order list!");
                                                return;
                                            }

                                        $scope.suppliesOS.push({
                                            "visit_id": patient.account_id,
                                            "admission_id": '',
                                            "out_of_stock": 'OS',
                                            "payment_filter": filter,
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
                                        $('#supplies').val('');
                                        $('#supply_qty').val('');
                                        swal("Item added under Out of Stock category", "", "success");
                                    } else {
                                        swal("canceled", "Choose different Item", "info");
                                        $('#supplies').val('');
                                        $('#supply_qty').val('');
                                        return;
                                    }
                                }
                            });

                        }

                        $scope.saveSupplies = function() {
                            if ($scope.supplies == "" && $scope.suppliesOS == "") {
                                swal("No Items to Save,Please choose items..", "", "error");
                                return;
                            }

                            if ($scope.supplies != "") {
                                $http.post('/api/postMedicalSupplies', $scope.supplies).then(function(data) {});
                            }

                            $http.post('/api/outOfStockMedicalSupplies', $scope.suppliesOS).then(function(data) {

                            });
                            $scope.supplies = [];
                            $scope.suppliesOS = [];
                            swal("Patient's medical supplies successfully saved!", "", "success");
                        }

                        //medical supplies ends

                        //procedures

                        var procedureData = [];

                        $scope.procedures = [];

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

                        }

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

                        $scope.saveProcedures = function(objectData) {
                            $http.post('/api/postPatientProcedures', objectData).then(function(data) {

                            });
                            swal("Patient procedures successfully saved!", "", "success");
                            $scope.procedures = [];
                        }
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
                                "death_certifier": user_id,
                                "user_id": user_id,
                                "facility_id": facility_id,
                                "immediate_cause": corpse.immediate_cause,
                                "underlying_cause": corpse.underlying_cause,
                                "dept_id": 1
                            };

                            $http.post('/api/postDeceased', deceased).then(function(data) {

                                if (data.data.status == 0) {
                                    swal(data.data.data, "", "error");
                                } else {
                                    swal(item.first_name + ' ' + item.last_name + " sent to Last office", "", "success");
                                }
                            });
                        }
                    },
                    templateUrl: '/views/modules/clinic/Medical/MedicalConsultation.html',
                    parent: angular.element(document.body),
                    clickOutsideToClose: false,
                    fullscreen: true,
                });
            };
            $scope.getCorpseModal = function(item) {
                var  object = item;
                var modalInstance = $uibModal.open({
                    templateUrl: '/views/modules/clinicalServices/deceased.html',
                    size: 'lg',
                    animation: true,
                    controller: 'admissionModal',
                    resolve: {
                        object: function() {
                            return object;
                        }
                    }
                });
            }

            $scope.billsCancellation = function() {
                $http.post('/api/getBillList', {
                    "facility_id": facility_id
                }).then(function(data) {
                    $scope.patientBill = data.data;
                });
            }
            $scope.getBillModal = function(item) {
                $http.post('/api/cancelPatientBill', {
                    "patient_id": item.patient_id,
                    "facility_id": facility_id
                }).then(function(data) {
                    var object = data.data;
                    var modalInstance = $uibModal.open({
                        templateUrl: '/views/modules/clinicalServices/billCancellationModal.html',
                        size: 'lg',
                        animation: true,
                        controller: 'opdController',
                        windowClass: 'app-modal-window',
                        resolve: {
                            object: function() {
                                return object;
                            }
                        }
                    });
                });
            }

            $scope.tallyAttendance = function(attendance){
                var patient_id = $scope.selectedPatient.patient_id;
                var TallyRegister = {attempt:0, load: function(){
                    TallyRegister.attempt++;
                    $http.post('/api/'+(attendance.toLowerCase() == 'new' ? 'countNewAttendance' : 'countReattendance'),{facility_id:facility_id, dob: $scope.selectedPatient.dob,gender: $scope.selectedPatient.gender, clinic_id:1}).then(function(data){
                        var Tally = {attempt:0, load: function(patient_id){
                            Tally.attempt++;
                            $http.post('/api/tallied',{patient_id: patient_id}).then(function(data){},function(data){if(Tally.attempt < 5) Tally.load(patient_id);});
                        }};
                        Tally.load(patient_id);
                    },function(data){if(TallyRegister.attempt < 5) TallyRegister.load();});
                }}
                TallyRegister.load();
            }

        }]);

})();