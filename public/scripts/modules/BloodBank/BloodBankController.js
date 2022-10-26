(function() {

    'use strict';

    angular
        .module('authApp')
        .controller('BloodBankController',BloodBankController);

    function BloodBankController($http, $auth, $rootScope,$state,$location,$scope,$timeout,$mdDialog, Helper) {

        //loading menu
        var user_id=$rootScope.currentUser.id;
        var facility_id=$rootScope.currentUser.facility_id;
        $http.get('/api/getUsermenu/'+user_id ).then(function(data) {
            $scope.menu=data.data;
            ////console.log($scope.menu);

        });
        var resdata=[];
        var patientsList=[];
		
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
     /*   $scope.showSearchMarital = function (searchKey) {

            $http.get('/api/getMaritalStatus/' + searchKey).then(function (data) {
                resdata = data.data;

            });
            ////console.log(maritals);
            return resdata;
        }*/
		$http.get('/api/getMaritalStatus').then(function(data) {
                $scope.maritals = data.data;
            });

        $scope.getCountry = function (searchKey) {

            $http.get('/api/getCountry/' + searchKey).then(function (data) {
                resdata = data.data;

            });
            return resdata;
        }

        $scope.showSearchOccupation = function (searchKey) {

            $http.get('/api/getOccupation/' + searchKey).then(function (data) {
                resdata = data.data;

            });
            return resdata;
        }

        $scope.Blood_request_queue=function () {


            $http.get('/api/Blood_request_queue/' + facility_id).then(function (data) {
                $scope.blood_requests = data.data;

            });
        }
        $scope.Blood_request_queue();




        $scope.Issue_blood_request = function (item) {


            $mdDialog.show({
                controller: function ($scope) {
                    $scope.selectedPatient = item;

                    $scope.Blood_request_queue=function () {


                        $http.get('/api/Blood_request_queue/' + facility_id).then(function (data) {
                            $scope.blood_requests = data.data;

                        });
                    }



                    $scope.cancel = function () {
                        $scope.Blood_request_queue();
                        $mdDialog.hide();

                    };

                    $scope.Issue_blood_request = function (request) {
                        if (request.blood == undefined) {

                            swal(
                                'Error',
                                'Please Select Blood Group',
                                'error'
                            );
                            return;
                        }
                        if (request.unit == undefined) {

                            swal(
                                'Error',
                                'Please Fill  Required Units As per Request',
                                'error'
                            );
                            return;
                        }

                        swal({
                            title: 'Are you sure You Want Issue Blood Group  '+ request.blood+ ' To '+ request.medical_record_number+' ? ',

                            text: "This May no be easy to Reverse",
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

                            var dataa={facility_id:facility_id,patient_id:request.patient_id,user_id:user_id,id:request.id,unit_issued:request.unit,bag_no:request.bag_no,unit_requested:request.unit_requested,blood_group_requested:request.blood_group,blood_group:request.blood};
 if(request.blood_group !=undefined && request.blood_group !=request.blood){
     swal({
         title: ' Blood Group Requested Is '+ request.blood_group+ ' And Not '+' '+ request.blood +' Continue any way ? ',

         text: "This May no be easy to Reverse",
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

         $http.post('/api/Issue_blood_request',dataa).then(function (data) {

             var sending = data.data.msg;

             if (data.data.status == 0) {
                 swal(
                     'Error',
                     sending,
                     'error'
                 )
             }
             else {
                 swal(
                     'Success',
                     sending,
                     'success'
                 )
             }


         })


     }, function (dismiss) {
         // dismiss can be 'cancel', 'overlay',
         // 'close', and 'timer'
         if (dismiss === 'cancel') {

         }
     })
}
                            else{

     $http.post('/api/Issue_blood_request',dataa).then(function (data) {

         var sending = data.data.msg;

         if (data.data.status == 0) {
             swal(
                 'Error',
                 sending,
                 'error'
             )
         }
         else {
             swal(
                 'Success',
                 sending,
                 'success'
             )
         }


     })
 }



                        }, function (dismiss) {
                            // dismiss can be 'cancel', 'overlay',
                            // 'close', and 'timer'
                            if (dismiss === 'cancel') {

                            }
                        })

                    }


                },
                templateUrl: '/views/modules/BloodBank/BloodBank_requestModel.html',
                parent: angular.element(document.body),
                clickOutsideToClose: true,
                fullscreen: false,
            });
        }

  $scope.showSearchTribe = function (searchKey) {

            $http.get('/api/getTribe/' + searchKey).then(function (data) {
                resdata = data.data;

            });
            return resdata;
        }


        $scope.getRelationships = function (searchKey) {

            $http.get('/api/getRelationships/' + searchKey).then(function (data) {
                resdata = data.data;
            });
            return resdata;
        }
        $scope.showSearchResidences = function (searchKey) {

            $http.get('/api/searchResidences/' + searchKey).then(function (data) {
                resdata = data.data;
            });
            ////console.log(resdata);
            return resdata;
        }

$scope.blood_bank_screening=function (screen,patient) {
    if (patient == undefined) {

        swal(
            'Error',
            'Please Select Client',
            'error'
        );
return;
    }
    if (screen == undefined) {

        swal(
            'Error',
            'Please Fill All Required Fields',
            'error'
        );
return;
    }
    var blood_screen={assay_type:screen.assay_type,blood_group:screen.blood_group,rh:screen.rh,rpr:screen.rpr,hbsag:screen.hbsag,hcv:screen.hcv,hiv:screen.hiv,patient_id:patient.patient_id,facility_id:facility_id,user_id:user_id};
   console.log(blood_screen);

    $http.post('/api/blood_bank_screening',blood_screen).then(function (data) {
        $scope.screened = data.data;
        var msg = data.data.msg;
        var statuss = data.data.status;
        if (statuss == 0) {

            swal(
                'Error',
                msg,
                'error'
            );

        }
        else {
            swal(
                'Success',
                msg,
                'success'
            );
        }
    });
}

        $scope.blood_stocks_balance=function () {

    $http.get('/api/blood_stock_balance/' +facility_id).then(function (data) {
        $scope.blood_stocks = data.data;
        $scope.Total_unit_balance=Total_unit_balance($scope.blood_stocks)

        $scope.xs = [];
        $scope.ys = [];

        for(var i=0;i< $scope.blood_stocks.length; i++){
                $scope.xs.push($scope.blood_stocks[i].blood_group);
                $scope.ys.push($scope.blood_stocks[i].available_unit);
            }

            $scope.labels=$scope.xs ;
            $scope.data =  $scope.ys;
    });
}
        $scope.getBloodScreening=function (item) {
            var records={facility_id:facility_id,user_id:user_id,start_date:item.start_date,end_date:item.end_date}
    $http.post('/api/getBloodScreening',records).then(function (data) {
        $scope.screenings = data.data;

    });
}

        $scope.NumberOfBloodUnitCollected=function (item) {
            var records={facility_id:facility_id,user_id:user_id,start_date:item.start_date,end_date:item.end_date}
    $http.post('/api/NumberOfBloodUnitCollected',records).then(function (data) {
        $scope.bloods = data.data;

    });
}
        $scope.outreach='patient';
        $scope.blood_stocks_issued=function (item) {
            var records={facility_id:facility_id,user_id:user_id,start_date:item.start_date,end_date:item.end_date}
    $http.post('/api/blood_stock_issued',records).then(function (data) {
        $scope.blood_stock_issues = data.data;

        $scope.Total_unit_issued=Total_unit_issued($scope.blood_stock_issues)
        $scope.Total_unit_issued_out=Total_unit_issued_out($scope.blood_stock_issues)
        $scope.xs = [];
        $scope.ys = [];

        for(var i=0;i< $scope.blood_stock_issues[0].length; i++){
            $scope.xs.push($scope.blood_stock_issues[0][i].blood_group);
            $scope.ys.push($scope.blood_stock_issues[0][i].unit_issued);
        }

        $scope.labels1=$scope.xs ;
        $scope.data1 =  $scope.ys;
    });
}

        $scope.blood_arrays=[];
        $scope.Add_blood_stock = function (stock) {

            for (var i = 0; i < $scope.blood_arrays.length; i++) {


                if ($scope.blood_arrays[i].blood_group == stock.blood_group) {

                    return;
                }
            }
            if (stock == undefined) {
                swal('Error', 'Fill all Fields', 'error')
            }
            else if (stock.blood_group == undefined) {
                swal('Error', 'Please Choose Blood Group', 'error')
            }
            else if (stock.unit == undefined) {
                swal('Error', 'Please enter Number of Blood Unit', 'error')
            }
            else {
                $scope.blood_arrays.push({
                    blood_group: stock.blood_group,
                    unit: stock.unit,
                    facility_id: facility_id,
                    user_id: user_id,
                    control: 'l',
                    control_in: 'r'
                });
                $scope.Total_unit=Total_unit_cal($scope.blood_arrays)

            }
        }
        var Total_unit_cal = function () {
            var TotalUnit = 0;
            for (var i = 0; i < $scope.blood_arrays.length; i++) {
                TotalUnit -= -(($scope.blood_arrays[i].unit));
            }

            return TotalUnit;

        }
        var Total_unit_balance = function () {
            var TotalUnitbalance = 0;
            for (var i = 0; i < $scope.blood_stocks.length; i++) {
                TotalUnitbalance -= -(($scope.blood_stocks[i].available_unit));
            }

            return TotalUnitbalance;

        }
 var Total_unit_issued = function () {
            var TotalUnitissued = 0;
            for (var i = 0; i < $scope.blood_stock_issues[0].length; i++) {
                TotalUnitissued -= -(($scope.blood_stock_issues[0][i].unit_issued));
            }

            return TotalUnitissued;

        }
        var Total_unit_issued_out = function () {
            var TotalUnitissued = 0;
            for (var i = 0; i < $scope.blood_stock_issues[1].length; i++) {
                TotalUnitissued -= -(($scope.blood_stock_issues[1][i].unit_issued));
            }

            return TotalUnitissued;

        }

        $scope.removeBloodArray = function (x) {

            $scope.blood_arrays.splice(x, 1);


        }

        $scope.blood_stock=function () {
                $http.post('/api/blood_stock',$scope.blood_arrays).then(function (data) {
                    var msg = data.data.msg;
                    var statuss = data.data.status;
                    if (statuss == 0) {

                        swal(
                            'Error',
                            msg,
                            'error'
                        );

                    }
                    else {
                        swal(
                            'Success',
                            msg,
                            'success'
                        );
                        $scope.blood_stocks_balance();
                        $scope.blood_arrays=[];
                    }
                });
            }

  $scope.blood_stock_issuing=function (issue,selectedPatient) {


      if (issue == undefined) {
          swal('Error', 'Fill all Fields', 'error')
          return;
      }
      else if (issue.blood_group == undefined) {
          swal('Error', 'Please Choose Blood Group', 'error')
          return;
      }
      else if (issue.unit == undefined) {
          swal('Error', 'Please enter Number of Blood Unit', 'error')
          return;
      }
      if(issue.unit_issued_out !=undefined){

         var  unit_issued_out=issue.unit_issued_out;
         var  patient_id=null;
      }
      if(selectedPatient !=undefined){
          var  unit_issued_out=null;
          var  patient_id=selectedPatient.patient_id;
      }
      var issued_blood={blood_group:issue.blood_group,unit_issued:issue.unit,facility_id: facility_id,
          user_id: user_id,control: 'l',patient_id:patient_id,unit_issued_out:unit_issued_out,out:issue.outt};
                $http.post('/api/blood_stock_issuing',issued_blood).then(function (data) {
                    var msg = data.data.msg;
                    var statuss = data.data.status;
                    $scope.blood_stocks_balance();
                    if (statuss == 0) {

                        swal(
                            'Error',
                            msg,
                            'error'
                        );

                    }
                    else {
                        swal(
                            'Success',
                            msg,
                            'success'
                        );

                    }

                });
      $scope.issue={};
      $('#out').val('');
      $('#unit_issued_out').val('');
      $('#blood_group').val('');
      $('#unit').val('');
            }



         
        //age calculation
		$scope.patient = {};
		$scope.patient.dob = "";
        $scope.calculateAge = function(source) {
			
            var dob = $scope.patient.dob;

            if ($scope.patient.dob instanceof Date) {
                dob = $scope.patient.dob.toISOString();
            }
            if ($scope.patient.dob == undefined && $scope.patient.age == undefined) {
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
                $scope.patient.dob = new Date((new Date().getFullYear() - $scope.patient.age) + '-07-01');
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
                if ($scope.patient.age_unit == 'Years')
                    $scope.calculateAge('age');
                else if ($scope.patient.age_unit == 'Months') {
                    if (((new Date()).getMonth() + 1) >= ($scope.patient.age % 12))
                        $scope.patient.dob = ((new Date()).getFullYear() - ~~($scope.patient.age / 12)) + '-' + ((((new Date()).getMonth() + 1) - ($scope.patient.age % 12)).toString().length == 2 ? '' : '0') + (((new Date()).getMonth() + 1) - ($scope.patient.age % 12)) + '-01';
                    else
                        $scope.patient.dob = ((new Date()).getFullYear() - 1 - ~~($scope.patient.age / 12)) + '-' + (((12 + ((new Date()).getMonth() + 1)) - ($scope.patient.age % 12)).toString().length == 2 ? '' : '0') + ((12 + ((new Date()).getMonth() + 1)) - ($scope.patient.age % 12)) + '-01';
                } else {
                    if (((new Date()).getDate()) >= ($scope.patient.age % 30))
                        $scope.patient.dob = ((new Date()).getFullYear() - ~~($scope.patient.age / 365)) + '-' + ((((new Date()).getMonth() + 1) - ~~($scope.patient.age / 30)).toString().length == 2 ? '' : '0') + (((new Date()).getMonth() + 1) - ~~($scope.patient.age / 30)) + '-' + ($scope.patient.age.toString().length == 2 ? '' : '0') + $scope.patient.age.toString();
                    else
                        $scope.patient.dob = ((new Date()).getFullYear() - ~~($scope.patient.age / 365)) + '-' + ((((new Date()).getMonth()) - ~~($scope.patient.age / 30)).toString().length == 2 ? '' : '0') + (((new Date()).getMonth()) - ~~($scope.patient.age / 30)) + '-' + (((30 + ((new Date()).getDate())) - ($scope.patient.age % 30)).toString().length == 2 ? '' : '0') + ((30 + ((new Date()).getDate())) - ($scope.patient.age % 30));
                }
            }
        };
        // registration  ..............................................



        $scope.Register_donor=function (patient) {
 

            var first_name = patient.first_name;
            var middle_name = patient.middle_name;
            var last_name = patient.last_name;
            var gender = patient.gender;
            var dob = moment(patient.dob).format("YYYY-MM-DD");
            console.log(dob)
            // var dob='2017-09-09';
            var mobile_number = patient.mobile_number;


            if (angular.isDefined(first_name) == false) {
                return sweetAlert("Please Enter FIRST NAME before SAVING", "", "error");
            }

            else if (angular.isDefined(middle_name) == false) {
                return sweetAlert("Please Enter MIDDLE NAME before SAVING", "", "error");
            }

            else if (angular.isDefined(last_name) == false) {
                return sweetAlert("Please Enter LAST NAME before SAVING", "", "error");
            }
            else if (angular.isDefined($scope.residence) == false) {
                return sweetAlert("Please type the Residence Name and choose from the suggestions", "", "error");
            }

            else if (angular.isDefined(patient) == false) {
                return sweetAlert("Please Enter Other information", "", "error");
            } else if (angular.isDefined(patient.marital) == false) {
                return sweetAlert("Please Enter Marital Status and choose from the suggestions", "", "error");
            }

            else if (angular.isDefined(patient.occupation) == false) {
                return sweetAlert("Please Enter Occupations and choose from the suggestions", "", "error");
            }

            var patient_residences =$scope.residence.residence_id;
            var marital_status = patient.marital;
            var occupation = patient.occupation.id;
            var full_registration = {
                "first_name": first_name,
                "middle_name": middle_name,
                "last_name": last_name,
                "dob": dob,
                "gender": gender,
                "mobile_number": mobile_number,
                "residence_id": patient_residences,
                "facility_id": facility_id,
                "user_id": user_id,
                "marital_status": marital_status,
                "occupation_id": occupation,

            }
            var datataa;
            var patient_id;

            $http.post('/api/blood_bank_registration', full_registration).then(function (data) {

                $scope.blood_bank_registration = data.data;
                datataa = data.data[0];
                //console.log(data.data);
                if (data.data.status == 0) {

                    sweetAlert(data.data.data, "", "error");
                    return;
                } else {

                    $mdDialog.show({
                        controller: function ($scope) {
                            $scope.SelectedClient = datataa;
                            patient_id = data.data[0].id;
                            $scope.cancel = function () {
                                $mdDialog.hide();

                            };
                            $scope.Donor_type_info=function (donor_info) {
                                if (donor_info== undefined) {
                                    swal('Error', 'Please Fill All Fields Required', 'error')
                                    return;
                                }
                                if (patient_id== undefined) {
                                    swal('Error', 'Please Choose Client', 'error')
                                    return;
                                }
                                var infos={donor_condition:donor_info.donor_condition,donor_type:donor_info.donor_type,post_address:donor_info.post_address,user_id:user_id,facility_id:facility_id,phy_address:donor_info.phy_address,fax:donor_info.fax,donor_no:donor_info.donor_no,
                                    last_donation_date:donor_info.last_donation_date,last_donation_place:donor_info.last_donation_place,patient_id:patient_id}
                                console.log(infos)
                                $http.post('/api/Donor_type_info',infos).then(function (data) {
                                    var msg = data.data.msg;
                                    var statuss = data.data.status;
                                    if (statuss == 0) {

                                        swal(
                                            'Error',
                                            msg,
                                            'error'
                                        );

                                    }
                                    else {
                                        swal(
                                            'Success',
                                            msg,
                                            'success'
                                        );

                                    }
                                });
                            }


                            $scope.Donor_dodoso=function (dodoso) {
                                if (dodoso== undefined) {
                                    swal('Error', 'Fill all Fields', 'error')
                                    return;
                                }
                                console.log(dodoso,searchKey.patient_id)
                            }
                            $scope.Donor_vipimo=function (vipimo) {
                                if (vipimo== undefined) {
                                    swal('Error', 'Fill all Fields', 'error')
                                    return;
                                }
                                if (patient_id== undefined) {
                                    swal('Error', 'Please Choose Client', 'error')
                                    return;
                                }
                                var vipimoo={hb:vipimo.hb,weight:vipimo.weight,user_id:user_id,facility_id:facility_id,pr:vipimo.pr,postpone_reason:vipimo.postpone_reason,evaluation:vipimo.evaluation,polygamy:vipimo.polygamy,
                                    wives:vipimo.wives,patient_id:patient_id}
                                console.log(vipimoo)

                                $http.post('/api/Donor_vipimo',vipimoo).then(function (data) {
                                    var msg = data.data.msg;
                                    var statuss = data.data.status;
                                    if (statuss == 0) {

                                        swal(
                                            'Error',
                                            msg,
                                            'error'
                                        );

                                    }
                                    else {
                                        swal(
                                            'Success',
                                            msg,
                                            'success'
                                        );

                                    }
                                });
                            }
                            $scope.Donor_damu=function (damu) {
                                if (damu== undefined) {
                                    swal('Error', 'Fill all Fields', 'error')
                                    return;
                                }
                                if (patient_id== undefined) {
                                    swal('Error', 'Please Choose Client', 'error')
                                    return;
                                }
                                var damuu={user_id:user_id,syring_injected_time:damu.syring_injected_time,syring_removed_time:damu.syring_removed_time,facility_id:facility_id,
                                    vein_success:damu.vein_success,little_blood:damu.little_blood,donation_success:damu.donation_success,bad_event:damu.bad_event,
                                    patient_id:patient_id}
                                console.log(damuu)
                                $http.post('/api/Donor_damu',damuu).then(function (data) {
                                    var msg = data.data.msg;
                                    var statuss = data.data.status;
                                    if (statuss == 0) {

                                        swal(
                                            'Error',
                                            msg,
                                            'error'
                                        );

                                    }
                                    else {
                                        swal(
                                            'Success',
                                            msg,
                                            'success'
                                        );

                                    }
                                });
                            }
                        },
                        templateUrl: '/views/modules/BloodBank/Donor_type_info.html',
                        parent: angular.element(document.body),
                        clickOutsideToClose: true,
                        fullscreen: false,
                    });


                }




                });




}

        $scope.getPatients = function (searchKey) {
            console.log(searchKey);
            $http.post('/api/getSeachedPatients', {searchKey:searchKey}).then(function (data) {
                patientsList = data.data;

            });
            return patientsList;

        }

        $scope.openDialogForServices = function (searchKey) {

            $mdDialog.show({
                controller: function ($scope) {
                    $scope.SelectedClient = searchKey;
                    $scope.cancel = function () {
                        $mdDialog.hide();

                    };
                    $scope.Donor_type_info=function (donor_info,patient_id) {
                        if (donor_info== undefined) {
                            swal('Error', 'Please Fill All Fields Required', 'error')
                            return;
                        }
                        if (patient_id== undefined) {
                            swal('Error', 'Please Choose Client', 'error')
                            return;
                        }
                        var infos={donor_condition:donor_info.donor_condition,donor_type:donor_info.donor_type,post_address:donor_info.post_address,user_id:user_id,facility_id:facility_id,phy_address:donor_info.phy_address,fax:donor_info.fax,donor_no:donor_info.donor_no,
                            last_donation_date:donor_info.last_donation_date,last_donation_place:donor_info.last_donation_place,patient_id:patient_id}
                        console.log(infos)
                        $http.post('/api/Donor_type_info',infos).then(function (data) {
                            var msg = data.data.msg;
                            var statuss = data.data.status;
                            if (statuss == 0) {

                                swal(
                                    'Error',
                                    msg,
                                    'error'
                                );

                            }
                            else {
                                swal(
                                    'Success',
                                    msg,
                                    'success'
                                );

                            }
                        });
                    }


                    $scope.Donor_dodoso=function (dodoso) {
                        if (dodoso== undefined) {
                            swal('Error', 'Fill all Fields', 'error')
                            return;
                        }
                        console.log(dodoso,searchKey.patient_id)
                    }
                    $scope.Donor_vipimo=function (vipimo,patient_id) {
                        if (vipimo== undefined) {
                            swal('Error', 'Fill all Fields', 'error')
                            return;
                        }
                        if (patient_id== undefined) {
                            swal('Error', 'Please Choose Client', 'error')
                            return;
                        }
                        var vipimoo={hb:vipimo.hb,weight:vipimo.weight,user_id:user_id,facility_id:facility_id,pr:vipimo.pr,postpone_reason:vipimo.postpone_reason,evaluation:vipimo.evaluation,polygamy:vipimo.polygamy,
                            wives:vipimo.wives,patient_id:patient_id}
                        console.log(vipimoo)

                        $http.post('/api/Donor_vipimo',vipimoo).then(function (data) {
                            var msg = data.data.msg;
                            var statuss = data.data.status;
                            if (statuss == 0) {

                                swal(
                                    'Error',
                                    msg,
                                    'error'
                                );

                            }
                            else {
                                swal(
                                    'Success',
                                    msg,
                                    'success'
                                );

                            }
                        });
                    }
                    $scope.Donor_damu=function (damu,patient_id) {
                        if (damu== undefined) {
                            swal('Error', 'Fill all Fields', 'error')
                            return;
                        }
                        if (patient_id== undefined) {
                            swal('Error', 'Please Choose Client', 'error')
                            return;
                        }
                        var damuu={user_id:user_id,syring_injected_time:damu.syring_injected_time,syring_removed_time:damu.syring_removed_time,facility_id:facility_id,
                            vein_success:damu.vein_success,little_blood:damu.little_blood,donation_success:damu.donation_success,bad_event:damu.bad_event,
                            patient_id:patient_id}
                        console.log(damuu)
                        $http.post('/api/Donor_damu',damuu).then(function (data) {
                            var msg = data.data.msg;
                            var statuss = data.data.status;
                            if (statuss == 0) {

                                swal(
                                    'Error',
                                    msg,
                                    'error'
                                );

                            }
                            else {
                                swal(
                                    'Success',
                                    msg,
                                    'success'
                                );

                            }
                        });
                    }
                },
                templateUrl: '/views/modules/BloodBank/Donor_type_info.html',
                parent: angular.element(document.body),
                clickOutsideToClose: true,
                fullscreen: false,
            });

        }

    }

})();