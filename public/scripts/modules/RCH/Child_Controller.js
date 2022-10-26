/**
 * Created by USER on 2017-02-13.
 */
(function() {

    'use strict';

    angular
        .module('authApp')
        .controller('Child_Controller', Child_Controller);

    function Child_Controller($http, $auth, $rootScope,$state,$location,$scope,$timeout,Helper) {
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
            //////console.log($scope.menu);

        });
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

        $scope.ClinicQueue=function () {
            $http.get('/api/searchClinicpatientQueue/'+facility_id).then(function(data) {
                $scope.resdatas = data.data[2];


            });
        }
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
        $scope.ClinicQueue();

        $scope.Anti_natal_in_referral=function (selectedPatient) {

            $http.post('/api/Child_incoming_referral', selectedPatient).then(function (data) {


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
        var resdata =[];

        $scope.showSearchChild = function(searchKey) {


            $http.post('/api/searchRchAllChild',{searchKey:searchKey}).then(function(data) {
                resdata = data.data;

            });


            return resdata;


        }

        $http.get('/api/facility_list').then(function (data) {
            $scope.facility = data.data;
        });

        $scope.showSearchResidences = function(searchKey) {

            $http.get('/api/searchResidences/' + searchKey).then(function (data) {
                resdata = data.data;
            });


            return resdata;
        }


        $scope.child_serial_no = function(patient) {

            $http.post('/api/Child_serial_no',{facility_id:facility_id,patient_id:patient,user_id:user_id}).then(function(data) {
                $scope.serial_no = data.data;

            });


        }

        $scope.institution_list=function () {

            $http.get('/api/institution_list').then(function (data) {

                $scope.institutes=data.data;
            });

        }
        $scope.institution_list();

        $scope.vaccination_list=function () {
            $http.get('/api/vaccination_list').then(function (data) {
                $scope.vaccinations = data.data;

            });
        }

        $scope.vaccination_list();

        $scope.child_registration = function(reg,patient_id) {
            if(reg==undefined){
                swal(
                    'Error',
                    'Please Fill all fields',
                    'error'
                );
            }
            else if(reg.first_name==undefined){
                swal(
                    'Error',
                    'Please Enter First Name',
                    'error'
                );
            }
        else if(reg.last_name==undefined){
                swal(
                    'Error',
                    'Please Enter Last Name',
                    'error'
                );
            }

            else if(reg.gender==undefined){
                swal(
                    'Error',
                    'Please Enter Gender',
                    'error'
                );
            }
            else if(reg.dob==undefined){
                swal(
                    'Error',
                    'Please Enter Date of Birth',
                    'error'
                );
            }
            else if(reg.weight==undefined){
                swal(
                    'Error',
                    'Please Enter Child Body Weight',
                    'error'
                );
            }
            else if(reg.delivery_place==undefined){
                swal(
                    'Error',
                    'Please Enter Delivery Place',
                    'error'
                );
            }
            else if(reg.midwife==undefined){
                swal(
                    'Error',
                    'Please Enter Type of Midwife Assisted Delivery',
                    'error'
                );
            } 
            else if(reg.mother_name==undefined){
                swal(
                    'Error',
                    "Please Enter Mother's Name",
                    'error'
                );
            } else if($scope.residence==undefined){
                swal(
                    'Error',
                    "Please Enter Residence",
                    'error'
                );
            }
            else if($scope.residence==undefined){
                swal(
                    'Error',
                    "Please Enter Residence",
                    'error'
                );
            }
            else{
                var regpost={
                    facility_id:facility_id,user_id:user_id,
                    first_name:reg.first_name,
                    middle_name:reg.middle_name,
                    last_name:reg.last_name,
                    gender:reg.gender,
                    dob:reg.dob,
                    weight:reg.weight,
                    delivery_place:reg.delivery_place,
                    midwife:reg.midwife,
                    mobile_number:reg.mobile_number,
                    mother_name:reg.mother_name,
                   father_name:reg.father_name,
                    residence_id:$scope.residence.residence_id,
                };
                //console.log(regpost);

                $http.post('/api/Child_registration_update',regpost).then(function(data) {
                    $scope.child = data.data;
                    var msg=data.data.msg;
                    var status=data.data.status;
                    reg.birth_reg_no=null;
                    if (status==0){
                        swal(
                            'Error',
                            msg,
                            'error'
                        );
                    }
                    else{
                        swal(
                            'Response',
                            msg,
                            'success'
                        );
                    }
                });

            }


        }
        
 $scope.child_mother_registration = function(reg,patient_id) {
            if(reg==undefined){
                swal(
                    'Error',
                    'Please Fill all fields',
                    'error'
                );
            }
            else if(reg.tt_given==undefined){
                swal(
                    'Error',
                    'Please Fill Option For Mother TT Given Status',
                    'error'
                );
            }

            else if(reg.vvu_status==undefined){
                swal(
                    'Error',
                    'Please Fill Option For Mother VVU Status',
                    'error'
                );
            }
            else{
                var regpost={facility_id:facility_id,patient_id:patient_id,user_id:user_id,tt_given:reg.tt_given,vvu_status:reg.vvu_status};
                ////console.log(regpost);
                $http.post('/api/Child_mother_registration',regpost).then(function(data) {
                    $scope.child = data.data;
                    var msg=data.data.msg;
                    var status=data.data.status;
                    if (status==0){
                        swal(
                            'Error',
                            msg,
                            'error'
                        );
                    }
                    else{
                        swal(
                            'Response',
                            msg,
                            'success'
                        );
                    }
                });

            }


        }

$scope.hiv_ID_registration = function(reg,patient_id) {
            if(reg==undefined){
                swal(
                    'Error',
                    'Please Fill all fields',
                    'error'
                );
            }
            else if(reg.heid_no==undefined){
                swal(
                    'Error',
                    'Please Enter HIV Expose ID NUMBER',
                    'error'
                );
            }


            else{
                var regpost={facility_id:facility_id,patient_id:patient_id,user_id:user_id,heid_no:reg.heid_no,mother_id:1};
                ////console.log(regpost);
                $http.post('/api/hiv_ID_registration',regpost).then(function(data) {
                    $scope.child = data.data;
                    var msg=data.data.msg;
                    var status=data.data.status;
                    if (status==0){
                        swal(
                            'Error',
                            msg,
                            'error'
                        );
                    }
                    else{
                        swal(
                            'Response',
                            msg,
                            'success'
                        );
                    }
                });

            }


        }




        $scope.Child_vaccination_registration=function (vaccine,selectedPatient) {

            if(selectedPatient==undefined){
                swal(
                    'Error',
                    'Please choose Client First',
                    'error'
                )
            }   else if(vaccine.date==undefined){
                swal(
                    'Error',
                    'Please Enter Date for '+vaccine.vaccination_id.vaccination_name,
                    'error'
                )
            } else if(vaccine.place==undefined){
                swal(
                    'Error',
                    'Please Choose Place of This Client',
                    'error'
                )
            }
            else{

                var vaccinations={'vaccination_id':vaccine.vaccination_id.id,'mother_id':1,
                    'date':vaccine.date,'patient_id':selectedPatient,'facility_id':facility_id,
                    'place':vaccine.place ,
                    'user_id':user_id
                };
                //console.log(vaccinations);
                $http.post('/api/Child_vaccination_registration',vaccinations).then(function(data) {
                    $scope.vaccines = data.data;
                    var sending = data.data.msg;
                    var statusee = data.data.status;
                    vaccine=null;
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

 $scope.child_growth_registration=function (selectedPatient) {
 
   var height=  document.getElementById('height').value;
   var heightz=  document.getElementById('heightz').value;
   var heightp=  document.getElementById('heightp').value;
   var weight=  document.getElementById('weight').value;
   var weightz=  document.getElementById('weightz').value;
   var weightp=  document.getElementById('weightp').value;
   var followup_date=  document.getElementById('followup_date').value;
     var allDAta={height:height,weight:weight,heightz:heightz,heightp:heightp,weightz:weightz,weightp:weightp,
         followup_date:followup_date,
         'patient_id':selectedPatient,'facility_id':facility_id,
         'user_id':user_id}
     //console.log(height,heightz,heightp,weight,weightz,weightp);
   //  return;
            // if(selectedPatient==undefined){
            //     swal(
            //         'Error',
            //         'Please choose Client First',
            //         'error'
            //     )
            // }   else if(gr==undefined){
            //     swal(
            //         'Error',
            //         'Please Fill All fields ',
            //         'error'
            //     )
            // } else if(gr.weight==undefined){
            //     swal(
            //         'Error',
            //         'Please Enter Weight ',
            //         'error'
            //     )
            // }else if(gr.followup_date==undefined){
            //     swal(
            //         'Error',
            //         'Please Enter Follow Up Date ',
            //         'error'
            //     )
            // }
            // else{
            //
            //     var grs={'height':gr.height,
            //         weight:gr.weight,
            //         followup_date:gr.followup_date,
            //          'patient_id':selectedPatient,'facility_id':facility_id,
            //         'user_id':user_id};
            //     //console.log(grs);
                $http.post('/api/child_growth_registration',allDAta).then(function(data) {
                    $scope.gr = data.data;
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

       // }

$scope.child_deworm_registration=function (gr,selectedPatient) {

            if(selectedPatient==undefined){
                swal(
                    'Error',
                    'Please choose Client First',
                    'error'
                )
            }   else if(gr==undefined){
                swal(
                    'Error',
                    'Please Fill All fields ',
                    'error'
                )
            }else if(gr.date_attended==undefined){
                swal(
                    'Error',
                    'Please Date Attended ',
                    'error'
                )
            }
            else{

                var grs={'deworm_given':gr.deworm_given,'vitamin_given':gr.vitamin_given,'mother_id':1,date_attended:gr.date_attended,
                     'client_id':selectedPatient,'facility_id':facility_id,
                    'user_id':user_id};
                //console.log(grs);
                $http.post('/api/child_deworm_registration',grs).then(function(data) {
                    $scope.gr = data.data;
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

$scope.child_voucher_registration=function (gr,selectedPatient) {

            if(selectedPatient==undefined){
                swal(
                    'Error',
                    'Please choose Client First',
                    'error'
                )
            }   else if(gr==undefined){
                swal(
                    'Error',
                    'Please Fill All fields ',
                    'error'
                )
            } else if(gr.voucher_given==undefined){
                swal(
                    'Error',
                    'Please Fill Option For Subsidized Voucher Given column',
                    'error'
                )
            } else if(gr.date==undefined){
                swal(
                    'Error',
                    'Please Enter Date where Subsidized Voucher   Given',
                    'error'
                )
            }
            else{

                var grs={'voucher_given':gr.voucher_given,date:gr.date,
                     'patient_id':selectedPatient,'facility_id':facility_id,
                    'user_id':user_id};
                //console.log(grs);
                $http.post('/api/child_voucher_registration',grs).then(function(data) {
                    $scope.gr = data.data;
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

$scope.child_feed_registration=function (gr,selectedPatient) {

            if(selectedPatient==undefined){
                swal(
                    'Error',
                    'Please choose Client First',
                    'error'
                )
            }   else if(gr==undefined){
                swal(
                    'Error',
                    'Please Fill optional fields ',
                    'error'
                )
            }
            else{

                var grs={'feeding_type':gr.feeding_type,'mother_id':1,
                     'patient_id':selectedPatient,'facility_id':facility_id,
                    'user_id':user_id};
                //console.log(grs);
                $http.post('/api/child_feeding_registration',grs).then(function(data) {
                    $scope.gr = data.data;
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
                    opinion_type:2, facility_id:facility_id,patient_id:patient,user_id:user_id
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


    }



})();
/**
 * Created by USER on 2017-03-08.
 */