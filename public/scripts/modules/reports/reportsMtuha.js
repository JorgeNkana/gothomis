(function () {

    'use strict';

    var app = angular.module('authApp');

    app.controller('reportsMtuha',

        ['$filter', '$scope', '$http', '$rootScope', '$uibModal', '$mdDialog',
            function ($filter, $scope, $http, $rootScope, $uibModal, $mdDialog, Helper) {

                var facility_id = $rootScope.currentUser.facility_id;
                var user_id = $rootScope.currentUser.id;
                $scope.cancel = function () {
                    $mdDialog.hide();
                };

				$scope.getReportBasedOnthisDate = function (dt_start, dt_end) {
                    var reportsOPD = {
                        "facility_id": facility_id,
                        "start_date": dt_start,
                        "end_date": dt_end
                    };
                    $scope.cancel = function () {
                        $mdDialog.hide();
                    };
                    $http.post('/api/getDoctorsPerfomaces', reportsOPD).then(function (data) {

                        $scope.DoctorsPerfomaces = data.data;


                    });
                    $http.get('/api/getLoginUserDetails/' + user_id).then(function (data) {
                        $scope.loginUserFacilityDetails = data.data
                    });
                };
				
                $scope.getNHIFdashboard = function () {
                    var reportsDrugs = {
                        "facility_id": facility_id,
                        "start_date": '2017-01-01',
                        "end_date": '2017-07-07'
                    };

                    $http.post('/api/reportsDrugs', reportsDrugs).then(function (data) {

                        var drugsOutOfStock = data.data[0];
                        var object = {'drugsOutOfStock': drugsOutOfStock};
                        var modalInstance = $uibModal.open({
                            templateUrl: '/views/modules/insurance/insurance.html',
                            size: 'lg',
                            animation: true,
                            controller: 'insuranceController',
                            windowClass: 'app-modal-window',
                            resolve: {
                                object: function () {
                                    return object;
                                }
                            }
                        });
                    });
                }




	$scope.showBookTopTen = function () {
					var reportsLegder = {"facility_id": facility_id, start_date: undefined, end_date: undefined};

                    $mdDialog.show({
                        controller: function ($scope, $rootScope, Helper) {
                            $scope.cancel =  function() {
                              $mdDialog.hide();
                            };
                            
							$http.get('/api/getLoginUserDetails/' + user_id).then(function (data) {
                                $scope.loginUserFacilityDetails = data.data
                            });
							
							$scope.start_date = Helper.reportDefaultDates().start_date;	
							$scope.end_date = Helper.reportDefaultDates().end_date;
							
							$scope.getReportBasedOnthisDate=function (dt_start,dt_end) {
								var reportsLegder={"facility_id":facility_id,"start_date":dt_start ? $filter('date')(dt_start,'yyyy-MM-dd') : dt_start,"end_date":dt_end ? $filter('date')(dt_end,'yyyy-MM-dd') : dt_end};
								if(dt_start != undefined && dt_end != undefined){
									$scope.start_date = dt_start;
									$scope.end_date = dt_end;
								}
                                var Report = {attempt:0, load: function(){
									Report.attempt++;
									Helper.overlay(true);
									$http.post('/api/showBookTopTen',reportsLegder).then(function(data) {
										Helper.overlay(false);
										$scope.toptens = data.data;
									}, function(data){Helper.overlay(false);if(Report.attempt < 5) Report.load();});
								}}
								
								Report.load();
                            }

							$scope.print = function(){
								Helper.printHTML($('.to-print').html(),facility_id);
							}
							
							$scope.setParameters = function(report){
								Helper.setParameters({book_name:report, facility_id:facility_id});
							}
							
							$scope.startup = function(){
								$scope.getReportBasedOnthisDate(undefined, undefined);
							}
							
							$scope.startup();

                        },
                        templateUrl: '/views/modules/reports/top-ten-disease.html',
                        parent: angular.element(document.body),
                        clickOutsideToClose: false,
                        fullscreen: true,
                    });
                };
$scope.showBookFivenmcp = function () {

                    var reportsOPD = {"facility_id": facility_id, start_date: undefined, end_date: undefined};

                    $mdDialog.show({
                        controller: function ($scope, $rootScope, Helper) {
                            $scope.cancel =  function() {
                              $mdDialog.hide();
                            };

							$http.get('/api/getLoginUserDetails/' + user_id).then(function (data) {
                                $scope.loginUserFacilityDetails = data.data
                            });

							$scope.start_date = Helper.reportDefaultDates().start_date;
							$scope.end_date = Helper.reportDefaultDates().end_date;

							$scope.getReportBasedOnthisDate=function (dt_start,dt_end) {
								var reportsOPD={"facility_id":facility_id,"start_date":dt_start ? $filter('date')(dt_start,'yyyy-MM-dd') : dt_start,"end_date":dt_end ? $filter('date')(dt_end,'yyyy-MM-dd') : dt_end};
								if(dt_start != undefined && dt_end != undefined){
									$scope.start_date = dt_start;
									$scope.end_date = dt_end;
								}
                                var Report = {attempt:0, load: function(){
									Report.attempt++;
									Helper.overlay(true);
									$http.post('/api/dispensed_item_range_group',reportsOPD).then(function(data) {
										Helper.overlay(false);
										$scope.dispensed_items_groups = data.data;
									}, function(data){Helper.overlay(false);if(Report.attempt < 5) Report.load();});
								}}

								Report.load();
                            }

							$scope.print = function(){
								Helper.printHTML($('.to-print').html(),facility_id);
							}

							$scope.setParameters = function(report){
								Helper.setParameters({book_name:report, facility_id:facility_id});
							}

							$scope.startup = function(){
								$scope.getReportBasedOnthisDate(undefined, undefined);
							}

							$scope.startup();

                        },
                        templateUrl: '/views/modules/reports/nmcpDispensing.html',
                        parent: angular.element(document.body),
                        clickOutsideToClose: false,
                        fullscreen: $scope.customFullscreen // Only for -xs, -sm breakpoints.
                    });
                };

                $scope.showDrugsOutOfStock = function () {
                    var reportsDrugs = {
                        "facility_id": facility_id,
                        "start_date": '2017-01-01',
                        "end_date": '2017-07-07'
                    };

                    $http.post('/api/reportsDrugs', reportsDrugs).then(function (data) {

                        var drugsOutOfStock = data.data[0];
                        var object = {'drugsOutOfStock': drugsOutOfStock};
                        var modalInstance = $uibModal.open({
                            templateUrl: '/views/modules/reports/drugs.html',
                            size: 'lg',
                            animation: true,
                            controller: 'drugsMtuhaController',
                            windowClass: 'app-modal-window',
                            resolve: {
                                object: function () {
                                    return object;
                                }
                            }
                        });
                    });
                }


                $scope.showTestsOutOfStock = function () {
                    var reportsTests = {
                        "facility_id": facility_id,
                        "start_date": '2017-01-01',
                        "end_date": '2017-07-01'
                    };

                    $http.post('/api/reportsUnavailableTests', reportsTests).then(function (data) {

                        var testsOutOfStock = data.data[0];
                        var object = {'testsOutOfStock': testsOutOfStock};
                        var modalInstance = $uibModal.open({
                            templateUrl: '/views/modules/reports/lab_tests.html',
                            size: 'lg',
                            animation: true,
                            controller: 'labMtuhaController',
                            windowClass: 'app-modal-window',
                            resolve: {
                                object: function () {
                                    return object;
                                }
                            }
                        });
                    });
                }


				$scope.showBookFour = function () {
					var reportsLegder = {"facility_id": facility_id, start_date: undefined, end_date: undefined};

                    $mdDialog.show({
                        controller: function ($scope, $rootScope, Helper) {
                            $scope.cancel =  function() {
                              $mdDialog.hide();
                            };
                            
							$http.get('/api/getLoginUserDetails/' + user_id).then(function (data) {
                                $scope.loginUserFacilityDetails = data.data
                            });
							
							$scope.start_date = Helper.reportDefaultDates().start_date;	
							$scope.end_date = Helper.reportDefaultDates().end_date;
							
							$scope.getReportBasedOnthisDate=function (dt_start,dt_end) {
								var reportsLegder={"facility_id":facility_id,"start_date":dt_start ? $filter('date')(dt_start,'yyyy-MM-dd') : dt_start,"end_date":dt_end ? $filter('date')(dt_end,'yyyy-MM-dd') : dt_end};
								if(dt_start != undefined && dt_end != undefined){
									$scope.start_date = dt_start;
									$scope.end_date = dt_end;
								}
                                var Report = {attempt:0, load: function(){
									Report.attempt++;
									Helper.overlay(true);
									$http.post('/api/ledger',reportsLegder).then(function(data) {
										Helper.overlay(false);
										$scope.ledgers = data.data;
									}, function(data){Helper.overlay(false);if(Report.attempt < 5) Report.load();});
								}}
								
								Report.load();
                            }

							$scope.print = function(){
								Helper.printHTML($('.to-print').html(),facility_id);
							}
							
							$scope.setParameters = function(report){
								Helper.setParameters({book_name:report, facility_id:facility_id});
							}
							
							$scope.startup = function(){
								$scope.getReportBasedOnthisDate(undefined, undefined);
							}
							
							$scope.startup();

                        },
                        templateUrl: '/views/modules/reports/Ledger.html',
                        parent: angular.element(document.body),
                        clickOutsideToClose: false,
                        fullscreen: true,
                    });
                };

                $scope.showBookFive = function () {

                    var reportsOPD = {"facility_id": facility_id, start_date: undefined, end_date: undefined};

                    $mdDialog.show({
                        controller: function ($scope, $rootScope, Helper) {
                            $scope.cancel =  function() {
                              $mdDialog.hide();
                            };
                            
							$http.get('/api/getLoginUserDetails/' + user_id).then(function (data) {
                                $scope.loginUserFacilityDetails = data.data
                            });
							
							$scope.start_date = Helper.reportDefaultDates().start_date;
							$scope.end_date = Helper.reportDefaultDates().end_date;
							
							$scope.getReportBasedOnthisDate=function (dt_start,dt_end) {
								var reportsOPD={"facility_id":facility_id,"start_date":dt_start ? $filter('date')(dt_start,'yyyy-MM-dd') : dt_start,"end_date":dt_end ? $filter('date')(dt_end,'yyyy-MM-dd') : dt_end};
								if(dt_start != undefined && dt_end != undefined){
									$scope.start_date = dt_start;
									$scope.end_date = dt_end;
								}
                                var Report = {attempt:0, load: function(){
									Report.attempt++;
									Helper.overlay(true);
									$http.post('/api/getMahudhurioOPD',reportsOPD).then(function(data) {
										Helper.overlay(false);
										$scope.opd_report = data.data;
									}, function(data){Helper.overlay(false);if(Report.attempt < 5) Report.load();});
								}}
								
								Report.load();
                            }

							$scope.print = function(){
								Helper.printHTML($('.to-print').html(),facility_id);
							}
							
							$scope.setParameters = function(report){
								Helper.setParameters({book_name:report, facility_id:facility_id});
							}
							
							$scope.startup = function(){
								$scope.getReportBasedOnthisDate(undefined, undefined);
							}
							
							$scope.startup();

                        },
                        templateUrl: '/views/modules/reports/opd.html',
                        parent: angular.element(document.body),
                        clickOutsideToClose: false,
                        fullscreen: $scope.customFullscreen // Only for -xs, -sm breakpoints.
                    });
                };


                $scope.showDoctorsPerfomances = function () {
					var reportsANC = {"facility_id": facility_id, start_date: undefined, end_date: undefined};

                    $mdDialog.show({
                        controller: function ($scope, $rootScope, Helper) {
                            $scope.cancel =  function() {
                              $mdDialog.hide();
                            };
                            
							$http.get('/api/getLoginUserDetails/' + user_id).then(function (data) {
                                $scope.loginUserFacilityDetails = data.data
                            });
							
							$scope.start_date = Helper.reportDefaultDates().start_date;
							$scope.end_date = Helper.reportDefaultDates().end_date;
							
							$scope.getReportBasedOnthisDate=function (dt_start,dt_end) {
								var reportsANC={"facility_id":facility_id,"start_date":dt_start ? $filter('date')(dt_start,'yyyy-MM-dd') : dt_start,"end_date":dt_end ? $filter('date')(dt_end,'yyyy-MM-dd') : dt_end};
								if(dt_start != undefined && dt_end != undefined){
									$scope.start_date = dt_start;
									$scope.end_date = dt_end;
								}
                                var Report = {attempt:0, load: function(){
									Report.attempt++;
									Helper.overlay(true);
									$http.post('/api/getDoctorsPerfomaces',reportsANC).then(function(data) {
										Helper.overlay(false);
										$scope.antinatal = data.data;
									}, function(data){Helper.overlay(false);if(Report.attempt < 5) Report.load();});
								}}
								
								Report.load();
                            }

							$scope.print = function(){
								Helper.printHTML($('.to-print').html(),facility_id);
							}
							
							$scope.setParameters = function(report){
								Helper.setParameters({book_name:report, facility_id:facility_id});
							}
							
							$scope.startup = function(){
								$scope.getReportBasedOnthisDate(undefined, undefined);
							}
							
							$scope.startup();

                        },
                        templateUrl: '/views/modules/reports/doctorsPerfomaces.html',
                        parent: angular.element(document.body),
                        clickOutsideToClose: false,
                        fullscreen: $scope.customFullscreen // Only for -xs, -sm breakpoints.
                    });

                };


                $scope.showDrugsExpired = function () {

                    $http.get('/api/expired/' + facility_id).then(function (data) {

                        var drugsExpired = data.data;
                        var object = {'drugsExpired': drugsExpired};
                        var modalInstance = $uibModal.open({
                            templateUrl: '/views/modules/reports/expiredDrugs.html',
                            size: 'lg',
                            animation: true,
                            controller: 'expiredDrugsMtuhaController',
                            windowClass: 'app-modal-window',
                            resolve: {
                                object: function () {
                                    return object;
                                }
                            }
                        });
                    });
                }

				$scope.showBookFourteen = function () {

                    var reportsIPD = {"facility_id": facility_id};

                    $mdDialog.show({
                        controller: function ($scope, $rootScope, Helper) {
                            $scope.cancel =  function() {
                              $mdDialog.hide();
                            };
                            $http.get('/api/getLoginUserDetails/'+user_id ).then(function(data) {
                                $scope.loginUserFacilityDetails=data.data;
                            });
							
							$scope.start_date = Helper.reportDefaultDates().start_date;
							$scope.end_date = Helper.reportDefaultDates().end_date;
							
							$scope.getReportBasedOnthisDate=function (dt_start,dt_end) {
                                var reportsIPD={"facility_id":facility_id,"start_date":dt_start ? $filter('date')(dt_start,'yyyy-MM-dd') : dt_start,"end_date":dt_end ? $filter('date')(dt_end,'yyyy-MM-dd') : dt_end};
								if(dt_start != undefined && dt_end != undefined){
									$scope.start_date = dt_start;
									$scope.end_date = dt_end;
								}
                                
                                var Report = {attempt:0, load: function(){
									Report.attempt++;
									Helper.overlay(true);
									$http.post('/api/getIpdReport',reportsIPD).then(function(data) {
										Helper.overlay(false);
										$scope.ipd_report = data.data;
									}, function(data){Helper.overlay(false);if(Report.attempt < 5) Report.load();});
								}};
								
								Report.load();
                            }
							
							$scope.setParameters = function(report){
								Helper.setParameters({book_name:report, facility_id:facility_id});
							}

							$scope.startup = function(){
									$scope.getReportBasedOnthisDate(undefined, undefined);
								}
								
							$scope.startup();
								
							$scope.print = function(){
								Helper.printHTML($('.to-print').html(),facility_id);
							}

                        },
                        templateUrl: '/views/modules/reports/ipd.html',
                        parent: angular.element(document.body),
                        clickOutsideToClose: false,
                        fullscreen: $scope.customFullscreen // Only for -xs, -sm breakpoints.
                    });
                };

                //Dental and Eye mtuha reports start
				$scope.showBookEleven = function () {
                    
                    var reportsIPD = {"facility_id": facility_id};

                    $mdDialog.show({
                        controller: function ($scope, $rootScope, Helper) {
                            $scope.cancel =  function() {
                              $mdDialog.hide();
                            };
                            $http.get('/api/getLoginUserDetails/'+user_id ).then(function(data) {
                                $scope.loginUserFacilityDetails=data.data;
                            });
							
							$scope.start_date = Helper.reportDefaultDates().start_date;
							$scope.end_date = Helper.reportDefaultDates().end_date;
							
							$scope.getReportBasedOnthisDate=function (dt_start,dt_end) {
                                var reportsIPD={"facility_id":facility_id,"start_date":dt_start ? $filter('date')(dt_start,'yyyy-MM-dd') : dt_start,"end_date":dt_end ? $filter('date')(dt_end,'yyyy-MM-dd') : dt_end};
								if(dt_start != undefined && dt_end != undefined){
									$scope.start_date = dt_start;
									$scope.end_date = dt_end;
								}
                                
                                var Report = {attempt:0, load: function(){
									Report.attempt++;
									Helper.overlay(true);
									$http.post('/api/mtuhaDentalReports',reportsIPD).then(function(data) {
										Helper.overlay(false);
										$scope.dentalReports = data.data;
									}, function(data){Helper.overlay(false);if(Report.attempt < 5) Report.load();});
								}};
								
								Report.load();
                            }
							
							$scope.setParameters = function(report){
								Helper.setParameters({book_name:report, facility_id:facility_id});
							}
							
							$scope.startup = function(){
									$scope.getReportBasedOnthisDate(undefined, undefined);
								}
								
							$scope.startup();
								
							$scope.print = function(){
								Helper.printHTML($('.to-print').html(),facility_id);
							}

                        },
                        templateUrl: '/views/modules/reports/dental.html',
                        parent: angular.element(document.body),
                        clickOutsideToClose: false,
                        fullscreen: $scope.customFullscreen // Only for -xs, -sm breakpoints.
                    });
                }


                $scope.showBookSixteen = function () {
                    
                    var reportsIPD = {"facility_id": facility_id};

                    $mdDialog.show({
                        controller: function ($scope, $rootScope, Helper) {
                            $scope.cancel =  function() {
                              $mdDialog.hide();
                            };
                            $http.get('/api/getLoginUserDetails/'+user_id ).then(function(data) {
                                $scope.loginUserFacilityDetails=data.data;
                            });
							
							$scope.start_date = Helper.reportDefaultDates().start_date;
							$scope.end_date = Helper.reportDefaultDates().end_date;
							
							$scope.getReportBasedOnthisDate=function (dt_start,dt_end) {
                                var reportsIPD={"facility_id":facility_id,"start_date":dt_start ? $filter('date')(dt_start,'yyyy-MM-dd') : dt_start,"end_date":dt_end ? $filter('date')(dt_end,'yyyy-MM-dd') : dt_end};
								if(dt_start != undefined && dt_end != undefined){
									$scope.start_date = dt_start;
									$scope.end_date = dt_end;
								}
                                
                                var Report = {attempt:0, load: function(){
									Report.attempt++;
									Helper.overlay(true);
									$http.post('/api/mtuhaEyeReports',reportsIPD).then(function(data) {
										Helper.overlay(false);
										$scope.eyeReports = data.data;
									}, function(data){Helper.overlay(false);if(Report.attempt < 5) Report.load();});
								}};
								
								Report.load();
                            }
							
							$scope.setParameters = function(report){
								Helper.setParameters({book_name:report, facility_id:facility_id});
							}
							
							$scope.startup = function(){
									$scope.getReportBasedOnthisDate(undefined, undefined);
								}
								
							$scope.startup();
								
							$scope.print = function(){
								Helper.printHTML($('.to-print').html(),facility_id);
							}

                        },
                        templateUrl: '/views/modules/reports/eye.html',
                        parent: angular.element(document.body),
                        clickOutsideToClose: false,
                        fullscreen: $scope.customFullscreen // Only for -xs, -sm breakpoints.
                    });
                }

		
		

				$scope.showBookSix = function () {
					var reportsANC = {"facility_id": facility_id, start_date: undefined, end_date: undefined};

                    $mdDialog.show({
                        controller: function ($scope, $rootScope, Helper) {
                            $scope.cancel =  function() {
                              $mdDialog.hide();
                            };
                            
							$http.get('/api/getLoginUserDetails/' + user_id).then(function (data) {
                                $scope.loginUserFacilityDetails = data.data
                            });
							
							$scope.start_date = Helper.reportDefaultDates().start_date;
							$scope.end_date = Helper.reportDefaultDates().end_date;
							
							$scope.getReportBasedOnthisDate=function (dt_start,dt_end) {
								var reportsANC={"facility_id":facility_id,"start_date":dt_start ? $filter('date')(dt_start,'yyyy-MM-dd') : dt_start,"end_date":dt_end ? $filter('date')(dt_end,'yyyy-MM-dd') : dt_end};
								if(dt_start != undefined && dt_end != undefined){
									$scope.start_date = dt_start;
									$scope.end_date = dt_end;
								}
                                var Report = {attempt:0, load: function(){
									Report.attempt++;
									Helper.overlay(true);
									$http.post('/api/Anti_natl_mtuha',reportsANC).then(function(data) {
										Helper.overlay(false);
										$scope.antinatal = data.data;
									}, function(data){Helper.overlay(false);if(Report.attempt < 5) Report.load();});
								}}
								
								Report.load();
                            }

							$scope.print = function(){
								Helper.printHTML($('.to-print').html(),facility_id);
							}
							
							$scope.setParameters = function(report){
								Helper.setParameters({book_name:report, facility_id:facility_id});
							}
							
							$scope.startup = function(){
								$scope.getReportBasedOnthisDate(undefined, undefined);
							}
							
							$scope.startup();

                        },
                        templateUrl: '/views/modules/reports/anti_natal.html',
                        parent: angular.element(document.body),
                        clickOutsideToClose: false,
                        fullscreen: true,
                    });
                };
				
                $scope.showBookSeven = function () {
						var reportsANC = {"facility_id": facility_id, start_date: undefined, end_date: undefined};

                    $mdDialog.show({
                        controller: function ($scope, $rootScope, Helper) {
                            $scope.cancel =  function() {
                              $mdDialog.hide();
                            };
                            
							$http.get('/api/getLoginUserDetails/' + user_id).then(function (data) {
                                $scope.loginUserFacilityDetails = data.data
                            });
							
							$scope.start_date = Helper.reportDefaultDates().start_date;
							$scope.end_date = Helper.reportDefaultDates().end_date;
							
							$scope.getReportBasedOnthisDate=function (dt_start,dt_end) {
								var reportsANC={"facility_id":facility_id,"start_date":dt_start ? $filter('date')(dt_start,'yyyy-MM-dd') : dt_start,"end_date":dt_end ? $filter('date')(dt_end,'yyyy-MM-dd') : dt_end};
								if(dt_start != undefined && dt_end != undefined){
									$scope.start_date = dt_start;
									$scope.end_date = dt_end;
								}
                                var Report = {attempt:0, load: function(){
									Report.attempt++;
									Helper.overlay(true);
									$http.post('/api/getChilddewormgivenReport',reportsANC).then(function(data) {
										Helper.overlay(false);
										$scope.deworms = data.data;
									}, function(data){Helper.overlay(false);if(Report.attempt < 5) Report.load();});
								}}
								
								Report.load();
                            }

							$scope.print = function(){
								Helper.printHTML($('.to-print').html(),facility_id);
							}
							
							$scope.setParameters = function(report){
								Helper.setParameters({book_name:report, facility_id:facility_id});
							}
							
							$scope.startup = function(){
								$scope.getReportBasedOnthisDate(undefined, undefined);
							}
							
							$scope.startup();

                        },
                        templateUrl: '/views/modules/reports/child.html',
                        parent: angular.element(document.body),
                        clickOutsideToClose: false,
                        fullscreen: true,
                    });
                };
				
				$scope.showBookEight = function () {
					var reportsANC = {"facility_id": facility_id, start_date: undefined, end_date: undefined};

                    $mdDialog.show({
                        controller: function ($scope, $rootScope, Helper) {
                            $scope.cancel =  function() {
                              $mdDialog.hide();
                            };
                            
							$http.get('/api/getLoginUserDetails/' + user_id).then(function (data) {
                                $scope.loginUserFacilityDetails = data.data
                            });
							
							$scope.start_date = Helper.reportDefaultDates().start_date;
							$scope.end_date = Helper.reportDefaultDates().end_date;
							
							$scope.getReportBasedOnthisDate=function (dt_start,dt_end) {
								var reportsANC={"facility_id":facility_id,"start_date":dt_start ? $filter('date')(dt_start,'yyyy-MM-dd') : dt_start,"end_date":dt_end ? $filter('date')(dt_end,'yyyy-MM-dd') : dt_end};
								if(dt_start != undefined && dt_end != undefined){
									$scope.start_date = dt_start;
									$scope.end_date = dt_end;
								}
                                var Report = {attempt:0, load: function(){
									Report.attempt++;
									Helper.overlay(true);
									$http.post('/api/mtuhaFamily_planning',reportsANC).then(function(data) {
										Helper.overlay(false);
										$scope.antinatal = data.data;
									}, function(data){Helper.overlay(false);if(Report.attempt < 5) Report.load();});
								}}
								
								Report.load();
                            }

							$scope.print = function(){
								Helper.printHTML($('.to-print').html(),facility_id);
							}
							
							$scope.setParameters = function(report){
								Helper.setParameters({book_name:report, facility_id:facility_id});
							}
							
							$scope.startup = function(){
								$scope.getReportBasedOnthisDate(undefined, undefined);
							}
							
							$scope.startup();

                        },
                        templateUrl: '/views/modules/reports/family_planning.html',
                        parent: angular.element(document.body),
                        clickOutsideToClose: false,
                        fullscreen: true,
                    });
                };

				$scope.showBookNine = function () {
					var reportsANC = {"facility_id": facility_id, start_date: undefined, end_date: undefined};

                    $mdDialog.show({
                        controller: function ($scope, $rootScope, Helper) {
                            $scope.cancel =  function() {
                              $mdDialog.hide();
                            };
                            
							$http.get('/api/getLoginUserDetails/' + user_id).then(function (data) {
                                $scope.loginUserFacilityDetails = data.data
                            });
							
							$scope.start_date = Helper.reportDefaultDates().start_date;
							$scope.end_date = Helper.reportDefaultDates().end_date;
							
							$scope.getReportBasedOnthisDate=function (dt_start,dt_end) {
								var reportsANC={"facility_id":facility_id,"start_date":dt_start ? $filter('date')(dt_start,'yyyy-MM-dd') : dt_start,"end_date":dt_end ? $filter('date')(dt_end,'yyyy-MM-dd') : dt_end};
								if(dt_start != undefined && dt_end != undefined){
									$scope.start_date = dt_start;
									$scope.end_date = dt_end;
								}
                                var Report = {attempt:0, load: function(){
									Report.attempt++;
									Helper.overlay(true);
									$http.post('/api/mtuhaDTC',reportsANC).then(function(data) {
										Helper.overlay(false);
										$scope.dct = data.data;
									}, function(data){Helper.overlay(false);if(Report.attempt < 5) Report.load();});
								}}
								
								Report.load();
                            }

							$scope.print = function(){
								Helper.printHTML($('.to-print').html(),facility_id);
							}
							
							$scope.setParameters = function(report){
								Helper.setParameters({book_name:report, facility_id:facility_id});
							}
							
							$scope.startup = function(){
								$scope.getReportBasedOnthisDate(undefined, undefined);
							}
							
							$scope.startup();

                        },
                        templateUrl: '/views/modules/reports/dtc.html',
                        parent: angular.element(document.body),
                        clickOutsideToClose: false,
                        fullscreen: true,
                    });
                };
				$scope.showBookTwelve = function () {
					var reportsANC = {"facility_id": facility_id, start_date: undefined, end_date: undefined};

                    $mdDialog.show({
                        controller: function ($scope, $rootScope, Helper) {
                            $scope.cancel =  function() {
                              $mdDialog.hide();
                            };
                            
							$http.get('/api/getLoginUserDetails/' + user_id).then(function (data) {
                                $scope.loginUserFacilityDetails = data.data
                            });
							
							$scope.start_date = Helper.reportDefaultDates().start_date;
							$scope.end_date = Helper.reportDefaultDates().end_date;
							
							$scope.getReportBasedOnthisDate=function (dt_start,dt_end) {
								var reportsANC={"facility_id":facility_id,"start_date":dt_start ? $filter('date')(dt_start,'yyyy-MM-dd') : dt_start,"end_date":dt_end ? $filter('date')(dt_end,'yyyy-MM-dd') : dt_end};
								if(dt_start != undefined && dt_end != undefined){
									$scope.start_date = dt_start;
									$scope.end_date = dt_end;
								}
                                var Report = {attempt:0, load: function(){
									Report.attempt++;
									Helper.overlay(true);
									$http.post('/api/mtuhaLabour',reportsANC).then(function(data) {
										Helper.overlay(false);
										$scope.antinatal = data.data;
									}, function(data){Helper.overlay(false);if(Report.attempt < 5) Report.load();});
								}}
								
								Report.load();
                            }

							$scope.print = function(){
								Helper.printHTML($('.to-print').html(),facility_id);
							}
							
							$scope.setParameters = function(report){
								Helper.setParameters({book_name:report, facility_id:facility_id});
							}
							
							$scope.startup = function(){
								$scope.getReportBasedOnthisDate(undefined, undefined);
							}
							
							$scope.startup();

                        },
                        templateUrl: '/views/modules/reports/labour.html',
                        parent: angular.element(document.body),
                        clickOutsideToClose: false,
                        fullscreen: true,
                    });
                };
				
				$scope.showBookThirteen = function () {
					var reportsANC = {"facility_id": facility_id, start_date: undefined, end_date: undefined};

                    $mdDialog.show({
                        controller: function ($scope, $rootScope, Helper) {
                            $scope.cancel =  function() {
                              $mdDialog.hide();
                            };
                            
							$http.get('/api/getLoginUserDetails/' + user_id).then(function (data) {
                                $scope.loginUserFacilityDetails = data.data
                            });
							
							$scope.start_date = Helper.reportDefaultDates().start_date;
							$scope.end_date = Helper.reportDefaultDates().end_date;
							
							$scope.getReportBasedOnthisDate=function (dt_start,dt_end) {
								var reportsANC={"facility_id":facility_id,"start_date":dt_start ? $filter('date')(dt_start,'yyyy-MM-dd') : dt_start,"end_date":dt_end ? $filter('date')(dt_end,'yyyy-MM-dd') : dt_end};
								if(dt_start != undefined && dt_end != undefined){
									$scope.start_date = dt_start;
									$scope.end_date = dt_end;
								}
                                var Report = {attempt:0, load: function(){
									Report.attempt++;
									Helper.overlay(true);
									$http.post('/api/mtuhaPost_natal',reportsANC).then(function(data) {
										Helper.overlay(false);
										$scope.postna = data.data;
									}, function(data){Helper.overlay(false);if(Report.attempt < 5) Report.load();});
								}}
								
								Report.load();
                            }

							$scope.print = function(){
								Helper.printHTML($('.to-print').html(),facility_id);
							}
							
							$scope.setParameters = function(report){
								Helper.setParameters({book_name:report, facility_id:facility_id});
							}
							
							$scope.startup = function(){
								$scope.getReportBasedOnthisDate(undefined, undefined);
							}
							
							$scope.startup();

                        },
                        templateUrl: '/views/modules/reports/post_natal.html',
                        parent: angular.element(document.body),
                        clickOutsideToClose: false,
                        fullscreen: true,
                    });
                };
				
				$scope.showBookFifteen = function () {
					var paramters = {"facility_id": facility_id};

                    $mdDialog.show({
                        controller: function ($scope, $rootScope, Helper) {
                            $scope.cancel =  function() {
                              $mdDialog.hide();
                            };
                            $http.get('/api/getLoginUserDetails/'+user_id ).then(function(data) {
                                $scope.loginUserFacilityDetails=data.data;
                            });
							
							$scope.start_date = Helper.reportDefaultDates().start_date;
							$scope.end_date = Helper.reportDefaultDates().end_date;
							
							$scope.getReportBasedOnthisDate=function (dt_start,dt_end) {
                                var paramters={"facility_id":facility_id,"start_date":dt_start ? $filter('date')(dt_start,'yyyy-MM-dd') : dt_start,"end_date":dt_end ? $filter('date')(dt_end,'yyyy-MM-dd') : dt_end};
								if(dt_start != undefined && dt_end != undefined){
									$scope.start_date = dt_start;
									$scope.end_date = dt_end;
								}
                                
                                var Report = {attempt:0, load: function(){
									Report.attempt++;
									Helper.overlay(true);
									$http.post('/api/tracer-medicines-report',paramters).then(function(data) {
										Helper.overlay(false);
										$scope.tracer_items = data.data;
									}, function(data){Helper.overlay(false);if(Report.attempt < 5) Report.load();});
								}};
								
								Report.load();
                            }
							
							
							$scope.setParameters = function(report){
								Helper.setParameters({book_name:report, facility_id:facility_id});
							}
							
							$scope.startup = function(){
									$scope.getReportBasedOnthisDate(undefined, undefined);
								}
								
							$scope.startup();
								
							$scope.print = function(){
								Helper.printHTML($('.to-print').html(),facility_id);
							}

                        },
                        templateUrl: '/views/modules/reports/tracer_medecine.html',
                        parent: angular.element(document.body),
                        clickOutsideToClose: false,
                        fullscreen: true,
                    });
                };
				
				$scope.showBedOccupancy = function () {
					var paramters = {"facility_id": facility_id};

                    $mdDialog.show({
                        controller: function ($scope, $rootScope, Helper) {
                            $scope.cancel =  function() {
                              $mdDialog.hide();
                            };
                            $http.get('/api/getLoginUserDetails/'+user_id ).then(function(data) {
                                $scope.loginUserFacilityDetails=data.data;
                            });
							
							$scope.start_date = Helper.reportDefaultDates().start_date;
							$scope.end_date = Helper.reportDefaultDates().end_date;
							
							$scope.getReportBasedOnthisDate=function (dt_start,dt_end) {
                                var paramters={"facility_id":facility_id,"start_date":dt_start ? $filter('date')(dt_start,'yyyy-MM-dd') : dt_start,"end_date":dt_end ? $filter('date')(dt_end,'yyyy-MM-dd') : dt_end};
								if(dt_start != undefined && dt_end != undefined){
									$scope.start_date = dt_start;
									$scope.end_date = dt_end;
								}
                                
                                var Report = {attempt:0, load: function(){
									Report.attempt++;
									Helper.overlay(true);
									$http.post('/api/bed-occupancy',paramters).then(function(data) {
										Helper.overlay(false);
										$scope.bed_occupancy_report = data.data;
									}, function(data){Helper.overlay(false);if(Report.attempt < 5) Report.load();});
								}};
								
								Report.load();
                            }
							
							$scope.setParameters = function(report){
								Helper.setParameters({book_name:report, facility_id:facility_id});
							}
							
							$scope.startup = function(){
									$scope.getReportBasedOnthisDate(undefined, undefined);
								}
								
							$scope.startup();
								
							$scope.print = function(){
								Helper.printHTML($('.to-print').html(),facility_id);
							}

                        },
                        templateUrl: '/views/modules/reports/bed_occupancy.html',
                        parent: angular.element(document.body),
                        clickOutsideToClose: false,
                        fullscreen: true,
                    });
                };



                //updates start
                $scope.showIsdr = function () {
                    var isdr = {"facility_id": facility_id};

                    $mdDialog.show({
                        controller: function ($scope, $rootScope, Helper) {
                            $scope.cancel =  function() {
                                $mdDialog.hide();
                            };
                            $http.get('/api/getLoginUserDetails/'+user_id ).then(function(data) {
                                $scope.loginUserFacilityDetails=data.data;
                            });

                            $scope.start_date = Helper.reportDefaultDates().start_date;
                            $scope.end_date = Helper.reportDefaultDates().end_date;

                            $scope.getIsdrReportBasedOnthisDate=function (dt_start,dt_end) {
                                var reportsIsdr={"facility_id":facility_id,"start_date":dt_start ? $filter('date')(dt_start,'yyyy-MM-dd') : dt_start,"end_date":dt_end ? $filter('date')(dt_end,'yyyy-MM-dd') : dt_end};
                                if(dt_start != undefined && dt_end != undefined){
                                    $scope.start_date = dt_start;
                                    $scope.end_date = dt_end;
                                }

                                var Report = {attempt:0, load: function(){
                                        Report.attempt++;
                                        Helper.overlay(true);
                                        $http.post('/api/isdr-report',reportsIsdr).then(function(data) {
                                            Helper.overlay(false);
                                            $scope.isdr = data.data;
                                        }, function(data){Helper.overlay(false);if(Report.attempt < 5) Report.load();});
                                    }};

                                Report.load();
                            }

                            $scope.setParameters = function(report){
                                Helper.setParameters({book_name:report, facility_id:facility_id});
                            }

                            $scope.startup = function(){
                                $scope.getIsdrReportBasedOnthisDate(undefined, undefined);
                            }

                            $scope.startup();

                            $scope.print = function(){
                                Helper.printHTML($('.to-print').html(),facility_id);
                            }

                        },
                        templateUrl: '/views/modules/reports/isdr.html',
                        parent: angular.element(document.body),
                        clickOutsideToClose: false,
                        fullscreen: true,
                    });
                };

                $scope.showSti = function () {
                    var isdr = {"facility_id": facility_id};

                    $mdDialog.show({
                        controller: function ($scope, $rootScope, Helper) {
                            $scope.cancel =  function() {
                                $mdDialog.hide();
                            };
                            $http.get('/api/getLoginUserDetails/'+user_id ).then(function(data) {
                                $scope.loginUserFacilityDetails=data.data;
                            });

                            $scope.start_date = Helper.reportDefaultDates().start_date;
                            $scope.end_date = Helper.reportDefaultDates().end_date;

                            $scope.getStiReportBasedOnthisDate=function (dt_start,dt_end) {
                                var reportsSti={"facility_id":facility_id,"start_date":dt_start ? $filter('date')(dt_start,'yyyy-MM-dd') : dt_start,"end_date":dt_end ? $filter('date')(dt_end,'yyyy-MM-dd') : dt_end};
                                if(dt_start != undefined && dt_end != undefined){
                                    $scope.start_date = dt_start;
                                    $scope.end_date = dt_end;
                                }

                                var Report = {attempt:0, load: function(){
                                        Report.attempt++;
                                        Helper.overlay(true);
                                        $http.post('/api/sti-report',reportsSti).then(function(data) {
                                            Helper.overlay(false);
                                            $scope.sti = data.data;
                                            console.log(data);
                                        }, function(data){Helper.overlay(false);if(Report.attempt < 5) Report.load();});
                                    }};

                                Report.load();
                            }

                            $scope.setParameters = function(report){
                                Helper.setParameters({book_name:report, facility_id:facility_id});
                            }

                            $scope.startup = function(){
                                $scope.getStiReportBasedOnthisDate(undefined, undefined);
                            }

                            $scope.startup();

                            $scope.print = function(){
                                Helper.printHTML($('.to-print').html(),facility_id);
                            }

                        },
                        templateUrl: '/views/modules/reports/sti.html',
                        parent: angular.element(document.body),
                        clickOutsideToClose: false,
                        fullscreen: true,
                    });
                };
                $scope.showDeath = function () {
                    var isdr = {"facility_id": facility_id};

                    $mdDialog.show({
                        controller: function ($scope, $rootScope, Helper) {
                            $scope.cancel =  function() {
                                $mdDialog.hide();
                            };
                            $http.get('/api/getLoginUserDetails/'+user_id ).then(function(data) {
                                $scope.loginUserFacilityDetails=data.data;
                            });

                            $scope.start_date = Helper.reportDefaultDates().start_date;
                            $scope.end_date = Helper.reportDefaultDates().end_date;

                            $scope.getDeathReportBasedOnthisDate=function (dt_start,dt_end) {
                                var reportsSti={"facility_id":facility_id,"start_date":dt_start ? $filter('date')(dt_start,'yyyy-MM-dd') : dt_start,"end_date":dt_end ? $filter('date')(dt_end,'yyyy-MM-dd') : dt_end};
                                if(dt_start != undefined && dt_end != undefined){
                                    $scope.start_date = dt_start;
                                    $scope.end_date = dt_end;
                                }

                                var Report = {attempt:0, load: function(){
                                        Report.attempt++;
                                        Helper.overlay(true);
                                        $http.post('/api/death-report',reportsSti).then(function(data) {
                                            Helper.overlay(false);
                                            $scope.deaths = data.data;
                                            console.log(data);
                                        }, function(data){Helper.overlay(false);if(Report.attempt < 5) Report.load();});
                                    }};

                                Report.load();
                            }

                            $scope.setParameters = function(report){
                                Helper.setParameters({book_name:report, facility_id:facility_id});
                            }

                            $scope.startup = function(){
                                $scope.getDeathReportBasedOnthisDate(undefined, undefined);
                            }

                            $scope.startup();

                            $scope.print = function(){
                                Helper.printHTML($('.to-print').html(),facility_id);
                            }

                        },
                        templateUrl: '/views/modules/reports/death.html',
                        parent: angular.element(document.body),
                        clickOutsideToClose: false,
                        fullscreen: true,
                    });
                };
                //updates end
//updates on mtuha 01-january-2018                


                $scope.cancel = function () {
                    //console.log('done and cleared');
                    $uibModalInstance.dismiss();

                }


                $scope.closeAllModals = function () {
                    //console.log('done and cleared');
                    $uibModalInstance.dismissAll();

                }
				
				$scope.restartRegister = function(){
					Helper.overlay(true);
					$http.post('/api/restartRegister', {facility_id: facility_id}).then(function (data) {
						Helper.overlay(false);
						swal('Registeres successfully re-populated','','info');
					}, function(data){Helper.overlay(false);});
				}

 $scope.staff_performance = function () {
                    //location.reload();
                    var DocumentContainer = document.getElementById('staff_perform');
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

                };
				
				
		}]);


}());