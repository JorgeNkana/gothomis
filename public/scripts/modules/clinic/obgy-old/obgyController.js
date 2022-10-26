/**
 * Created by Mazigo Jr on 2017-07-17.
 */
(function() {
    'use strict';
    var app = angular.module('authApp');
    app.controller('obgyController', ['$scope', '$http', '$state', '$uibModal','$rootScope', '$mdDialog',
        function($scope, $http, $state, $uibModal, $rootScope, $mdDialog) {
            $scope.cancel = function() {
                $mdDialog.cancel();
                $state.reload();
            };

            var object = $scope.item;

            $scope.selectedPatient = object;
            $scope.patientBills = object;

            var user_id = $rootScope.currentUser.id;
            var facility_id = $rootScope.currentUser.facility_id;
            var patient_id = object.patient_id;
            $scope.today = new Date();
            $scope.saveGyna = function (gyna,patient) {
                var details = {
                    "admission_id": '',
                    "patient_id": patient_id,
                    "visit_date_id": patient.account_id,
                    "user_id": user_id,
                    "facility_id": facility_id,
                };
                var varData = {gyna_data:gyna,details:details};
                $http.post('/api/postGyna',varData).then(function (data) {
                        if(data.data.status == 1){
                            swal(data.data.msg,'','success');
                        }
                   else if(data.data.status == 0) {
                            swal(data.data.msg,'','error');
                        }
                        else {
                            swal('Nothing to save','Please make sure you have written some information in the provided input fields','info');
                        }
                        $('#menarche').val('');$('#cycle').val('');$('#period').val('');$('#menopause').val('');
                    $('#gravidity').val('');$('#parity').val('');$('#abortion').val('');$('#children').val('');$('#lnmp').val('');
                    $('#std').val('');$('#contraceptives').val('');$('#menstrual_cycles').val('');
                });
            }
            $scope.saveObs = function (obs,patient) {
                var details = {
                    "admission_id": '',
                    "patient_id": patient_id,
                    "visit_date_id": patient.account_id,
                    "user_id": user_id,
                    "facility_id": facility_id,
                };
                var varData = {gyna_data:obs,details:details};
                $http.post('/api/postObs',varData).then(function (data) {
                        if(data.data.status == 1){
                            swal(data.data.msg,'','success');
                        }
                   else if(data.data.status == 0) {
                            swal(data.data.msg,'','error');
                        }
                        else {
                            swal('Nothing to save','Please make sure you have written some information in the provided input fields','info');
                        }
                    $('#obs_gravidity').val('');$('#obs_parity').val('');$('#obs_due_date').val('');
                    $('#obs_abortion').val('');$('#obs_children').val('');$('#gestational_age').val('');
                });
            }

            $scope.toto = function() {
                var total = 0;
                for (var i = 0; i < $scope.patientBills.length; i++) {
                    total -= -($scope.patientBills[i].quantity * $scope.patientBills[i].price - $scope.patientBills[i].discount);
                }
                return total;
            }

            $scope.cancelBill = function(item) {
                $http.post('/api/cancelBillItem', {
                    "id": item.id,
                    "patient_id": item.patient_id,
                    "facility_id": facility_id,
                    "user_id": user_id
                }).then(function(data) {
                    swal(item.item_name + " Has been cancelled for this patient", "", "success");
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

                    "date_attended": item.date_attended

                }).then(function(data) {

                    $scope.prevHistory = data.data;

                });

                $http.post('/api/getPrevDiagnosis', {

                    "patient_id": item.patient_id,

                    "date_attended": item.date_attended

                }).then(function(data) {

                    $scope.prevDiagnosis = data.data;

                });

                $http.post('/api/getPrevRos', {

                    "patient_id": item.patient_id,

                    "date_attended": item.date_attended

                }).then(function(data) {

                    $scope.prevRos = data.data;

                });

                $http.post('/api/getPrevBirth', {

                    "patient_id": item.patient_id,

                    "date_attended": item.date_attended

                }).then(function(data) {

                    $scope.prevBirth = data.data;

                });

                $http.post('/api/getPrevObs', {

                    "patient_id": item.patient_id,

                    "date_attended": item.date_attended

                }).then(function(data) {

                    $scope.prevObs = data.data;

                });

                $http.post('/api/getPrevFamily', {

                    "patient_id": item.patient_id,

                    "date_attended": item.date_attended

                }).then(function(data) {

                    $scope.prevFamily = data.data;

                });

                $http.post('/api/getPrevPhysical', {

                    "patient_id": item.patient_id,

                    "date_attended": item.date_attended

                }).then(function(data) {

                    $scope.prevPhysical = data.data;

                });

                $http.post('/api/prevInvestigationResults', {

                    "patient_id": item.patient_id,

                    "date_attended": item.date_attended,

                    "dept_id": 2

                }).then(function(data) {

                    $scope.labInvestigationsz = data.data;

                });

                $http.post('/api/getInvestigationResults', {

                    "patient_id": item.patient_id,

                    "date_attended": item.date_attended,

                    "dept_id": 3

                }).then(function(data) {

                    $scope.radiologyResults = data.data;

                });

                $http.post('/api/getPastMedicine', {

                    "patient_id": item.patient_id,

                    "date_attended": item.date_attended

                }).then(function(data) {

                    $scope.prevMedicines = data.data;

                });

                $http.post('/api/getPastProcedures', {

                    "patient_id": item.patient_id,

                    "date_attended": item.date_attended

                }).then(function(data) {

                    $scope.pastProcedures = data.data;

                });

                $http.post('/api/getAllergies', {

                    "patient_id": item.patient_id,

                    "date_attended": item.date_attended

                }).then(function(data) {

                    $scope.allergies = data.data;

                });

                $http.post('/api/vitalsTime', {

                    "patient_id": item.patient_id,

                    "date_attended": item.date_attended

                }).then(function(data) {

                    $scope.vitals = data.data;

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

            var pastMed = [];

            $scope.showMedication = function(search) {

                $http.post('/api/reviewOfSystems', {

                    "search": search,

                    "category": "Medication"

                }).then(function(data) {

                    pastMed = data.data;

                });

                return pastMed;

            }



            var pastMed = [];

            $scope.showIllness = function(search) {

                $http.post('/api/reviewOfSystems', {

                    "search": search,

                    "category": "Past Medical History"

                }).then(function(data) {

                    pastMed = data.data;

                });

                return pastMed;

            }

            var pastAdm = [];

            $scope.showAdmission = function(search) {

                $http.post('/api/reviewOfSystems', {

                    "search": search,

                    "category": "Admission History"

                }).then(function(data) {

                    pastAdm = data.data;

                });

                return pastAdm;

            }

            var pastImmune = [];

            $scope.showImmune = function(search) {

                $http.post('/api/reviewOfSystems', {

                    "search": search,

                    "category": "Immunisation"

                }).then(function(data) {

                    pastImmune = data.data;

                });

                return pastImmune;

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

                    if (data.data.length > 0) {

                        swal("Items with red marks are currently not available..", "But You can still order them if necessary", "info");

                    } else {

                        swal("If  no Tests displayed under this category..", "Please, Contact Lab manager", "info");

                    }

                });

            }

            $scope.investigationOrders = [];

            $scope.unavailableOrders = [];

            $scope.orders = function(item, isChecked, patient) {

                var status_id = 1;

                var filter = patient.bill_id;
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

                            "patient_id": patient.patient_id,

                            "user_id": user_id,

                            "item_id": item.item_id,

                            "item_name": item.item_name,

                            "priority": '',

                            "clinical_note": '',

                            "payment_filter": filter

                        });

                        //console.log($scope.investigationOrders);

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
                    "user_id": user_id,"facility_id": facility_id,"requesting_department_id":26};

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
                $scope.selectedcomplaint = "";
                $scope.qty = "";
                $scope.unit = "";
            }

            $scope.saveComplaints = function(objectData, history,patient) {

                if(objectData != " "){
                    $http.post('/api/postHistory', objectData).then(function(data) {

                    });
                }
                else if(history.other_complaints != ""){
                    var other_complaints = {
                        "admission_id": '',

                        "patient_id": patient.patient_id,

                        "facility_id": facility_id,

                        "visit_date_id": patient.account_id,

                        "user_id": user_id,
                        "other_complaints":history.other_complaints
                    };
                    $http.post('/api/postOtherComplaints',other_complaints).then(function (data) {

                    });
                }

                swal("Complaints  data successfully saved!", "", "success");

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

                if (objectData == "") {

                    swal("Oops Data not saved!", "Please search and select items then click 'Save' button to save data..");

                    return;

                }

                for (var i = 0; i < objectData.length; i++) {

                    objectData[i]['review_summary'] = rosSummary;

                }

                $http.post('/api/postRoS', objectData).then(function(data) {



                });

                swal("Review of systems data successfully Saved!", "", "success");

                $scope.rosTemp = [];

                $("#review_summary").val('');

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

                $("#allergy").val('');

            }

            $scope.pastMedicals3 = function(item, patient) {

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

                $("#illness").val('');

            }

            $scope.pastMedicals4 = function(item, patient) {

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

                $("#admission").val('');

            }

            $scope.pastMedicals5 = function(item, patient) {

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

                $("#immunisation").val('');

            }



            $scope.savePastMedical = function(objectData, other_past_medicals) {

                if (objectData == "") {

                    swal("Oops Data not saved!", "Please search and select items then click 'Save' button to save data..");

                    return;

                }

                for (var i = 0; i < objectData.length; i++) {

                    objectData[i]['other_past_medicals'] = other_past_medicals;

                }

                $http.post('/api/postPastMed', objectData).then(function(data) {



                });

                swal("Past medical history data successfully Saved!", "", "success");

                $scope.pastTemp = [];

                $("#other_past_medicals").val('');



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

                $('#local_exam').val('');

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

                $('#gen_exam').val('');

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

            $scope.saveDiagnosis = function(objectData) {

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
						$http.post('/api/countClinicDiagnosis',{facility_id:facility_id, dob: $scope.selectedPatient.dob,gender: $scope.selectedPatient.gender,concepts:confirmedDiagnoses, clinic_id: 26}).then(function(data){},function(data){if(TallyRegister.attempt < 5) TallyRegister.load();});
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

                        swal('ATTENTION', item_name + ' In Dosage Progress ' + diff + ' day(s) remained to Complete a Dosage', 'info');

                    }

                });





            }

            $scope.addMedicine = function(item, patient, dawa) {



                var status_id = 1;

                var filter = patient.bill_id;

                var main_category = patient.main_category_id;

                var quantity = item.dose * item.duration * 24 / item.frequency;

                if (patient == null) {

                    swal("Please search and select Patient to prescribe");

                    return;

                }

                if (dawa == null) {

                    swal("Please search and select medicine!");

                    return;

                }
                //
                // if (item.instructions == null) {
                //
                //     swal("Please Write Instructions and click 'Add to List' Button", "", "error");
                //
                //     return;
                //
                // }

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

                    if (balance.length < 1) {

                        swal(dawa.item_name + ' is not available in store.', 'Contact store manager', 'info');

                        return;

                    } else if (balance.length > 0 && balance[0].balance >= quantity) {

                        $scope.medicines.push({

                            "facility_id": facility_id,

                            "item_type_id": dawa.item_type_id,

                            "item_price_id": dawa.price_id,

                            "quantity": quantity,

                            "status_id": status_id,

                            "dose": item.dose,

                            "frequency": item.frequency,

                            "duration": item.duration,

                            "instructions": item.instructions,

                            "out_of_stock": "",

                            "payment_filter": filter,

                            "account_number_id": patient.account_id,

                            "visit_id": patient.account_id,

                            "admission_id": '',

                            "patient_id": patient.patient_id,

                            "user_id": user_id,

                            "item_id": dawa.item_id,

                            "item_name": dawa.item_name,

                            "dose_formulation": dawa.dose_formulation

                        });
                        $scope.selectedItem = "";
                        $scope.dosage.dose = "";
                        $scope.dosage.frequency = "";
                        $scope.dosage.duration = "";
                        $scope.dosage.instructions = "";


                    } else if (balance.length < 1 || balance[0].balance < quantity) {

                        var conf = confirm("This Item is not available in Store..Do you want to prescribe anyway?");

                        if (conf == true) {

                            for (var i = 0; i < $scope.medicinesOs.length; i++)

                                if ($scope.medicinesOs[i].item_id == dawa.item_id) {

                                    swal("Item already in your order list!");

                                    return;

                                }

                            $scope.medicinesOs.push({
                                "facility_id": facility_id,
                                "item_type_id": dawa.item_type_id,
                                "item_price_id": dawa.price_id,
                                "quantity": quantity,
                                "status_id": status_id,
                                "dose": item.dose,
                                "frequency": item.frequency,
                                "duration": item.duration,
                                "instructions": item.instructions,
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
                            $scope.selectedItem = "";
                            $scope.dosage.dose = "";
                            $scope.dosage.frequency = "";
                            $scope.dosage.duration = "";
                            $scope.dosage.instructions = "";
                        } else {
                            swal("canceled", "Choose different Item for Prescription", "info");
                            return;
                        }
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

                if (patient == null) {
                    swal("Please search and select Patient", "", "error");
                    return;
                }

                if (item == null) {
                    swal("Please search and select Medical supplies!", "", "error");
                    return;
                }

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
                        $scope.selectedSupplies ="";
                        $scope.supply_qty ="";
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
                            $scope.selectedSupplies ="";
                            $scope.supply_qty ="";
                            swal("Item added under Out of Stock category", "", "success");
                        } else {
                            swal("canceled", "Choose different Item", "info");
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
                $scope.selectedProcedures = "";
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
			
			$scope.tallyAttendance = function(attendance ){
				var patient_id = $scope.selectedPatient.patient_id;
				var TallyRegister = {attempt:0, load: function(){
					TallyRegister.attempt++;
					$http.post('/api/'+(attendance.toLowerCase() == 'new' ? 'countNewAttendance' : 'countReattendance'),{facility_id:facility_id, dob: $scope.selectedPatient.dob,gender: $scope.selectedPatient.gender, clinic_id:26}).then(function(data){
						var Tally = {attempt:0, load: function(patient_id){
							Tally.attempt++;
							$http.post('/api/tallied',{patient_id: patient_id}).then(function(data){},function(data){if(Tally.attempt < 5) Tally.load(patient_id);});
						}};
						Tally.load(patient_id);					
					},function(data){if(TallyRegister.attempt < 5) TallyRegister.load();});
				}}
				TallyRegister.load();
			}
        }
    ]);
})();