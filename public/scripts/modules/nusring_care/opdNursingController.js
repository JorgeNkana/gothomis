(function() {

    'use strict';

    angular
        .module('authApp')
        .controller('opdNursingController',opdNursingController);

    function opdNursingController($http, $auth, $rootScope,$state,$location,$scope,$timeout,$mdDialog, Helper) {

        //loading menu
        var user_id=$rootScope.currentUser.id;
        var facility_id=$rootScope.currentUser.facility_id;
        $http.get('/api/getUsermenu/'+user_id ).then(function(data) {
            $scope.menu=data.data;
            ////console.log($scope.menu);

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

        var PoSata =[];
        $scope.searchItems = function(searchKey) {

            $http.post('/api/opd_nurse_service',{"search":searchKey}).then(function(data) {
                PoSata = data.data;
            });
            return PoSata;
        }
        $scope.checkServicePaymentStatus = function(item) {

            $http.post('/api/checkServicePaymentStatus',{"item_id":item.selectedItem.id, "main_category_id":item.selectedPatient.main_category_id, "patient_category_id":item.selectedPatient.patient_category_id,visit_id: item.selectedPatient.account_id}).then(function(data) {
                 data.data;
            });
            return PoSata;
        }
        $scope.SaveOpdService = function(item) {

if(item==undefined){
    swal('','Fill All Fields','error')
    return;
}
            if(item.selectedItem==undefined){
    swal('','Choose Service','error')
    return;
}
            if(item.periodic==undefined){
    swal('','Enter Frequency of Having This Service','error');
    return;
}
            if(item.periodic==undefined){
    swal('','Enter Frequency of Having This Service','error');
    return;
}
            if(item.duration==undefined){
    swal('','Enter Duration of This Service','error');
    return;
}   if(item.route==undefined){
    swal('','Enter Route This Service','error');
    return;
} if(item.service_type==undefined){
    swal('','Choose Service Type Given','error');
    return;
}
            var status=1;
            if(item.periodic>1){
                status=0;
            }
            $http.post('/api/SaveOpdService',{route:item.route,duration:item.duration,start:true,status:status,facility_id:facility_id,user_id:user_id,service_type:item.service_type,periodic:item.periodic,"item_id":item.selectedItem.id,visit_id: item.selectedPatient.account_id,patient_id: item.selectedPatient.patient_id}).then(function(data) {
                 var msg=data.data.msg;
                 var status=data.data.status;
                $scope.getOnGoingDosage();
                if(status==1){
                    swal('',msg,'success')
                }
                else{
                    swal('',msg,'error')
                }
            });

        }

		
		
		$scope.SaveOpdServiceOnservice = function(item) {
 
if(item==undefined){
    swal('','Fill All Fields','error')
    return;
}
            if(item.item_id==undefined){
    swal('','Choose Service','error')
    return;
}
            if(item.periodic==undefined){
    swal('','Enter Frequency of Having This Service','error');
    return;
}
            if(item.periodic==undefined){
    swal('','Enter Frequency of Having This Service','error');
    return;
}
            if(item.duration==undefined){
    swal('','Enter Duration of This Service','error');
    return;
}   if(item.route==undefined){
    swal('','Enter Route This Service','error');
    return;
} if(item.service_type==undefined){
    swal('','Choose Service Type Given','error');
    return;
}
            var status=1;
            if(item.periodic>1){
                status=0;
            }
            $http.post('/api/SaveOpdService',{route:item.route,duration:item.duration,start:true,status:status,facility_id:facility_id,user_id:user_id,service_type:item.service_type,periodic:item.periodic,"item_id":item.item_id,visit_id: item.visit_id,patient_id: item.patient_id}).then(function(data) {
                 var msg=data.data.msg;
                 var status=data.data.status;
                $scope.getOnGoingDosage();
                if(status==1){
                    swal('',msg,'success')
                }
                else{
                    swal('',msg,'error')
                }
            });

        }
		
		
        $scope.getPatientReport = function(item) {
           $http.post('/api/getPastMedicine', {

                    "patient_id": item.patient_id,

                    "visit_date_id": item.account_id

                }).then(function(data) {

                    $scope.prevMedicines = data.data;

                });


            $http.post('/api/getPastProcedures', {

                "patient_id": item.patient_id,

                "visit_date_id": item.account_id

            }).then(function(data) {

                $scope.pastProcedures = data.data;

            });
        } 
		$scope.getOnGoingDosage = function() {
            $http.get('/api/getOnGoingDosage/'+facility_id).then(function(data) {
                $scope.dosages = data.data;
            });

        }
        $scope.opd_nursing_report = function(data) {
            $http.post('/api/opd_nursing_report',{facility_id:facility_id,data:data}).then(function(data) {
                $scope.stages = data.data;
            });

        }
        $scope.cancel_opd_dosage = function(patient_id) {

            swal({
                title: 'Are you sure You Want To Cancel Patient Dosage?',

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

                $http.get('/api/cancel_opd_dosage/'+patient_id).then(function(data) {
                    $scope.canceled = data.data;
                    var msg=data.data.msg;
                    var status=data.data.status;

                    if(status==1){
                        swal('',msg,'success')
                    }
                    else{
                        swal('',msg,'error')
                    }
                });

            }, function (dismiss) {
                // dismiss can be 'cancel', 'overlay',
                // 'close', and 'timer'
                if (dismiss === 'cancel') {

                }
            })





        }
        $scope.getOnGoingDosage();
        $scope.loadPatientDosagePregres = function(patient) {
            $http.get('/api/loadPatientDosagePregres/'+patient.visit_id).then(function(data) {
                $scope.dosageprogres = data.data;
            });
			$http.post('/api/getPastMedicine', {

                    "patient_id": patient.patient_id,

                    "visit_date_id": patient.visit_id

                }).then(function(data) {

                    $scope.prevMedicines = data.data;

                });

        }
        $scope.ViewProgressDosage = function(patient) {

            $mdDialog.show({
                controller: function ($scope) {
                    $scope.selectedPatient = item;

                    $http.post('/api/ViewProgressDosage',{visit_id:patient.visit_id,item_id:patient.item_id}).then(function(data) {
                        $scope.dosageitems = data.data;
                    });
                    $scope.searchItems = function(searchKey) {

                        $http.post('/api/opd_nurse_service',{"search":searchKey}).then(function(data) {
                            PoSata = data.data;
                        });
                        return PoSata;
                    }
                    // $scope.checkServicePaymentStatus = function(item) {
                    //
                    //     $http.post('/api/checkServicePaymentStatus',{"item_id":item.selectedItem.id, "main_category_id":item.selectedPatient.main_category_id, "patient_category_id":item.selectedPatient.patient_category_id,visit_id: item.selectedPatient.account_id}).then(function(data) {
                    //         data.data;
                    //     });
                    //     return PoSata;
                    // }
                    $scope.SaveOpdService = function(item) {
                        var status=1;
                        if(patient.periodic>1){
                            status=0;
                        }
                        $http.post('/api/SaveOpdService',{route:patient.route,duration:patient.duration,start:false,status:status,facility_id:facility_id,user_id:user_id,service_type:item.service_type,periodic:patient.periodic,"item_id":item.item_id,visit_id:patient.visit_id,patient_id:patient.patient_id}).then(function(data) {
                            var msg=data.data.msg;
                            var status=data.data.status;

                            if(status==1){
                                swal('',msg,'success')
                            }
                            else{
                                swal('',msg,'error')
                            }
                        });

                    }

                    $scope.cancel = function () {
                        $mdDialog.hide();

                    };




                },
                templateUrl: '/views/modules/nursing_care/opd_nursing_continuity.html',
                parent: angular.element(document.body),
                clickOutsideToClose: true,
                fullscreen: false,
            });

        }
        $scope.ViewDosageCompleteness = function(patient) {

            $mdDialog.show({
                controller: function ($scope) {
                    $scope.selectedPatient = item;

                    $http.post('/api/ViewDosageCompleteness',{visit_id:patient.visit_id,item_id:patient.item_id}).then(function(data) {
                        $scope.dosagecompletenesses = data.data;
                    });

                    $scope.cancel = function () {
                        $mdDialog.hide();

                    };




                },
                templateUrl: '/views/modules/nursing_care/opd_nursing_continuity.html',
                parent: angular.element(document.body),
                clickOutsideToClose: true,
                fullscreen: false,
            });

        }

        $scope.print_opd_nurse=function () {
            //location.reload();
            var DocumentContainer = document.getElementById('id_opd');
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


})();