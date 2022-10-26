/**
 * Created by Mazigo Jr on 2017-08-14.
 */

(function() {

    'use strict';

    angular
        .module('authApp')


.controller('patient_tracerController',patient_tracerController);

    function patient_tracerController($http, $auth, $rootScope,$state,$location,$scope,$timeout,$mdDialog, Helper) {

        //loading menu
        var user_id=$rootScope.currentUser.id;
        var facility_id=$rootScope.currentUser.facility_id;
        $http.get('/api/getUsermenu/'+user_id ).then(function(data) {
            $scope.menu=data.data;
            $scope.cardTitle=data.data[0];
            

        });

        $http.get('/api/getLoginUserDetails/' + user_id).then(function (data) {
            $scope.loginUserFacilityDetails = data.data;  


        });
         $scope.Patient_tracer=function (item) {
             $http.post('/api/Patient_tracer',{start_date:item.start_date,end_date:item.end_date,facility_id:facility_id}).then(function(data) {
                 $scope.tracer_lists=data.data;
                 ////console.log($scope.menu);

             });

         }
         $scope.Patient_nhif_tracer=function (item) {
             $http.post('/api/Patient_nhif_tracer',{start_date:item.start_date,end_date:item.end_date,facility_id:facility_id}).then(function(data) {
                 $scope.nhif_tracer_lists=data.data;
                 ////console.log($scope.menu);

             });

         }


        $scope.Patient_nhif_service_tracer = function (searchKey,dates) {

            $mdDialog.show({
                controller: function ($scope) {
                    $scope.SelectedClient = searchKey;
                    $scope.cancel = function () {
                        $mdDialog.hide();

                    };
                    $http.post('/api/Patient_nhif_service_tracer',{patient_id:searchKey.patient_id,start_date:dates.start_date,end_date:dates.end_date}).then(function(data) {
                        $scope.receptions=data.data;
                        ////console.log($scope.menu);

                    });
                },
                templateUrl: '/views/modules/patient_tracing/nhifInterface.html',
                parent: angular.element(document.body),
                clickOutsideToClose: true,
                fullscreen: false,
            });

        }

        $scope.View_patient_flow = function (searchKey) {

            $mdDialog.show({
                controller: function ($scope) {
                    $scope.SelectedClient = searchKey;
                    $scope.cancel = function () {
                        $mdDialog.hide();

                    };
                    $http.post('/api/Patient_flow',{visit_id:searchKey.visit_id}).then(function(data) {
                        $scope.receptions=data.data;
                        ////console.log($scope.menu);

                    });
                },
                templateUrl: '/views/modules/patient_tracing/patient_trace_view.html',
                parent: angular.element(document.body),
                clickOutsideToClose: true,
                fullscreen: false,
            });

        }


        $scope.getPatientToEncounter = function (text) {
            return Helper.getPatientToEncounter(text)
                .then(function (response) {
                    return response.data;
                });
        };

         $scope.getVisits=function(patient){
             $scope.selectedPatient = patient;
             $scope.getPatientReportPrinted(patient);
             $http.post('/api/previousVisits',{"patient_id":patient.id}).then(function (data) {
                 $scope.patientsVisits = data.data;
                 $scope.selectedPatient = patient;


             });
         }

 $scope.getReferrals=function(patient){
             $scope.selectedPatient = patient;
             
             $http.post('/api/getReferralLists',{start_date:patient.start_date,end_date:patient.end_date,patient_id:patient.id}).then(function (data) {
                 $scope.referrals = data.data[0];
                 $scope.referralsout = data.data[1];
                 $scope.incomings = data.data[2];
                 $scope.referralsin = data.data[3];
                 $scope.totalIn=$scope.TotalIn();
                 $scope.totalOut=$scope.TotalOut();
                 $scope.selectedPatient = patient;


             });
         }
$scope.TotalOut=function(){
    var sum=0;
    for (var i = 0 ; i <$scope.referralsout.length; i++) {
        sum -=-($scope.referralsout[i].total);
    }
    return sum;
}

$scope.TotalIn=function(){
    var sum=0;
    for (var i = 0 ; i <$scope.referralsin.length; i++) {
        sum -=-($scope.referralsin[i].total);
    }
    return sum;
}


         $scope.getReferralDetails=function(records){
            $scope.referral_code = records.referral_code;
             $scope.refdata = records;
             
             
            
         }
 
                         $scope.PrintContent1 = function() {
                             
                        var DocumentContainer = document.getElementById('refId');
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

        $scope.demographicDetails=function(item) {
            $http.post('/api/demographicDetails',{start_date:item.start_date,end_date:item.end_date,facility_id:facility_id}).then(function (data) {
                $scope.demographs = data.data;

            });
        }

        $scope.getPatientReportPrinted = function(item) {

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

        $scope.print_form=function () {

            //location.reload();
            var DocumentContainer = document.getElementById('printable');
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

        $scope.printDiagnosis=function () {

            //location.reload();
            var DocumentContainer = document.getElementById('diagnosis_id');
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

        $scope.DiagnosisLIstDATA=function(item) {
            Helper.overlay(true)

            $http.post('/api/DiagnosisLIst',{start_date:item.start_date,end_date:item.end_date,facility_id:facility_id}).then(function (data) {
                $scope.diagnoses = data.data;
                Helper.overlay(false);
            }, function(data){Helper.overlay(false);});
        }
        $scope.DiagnosisLIstDATA1=function(item) {
            Helper.overlay(true);
            $http.post('/api/DiagnosisLIst',{code:item.code,facility_id:facility_id}).then(function (data) {
                $scope.diagnoses = data.data;
                Helper.overlay(false);
            }, function(data){Helper.overlay(false);});
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

    }

})();