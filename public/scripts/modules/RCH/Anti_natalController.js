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
        .controller('Anti_natalController', Anti_natalController);

    function Anti_natalController($http, $auth, $rootScope,$state,$location,$scope,$timeout,Helper) {
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

        $scope.ClinicQueue=function () {
            $http.get('/api/searchClinicpatientQueue/'+facility_id).then(function(data) {
                $scope.resdatas = data.data[1];
            });
        }
        $scope.ClinicQueue();
        $http.get('/api/facility_list').then(function (data) {
            $scope.facility = data.data;
        });

        $scope.showSearch = function(searchKey) {


                $http.post('/api/searchRchpatient',{searchKey:searchKey}).then(function(data) {
                    resdata = data.data;

                });


                return resdata;

            
        }
        $scope.department_list=function () {

            $http.get('/api/department_list').then(function(data) {
                $scope.departments=data.data;

            });
        }

        $scope.department_list();
        $scope.SearchStds = function(searchKey) {

                $http.post('/api/SearchStds',{searchKey:searchKey}).then(function(data) {
                    resdata = data.data;

                });


                return resdata;


        }
        $scope.LNMP = function(client) {

            $http.get('/api/calculateWeek/' + client).then(function (data) {
                $scope.lnmp = data.data;
                //console.log( $scope.lnmp);
            });


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



        $scope.vaccination_list=function () {
            $http.get('/api/vaccination_list').then(function (data) {
                $scope.vaccinations = data.data;

            });
        }

        $scope.vaccination_list();

$scope.vaccination_registration=function (vaccine) {

    $http.post('/api/vaccination_registration',vaccine).then(function(data) {
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
        $scope.vaccination_list();
    });
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

        $scope.anti_natal_lab_results=function (lab_result,selectedPatient) {

if(lab_result==undefined){
    swal(
        'Error',
        'Please Fill all Fields',
        'error'
    )
} else if(lab_result.blood_group==undefined){
    swal(
        'Error',
        'Please Enter Blood Group',
        'error'
    )
} else if(lab_result.rh==undefined){
    swal(
        'Error',
        'Please Enter Rhesus',
        'error'
    )
}
else if(lab_result.vdrl_rpr==undefined){
    swal(
        'Error',
        'Please Enter vdrl/rpr',
        'error'
    )
}
else if(lab_result.pmtct==undefined){
    swal(
        'Error',
        'Please Enter PMTCT Status',
        'error'
    )
}
else if(lab_result.mrdt_bs==undefined){
    swal(
        'Error',
        'Please Choose MRDT or BS',
        'error'
    )
}else if(lab_result.result==undefined){
    swal(
        'Error',
        'Please Enter Results for  '+lab_result.mrdt_bs,
        'error'
    )
}

            else{

    var lab_results={
        'blood_group':lab_result.blood_group,
        'rh':lab_result.rh,
        'vdrl_rpr':lab_result.vdrl_rpr,
        'pmtct':lab_result.pmtct,
        'mrdt_bs':lab_result.mrdt_bs,
        'result':lab_result.result,
        'other_test':lab_result.other_test,
        'client_id':selectedPatient,'facility_id':facility_id,
        'user_id':user_id};
    //console.log(lab_results);
    $http.post('/api/anti_natal_lab_results',lab_results).then(function(data) {
        $scope.vaccines = data.data;
        var sending = data.data.msg;
        var statusee = data.data.status;
        if (statusee == 0) {
            swal(
                'Error',
                sending,
                'error'
            );

        }
        else {
            swal(
                'Feedback..',
                sending,
                'success'
            );
        }
        $scope.lab_result == null;
    });
}

}
        $scope.partner_lab_results=function (lab_result,selectedPatient) {

if(lab_result==undefined){
    swal(
        'Error',
        'Please Fill all Fields',
        'error'
    )
} else if(lab_result.blood_group==undefined){
    swal(
        'Error',
        'Please Enter Blood Group',
        'error'
    )
} else if(lab_result.rh==undefined){
    swal(
        'Error',
        'Please Enter Rhesus',
        'error'
    )
}
else if(lab_result.vdrl_rpr==undefined){
    swal(
        'Error',
        'Please Enter vdrl/rpr',
        'error'
    )
}
else if(lab_result.pmtct==undefined){
    swal(
        'Error',
        'Please Enter PMTCT Status',
        'error'
    )
}


            else{

    var lab_results={
        'blood_group':lab_result.blood_group,
        'rh':lab_result.rh,
        'vdrl_rpr':lab_result.vdrl_rpr,
        'pmtct':lab_result.pmtct,
        'other_test':lab_result.other_test,
        'client_id':selectedPatient,'facility_id':facility_id,
        'user_id':user_id};
    //console.log(lab_results);
    $http.post('/api/partner_lab_results',lab_results).then(function(data) {
        $scope.vaccines = data.data;
        var sending = data.data.msg;
        var statusee = data.data.status;
        if (statusee == 0) {
            swal(
                'Error',
                sending,
                'error'
            );

        }
        else {
            swal(
                'Feedback..',
                sending,
                'success'
            );
        }
        $scope.p_lab_result == null;
    });
}

}
        $scope.reattendance_registration=function (reattend,selectedPatient) {

if(selectedPatient==undefined){
    swal(
        'Error',
        'Please choose Client First',
        'error'
    )
} else if(reattend==undefined){
    swal(
        'Error',
        'Please Fill Fields required',
        'error'
    )
}
else if(reattend.hb==undefined){
    swal(
        'Error',
        'Please Fill for HB',
        'error'
    )
}
else if(reattend.bp==undefined){
    swal(
        'Error',
        'Please Fill for BP',
        'error'
    )
}
else if(reattend.urine_albumin==undefined){
    swal(
        'Error',
        'Jaza Albumin Kwenye Mkojo',
        'error'
    )
}
else if(reattend.urine_sugar==undefined){
    swal(
        'Error',
        'Jaza Sukari Kwenye Mkojo',
        'error'
    )
}else if(reattend.oedema==undefined){
    swal(
        'Error',
        'Jaza Kuvimba Miguu/Uso/Mikono',
        'error'
    )
}else if(reattend.date_attended==undefined){
    swal(
        'Error',
        'Jaza Tarehe ya Mahudhurio',
        'error'
    )
}
else if(reattend.followup_date==undefined){
    swal(
        'Error',
        'Jaza Tarehe ya Kurudi',
        'error'
    )
}


            else{

    var reattendance={
        'weight':reattend.weight,
        'hb':reattend.hb,
        'bp':reattend.bp,
        'urine_albumin':reattend.urine_albumin,
        'urine_sugar':reattend.urine_sugar,
        'pregnancy_height':reattend.pregnancy_height,
        'baby_position':reattend.baby_position,
        'baby_pointer':reattend.baby_pointer,
        'baby_play':reattend.baby_play,
        'baby_heart_beat':reattend.baby_heart_beat,
        'twins':reattend.twins,
        'oedema':reattend.oedema,
        'date_attended':reattend.date_attended,
        'followup_date':reattend.followup_date,

        'client_id':selectedPatient,'facility_id':facility_id,
        'user_id':user_id};
    //console.log(reattendance);
    $http.post('/api/reattendance_registration',reattendance).then(function(data) {
        $scope.vaccines = data.data;
        var sending = data.data.msg;
        var statusee = data.data.status;
        if (statusee == 0) {
            swal(
                'Error',
                sending,
                'error'
            );

        }
        else {
            swal(
                'Feedback..',
                sending,
                'success'
            );
        }
        $scope.investigation = null;
    });
}

}

        $scope.pmtct_registration=function (pmtct,selectedPatient) {

if(selectedPatient==undefined){
    swal(
        'Error',
        'Please choose Client First',
        'error'
    )
} else if(pmtct.vvu_infection==undefined){
    swal(
        'Error',
        'Please Fill Column for VVU Infection  ',
        'error'
    )
}
else if(pmtct.has_counsel_given==undefined){
    swal(
        'Error',
        'Please Fill Column for  Counselling Given  ',
        'error'
    )
}

else if(pmtct.has_taken_vvu_test==undefined){
    swal(
        'Error',
        'Please Fill Column for VVU test Taken ',
        'error'
    )
}

else if(pmtct.vvu_first_test_result==undefined){
    swal(
        'Error',
        'Please Fill Column for VVU First Test Result ',
        'error'
    )
}
else if(pmtct.counselling_after_vvu_test==undefined){
    swal(
        'Error',
        'Please Fill Column for Counselling After VVU Test ',
        'error'
    )
}
else if(pmtct.vvu_second_test_result==undefined){
    swal(
        'Error',
        'Please Fill Column for VVU Second Test Result ',
        'error'
    )
}
else if(pmtct.baby_feeding_counsel_given==undefined){
    swal(
        'Error',
        'Please Fill Column for Baby Feeding Counselling ',
        'error'
    )
}


            else{

    var pmtcts={


        'vvu_infection':pmtct.vvu_infection,
        'has_counsel_given':pmtct.has_counsel_given,
        'counselling_date':pmtct.counselling_date,
        'has_taken_vvu_test':pmtct.has_taken_vvu_test,
        'date_of_test_taken':pmtct.date_of_test_taken,
        'vvu_first_test_result':pmtct.vvu_first_test_result,
        'counselling_after_vvu_test':pmtct.counselling_after_vvu_test,
        'vvu_second_test_result':pmtct.vvu_second_test_result,
        'baby_feeding_counsel_given':pmtct.baby_feeding_counsel_given,

        'patient_id':selectedPatient,'facility_id':facility_id,
        'user_id':user_id};
     
    $http.post('/api/pmtct_registration',pmtcts).then(function(data) {
        $scope.pmtct = data.data;
        var sending = data.data.msg;
        var statusee = data.data.status;
        if (statusee == 0) {
            swal(
                'Error',
                sending,
                'error'
            );

        }
        else {
            swal(
                'Feedback..',
                sending,
                'success'
            );
        }
        $scope.pmtct = null;
    });
}

}

        $scope.pmtct_partner_registration=function (pmtct,selectedPatient) {

if(selectedPatient==undefined){
    swal(
        'Error',
        'Please choose Client First',
        'error'
    )
} else if(pmtct.vvu_infection==undefined){
    swal(
        'Error',
        'Please Fill Column for VVU Infection  ',
        'error'
    )
}
else if(pmtct.has_counsel_given==undefined){
    swal(
        'Error',
        'Please Fill Column for  Counselling Given  ',
        'error'
    )
}

else if(pmtct.has_taken_vvu_test==undefined){
    swal(
        'Error',
        'Please Fill Column for VVU test Taken ',
        'error'
    )
}

else if(pmtct.vvu_first_test_result==undefined){
    swal(
        'Error',
        'Please Fill Column for VVU First Test Result ',
        'error'
    )
}
else if(pmtct.counselling_after_vvu_test==undefined){
    swal(
        'Error',
        'Please Fill Column for Counselling After VVU Test ',
        'error'
    )
}
else if(pmtct.vvu_second_test_result==undefined){
    swal(
        'Error',
        'Please Fill Column for VVU Second Test Result ',
        'error'
    )
}



            else{

    var pmtcts={


        'vvu_infection':pmtct.vvu_infection,
        'has_counsel_given':pmtct.has_counsel_given,
        'counselling_date':pmtct.counselling_date,
        'has_taken_vvu_test':pmtct.has_taken_vvu_test,
        'date_of_test_taken':pmtct.date_of_test_taken,
        'vvu_first_test_result':pmtct.vvu_first_test_result,
        'counselling_after_vvu_test':pmtct.counselling_after_vvu_test,
        'vvu_second_test_result':pmtct.vvu_second_test_result,


        'patient_id':selectedPatient,'facility_id':facility_id,
        'user_id':user_id};

    $http.post('/api/pmtct_partner_registration',pmtcts).then(function(data) {
        $scope.pmtct = data.data;
        var sending = data.data.msg;
        var statusee = data.data.status;
        if (statusee == 0) {
            swal(
                'Error',
                sending,
                'error'
            );

        }
        else {
            swal(
                'Feedback..',
                sending,
                'success'
            );
        }
        $scope.pmtct = null;
    });
}

}
        $scope.preventives_registration=function (malaria,selectedPatient) {

if(malaria==undefined){
    swal(
        'Error',
        'Please choose Client First',
        'error'
    )
} else if(malaria.ferrous_sulphate==undefined){
    swal(
        'Error',
        'Please Fill Column for ferrous sulphate  ',
        'error'
    )
}else if(malaria.folic_acid==undefined){
    swal(
        'Error',
        'Please Fill Column for folic acid  ',
        'error'
    )
}else if(malaria.deworm==undefined){
    swal(
        'Error',
        '  Amepewa Dawa ya Minyoo ?',
        'error'
    )
}else if(malaria.malaria==undefined){
    swal(
        'Error',
        '  Amepewa dawa ya kujikinga na Malaria ?',
        'error'
    )
}else if(malaria.date_attended==undefined){
    swal(
        'Error',
        '  Tarehe ya Mahudhurio ?',
        'error'
    )
}


            else{

    var preventives={


        'ferrous_sulphate':malaria.ferrous_sulphate,
        'folic_acid':malaria.folic_acid,
        'malaria':malaria.malaria,
        'deworm':malaria.deworm,
        'date_attended':malaria.date_attended,

        'client_id':selectedPatient,'facility_id':facility_id,
        'user_id':user_id};
    //console.log(preventives);

    $http.post('/api/preventives_registration',preventives).then(function(data) {
        $scope.prevents = data.data;
        var sending = data.data.msg;
        var statusee = data.data.status;
        if (statusee == 0) {
            swal(
                'Error',
                sending,
                'error'
            );

        }
        else {
            swal(
                'Feedback..',
                sending,
                'success'
            );
        }
        $scope.malaria = null;
    });
}

}
        $scope.ipt_registration=function (ipt,selectedPatient) {

if(selectedPatient==undefined){
    swal(
        'Error',
        'Please choose Client First',
        'error'
    )
} else if(ipt.ipt==undefined){
    swal(
        'Error',
        'Please Choose IPT Number first',
        'error'
    )
}else if(ipt.ipt_date==undefined){
    swal(
        'Error',
        'Please Enter Date for   '+ipt.ipt,
        'error'
    )
}
            else{

    var ipts={


        'ipt':ipt.ipt,
        'ipt_date':ipt.ipt_date,

        'patient_id':selectedPatient,'facility_id':facility_id,
        'user_id':user_id};
    //console.log(ipts);

    $http.post('/api/ipt_registration',ipts).then(function(data) {
        $scope.pmtct = data.data;
        var sending = data.data.msg;
        var statusee = data.data.status;
        if (statusee == 0) {
            swal(
                'Error',
                sending,
                'error'
            );

        }
        else {
            swal(
                'Feedback..',
                sending,
                'success'
            );
        }
        $scope.ipt = null;
    });
}

}
        $scope.counselling_area_registration=function (councel) {

if(councel==undefined){
    swal(
        'Error',
        'Please Fill Column shown',
        'error'
    )
}
            else{




    $http.post('/api/counselling_area_registration',councel).then(function(data) {
        $scope.counsellings = data.data;
        var sending = data.data.msg;
        var statusee = data.data.status;
        if (statusee == 0) {
            swal(
                'Error',
                sending,
                'error'
            );

        }
        else {
            swal(
                'Feedback..',
                sending,
                'success'
            );
            $scope.counseling_aresa();
        }

    });
}

}

        $scope.counseling_aresa=function () {
            $http.get('/api/councelling_lists').then(function(data) {
                $scope.counselings = data.data;
            });
        }



        $scope.counseling_aresa();

$scope.counseling_givens=[];
        $scope.Add_counsel=function (data,patient) {
            if(data ==undefined){
                swal(
                    'Error',
                    'Choose Description you want',
                    'error'
                )
            }
           else if(data.description_id ==undefined){
                swal(
                    'Error',
                    'Choose Description you want',
                    'error'
                )
            } else if(data.description_id.id ==undefined){
                swal(
                    'Error',
                    'Choose Description you want',
                    'error'
                )
            }
           else  if(data.status ==undefined){
                swal(
                    'Error',
                    'Choose Description Status',
                    'error'
                )
            }
            else{



            $scope.counseling_givens.push({'facility_id':facility_id,'user_id':user_id,'description':data.description_id.description,'description_id':data.description_id.id,'client_id':patient,'status':data.status});
        }
        }

        $scope.counselling_registration=function () {
            $http.post('/api/counselling_registration',$scope.counseling_givens).then(function(data) {
                $scope.send = data.data;
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
                $scope.counseling_givens=[];
            });
        }

        $scope.removeIT=function (id) {
            $scope.counseling_givens.slice(id,1);
        }


        $scope.tt_vaccination_registration=function (tt_vaccine,selectedPatient) {

if(selectedPatient==undefined){
    swal(
        'Error',
        'Please choose Client First',
        'error'
    )
} else if(tt_vaccine.vaccination_date==undefined){
    swal(
        'Error',
        'Please Enter Date for '+tt_vaccine.vaccination_id.vaccination_name,
        'error'
    )
}else if(tt_vaccine.has_card==undefined){
    swal(
        'Error',
        'Please choose option if Client has card or not ',
        'error'
    )
}
            else{

    var tt_vaccination={'vaccination_id':tt_vaccine.vaccination_id.id,'has_card':tt_vaccine.has_card,
        'vaccination_date':tt_vaccine.vaccination_date,'patient_id':selectedPatient,'facility_id':facility_id,
        'user_id':user_id};
    //console.log(tt_vaccination);
    $http.post('/api/tt_vaccination_registration',tt_vaccination).then(function(data) {
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
        $scope.std_registration=function (std,selectedPatient) {

if(selectedPatient==undefined){
    swal(
        'Error',
        'Please choose Client First',
        'error'
    )
} else if(std.std_id==undefined){
    swal(
        'Error',
        'Please Select STDs Diagnosis First  ',
        'error'
    )
}
else if(std.std_id.id==undefined){
    swal(
        'Error',
        'Select STDs Diagnosis First',
        'error'
    )
}else if(std.result==undefined){
    swal(
        'Error',
        'Please choose option for Status about '+std.std_id.description,
        'error'
    )
}else if(std.treated==undefined){
    swal(
        'Error',
        'Please choose option for treatment about '+std.std_id.description+' selected',
        'error'
    )
}
            else{

    var stds={'std_id':std.std_id.id,
        'result':std.result,
        'treated':std.treated,
        'p_result':std.p_result,
        'p_treated':std.p_treated,
        'patient_id':selectedPatient,'facility_id':facility_id,
        'user_id':user_id};
    //console.log(stds);
    $http.post('/api/std_registration',stds).then(function(data) {
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

        $scope.institution_list=function () {

            $http.get('/api/institution_list').then(function (data) {

                $scope.institutes=data.data;
            });

        }
        $scope.institution_list();

$scope.vaccination_update=function (vaccine) {

    $http.post('/api/vaccination_update',vaccine).then(function(data) {
       $scope.vaccines = data.data;
        $scope.vaccination_list();
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
                    'sender_clinic_id':18,
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



        $scope.vaccination_update=function (vaccine) {

    $http.post('/api/vaccination_update',vaccine).then(function(data) {
       $scope.vaccines = data.data;
        $scope.vaccination_list();
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