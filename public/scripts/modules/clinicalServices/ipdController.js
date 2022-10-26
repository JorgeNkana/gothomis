/**
 * Created by Mazigo Jr on 2017-03-09.
 */

(function () {
    'use strict';
    var app =  angular.module('authApp');
    app.controller('ipdController',['$scope','$http','$state','$rootScope','$uibModal','$mdDialog',
        function ($scope,$http,$state,$rootScope,$uibModal,$mdDialog) {

            $scope.selectedPatient = $scope.item;
            $scope.cancel = function() {
                $mdDialog.cancel();
            };
            $scope.patientBills = $scope.item;
            var user_id = $rootScope.currentUser.id;
            var facility_id = $rootScope.currentUser.facility_id;
            var patient_id = $scope.item.patient_id;
            var account_id = $scope.item.account_id;
            $scope.setTab = function(newTab){
                $scope.tab = newTab;
            };
            $scope.isSet = function(tabNum){
                return $scope.tab === tabNum;
            }
            $scope.toto = function () {
                var  total = 0;
                for(var i = 0; i < $scope.patientBills.length ; i++) {
                    total += ($scope.patientBills[i].quantity * $scope.patientBills[i].price - $scope.patientBills[i].discount);
                }
                return total;
            }
            
			$scope.cancelBill = function(item) {
				var html = '<form class="form-horizontal" role="form" name="myForm" autocomplete="off" >\
				<br />\
				<div class="row">\
					<div class="form-group">\
						<label class="col-md-3 control-label">User:</label>\
						<div class="col-md-9">\
							<input type="text" disabled class="form-control" value="' +$rootScope.currentUser.name+ '"/>\
						</div>\
					</div>\
					<div class="form-group">\
						<label class="col-md-3 control-label">Reason:</label>\
						<div class="col-md-9">\
							<textarea id="reason" class="form-control" rows="4"></textarea>\
						</div>\
					</div>\
				</div>\
				</form>';
				
				swal({
						title: 'Cancelling Client Bill Item',
						html: html,
						type: 'info',
						showCancelButton: true,
						confirmButtonColor: '#3085d6',
						cancelButtonColor: '#d33',
						confirmButtonText: 'OK',
						customClass: 'swal-wide',
						allowOutsideClick:false
					}).then(function () {
						if($.trim($('textarea#reason').val()) == ''){
							swal('You must provide a reason for your action','','warning');
							return;
						}
						Helper.overlay(true);
						$http.post('/api/cancelBillItem', {
							"id": item.id,
							"patient_id": item.patient_id,
							"facility_id": facility_id,
							"user_id": user_id,
							"reason":$.trim($('textarea#reason').val())
						}).then(function(data) {
							Helper.overlay(false);
							swal({
									title:'Cancelling Client Bill Item',
									html:'Action executed successfully',
									type:'info',
									customClass: 'swal-wide',
									allowOutsideClick:false
								});
							//remove the item from the list of bills
							for(var i = 0; i < $scope.patientBills.length; i++)
								if($scope.patientBills[i].id == item.id)
									$scope.patientBills.splice(i);
							//close the model if no more bills for the client
							if($scope.patientBills.length == 0)
								$scope.cancel();
						}, function(data){Helper.overlay(false);});
					}, function(){ return;});
                
            };
			
            $scope.oneAtATime = true;
            $scope.closeModal = function () {
                $uibModalInstance.dismiss();
            }
            $scope.generateDischargeForm = function (item) {
                //console.log(item)
                $http.post('/api/getPrevDiagnosis',{"patient_id":item.patient_id,"date_attended":item.visit_date}).then(function (data) {
                    $scope.prevDiagnosis = data.data;
                });
                $http.post('/api/getPastMedicine',{"patient_id":item.patient_id,"date_attended":item.visit_date}).then(function (data) {
                    $scope.prevMedicines = data.data;
                });
                $http.post('/api/getPastProcedures',{"patient_id":item.patient_id,"date_attended":item.visit_date}).then(function (data) {
                    $scope.pastProcedures = data.data;

                });
            }
            $scope.setAppointment = function (patient,apt) {

                var detail={patient_id:patient.patient_id,user_id:user_id,facility_id:facility_id,clinic_id:apt.clinic_id,appointment_date:apt.appointment};
                $http.post('/api/setAppointments',detail).then(function (data) {

                    if(data.data.status==0){
                        swal('',data.data.data,'error')
                    }
                    else{
                        swal('',data.data.data,'success')
                    }


                });
            }
            $scope.dischargePatient = function (patient) {
                $http.post('/api/dischargePatient',{"patient_id":patient.patient_id,"facility_id":facility_id}).then(function (data) {
                    if(data.data.status==0){
                        swal('',data.data.data,'error')
                    }
                    else{
                        swal('',data.data.data,'success')
                    }
                });
            }
            $scope.templates = [
                {   name: 'Discharge',
                    url: 'deceased.html'},
                {
                    name: 'Internal Transfer',
                    url: 'internal.html'},
                {
                    name: 'External Referral',
                    url: 'referral.html'},
                {
                    name: 'Deceased',
                    url: 'deceased.html'}
            ];

            //get patients who have paid consultation fee/exempted/insurance
            var patientData =[];
            $scope.showSearch = function(searchKey) {
                $http.post('/api/getOpdPatients',{"facility_id":facility_id}).then(function(data) {
                    patientData = data.data;

                });
                return patientData;
            }

            var patientInvData = [];
            $scope.showSearch = function (searchKey) {
                $http.post('/api/getOpdInvPatients', {"facility_id": facility_id}).then(function (data) {
                    patientInvData = data.data;
                });
                return patientInvData;
            }
            //Previous History
            angular.element(document).ready(function () {
                $scope.setTab(1);
                $scope.getNotes($scope.item);
                $http.post('/api/previousVisits',{"patient_id":patient_id}).then(function (data) {
                    $scope.patientsVisits = data.data;
                });
                $http.post('/api/getResults',{"patient_id":patient_id,"dept_id":3}).then(function (data) {
                    $scope.radiology = data.data;
                });
                $http.post('/api/getResults',{"patient_id":patient_id,"dept_id":2}).then(function (data) {
                    $scope.labInvestigations = data.data;
                });
                $http.get('/api/getWards/'+facility_id).then(function (data) {
                    $scope.wards = data.data;
                });
                $http.get('/api/getSpecialClinics').then(function (data) {
                    $scope.clinics = data.data;
                });
            });
            $scope.getPatientReport = function (item) {

                $http.post('/api/prevHistory',{"patient_id":item.patient_id,"date_attended":item.date_attended}).then(function (data) {
                    $scope.prevHistory = data.data;
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
                $http.post('/api/getPrevObs',{"patient_id":item.patient_id,"date_attended":item.date_attended}).then(function (data) {
                    $scope.prevObs = data.data;
                });
                $http.post('/api/getPrevFamily',{"patient_id":item.patient_id,"date_attended":item.date_attended}).then(function (data) {
                    $scope.prevFamily = data.data;
                });
                $http.post('/api/getPrevPhysical',{"patient_id":item.patient_id,"date_attended":item.date_attended}).then(function (data) {
                    $scope.prevPhysical = data.data;
                });
                $http.post('/api/getInvestigationResults',{"patient_id":item.patient_id,"date_attended":item.date_attended,"dept_id":2}).then(function (data) {
                    $scope.labInvestigations = data.data;
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

            }
            $scope.getVitalTime=function (item,patient) {
                $http.post('/api/vitalsTime',{"patient_id":patient.patient_id,"date_attended":item.date_attended}).then(function (data) {
                    $scope.vitalsTime = data.data;
                });
            }
            var patientVitals = [];
            $scope.getVitalData=function (item,patient) {
                $http.post('/api/patientVitals',{"patient_id":patient.patient_id,"time_attended":item.time_attended}).then(function (data) {
                    patientVitals = data.data;
                    var object = angular.extend({},patientVitals,patient);
                    var modalInstance = $uibModal.open({
                        templateUrl: '/views/modules/clinicalServices/vitalSigns.html',
                        size: 'lg',
                        animation: true,
                        controller: 'admissionModal',
                        resolve:{
                            object: function () {
                                return object;
                            }
                        }
                    });
                });
            }
            $scope.getDiagnosis = function (item) {
                $http.post('/api/getPrevDiagnosis',{"patient_id":item.patient_id,"date_attended":item.date_attended}).then(function (data) {
                    $scope.prevDiagnosis = data.data;
                });
            }
            $scope.getHistory = function (item) {
                $http.post('/api/prevHistory',{"patient_id":item.patient_id,"date_attended":item.date_attended}).then(function (data) {
                    $scope.prevHistory = data.data;
                });
            }
            $scope.getRos = function (item) {
                $http.post('/api/getPrevRos',{"patient_id":item.patient_id,"date_attended":item.date_attended}).then(function (data) {
                    $scope.prevRos = data.data;
                });
            }
            $scope.getBirth = function (item) {
                $http.post('/api/getPrevBirth',{"patient_id":item.patient_id,"date_attended":item.date_attended}).then(function (data) {
                    $scope.prevBirth = data.data;
                });
            }
            $scope.getObs = function (item) {
                $http.post('/api/getPrevObs',{"patient_id":item.patient_id,"date_attended":item.date_attended}).then(function (data) {
                    $scope.prevObs = data.data;
                });
            }
            $scope.getFamily = function (item) {
                $http.post('/api/getPrevFamily',{"patient_id":item.patient_id,"date_attended":item.date_attended}).then(function (data) {
                    $scope.prevFamily = data.data;
                });
            }
            $scope.getPhysical = function (item) {
                $http.post('/api/getPrevPhysical',{"patient_id":item.patient_id,"date_attended":item.date_attended}).then(function (data) {
                    $scope.prevPhysical = data.data;
                });
            }
            //chief complaints
            var chiefComplaints = [];
            $scope.complaints = function (search) {
                $http.post('/api/chiefComplaints',{"search":search}).then(function (data) {
                    chiefComplaints = data.data;
                });
                return chiefComplaints;
            }
            //review of systems
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
            var cardiovascular = [];
            $scope.showCardio = function (search) {
                $http.post('/api/reviewOfSystems',{"search":search,"category":"Cardiovascular"}).then(function (data) {
                    cardiovascular = data.data;
                });
                return cardiovascular;
            }
            var Respiratory = [];
            $scope.showRespiratory = function (search) {
                $http.post('/api/reviewOfSystems',{"search":search,"category":"Respiratory"}).then(function (data) {
                    Respiratory = data.data;
                });
                return Respiratory;
            }
            var gatro = [];
            $scope.showGastro = function (search) {
                $http.post('/api/reviewOfSystems',{"search":search,"category":"Gastrointerstinal"}).then(function (data) {
                    gatro = data.data;
                });
                return gatro;
            }
            var musculo = [];
            $scope.showMusculo = function (search) {
                $http.post('/api/reviewOfSystems',{"search":search,"category":"Musculoskeletal"}).then(function (data) {
                    musculo = data.data;
                });
                return musculo;
            }
            var genito = [];
            $scope.showGenito = function (search) {
                $http.post('/api/reviewOfSystems',{"search":search,"category":"Genitourinary"}).then(function (data) {
                    genito = data.data;
                });
                return genito;
            }
            var cns = [];
            $scope.showCNS = function (search) {
                $http.post('/api/reviewOfSystems',{"search":search,"category":"Central Nervous System"}).then(function (data) {
                    cns = data.data;
                });
                return cns;
            }
            var endo = [];
            $scope.showEndo = function (search) {
                $http.post('/api/reviewOfSystems',{"search":search,"category":"Endocrine"}).then(function (data) {
                    endo = data.data;
                });
                return endo;
            }
            var allergy = [];
            $scope.showAllergy = function (search) {
                $http.post('/api/reviewOfSystems',{"search":search,"category":"Allergy"}).then(function (data) {
                    allergy = data.data;
                });
                return allergy;
            }
            var pastMed = [];
            $scope.showMedication = function (search) {
                $http.post('/api/pastMedications',{"search":search,"category":"Medication"}).then(function (data) {
                    pastMed = data.data;
                });
                return pastMed;
            }

            var pastMed = [];
            $scope.showIllness = function (search) {
                $http.post('/api/reviewOfSystems',{"search":search,"category":"Past Medical History"}).then(function (data) {
                    pastMed = data.data;
                });
                return pastMed;
            }
            var pastAdm = [];
            $scope.showAdmission = function (search) {
                $http.post('/api/reviewOfSystems',{"search":search,"category":"Admission History"}).then(function (data) {
                    pastAdm = data.data;
                });
                return pastAdm;
            }
            var pastImmune = [];
            $scope.showImmune = function (search) {
                $http.post('/api/reviewOfSystems',{"search":search,"category":"Immunisation"}).then(function (data) {
                    pastImmune = data.data;
                });
                return pastImmune;
            }
            var pastInsp = [];
            $scope.showInspection = function (search) {
                $http.post('/api/reviewOfSystems',{"search":search,"category":"Inspection"}).then(function (data) {
                    pastInsp = data.data;
                });
                return pastInsp;
            }
            var pastPalp = [];
            $scope.showPalpation = function (search) {
                $http.post('/api/reviewOfSystems',{"search":search,"category":"Palpation"}).then(function (data) {
                    pastPalp = data.data;
                });
                return pastPalp;
            }
            var pastPerc = [];
            $scope.showPercussion = function (search) {
                $http.post('/api/reviewOfSystems',{"search":search,"category":"Percussion"}).then(function (data) {
                    pastPerc = data.data;
                });
                return pastPerc;
            }
            var pastAus = [];
            $scope.showAuscultation = function (search) {
                $http.post('/api/reviewOfSystems',{"search":search,"category":"Auscultation"}).then(function (data) {
                    pastAus = data.data;
                });
                return pastAus;
            }
            var diag = [];
            $scope.showDiagnosis = function (search) {
                $http.post('/api/getDiagnosis',{"search":search}).then(function (data) {
                    diag = data.data;
                });
                return diag;
            }
            $scope.allergyChecker = function (item) {
                $http.post('/api/getAllergy',{"patient_id":item.patient_id}).then(function (data) {
                    swal("This Patient is allergic to "+data.data[0].descriptions,"","info");
                });
            }
            //Investigations
            $scope.getSubDepts = function (item) {
                $http.post('/api/getSubDepts',{"department_id":item}).then(function (data) {
                    $scope.subDepartments = data.data;
                });
            }
            $scope.getTests = function (item,category) {
                if(angular.isDefined(category)==false){swal("Please...select Patient first","","error");return;}
                var category_id =category.bill_id;
                if(category.main_category_id == 3){
                    category_id = 1;
                }
                $http.post('/api/getPanels',{"patient_category_id":category_id,"sub_dept_id":item,"facility_id":facility_id}).then(function (data) {
                    $scope.panels = data.data;
                });

                $http.post('/api/getSingleTests',{"patient_category_id":category_id,"sub_dept_id":item,"facility_id":facility_id}).then(function (data) {
                    $scope.singleTests = data.data;
                });

                $http.post('/api/getTests',{"patient_category_id":category_id,"sub_dept_id":item,"facility_id":facility_id}).then(function (data) {
                    $scope.labTests = data.data;
                    if(data.data.length>0){
                        swal("Items with red marks are currently not available..","But You can still order them if necessary","info");
                    }
                    else {
                        swal("If  no Tests displayed under this category..","Please, Contact Lab manager","info");
                    }
                });
            }
            $scope.investigationOrders = [];
            $scope.unavailableOrders = [];
            $scope.orders = function (item,isChecked,patient) {				
                var status_id = 1;
                var filter = patient.bill_id;
                if(patient.patient_id == null){
                    swal("Ooops!! no Patient selected","Please search and select patient first..");
                    return;
                }
                if(isChecked==true){
                    for(var i=0;i<$scope.investigationOrders.length;i++)
                        if($scope.investigationOrders[i].item_id == item.item_id){
                            swal(item.item_name+' '+" already in your order list!");
                            return;
                        }
                    if(item.on_off== 1) {
                        if(patient.main_category_id != 1){ filter = patient.bill_id;}
                        $scope.investigationOrders.push({"requesting_department_id":5,"admission_id":patient.admission_id,"facility_id":facility_id,"item_type_id":item.item_type_id,"item_price_id":item.item_price_id,"status_id":status_id,
                            "account_number_id":patient.account_id,"patient_id":patient.patient_id,"user_id":user_id,"item_id":item.item_id,"item_name":item.item_name,
                            "priority":'',"clinical_note":'',"payment_filter":filter});
                        //console.log($scope.investigationOrders);
                    }
                    else {
                        for(var i=0;i<$scope.unavailableOrders.length;i++)
                            if($scope.unavailableOrders[i].item_id == item.item_id){
                                swal(item.item_name+' '+" already in your order list!");
                                return;
                            }
                        $scope.unavailableOrders.push({"facility_id":facility_id,"visit_date_id":patient.account_id,
                            "patient_id":patient.patient_id,"user_id":user_id,"item_id":item.item_id,"item_name":item.item_name});
                        return;
                    }
                }
            }
            $scope.saveInvestigation = function(item) {
                if ($scope.investigationOrders == "" && $scope.unavailableOrders == null) {

                    swal("You dont have Items to save!", "Please select Items first!");

                    return;

                }

                for (var i = 0; i < $scope.investigationOrders.length; i++) {

                    $scope.investigationOrders[i]["priority"] = item.priority;

                    $scope.investigationOrders[i]["clinical_note"] = item.clinical_note;

                }

                if ($scope.investigationOrders != "") {

                    $http.post('/api/postInvestigations', $scope.investigationOrders).then(function(data) {});

                    $scope.investigationOrders = [];
					$('#clinical_note').val('');
					$('#priority').val('');


                }

                $http.post('/api/postUnavailableInvestigations', $scope.unavailableOrders).then(function(data) {



                });

                swal("Investigation order successfully saved!", "", "success");



                $scope.unavailableOrders = [];
					$('#clinical_note').val('');
					$('#priority2').val('');

            }

            //Investigation results
            $scope.getLabResults = function (item) {
                var results = {"patient_id":item.patient_id,"date_attended":item.date_attended,"dept_id":item.dept_id};
                $http.post('/api/getInvestigationResults',results).then(function (data) {
                    $scope.labResults = data.data;
                });
            }
            $scope.getRadResults = function (item) {
                var results = {"patient_id":item.patient_id,"date_attended":item.date_attended,"dept_id":item.dept_id};
                $http.post('/api/getInvestigationResults',results).then(function (data) {
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
            $scope.reviewOfSystems = function (item,patient) {

                if(patient.patient_id == null){ swal("Ooops!! no Patient selected","Please search and select patient first.."); return; }
                $scope.rosTemp.push({"admission_id":patient.admission_id,"patient_id":patient.patient_id,"visit_date_id":patient.account_id,"user_id":user_id,"facility_id":facility_id,"system_id":item.id,"name":item.name,"status":item.category});
            }
            $scope.reviewOfSystems2 = function (item,patient) {
                if(patient.patient_id == null){ swal("Ooops!! no Patient selected","Please search and select patient first.."); return; }
                $scope.rosTemp.push({"admission_id":patient.admission_id,"patient_id":patient.patient_id,"visit_date_id":patient.account_id,"user_id":user_id,"facility_id":facility_id,"system_id":item.id,"name":item.name,"status":item.category});
            }
            $scope.reviewOfSystems3 = function (item,patient) {
                if(patient.patient_id == null){ swal("Ooops!! no Patient selected","Please search and select patient first.."); return; }
                $scope.rosTemp.push({"admission_id":patient.admission_id,"patient_id":patient.patient_id,"visit_date_id":patient.account_id,"user_id":user_id,"facility_id":facility_id,"system_id":item.id,"name":item.name,"status":item.category});
            }
            $scope.reviewOfSystems4 = function (item,patient) {
                if(patient.patient_id == null){ swal("Ooops!! no Patient selected","Please search and select patient first.."); return; }
                $scope.rosTemp.push({"admission_id":patient.admission_id,"patient_id":patient.patient_id,"visit_date_id":patient.account_id,"user_id":user_id,"facility_id":facility_id,"system_id":item.id,"name":item.name,"status":item.category});
            }
            $scope.reviewOfSystems5 = function (item,patient) {
                if(patient.patient_id == null){ swal("Ooops!! no Patient selected","Please search and select patient first.."); return; }
                $scope.rosTemp.push({"admission_id":patient.admission_id,"patient_id":patient.patient_id,"visit_date_id":patient.account_id,"user_id":user_id,"facility_id":facility_id,"system_id":item.id,"name":item.name,"status":item.category});
            }
            $scope.reviewOfSystems6 = function (item,patient) {
                if(patient.patient_id == null){ swal("Ooops!! no Patient selected","Please search and select patient first.."); return; }
                $scope.rosTemp.push({"admission_id":patient.admission_id,"patient_id":patient.patient_id,"visit_date_id":patient.account_id,"user_id":user_id,"facility_id":facility_id,"system_id":item.id,"name":item.name,"status":item.category});
            }
            $scope.reviewOfSystems7 = function (item,patient) {
                if(patient.patient_id == null){ swal("Ooops!! no Patient selected","Please search and select patient first.."); return; }
                $scope.rosTemp.push({"admission_id":patient.admission_id,"patient_id":patient.patient_id,"visit_date_id":patient.account_id,"user_id":user_id,"facility_id":facility_id,"system_id":item.id,"name":item.name,"status":item.category});
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
            $scope.pastMedicals = function (item,patient) {
                if(patient.patient_id == null){ swal("Ooops!! no Patient selected","Please search and select patient first.."); return; }
                $scope.pastTemp.push({"admission_id":patient.admission_id,"patient_id":patient.patient_id,"visit_date_id":patient.account_id,"user_id":user_id,"facility_id":facility_id,"system_id":item.id,"name":item.name,"status":item.category});
            }

            $scope.pastMedicals3 = function (item,patient) {
                if(patient.patient_id == null){ swal("Ooops!! no Patient selected","Please search and select patient first.."); return; }
                $scope.pastTemp.push({"admission_id":patient.admission_id,"patient_id":patient.patient_id,"visit_date_id":patient.account_id,"user_id":user_id,"facility_id":facility_id,"system_id":item.id,"name":item.name,"status":item.category});
            }
            $scope.pastMedicals4 = function (item,patient) {
                if(patient.patient_id == null){ swal("Ooops!! no Patient selected","Please search and select patient first.."); return; }
                $scope.pastTemp.push({"admission_id":patient.admission_id,"patient_id":patient.patient_id,"visit_date_id":patient.account_id,"user_id":user_id,"facility_id":facility_id,"system_id":item.id,"name":item.name,"status":item.category});
            }
            $scope.pastMedicals5 = function (item,patient) {
                if(patient.patient_id == null){ swal("Ooops!! no Patient selected","Please search and select patient first.."); return; }
                $scope.pastTemp.push({"admission_id":patient.admission_id,"patient_id":patient.patient_id,"visit_date_id":patient.account_id,"user_id":user_id,"facility_id":facility_id,"system_id":item.id,"name":item.name,"status":item.category});
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
            //Physical Examinations
            $scope.removeFromSelection = function(item,objectdata){
                var indexremoveobject = objectdata.indexOf(item);
                objectdata.splice(indexremoveobject,1);
            }
            $scope.physicalMusculoskeletal = [];  $scope.physicalRespiratory = [];
            $scope.physicalCardiovascular = [];  $scope.physicalGastrointestinal = [];
            $scope.physicalGenitourinary = []; $scope.physicalCNS = []; $scope.physicalEndocrine = [];

            $scope.physicalMusculo = function (item,patient,system) {
                if(patient.patient_id == null){ swal("Ooops!! no Patient selected","Please search and select patient first.."); return; }
                $scope.physicalMusculoskeletal.push({"admission_id":patient.admission_id,"patient_id":patient.patient_id,"visit_date_id":patient.account_id,"user_id":user_id,"facility_id":facility_id,"system":system,"category":item.category,"observation":item.name});
            }
            $scope.physicalResp = function (item,patient,system) {
                if(patient.patient_id == null){ swal("Ooops!! no Patient selected","Please search and select patient first.."); return; }
                $scope.physicalRespiratory.push({"admission_id":patient.admission_id,"patient_id":patient.patient_id,"visit_date_id":patient.account_id,"user_id":user_id,"facility_id":facility_id,"system":system,"category":item.category,"observation":item.name});
            }
            $scope.physicalCardio = function (item,patient,system) {
                if(patient.patient_id == null){ swal("Ooops!! no Patient selected","Please search and select patient first.."); return; }
                $scope.physicalCardiovascular.push({"admission_id":patient.admission_id,"patient_id":patient.patient_id,"visit_date_id":patient.account_id,"user_id":user_id,"facility_id":facility_id,"system":system,"category":item.category,"observation":item.name});
            }
            $scope.physicalGastro = function (item,patient,system) {
                if(patient.patient_id == null){ swal("Ooops!! no Patient selected","Please search and select patient first.."); return; }
                $scope.physicalGastrointestinal.push({"admission_id":patient.admission_id,"patient_id":patient.patient_id,"visit_date_id":patient.account_id,"user_id":user_id,"facility_id":facility_id,"system":system,"category":item.category,"observation":item.name});
            }
            $scope.physicalGenito = function (item,patient,system) {
                if(patient.patient_id == null){ swal("Ooops!! no Patient selected","Please search and select patient first.."); return; }
                $scope.physicalGenitourinary.push({"admission_id":patient.admission_id,"patient_id":patient.patient_id,"visit_date_id":patient.account_id,"user_id":user_id,"facility_id":facility_id,"system":system,"category":item.category,"observation":item.name});
            }
            $scope.physicalCns = function (item,patient,system) {
                if(patient.patient_id == null){ swal("Ooops!! no Patient selected","Please search and select patient first.."); return; }
                $scope.physicalCNS.push({"admission_id":patient.admission_id,"patient_id":patient.patient_id,"visit_date_id":patient.account_id,"user_id":user_id,"facility_id":facility_id,"system":system,"category":item.category,"observation":item.name});
            }
            $scope.physicalEndo = function (item,patient,system) {
                if(patient.patient_id == null){ swal("Ooops!! no Patient selected","Please search and select patient first.."); return; }
                $scope.physicalEndocrine.push({"admission_id":patient.admission_id,"patient_id":patient.patient_id,"visit_date_id":patient.account_id,"user_id":user_id,"facility_id":facility_id,"system":system,"category":item.category,"observation":item.name});
            }
            $scope.savePhysicalExamination = function (objectData) {
                if(objectData == "") {
                    swal("Oops Data not saved!", "Please search and select items then click 'Save' button to save data..");
                    return;
                }
                $http.post('/api/postPhysical',objectData).then(function (data) {

                });
                swal(objectData[0].system+'  ' +"system data successfully Saved!","","success");
                $scope.physicalMusculoskeletal = []; $scope.physicalRespiratory = [];  $scope.physicalCardiovascular = [];
                $scope.physicalGastrointestinal = [];  $scope.physicalGenitourinary = []; $scope.physicalCNS = []; $scope.physicalEndocrine = [];
            }
            $scope.saveLocalExams = function (patient,examData) {
                if(examData == null){
                    swal('Please write examination for this patient first','','error'); return;
                }
                var local_examz = {"admission_id":patient.admission_id,"patient_id":patient.patient_id,
                    "visit_date_id":patient.account_id,"user_id":user_id,"facility_id":facility_id,"local_examination":examData}
                $http.post('/api/postLocalPhysical',local_examz).then(function (data) {
                });
                swal('Local Examination','data for this patient saved','success');
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
            //family and social history
            $scope.saveSocialCommunity = function (item,patient) {
                if(patient.patient_id == null){ swal("Ooops!! no Patient selected","Please search and select patient first.."); return; }
                var child = {"patient_id":patient.patient_id,"visit_date_id":patient.account_id,"facility_id":facility_id,"user_id":user_id,"chronic_illness":item.chronic_illness,"substance_abuse":item.substance_abuse,"adoption":item.adoption,"others":item.others};
                $http.post('/api/familyHistory',child).then(function (data) {
                    swal("Family and social history data successfully Saved!","","success");
                });
                $("#chronic_illness").val('');
                $("#substance_abuse").val('');
                $("#adoption").val('');
                $("#others").val('');
            }
            //Provisional , differential and confirmed diagnosis
            $scope.diagnosisTemp =[];
            $scope.addProv = function (item,patient,status) {

                if(patient.patient_id == null){ swal("Ooops!! no Patient selected","Please search and select patient first.."); return; }
                $scope.diagnosisTemp.push({"admission_id":patient.admission_id,"patient_id":patient.patient_id,"visit_date_id":patient.account_id,"facility_id":facility_id,"user_id":user_id,"diagnosis_description_id":item.id,"description":item.description,"status":status});
            }
            $scope.addDiff = function (item,patient,status) {

                if(patient.patient_id == null){ swal("Ooops!! no Patient selected","Please search and select patient first.."); return; }
                $scope.diagnosisTemp.push({"admission_id":patient.admission_id,"patient_id":patient.patient_id,"visit_date_id":patient.account_id,"facility_id":facility_id,"user_id":user_id,"diagnosis_description_id":item.id,"description":item.description,"status":status});
            }
            $scope.addConf = function (item,patient,status) {
                if(patient.patient_id == null){ swal("Ooops!! no Patient selected","Please search and select patient first.."); return; }
                $scope.diagnosisTemp.push({"admission_id":patient.admission_id,"patient_id":patient.patient_id,"visit_date_id":patient.account_id,"facility_id":facility_id,"user_id":user_id,"diagnosis_description_id":item.id,"description":item.description,"status":status});
            }
            $scope.saveDiagnosis = function (objectData) {
                if(objectData == "") {
                    swal("Oops Data not saved!", "Please search and select items then click 'Save' button to save data..");
                    return;
                }
                $http.post('/api/postDiagnosis',objectData).then(function (data) {
                    swal("Diagnosis data successfully Saved!","","success");
                });
                $scope.diagnosisTemp =[];
            }
            //Dispositions

            var facilities =[];
            $scope.showFacility = function(searchKey) {
                $http.get('/api/getFacilities',{"searchKey":searchKey}).then(function(data) {

                    facilities = data.data;
                });
                return facilities;
            }
            $scope.exReferral = function (patient,facility,ref) {
                if(facility==null ||ref==null){
                    swal("Please fill all fields and click save","","error");return;
                }
                var ext = { "account_id": patient.account_id,"summary":ref.summary,"patient_id":patient.patient_id,"from_facility_id":facility_id,"sender_id":user_id,"to_facility_id":facility.id,"referral_type":1,"status":1};
                $http.post('/api/postReferral',ext).then(function (data) {
                    $scope.ref == null;
                    swal("Patient Referred successfully","","success");
                });

            }
            $scope.internalTransfer = function (clinic,patient) {
                var patientDetails = {"patient_id":patient.patient_id,"main_category_id":patient.main_category_id,"bill_id":patient.bill_id,"sender_clinic_id":5,"first_name":patient.first_name,"middle_name":patient.middle_name,"last_name":patient.last_name,
                    "medical_record_number":patient.medical_record_number,"gender":patient.gender,"dept_id":clinic.id,"dob":patient.dob,"visit_id":patient.account_id};
                var object = angular.extend({},clinic, patientDetails);
                var modalInstance = $uibModal.open({
                    templateUrl: '/views/modules/clinicalServices/internalTransfer.html',
                    size: 'lg',
                    animation: true,
                    controller: 'admissionModal',
                    resolve:{
                        object: function () {
                            return object;
                        }
                    }
                });
            }
            //continuation notes
            $scope.contiNotes = [];
            $scope.getNotes = function (patient) {
                $http.post('/api/getNotes',{"patient_id":patient.patient_id}).then(function (data) {
                    $scope.contiNotes = data.data;
                });
            }
            $scope.takeNotes = function (cnotes,patient) {
                if(cnotes ==null){
                    swal('Please write continuation notes for this patient','then click save notes button','error'); return;
                }
                var notes ={"notes":cnotes,"patient_id":patient.patient_id,"user_id":user_id,"facility_id":facility_id};
                $http.post('/api/postNotes',notes).then(function (data) {
                    $scope.getNotes(patient);
                });
                swal("Continuation notes successfully saved!","","success");

                $('#notes').val('');
            }
            $scope.loadIndex = 4;
            $scope.showMore = function() {
                if ($scope.loadIndex < $scope.contiNotes.length) {
                    $scope.loadIndex += 2;
                }
            }
            $scope.showLess = function() {
                if ($scope.loadIndex > 2) {
                    $scope.loadIndex -= 2;
                }
            }
            //Treatments:medication and procedures
            var mediData =[];
            $scope.medicines =[];
            $scope.medicinesOs =[];
            $scope.searchItems = function(searchKey,patient) {
                var pay_id =patient.bill_id;
                if(pay_id==null){
                    swal("Please search patient to be prescribed before searching Medicine!");
                    return;
                }
                if(patient.main_category_id == 3){
                    pay_id = 1;
                }
                $http.post('/api/getMedicine',{"search":searchKey,"facility_id":facility_id,"patient_category_id":pay_id}).then(function(data) {
                    mediData = data.data;

                });
                return mediData;

            }
            var balance=[];
            $scope.checkDosage = function (item_id,patient_id) {
                var item_name=item_id.item_name;
                $http.post('/api/dosageChecker',{"item_id":item_id.item_id,"patient_id":patient_id}).then(function (data) {
                    //console.log(data.data);
                    if(data.data.length>0){
                        var diff = data.data[0].duration - data.data[0].days;
                        $scope.dosageCheck=data.data;
                        swal('ATTENTION',item_name+' In Dosage Progress '+ diff +' day(s) remained to Complete this Dosage','info');
                    }
                });


            }


            $scope.stopMedication = function(prescription_id) {
                if (prescription_id==undefined) {
                    swal("Something went wrong with this database setup, please contact for technical support", "", "error");
                    return;
                }
                $http.post('/api/stopMedication',{user_id:user_id,patient_id:prescription_id.patient_id,prescription_id:prescription_id.prescription_id}).then(function(data) {
                    $scope.prevMedicines = data.data;
                    swal("Patient prescription successfully stoped!", "", "success");
                });


            }
            $scope.addMedicine = function (item,patient,dawa) {
				
                var status_id = 1;
                var filter = patient.bill_id;
                var main_category = patient.main_category_id;
				
                var quantity = item.dose*item.duration*24/item.frequency;
                if(patient ==null){ swal("Please search and select Patient to prescribe"); return; }
                if(dawa ==null){ swal("Please search and select medicine!"); return;}
                if(item.instructions ==null){ swal("Please Write Instructions and click 'Add to List' Button","","error"); return;}
                for(var i=0;i<$scope.medicines.length;i++)
                    if($scope.medicines[i].item_id == dawa.item_id){ swal(dawa.item_name+" already in your order list!"); return;}
                if(main_category != 1 && dawa.exemption_status == 0){
                    filter = patient.bill_id;
                }
                if(main_category == 3  && dawa.exemption_status == 1){
                    filter = 1;
                }
                if(main_category == 2  && dawa.exemption_status == 1){
                    filter = patient.bill_id;
                }
                if(main_category == 3){  main_category=1;}
                $http.post('/api/balanceCheck',{"main_category_id":main_category,"item_id":dawa.item_id,"facility_id":facility_id, "user_id": user_id}).then(function (data) {
                    balance = data.data;
                    if(balance.length >0 && balance[0].balance>=quantity){
                        $scope.medicines.push({"admission_id":patient.admission_id,"visit_id":patient.account_id,"facility_id":facility_id,"item_type_id":dawa.item_type_id,"item_price_id":dawa.price_id,"quantity":quantity,"status_id":status_id,
                            "dose":item.dose,"frequency":item.frequency,"duration":item.duration,"instructions":item.instructions,"out_of_stock":"","payment_filter":filter,
                            "account_number_id":patient.account_id,"patient_id":patient.patient_id,"user_id":user_id,"item_id":dawa.item_id,"item_name":dawa.item_name,"dose_formulation":dawa.dose_formulation
                        });						
						$("#item_search").val('');
					$("#dose").val('');
					$("#frequency").val('');
					$("#duration").val('');
					$("#instruction").val('');
                    }
                    else if (balance.length<1 || balance[0].balance<quantity){
                        var conf= confirm("This Item is not available in Store..Do you want to prescribe anyway?");
                        if(conf== true){
                            for(var i=0;i<$scope.medicinesOs.length;i++)
				if($scope.medicinesOs[i].item_id == dawa.item_id){ swal("Item already in your order list!"); return;}
			$scope.medicinesOs.push({"admission_id":patient.admission_id,"visit_id":patient.account_id,"facility_id":facility_id,"item_type_id":dawa.item_type_id,"item_price_id":dawa.price_id,"quantity":quantity,"status_id":status_id,
				"dose":item.dose,"frequency":item.frequency,"duration":item.duration,"instructions":item.instructions,"out_of_stock":"OS",
				"account_number_id":patient.account_id,"patient_id":patient.patient_id,"user_id":user_id,"item_id":dawa.item_id,"item_name":dawa.item_name
			});
			swal("Item added under Out of Stock category","","success");
                        }else {
                            swal("canceled","Choose different Item for Prescription","info");
							$("#item_search").val('');
							$("#dose").val('');
							$("#frequency").val('');
							$("#duration").val('');
							$("#instruction").val('');
                            return;
                        }
                    }
                });
                $("#item_search").val('');
                $("#dose").val('');
                $("#frequency").val('');
                $("#duration").val('');
                $("#instruction").val('');
            }
            $scope.saveMedicine = function () {
                if($scope.medicines == "" && $scope.medicinesOs == ""){
                    swal("No Items to Save,Please choose items..","","error");
                    return;
                }
                if($scope.medicines !=""){
                    $http.post('/api/postMedicines',$scope.medicines).then(function (data) {

                    });
                    $scope.medicines = [];
                }
                $http.post('/api/outOfStockMedicine',$scope.medicinesOs).then(function (data) {

                });
                swal("Patient successfully prescribed!","","success");
                $scope.medicinesOs = [];
            }
            $scope.prevMedics = function (item) {
                $http.post('/api/getPrevMedicine',{"patient_id":item.patient_id}).then(function (data) {
                    $scope.prevMedicines = data.data;
                });
            }
            $scope.prevProcedure = function (item) {

                $http.post('/api/getPrevProcedures',{"patient_id":item.patient_id}).then(function (data) {
                    $scope.prevProcedures = data.data;
                });
            }
            //medical supplies starts
            var supplies = [];
            var balance02 = [];
            $scope.supplies=[];
            $scope.suppliesOS=[];
            $scope.searchMediSupplies = function(searchKey,patient) {
                var pay_id = patient.bill_id;
                if (pay_id == null) {swal("Please search patient before searching medical supplies!");return;}
                if (patient.main_category_id == 3) {pay_id = 1; }
                $http.post('/api/getMedicalSupplies', {"search": searchKey,"facility_id": facility_id,"patient_category_id": pay_id}).then(function (data) {
                    supplies = data.data;
                });
                return supplies;
            }
            $scope.addSupplies = function (patient,qty,item) {
                var status_id = 1;
                var filter = patient.bill_id;
                var main_category = patient.main_category_id;

                var quantity =qty;
                if(patient ==null){ swal("Please search and select Patient","","error"); return; }
                if(item ==null){
                    swal("Please search and select Medical supplies!","","error"); return;
                }
                for(var i=0;i<$scope.supplies.length;i++)
                    if($scope.supplies[i].item_id == item.item_id){ swal(item.item_name+" already in your order list!","","info"); return;}
                if(main_category != 1 && item.exemption_status == 0){
                    filter = patient.bill_id;
                }
                if(main_category == 3  && item.exemption_status == 1){
                    filter = patient.bill_id;
                }
                if(main_category == 2  && item.exemption_status == 1){
                    filter = patient.bill_id;
                }
                if(main_category == 3){  main_category=1;}
                $http.post('/api/balanceCheck',{"main_category_id":main_category,"item_id":item.item_id,"facility_id":facility_id, "user_id": user_id}).then(function (data) {
                    balance02 = data.data;
                    if(balance02.length<1){
                        swal(item.item_name +' is not available in store.','Contact store manager','info');
                        return;
                    }
                    else if(balance02.length >0 && balance02[0].balance>=quantity){
                        $scope.supplies.push({"out_of_stock":'',"payment_filter":filter,"admission_id":patient.admission_id,"visit_id":patient.account_id,"facility_id":facility_id,"item_type_id":item.item_type_id,"item_price_id":item.price_id,"quantity":qty,"status_id":status_id,
                            "account_number_id":patient.account_id,"patient_id":patient.patient_id,"user_id":user_id,"item_id":item.item_id,"item_name":item.item_name
                        });
                    }
                    else if (balance02.length<1 || balance02[0].balance<quantity){
                        var conf= confirm("This Item is not available in Store..Do you want to select it anyway?","","info");
                        if(conf== true){
                            for(var i=0;i<$scope.suppliesOS.length;i++)
                                if($scope.suppliesOS[i].item_id == item.item_id){ swal(item.item_name+" already in your order list!"); return;}
                            $scope.suppliesOS.push({"out_of_stock":'OS',"payment_filter":filter,"admission_id":patient.admission_id,"visit_id":patient.account_id,"facility_id":facility_id,"item_type_id":item.item_type_id,"item_price_id":item.price_id,"quantity":qty,"status_id":status_id,
                                "account_number_id":patient.account_id,"patient_id":patient.patient_id,"user_id":user_id,"item_id":item.item_id,"item_name":item.item_name
                            });
                            swal("Item added under Out of Stock category","","success");
                        }else {
                            swal("canceled","Choose different Item","info");
                            return;
                        }
                    }
                });
                $("#supplies").val('');
                $("#qty").val('');
            }
            $scope.saveSupplies = function (){
                if($scope.supplies == "" && $scope.suppliesOS == ""){
                    swal("No Items to Save,Please choose items..","","error");
                    return;
                }
                if($scope.supplies !=""){
                    $http.post('/api/postMedicalSupplies',$scope.supplies).then(function (data) {

                    });
                    $scope.supplies = [];
                }
                $http.post('/api/outOfStockMedicalSupplies',$scope.suppliesOS).then(function (data) {

                });
                swal("Patient's medical supplies successfully saved!","","success");
                $scope.suppliesOS = [];
            }
            //medical supplies ends
            //procedures
            var procedureData =[];
            $scope.procedures =[];
            $scope.searchProcedures = function(searchKey,patient) {
                var pay_id = patient.bill_id;
                if (pay_id == null) {swal("Please search patient before searching procedures!");return;}
                if (patient.main_category_id == 3) {pay_id = 1; }
                $http.post('/api/getPatientProcedures', {"search": searchKey,"facility_id": facility_id,"patient_category_id": pay_id}).then(function (data) {
                    procedureData = data.data;
                });
                return procedureData;
            }
            $scope.getDefaultProcedures = function (patient) {
                var pay_id = patient.bill_id;
                if (pay_id == null) {swal("Please search patient before searching procedures!");return;}
                if (patient.main_category_id == 3) {pay_id = 1; }
                $http.post('/api/getProcedures',{"facility_id": facility_id,"patient_category_id": pay_id}).then(function (data) {
                    $scope.defaultProcedures = data.data;
                });
            }
            $scope.addProcedure = function (item,patient) {

                var filter = patient.bill_id;
                var status_id = 1;
                var main_category = patient.main_category_id;
                if(patient.patient_id ==null){ swal("Please search and select Patient to prescribe"); return; }
                if(item.item_id ==null){ swal("Please search and select Procedure!"); return;}
                for(var i=0;i<$scope.procedures.length;i++)
                    if($scope.procedures[i].item_id == item.item_id){ swal(item.item_name+" already in your order list!","","info"); return;}
                if(main_category != 1 && item.exemption_status == 0){
                    filter = patient.bill_id;
                }
                if(main_category == 3  && item.exemption_status == 1){
                    filter = patient.bill_id;
                }
                if(main_category == 2  && item.exemption_status == 1){
                    filter = patient.bill_id;
                }


                $scope.procedures.push({"payment_filter":filter,"admission_id":patient.admission_id,"facility_id":facility_id,"item_type_id":item.item_type_id,"item_price_id":item.price_id,"quantity":1,"status_id":status_id,
                    "account_number_id":patient.account_id,"patient_id":patient.patient_id,"user_id":user_id,"item_id":item.item_id,"item_name":item.item_name
                });
            }
            $scope.saveProcedures = function (objectData) {
                console.log(objectData)
                $http.post('/api/postPatientProcedures',objectData).then(function (data) {

                });
                swal("Patient procedures successfully saved!","","success");
                $scope.procedures = [];
            }

           $scope.deceased = function (item,corpse,diag) {
                if(angular.isDefined(corpse)==false){
                    swal("An error occurred","Data not saved...Please write causes of death and click send to last office button","error");return;
                }
                var deceased = {"first_name":item.first_name,
                    "diagnosis_id":diag.id,
                    "diagnosis_code":diag.code,"residence_id":item.residence_id,"middle_name":item.middle_name,"last_name":item.last_name,
                    "patient_id":item.patient_id,"user_id":user_id,"facility_id":facility_id,"immediate_cause":corpse.immediate_cause,
                    "underlying_cause":corpse.underlying_cause,"dept_id":5};
                $http.post('/api/certifyCorpse',deceased).then(function (data) {                    if(data.data.status ==0){

                        swal(data.data.data, "", "error");
                    }
                    else{
                        swal(item.first_name+' '+item.last_name+" sent to Last office","","success");
                    }
                });
                $("#immediate_cause").val('');
                $("#underlying_cause").val('');
                swal(item.first_name+' '+item.last_name+" sent to Last office","","success");
            }



        }
    ]);
})();