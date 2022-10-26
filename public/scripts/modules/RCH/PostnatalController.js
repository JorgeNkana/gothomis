/**
 * Created by USER on 2017-03-27.
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
        .controller('PostnatalController', PostnatalController);

    function PostnatalController($http, $auth, $rootScope,$state,$location,$scope,$timeout,Helper) {
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

        $scope.ClinicQueue=function () {
            $http.get('/api/searchClinicpatientQueue/'+facility_id).then(function(data) {
                $scope.resdatas = data.data[3];
            });
        }
        $scope.ClinicQueue();
        $scope.department_list=function () {

            $http.get('/api/department_list').then(function(data) {
                $scope.departments=data.data;

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
        $scope.department_list();
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


            $http.post('/api/searchRchpatient',{searchKey:searchKey}).then(function(data) {
                resdata = data.data;

            });


            return resdata;


        }
        $scope.showARVITEM = function(searchKey) {
            if(searchKey.length<4){

            }
            else{
                $http.post('/api/searchItem',{searchKey:searchKey}).then(function(data) {
                    resdata = data.data;
                });
                return resdata;

            }

        }

        $scope.Post_natal_serial_no = function(patient) {

            $http.post('/api/Post_natal_serial_no',{facility_id:facility_id,patient_id:patient,user_id:user_id}).then(function(data) {
                $scope.serial_no = data.data;

            });


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
            } else   if(patient.height==undefined){
                swal(
                    'Error',
                    'Please Fill Height',
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


        $scope.vaccination_list=function () {
            $http.get('/api/vaccination_list').then(function (data) {
                $scope.vaccinations = data.data;

            });
        }

        $scope.vaccination_list();



        $scope.post_natal_registration = function(reg,patient_id) {
            if(reg==undefined){
                swal(
                    'Error',
                    'Please Enter RCH-4 Number',
                    'error'
                );
            }
           else if(reg.rch_4==undefined){
                swal(
                    'Error',
                    'Please Enter RCH-4 Number',
                    'error'
                );
            }

else if(reg.post_natal_reg_date==undefined){
                swal(
                    'Error',
                    'Please Enter Date for Post natal registration',
                    'error'
                );
            }
else{
    var regpost={facility_id:facility_id,patient_id:patient_id,user_id:user_id,rch_4:reg.rch_4,post_natal_reg_date:reg.post_natal_reg_date};
                ////console.log(regpost);
                $http.post('/api/post_natal_registration_update',regpost).then(function(data) {
        $scope.rch_4 = data.data;
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

//         $scope.prev_preg_info_registration = function(birth_info,patient_id) {
//             if (birth_info == undefined) {
//                 swal(
//                     'Error',
//                     'Please Fill all fields required',
//                     'error'
//                 );
//             }
//             else if (birth_info.number_of_delivery == undefined) {
//                 swal(
//                     'Error',
//                     'Please Enter Number of Delivery',
//                     'error'
//                 );
//             }
//             else if (birth_info.delivery_date == undefined) {
//                 swal(
//                     'Error',
//                     'Please Enter date of Delivery',
//                     'error'
//                 );
//             }
//             else if (birth_info.place_of_delivery == undefined) {
//                 swal(
//                     'Error',
//                     'Please Enter Place of Delivery',
//                     'error'
//                 );
//             }
//             else if (birth_info.midwife_proffesion == undefined) {
//                 swal(
//                     'Error',
//                     'Please Enter Midwife profession',
//                     'error'
//                 );
//             }
//
//             else if (birth_info.mother_status == undefined) {
//                 swal(
//                     'Error',
//                     'Please Enter Status Of Mother',
//                     'error'
//                 );
//             }
//
//             else if (birth_info.number_of_newborn == undefined) {
//                 swal(
//                     'Error',
//                     'Please Enter Number of Newborns',
//                     'error'
//                 );
//             }
//             else if (birth_info.number_of_newborn_alive == undefined) {
//                 swal(
//                     'Error',
//                     'Please Enter Number of Newborns Alive',
//                     'error'
//                 );
//             }
//             else if (birth_info.number_of_newborn_died == undefined) {
//                 swal(
//                     'Error',
//                     'Please Enter Number of Newborns Died',
//                     'error'
//                 );
//             }
//
//
//             else {
//                 var birth_infos = {
//                     facility_id: facility_id, patient_id: patient_id, user_id: user_id,
//                     number_of_newborn_died: birth_info.number_of_newborn_died,
//                     number_of_newborn_alive: birth_info.number_of_newborn_alive,
//                     number_of_newborn: birth_info.number_of_newborn,
//                     mother_status: birth_info.mother_status,
//                     midwife_proffesion: birth_info.midwife_proffesion,
//                     place_of_delivery: birth_info.place_of_delivery,
//                     number_of_delivery: birth_info.number_of_delivery,
//                     delivery_date: birth_info.delivery_date,
//                 };
// ////console.log(birth_infos);
//                 $http.post('/api/post_birth_info_registration', birth_infos).then(function (data) {
//                     $scope.rch_4 = data.data;
//                     var msg = data.data.msg;
//                     var status = data.data.status;
//                     if (status == 0) {
//                         swal(
//                             'Error',
//                             msg,
//                             'error'
//                         );
//                     }
//                     else {
//                         swal(
//                             'Response',
//                             msg,
//                             'success'
//                         );
//                     }
//                 });
//
//             }
//         }
            $scope.baby_feed_registration = function(feed,patient_id) {
            if(feed==undefined){
                swal(
                    'Error',
                    'Please Fill Option',
                    'error'
                );
            }


else{
    var feeds={facility_id:facility_id,patient_id:patient_id,user_id:user_id,
        baby_breastfeeding_within_hour:feed.baby_breastfeeding_within_hour,

    };

                $http.post('/api/baby_feed_registration',feeds).then(function(data) {
        $scope.rch_4 = data.data;
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


 $scope.pmtct_registration = function(pmtct,patient_id) {
            if(pmtct==undefined){
                swal(
                    'Error',
                    'Please Fill Option',
                    'error'
                );
            }
     else if(pmtct.anti_natal_vvu_infection_status==undefined){
                swal(
                    'Error',
                    'Please Fill Option for Anti Natal VVU Infection Status',
                    'error'
                );
            }else if(pmtct.post_natal_vvu_infection_status==undefined){
                swal(
                    'Error',
                    'Please Fill Option for Post Natal VVU Infection Status',
                    'error'
                );
            }


else{
    var pmtcts={facility_id:facility_id,patient_id:patient_id,user_id:user_id,
        anti_natal_vvu_infection_status:pmtct.anti_natal_vvu_infection_status,
        post_natal_vvu_infection_status:pmtct.post_natal_vvu_infection_status,

    };

                $http.post('/api/pmtct_post_registration',pmtcts).then(function(data) {
        $scope.rch_4 = data.data;
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

 $scope.reattendance_registration = function(attendance,patient_id) {
            if(attendance==undefined){
                swal(
                    'Error',
                    'Please Fill Option for Attendance Range',
                    'error'
                );
            }
     else if(attendance.attendance_range==undefined){
                swal(
                    'Error',
                    'Please Fill Option for attendance range',
                    'error'
                );
            }else if(attendance.date_attended==undefined){
                swal(
                    'Error',
                    'Please Fill Date Attended',
                    'error'
                );
            }else if(attendance.temperature==undefined){
                swal(
                    'Error',
                    'Please Fill Body Temperature',
                    'error'
                );
            }
            else if(attendance.hb==undefined){
                swal(
                    'Error',
                    'Please Fill HB',
                    'error'
                );
            } else if(attendance.bp==undefined){
                swal(
                    'Error',
                    'Please Fill BP',
                    'error'
                );
            }


else{
    var attendances={facility_id:facility_id,patient_id:patient_id,user_id:user_id,
        attendance_range:attendance.attendance_range,
        date_attended:attendance.date_attended,
        temperature:attendance.temperature,
        hb:attendance.hb,
        bp:attendance.bp,
        followup_date:attendance.followup_date,

    };

                $http.post('/api/post_reattendance_registration',attendances).then(function(data) {
        $scope.rch_4 = data.data;
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


        $scope.post_chilreattendance_registration = function(chilreattendance,patient_id) {
            if(chilreattendance==undefined){
                swal(
                    'Error',
                    'Please Fill all Options',
                    'error'
                );
            }
              else if(chilreattendance.attendance_range==undefined){
                swal(
                    'Error',
                    'Please Fill Option for Attendance Range',
                    'error'
                );
            }
            else if(chilreattendance.date_attended==undefined){
                swal(
                    'Error',
                    'Please Fill Date Attended',
                    'error'
                );
            }
            else if(chilreattendance.temperature==undefined){
                swal(
                    'Error',
                    'Please Fill Body Temperature',
                    'error'
                );
            }
            else if(chilreattendance.hb==undefined){
                swal(
                    'Error',
                    'Please Fill HB',
                    'error'
                );
            } else if(chilreattendance.bp==undefined){
                swal(
                    'Error',
                    'Please Fill BP',
                    'error'
                );
            }


            else{
                var chilreattendances={facility_id:facility_id,patient_id:patient_id,user_id:user_id,
                    date_attended:chilreattendance.date_attended,
                    attendance_range:chilreattendance.attendance_range,
                    temperature:chilreattendance.temperature,
                    hb:chilreattendance.hb,
                    bp:chilreattendance.bp,
                    followup_date:chilreattendance.followup_date,

                };

                $http.post('/api/post_chilreattendance_registration',chilreattendances).then(function(data) {
                    $scope.rch_4 = data.data;
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




 $scope.breast_registration = function(breast,patient_id) {
            if(breast==undefined){
                swal(
                    'Error',
                    'Please Fill all Option for Breast status',
                    'error'
                );
            }
     else if(breast.breast_rupture==undefined){
                swal(
                    'Error',
                    'Please Fill Option for Breast Rupture',
                    'error'
                );
            }else if(breast.mastitis==undefined){
                swal(
                    'Error',
                    'Please enter option for Mastitis',
                    'error'
                );
            }
else if(breast.abscess==undefined){
                swal(
                    'Error',
                    'Please enter option for Abscess  ',
                    'error'
                );
            }else if(breast.breast_contriction ==undefined){
                swal(
                    'Error',
                    'Please enter option for Breast constriction ',
                    'error'
                );
            }


else{
    var breasts={facility_id:facility_id,patient_id:patient_id,user_id:user_id,
        breast_rupture:breast.breast_rupture,
        mastitis:breast.mastitis,
        abscess:breast.abscess,
        breast_contriction:breast.breast_contriction,

    };

                $http.post('/api/breast_registration',breasts).then(function(data) {
        $scope.rch_4 = data.data;
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

 $scope.post_natal_observation_lists_registration = function(part) {
            if(part==undefined){
                swal(
                    'Error',
                    'Please Fill all Option',
                    'error'
                );
            }



else{
    var parts={description:part.description


    };

                $http.post('/api/post_natal_observation_lists_registration',parts).then(function(data) {
        $scope.parts = data.data;
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
            $scope.observation_lists();
        }
    });

}


        }

        $scope.observation_lists=function () {
            $http.get('/api/post_natal_observation_lists').then(function (data) {
                $scope.descriptions = data.data;


            });
        }

        $scope.observation_lists();

        $scope.description_lists=function (id) {
            $http.get('/api/post_natal_observation_description_list/'+id).then(function(data) {
                $scope.observations = data.data;

            });
        }
        //$scope.description_lists();

$scope.post_natal_observation_descriptions = function(desc) {
    //console.log(desc);
            if(desc==undefined){
                swal(
                    'Error',
                    'Please Fill all Option',
                    'error'
                );
            }
    if(desc.observation_id==undefined){
                swal(
                    'Error',
                    'Please Choose Observation Part',
                    'error'
                );
            }
           if(desc.observation==undefined){
                swal(
                    'Error',
                    'Please Choose Observation',
                    'error'
                );
            }




else{
    var descs={
        observation_id:desc.observation_id,
        observation:desc.observation,


    };

                $http.post('/api/post_natal_observation_descriptions',descs).then(function(data) {
        $scope.descs = data.data;
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
                    $scope.description_lists();
    });

}


        }
$scope.statuses=[];
        $scope.post_natal_observation_status = function(observation,patient_id) {
    //console.log(observation,patient_id);




            if(observation==undefined){
                swal(
                    'Error',
                    'Please Fill all Option',
                    'error'
                );
            }



else{
                var i;
for( i=0; i<observation.length; i++){

if(observation.status==undefined){

}
else{


    $scope.statuses.push({'client_id':patient_id,'facility_id':facility_id,'user_id':user_id,
    'status':observation.status,'observation_id':observation[i].id});

}
}
                //console.log($scope.statuses);

                $http.post('/api/post_natal_observation_status',$scope.statuses).then(function(data) {
        $scope.descs = data.data;
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
                    $scope.statuses=[];

    });

}


  }


 $scope.dehiscence_registration = function(dehis,patient_id) {
            if(dehis==undefined){
                swal(
                    'Error',
                    'Please Fill all Options',
                    'error'
                );
            }
     else if(dehis.dehiscence_join==undefined){
                swal(
                    'Error',
                    'Please Fill Option for Dehiscence Status',
                    'error'
                );
            }else if(dehis.mental_ability==undefined){
                swal(
                    'Error',
                    'Please enter option for Mental Status',
                    'error'
                );
            }
else if(dehis.fistula==undefined){
                swal(
                    'Error',
                    'Please enter option for Fistula  ',
                    'error'
                );
            } 

else{
    var dehises={facility_id:facility_id,patient_id:patient_id,user_id:user_id,
        dehiscence_join:dehis.dehiscence_join,
        mental_ability:dehis.mental_ability,
        fistula:dehis.fistula,
        
         


    };

                $http.post('/api/dehiscence_registration',dehises).then(function(data) {
        $scope.rch_4 = data.data;
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

        $scope.post_additional_medication_registration = function(medic,patient_id) {
            if(medic==undefined){
                swal(
                    'Error',
                    'Please Fill all Options',
                    'error'
                );
            }
     else if(medic.ferrous_sulphate==undefined){
                swal(
                    'Error',
                    'Please Fill Option for Ferrous Sulphate',
                    'error'
                );
            }else if(medic.folic_acid==undefined){
                swal(
                    'Error',
                    'Please Fill Option for Folic Acid  Given',
                    'error'
                );
            }
else if(medic.vitamin_a==undefined){
                swal(
                    'Error',
                    'Please enter option for Vitamin A  Given ',
                    'error'
                );
            }

else{
    var medics={facility_id:facility_id,patient_id:patient_id,user_id:user_id,
        ferrous_sulphate:medic.ferrous_sulphate,
        folic_acid:medic.folic_acid,
        fs_quantity:medic.fs_quantity,
        fa_quantity:medic.fa_quantity,
        vitamin_a:medic.vitamin_a,
        other_medics:medic.other_medics,

    };

                $http.post('/api/post_additional_medication_registration',medics).then(function(data) {
        $scope.rch_4 = data.data;
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

$scope.post_family_planing_registration = function(family,patient_id) {
            if(family==undefined){
                swal(
                    'Error',
                    'Please Fill all Options',
                    'error'
                );
            }
     else if(family.counselling_given==undefined){
                swal(
                    'Error',
                    'Please Fill Option for Counselling column',
                    'error'
                );
            }  else if(family.iec_material_given==undefined){
                swal(
                    'Error',
                    'Please Fill Option for IEC material column',
                    'error'
                );
            }
            else if(family.referral_for_family_planning==undefined){
                swal(
                    'Error',
                    'Please Fill Option for Referral column',
                    'error'
                );
            }

else{
    var families={facility_id:facility_id,patient_id:patient_id,user_id:user_id,
        referral_for_family_planning:family.referral_for_family_planning,
        iec_material_given:family.iec_material_given,
        counselling_given:family.counselling_given,

    };

                $http.post('/api/post_family_planing_registration',families).then(function(data) {
        $scope.rch_4 = data.data;
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




$scope.post_child_inv_registration = function(chilinvest,patient_id) {
            if(chilinvest==undefined){
                swal(
                    'Error',
                    'Please Fill all Options',
                    'error'
                );
            }
     else if(chilinvest.temperature==undefined){
                swal(
                    'Error',
                    'Please Fill Temperature',
                    'error'
                );
            }  else if(chilinvest.weight==undefined){
                swal(
                    'Error',
                    'Please Fill Weight',
                    'error'
                );
            }
            else if(chilinvest.hb==undefined){
                swal(
                    'Error',
                    'Please Fill HB',
                    'error'
                );
            } else if(chilinvest.kmc==undefined){
                swal(
                    'Error',
                    'Please Fill KMC',
                    'error'
                );
            }

else{
    var chilinvests={facility_id:facility_id,patient_id:patient_id,user_id:user_id,
        temperature:chilinvest.temperature,
        weight:chilinvest.weight,
        hb:chilinvest.hb,
        kmc:chilinvest.kmc,


    };

                $http.post('/api/post_child_inv_registration',chilinvests).then(function(data) {
        $scope.rch_4 = data.data;
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



        $scope.tt_vaccination_registration=function (tt_vaccine,selectedPatient) {
            var vaccination_id="";
            var vaccination_name="";

            if(selectedPatient==undefined){
                swal(
                    'Error',
                    'Please choose Client First',
                    'error'
                )
            }  else if(tt_vaccine.number_of_tt_given==undefined){
                swal(
                    'Error',
                    'Please Enter number of TT given ',
                    'error'
                )
            }
            else if(tt_vaccine.enough==undefined){
                swal(
                    'Error',
                    'Please Choose Option For Quantity Given ',
                    'error'
                )
            }
            else if(tt_vaccine.enough=='NO' && tt_vaccine.vaccination_date==undefined) {


                swal(
                    'Error',
                    'Please Enter Date for ' + tt_vaccine.vaccination_id.vaccination_name,
                    'error'
                )
            }





            else{
                if(tt_vaccine.enough=='NO'){
                    vaccination_id=  tt_vaccine.vaccination_id.id;
                    vaccination_name=  vaccination_id.vaccination_name;
                }


                var tt_vaccination={'vaccination_id':vaccination_id,

                    'number_of_tt_given':tt_vaccine.number_of_tt_given,
                    'enough':tt_vaccine.enough,
                    'tt_name':tt_vaccine.vaccination_name,
                    'vaccination_date':tt_vaccine.vaccination_date,'patient_id':selectedPatient,'facility_id':facility_id,
                    'user_id':user_id};
                ////console.log(tt_vaccination);
                $http.post('/api/post_tt_vaccination_registration',tt_vaccination).then(function(data) {
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

 $scope.child_vaccination_registration=function (child_vaccine,selectedPatient) {

            if(selectedPatient==undefined){
                swal(
                    'Error',
                    'Please choose Client First',
                    'error'
                )
            }

            else if( child_vaccine==undefined) {


                swal(
                    'Error',
                    'Please Fill all Fields ',
                    'error'
                )
            }
            else if( child_vaccine.vaccination_date==undefined) {


                swal(
                    'Error',
                    'Please Enter Date for ' + child_vaccine.vaccination_id.vaccination_name,
                    'error'
                )
            }

            else{


                     ;



                var chil_vaccination={'vaccination_id':child_vaccine.vaccination_id.id,



                    'vaccination_date':child_vaccine.vaccination_date,'patient_id':selectedPatient,'facility_id':facility_id,
                    'user_id':user_id};
                ////console.log(chil_vaccination);
                $http.post('/api/child_vaccination_registration',chil_vaccination).then(function(data) {
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

 $scope.child_infection_registration=function (child_inf,selectedPatient) {

            if(selectedPatient==undefined){
                swal(
                    'Error',
                    'Please choose Client First',
                    'error'
                )
            }

            else if( child_inf==undefined) {


                swal(
                    'Error',
                    'Please Fill all Fields ',
                    'error'
                )
            }
            else if( child_inf.navel==undefined) {


                swal(
                    'Error',
                    'Please Fill for Novel status ',
                    'error'
                )
            }
            else if( child_inf.mouth==undefined) {


                swal(
                    'Error',
                    'Please Fill for Mouth status ',
                    'error'
                )
            }
 else if( child_inf.skin==undefined) {


                swal(
                    'Error',
                    'Please Fill for Skin status ',
                    'error'
                )
            }
 else if( child_inf.eye==undefined) {


                swal(
                    'Error',
                    'Please Fill for Eye status ',
                    'error'
                )
            }else if( child_inf.jaundice==undefined) {


                swal(
                    'Error',
                    'Please Fill for Jaundice status ',
                    'error'
                )
            }else if( child_inf.high_infection==undefined) {


                swal(
                    'Error',
                    'Please Fill for High Infection status ',
                    'error'
                )
            }

            else{


                     ;



                var child_infs={
                    'navel':child_inf.navel,
                    'skin':child_inf.skin,
                    'mouth':child_inf.mouth,
                    'eye':child_inf.eye,
                    'jaundice':child_inf.jaundice,
                    'high_infection':child_inf.high_infection,
                    'patient_id':selectedPatient,'facility_id':facility_id,
                    'user_id':user_id};
                ////console.log(child_infs);
                $http.post('/api/child_infection_registration',child_infs).then(function(data) {
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

        $scope.post_natal_arv_registration=function (arv,selectedPatient) {

            if(selectedPatient==undefined){
                swal(
                    'Error',
                    'Please choose Client First',
                    'error'
                )
            }

            else if( arv==undefined) {


                swal(
                    'Error',
                    'Please Fill all Fields ',
                    'error'
                )
            }
            else if( arv.selectedItem.id==undefined) {


                swal(
                    'Error',
                    'Please Select ARV Name ',
                    'error'
                )
            } else if( arv.time==undefined) {


                swal(
                    'Error',
                    'Please Enter Time of ARV Usage ',
                    'error'
                )
            }

            else{



                var arvs={
                    'arv_id':arv.selectedItem.id,
                    'time':arv.time,

                    'patient_id':selectedPatient,'facility_id':facility_id,
                    'user_id':user_id};
                ////console.log(arvs);
                $http.post('/api/post_natal_arv_registration',arvs).then(function(data) {
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

 $scope.post_natal_feeding_registration=function (feed,selectedPatient) {

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
                $http.post('/api/post_natal_feeding_registration',feeds).then(function(data) {
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
                    'sender_clinic_id':20,
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

        $scope.Anti_natal_in_referral=function (selectedPatient) {

            $http.post('/api/Anti_incoming_referral', selectedPatient).then(function (data) {


                $scope.selectedPatient=data.data;


            });
            $scope.ClinicQueue();
        }

    }

})();
/**
 * Created by USER on 2017-03-08.
 */