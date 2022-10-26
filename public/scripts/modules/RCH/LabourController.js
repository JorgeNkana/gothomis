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
        .controller('LabourController', LabourController);

    function LabourController($http, $auth, $rootScope,$state,$location,$scope,$timeout,Helper) {
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
                $scope.resdatas = data.data[4];
            });
        }
        $scope.ClinicQueue();

        $scope.Anti_natal_in_referral=function (selectedPatient) {

            $http.post('/api/Anti_incoming_referral', selectedPatient).then(function (data) {


                $scope.selectedPatient=data.data;


            });
            $scope.ClinicQueue();
        }
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
        $scope.department_list=function () {

            $http.get('/api/department_list').then(function(data) {
                $scope.departments=data.data;

            });
        }

        $scope.department_list();

        $http.get('/api/user_list').then(function(data) {
            $scope.users=data.data;
            ////console.log($scope.users)

        });
        var occupation=[];
        var resdata =[];
         

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


            $http.post('/api/searchRchpatient',{searchKey:searchKey}).then(function(data) {
                resdata = data.data;

            });


            return resdata;


        }





        $scope.anti_natal_registration = function(patient) {

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
            else if($scope.residence==undefined){
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


            else  if(patient.partner_name==undefined){
                swal(
                    'Error',
                    'Please Fill Partner Name',
                    'error'
                )
            }
            else  if(patient.occupation1==undefined){
                swal(
                    'Error',
                    'Please Fill Partner Occupation',
                    'error'
                )
            }
            else{


                var details={
                    facility_id:facility_id,user_id:user_id,
                    first_name:patient.first_name,
                    middle_name:patient.middle_name,
                    last_name:patient.last_name,
                    gender:'Female',
                    dob:patient.dob,
                    dob1:patient.dob1,
                    height:patient.height,
                    voucher_no:patient.voucher_no,
                    education1:patient.education1,
                    education:patient.education,
                    mobile_number:patient.mobile_number,
                    partner_name:patient.partner_name,
                    occupation_id:patient.occupation.id,
                    occupation_id1:patient.occupation1.id,
                    residence_id:$scope.residence.residence_id,

                };
                $http.post('/api/anti_natal_registration',details).then(function(data) {
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


        $scope.prev_preg_info_registration=function (preg_info,selectedPatient) {

            if(selectedPatient==undefined){
                swal(
                    'Error',
                    'Please choose Client First',
                    'error'
                )
            } else if(preg_info.number_of_pregnancy==undefined){
                swal(
                    'Error',
                    'Please Enter Number of pregnancy',
                    'error'
                )
            }else if(preg_info.number_of_delivery==undefined){
                swal(
                    'Error',
                    'Please Enter Number of delivery',
                    'error'
                )
            }else if(preg_info.number_alive_children==undefined){
                swal(
                    'Error',
                    'Please Enter Number of alive children',
                    'error'
                )
            }else if(preg_info.number_of_miscarriage==undefined){
                swal(
                    'Error',
                    'Please Enter Number of Miscarriage',
                    'error'
                )
            }else if(preg_info.year==undefined){
                swal(
                    'Error',
                    'Please Enter year of events above',
                    'error'
                )
            }
            else if(preg_info.lnmp==undefined){
                swal(
                    'Error',
                    'Please Enter LNMP',
                    'error'
                )
            }else if(preg_info.edd==undefined){
                swal(
                    'Error',
                    'Please Enter EDD',
                    'error'
                )
            }
            else if(preg_info.delivery_place==undefined){
                swal(
                    'Error',
                    'Please Enter Suggested Place of Delivery',
                    'error'
                )
            }
            else{

                var pregnancy_info={
                    'number_of_pregnancy':preg_info.number_of_pregnancy,
                    'number_of_delivery':preg_info.number_of_delivery,
                    'number_alive_children':preg_info.number_alive_children,
                    'number_of_miscarriage':preg_info.number_of_miscarriage,
                    'edd':preg_info.edd,
                    'lnmp':preg_info.lnmp,
                    'year':preg_info.year,
                    'delivery_place':preg_info.delivery_place.id,
                    'client_id':selectedPatient,'facility_id':facility_id,
                    'user_id':user_id};
                //console.log(pregnancy_info);
                $http.post('/api/prev_preg_info_registration',pregnancy_info).then(function(data) {
                    $scope.vaccines = data.data;
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

        $scope.prev_preg_info_indicator=function (prev_preg_indicator,selectedPatient) {

            if(prev_preg_indicator==undefined){
                swal(
                    'Error',
                    'Please Fill all fields',
                    'error'
                )
            } else if(prev_preg_indicator.fp_35_years_n_above==undefined){
                swal(
                    'Error',
                    'Mimba ya kwanza Zaidi ya miaka 35?',
                    'error'
                )
            }else if(prev_preg_indicator.lp_10_years_n_above==undefined){
                swal(
                    'Error',
                    'Mimba ya mwisho Zaidi ya miaka 10?',
                    'error'
                )
            } else if(prev_preg_indicator.delivery_method==undefined){
                swal(
                    'Error',
                    'Alijifungua Je?',
                    'error'
                )
            }else if(prev_preg_indicator.fbs_msb==undefined){
                swal(
                    'Error',
                    'Kuzaa mtoto Mfu au Kifo cha mtoto mchanga?',
                    'error'
                )
            }
            else if(prev_preg_indicator.miscarriage_three_plus==undefined){
                swal(
                    'Error',
                    'Kuharibika kwa mimba tatu au zaidi?',
                    'error'
                )
            }
            else if(prev_preg_indicator.heart_disease==undefined){
                swal(
                    'Error',
                    'Ana Ugonjwa wa Moyo?',
                    'error'
                )
            }
            else if(prev_preg_indicator.diabetic==undefined){
                swal(
                    'Error',
                    'Alishawahi kuugua Kisukari?',
                    'error'
                )
            }else if(prev_preg_indicator.tb==undefined){
                swal(
                    'Error',
                    'Alishawahi kuugua Kifua Kikuu?',
                    'error'
                )
            }
            else if(prev_preg_indicator.waist_disability==undefined){
                swal(
                    'Error',
                    'Anaulemavu wa Nyonga?',
                    'error'
                )
            }else if(prev_preg_indicator.high_bleeding==undefined){
                swal(
                    'Error',
                    'Kutoka Damu Nyingi wakati wa kujifungua?',
                    'error'
                )
            }
            else if(prev_preg_indicator.placenta_stacked==undefined){
                swal(
                    'Error',
                    ' Kondo la Nyuma Kukwama?',
                    'error'
                )
            }
            else{

                var pregnancy_indicator={

                    'fp_35_years_n_above':prev_preg_indicator.fp_35_years_n_above,
                    'lp_10_years_n_above':prev_preg_indicator.lp_10_years_n_above,
                    'delivery_method':prev_preg_indicator.delivery_method,
                    'high_bleeding':prev_preg_indicator.high_bleeding,
                    'fbs_msb':prev_preg_indicator.fbs_msb,
                    'miscarriage_three_plus':prev_preg_indicator.miscarriage_three_plus,
                    'heart_disease':prev_preg_indicator.heart_disease,
                    'diabetic':prev_preg_indicator.diabetic,
                    'tb':prev_preg_indicator.tb,
                    'waist_disability':prev_preg_indicator.waist_disability,
                    'placenta_stacked':prev_preg_indicator.placenta_stacked,
                    'client_id':selectedPatient,'facility_id':facility_id,
                    'user_id':user_id};
                //console.log(pregnancy_indicator);
                $http.post('/api/pregnancy_indicator',pregnancy_indicator).then(function(data) {
                    $scope.vaccines = data.data;
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
      


 $scope.admin_registration = function(adm,patient_id) {
            if (adm == undefined) {
                swal(
                    'Error',
                    'Please Fill Date and Time for This Admission',
                    'error'
                );
            }

            else {
                var admission = {
                    facility_id: facility_id, client_id: patient_id, user_id: user_id,

                    admission_date: adm.admission_date,
                    pregnancy_age: adm.pregnancy_age,
                    pregnancy_height: adm.pregnancy_height,

                };
////console.log(birth_infos);
                $http.post('/api/labour_admission_registration', admission).then(function (data) {
                    $scope.rch_4 = data.data;
                    var msg = data.data.msg;
                    var status = data.data.status;
                    if (status == 0) {
                        swal(
                            'Error',
                            msg,
                            'error'
                        );
                    }
                    else {
                        swal(
                            'Response',
                            msg,
                            'success'
                        );
                    }
                });

            }
        }
        
        
        
        $scope.delivery_registration = function(deli,patient_id) {
            var shona=null;
            if(deli.delivery_date == undefined) {
                swal(
                    'Error',
                    'Please Fill Date and Time of Delivery',
                    'error'
                );
            }


            else {
                if(deli.msamba !="HAUJACHANIKA" && deli.tailer_id==undefined){
                    swal(
                        'Error',
                        'Jina la Aliyeshona ni nani?',
                        'error'
                    );
                    return;
                } else if(deli.msamba =="HAUJACHANIKA" ){
                    shona==null ;
                }
                else{
                     shona=deli.tailer_id.id;
                }
                var delis = {
                    facility_id: facility_id, client_id: patient_id, user_id: user_id,
                    delivery_date: deli.delivery_date,
                    place_of_delivery: deli.place_of_delivery,
                    number_of_newborn: deli.number_of_newborn,
                    method_of_delivery: deli.method_of_delivery,
                    midwife_name: deli.midwife_name,
                    vitamin_given: deli.vitamin_given,
                    reason_for_scisoring: deli.reason_for_scisoring,
                    placenter_removed: deli.placenter_removed,
                    placenter_removed_date: deli.placenter_removed_date,
                    blood_discharged: deli.blood_discharged,
                    labour_catalyst: deli.labour_catalyst,
                    msamba: deli.msamba,
                    comment: deli.comment,
                    bp: deli.bp,
                    tailer_id: shona,

                };
 //console.log(delis);
                $http.post('/api/labour_delivery_registration', delis).then(function (data) {
                    $scope.rch_4 = data.data;
                    var msg = data.data.msg;
                    var status = data.data.status;
                    if (status == 0) {
                        swal(
                            'Error',
                            msg,
                            'error'
                        );
                    }
                    else {
                        swal(
                            'Response',
                            msg,
                            'success'
                        );
                    }
                });

            }
        }




        $scope.child_referral_registration11=function (ref,selectedPatient,client) {

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
                    'sender_clinic_id':21,
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

        $scope.newborn_info_registration=function (ref,selectedPatient,client) {

            if(selectedPatient==undefined){
                swal(
                    'Error',
                    'Please choose Client First',
                    'error'
                )
            } else if(ref==undefined){
                swal(
                    'Error',
                    'Please Fill all Fields  ',
                    'error'
                )
            }else if(ref.gender==undefined){
                swal(
                    'Error',
                    'Please Select Newborn Gender  ',
                    'error'
                )
            }
            else if(ref.first_minute_score==undefined) {
                swal(
                    'Error',
                    '  APGAR  SCORE 1 DAKIKA',
                    'error'
                )
            }
            else if(ref.newborn_weight==undefined) {
                swal(
                    'Error',
                    '  newborn weight',
                    'error'
                )
            } else if(ref.fifth_minute_score==undefined) {
                swal(
                    'Error',
                    '  APGAR  SCORE 5 DAKIKA',
                    'error'
                )
            }

            else if(ref.breast_feeding_within_hour==undefined){
                swal(
                    'Error',
                    'Please Choose option for  Breast feeding within hour ',
                    'error'
                )
            }
            else{

                var borns={
                    'gender':ref.gender,
                    'newborn_weight':ref.newborn_weight,
                    'first_minute_score':ref.first_minute_score,
                    'fifth_minute_score':ref.fifth_minute_score,
                    'breast_feeding_within_hour':ref.breast_feeding_within_hour,
                    'client_id':selectedPatient,'facility_id':facility_id,
                    'user_id':user_id};
               // //console.log(borns);
                $http.post('/api/newborn_info_registration',borns).then(function(data) {
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


$scope.labour_fsb_msb_registration=function (ref,selectedPatient,client) {

            if(selectedPatient==undefined){
                swal(
                    'Error',
                    'Please choose Client First',
                    'error'
                )
            } else if(ref==undefined){
                swal(
                    'Error',
                    'Please Fill  Field  ',
                    'error'
                )
            }
            else{

                var borns={
                     'fsb_msb':ref.fsb_msb,
                    'patient_id':selectedPatient,'facility_id':facility_id,
                    'user_id':user_id};
               // //console.log(borns);
                $http.post('/api/labour_fsb_msb_registration',borns).then(function(data) {
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

$scope.labour_fgm_registration=function (ref,selectedPatient,client) {

            if(selectedPatient==undefined){
                swal(
                    'Error',
                    'Please choose Client First',
                    'error'
                )
            } else if(ref==undefined){
                swal(
                    'Error',
                    'Please Fill  Field  ',
                    'error'
                )
            }
            else{

                var borns={
                     'fgm':ref.fgm,
                    'patient_id':selectedPatient,'facility_id':facility_id,
                    'user_id':user_id};
               // //console.log(borns);
                $http.post('/api/labour_fgm_registration',borns).then(function(data) {
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


$scope.labour_complication_registration=function (ref,selectedPatient,client) {
    //console.log(ref);
            if(selectedPatient==undefined){
                swal(
                    'Error',
                    'Please choose Client First',
                    'error'
                )
            }  
            else{

                var borns={
                     'vaginal_bleeding':ref.vaginal_bleeding,'prom':ref.prom,'preeclampsia':ref.preeclampsia,'eclampsia':ref.eclampsia,
                     'anaemia':ref.anaemia, 'sepsis':ref.sepsis,'malaria':ref.malaria, 'hiv_p':ref.hiv_p,
                     'pph':ref.pph, 'fgm':ref.fgm,'obstructed_labour':ref.obstructed_labour, 'three_tear':ref.three_tear,
                     'retained_placenta':ref.retained_placenta, 'chest_pain':ref.chest_pain,
                     'loss_strength':ref.loss_strength, 'other_complication':ref.other_complication,
                    'patient_id':selectedPatient,'facility_id':facility_id,
                    'user_id':user_id};
               // //console.log(borns);
                $http.post('/api/labour_complication_registration',borns).then(function(data) {
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


$scope.labour_observation_registration=function (obs,selectedPatient) {

            if(obs.amniotic_bust==undefined){
                swal(
                    'Error',
                    'Please Fill Some of the Important Fields',
                    'error'
                )
            }
            else{

                var records={

                     'labour_start_date':obs.labour_start_date,'amniotic_bust':obs.amniotic_bust,
                     'amniotic_bust_date':obs.amniotic_bust_date,'baby_possition':obs.baby_possition,
                     'baby_pointer':obs.baby_pointer,'sacral_promontary_reached':obs.sacral_promontary_reached,
                     'ischial_spine_apeared':obs.ischial_spine_apeared,'narrow_outlet':obs.narrow_outlet,
                     'large_servix':obs.large_servix,'temperature':obs.temperature,
                     'hb':obs.hb,'bp':obs.bp,
                     'blood_bleeding':obs.blood_bleeding,'baby_heart_beat':obs.baby_heart_beat,
                     'comment':obs.comment,
                    'client_id':selectedPatient,'facility_id':facility_id,
                    'user_id':user_id};
                //console.log(records);
                $http.post('/api/labour_observation_registration',records).then(function(data) {
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

        $scope.labour_child_feeding_registration=function (feed,selectedPatient) {

            if(selectedPatient==undefined){
                swal(
                    'Error',
                    'Please choose Client First',
                    'error'
                )
            }

            else if( feed==undefined) {


                swal(
                    'Error',
                    'Please Fill Feeding Type ',
                    'error'
                )
            }

            else{



                var feeds={
                    'feeding_type':feed.feeding_type,
                    'patient_id':selectedPatient,'facility_id':facility_id,
                    'user_id':user_id};
                ////console.log(arvs);
                $http.post('/api/labour_child_feeding_registration',feeds).then(function(data) {
                    $scope.vaccines = data.data;
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

        $scope.labour_mother_disposition_registration=function (feed,selectedPatient) {

            if(selectedPatient==undefined){
                swal(
                    'Error',
                    'Please choose Client First',
                    'error'
                )
            }

            else if( feed==undefined) {


                swal(
                    'Error',
                    'Please Fill all Fields required ',
                    'error'
                )
            }

            else{



                var feeds={
                    'alive':feed.alive,
                    'disposition_date':feed.disposition_date,
                    'death_date':feed.death_date,
                    'death_reason':feed.death_reason,
                    'patient_id':selectedPatient,'facility_id':facility_id,
                    'user_id':user_id};
                ////console.log(arvs);
                $http.post('/api/labour_mother_disposition_registration',feeds).then(function(data) {
                    $scope.vaccines = data.data;
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

 $scope.labour_child_disposition_registration=function (feed,selectedPatient) {

            if(selectedPatient==undefined){
                swal(
                    'Error',
                    'Please choose Client First',
                    'error'
                )
            }

            else if( feed==undefined) {


                swal(
                    'Error',
                    'Please Fill all Fields required ',
                    'error'
                )
            }

            else{



                var feeds={
                    'alive':feed.alive,
                    'disposition_date':feed.disposition_date,
                    'death_date':feed.death_date,
                    'death_reason':feed.death_reason,
                    'patient_id':selectedPatient,'facility_id':facility_id,
                    'user_id':user_id};
                ////console.log(arvs);
                $http.post('/api/labour_child_disposition_registration',feeds).then(function(data) {
                    $scope.vaccines = data.data;
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


        $scope.getprev_preg_info = function(patient) {

            $http.get('/api/getprev_preg_info/'+ patient).then(function (data) {
                $scope.histories = data.data;

            });
        }


        $scope.printLabour=function () {

            //location.reload();
            var DocumentContainer = document.getElementById('printlabour');
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
/**
 * Created by USER on 2017-03-08.
 */