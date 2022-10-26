/**
 * Created by japhari on 02/03/2017.
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
        .controller('radiologyController', radiologyController);
    function radiologyController($http, $auth, $rootScope, $state, $location, $scope, $timeout, $interval, $window, $mdDialog, Helper,toastr) {
        $scope.isNavCollapsed = false;
        $scope.isCollapsedHorizontal = true;
        var user_name = $rootScope.currentUser.id;
        var username = $rootScope.currentUser.name;
        var facility_id = $rootScope.currentUser.facility_id;
        var formdata = new FormData();
        $scope.oneAtATime = true;
        $scope.getTheFiles = function ($files) {
            angular.forEach($files, function (value, key) {
                formdata.append(key, value);
            });
        };
        angular.element(document).ready(function() {
           $scope.patientOrders();
        });
		
		$scope.patientOrders = function(){
			 $http.post('/api/getPatientQueXray', {
                "facility_id": facility_id,
                "user_id": user_name
            }).then(function(data) {
                $scope.patientXray = data.data;
            });
		};
		
        $scope.reportRecord = function (item) {
            var reportData = {
                start:item.start,
                end:item.end};
            $http.post('/api/getPostedResults',reportData).then(function (data) {
                $scope.reportData = data.data;
                console.log(data.data);
            });
        }
        $scope.getPatientQueXrayNotInList = function (text) {
            return Helper.getRadiologyPatients(text,facility_id,user_name)
                .then(function (response) {
                    return response.data;
                });
        };
        var patientOpdPatients = [];
        $scope.showSearch = function(searchKey) {
            $http.post('/api/getAllRadiographics', {
                "searchKey": searchKey,
                "facility_id": facility_id
            }).then(function(data) {
                patientOpdPatients = data.data;
            });
            return patientOpdPatients;
        }
        $http.get('/api/facility_list').then(function (data) {
            $scope.facilities = data.data;
        });

        $scope.getXrays = function () {
            $http.get('/api/getXrayImage').then(function (data) {
                $scope.Xrays = data.data;
            });
        }
        $scope.imageState = function (patient_id) {
            $mdDialog.show({
                controller: function ($scope) {
                    $http.get('/api/imageStatus/' + patient_id).then(function (data) {
                        $scope.patient_orders = data.data;
                        $scope.patientdata = data.data[0];
                    });
                    $scope.cancel = function () {
                        $mdDialog.hide();
                    };
                },
                templateUrl: '/views/modules/radiology/imageverification.html',
                parent: angular.element(document.body),
                clickOutsideToClose: false,
                fullscreen: $scope.customFullscreen
            })
        };
        $scope.getRequestForm = function (patientInfo) {
            if (typeof patientInfo !=='undefined') {
                $mdDialog.show({
                    controller: function ($scope) {
                        $scope.cancel = function () {
                            $mdDialog.hide();
                        };
                        $scope.selectedPatient = patientInfo;
                        $scope.SaveImages = function (explanation, order,item) {
                            if (explanation == undefined) {
                                swal(
                                    username,
                                    'Findings are Missed',
                                    'error'
                                )
                            }
                            else if (explanation == "") {
                                swal(
                                    username,
                                    'Findings are Missed',
                                    'error'
                                )
                            }
                            else {

                                var ImageData = {
                                    'order_id': order,
                                    'item_id': item,
                                    'description': explanation,
                                    'post_user': user_name,
                                    'confirmation_status': 0,
                                    'eraser': 1
                                };
                                swal({
                                    text: "Are you sure you want to send this Findings",
                                    type: 'warning',
                                    showCancelButton: true,
                                    confirmButtonColor: '#3085d6',
                                    cancelButtonColor: '#d33',
                                    confirmButtonText: 'Yes, send it!'
                                }).then(function () {
                                    $http.post('/api/SaveImage', ImageData).then(function (data) {

                                        //console.log(data.data);
                                        swal(
                                            'Findings Sent',
                                            'successfully',
                                            'success'
                                        )

                                    });

                                })
                            }
                        }

                        $scope.xrayImage = function (explanation, orders, mrns) {
                            var id_user = user_name;
                            formdata.append('explanation', explanation.explanation);
                            formdata.append('mrn', mrns);
                            formdata.append('order', orders);
                            formdata.append('post_user', id_user);

                            var request = {
                                method: 'POST',
                                url: '/api/' + 'xrayImage',
                                data: formdata,
                                headers: {
                                    'Content-Type': undefined
                                }
                            };

                            swal({
                                title: username,
                                text: "Are you sure you want to Upload Image",
                                type: 'warning',
                                showCancelButton: true,
                                confirmButtonColor: '#3085d6',
                                cancelButtonColor: '#d33',
                                confirmButtonText: 'Yes, send it!'
                            }).then(function () {
                                $http(request).then(function (data) {
									swal({
										title: 'DIGITAL RADIOGRAPHY',
										text: data.data,
										type: 'success',
										confirmButtonColor: '#3085d6',
										cancelButtonColor: '#d33'
									}).then(function () {
										$scope.cancel();
										$http.post('/api/getPatientQueXray', {"facility_id": facility_id,"user_id": user_name}).then(function(data) {
											$scope.patientXray = data.data;
										});
                                    });
                                })
                            })
                        },
						$scope.cancel = function () {
                            $mdDialog.hide();
                        };
                    },
                    templateUrl: '/views/modules/radiology/radiologyQue.html',
                    parent: angular.element(document.body),
                    clickOutsideToClose: false,
                    fullscreen: $scope.customFullscreen
                })
            }
        };
        $scope.getRadiologyModal = function (patientInfo) {
                
            if (typeof patientInfo !=='undefined') {
                $mdDialog.show({
                    controller: function ($scope) {
                        $scope.doctorRequest = patientInfo;
                        console.log(patientInfo);
                        var patient_id = patientInfo.patient_id;
                        var date_attended = patientInfo.visited_date;
                        console.log(patient_id);                       

                        $scope.radiologyFindings = function (id,mrn,visit_date) {
                            $http.post('/api/doctorRequest', {
                                "patient_id": id,
                                "date_attended": visit_date
                            }).then(function(data) {
                                $scope.post = data.data;
                                $scope.post1 = data.data[0];
                                $scope.post2 = data.data[1];
                                $scope.post3 = data.data[2];
                                $scope.post4 = data.data[3];
                                $scope.post5 = data.data[4];
                                $scope.post6 = data.data[5];
                                
                            });
                            $http.post('/api/doctorRequest', {
                                "patient_id": id,
                                "date_attended": visit_date
                            }).then(function(data) {
                                $scope.findingsCheck = data.data[0];
                            });
                              $http.post('/api/doctorRequest', {
                                "patient_id": id,
                                "date_attended": visit_date

                            }).then(function(data) {
                                $scope.findings = data.data;
                                console.log(data.data);
                            });
                        }
                        $scope.regex=/\s/g;
                        $scope.FindingsRegister = function(findings) {
                            var order_id = findings.OrderId
                            formdata.append('findings', findings);
                            console.log(formdata);
                            var order_id = findings.OrderId;
                            console.log(findings);
                            var FindingData = [];
                            console.log(FindingData);
                            var field_id;
                            $scope.findings.forEach(function (findings) {
                                field_id = findings.OrderId.replace($scope.regex, '_');
                                if ($('#' + field_id).val() != '') {
                                    FindingData.push({
                                        'order_id':findings.OrderId,
                                        'description': $('#' + field_id).val(),
                                        'post_user':user_name,
                                        'confirmation_status':0,
                                        'eraser':1
                                    });
                                    $('#' + field_id).val('');
                                }
                            })
                            if (FindingData.length > 0) {
                                $http.post('/api/FindingsSaveRegister', FindingData).then(function (data) {
                                    var msg = data.data.msg;
                                    var notification = data.data.notification;
                                    var status = data.data.status;
                                    if (status == 0) {
                                        toastr.error(notification, msg);
                                    }
                                    else {
                                        toastr.success(notification, msg);
                                    }
                                });
                            }
                        }
                        $scope.FindingsUploads = function(findings,patient) {
                            console.log(findings);
                            console.log(patient);
                            var UploadingData = [];
                            console.log(UploadingData);
                            var field_id;
                            $scope.regex=/\s/g;
                            $scope.findings.forEach(function (findings) {
                                field_id = findings.OrderId.replace($scope.regex, '_');
                                if ($('#' + field_id).val() != '') {
                                    UploadingData.push({
                                        'order_id':findings.OrderId,
                                        'description': $('#' + field_id).val(),
                                        'post_user':user_name,
                                        'confirmation_status':0,
                                        'eraser':1
                                    });
                                    $('#' + field_id).val('');
                                }
                            })

                        }

                        $scope.verifyPerPatients = function (id) {
                            $http.post('/api/verifyPerPatients', {
                                "patient_id": id

                            }).then(function(data) {
                                $scope.verified = data.data;
                                console.log(data.data);
                            });
                        }
                        $scope.verifyFindingsData = function (verified) {
                            var order_id = verified.order_id;
                            var patient_id = verified.patient_id;
                            console.log(verified);
                            $http.post('/api/verifyPerRequests', {
                                "patient_id": patient_id,
                                "verify_user": user_name,
                                "order_id": order_id

                            }).then(function(data) {
                                $scope.findingsCheck = data.data[0];
                                console.log(data.data);
                            });
                        }
                        $scope.SaveRadiologyFindings = function (explanation, order) {
                            console.log(explanation);
                            console.log(order);
                            if (explanation == undefined) {
                                swal(
                                    username,
                                    'Findings are Missed',
                                    'error'
                                )
                            }
                            else if (explanation == "") {
                                swal(
                                    username,
                                    'Findings are Missed',
                                    'error'
                                )
                            }
                            else {

                                var ImageData = {
                                    'order_id': order,
                                    'description': explanation,
                                    'post_user': user_name,
                                    'confirmation_status': 0,
                                    'eraser': 1
                                };
                                swal({
                                    title: username,
                                    text: "Are you sure you want to send this Findings",
                                    type: 'warning',
                                    showCancelButton: true,
                                    confirmButtonColor: '#3085d6',
                                    cancelButtonColor: '#d33',
                                    confirmButtonText: 'Yes, send it!'
                                }).then(function () {
                                    $http.post('/api/SaveImage', ImageData).then(function (data) {

                                        //console.log(data.data);
                                        swal(
                                            'Registered Successfully',
                                            'Findings sent!',
                                            'success'
                                        )

                                    });

                                })
                            }
                        }
                        $scope.cancel = function () {
                            $mdDialog.hide();
                        
                        };
                        $scope.selectedPatient = patientInfo;
                    },
                    templateUrl: '/views/modules/radiology/radiologyModalQue.html',
                    parent: angular.element(document.body),
                    clickOutsideToClose: false,
                    fullscreen: $scope.customFullscreen
                })
            }
        };
        $scope.getDiagnoses = function (patient_id) {
            $http.get('/api/getdiagnosis/' + patient_id).then(function (data) {
                $scope.diagnosed = data.data;

            });
        }
        $scope.loadPatientRequest = function (request) {
            $scope.selectedPatients = request;
            //console.log(request)
        }
        $scope.imageStatus = function (patient_id) {
            $http.get('/api/imageStatus/' + patient_id).then(function (data) {
                $scope.patient_orders = data.data;
            });
        };
        $scope.VerifyXray = function (id, remarks, name, mobile, middle, last) {
            console.log(id)
            var verify_user = user_name;
            var VerifyXrays = {
                'remarks': remarks,
                'first_name': name,
                'middle_name': middle,
                'last_name': last,
                'mobile_number': mobile,
                'verify_user': verify_user,
                'id': id
            };
            $http.post('/api/VerifyXrays', VerifyXrays).then(function (data) {
                swal(
                    'Updated Successfully',
                    'Image  Verified!',
                    'success'
                )
                $scope.getXrays();
            });
        };
        $scope.DeleteXray = function (id) {
            var del_user = user_name;
            var DeleteXray = {
                'verify_user': del_user,
                'id': id
            };
            $http.post('/api/DeleteXray', DeleteXray).then(function (data) {
                swal(
                    'Deleted Successfully',
                    'Findings Removed',
                    'success'
                )
                $scope.getXrays();
            });
        };
        $scope.PatientsXray = function () {
            $http.get('/api/PatientsXray').then(function (data) {
                $scope.gpatientsXarayss = data.data;

            });
        };
        var patients = [];
        $scope.SearchPatientInXray = function ($value) {

            var SearchKey = {
                'searchKey': $value,
                'user_id': user_name,
                'facility_id': facility_id
            };
            if ($value.length > 3) {
                $http.post('/api/SearchPatientInXray', SearchKey).then(function (data) {

                    patients = data.data;
                });
            }
            return patients;
        }
        $scope.loadPatientRequest1 = function (patient_id) {
            var patientRequest = {
                'patient_id': patient_id,
                'facility_id': facility_id
            };
            $http.post('/api/loadPatientRadiologyRequest', patientRequest).then(function (data) {
                $scope.requestPatient = data.data;
            });
        }
        // $http.get('/api/getradiologypatients').then(function (data) {
        //     $scope.radiologyList = data.data;

        // });
        $scope.SaveImages = function (explanation, order,item) {
            if (explanation == undefined) {
                swal(
                    username,
                    'Findings are Missed',
                    'error'
                )
            }
            else if (explanation == "") {
                swal(
                    username,
                    'Findings are Missed',
                    'error'
                )
            }
            else {

                var ImageData = {
                    'order_id': order,
                    'item_id': item,
                    'description': explanation,
                    'post_user': user_name,
                    'confirmation_status': 0,
                    'eraser': 1
                };
                swal({
                    text: "Are you sure you want to send this Findings",
                    type: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#3085d6',
                    cancelButtonColor: '#d33',
                    confirmButtonText: 'Yes, send it!'
                }).then(function () {
                    $http.post('/api/SaveImage', ImageData).then(function (data) {

                        //console.log(data.data);
                        swal(
                            'Registered Successfully',
                            'Findings sent!',
                            'success'
                        )

                    });

                })
            }
        }
        $scope.lauchDescription = function (description) {
            $mdDialog.show({
                controller: function ($scope) {
                    $scope.cancel = function () {
                        $mdDialog.hide();
                    };

                    $scope.patientLoaded = description;
                    $scope.finderUser =username;
                    $scope.PrintResults=function () {
                        //location.reload();
                        var DocumentContainer = document.getElementById('divtoprint1');
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
                templateUrl: '/views/modules/radiology/description.html',
                parent: angular.element(document.body),
                clickOutsideToClose: true,
                fullscreen: $scope.customFullscreen
            })
        };
        $scope.xrayImage = function (explanation, orders, mrns,item_id) {
            var id_user = user_name;
            formdata.append('explanation', explanation.explanation);
            formdata.append('patient_id', mrns);
            formdata.append('order', orders);
            formdata.append('item_id', item_id);
            formdata.append('post_user', id_user);
            var request = {
                method: 'POST',
                url: '/api/' + 'xrayImage',
                data: formdata,
                headers: {
                    'Content-Type': undefined
                }
            };
            swal({
                title: username,
                text: "Are you sure you want to Upload Image",
                type: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Yes, send it!'
            }).then(function () {
                // SEND THE FILES.
                $http(request).then(function (data) {
                    swal({
						title: 'DIGITAL RADIOGRAPHY',
						text: data.data,
						type: 'success',
						confirmButtonColor: '#3085d6',
						cancelButtonColor: '#d33'
					}).then(function () {
						$scope.cancel();
						$http.post('/api/getPatientQueXray', {"facility_id": facility_id,"user_id": user_name}).then(function(data) {
							$scope.patientXray = data.data;
						});
					});
                })
            })
        }
        String.prototype.trunc = String.prototype.trunc ||
            function(n){
                return this.length>n ? this.substr(0,n-1)+'...' : this.toString();
            };


        $scope.getDetailedReportsdepartmentally = function (item) {
            $http.post('/api/getDetailedReportsdepartmentally', {
                "start": item.start,
                "end": item.end,
                "dept_id": 3,
                "facility_id": facility_id
            }).then(function (data) {
                $scope.cashdetailedData = data.data[0];
                $scope.insurancedetailedData = data.data[1];
                $scope.exemptiondetailedData = data.data[2];

                $scope.cashdetailedTotal = $scope.cashsum();
                $scope.exemptiondetailedTotal = $scope.exemptionsum();
                $scope.insurancedetailedTotal = $scope.insurancesum();

                $scope.selData = function (d, idx) {
                    $scope.selectedData = d;
                    $scope.selIdx = idx;
                };
                var report_generated_on = new Date() + "";
                $scope.report_generated_on = report_generated_on.substring(0, 24);


                $scope.isSelData = function (d) {
                    return $scope.selectedData === d;
                }
            });
        };
        $scope.cashsum = function () {
            var total = 0;
            for (var i = 0; i < $scope.cashdetailedData.length; i++) {
                total -= -($scope.cashdetailedData[i].sub_total);
            }
            return total;
        }
        $scope.insurancesum = function () {
            var total = 0;
            for (var i = 0; i < $scope.insurancedetailedData.length; i++) {
                total -= -($scope.insurancedetailedData[i].sub_total);
            }
            return total;
        }
        $scope.exemptionsum = function () {
            var total = 0;
            for (var i = 0; i < $scope.exemptiondetailedData.length; i++) {
                total -= -($scope.exemptiondetailedData[i].sub_total);
            }
            return total;
        }


        $scope.pharmacashprint=function () {

            //location.reload();
            var DocumentContainer = document.getElementById('pharmcash_id');
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