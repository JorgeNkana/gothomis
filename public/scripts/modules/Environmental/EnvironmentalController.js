/**
 * Created by USER on 2017-08-12.
 */

(function() {

    'use strict';

    angular
        .module('authApp')
        .controller('EnvironmentalController',EnvironmentalController);

    function EnvironmentalController($http, $auth, $rootScope,$state,$location,$scope,$timeout,$mdDialog,Helper) {

        //loading menu
        var user_id = $rootScope.currentUser.id;
        var facility_id = $rootScope.currentUser.facility_id;
        $http.get('/api/getUsermenu/' + user_id).then(function (data) {
            $scope.menu = data.data;


        });
        $http.get('/api/getLoginUserDetails/' + user_id).then(function (data) {
            $scope.loginUserFacilityDetails = data.data;

        });
        var diag = [];

        $scope.showDiagnosis = function (search) {

            $http.post('/api/getDiagnosis', {

                "search": search

            }).then(function (data) {

                diag = data.data;

            });

            return diag;

        }

        $scope.diagnosisTemp = [];
        $scope.addNotifiable = function (item) {
            $('#disease').val('');
            for (var i = 0; i < $scope.diagnosisTemp.length; i++) {

                if ($scope.diagnosisTemp[i].diagnosis_id == item.id) {

                    return;
                }
            }
            $scope.diagnosisTemp.push({


                "diagnosis_id": item.id,
                "description": item.description,

            });

        }
        $scope.removeFromSelection = function (item, objectdata) {

            var indexremoveobject = objectdata.indexOf(item);

            objectdata.splice(indexremoveobject, 1);

        }

        $scope.save_notifiable_Diagnosis = function () {
            $http.post('/api/save_notifiable_Diagnosis', $scope.diagnosisTemp).then(function (data) {
                $scope.diagnosisTemp = [];
                $scope.save_notifiable_Diagnosis_list();
                var sending = data.data.msg;
                var statusee = data.data.status;
                if (statusee != 1) {
                    swal(
                        'Error',
                        sending,
                        'error'
                    )

                }
                else {
                    swal(
                        'Feedback..',
                        sending,
                        'success'
                    )

                }
            });
        }

        $scope.nuisance_list = function () {
            $http.get('/api/nuisance_list').then(function (data) {
                $scope.nuisances = data.data;
            });

        }
        $scope.save_notifiable_Diagnosis_list=function () {
        $http.get('/api/save_notifiable_Diagnosis_list').then(function (data) {
            $scope.notifiables = data.data;
        });
    }

    $scope.recent_notified_disease=function () {
        $http.get('/api/recent_notified_disease/'+facility_id).then(function (data) {
            $scope.recent_notifiables = data.data;
        });
    }
        $scope.recent_notified_disease();
    $scope.patient_notifed_Diagnosis_list=function (data) {
        var getdata={facility_id:facility_id,start_date:data.start_date,end_date:data.end_date}
        $http.post('/api/patient_notifed_Diagnosis_list',getdata).then(function (data) {
            $scope.patient_notified = data.data;
        });
    }
        $scope.ant_rabies_monitoring=function (data) {
        var getdata={facility_id:facility_id,start_date:data.start_date,end_date:data.end_date}
        $http.post('/api/ant_rabies_monitoring',getdata).then(function (data) {
            $scope.patient_rabies = data.data;
        });
    }
        $scope.summary_out_break_disease_deaths=function (data) {
        var getdata={facility_id:facility_id,start_date:data.start_date,end_date:data.end_date}
        $http.post('/api/summary_out_break_disease_death',getdata).then(function (data) {
            $scope.notifiable_disease_death = data.data;
            
        });
    }
    $scope.patient_notifed_Diagnosis_freq=function (data) {
        var getdata={facility_id:facility_id,start_date:data.start_date,end_date:data.end_date}
        $http.post('/api/patient_notifed_Diagnosis_freq',getdata).then(function (data) {
            $scope.notified_feq = data.data;
            $scope.total_notified=Total_notified($scope.notified_feq);
        });
    }
 $scope.patient_notified_admision_status=function (visit_id) {

        $http.get('/api/patient_notified_admision_status/'+visit_id).then(function (data) {
            $scope.admission_status = data.data[0];
            var admissition= "ADMISSION STATUS";
            if(data.data.length>0){
                var ward= "Ward Name: " +$scope.admission_status.ward_name+"   "+"Bed Name: " +$scope.admission_status.bed_name;

                swal(admissition,ward,'success')  ;
            } else{

                swal(admissition,'PATIENT HAS NOT YET ADMITED','info')  ;
            }

        });
    }

        $scope.save_notifiable_Diagnosis_list();
        $scope.waste_type_list=function () {
            $http.get('/api/waste_type_list').then(function (data) {
                $scope.waste_types = data.data;
            });
        }
        $scope.waste_dispose_list=function () {
            $http.get('/api/waste_dispose_list').then(function (data) {
                $scope.waste_disposes = data.data;
            });
        }
        $scope.equipment_type_list=function () {
            $http.get('/api/equipment_type_list').then(function (data) {
                $scope.equipment_types = data.data;
            });
        }
        $scope.equipment_list=function () {
            $http.get('/api/environment_equipment_list/'+facility_id).then(function (data) {
                $scope.equipments = data.data;
            });
        }
        $scope.environment_Receiving_issuing_summary=function () {
            $http.get('/api/environment_Receiving_issuing_summary/'+facility_id).then(function (data) {
                $scope.summary_received = data.data[0];
                $scope.summary_issued = data.data[1];


            });
        }

        $scope.environment_Receiving_issuing_summary();
        $scope.equipment_received_list=function (data) {
            var geequip={facility_id:facility_id,start_date:data.start_date,end_date:data.end_date}
            $http.post('/api/equipment_received_list',geequip).then(function (data) {
                $scope.receives = data.data;
            });
        } 
        $scope.wastes_collected=function (data) {
            var waste_colect={facility_id:facility_id,start_date:data.start_date,end_date:data.end_date}
            $http.post('/api/wastes_collected',waste_colect).then(function (data) {
                $scope.wastes_collections = data.data;
                $scope.total_waste_collected=wastes_collectionsTotal($scope.wastes_collections);

                $scope.xs = [];
                $scope.ys = [];

                for (var i = 0; i < $scope.wastes_collections.length; i++) {
                    $scope.xs.push($scope.wastes_collections[i].waste_type);
                    $scope.ys.push($scope.wastes_collections[i].waste_collected);
                }

                $scope.labels = $scope.xs;
                $scope.data = $scope.ys;
            });
        }

        var wastes_collectionsTotal = function(){
            var total = 0;

            for(var i=0; i<$scope.wastes_collections.length;i++){
                total -= -($scope.wastes_collections[i].waste_collected);
            }

            return total;

        }
        $scope.waste_disposal_list=function (data) {
            var waste_dispose={facility_id:facility_id,start_date:data.start_date,end_date:data.end_date}
            $http.post('/api/waste_disposal_list',waste_dispose).then(function (data) {
                $scope.wastes_dispositions = data.data;
                $scope.total_waste_disposed=wastes_dispositionTotal($scope.wastes_dispositions);

                $scope.xs = [];
                $scope.ys = [];

                for (var i = 0; i < $scope.wastes_dispositions.length; i++) {
                    $scope.xs.push($scope.wastes_dispositions[i].waste_type);
                    $scope.ys.push($scope.wastes_dispositions[i].waste_disposed);
                }

                $scope.labels = $scope.xs;
                $scope.data = $scope.ys;
            });
        }


        var wastes_dispositionTotal = function(){
            var total = 0;

            for(var i=0; i<$scope.wastes_dispositions.length;i++){
                total -= -($scope.wastes_dispositions[i].waste_disposed);
            }

            return total;

        }

        var Total_notified = function(){
            var total = 0;

            for(var i=0; i<$scope.notified_feq.length;i++){
                total -= -($scope.notified_feq[i].total);
            }

            return total;

        }
        $scope.equipment_issued_list=function (data) {
            var geequip={facility_id:facility_id,start_date:data.start_date,end_date:data.end_date}
            $http.post('/api/equipment_issued_list',geequip).then(function (data) {
                $scope.issues = data.data;


            });
        }

        $scope.nuisance_composed=function (data) {

            var getNuisance={facility_id:facility_id,start_date:data.start_date,end_date:data.end_date}
            $http.post('/api/nuisance_composed',getNuisance).then(function (data) {
                $scope.nuisance_compose = data.data;


            });
        }
        $scope.equipment_balances=function (data) {

            var getbalance={facility_id:facility_id}
            $http.post('/api/equipment_balances',getbalance).then(function (data) {
                $scope.balances = data.data;


            });
        }
        
        $scope.nuisance_list();
        $scope.equipment_balances();
        $scope.equipment_type_list();
        $scope.waste_dispose_list();

        $scope.waste_type_list();
        $scope.equipment_list();
        $scope.nuisance_registration=function (nuisance) {
            if(nuisance==undefined){
                swal(
                    'Error',
                    'Please Fill This Field',
                    'error'
                )
                return
            }
            $http.post('/api/nuisance_registration',nuisance).then(function(data) {
                $scope.nuisances=data.data;
                $('#nuisance').val('');
                $scope.nuisance_list();
                var sending = data.data.msg;
                var statusee = data.data.status;
                if (statusee == 0) {
                    swal(
                        'Error',
                        sending,
                        'error'
                    )

                }
                else {
                    swal(
                        'Feedback..',
                        sending,
                        'success'
                    )

                }

            });
        }
        $scope.nuisance_update=function (nuisance) {
            $http.post('/api/nuisance_update',nuisance).then(function(data) {
                $scope.nuisances=data.data;
                $scope.nuisance_list();
                var sending = data.data.msg;
                var statusee = data.data.status;
                if (statusee == 0) {
                    swal(
                        'Error',
                        sending,
                        'error'
                    )

                }
                else {
                    swal(
                        'Feedback..',
                        sending,
                        'success'
                    )

                }

            });
        }
         $scope.environment_equipment_type_registration=function (equipment) {
            if(equipment==undefined){
                swal(
                    'Error',
                    'Please Fill This Field',
                    'error'
                )
                return
            }
            $http.post('/api/environment_equipment_type_registration',equipment).then(function(data) {
                $scope.equipment_types=data.data;
                $('#equip').val('');
                $scope.equipment_type_list();
                var sending = data.data.msg;
                var statusee = data.data.status;
                if (statusee == 0) {
                    swal(
                        'Error',
                        sending,
                        'error'
                    )

                }
                else {
                    swal(
                        'Feedback..',
                        sending,
                        'success'
                    )

                }

            });
        }
        $scope.equipment_type_update=function (equip) {
            $http.post('/api/equipment_type_update',equip).then(function(data) {

                $scope.equipment_type_list();
                var sending = data.data.msg;
                var statusee = data.data.status;
                if (statusee == 0) {
                    swal(
                        'Error',
                        sending,
                        'error'
                    )

                }
                else {
                    swal(
                        'Feedback..',
                        sending,
                        'success'
                    )

                }

            });
        }

        $scope.environment_waste_collection=function (waste) {
            console.log(waste)
            if(waste==undefined){
                swal(
                    'Error',
                    'Please Fill This Field',
                    'error'
                )
                return
            }
            if(waste.waste_type==undefined){
                swal(
                    'Error',
                    'Please Choose Waste Type',
                    'error'
                )
                return
            } if(waste.waste_collected==undefined){
                swal(
                    'Error',
                    'Please Enter Waste Collected',
                    'error'
                )
                return
            }
            if(waste.equipment==undefined){
                swal(
                    'Error',
                    'Please Choose Equipment Used To Collect',
                    'error'
                )
                return
            }

            var wastes={waste_type_id:waste.waste_type,waste_collected:waste.waste_collected,equipment_used_id:waste.equipment,facility_id:facility_id,user_id:user_id}
           console.log(wastes)
            $http.post('/api/environment_waste_collection',wastes).then(function(data) {
                $scope.waste_collections=data.data;
                $('#wastes').val('');
                $scope.waste_type_list();
                var sending = data.data.msg;
                var statusee = data.data.status;
                if (statusee == 0) {
                    swal(
                        'Error',
                        sending,
                        'error'
                    )

                }
                else {
                    swal(
                        'Feedback..',
                        sending,
                        'success'
                    )

                }

            });
        }

        $scope.environment_waste_disposal=function (disposal) {
            console.log(disposal)
            if(disposal==undefined){
                swal(
                    'Error',
                    'Please Fill This Field',
                    'error'
                )
                return
            }
            if(disposal.waste_type==undefined){
                swal(
                    'Error',
                    'Please Choose Waste Type',
                    'error'
                )
                return
            } if(disposal.waste_disposed==undefined){
                swal(
                    'Error',
                    'Please Enter Waste disposed',
                    'error'
                )
                return
            }
            if(disposal.waste_disposal_type==undefined){
                swal(
                    'Error',
                    'Please Choose Disposal Method used',
                    'error'
                )
                return
            }

            var disposals={waste_type_id:disposal.waste_type,waste_disposed:disposal.waste_disposed,waste_disposal_type:disposal.waste_disposal_type,facility_id:facility_id,user_id:user_id}
           console.log(wastes)
            $http.post('/api/environment_waste_disposal',disposals).then(function(data) {
                $scope.waste_dispositions=data.data;
                $('#wastes').val('');

                var sending = data.data.msg;
                var statusee = data.data.status;
                if (statusee == 0) {
                    swal(
                        'Error',
                        sending,
                        'error'
                    )

                }
                else {
                    swal(
                        'Feedback..',
                        sending,
                        'success'
                    )

                }

            });
        }



        $scope.environment_waste_registration=function (waste) {
            if(waste==undefined){
                swal(
                    'Error',
                    'Please Fill This Field',
                    'error'
                )
                return
            }
            $http.post('/api/environment_waste_registration',waste).then(function(data) {
                $scope.waste_types=data.data;
                $('#waste').val('');
                $scope.waste_type_list();
                var sending = data.data.msg;
                var statusee = data.data.status;
                if (statusee == 0) {
                    swal(
                        'Error',
                        sending,
                        'error'
                    )

                }
                else {
                    swal(
                        'Feedback..',
                        sending,
                        'success'
                    )

                }

            });
        }
        $scope.waste_type_update=function (waste) {
            $http.post('/api/waste_type_update',waste).then(function(data) {

                $scope.waste_type_list();
                var sending = data.data.msg;
                var statusee = data.data.status;
                if (statusee == 0) {
                    swal(
                        'Error',
                        sending,
                        'error'
                    )

                }
                else {
                    swal(
                        'Feedback..',
                        sending,
                        'success'
                    )

                }

            });
        }
 $scope.environment_waste_dispose_registration=function (waste) {
            if(waste==undefined){
                swal(
                    'Error',
                    'Please Fill This Field',
                    'error'
                )
                return
            }
            $http.post('/api/environment_waste_dispose_registration',waste).then(function(data) {
                $scope.waste_disposes=data.data;
                $('#disposal').val('');
                $scope.waste_dispose_list();
                var sending = data.data.msg;
                var statusee = data.data.status;
                if (statusee == 0) {
                    swal(
                        'Error',
                        sending,
                        'error'
                    )

                }
                else {
                    swal(
                        'Feedback..',
                        sending,
                        'success'
                    )

                }

            });
        }
        $scope.waste_dispose_update=function (waste) {
            $http.post('/api/waste_dispose_update',waste).then(function(data) {

                $scope.waste_dispose_list();
                var sending = data.data.msg;
                var statusee = data.data.status;
                if (statusee == 0) {
                    swal(
                        'Error',
                        sending,
                        'error'
                    )

                }
                else {
                    swal(
                        'Feedback..',
                        sending,
                        'success'
                    )

                }

            });
        }

 $scope.environment_equipment_registration=function (equipment) {
            if(equipment==undefined){
                swal(
                    'Error',
                    'Please Fill This Field',
                    'error'
                )
                return
            }
     if(equipment.equipment_type==undefined){
                swal(
                    'Error',
                    'Please Fill Equipment Type',
                    'error'
                )
                return
            }
     if(equipment.equipment_name==undefined){
                swal(
                    'Error',
                    'Please Fill Equipment Name',
                    'error'
                )
                return
            } if(equipment.status==undefined){
                swal(
                    'Error',
                    'Please Fill Equipment Status',
                    'error'
                )
                return
            }
     var details={status:equipment.status,equipment_name:equipment.equipment_name,equipment_type_id:equipment.equipment_type,facility_id:facility_id,user_id:user_id}

            $http.post('/api/environment_equipment_registration',details).then(function(data) {
                $scope.equipments=data.data;
                $('#equip1').val('');

                var sending = data.data.msg;
                var statusee = data.data.status;
                if (statusee == 0) {
                    swal(
                        'Error',
                        sending,
                        'error'
                    )

                }
                else {
                    swal(
                        'Feedback..',
                        sending,
                        'success'
                    )

                }
                $scope.equipment_balances();
            });
        }
        $scope.environment_equipment_update=function (equipment) {
            var details={id:equipment.id,status:equipment.status,equipment_name:equipment.equipment_name,equipment_type_id:equipment.equipment_type_id,facility_id:facility_id,user_id:user_id}

            $http.post('/api/environment_equipment_update',details).then(function(data) {

                $scope.equipment_list();
                var sending = data.data.msg;
                var statusee = data.data.status;
                if (statusee == 0) {
                    swal(
                        'Error',
                        sending,
                        'error'
                    )

                }
                else {
                    swal(
                        'Feedback..',
                        sending,
                        'success'
                    )

                }

            });
        }


 $scope.environment_equipment_receiving=function (equipment) {
     if(equipment==undefined){
         swal(
             'Error',
             'Please Fill All Fields',
             'error'
         )
         return
     } if(equipment.equipment==undefined){
         swal(
             'Error',
             'Please Choose Equipment',
             'error'
         )
         return
     }
     if(equipment.quantity==undefined){
         swal(
             'Error',
             'Please Fill Quantity',
             'error'
         )
         return
     }
            var details={status_received:'r',quantity:equipment.quantity,equipment_id:equipment.equipment,facility_id:facility_id,user_id:user_id}

            $http.post('/api/environment_equipment_receiving',details).then(function(data) {
                $('#equip1').val('');
                $('#quantity').val('');

                var sending = data.data.msg;
                var statusee = data.data.status;
                if (statusee == 0) {
                    swal(
                        'Error',
                        sending,
                        'error'
                    )

                }
                else {
                    swal(
                        'Feedback..',
                        sending,
                        'success'
                    )

                }

            });
        }

        // ant_rabies_vaccination_registry

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
        $scope.ant_rabies_vaccination_lists=function (equipment) {
            $http.get('/api/ant_rabies_vaccination_list/'+facility_id).then(function(data) {
                $scope.ant_rabies=data.data;
            });
            }

        //ant_rabies_vaccination_usage
        
        $http.get('/api/ant_rabies_vaccination_usage/'+facility_id).then(function(data) {
            $scope.ant_use=data.data;
        });
        $scope.ant_rabies_vaccination_update=function (id) {
            swal({
                title: 'ARE YOU SURE?',

                text:'',
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

                $http.get('/api/ant_rabies_vaccination_update/'+id).then(function(data) {
                    $scope.ant_rabies_vaccination_lists();
                    swal('','Updated...','success')
                });


            }, function (dismiss) {
                // dismiss can be 'cancel', 'overlay',
                // 'close', and 'timer'
                if (dismiss === 'cancel') {

                }
            })


            }

        $scope.ant_rabies_vaccination_lists();
        $scope.ant_rabies_vaccination_registry=function (equipment) {
     if(equipment==undefined){
         swal(
             'Error',
             'Please Fill All Fields',
             'error'
         )
         return
     } if(equipment.batch_no==undefined){
         swal(
             'Error',
             'Please Enter Batch Number',
             'error'
         )
         return
     }
     if(equipment.ant_rabies_name==undefined){
         swal(
             'Error',
             'Please Fill Vaccination Name',
             'error'
         )
         return
     }  if(equipment.quantity==undefined){
         swal(
             'Error',
             'Please Fill Quantity',
             'error'
         )
         return
     }
            var details={status:1,ant_rabies_name:equipment.ant_rabies_name,quantity:equipment.quantity,batch_no:equipment.batch_no,facility_id:facility_id,user_id:user_id}

            $http.post('/api/ant_rabies_vaccination_registry',details).then(function(data) {


                var sending = data.data.msg;
                var statusee = data.data.status;
                if (statusee == 0) {
                    swal(
                        'Error',
                        sending,
                        'error'
                    )

                }
                else {
                    $('#ant_rabies_name').val('');
                    $('#quantity').val('');
                    $('#batch_no').val('');
                    $scope.ant_rabies_vaccination_lists();
                    swal(
                        'Feedback..',
                        sending,
                        'success'
                    )

                }

            });
        }

        $scope.patient_antrabies_vaccination=function (equipment,patient) {
     if(patient==undefined){
         swal(
             'Error',
             'Please Choose Patient First',
             'error'
         )
         return
     } if(patient.id==undefined){
         swal(
             'Error',
             'Please Choose Patient First',
             'error'
         )
         return
     }  if(equipment==undefined){
         swal(
             'Error',
             'Please Fill All Fields',
             'error'
         )
         return
     } if(equipment.vaccination_id==undefined){
         swal(
             'Error',
             'Please Choose Vaccination Batch Number',
             'error'
         )
         return
     }
     if(equipment.vacc_type==undefined){
         swal(
             'Error',
             'Please Choose Vaccination Type',
             'error'
         )
         return
     }  if(equipment.dose_type==undefined){
         swal(
             'Error',
             'Please Choose Vaccination Type',
             'error'
         )
         return
     }
            var details={vaccination_id:equipment.vaccination_id,vacc_type:equipment.vacc_type,dose_type:equipment.dose_type,patient_id:patient.id,facility_id:facility_id,user_id:user_id}

            $http.post('/api/patient_antrabies_vaccination',details).then(function(data) {


                var sending = data.data.msg;
                var statusee = data.data.status;
                if (statusee == 0) {
                    swal(
                        'Error',
                        sending,
                        'error'
                    )

                }
                else {
                    $('#vaccination_id').val('');
                    $('#type').val('');
                    $('#type1').val('');

                    swal(
                        'Feedback..',
                        sending,
                        'success'
                    )

                }

            });
        }




$scope.environment_equipment_issuing=function (equipment) {
     if(equipment==undefined){
         swal(
             'Error',
             'Please Fill All Fields',
             'error'
         )
         return
     } if(equipment.equipment==undefined){
         swal(
             'Error',
             'Please Choose Equipment',
             'error'
         )
         return
     }
     if(equipment.issued_quantity==undefined){
         swal(
             'Error',
             'Please Fill Issued Quantity',
             'error'
         )
         return
     }
            var details={issued_quantity:equipment.issued_quantity,equipment_id:equipment.equipment,facility_id:facility_id,user_id:user_id}

            $http.post('/api/environment_equipment_issuing',details).then(function(data) {


                var sending = data.data.msg;
                var statusee = data.data.status;
                if (statusee == 0) {
                    swal(
                        'Error',
                        sending,
                        'error'
                    )

                }
                else {
                    $('#issued_quantity').val('');
                    $('#quantity').val('');
                    swal(
                        'Feedback..',
                        sending,
                        'success'
                    )
$scope.equipment_balances();
                }

            });
        }

        $scope.nuisance_composition=function (nuisance) {
            if(nuisance==undefined){
                swal(
                    'Error',
                    'Fill all Required Fields',
                    'error'
                )
return;
            }
            if(nuisance.nuisance_type==undefined){
                swal(
                    'Error',
                    'Choose Nuisance Type',
                    'error'
                )
return;
            }
            if(nuisance.cause==undefined){
                swal(
                    'Error',
                    'Enter Nuisance Causes',
                    'error'
                )
return;
            }

            if(nuisance.location==undefined){
                swal(
                    'Error',
                    'Enter Nuisance Location',
                    'error'
                )
return;
            }
            if(nuisance.event_date==undefined){
                swal(
                    'Error',
                    'Enter Date And of Event Occured',
                    'error'
                )
return;
            }
            var nuisances_occured={user_id:user_id,facility_id:facility_id,event_date:nuisance.event_date,nuisance_id:nuisance.nuisance_type,cause:nuisance.cause,location:nuisance.location,abatement:nuisance.abatement}
            $http.post('/api/nuisance_composition',nuisances_occured).then(function(data) {
                $scope.nuisances=data.data;
                $('#cause').val('');
                $('#location').val('');
                $('#abatement').val('');
                var sending = data.data.msg;
                var statusee = data.data.status;
                if (statusee == 0) {
                    swal(
                        'Error',
                        sending,
                        'error'
                    )

                }
                else {
                    swal(
                        'Feedback..',
                        sending,
                        'success'
                    )

                }

            });
        }


        $scope.print_notified_feq=function () {
            //location.reload();
            var DocumentContainer = document.getElementById('divtoprint_feq');
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
        $scope.print_patient_notified_feq=function () {
            //location.reload();
            var DocumentContainer = document.getElementById('divtoprint_pat');
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
        $scope.print_weekly_out_b=function () {
            //location.reload();
            var DocumentContainer = document.getElementById('divtoprint_weekly');
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

        $scope.print_nuisance=function () {
            //location.reload();
            var DocumentContainer = document.getElementById('divtoprint_nuise');
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
$scope.print_wastes_dispositions=function () {
            //location.reload();
            var DocumentContainer = document.getElementById('divtoprint_waste_dis');
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
        $scope.print_wastes_collections=function () {
            //location.reload();
            var DocumentContainer = document.getElementById('divtoprint_waste_col');
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
  $scope.summary_out_break_disease_death=function () {
            //location.reload();
            var DocumentContainer = document.getElementById('divtoprint_summary_out_break');
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
$scope.anti_rabies_print=function () {
            //location.reload();
            var DocumentContainer = document.getElementById('rabies_p');
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
$scope.rubies_summary=function () {
            //location.reload();
            var DocumentContainer = document.getElementById('rabi');
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