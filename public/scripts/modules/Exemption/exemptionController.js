/**
 * Created by USER on 2017-02-18.
 */
/**
 * Created by USER on 2017-02-14.
 */

(function () {

    'use strict';

    angular
        .module('authApp').directive('ngFiles', ['$parse', function ($parse) {

            function fn_link(scope, element, attrs) {
                var onChange = $parse(attrs.ngFiles);
                element.on('change', function (event) {
                    onChange(scope, {$files: event.target.files});
                });
            };

            return {
                link: fn_link
            }
        }])
        .controller('exemptionController', exemptionController);

    function exemptionController($http, $auth, $rootScope, $state, $location, $scope, $uibModal, toastr, $mdDialog,Helper) {
        $scope.patient = {};

        var formdata = new FormData();

        $scope.getTheFiles = function ($files) {

            angular.forEach($files, function (value, key) {
                formdata.append(key, value);

            });
            console.log(formdata)
        };

        $scope.setTab = function (newTab) {
            $scope.tab = newTab;
        };
        $scope.isSet = function (tabNum) {
            return $scope.tab === tabNum;
        }


        $scope.oneAtATime = true;

        var user_id = $rootScope.currentUser.id;
        var facility_id = $rootScope.currentUser.facility_id;
        $http.get('/api/facility_list').then(function (data) {
            $scope.facilities = data.data;

        });
        $http.get('/api/getUsermenu/'+user_id).then(function(cardTitle){
            $scope.cardTitle=cardTitle.data[0];});
        $http.get('/api/getLoginUserDetails/' + user_id).then(function (data) {
            $scope.loginUserFacilityDetails = data.data;  });


        $scope.getPatientToEncounter = function (text) {
            return Helper.getPatientToEncounter(text)
                .then(function (response) {
                    $scope.patient=response.data;
                });
        };



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
                    if (datee.patient_id >0) {
                        $scope.gbv_vac_panel(datee);
                    }

                 
        };
		$scope.DisplayInfors1 = function (datee) {

                    $scope.selectedPatientInf=datee;


        };
        $scope.exemption_sub_dept_finance = function (item) {
            $http.post('/api/exemption_sub_dept_finance', {
                "start_date": item.start_date,
                "end_date": item.end_date,
                "facility_id": facility_id
            }).then(function (data) {
                $scope.subdepartments = data.data;
                var report_generated_on = new Date() + "";
                $scope.department_report_generated_on = report_generated_on.substring(0, 24);
                $scope.subDepGrandTotal = $scope.subdepTotal();
            });
        }

        $scope.subdepTotal = function () {
            var total = 0;
            for (var i = 0; i < $scope.subdepartments.length; i++) {
                total -= -($scope.subdepartments[i].total);
            }
            return total;
        }
        $scope.exemption_list = function (item) {
            var datee = {start_date: item.start_date, end_date: item.end_date, facility_id: facility_id};
            $http.post('/api/exemption_list', datee).then(function (data) {

                $scope.exemptions = data.data;
                //console.log(data.data)
                $scope.list_of_payment_category();
            });
        }
        $scope.list_of_payment_category = function () {
            $http.get('/api/payment_sub_category_list').then(function (data) {

                $scope.cat_categories = data.data;
            });

        }

        $scope.complain_report = function (item) {
            var datee = {start_date: item.start_date, end_date: item.end_date, facility_id: facility_id};
            $http.post('/api/complain_report', datee).then(function (data) {

                $scope.complains = data.data;
                $scope.total = totalcomplains($scope.complains);
                //console.log(data.data)

            });
        }
        var totalcomplains = function () {
            var total_total = 0;

            for (var i = 0; i < $scope.complains.length; i++) {
                total_total -= -$scope.complains[i].count;
            }

            return total_total;

        }
        var patientsList = [];

        $scope.sub_violences = [];
        $scope.getPatients = function (searchKey) {
            console.log(searchKey);
            $http.get('/api/getSeachedPatients/' + searchKey).then(function (data) {
                patientsList = data.data;

            });
            return patientsList;

        }
        $scope.ClinicQueue = function () {
            $http.get('/api/searchClinicpatientQueue/' + facility_id).then(function (data) {
                $scope.resdatas = data.data[8];

            });
        }
        $scope.ClinicQueue();

        $scope.Anti_natal_in_referral = function (selectedPatient) {

            $scope.selectedPatient == selectedPatient;


            $http.post('/api/Anti_incoming_referral', selectedPatient).then(function (data) {


            });
            $scope.ClinicQueue();
        }
        $http.get('/api/getRelationships').then(function(data) {
            $scope.relationships = data.data;
        });
        $http.get('/api/getMaritalStatus').then(function(data) {
            $scope.maritals = data.data;
        });
        $scope.exemption_registration_update_s = function (exempt, selectedPatient) {
                      

            var reason_for_revoke = "..";
            var patient = selectedPatient.patient_id;

            if (selectedPatient.patient_id == undefined) {
                swal(
                    'Feedback..',
                    'Please Select Client from a Search Box above...',
                    'error'
                )

            }
            else if (exempt == undefined) {
                swal(
                    'Feedback..',
                    'Please Fill all required fields ',
                    'error'
                )
            }
            else if (exempt.exemption_type_id == undefined) {
                swal(
                    'Feedback..',
                    'Please Select Exemption Category ',
                    'error'
                )
            }

            else if (exempt.exemption_reason == undefined) {
                swal(
                    'Feedback..',
                    'Please Fill  Reason(s) for This exemption ',
                    'error'
                )
            }


            else {


                var status_id = 2;
                var change = true;
				 
                            var price = exempt;
                            
                          /*  var item_id = exempt.service.service_id;

                            var item_price_id = exempt.service.price_id;
                            var item_type_id = exempt.service.item_type_id;*/
                            var patient = selectedPatient.patient_id;
                            var exemption_type_id = exempt.exemption_type_id.id;
                            var main_category_id = exempt.exemption_type_id.pay_cat_id;
                            var user_id = $rootScope.currentUser.id;
                            var facility_id = $rootScope.currentUser.facility_id;
                            var patient_id = selectedPatient.patient_id;
                            var bill_id = exempt.exemption_type_id.id;
                            var status_id = status_id;
                            var exemption_reason = exempt.exemption_reason;
                            var reason_for_revoke = reason_for_revoke;
                            var description = exempt.description;
                            formdata.append('change', change);
                            formdata.append('price', price);
                           // formdata.append('item_id', item_id);
                            //formdata.append('item_price_id', item_price_id);
                           // formdata.append('item_type_id', item_type_id);
                            formdata.append('payment_filter', exemption_type_id);
                            formdata.append('quantity', 1);
                            formdata.append('main_category_id', main_category_id);
                            formdata.append('bill_id', bill_id);
                            formdata.append('exemption_type_id', exemption_type_id);
                            formdata.append('exemption_reason', exemption_reason);
                            formdata.append('user_id', user_id);
                            formdata.append('facility_id', facility_id);
                            formdata.append('patient_id', patient_id);
                            formdata.append('reason_for_revoke', reason_for_revoke);
                            formdata.append('status_id', status_id);
                           // formdata.append('dept_id', dept_id);
                var request = {
                    method: 'POST',
                    url: '/api/' + 'patient_exemption',
                    data: formdata,
                    headers: {
                        'Content-Type': undefined
                    }

                };

                // SEND THE FILES.
                $http(request).then(function (data) {

                        var msg = data.data.msg;
                        $scope.ok = data.data.status;
                        //console.log(data.data.status);
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
                    })
                    .then(function () {
                    });


            }
        }
        $scope.openDialogForServices = function (selectedPatient) {
            if(selectedPatient.patient_id != undefined){
            $scope.quick_registration = selectedPatient;


            $mdDialog.show({
                controller: function ($scope) {
                    $scope.quick_registration = selectedPatient;
                    $http.get('/api/getexemption_services/' + facility_id).then(function (data) {
                        $scope.exemption_services = data.data;
                        $http.get('/api/exemption_type_list/' + user_id).then(function (data) {
                            $scope.exemption_types = data.data;

                        });
						
                    });
 
									$http.get('/api/patients_address_info/' + $scope.quick_registration.patient_id).then(function (data) {
                            $scope.patients_address = data.data[0];

                        });
                    $http.get('/api/getSpecialClinics').then(function (data) {
                        $scope.departments = data.data;

                    });
						$http.get('/api/getUsermenu/'+user_id).then(function(cardTitle){
                                $scope.cardTitle=cardTitle.data[0];

                            });
                    $scope.cancel = function () {
                        $mdDialog.hide();

                    };
                    $scope.exemption_registration = function (exempt, selectedPatient) {
						//console.log(exempt.service)
                        var dept_id=1;
                        if (exempt.referral_id != undefined) {
                            dept_id = exempt.referral_id;
                        }
                        var reason_for_revoke = "..";
                        var patient = selectedPatient.patient_id;

                        if (selectedPatient.patient_id == undefined) {
                            swal(
                                'Feedback..',
                                'Please Select Client from a Search Box above...',
                                'error'
                            )

                        }
                        else if (exempt == undefined) {
                            swal(
                                'Feedback..',
                                'Please Fill all required fields ',
                                'error'
                            )
                        }
                        else if (exempt.exemption_type_id == undefined) {
                            swal(
                                'Feedback..',
                                'Please Select Exemption Category ',
                                'error'
                            )
                        }

                        else if (exempt.exemption_reason == undefined) {
                            swal(
                                'Feedback..',
                                'Please Fill  Reason(s) for This exemption ',
                                'error'
                            )
                        } else if (exempt.service == undefined) {
                            swal(
                                'Feedback..',
                                'Please Choose service ',
                                'error'
                            )
                        }


                        else {


                            var status_id = 2;
                            var change = false;

                            var price = exempt;
                            var item_id = exempt.service.service_id;
                            var item_price_id = exempt.service.price_id;
                            var item_type_id = exempt.service.item_type_id;
                            var patient = selectedPatient.patient_id;
                            var exemption_type_id = exempt.exemption_type_id.id;
                            var main_category_id = exempt.exemption_type_id.pay_cat_id;
                            var user_id = $rootScope.currentUser.id;
                            var facility_id = $rootScope.currentUser.facility_id;
                            var patient_id = selectedPatient.patient_id;
                            var bill_id = exempt.exemption_type_id.id;
                            var status_id = status_id;
                            var exemption_reason = exempt.exemption_reason;
                            var reason_for_revoke = reason_for_revoke;
                            var description = exempt.description;
                            formdata.append('change', change);
                            formdata.append('price', price);
                            formdata.append('item_id', item_id);
                            formdata.append('item_price_id', item_price_id);
                            formdata.append('item_type_id', item_type_id);
                            formdata.append('payment_filter', exemption_type_id);
                            formdata.append('quantity', 1);
                            formdata.append('main_category_id', main_category_id);
                            formdata.append('bill_id', bill_id);
                            formdata.append('exemption_type_id', exemption_type_id);
                            formdata.append('exemption_reason', exemption_reason);
                            formdata.append('user_id', user_id);
                            formdata.append('facility_id', facility_id);
                            formdata.append('patient_id', patient_id);
                            formdata.append('reason_for_revoke', reason_for_revoke);
                            formdata.append('status_id', status_id);
                            formdata.append('dept_id', dept_id);
                            var request = {
                                method: 'POST',
                                url: '/api/' + 'patient_exemption',
                                data: formdata,
                                headers: {
                                    'Content-Type': undefined
                                }

                            };

                            // SEND THE FILES.
                            $http(request).then(function (data) {

                                    var msg = data.data.msg;

                                    $scope.ok = data.data.status;
                                    //console.log(data.data.status);
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
                                })
                                .then(function () {
                                });


                        }
                    }
					 $scope.PrintContentR=function () {
                                //location.reload();
                                var DocumentContainer = document.getElementById('divtoprintCArd');
                                var WindowObject = window.open("", "PrintWindow",
                                    "width=900,height=700,top=50,left=450,toolbars=no,scrollbars=no,status=no,resizable=yes");
                                WindowObject.document.title = "PRINT PATIENT CARD: GoT-HOMIS";
                                WindowObject.document.writeln(DocumentContainer.innerHTML);
                                WindowObject.document.close();

                                setTimeout(function () {
                                    WindowObject.focus();
                                    WindowObject.print();
                                    WindowObject.close();
                                });

                            }
                },
                templateUrl: '/views/modules/Exemption/exemptionModel.html',
                parent: angular.element(document.body),
                clickOutsideToClose: true,
                fullscreen: false,
            });
        }
        }

        $scope.violence_sub_array = function (item) {

            for (var i = 0; i < $scope.sub_violences.length; i++) {


                if ($scope.sub_violences[i].sub_violence == item.sub_violence) {
                    swal(item.sub_violence + " already in your list  ", "", "info");
                    return;
                }
            }
            $scope.sub_violences.push({
                violence_category_id: item.violence_category_id.id,
                violence: item.violence_category_id.violence_type_category,
                sub_violence: item.sub_violence,
            });

        }
        $scope.getViolence_ids = [];
        $scope.violence_services = [];
        $scope.violence_outputs = [];
        $scope.getservices = [];
        $scope.getoutputs = [];
        $scope.getViolence_id = function (item, violence, patient_id) {

            if (violence.violence_type_id == undefined) {
                swal('fill Violence Type', '', 'info');
                return;
            }
            if (item.id == undefined) {
                swal('fill Violence category', '', 'info');
                return;
            }
            if (violence.event_date == undefined) {
                swal('fill date of Event', '', 'info');
                return;
            }
            for (var i = 0; i < $scope.getViolence_ids.length; i++) {
                if ($scope.getViolence_ids[i].sub_violences == item.id) {

                    return;
                }
            }
            $scope.getViolence_ids.push({
                event_date: violence.event_date,
                sub_violences: item.id,
                patient_id: patient_id,
                user_id: user_id,
                facility_id: facility_id,
                sub_violence: item.sub_violence,
                sub_violence_id: item.id,
                violence_type_id: violence.violence_type_id.id,
                violence_type_name: violence.violence_type_id.violence_type_name,
                violence_category_id: violence.violence_category_id.id,
                violence_type_category: violence.violence_category_id.violence_type_category,

            });

        }

        $scope.services_given_array = function (item, patient_id) {


            if (item == undefined) {
                swal('fill Service Given', '', 'info');
                return;
            }
            for (var i = 0; i < $scope.getservices.length; i++) {
                if ($scope.getservices[i].service_id == item.id) {

                    return;
                }
            }
            $scope.getservices.push({
                patient_id: patient_id,
                user_id: user_id,
                service_id: item.id,
                facility_id: facility_id,
                service_name: item.service_name,
                other_service: item.other_service,

            });

        }

        $scope.outputs_given_array = function (item, patient_id) {


            if (item == undefined) {
                swal('fill Output', '', 'info');
                return;
            }
            for (var i = 0; i < $scope.getoutputs.length; i++) {
                if ($scope.getoutputs[i].output_id == item.id) {

                    return;
                }
            }
            $scope.getoutputs.push({
                patient_id: patient_id,
                user_id: user_id,
                output_id: item.id,
                facility_id: facility_id,
                output: item.output,
            });

        }


        $scope.violence_service_array = function (item) {
            for (var i = 0; i < $scope.violence_services.length; i++) {
                if ($scope.violence_services[i].service_name == item.service_name) {
                    return;
                }
            }
            $scope.violence_services.push({
                service_name: item.service_name,

            });

        }

        $scope.violence_output_array = function (item) {
            for (var i = 0; i < $scope.violence_outputs.length; i++) {
                if ($scope.violence_outputs[i].output == item.output) {
                    return;
                }
            }
            $scope.violence_outputs.push({
                output: item.output,

            });

        }
        $scope.violence_sub_registration = function (item) {

            $http.post('/api/violence_sub_registration', $scope.sub_violences).then(function (data) {
                var sending = data.data.msg;
                var statusee = data.data.status;
                $scope.sub_violences = [];
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

        $scope.violence_client_service_registration = function (item) {

            $http.post('/api/violence_client_service_registration', $scope.getservices).then(function (data) {
                var sending = data.data.msg;
                var statusee = data.data.status;
                $scope.getservices = [];
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
        $scope.social_issue_register = function (item) {

            $http.post('/api/social_issue_register', item).then(function (data) {
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
                    $scope.social_issue_list();
                }


            });


        }

        //marriage register issues
$scope.marriage_issues_register = function (item,patient) {

if(item==undefined){
    swal('oops!','Please Enter All required fields','error')
    return
}
    if(item.complainer_description==undefined){
    swal('oops!','Please Enter Fill Complainer Description','error')
    return
}
    if(item.social_description==undefined){
    swal('oops!','Please Enter Fill Social worker Description','error')
    return
}
    if(item.event_date==undefined){
    swal('oops!','Please Enter Fill Date of Event Occured','error')
    return
}
    var issues={status:0,user_id:user_id,facility_id:facility_id,patient_id:patient,event_date:item.event_date,complainer_description:item.complainer_description,social_description:item.social_description};
            $http.post('/api/marriage_issues_register', issues).then(function (data) {
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
                   $('#complainer_description').val('');
                   $('#social_description').val('');
                }


            });


        }
        $scope.marriage_issues_list = function (item) {
            var records = {
                facility_id: facility_id,
                user_id: user_id,
                start_date: item.start_date,
                end_date: item.end_date
            }
            $http.post('/api/marriage_issues_list', records).then(function (data) {
                $scope.marriages = data.data[0];
                $scope.stauses = data.data[1];


            });
        }

        $scope.ward_round_register = function (round, patient) {
            if (patient == undefined) {
                swal(
                    'Error',
                    'Choose Client first From search bar above',
                    'error'
                )
                return;
            }
            else if (round == undefined) {
                swal(
                    'Error',
                    'Fill Issue Observed..',
                    'error'
                )
                return;
            }
            else if (round.issue_id == undefined) {
                swal(
                    'Error',
                    'Fill Issue Observed..',
                    'error'
                )
                return;
            } else if (round.plan == undefined) {
                swal(
                    'Error',
                    'Fill Your Plan..',
                    'error'
                )
                return;
            }
            var social_round = {
                patient_id: patient, user_id: user_id, facility_id: facility_id,
                issue_id: round.issue_id, plan: round.plan, output: round.output, remarks: round.remarks
            }
            $http.post('/api/ward_round_register', social_round).then(function (data) {
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

        $scope.client_complains_register = function (complain, patient) {
            if (patient == undefined) {
                swal(
                    'Error',
                    'Choose Client first From search bar above',
                    'error'
                )
                return;
            }
            else if (complain == undefined) {
                swal(
                    'Error',
                    'Fill all required fields..',
                    'error'
                )
                return;
            }
            else if (complain.complain_area_id == undefined) {
                swal(
                    'Error',
                    'Choose Area of Complain raised..',
                    'error'
                )
                return;
            } else if (complain.complain == undefined) {
                swal(
                    'Error',
                    'Fill Client Complain..',
                    'error'
                )
                return;
            }
            var complains = {
                patient_id: patient,
                user_id: user_id,
                facility_id: facility_id,
                complain: complain.complain,
                complain_area_id: complain.complain_area_id,
                immediate_measure: complain.immediate_measure,
                solution: complain.solution,
                remarks: complain.remarks,
            }
            $http.post('/api/client_complains_register', complains).then(function (data) {
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

        $scope.social_issue_list = function (item) {

            $http.get('/api/social_issue_list').then(function (data) {
                $scope.issues_lists = data.data;
            });

        }
        $scope.social_issue_list();
        $scope.get_violence_sub_category = function (item) {

            $http.get('/api/get_violence_sub_category/' + item).then(function (data) {
                $scope.sub_violence_list = data.data;
            });


        }


        $http.get('/api/get_violence_service_registration').then(function (data) {
            $scope.services = data.data;
        });

        $http.get('/api/get_violence_output_registration').then(function (data) {
            $scope.outputs = data.data;
        });




        $scope.violence_client_registration = function () {

            $http.post('/api/violence_client_registration', $scope.getViolence_ids).then(function (data) {
                $scope.service = data.data;
                $scope.getViolence_ids = [];
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
                        'Successful',
                        msg,
                        'success'
                    );
                }

            });


        }

        $scope.violence_client_output_registration = function () {

            $http.post('/api/violence_client_output_registration', $scope.getoutputs).then(function (data) {
                $scope.outputss = data.data;
                $scope.getoutputs = [];
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
                        'Successful',
                        msg,
                        'success'
                    );
                }

            });


        }
        $scope.violence_client_informant_registration = function (item, patient_id) {

            var informants = {
                patient_id: patient_id,
                user_id: user_id,
                relationship: item.relationship,
                description: item.description,
                facility_id: facility_id
            }
            $http.post('/api/violence_client_informant_registration', informants).then(function (data) {
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
                        'Successful',
                        msg,
                        'success'
                    );
                }

            });


        }
        $scope.violence_service_registration = function () {

            $http.post('/api/violence_service_registration', $scope.violence_services).then(function (data) {
                $scope.service = data.data;
                $scope.violence_services = [];
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
                        'Successful',
                        msg,
                        'success'
                    );
                }

            });


        }

        $scope.violence_output_registration = function () {

            $http.post('/api/violence_output_registration', $scope.violence_outputs).then(function (data) {
                $scope.service = data.data;
                $scope.violence_outputs = [];
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
                        'Successful',
                        msg,
                        'success'
                    );
                }

            });


        }

        $scope.removeItemArray = function (x) {

            $scope.sub_violences.splice(x, 1);


        }
        $scope.removeItemArray_service = function (x) {

            $scope.violence_services.splice(x, 1);


        }
        $scope.removeItemArray_output = function (x) {

            $scope.violence_outputs.splice(x, 1);


        }
        $scope.removeItemArray_violence = function (x) {

            $scope.getViolence_ids.splice(x, 1);


        }
        $scope.removeItemArray_service = function (x) {

            $scope.getservices.splice(x, 1);


        }
        $scope.removeItemArray_output = function (x) {

            $scope.getoutputs.splice(x, 1);


        }



        $scope.GetDebts_list_summary = function (item) {
            var records={facility_id:facility_id,user_id:user_id,start_date:item.start_date,end_date:item.end_date}
            $http.post('/api/GetDebts_list_summary',records).then(function (data) {
                $scope.temporary_exemptions=data.data;
                $scope.debt_lists=data.data;
                $scope.grandTotal_sum=calcgrandSummary($scope.temporary_exemptions);
                $scope.Total_sum=calcTotal($scope.temporary_exemptions);
                $scope.discount_sum=calcDiscount($scope.temporary_exemptions);
            });
        }
        //$scope.exemption_list();

        $scope.temporary_exemption_list = function () {

            $http.get('/api/temporary_exemption_list/' + facility_id).then(function (data) {

                if (data.data.length < 1) {

                    $scope.tempo_exemptions = data.data;
                }
                else {
                    $scope.tempo_exemptions = data.data;
                    //console.log(data.data)

                }
            });
        }



        $scope.temporary_exemption_view = function (exemption) {

  $mdDialog.show({
                controller: function ($scope) {
                    $scope.selectedPatient=exemption;
                    $scope.checkedList=[];
                    var facility_id=exemption.facility_id;
                    $http.get('/api/temporary_exemption_clients/' + exemption.patient_id).then(function (data) {
                        $scope.temporary_exemptions=data.data;
                        $scope.grandTotal=calcgrandTotal($scope.temporary_exemptions);
                        $scope.Total=calcTotal($scope.temporary_exemptions);
                        $scope.discount=calcDiscount($scope.temporary_exemptions);
                    });

                    $scope.temporary_exemption_checked=function (item) {

                        for(var i=0;i<$scope.checkedList.length;i++){
                            if($scope.checkedList[i].id==item.id){

                                return
                            }
                            }


                                $scope.checkedList.push({user_id:user_id,id:item.id,patient_id:item.patient_id,item:item,item_name:item.item_name,discount:item.discount,quantity:item.quantity,price:item.price});
                        $scope.grandTotalPay=calcgrandTotalPay($scope.checkedList);
                    }

                    $scope.temporary_exemption_removed=function (x) {
                        $scope.checkedList.splice(x,1);
                        $scope.grandTotalPay=calcgrandTotalPay($scope.checkedList);
                    }
                    $scope.cancel = function () {
                        $mdDialog.hide();

                    };

                    var calcgrandTotal = function () {
                        var TotalExemp = 0;
                        for (var i = 0; i < $scope.temporary_exemptions.length; i++) {
                            TotalExemp -= -(($scope.temporary_exemptions[i].quantity*$scope.temporary_exemptions[i].price)-$scope.temporary_exemptions[i].discount);
                        }

                        return TotalExemp;

                    }
                    var calcgrandTotalPay = function () {
                        var TotalExemp = 0;
                        for (var i = 0; i <  $scope.checkedList.length; i++) {
                            TotalExemp -= -(( $scope.checkedList[i].quantity* $scope.checkedList[i].price)- $scope.checkedList[i].discount);
                        }

                        return TotalExemp;

                    }
                    var calcTotal = function () {
                        var Total = 0;
                        for (var i = 0; i < $scope.temporary_exemptions.length; i++) {
                            Total -= -(($scope.temporary_exemptions[i].price * $scope.temporary_exemptions[i].quantity));
                        }

                        return Total;

                    }

                    var calcDiscount = function () {
                        var TotalDiscount = 0;
                        for (var i = 0; i < $scope.temporary_exemptions.length; i++) {
                            TotalDiscount -= -($scope.temporary_exemptions[i].discount);
                        }

                        return TotalDiscount;

                    }


                    $scope.temp_exe_invoice=function () {
                        //location.reload();
                        var DocumentContainer = document.getElementById('invoice_id');
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

                    $scope.temporary_exemption_status_update = function (Data) {


                        swal({
                            title: 'ARE YOU SURE YOU WANT To CHANGE TRANSACTIONS RECORD Of ' + Data.medical_record_number+' '+'INTO  NORMAL PAYMENT ?',

                            text: $scope.checkedList.length+ " record(s) will be updated",
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

                            $http.post('/api/temporary_exemption_status_update',$scope.checkedList).then(function (data) {


                                var sending = data.data.msg;
                                swal(
                                    'Feedback..',
                                    sending,
                                    'success'
                                )
                                $scope.temporary_exemption_list();
                            });


                        }, function (dismiss) {
                            // dismiss can be 'cancel', 'overlay',
                            // 'close', and 'timer'
                            if (dismiss === 'cancel') {

                            }
                        })


                    }
                    $scope.temporary_exemption_status_single_row_update = function (patient,exemption) {
                        $scope.checkedList_single=[];

                        $scope.checkedList_single.push({id:exemption.id,patient_id:exemption.patient_id})
                        swal({
                            title: 'ARE YOU SURE YOU WANT To CHANGE TRANSACTION RECORD OF' + patient.medical_record_number+' '+' INTO  NORMAL PAYMENT ?',

                            text: $scope.checkedList_single.length+ " record(s) will be updated",
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

                            $http.post('/api/temporary_exemption_status_update',$scope.checkedList_single).then(function (data) {


                                var sending = data.data.msg;
                                swal(
                                    'Feedback..',
                                    sending,
                                    'success'
                                )
                                $scope.temporary_exemption_list();
                            });


                        }, function (dismiss) {
                            // dismiss can be 'cancel', 'overlay',
                            // 'close', and 'timer'
                            if (dismiss === 'cancel') {

                            }
                        })


                    }

                    $scope.temporary_exemption_list = function () {

                        $http.get('/api/temporary_exemption_list/' + facility_id).then(function (data) {

                            if (data.data.length < 1) {

                                $scope.tempo_exemptions = data.data;
                            }
                            else {
                                $scope.tempo_exemptions = data.data;
                                //console.log(data.data)

                            }
                        });
                    }

                    $scope.temporary_exemption_list();



                },
                templateUrl: '/views/modules/Exemption/Temporary_exemptionModel.html',
                parent: angular.element(document.body),
                clickOutsideToClose: true,
                fullscreen: false,
            });


        }

        //age calculation
        $scope.exemption_calculateAge = function (patient, source) {

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
                    $scope.exemption_calculateAge('age');
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
        // registration  ..............................................
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

            }
        }

        $scope.fullRegistration = function (patient, others) {
            console.log(residence_id)
            console.log(patient, others,residence_id)
            var first_name = patient.first_name;
            var middle_name = patient.middle_name;
            var last_name = patient.last_name;
            var gender = patient.gender;
            var dob = moment(patient.dob).format("YYYY-MM-DD");

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
            else if (angular.isDefined(residence_id) == false) {
                return sweetAlert("Please type the Residence Name and choose from the suggestions", "", "error");
            }

            else if (angular.isDefined(others) == false) {
                return sweetAlert("Please Enter Other information", "", "error");
            } else if (angular.isDefined(others.marital) == false) {
                return sweetAlert("Please Enter Marital Status and choose from the suggestions", "", "error");
            }

            else if (angular.isDefined(others.occupation) == false) {
                return sweetAlert("Please Enter Occupations and choose from the suggestions", "", "error");
            }

            else if (angular.isDefined(others.tribe) == false) {
                return sweetAlert("Please Enter Tribe and choose from the suggestions", "", "error");
            }
            else if (angular.isDefined(others.country) == false) {
                return sweetAlert("Please Enter Country and choose from the suggestions", "", "error");
            }

            else if (angular.isDefined(others.relationship) == false) {
                return sweetAlert("Please Enter Relationships and choose from the suggestions", "", "error");
            }

            // var patient_residences = patient.resedence_id.residence_id;
            var patient_residences = residence_id;
            var marital_status = others.marital;
            var occupation = others.occupation.id;
            var tribe = others.tribe.id;
            var country = others.country.id;
            var next_of_kin_name = others.next_of_kin_name;
            var next_of_kin_resedence_id =residence_id;
            var relationship = others.relationship;
            var mobile_number_next_kin = others.mobile_number_next_kin;
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
                "tribe": tribe,
                "country_id": country,
                "next_of_kin_name": next_of_kin_name,
                "next_of_kin_resedence_id": residence_id,
                "relationship": relationship,
                "mobile_number_next_kin": '0711111111'
            }


            $http.post('/api/full_registration', full_registration).then(function (data) {
                $scope.quick_registration1 = data.data;

                if (data.data.status == 0) {

                    sweetAlert(data.data.data, "", "error");
                } else {

                    var selectedPatient = data.data[0][0];
                    var selectedPatientResidence = data.data[2][0];

                    $mdDialog.show({
                        controller: function ($scope) {

                            $scope.quick_registration1 = data.data[0][0];

                            $scope.residence = selectedPatientResidence;
                            $scope.nationality = others.country.country_name;
                            $scope.occupation = others.occupation.occupation_name;
                            $scope.cancel = function () {
                                $mdDialog.hide();

                            };

                            $http.get('/api/getUsermenu/'+user_id).then(function(cardTitle){
                                $scope.cardTitle=cardTitle.data[0];

                            });
                            $http.get('/api/getSpecialClinics').then(function (data) {
                                $scope.departments = data.data;

                            });
                            $http.get('/api/getexemption_services/' + facility_id).then(function (data) {
                                $scope.exemption_services = data.data;

                                $http.get('/api/exemption_type_list/' + user_id).then(function (data) {
                                    $scope.exemption_types = data.data;

                                });
                            });

                            $scope.PrintContent=function () {
                                //location.reload();
                                var DocumentContainer = document.getElementById('divtoprintCArd');
                                var WindowObject = window.open("", "PrintWindow",
                                    "width=900,height=700,top=50,left=450,toolbars=no,scrollbars=no,status=no,resizable=yes");
                                WindowObject.document.title = "PRINT PATIENT CARD: GoT-HOMIS";
                                WindowObject.document.writeln(DocumentContainer.innerHTML);
                                WindowObject.document.close();

                                setTimeout(function () {
                                    WindowObject.focus();
                                    WindowObject.print();
                                    WindowObject.close();
                                });

                            }
                            $scope.exemption_registration = function (exempt, selectedPatient) {
                                var dept_id=1;
                                if (exempt.referral_id != undefined) {
                                    dept_id = exempt.referral_id;
                                }
                                var reason_for_revoke = "..";
                                if (selectedPatient.id == undefined) {
                                    var patient = selectedPatient.patient_id;
                                }
                                else {
                                    var patient = selectedPatient.id;
                                }


                                if (selectedPatient.patient_id == undefined && selectedPatient.id == undefined) {
                                    swal(
                                        'Feedback..',
                                        'Please Select Client from a Search Box above...',
                                        'error'
                                    )

                                }
                                else if (exempt == undefined) {
                                    swal(
                                        'Feedback..',
                                        'Please Fill all required fields ',
                                        'error'
                                    )
                                }
                                else if (exempt.exemption_type_id == undefined) {
                                    swal(
                                        'Feedback..',
                                        'Please Select Exemption Category ',
                                        'error'
                                    )
                                }

                                else if (exempt.exemption_reason == undefined) {
                                    swal(
                                        'Feedback..',
                                        'Please Fill  Reason(s) for This exemption ',
                                        'error'
                                    )
                                }


                                else {


                                    var status_id = 2;
                                    var change = false;
                                    var price = exempt;
                                    var item_id = exempt.service.service_id;
                                    var item_price_id = exempt.service.price_id;
                                    var item_type_id = exempt.service.item_type_id;
                                    var patient = patient;
                                    var exemption_type_id = exempt.exemption_type_id.id;
                                    var main_category_id = exempt.exemption_type_id.pay_cat_id;
                                    var user_id = $rootScope.currentUser.id;
                                    var facility_id = $rootScope.currentUser.facility_id;
                                    var patient_id = patient;
                                    var bill_id = exempt.exemption_type_id.id;
                                    var status_id = status_id;
                                    var exemption_reason = exempt.exemption_reason;
                                    var reason_for_revoke = reason_for_revoke;
                                    var description = exempt.description;
                                    formdata.append('change', change);
                                    formdata.append('price', price);
                                    formdata.append('item_id', item_id);
                                    formdata.append('item_price_id', item_price_id);
                                    formdata.append('item_type_id', item_type_id);
                                    formdata.append('payment_filter', exemption_type_id);
                                    formdata.append('quantity', 1);
                                    formdata.append('main_category_id', main_category_id);
                                    formdata.append('bill_id', bill_id);
                                    formdata.append('exemption_type_id', exemption_type_id);
                                    formdata.append('exemption_reason', exemption_reason);
                                    formdata.append('user_id', user_id);
                                    formdata.append('facility_id', facility_id);
                                    formdata.append('patient_id', patient_id);
                                    formdata.append('reason_for_revoke', reason_for_revoke);
                                    formdata.append('status_id', status_id);
                                    formdata.append('dept_id', dept_id);
                                    var request = {
                                        method: 'POST',
                                        url: '/api/' + 'patient_exemption',
                                        data: formdata,
                                        headers: {
                                            'Content-Type': undefined
                                        }

                                    };

                                    // SEND THE FILES.
                                    $http(request).then(function (data) {

                                            var msg = data.data.msg;
                                            $scope.ok = data.data.status;

                                            var statuss = data.data.status;
                                            if (statuss == 0) {

                                                swal(
                                                    'Error',
                                                    msg,
                                                    'error'
                                                );

                                            }

                                        })
                                        .then(function () {
                                        });


                                }
                            }


                        },
                        templateUrl: '/views/modules/Exemption/exemptionModel1.html',
                        parent: angular.element(document.body),
                        clickOutsideToClose: true,
                        fullscreen: false,
                    });
                }
            });


        }



        $scope.temporary_exemption_list();
        $scope.exemption_filter = function (item) {
            var datee = {start_date: item.start_date, end_date: item.end_date, facility_id: facility_id};

            $http.post('/api/exemption_list_by_gender', datee).then(function (data) {

                if (data.data.length < 1) {
                    swal(
                        'Feedback..',
                        'No data set available..',
                        'info'
                    )
                    $scope.exemption_by_genders = data.data;
                    $scope.totalex = null;
                }
                else {
                    $scope.exemption_by_genders = data.data;
                    $scope.totalex = calcTotalExemption($scope.exemption_by_genders);
                    //console.log(data.data)

                    $scope.xs = [];
                    $scope.ys = [];
                    $scope.z = [];

                    for (var i = 0; i < $scope.exemption_by_genders.length; i++) {
                        $scope.xs.push($scope.exemption_by_genders[i].gender);
                        $scope.series = $scope.z.push($scope.exemption_by_genders[i].exemption_name);

                        $scope.ys.push($scope.exemption_by_genders[i].total);
                    }

                    $scope.labels = $scope.xs;
                    $scope.data = $scope.ys;
                }

            });

        }
        $scope.exemption_filter_by_employee = function (item) {
            var datee = {start_date: item.start_date, end_date: item.end_date, facility_id: facility_id};

            $http.post('/api/exemption_filter_by_employee', datee).then(function (data) {

                if (data.data.length < 1) {
                    swal(
                        'Feedback..',
                        'No data set available..',
                        'info'
                    )
                    $scope.exemption_by_employees = data.data;
                    $scope.total_employee = null;
                }
                else {
                    $scope.exemption_by_employees = data.data;
                    $scope.total_employee = calcTotalExemptionEmploy($scope.exemption_by_employees);

                }

            });

        }
        $scope.ward_round = function (item) {
            var datee = {start_date: item.start_date, end_date: item.end_date, facility_id: facility_id};

            $http.post('/api/ward_round', datee).then(function (data) {

                if (data.data.length < 1) {
                    swal(
                        'Feedback..',
                        'No data set available..',
                        'info'
                    )
                    $scope.rounds = data.data;
                }
                else {
                    $scope.rounds = data.data;

                }

            });

        }
        $scope.exemption_finance = function (item) {
            var datee = {start_date: item.start_date, end_date: item.end_date, facility_id: facility_id};
            var s = moment(item.start_date).toISOString();
            console.log(datee);

            $http.post('/api/exemption_finance', datee).then(function (data) {


                $scope.finances = data.data;

                $scope.totalexfinance = totalexfinances($scope.finances);
                $scope.totalexfinancefeque = totalexfinancesFequence($scope.finances);

            });

        }

$scope.exemption_finance_depts = function (item) {
            var datee = {start_date: item.start_date, end_date: item.end_date, facility_id: facility_id};
            var s = moment(item.start_date).toISOString();
            console.log(datee);

            $http.post('/api/exemption_finance_depts', datee).then(function (data) {


                $scope.exempted_departments = data.data;

                $scope.totalexempted = totalexempted($scope.exempted_departments);


            });

        }

 $scope.exemption_finance_detailed = function (item) {
            var datee = {start_date: item.start_date, end_date: item.end_date, facility_id: facility_id};
            var s = moment(item.start_date).toISOString();
            console.log(datee);

            $http.post('/api/exemption_finance_detail', datee).then(function (data) {
                $scope.exemption_finance_detail= data.data;
                $scope.finances = data.data;
               // $scope.totalexfinance = totalexfinances($scope.finances);
                $scope.totalexfinance = totalexfinancesFequence($scope.finances);

            });

        }

        $scope.Attachment = function (item) {

            var Attachment = {'attachment':item.patient_id}

            $http.post('/api/Attachment', Attachment).then(function (data) {

                if (data.data.length < 1) {

                    swal(
                        'Info',
                        'No Attachment',
                        'info'
                    );

                }

                else {
                    var Attachments = data.data;

                    $mdDialog.show({
                        controller: function ($scope) {
                            $scope.Attachments = Attachments;
                            $scope.selectedPatient = item;
                            $scope.cancel = function () {
                                $mdDialog.hide();

                            };

                            $scope.pdf = function (pdft) {
 console.log(pdft+'/uploads/'+ pdft)
                                $scope.pdd = PDFObject.embed("/uploads/"+pdft, "#example1");

                            }
                        },
                        templateUrl: '/views/modules/Exemption/AttachmentModal.html',
                        parent: angular.element(document.body),
                        clickOutsideToClose: true,
                        fullscreen: false,
                    });
                }

            });


        }
        $scope.complain_view = function (item) {
            var dept = {'dept': item}

            $http.post('/api/complain_view', dept).then(function (data) {

                var complain_contents = data.data;
                $mdDialog.show({
                    controller: function ($scope) {
                        $scope.complain_contents = complain_contents;
                        $scope.cancel = function () {
                            $mdDialog.hide();

                        };
                        $scope.Update_complain_content = function (item) {
                            var updt = {'id': item.id, 'solution': item.solution, 'remarks': item.remarks};

                            $http.post('/api/Update_complain_content', updt).then(function (data) {

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
                    templateUrl: '/views/modules/Exemption/ComplainModal.html',
                    parent: angular.element(document.body),
                    clickOutsideToClose: true,
                    fullscreen: false,
                });

            });

        }
        
  $scope.marriage_conflict_view = function (item) {
            
                $mdDialog.show({
                    controller: function ($scope) {
                        $scope.marriage = item;
                        $scope.marriage.social_description = item.social_description;
                        $scope.marriage.complainer_description = item.complainer_description;
                        $scope.marriage.complainee_description = item.complainee_description;
                        $scope.marriage.event_date = item.event_date;
                        $scope.marriage.staus = item.status;
                        $scope.cancel = function () {
                            $mdDialog.hide();

                        };
                        $scope.Update_conflict_content = function (item) {

                            var issues={id:item.id,status:item.status,user_id:user_id,facility_id:facility_id,event_date:item.event_date,complainer_description:item.complainer_description,social_description:item.social_description,complainee_description:item.complainee_description};
                            $http.post('/api/Update_conflict_content', issues).then(function (data) {

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
                    templateUrl: '/views/modules/Exemption/ConflictModal.html',
                    parent: angular.element(document.body),
                    clickOutsideToClose: true,
                    fullscreen: false,
                });

            

        }

//         $scope.Update_complain_content=function(item){
// var updt={'id':item.id,'solution':item.solution,'remarks':item.remarks};
//
//             $http.post('/api/Update_complain_content',updt).then(function(data) {
//
//                 var msg = data.data.msg;
//                 var statuss = data.data.status;
//                 if (statuss ==0) {
//
//                     swal(
//                         'Error',
//                         msg,
//                         'error'
//                     );
//
//                 }
//                 else{
//                     swal(
//                         'Success',
//                         msg,
//                         'success'
//                     );
//                 }
//             });
//
//         }
        $scope.Update_ward_round_content = function (item) {
            var updt = {'id': item.id, 'output': item.output, 'remarks': item.remarks};

            $http.post('/api/Update_ward_round_content', updt).then(function (data) {

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


        //exemption totality
        var calcTotalExemptionEmploy = function () {
            var TotalExemp = 0;

            for (var i = 0; i < $scope.exemption_by_employees.length; i++) {
                TotalExemp -= -($scope.exemption_by_employees[i].count);
            }

            return TotalExemp;

        }
        var calcTotalExemption = function () {
            var TotalExemp = 0;

            for (var i = 0; i < $scope.exemption_by_genders.length; i++) {
                TotalExemp -= -($scope.exemption_by_genders[i].total);
            }

            return TotalExemp;

        }
        var totalexfinances = function () {
            var totalexfinance = 0;

            for (var i = 0; i < $scope.finances.length; i++) {
                totalexfinance -= -($scope.finances[i].total);
            }

            return totalexfinance;

        }
        var totalexempted = function () {
            var totalexempted = 0;

            for (var i = 0; i < $scope.exempted_departments.length; i++) {
                totalexempted -= -($scope.exempted_departments[i].total);
            }

            return totalexempted;

        }
        var totalexfinancesFequence = function () {
            var totalexfinancefeq = 0;

            for (var i = 0; i < $scope.finances.length; i++) {
                totalexfinancefeq -= -(($scope.finances[i].price)*($scope.finances[i].quantity));
            }

            return totalexfinancefeq;

        }


        //user menu
        $scope.pdf = function (pdft) {

            $scope.pdd = PDFObject.embed("/uploads/" + pdft, "#example1");


        }

        $scope.printUserMenu = function (user_id) {

            $http.get('/api/getUsermenu/' + user_id).then(function (data) {
                $scope.menu = data.data;


            });

        }


        var user_id = $rootScope.currentUser.id;
        $scope.printUserMenu(user_id);


        //Exemption (CRUD) Registration
        var patientName = $scope.selectedPatient;

        // NOW UPLOAD  FILES.


        // end....of uploading


        var resdata = [];
        $scope.showSearch = function (searchKey) {

            $http.post('/api/searchpatientForBill', {searchKey: searchKey}).then(function (data) {
                resdata = data.data;
                $scope.exemption_type_s();
            });
            return resdata;

        }


        //depositing and with drawing

        $scope.deposit_summary_view = function (deposit) {
            var withdrawing={visit_id:deposit.visit_id,patient_id:deposit.patient_id};



            $mdDialog.show({
                controller: function ($scope) {

                    $http.post('/api/deposit_summary_view',withdrawing).then(function (data) {

$scope.depositee=data.data;

                    });

                    $scope.cancel = function () {
                        $mdDialog.hide();

                    };


                    $scope.PrintContent_deposit=function () {
                        //location.reload();
                        var DocumentContainer = document.getElementById('print_d');
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

                },
                templateUrl: '/views/modules/Exemption/deposit_detail.html',
                parent: angular.element(document.body),
                clickOutsideToClose: true,
                fullscreen: false,
            });


        }
        $scope.return_change = function (deposit) {
            var withdrawing={name:deposit.name,visit_id:deposit.visit_id,patient_id:deposit.patient_id,action_type:'return',user_id:user_id,facility_id:facility_id};
            $('#amount_').val('');
            $('#patient_').val('');





            swal({
                title: 'ARE YOU SURE YOU WANT To RETURN CHANGE  TO ' + deposit.name+' '+' ?',

                text:  "",
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

                $http.post('/api/return_change',withdrawing).then(function (data) {
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

                });


            }, function (dismiss) {
                // dismiss can be 'cancel', 'overlay',
                // 'close', and 'timer'
                if (dismiss === 'cancel') {

                }
            });

        }

        $scope.deposit_summary = function (deposit,search) {
            var withdrawing={visit_id:deposit.visit_id,patient_id:deposit.patient_id,withdraw:search,action_type:'withdraw',user_id:user_id,facility_id:facility_id};
            $('#amount_').val('');
            $('#patient_').val('');

            $http.post('/api/deposit_summary',withdrawing).then(function (data) {
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

            });


        }
        $scope.saveDepositCash = function (patient,deposits) {
            var depositing={patient_id:patient.id,amount:deposits.amount,action_type:'deposit',user_id:user_id,facility_id:facility_id};
            $('#amount_').val('');
            $('#patient_').val('');

            $http.post('/api/saveDepositCash',depositing).then(function (data) {
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

            });


        }
        $scope.showSearchFordeposit = function (searchKey) {

            $http.post('/api/showSearchFordeposit', {searchKey: searchKey}).then(function (data) {
                resdata = data.data;

            });
            return resdata;

        }

        $scope.getDepositing_lists = function (dataa) {

            $http.post('/api/getDepositing_lists',dataa).then(function (data) {
                $scope.deposits = data.data;
                $scope.deposited=$scope.depositedAmount();
                $scope.used=$scope.usedAmount();
                $scope.balance=$scope.balanceAmount();

            });


        }

        $scope.getEmployeeDepositing_lists = function (dataa) {

            $http.post('/api/getEmployeeDepositing_lists',dataa).then(function (data) {
                $scope.deposits = data.data;
                $scope.employeedeposits = data.data;
                $scope.deposited=$scope.depositedAmount();
                $scope.used=$scope.usedAmount();
                $scope.balance=$scope.balanceAmount();

            });


        }

        $scope.depositedAmount=function(){
            var sum = 0;

            for (var i = 0; i < $scope.deposits[0].length; i++) {
                sum -= -( $scope.deposits[0][i].amount);
            }

            return sum;
        }
        $scope.usedAmount=function(){
            var sum = 0;

            for (var i = 0; i < $scope.deposits[0].length; i++) {
                sum -= -( $scope.deposits[0][i].withdraw);
            }

            return sum;
        }
$scope.balanceAmount=function(){
            var sum = 0;

            for (var i = 0; i < $scope.deposits[0].length; i++) {
                sum -= -( $scope.deposits[0][i].balance);
            }

            return sum;
        }

        // depositing and withdraw ends
        $scope.exempted_service = function (encounter) {

            var patient_category = encounter.exemption_type_id;
            var service_category = 1;
            var service_id = 1;
            var price_id = 1;
            var item_type_id = 1;
            var patient_id = encounter.selectedPatient.patient_id;
            var facility_id = $rootScope.currentUser.facility_id;
            var user_id = $rootScope.currentUser.id;

            var bill_category_id = encounter.exemption_type_id;
            var main_category_id = 4;

            var enterEncounter = {
                'item_type_id': item_type_id,
                'patient_category': patient_category,
                'main_category_id': main_category_id,
                'bill_id': bill_category_id,
                'service_category': service_category,
                'service_id': service_id,
                'price_id': price_id,
                'patient_id': patient_id,
                'facility_id': facility_id,
                'user_id': user_id
            };

        }

        $scope.exemption_type_list = function () {
            $http.get('/api/exemption_type_list/' + user_id).then(function (data) {
                $scope.exemption_types = data.data;


            });
        }


        $scope.exemption_type_s = function () {
            $http.get('/api/exemption_type_s').then(function (data) {
                $scope.exemption_s = data.data;


            });
        }

        $scope.exemption_type_list();
        $scope.exemption_type_s();

        $scope.exemption_type_registration = function (exemption_type) {

            $http.post('/api/exemption_type_registration', exemption_type).then(function (data) {

                $scope.exemption_type_list();
                // $scope.exemption_type.exemption_type="";
                var sending = data.data;
                swal(
                    'Feedback..',
                    sending,
                    'success'
                )
            });
        }

 $scope.social_referral_registry = function (ref,patient) {
if(patient==undefined){
    swal('Please choose Client','','error')
    return;
}
var dataa={patient_id:patient,user_id:user_id,facility_id:facility_id,ref_type:ref.ref_type,facility_name:ref.facility_name}

            $http.post('/api/social_referral_registry',dataa).then(function (data) {


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

        $scope.vulnerables = function (item) {
            var datee = {start_date: item.start_date, end_date: item.end_date, facility_id: facility_id};
            $http.post('/api/vulnerables', datee).then(function (data) {
                $scope.vulnerables = data.data;

                $scope.female_under_59_total = female_under_59vulnerables($scope.vulnerables);
                $scope.male_under_59_total = male_under_59vulnerables($scope.vulnerables);
                $scope.total_under_59_total = total_under_59vulnerables($scope.vulnerables);
                $scope.female_under_9_total = female_under_9vulnerables($scope.vulnerables);
                $scope.male_under_9_total = male_under_9vulnerables($scope.vulnerables);
                $scope.total_under_9_total = total_under_9vulnerables($scope.vulnerables);
                $scope.female_under_24_total = female_under_24vulnerables($scope.vulnerables);
                $scope.male_under_24_total = male_under_24vulnerables($scope.vulnerables);
                $scope.total_under_24_total = total_under_24vulnerables($scope.vulnerables);
                $scope.female_above_25_total = female_above_25vulnerables($scope.vulnerables);
                $scope.male_above_25_total = male_above_25vulnerables($scope.vulnerables);
                $scope.total_above_25_total = total_above_25vulnerables($scope.vulnerables);
                $scope.total_female_total = total_femalevulnerables($scope.vulnerables);
                $scope.total_male_total = total_malevulnerables($scope.vulnerables);
                $scope.total_total = totalvulnerables($scope.vulnerables);

            });
        }

        $scope.violances = function (item) {
            var datee = {start_date: item.start_date, end_date: item.end_date, facility_id: facility_id};
            $http.post('/api/violances', datee).then(function (data) {
                $scope.attendance = data.data;
  //console.log($scope.attendance)
            });
        }


        //totality violances

        var female_under_59 = function () {
            var female_under_59_total = 0;

            for (var i = 0; i < $scope.violences.length; i++) {
                female_under_59_total -= -($scope.violences[i].female_under_59);
            }

            return female_under_59_total;

        }
        var male_under_59 = function () {
            var male_under_59_total = 0;

            for (var i = 0; i < $scope.violences.length; i++) {
                male_under_59_total -= -($scope.violences[i].male_under_59);
            }

            return male_under_59_total;

        }
        var total_under_59 = function () {
            var total_under_59_total = 0;

            for (var i = 0; i < $scope.violences.length; i++) {
                total_under_59_total -= -($scope.violences[i].total_under_59);
            }

            return total_under_59_total;

        }

        var female_under_9 = function () {
            var female_under_9_total = 0;

            for (var i = 0; i < $scope.violences.length; i++) {
                female_under_9_total -= -$scope.violences[i].female_under_9;
            }

            return female_under_9_total;

        }
        var male_under_9 = function () {
            var male_under_9_total = 0;

            for (var i = 0; i < $scope.violences.length; i++) {
                male_under_9_total -= -($scope.violences[i].male_under_9);
            }

            return male_under_9_total;

        }
        var total_under_9 = function () {
            var total_under_9_total = 0;

            for (var i = 0; i < $scope.violences.length; i++) {
                total_under_9_total -= -($scope.violences[i].total_under_9);
            }

            return total_under_9_total;

        }
        var female_under_24 = function () {
            var female_under_24_total = 0;

            for (var i = 0; i < $scope.violences.length; i++) {
                female_under_24_total -= -($scope.violences[i].female_under_24);
            }

            return female_under_24_total;

        }
        var male_under_24 = function () {
            var male_under_24_total = 0;

            for (var i = 0; i < $scope.violences.length; i++) {
                male_under_24_total -= -( $scope.violences[i].male_under_24);
            }

            return male_under_24_total;

        }

        var total_under_24 = function () {
            var total_under_24_total = 0;

            for (var i = 0; i < $scope.violences.length; i++) {
                total_under_24_total -= -($scope.violences[i].total_under_24);
            }

            return total_under_24_total;

        }
        var female_above_25 = function () {
            var female_above_25_total = 0;

            for (var i = 0; i < $scope.violences.length; i++) {
                female_above_25_total -= -($scope.violences[i].female_above_25);
            }

            return female_above_25_total;

        }
        var male_above_25 = function () {
            var male_above_25_total = 0;

            for (var i = 0; i < $scope.violences.length; i++) {
                male_above_25_total -= -($scope.violences[i].male_above_25);
            }

            return male_above_25_total;

        }
        var total_above_25 = function () {
            var total_above_25_total = 0;

            for (var i = 0; i < $scope.violences.length; i++) {
                total_above_25_total -= -($scope.violences[i].total_above_25);
            }

            return total_above_25_total;

        }
        var total_female = function () {
            var total_female_total = 0;

            for (var i = 0; i < $scope.violences.length; i++) {
                total_female_total -= -($scope.violences[i].total_female);
            }

            return total_female_total;

        }
        var total_male = function () {
            var total_male_total = 0;

            for (var i = 0; i < $scope.violences.length; i++) {
                total_male_total -= -$scope.violences[i].total_male;
            }

            return total_male_total;

        }
        var total = function () {
            var total_total = 0;

            for (var i = 0; i < $scope.violences.length; i++) {
                total_total -= -$scope.violences[i].total;
            }

            return total_total;

        }


        //totality vulnerables

        var female_under_59vulnerables = function () {
            var female_under_59_total = 0;

            for (var i = 0; i < $scope.vulnerables.length; i++) {
                female_under_59_total -= -$scope.vulnerables[i].female_under_59;
            }

            return female_under_59_total;

        }
        var male_under_59vulnerables = function () {
            var male_under_59_total = 0;

            for (var i = 0; i < $scope.vulnerables.length; i++) {
                male_under_59_total -= -$scope.vulnerables[i].male_under_59;
            }

            return male_under_59_total;

        }
        var total_under_59vulnerables = function () {
            var total_under_59_total = 0;

            for (var i = 0; i < $scope.vulnerables.length; i++) {
                total_under_59_total -= -$scope.vulnerables[i].total_under_59;
            }

            return total_under_59_total;

        }

        var female_under_9vulnerables = function () {
            var female_under_9_total = 0;

            for (var i = 0; i < $scope.vulnerables.length; i++) {
                female_under_9_total -= -$scope.vulnerables[i].female_under_9;
            }

            return female_under_9_total;

        }
        var male_under_9vulnerables = function () {
            var male_under_9_total = 0;

            for (var i = 0; i < $scope.vulnerables.length; i++) {
                male_under_9_total -= -$scope.vulnerables[i].male_under_9;
            }

            return male_under_9_total;

        }
        var total_under_9vulnerables = function () {
            var total_under_9_total = 0;

            for (var i = 0; i < $scope.vulnerables.length; i++) {
                total_under_9_total -= -$scope.vulnerables[i].total_under_9;
            }

            return total_under_9_total;

        }
        var female_under_24vulnerables = function () {
            var female_under_24_total = 0;

            for (var i = 0; i < $scope.vulnerables.length; i++) {
                female_under_24_total -= -$scope.vulnerables[i].female_under_24;
            }

            return female_under_24_total;

        }
        var male_under_24vulnerables = function () {
            var male_under_24_total = 0;

            for (var i = 0; i < $scope.vulnerables.length; i++) {
                male_under_24_total -= -$scope.vulnerables[i].male_under_24;
            }

            return male_under_24_total;

        }

        var total_under_24vulnerables = function () {
            var total_under_24_total = 0;

            for (var i = 0; i < $scope.vulnerables.length; i++) {
                total_under_24_total -= -$scope.vulnerables[i].total_under_24;
            }

            return total_under_24_total;

        }
        var female_above_25vulnerables = function () {
            var female_above_25_total = 0;

            for (var i = 0; i < $scope.vulnerables.length; i++) {
                female_above_25_total -= -$scope.vulnerables[i].female_above_25;
            }

            return female_above_25_total;

        }
        var male_above_25vulnerables = function () {
            var male_above_25_total = 0;

            for (var i = 0; i < $scope.vulnerables.length; i++) {
                male_above_25_total -= -$scope.vulnerables[i].male_above_25;
            }

            return male_above_25_total;

        }
        var total_above_25vulnerables = function () {
            var total_above_25_total = 0;

            for (var i = 0; i < $scope.vulnerables.length; i++) {
                total_above_25_total -= -$scope.vulnerables[i].total_above_25;
            }

            return total_above_25_total;

        }
        var total_femalevulnerables = function () {
            var total_female_total = 0;

            for (var i = 0; i < $scope.vulnerables.length; i++) {
                total_female_total -= -$scope.vulnerables[i].total_female;
            }

            return total_female_total;

        }
        var total_malevulnerables = function () {
            var total_male_total = 0;

            for (var i = 0; i < $scope.vulnerables.length; i++) {
                total_male_total -= -$scope.vulnerables[i].total_male;
            }

            return total_male_total;

        }
        var totalvulnerables = function () {
            var total_total = 0;

            for (var i = 0; i < $scope.vulnerables.length; i++) {
                total_total -= -$scope.vulnerables[i].total;
            }

            return total_total;

        }


        //  update


        $scope.exemption_type_update = function (exemption_type) {

            swal({
                title: 'Are you sure You Want To Update?',

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

                $http.post('/api/exemption_type_update', exemption_type).then(function (data) {

                    swal(
                        'Feedback..',
                        'Updated..',
                        'success'
                    )
                    $scope.exemption_type_list();

                })


            }, function (dismiss) {
                // dismiss can be 'cancel', 'overlay',
                // 'close', and 'timer'
                if (dismiss === 'cancel') {

                }
            })


        }

        //  patient_exemption_status_update


        $scope.exemption_type_delete = function (exemption_type) {
////console.log(exemption_type)
            swal({
                title: 'Are you sure You Want To Delete?',

                text: "",
                type: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: ' Yes!  ',
                cancelButtonText: '  No, cancel!',
                confirmButtonClass: 'btn btn-success',
                cancelButtonClass: 'btn btn-danger',
                buttonsStyling: false
            }).then(function () {

                $http.get('/api/exemption_type_delete/' + exemption_type).then(function (data) {
                    var msg = data.data.msg;
                    if (data.data.status == 0) {
                        swal(
                            'Error',
                            msg,
                            'error'
                        )

                    }
                    else {
                        swal(
                            'Feedback..',
                            msg,
                            'info'
                        )
                        $scope.exemption_type_list();
                    }


                })


            }, function (dismiss) {
                // dismiss can be 'cancel', 'overlay',
                // 'close', and 'timer'
                if (dismiss === 'cancel') {

                }
            })


        }

        //  patient_exemption_status_update


        $scope.patient_exemption_status_update = function (status, exemption) {

            var exemption_status_update_normal = {
                'id': exemption.id,
                'bill_id': status.id,
                'main_category_id': status.pay_cat_id,
                'sub_act_id': exemption.sub_act_id,
                'sub_category_name1': exemption.sub_category_name,
                'sub_category_name': status.sub_category_name,
                'main_category_prev': exemption.pay_cat_id,
                'patient_id': exemption.patient_id,
                'facility_id': facility_id,
                'user_id': user_id,
                'reason_for_revoke': "-"
            };


            swal({
                title: 'Are you sure You Want To Change This ' + exemption.sub_category_name + ' To ' + status.sub_category_name + ' ?',

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


                $http.post('/api/patient_exemption_status_update', exemption_status_update_normal).then(function (data) {

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



        //Exemption Status CRUD


        $scope.exemption_status_registration = function (exemption_status) {
            ////console.log(exemption_status)
            $http.post('/api/exemption_status_registration', exemption_status).then(function (data) {

                exemption_status = " ";
                var sending = data.data;
                $scope.exemption_status_list();


                swal(
                    'Feedback..',
                    sending,
                    'success'
                )
            });
        }

        $scope.exemption_status_list = function () {
            $http.get('/api/exemption_status_list').then(function (data) {
                $scope.exemption_statuses = data.data;


            });
        }

        $scope.exemption_status_list();
        //  update


        $scope.exemption_status_update = function (exemption_status) {

            swal({
                title: 'Are you sure You Want To Update?',

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

                $http.post('/api/exemption_status_update', exemption_status).then(function (data) {


                    $scope.exemption_status_list();
                    swal(
                        'Response!',
                        'Updated ....',
                        'success'
                    )
                })


            }, function (dismiss) {
                // dismiss can be 'cancel', 'overlay',
                // 'close', and 'timer'
                if (dismiss === 'cancel') {

                }
            })


        }


//  delete
        $scope.exemption_status_delete = function (exemption_status, id) {

            swal({
                title: 'Are you sure You Want To Delete?',

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

                $http.get('/api/exemption_status_delete/' + id).then(function (data) {


                    $scope.exemption_status_list();
                    swal(
                        '',
                        'Item Deleted',
                        'warning'
                    )
                })


            }, function (dismiss) {
                // dismiss can be 'cancel', 'overlay',
                // 'close', and 'timer'
                if (dismiss === 'cancel') {

                }
            })


        }

        $scope.vulnerable_followup_neglect = function (violence_cat,patient_id) {
            ////console.log(violence_cat)
            var neglect="NO";
            var followup="NO";
            var vulnerable="NO";
            if (violence_cat == undefined) {
                swal('Error', 'Choose From optional field', 'error')
                return;
            }

            else {
                if (violence_cat.receive_r == "vulnerable") {
                    vulnerable="YES";
                }
                if (violence_cat.receive_r == "followup") {
                    followup="YES";
                }
                if (violence_cat.receive_r == "neglect") {
                    neglect="YES";
                }
                $http.post('/api/vulnerable_followup_neglect', {user_id:user_id,facility_id:facility_id,patient_id:patient_id,vulnerable:vulnerable,followup:followup,neglect:neglect,remarks:violence_cat.remarks}).then(function (data) {
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

        $scope.violence_cat_registration = function (violence_cat) {
            ////console.log(violence_cat)
            if (violence_cat == undefined) {
                swal('Error', 'Fill This field', 'error')
            }
            else {
                $http.post('/api/violence_cat_registration', violence_cat).then(function (data) {
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

                    $scope.violence_cat_list();
                });

            }
        }


        $scope.violence_cat_update = function (violence_cat) {

            $http.post('/api/violence_cat_update', violence_cat).then(function (data) {

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

                $scope.violence_cat_list();
            });

        }
        $scope.violence_cat_list = function () {


            $http.get('/api/violence_cat_list').then(function (data) {

                $scope.violence_cats = data.data;
            });

        }
        //gbv/vac
        $http.get('/api/violence_type_list').then(function (data) {

            $scope.violence_types = data.data;
        });

        $scope.institution_registration = function (institute) {
            ////console.log(institute)
            if (institute == undefined) {
                swal('Error', 'Fill Instituition Name', 'error')
            } else if (institute.institution_type == undefined) {
                swal('Error', 'Fill Instituition Type', 'error')
            }
            else {
                $http.post('/api/institution_registration', institute).then(function (data) {
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

                    $scope.institution_list();
                });

            }


        }


        $scope.institution_update = function (institute) {

            $http.post('/api/institution_update', institute).then(function (data) {

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

                $scope.institution_list();
            });

        }


        $scope.institution_list = function () {

            $http.get('/api/institution_list').then(function (data) {

                $scope.institutes = data.data;
            });


        }
        $scope.violence_cat_list();


        $scope.violence_registration = function (attach, patient) {
//////console.log(violence);
            if (patient == undefined) {

                swal('Error', 'Choose Patient', 'error')

            }

            if (attach == undefined) {

                swal('Error', 'Choose Attachment', 'error')

            }


            else {


                var patient_id = patient;
                var description = attach.description;


                formdata.append('user_id', user_id);
                formdata.append('facility_id', facility_id);
                formdata.append('patient_id', patient_id);
                formdata.append('describtion', description);

                var request = {
                    method: 'POST',
                    url: '/api/' + 'violation_registration',
                    data: formdata,
                    headers: {
                        'Content-Type': undefined
                    }

                };

                // SEND THE FILES.
                $http(request).then(function (data) {
                        ////console.log(data.data);
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
                    })
                    .then(function () {
                    });


            }


        }
        $scope.department_list = function () {

            $http.get('/api/getSpecialClinics').then(function (data) {
                $scope.departments = data.data;

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
                    'user_id': user_id
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

        var user_exemption = [];
        $scope.SearchUser = function (seachKey) {

            var searchUser = {'userKey': seachKey, 'facility_id': facility_id};
            $http.post('/api/getUserToSetStoreToAccess', searchUser).then(function (data) {
                user_exemption = data.data;

            });
            $scope.exemption_type_s();
            return user_exemption;

        }
        $scope.User_exemption_populate = [];
        var ckecks;

        $scope.populateInArray = function (category, selected_user_exemption) {
            if (selected_user_exemption == undefined) {
                swal(
                    'Info',
                    'Choose User first!!!',
                    'info'
                )
                return;
            }
            if (category.value1 == true) {


                for (var i = 0; i < $scope.User_exemption_populate.length; i++) {
                    if ($scope.User_exemption_populate[i].exempt_id == category.id) {

                        return;
                    }
                }


                $scope.User_exemption_populate.push({
                    'exempt_id': category.id,
                    'user_id': selected_user_exemption.id,
                    'exemption_name': category.exemption_name,
                });


            }

        }

        $scope.exemption_user_configure = function () {

            if ($scope.User_exemption_populate.length < 1) {
                swal(
                    'Error',
                    'Nothing to save',
                    'error'
                )
            }

            else {
                $http.post('/api/exemption_user_configure', $scope.User_exemption_populate).then(function (data) {
                    var msg = data.data.msg;
                    var status = data.data.status;
                    if (status == 0) {
                        swal(
                            'Feedback!',
                            msg,
                            'info'
                        )
                    }
                    else {
                        swal(
                            'Feedback!',
                            msg,
                            'success'
                        )
                    }


                    if (data) {
                        $scope.User_exemption_populate = [];
                    }
                });
            }

        }


        $scope.removeItem = function (x) {

            $scope.User_exemption_populate.splice(x, 1);

        }

        $scope.showSearchMarital = function (searchKey) {

            $http.get('/api/getMaritalStatus/' + searchKey).then(function (data) {
                resdata = data.data;

            });
            ////console.log(maritals);
            return resdata;
        }

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
var tribe=[];
        $scope.showSearchTribe = function (searchKey) {
            $http.post('/api/getTribes', {search:searchKey}).then(function (data) {
                tribe = data.data;
            });
             
            return tribe;
        }


        // $scope.getRelationships = function (searchKey) {
        //
        //     $http.get('/api/getRelationships/' + searchKey).then(function (data) {
        //         resdata = data.data;
        //     });
        //     return resdata;
        // }
        $scope.showSearchResidences = function (searchKey) {

            $http.get('/api/searchResidences/' + searchKey).then(function (data) {
                resdata = data.data;
            });
            ////console.log(resdata);
            return resdata;
        }


        $scope.Create_debt = function (trans_id) {
$scope.debt=[];
            $scope.debt.push({user_i:user_id,id:trans_id.id,patient_id:trans_id.patient_id});

            swal({
                title: 'Are you sure You Want To Change This Into DEBT ?',

                text: "This can not be Reversed",
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


                $http.post('/api/Create_debt',$scope.debt).then(function (data) {

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





        //discounting ................................................


        var resdata =[];
        $scope.showSearch = function(searchKey) {
            if(searchKey.length<5){

            }
            else{
                $http.post('/api/searchpatientForBill',{searchKey:searchKey}).then(function(data) {
                    resdata = data.data;
                });
                return resdata;
            }
        }



        var user_id=$rootScope.currentUser.id;
        var facility_id=$rootScope.currentUser.facility_id;

        //user menu
        $scope.printUserMenu=function (user_id) {

            $http.get('/api/getUsermenu/'+user_id ).then(function(data) {
                $scope.menu=data.data;


            });

        }
        var user_id=$rootScope.currentUser.id;
        $scope.printUserMenu(user_id);


        //Exemption (CRUD) Registration
        var patientName= $scope.selectedPatient;

        // $scope.exemption_registration=function (exempt) {
        //
        //     var exemption_data={'exemption_no':1,'user_id':$rootScope.currentUser.id,'facility_code':$rootScope.currentUser.facility_id,"patient_id":exempt.selectedPatient.patient_id,"status_id":exempt.status_id,"exemption_type_id":exempt.exemption_type_id,
        //         "exemption_reason":exempt.exemption_reason,"reason_for_revoke":exempt.reason_for_revoke,'description':exempt.description};
        //     console.log(exemption_data)
        //     $http.post('/api/patient_exemption',exemption_data).then(function(data) {
        //
        //     });
        // }



        $scope.loadBill=function (selectedPatient) {


            $http.get('/api/loadDiscountBill/'+selectedPatient.patient_id).then(function(data) {
                $scope.discounts=data.data;
                $scope.previusDiscount = calcDiscountfromDB($scope.discounts);

                $scope.jumla = calctotal($scope.discounts);

                $scope.TotalDiscount="";

            });
        }
        $scope.discountArray=[];
        var discount_reason="";


        $scope.discounting=function (discount) {

            var asilimia=(discount.amount/(discount.price * discount.quantity) * 100);

            if ((discount.price * discount.quantity) - discount.amount >= 0){
                $scope.discountArray.push({'patient_id':discount.patient_id,'receipt_number':discount.invoice_id,'id':discount.item_referrence,'user_id':user_id,'invoice_id':discount.item_referrence,
                    'quantity':discount.quantity,'price':discount.price,'discount':discount.amount});
					
                $scope.TotalDiscount=calcDiscountFromSocial($scope.discountArray);

            }
            else if(discount.amount ==null){

            }
            else{
                swal(
                    'Warning',
                    'Please Check again Your Discount. Otherwise Your discount for any row data exceeds limit, will be ignored',
                    'warning'
                )
            }




        }
        var discounting_resaon="";
        $scope.discount_reason=function (reason) {
            discount_reason=reason;

            console.log(discounting_resaon);
        }


        $scope.CommitDiscount=function () {


            if($scope.discountArray.length >0)
            {

                swal({
                    title: 'Are you sure?',

                    text: "You won't be able to revert this!",
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

//please write reasons for this discount

                    swal({
                        title: 'Reasons for This discount',
                        input: 'textarea',

                        showCancelButton: true,
                        inputValidator: function (value) {
                            return new Promise(function (resolve, reject) {
                                if (value) {
                                    resolve()
                                } else {
                                    reject('You need to write Reasons for This discount!')
                                }



                                //--------------------




                                //--------------------
                            })
                        }
                    }).then(function (result) {
                        discounting_resaon={'discount_reason':result,'patient_id':$scope.discountArray[0].patient_id,
                            'receipt_number':$scope.discountArray[0].receipt_number,'facility_id':facility_id};

                        $http.post('/api/invoice_discount',$scope.discountArray).then(function(data) {
                            if(data){
                                $http.post('/api/discountingReason',discounting_resaon).then(function(data) {

                                });
                            }


                            swal(

                                'Success!!!',
                                'Discount Successful Granted..',
                                'success'
                            )

                            $scope.discountArray=[];

                        });
                    })





                }, function (dismiss) {
                    // dismiss can be 'cancel', 'overlay',
                    // 'close', and 'timer'
                    if (dismiss === 'cancel') {
                        swal(
                            'Cancelled',
                            'Discount Has Cancelled',
                            'error'
                        )
                    }
                })



            }
            else{
                swal(

                    'Oops!!!..',
                    'Discount Already Granted..',
                    'info'
                )
            }


        }


        //calculation of transactions from all Point Of services
        var calctotal = function(){
            var sum = 0;

            for(var i=0; i<$scope.discounts.length;i++){
                sum -= -($scope.discounts[i].price * $scope.discounts[i].quantity);
            }

            return sum;

        }
        //calculation of transactions Discount from all Point Of services by social welfare officer
        var calcDiscountFromSocial = function(){
            var DiscountFromSocial = 0;

            for(var i=0; i<$scope.discountArray.length;i++){
                DiscountFromSocial -= -($scope.discountArray[i].discount);
            }

            return DiscountFromSocial;

        }

        //calculation of transactions Discount from all Point Of services  though by default is zero discount
        var calcDiscountfromDB = function(){
            var TotalDiscountfromDB = 0;

            for(var i=0; i<$scope.discounts.length;i++){
                TotalDiscountfromDB -= -($scope.discounts[i].discount);
            }

            return TotalDiscountfromDB;

        }

        //discounting ................................................


        $scope.SelectedUserWithExemptionAccess = function(user_id){

            $http.get('/api/SelectedUserWithExemptionAccess/'+user_id ).then(function(data) {
                $scope.access_givens=data.data;

            });

        }

        $scope.Remove_user_Exemption_access = function(id){

            $http.get('/api/Remove_user_Exemption_access/'+id ).then(function(data) {
                $scope.removeds=data.data;
                swal('','Access removed','success')

            });

        }

        var calcgrandTotal = function () {
            var TotalExemp = 0;
            for (var i = 0; i < $scope.temporary_exemptions.length; i++) {
                TotalExemp -= -(($scope.temporary_exemptions[i].quantity*$scope.temporary_exemptions[i].price)-$scope.temporary_exemptions[i].discount);
            }

            return TotalExemp;

        }
        var calcgrandSummary = function () {
            var TotalExemp = 0;
            for (var i = 0; i < $scope.temporary_exemptions.length; i++) {
                TotalExemp -= -($scope.temporary_exemptions[i].price * $scope.temporary_exemptions[i].quantity);
            }

            return TotalExemp;

        }
        var calcgrandTotalPay = function () {
            var TotalExemp = 0;
            for (var i = 0; i <  $scope.checkedList.length; i++) {
                TotalExemp -= -(( $scope.checkedList[i].quantity* $scope.checkedList[i].price)- $scope.checkedList[i].discount);
            }

            return TotalExemp;

        }
        var calcTotal = function () {
            var Total = 0;
            for (var i = 0; i < $scope.temporary_exemptions.length; i++) {
                Total -= -(($scope.temporary_exemptions[i].quantity*$scope.temporary_exemptions[i].price));
            }

            return Total;

        }

        var calcDiscount = function () {
            var TotalDiscount = 0;
            for (var i = 0; i < $scope.temporary_exemptions.length; i++) {
                TotalDiscount -= -($scope.temporary_exemptions[i].discount);
            }

            return TotalDiscount;

        }

        var resdata =[];
        $scope.showSearchIvoice = function(searchKey) {
            $http.post('/api/patientsToPoS',{"search":searchKey,"facility_id":facility_id}).then(function(data) {
                resdata = data.data;
            });
            return resdata;
        }
        var PoSata =[];
        $scope.searchItems = function(searchKey,patient) {
            var category_id =patient.patient_category_id;
            if (category_id == null) {
                swal("Please search patient first!", "", "error"); return;
            }

            $http.post('/api/itemsToPoS',{"search":searchKey, "facility_id":facility_id, "main_category_id":patient.main_category_id, "patient_category_id":patient.patient_category_id,sub_category_name: patient.sub_category_name}).then(function(data) {
                PoSata = data.data;
            });
            return PoSata;
        }
        $scope.itemData = [];
        $scope.removeItem = function(item){

            var indexofItem = $scope.itemData.indexOf(item);
            $scope.itemData.splice(indexofItem,1);
            $scope.toto = $scope.getTotal();

        }
        $scope.getTotal = function () {
            var  total = 0;
            for(var i = 0; i < $scope.itemData.length ; i++) {
                total += ($scope.itemData[i].sub_total);
            }
            return total;
        }

        $scope.addItem = function(item,x,quantity){


                var payment_status = 1;
                var sub_category_name = x.sub_category_name.toLowerCase();

                var payment_filter = 2;
                var main_category = item.main_category_id;
                if(item == null || x==null || quantity ==null){
                    swal("Please search Patient and Item the click 'Add' Button"); return;
                }
                for(var i=0;i<$scope.itemData.length;i++)
                    if($scope.itemData[i].item_id == x.item_id){
                        swal(x.item_name+" already in your order list!","","info");
                        $scope.selectedItem= "";
                        $scope.quantity= "";
                        return;}
                var sub_total = x.price * quantity;


                $scope.itemData.push({
                    hospital_shop_posting:false,
                    "dept_id": x.dept_id,
                    "item_id": x.item_id,
                    "item_name": x.item_name,
                    "sub_total": sub_total,
                    "receipt_number": "",
                    "item_type_id": x.item_type_id,
                    "quantity": quantity,
                    "price": x.price,
                    "item_price_id": x.price_id,
                    "user_id": user_id,
                    "patient_id": item.patient_id,
                    "medical_record_number": item.medical_record_number,
                    "account_number": item.account_number,
                    "account_number_id": item.account_id,
                    "first_name": item.first_name,
                    "middle_name": item.middle_name,
                    "last_name": item.last_name,
                    "status_id": payment_status,
                    "sub_category_name": item.sub_category_name,
                    "payment_filter": payment_filter,
                    "payment_method_id": 1,
                    "facility_id": facility_id,
                    "discount": 0,
                    "discount_by": user_id
                });
                $scope.toto = $scope.getTotal();
               $('#item').val('');



        }


        $scope.toto = $scope.getTotal();
        $scope.processSales = function (paymentMethod,patient) {
            var sub_category_name = patient.sub_category_name.toLowerCase();

                var x= $scope.getTotal();


            swal({
                title: 'Are you sure you want to Create debt for this transaction with a sum of '+x+' Tshs?',
                type: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Yes'
            }).then(function () {
                var receiptNumber="";
                //gepg codes snippet

                $http.post('/api/saveFromPoS',$scope.itemData).then(function (data) {

                    if(data.data){
                        $scope.itemData=[];
                        $scope.temporary_exemption_list();
                        swal('Debt Successful created','GO DEBTORS LIST TO PRINT BILLING FORM','success');
                    }
                    else {
                        swal('ATTENTION!','Something went wrong,Debt Failed','info');

                    }
                });
            }, function (dismiss) {});

        }

        $scope.Exemption_finance=function () {
            //location.reload();
            var DocumentContainer = document.getElementById('divtoprint7');
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
        $scope.Exemption_finance_detail=function () {
            //location.reload();
            var DocumentContainer = document.getElementById('divtoprint_detail');
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
        $scope.Exemption_summary=function () {
            //location.reload();
            var DocumentContainer = document.getElementById('divtoprint8');
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
        $scope.Exemption_employee=function () {
            //location.reload();
            var DocumentContainer = document.getElementById('divtoprint9');
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
        $scope.exempted_department=function () {
            //location.reload();
            var DocumentContainer = document.getElementById('divtoprint90');
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
        $scope.Exemption_sub_finance=function () {
            //location.reload();
            var DocumentContainer = document.getElementById('divtoprint009');
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
        $scope.PrintContent_mtuha=function () {
            //location.reload();
            var DocumentContainer = document.getElementById('divtoprint_mtuha');
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

        $scope.dept_sum_print=function () {

            //location.reload();
            var DocumentContainer = document.getElementById('dept_sum_id');
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

        $scope.PrintEmployeeDepositing_lists=function () {

            //location.reload();
            var DocumentContainer = document.getElementById('deposit_id');
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



        $scope.gbv_vac_panel = function (clientInfo) {
            if (clientInfo !=undefined) {



            $mdDialog.show({
                controller: function ($scope) {

                    $scope.patientIfor=clientInfo;

                    $scope.SocialWelfareDataHistorory=function(){
                        $http.post('/api/SocialWelfareDataHistorory',{patient_id:clientInfo.patient_id}).then(function (data) {

                            $scope.socialHistories=data.data;

                        });
                    }
                    $scope.SocialWelfareDataHistorory();



                    $scope.UpdateSocialWelfareData=function(record){
                        if(record.event_date !=undefined){
                            var appointDate = record.event_date;


                            if (record.event_date instanceof Date) {
                                appointDate = record.event_date.toISOString();
                            }
                            if (record.event_date == undefined) {
                                return;
                            }


                            if (appointDate != '' && ((new Date()).getFullYear() < parseInt(appointDate.substring(0, 4)) ||
                                ((new Date()).getFullYear() == parseInt(appointDate.substring(0, 4)) && ((new Date()).getMonth() + 1)< parseInt(appointDate.substring(appointDate.indexOf("-") + 1, 7))) ||
                                ((new Date()).getFullYear() == parseInt(appointDate.substring(0, 4)) && ((new Date()).getMonth() + 1) == parseInt(appointDate.substring(appointDate.indexOf("-") + 1, 7)) && ((new Date()).getDate()) < parseInt(appointDate.substring(appointDate.lastIndexOf("-") + 1, 10))))) {


                                swal('Future dates Restricted!', '', 'warning');
                                return;
                            }

                        }
                        $http.post('/api/UpdateSocialWelfareData',record).then(function (data) {

                            $scope.social=data.data;
                            if (data.data.status==1) {
                                swal("",data.data.msg,"success");
                            }
                            else{
                                swal("","Failed","error");
                            }


                            $scope.SocialWelfareDataHistorory();

                        });
                    }
                    $scope.SocialWelfareData=function(record){
if (record==undefined) {
    swal("","No any record found to save","info");
    return;
}
                        if(record.event_date !=undefined){
                            var appointDate = record.event_date;


                            if (record.event_date instanceof Date) {
                                appointDate = record.event_date.toISOString();
                            }
                            if (record.event_date == undefined) {
                                return;
                            }


                            if (appointDate != '' && ((new Date()).getFullYear() < parseInt(appointDate.substring(0, 4)) ||
                                ((new Date()).getFullYear() == parseInt(appointDate.substring(0, 4)) && ((new Date()).getMonth() + 1)< parseInt(appointDate.substring(appointDate.indexOf("-") + 1, 7))) ||
                                ((new Date()).getFullYear() == parseInt(appointDate.substring(0, 4)) && ((new Date()).getMonth() + 1) == parseInt(appointDate.substring(appointDate.indexOf("-") + 1, 7)) && ((new Date()).getDate()) < parseInt(appointDate.substring(appointDate.lastIndexOf("-") + 1, 10))))) {


                                swal('Future dates Restricted!', '', 'warning');
                                return;
                            }

                        }

                        var records={"followup":record.followup,"vulnerable":record.vulnerable,"screening":record.screening,
     "within_72_hrs":record.within_72_hrs,
    "pt_result":record.pt_result,"hiv_result":record.hiv_result,"sti_result":record.sti_result,
    "disability":record.disability,
                            "incoming_referral":record.incoming_referral,
                            "internal_referral":record.internal_referral,
                            "outgoing_referral":record.outgoing_referral,

                            "referred_to":record.referred_to,
                            "dept_name":record.dept_name,
                            "incoming_from":record.incoming_from,

                            "event_date":record.event_date,
                            "pv_violence":record.pv_violence,"sv_violence":record.sv_violence, "ev_violence":record.ev_violence,
                            "ng_violence":record.ng_violence, "fi_service":record.fi_service, "im_service":record.im_service,
                            "c_service":record.c_service, "pep_service":record.pep_service, "sti_service":record.sti_service,
                            "ec_service":record.ec_service, "fp_service":record.fp_service,"p_service":record.p_service,
                            "la_service":record.la_service,"sws_service":record.sws_service,
    user_id:user_id,
    "patient_id":clientInfo.patient_id,"dob":clientInfo.dob,
    "residence_name":clientInfo.residence_name,"gender":clientInfo.gender,
    "client_name":clientInfo.first_name+" "+clientInfo.middle_name+" "+clientInfo.last_name,
    "medical_record_number":clientInfo.medical_record_number,"mobile_number":clientInfo.mobile,
    "facility_id":facility_id};
                        $http.post('/api/SocialWelfareData',records).then(function (data) {

                            $scope.social=data.data;
                            if (data.data.status==1) {
                                swal("",data.data.msg,"success");
                            }
                            else{
                                swal("","Failed","error");
                            }

                            $scope.SocialWelfareDataHistorory();

                        });
                    }



                    $scope.cancel = function () {
                        $mdDialog.hide();

                    };




                },
                templateUrl: '/views/modules/Exemption/gbv_vac.html',
                parent: angular.element(document.body),
                clickOutsideToClose: true,
                fullscreen: false,
            });

                }
        }

        $scope.ipdInvoices = function (item) {

             $mdDialog.show({
                controller: function ($scope) {
                   
                    $scope.cancel = function () {
                        $mdDialog.hide();

                    };
                    $http.get('/api/getUsermenu/'+user_id ).then(function(data) {
            $scope.menu=data.data;
            $scope.cardTitle=data.data[0];
            

        });

        $http.get('/api/getLoginUserDetails/' + user_id).then(function (data) {
            $scope.loginUserFacilityDetails = data.data;  


        });
                      $http.post('/api/ipdInvoices', {
                "patient_id": item,
                "account_id": item,
                "facility_id": facility_id
            }).then(function (data) {
                $scope.invoices = data.data[0];
                $scope.invoiceadm = data.data[1];
                $scope.total = data.data[2][0].total;
                $scope.discount = data.data[3][0].discount;
                $scope.paid = data.data[4][0].paid;
                var report_generated_on = new Date() + "";
                // $scope.department_report_generated_on = report_generated_on.substring(0, 24);
                // $scope.subDepGrandTotal = $scope.subdepTotal();
            });
             $scope.Exemption_finance_detail=function () {
            //location.reload();
            var DocumentContainer = document.getElementById('divtoprint_detail');
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
                },
                templateUrl: '/views/modules/patient_tracing/ipd_bill.html',
                parent: angular.element(document.body),
                clickOutsideToClose: true,
                fullscreen: false,
            });
          
        }

        $scope.getDischargedReport=function(ff){
            $http.post('/api/getDischargedBillReport',{nurse_id:user_id,start_date:ff.start,end_date:ff.end}).then(function(data) {
                $scope.pendingDischarged=data.data;
            });
        }
         $scope.PrintDischarged=function () {

            //location.reload();
            var DocumentContainer = document.getElementById('dischargedid');
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
 $scope.PrintAdmited=function () {

            //location.reload();
            var DocumentContainer = document.getElementById('admitedid');
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