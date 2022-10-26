/**
 * Created by USER on 2017-04-04.
 */
/**
 * Created by USER on 2017-04-02.
 */
/**
 * Created by USER on 2017-04-01.
 */
/**
 * Created by USER on 2017-03-27.
 */
/**
 * Created by USER on 2017-02-24.
 */
/**
 * Created by USER on 2017-02-13.
 */
/**
 * Created by USER on 2017-02-13.
 */
(function() {

    'use strict';

    angular
        .module('authApp')
        .controller('Family_planning_Controller', Family_planning_Controller);

    function Family_planning_Controller($http, $auth, $rootScope,$state,$location,$scope,$timeout,Helper) {
        $scope.setTab = function(newTab){
            $scope.tab = newTab;
        };
        $scope.isSet = function(tabNum){
            return $scope.tab === tabNum;
        }
        $scope.oneAtATime=true;
        //loading menu
        var user_id=$rootScope.currentUser.id;
        var  facility_id=$rootScope.currentUser.facility_id;
        $http.get('/api/getUsermenu/'+user_id ).then(function(data) {
            $scope.menu=data.data;
            ////////console.log($scope.menu);

        });
        $scope.getResidence = function (text) {
            return Helper.getResidence(text)
                .then(function (response) {
                    return response.data;
                });
        };
        var residence_id;
        $scope.selectedResidence = function (residence) {
            if (typeof residence != 'undefined') {
                residence_id = residence.residence_id;
                console.log(residence)
            }
            $scope.residence = residence;
        }
        $scope.ClinicQueue=function () {
            $http.get('/api/searchClinicpatientQueue/'+facility_id).then(function(data) {
                $scope.resdatas = data.data[5];
            });
        }
        $scope.ClinicQueue();
        $scope.patient = {};
        $scope.calculateAge = function (patient, source) {

            var dob = patient.dob;


            if (patient.dob instanceof Date) {
                dob = patient.dob.toISOString();
            }
            if (patient.dob == undefined && patient.age == undefined) {
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
                $scope.patient.dob = new Date((new Date().getFullYear() - patient.age) + '-01-01');
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
                if (patient.age_unit == 'Years')
                    $scope.calculateAge('age');
                else if (patient.age_unit == 'Months') {
                    if (((new Date()).getMonth() + 1) >= (patient.age % 12))
                        $scope.patient.dob = ((new Date()).getFullYear() - ~~(patient.age / 12)) + '-' + ((((new Date()).getMonth() + 1) - (patient.age % 12)).toString().length == 2 ? '' : '0') + (((new Date()).getMonth() + 1) - (patient.age % 12)) + '-01';
                    else
                        $scope.patient.dob = ((new Date()).getFullYear() - 1 - ~~(patient.age / 12)) + '-' + (((12 + ((new Date()).getMonth() + 1)) - (patient.age % 12)).toString().length == 2 ? '' : '0') + ((12 + ((new Date()).getMonth() + 1)) - (patient.age % 12)) + '-01';
                } else {
                    if (((new Date()).getDate()) >= (patient.age % 30))
                        $scope.patient.dob = ((new Date()).getFullYear() - ~~(patient.age / 365)) + '-' + ((((new Date()).getMonth() + 1) - ~~(patient.age / 30)).toString().length == 2 ? '' : '0') + (((new Date()).getMonth() + 1) - ~~(patient.age / 30)) + '-' + (patient.age.toString().length == 2 ? '' : '0') + patient.age.toString();
                    else
                        $scope.patient.dob = ((new Date()).getFullYear() - ~~(patient.age / 365)) + '-' + ((((new Date()).getMonth()) - ~~(patient.age / 30)).toString().length == 2 ? '' : '0') + (((new Date()).getMonth()) - ~~(patient.age / 30)) + '-' + (((30 + ((new Date()).getDate())) - (patient.age % 30)).toString().length == 2 ? '' : '0') + ((30 + ((new Date()).getDate())) - (patient.age % 30));
                }
            }
        };

        $scope.partiner_calculateAge = function (patient, source) {

            var dob1 = patient.dob1;


            if (patient.dob1 instanceof Date) {
                dob1 = patient.dob1.toISOString();
            }
            if (patient.dob1 == undefined && patient.age1 == undefined) {
                return;
            }


            if (dob1 != '' && source == 'date' && ((new Date()).getFullYear() < parseInt(dob1.substring(0, 4)) ||
                ((new Date()).getFullYear() == parseInt(dob1.substring(0, 4)) && ((new Date()).getMonth() + 1) < parseInt(dob1.substring(dob1.indexOf("-") + 1, 7))) ||
                ((new Date()).getFullYear() == parseInt(dob1.substring(0, 4)) && ((new Date()).getMonth() + 1) == parseInt(dob1.substring(dob1.indexOf("-") + 1, 7)) && ((new Date()).getDate()) < parseInt(dob1.substring(dob1.lastIndexOf("-") + 1, 10))))) {
                $scope.patient.dob1 = undefined;
                $scope.patient.age_unit1 = "";
                $scope.patient.age1 = "";
                swal('Future dates not allowed!', '', 'warning');
                return;
            }

            if (source == 'age1') {
                $scope.patient.dob1 = new Date((new Date().getFullYear() - patient.age1) + '-01-01');
                $scope.patient.age_unit1 = 'Years';

            } else if (source == 'date') {
                $scope.patient.dob1 = dob1.replace(/\//g, '-');
                var days = Math.floor(((new Date()) - new Date(dob1.substring(0, 4) + '-' + dob1.substring(dob1.indexOf("-") + 1, 7) + '-' + dob1.substring(dob1.lastIndexOf("-") + 1, 10))) / (1000 * 60 * 60 * 24));
                if (days > 365) {
                    $scope.patient.age1 = Math.floor(days / 365);
                    $scope.patient.age_unit1 = 'Years';
                } else if (days > 30) {
                    $scope.patient.age1 = Math.floor(days / 30);
                    $scope.patient.age_unit1 = 'Months';
                } else {
                    $scope.patient.age1 = days;
                    $scope.patient.age_unit1 = 'Days';
                }
            } else {
                if (patient.age_unit1 == 'Years')
                    $scope.partiner_calculateAge('age1');
                else if (patient.age_unit1 == 'Months') {
                    if (((new Date()).getMonth() + 1) >= (patient.age1 % 12))
                        $scope.patient.dob1 = ((new Date()).getFullYear() - ~~(patient.age1 / 12)) + '-' + ((((new Date()).getMonth() + 1) - (patient.age1 % 12)).toString().length == 2 ? '' : '0') + (((new Date()).getMonth() + 1) - (patient.age1 % 12)) + '-01';
                    else
                        $scope.patient.dob1 = ((new Date()).getFullYear() - 1 - ~~(patient.age1 / 12)) + '-' + (((12 + ((new Date()).getMonth() + 1)) - (patient.age1 % 12)).toString().length == 2 ? '' : '0') + ((12 + ((new Date()).getMonth() + 1)) - (patient.age1 % 12)) + '-01';
                } else {
                    if (((new Date()).getDate()) >= (patient.age1 % 30))
                        $scope.patient.dob1 = ((new Date()).getFullYear() - ~~(patient.age1 / 365)) + '-' + ((((new Date()).getMonth() + 1) - ~~(patient.age1 / 30)).toString().length == 2 ? '' : '0') + (((new Date()).getMonth() + 1) - ~~(patient.age1 / 30)) + '-' + (patient.age1.toString().length == 2 ? '' : '0') + patient.age1.toString();
                    else
                        $scope.patient.dob1 = ((new Date()).getFullYear() - ~~(patient.age1 / 365)) + '-' + ((((new Date()).getMonth()) - ~~(patient.age1 / 30)).toString().length == 2 ? '' : '0') + (((new Date()).getMonth()) - ~~(patient.age1 / 30)) + '-' + (((30 + ((new Date()).getDate())) - (patient.age1 % 30)).toString().length == 2 ? '' : '0') + ((30 + ((new Date()).getDate())) - (patient.age1 % 30));
                }
            }
        };

        $scope.Anti_natal_in_referral=function (selectedPatient) {

            $http.post('/api/Family_incoming_referral', selectedPatient).then(function (data) {


                $scope.selectedPatient=data.data;


            });
            $scope.ClinicQueue();
        }
        $scope.department_list=function () {

            $http.get('/api/department_list').then(function(data) {
                $scope.departments=data.data;

            });
        }

        $scope.department_list();
        var occupation=[];
        var resdata =[];
        var resdata1 =[];

        $scope.showSearchOccupation= function (searchKey) {

            $http.get('/api/getOccupation/'+searchKey).then(function(data) {
                occupation=data.data;

            });
            return occupation;
        }


        $scope.showSearchResidences = function(searchKey) {

            $http.get('/api/searchResidences/' + searchKey).then(function (data) {
                resdata = data.data;
            });

            

            return resdata;

        }


        $http.get('/api/facility_list').then(function (data) {
            $scope.facility = data.data;
        });

        $scope.showSearch = function(searchKey) {


            $http.post('/api/search_family_planing_clients',{searchKey:searchKey}).then(function(data) {
                resdata = data.data;

            });


            return resdata;


        }

        $scope.family_planning_registration = function(patient) {

            if(patient==undefined){
                swal(
                    'Error',
                    'Please Fill All required Fields',
                    'error'
                )
            }
            else  if(patient.first_name==undefined){
                swal(
                    'Error',
                    'Please Fill First name',
                    'error'
                )
            }
            else if( $scope.residence==undefined){
                swal(
                    'Error',
                    'Please Fill Residence',
                    'error'
                )
            }
            else   if(patient.occupation==undefined){
                swal(
                    'Error',
                    'Please Fill Occupation',
                    'error'
                )
            }



            else{


                var details={
                    facility_id:facility_id,user_id:user_id,
                    first_name:patient.first_name,
                    middle_name:patient.middle_name,
                    last_name:patient.last_name,
                    gender:patient.gender,
                    dob:patient.dob,
                    education:patient.education,
                    mobile_number:patient.mobile_number,
                    occupation_id:patient.occupation.id,
                    residence_id:$scope.residence.residence_id,

                };
                //console.log(details);
                 
                $http.post('/api/family_planning_registration',details).then(function(data) {
                    $scope.detail = data.data;


                    var sending = data.data.data;
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
        }


        $scope.Family_planning_serial_no = function(patient) {

            $http.post('/api/Family_planning_serial_no',{facility_id:facility_id,patient_id:patient,user_id:user_id}).then(function(data) {
                $scope.serial_no = data.data;
                $scope.mother_planning_method_status(patient);

            });


        }

        $scope.mother_planning_method_status = function(patient) {

            $http.get('/api/mother_planning_method_status/'+patient).then(function(data) {
                $scope.method_statuses = data.data;

            });


        }

        $scope.mother_planning_method_status_update=function (datarec) {
            var name=datarec.planning_method;
            swal({
                title: 'Are you sure?',
                text: "You Want to stop Using "+name,
                type: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Yes! ',
                cancelButtonText: '  No, cancel!',
                confirmButtonClass: 'btn btn-success  ',
                cancelButtonClass: '  btn btn-danger',
                buttonsStyling: false
            }).then(function () {


                $http.post('/api/mother_planning_method_status_update',datarec).then(function(data) {
                    $scope.method_statuses = data.data.records;
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






            }, function (dismiss) {
                // dismiss can be 'cancel', 'overlay',
                // 'close', and 'timer'
                if (dismiss === 'cancel') {

                }
            })


        }




        
        $scope.family_plan_reattendance_registration = function(patient) {

            $http.post('/api/family_plan_reattendance_registration',{facility_id:facility_id,patient_id:patient,user_id:user_id}).then(function(data) {
                $scope.serial_no = data.data;

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

 $scope.faimily_birth_registration = function(birth,patient) {
     if(birth==undefined){
         swal(
             'Error',
             'Fill all Fields',
             'error'
         )
     }
     else if(birth.pregnancy_number==undefined){
         swal(
             'Error',
             'Fill Number of Delivery column',
             'error'
         )
     } else if(birth.miscarriage_number==undefined){
         swal(
             'Error',
             'Fill Number of Miscarriage column',
             'error'
         )
     }else if(birth.msb_number==undefined){
         swal(
             'Error',
             'Fill Number of Maslated column',
             'error'
         )
     }else if(birth.alive_born_number==undefined){
         swal(
             'Error',
             'Fill Number of   Alive Born column',
             'error'
         )
     }
     else if(birth.child_alive_number==undefined){
         swal(
             'Error',
             'Fill Number of Children Alive column',
             'error'
         )
     }
else{
var births={
    pregnancy_number:birth.pregnancy_number,
    msb_number:birth.msb_number,
    alive_born_number:birth.alive_born_number,
    miscarriage_number:birth.miscarriage_number,
    child_alive_number:birth.child_alive_number,
    last_delivery_date:birth.last_delivery_date,
    facility_id:facility_id,patient_id:patient,user_id:user_id};

            $http.post('/api/faimily_birth_registration',births).then(function(data) {
                $scope.serial_no = data.data;

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

        }


        $scope.faimily_health_registration = function(health,patient) {
     if(health==undefined){
         swal(
             'Error',
             'Fill all Fields',
             'error'
         )
     }
     else if(health.headache==undefined){
         swal(
             'Error',
             'Fill Option of Headache column',
             'error'
         )
     }
     else if(health.yellow_fever==undefined){
         swal(
             'Error',
             'Fill Option of Yellow Fever column',
             'error'
         )
     } else if(health.heart_disease==undefined){
         swal(
             'Error',
             'Fill Option of Heart Disease column',
             'error'
         )
     }
     else if(health.bp==undefined){
         swal(
             'Error',
             'Fill Option of BP column',
             'error'
         )
     } else if(health.diabet==undefined){
         swal(
             'Error',
             'Fill Option of Diabetic column',
             'error'
         )
     }
     else if(health.breast_bunje==undefined){
         swal(
             'Error',
             'Fill Option of Bunje in Breast column',
             'error'
         )
     }
     else if(health.varicose_vein==undefined){
         swal(
             'Error',
             'Fill Option of Varicose Vein column',
             'error'
         )
     }
     else if(health.kifafa_medics==undefined){
         swal(
             'Error',
             'Fill Option of Had Ever used Kifafa Medicines ?',
             'error'
         )
     }
     else if(health.tb_medics==undefined){
         swal(
             'Error',
             'Fill Option of Had Ever used TB Medicines ?',
             'error'
         )
     }
else{
var births={
    headache:health.headache,
    yellow_fever:health.yellow_fever,
    heart_disease:health.heart_disease,
    bp:health.bp,
    diabet:health.diabet,
    breast_bunje:health.breast_bunje,
    varicose_vein:health.varicose_vein,
    kifafa_medics:health.kifafa_medics,
    tb_medics:health.tb_medics,
    other_problems:health.other_problems,

    facility_id:facility_id,client_id:patient,user_id:user_id};

            $http.post('/api/faimily_health_registration',births).then(function(data) {
                $scope.serial_no = data.data;

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

        }


$scope.faimily_delivery_result_registration = function(delivery,patient) {
     if(delivery==undefined){
         swal(
             'Error',
             'Fill all Fields',
             'error'
         )
     }
     else if(delivery.year==undefined){
         swal(
             'Error',
             'Fill Year of Event Occurred',
             'error'
         )
     } else if(delivery.delivery_method==undefined){
         swal(
             'Error',
             'Fill Delivery Method',
             'error'
         )
     }
     else if(delivery.delivery_place==undefined){
         swal(
             'Error',
             'Fill Delivery Place',
             'error'
         )
     }else if(delivery.delivery_results==undefined){
         swal(
             'Error',
             'Fill Delivery Results',
             'error'
         )
     }else if(delivery.baby_feeding==undefined){
         swal(
             'Error',
             'Fill BAby Feeding Method',
             'error'
         )
     }

else{
var births={
    year:delivery.year,
    delivery_method:delivery.delivery_method,
    delivery_place:delivery.delivery_place,
    delivery_results:delivery.delivery_results,
    baby_feeding:delivery.baby_feeding,


    facility_id:facility_id,client_id:patient,user_id:user_id};

            $http.post('/api/faimily_delivery_result_registration',births).then(function(data) {
                $scope.serial_no = data.data;

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

        }

$scope.faimily_menstral_result_registration = function(menstral,patient) {
     if(menstral==undefined){
         swal(
             'Error',
             'Fill all Fields',
             'error'
         )
     }
     else if(menstral.lnmp==undefined){
         swal(
             'Error',
             'Fill Last Normal Menstrauation Period',
             'error'
         )
     }
     else if(menstral.menstral_day==undefined){
         swal(
             'Error',
             'Fill   Menstruation Day',
             'error'
         )
     }
     else if(menstral.bleeding_quantity==undefined){
         swal(
             'Error',
             'Fill   Quantity of Blood Lost',
             'error'
         )
     }
     else if(menstral.menstral_cycle==undefined){
         swal(
             'Error',
             'Fill   Menstruation Cycle',
             'error'
         )
     }else if(menstral.pain==undefined){
         swal(
             'Error',
             'Fill option  Menstruation pain column',
             'error'
         )
     }
else{
var births={
    lnmp:menstral.lnmp,
    menstral_day:menstral.menstral_day,
    bleeding_quantity:menstral.bleeding_quantity,
    menstral_cycle:menstral.menstral_cycle,
    pain:menstral.pain,

    facility_id:facility_id,client_id:patient,user_id:user_id};

            $http.post('/api/faimily_menstral_result_registration',births).then(function(data) {
                $scope.serial_no = data.data;

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

        }

$scope.faimily_iptc_registration = function(pitc,patient) {
     if(pitc==undefined){
         swal(
             'Error',
             'Fill all Fields',
             'error'
         )
     }
     else if(pitc.pitc==undefined){
         swal(
             'Error',
             'Fill Option Of PITC',
             'error'
         )
     }
     else if(pitc.pitc_result==undefined){
         swal(
             'Error',
             'Fill   IPTC Results',
             'error'
         )
     }  else if(pitc.result_date==undefined){
         swal(
             'Error',
             'Fill  Date of  IPTC Results ',
             'error'
         )
     }

else{
var births={
    pitc:pitc.pitc,
    pitc_result:pitc.pitc_result,
    result_date:pitc.result_date,


    facility_id:facility_id,client_id:patient,user_id:user_id};

            $http.post('/api/faimily_iptc_registration',births).then(function(data) {
                $scope.serial_no = data.data;

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

        }
        $scope.faimily_cancer_registration = function(cancer,patient) {
     if(cancer==undefined){
         swal(
             'Error',
             'Fill all Fields',
             'error'
         )
     }
     else if(cancer.placenta_status==undefined){
         swal(
             'Error',
             'Fill Option Of Placenta Status',
             'error'
         )
     }
     else if(cancer.suspected_cancer==undefined){
         swal(
             'Error',
             'Fill Option Of Hypothetically Placenta Cancer Carrier',
             'error'
         )
     }
 else if(cancer.cryotherapy==undefined){
         swal(
             'Error',
             'Fill Option Of Cryotherapy ',
             'error'
         )
     }
     else if(cancer.breast_bunje==undefined){
         swal(
             'Error',
             'Fill Option Of Bunje In Breast ',
             'error'
         )
     }


else{
var births={
    placenta_status:cancer.placenta_status,
    suspected_cancer:cancer.suspected_cancer,
    cryotherapy:cancer.cryotherapy,
    breast_bunje:cancer.breast_bunje,

    facility_id:facility_id,client_id:patient,user_id:user_id};

            $http.post('/api/faimily_cancer_registration',births).then(function(data) {
                $scope.serial_no = data.data;

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

        }

 $scope.faimily_lab_test_registration = function(lab,patient) {
     if(lab==undefined){
         swal(
             'Error',
             'Fill all Fields',
             'error'
         )
     }
     else if(lab.urine==undefined){
         swal(
             'Error',
             'Fill Option Of Urine',
             'error'
         )
     }
     else if(lab.albumin==undefined){
         swal(
             'Error',
             'Fill Option Of Albumin',
             'error'
         )
     }
 else if(lab.sugar==undefined){
         swal(
             'Error',
             'Fill Option Of Sugar ',
             'error'
         )
     }



else{
var births={
    urine:lab.urine,
    albumin:lab.albumin,
    sugar:lab.sugar,
    others:lab.others,


    facility_id:facility_id,client_id:patient,user_id:user_id};

            $http.post('/api/faimily_lab_test_registration',births).then(function(data) {
                $scope.serial_no = data.data;

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

        }


$scope.fplanning_stomach_leg_investigation = function(obser,patient) {
     if(obser==undefined){
         swal(
             'Error',
             'Fill all Fields',
             'error'
         )
     }
     else if(obser.liver_inflammation==undefined){
         swal(
             'Error',
             'Fill all Columns',
             'error'
         )
     }


else{
var births={
    liver_inflammation:obser.liver_inflammation,
    leg_inflammation:obser.leg_inflammation,
    vericose_vein:obser.vericose_vein,

    others:obser.others,


    facility_id:facility_id,client_id:patient,user_id:user_id};

            $http.post('/api/fplanning_stomach_leg_investigation',births).then(function(data) {
                $scope.serial_no = data.data;

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

        }
$scope.fplanning_viginal_by_arm_investigations = function(viginal,patient) {
     if(viginal==undefined){
         swal(
             'Error',
             'Fill all Fields',
             'error'
         )
     }
     else if(viginal.placenta_size==undefined){
         swal(
             'Error',
             'Fill placenta size Columns',
             'error'
         )
     }else if(viginal.placenta_layout==undefined){
         swal(
             'Error',
             'Fill placenta Layout Columns',
             'error'
         )
     }
else if(viginal.adnexa==undefined){
         swal(
             'Error',
             'Fill adnexa Columns',
             'error'
         )
     }


else{
var births={
    placenta_size:viginal.placenta_size,
    placenta_layout:viginal.placenta_layout,
    adnexa:viginal.adnexa,
    others:viginal.others,


    facility_id:facility_id,client_id:patient,user_id:user_id};

            $http.post('/api/fplanning_viginal_by_arm_investigations',births).then(function(data) {
                $scope.serial_no = data.data;

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

        }

$scope.fplanning_viginal_by_spec_investigations = function(spec,patient) {
     if(spec==undefined){
         swal(
             'Error',
             'Fill all Fields',
             'error'
         )
     }
     else if(spec.viginal_wet==undefined){
         swal(
             'Error',
             'Fill option for Vaginal wet Columns',
             'error'
         )
     }else if(spec.viginal_dirt==undefined){
         swal(
             'Error',
             'Fill option for Vaginal Dirt Columns',
             'error'
         )
     }
     else if(spec.cervix_normal==undefined){
         swal(
             'Error',
             'Fill option for cervix normalilty Columns',
             'error'
         )
     }else if(spec.viginal_inflammation==undefined){
         swal(
             'Error',
             'Fill option for vaginal_inflammation Columns',
             'error'
         )
     }else if(spec.cancer==undefined){
         swal(
             'Error',
             'Fill option for Cancer Columns',
             'error'
         )
     }


else{
var births={
    viginal_inflammation:spec.viginal_inflammation,
    viginal_wet:spec.viginal_wet,
    viginal_dirt:spec.viginal_dirt,
    cervix_normal:spec.cervix_normal,
    cancer:spec.cancer,
    others:spec.others,



    facility_id:facility_id,client_id:patient,user_id:user_id};

            $http.post('/api/fplanning_viginal_by_spec_investigations',births).then(function(data) {
                $scope.serial_no = data.data;

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

        }
        
        
$scope.fplanning_attendances = function(attendance,patient) {
     if(attendance==undefined){
         swal(
             'Error',
             'Fill all Fields',
             'error'
         )
     }
     else if(attendance.weight==undefined){
         swal(
             'Error',
             'Fill Body Weight Column',
             'error'
         )
     }   else if(attendance.bp==undefined){
         swal(
             'Error',
             'Fill BP Columns',
             'error'
         )
     }  else if(attendance.lnmp==undefined){
         swal(
             'Error',
             'Fill L.N.M.P Column',
             'error'
         )
     }
else{
var births={
    
    weight:attendance.weight,
    bp:attendance.bp,
    lnmp:attendance.lnmp, 
    complains:attendance.complains,
    comment_treatment:attendance.comment_treatment,
    followup_date:attendance.followup_date,
    
    facility_id:facility_id,client_id:patient,user_id:user_id};

            $http.post('/api/fplanning_attendances',births).then(function(data) {
                $scope.serial_no = data.data;

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

        }


        $scope.planning_method_list_registration = function(method) {
     if(method==undefined){
         swal(
             'Error',
             'Fill Field',
             'error'
         )
     }

else{
var methods={
    planning_method:method.planning_method};
         ////console.log(methods)
            $http.post('/api/planning_method_list_registration',methods).then(function(data) {
                $scope.serial_no = data.data;
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
                    $scope.family_planning_method_list();
                }

            });


     }

        }

        $scope.family_planning_method_list_update = function(method) {

            var methods = {
                planning_method: method.planning_method,
                id: method.id
            };

            $http.post('/api/family_planning_method_list_update', methods).then(function (data) {
                $scope.serial_no = data.data;
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
                    method.planning_method=null;
                    $scope.family_planning_method_list();
                }

            });
        }

 $scope.RCH_recommendations_registration = function(opt,patient) {
if(opt==undefined){
    swal(
        'Error',
        'Write Recommendations first',
        'error'
    )
}
     else{


            var opts = {
                opinion: opt.opinion,
                opinion_type:5, facility_id:facility_id,patient_id:patient,user_id:user_id
            };

            $http.post('/api/RCH_recommendations_registration', opts).then(function (data) {
                $scope.opinions = data.data;
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
        }

$scope.mother_planning_method_registration = function(method,patient) {
////console.log(method)
    if(method==undefined){
        swal(
            'Error',
            '  Choose Method of Family Planning',
            'error'
        )
    }
    else if(method.id==undefined){
        swal(
            'Error',
            'Fill Choose Method of Family Planning',
            'error'
        )
    }else if(method.date_attended==undefined){
        swal(
            'Error',
            '  Choose Date of Starting to Use This Method',
            'error'
        )
    }
    else if(method.event_driven==undefined){
        swal(
            'Error',
            '  Choose Event Made you to Use This Method',
            'error'
        )
    }
    else if(method.place==undefined){
        swal(
            'Error',
            '  Choose Place of This Client',
            'error'
        )
    }
    else {


            var methods = {
                method_id: method.id.id,
                place: method.place,
                status:1,
                event_driven: method.event_driven,
                date_attended: method.date_attended,
                facility_id:facility_id,patient_id:patient,user_id:user_id
            };

            $http.post('/api/mother_planning_method_registration', methods).then(function (data) {
                $scope.method = data.data;
                var sending = data.data.msg;
                var statusee = data.data.status;
                $scope.mother_planning_method_status(patient);
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
        }

$scope.breast_cancer_registration = function(brs,patient) {

    if(brs==undefined){
        swal(
            'Error',
            'Fill Choose Fill All fields',
            'error'
        )
    }
    else if(brs.bunje==undefined) {
        swal(
            'Error',
            'Fill Choose Option For Bunje Column',
            'error'
        )
    }
    else if(brs.wound==undefined) {
        swal(
            'Error',
            'Fill Choose Option For Wound Column',
            'error'
        )
    }
    else if(brs.wound==undefined) {
        swal(
            'Error',
            'Fill Choose Option For Wound Column',
            'error'
        )
    }
    else {


            var brss = {
                bunje: brs.bunje,
                wound: brs.wound,
                breast_bleeding: brs.breast_bleeding,
                breast_abscess: brs.breast_abscess,
                others: brs.others,
                facility_id:facility_id,patient_id:patient,user_id:user_id
            };

            $http.post('/api/breast_cancer_registration', brss).then(function (data) {
                $scope.method = data.data;
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
        }

$scope.cervix_cancer_registration = function(cerv,patient) {

    if(cerv==undefined){
        swal(
            'Error',
            'Fill Choose Fill All fields',
            'error'
        )
    }
    else if(cerv.virginal_discharge==undefined) {
        swal(
            'Error',
            'Fill Choose Option For virginal discharge Column',
            'error'
        )
    }

    else {


            var cervs = {
                virginal_discharge: cerv.virginal_discharge,
                cervix_scratching: cerv.cervix_scratching,
                cervix_swelling: cerv.cervix_swelling,
                virginal_bleeding: cerv.virginal_bleeding,
                others: cerv.others,
                facility_id:facility_id,patient_id:patient,user_id:user_id
            };

            $http.post('/api/cervix_cancer_registration', cervs).then(function (data) {
                $scope.method = data.data;
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
        }

$scope.planning_vvu_registration = function(vvu,patient) {

    if(vvu==undefined){
        swal(
            'Error',
            'Fill Choose Fill All fields',
            'error'
        )
    }
    else if(vvu.current_vvu_status==undefined) {
        swal(
            'Error',
            'Fill Choose Option For VVU Current Status Column',
            'error'
        )
    }

    else {


            var vvus = {
                current_vvu_status: vvu.current_vvu_status,
                mother_vvu_status: vvu.mother_vvu_status,
                partner_vvu_status: vvu.partner_vvu_status,
                facility_id:facility_id,patient_id:patient,user_id:user_id
            };

            $http.post('/api/planning_vvu_registration', vvus).then(function (data) {
                $scope.method = data.data;
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
        }



        $scope.institution_list=function () {

            $http.get('/api/institution_list').then(function (data) {

                $scope.institutes=data.data;
            });

        }
        $scope.institution_list();

        
 $scope.family_planning_method_list=function () {

            $http.get('/api/family_planning_method_list').then(function (data) {

                $scope.methods=data.data;
            });

        }
        $scope.family_planning_method_list();


        $scope.child_referral_registration111=function (ref,selectedPatient,client) {

            if(selectedPatient==undefined){
                swal(
                    'Error',
                    'Please choose Client First',
                    'error'
                )
            } else if(ref==undefined){
                swal(
                    'Error',
                    'Please Fill All fields  ',
                    'error'
                )
            }
            else if(ref.referral_id==undefined) {
                swal(
                    'Error',
                    'Select Clinic for referring',
                    'error'
                )
            }
            else if(ref.reason==undefined){
                swal(
                    'Error',
                    'Please Enter Reasons for  Referral ',
                    'error'
                )
            }
            else{

                var refs={'transfered_institution_id':ref.referral_id,
                    'reason':ref.reason,mother_id:1,
                    'patient_id':selectedPatient,
                    'patient_id_table':client,
                    'sender_clinic_id':22,
                    'facility_id':facility_id,
                    'user_id':user_id};


                $http.post('/api/child_referral_registration',refs).then(function(data) {
                    $scope.stds = data.data;
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

        }

        $scope.department_list = function () {

            $http.get('/api/getSpecialClinics').then(function (data) {
                $scope.departments = data.data;

            });
        }
        $scope.Consultation = function (dept_id) {

            $http.post('/api/getConsultation', {
                "dept_id": dept_id,
                "patient_category_id":1,
                "facility_id": facility_id
            }).then(function(data) {
                $scope.consultations = data.data;
            });
        }


        $scope.department_list();


        $scope.child_referral_registration = function (ref, selectedPatient, client) {

            if (selectedPatient == undefined) {
                swal(
                    'Error',
                    'Please choose Client First',
                    'error'
                )
            } else if (ref == undefined) {
                swal(
                    'Error',
                    'Please Fill All fields  ',
                    'error'
                )
            }
            else if (ref.referral_id == undefined) {
                swal(
                    'Error',
                    'Select Clinic for referring',
                    'error'
                )
            } else if (ref.consultation == undefined) {
                swal(
                    'Error',
                    'Select Clinic Consultation',
                    'error'
                )
            }
            else if (ref.reason == undefined) {
                swal(
                    'Error',
                    'Please Enter Reasons for  Referral ',
                    'error'
                )
            }
            else {

                var refs = {
                    'transfered_institution_id': ref.referral_id,
                    'reason': ref.reason, mother_id: 1,
                    'patient_id': selectedPatient,
                    'patient_id_table': selectedPatient,
                    'sender_clinic_id': 24,
                    'facility_id': facility_id,
                    'item_id': ref.consultation.item_id,
                    'item_type_id': ref.consultation.item_type_id,
                    'price_id': ref.consultation.price_id,
                    'user_id': user_id,
                };


                $http.post('/api/child_referral_registration', refs).then(function (data) {
                    $scope.stds = data.data;
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

        }


        $scope.condom_usage_registration=function (condom,selectedPatient) {

             if(condom==undefined){
                swal(
                    'Error',
                    'Please Fill All fields  ',
                    'error'
                )
            }
           

             else  if(condom.place==undefined){
                swal(
                    'Error',
                    'Please Fill client Place Status ',
                    'error'
                )
            } else  if(condom.quantity==undefined){
                swal(
                    'Error',
                    'Please Fill client quantity of condoms Given(PCs) ',
                    'error'
                )
            }
           
            else{

                 
                var condoms={ 
                    'patient_id':selectedPatient,
                    'place':condom.place,
                    'quantity':condom.quantity,
                    'facility_id':facility_id,
                    'user_id':user_id};
                  
                $http.post('/api/condom_usage_registration',condoms).then(function(data) {
                    $scope.stds = data.data;
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

        }




    }



})();
/**
 * Created by USER on 2017-03-08.
 */