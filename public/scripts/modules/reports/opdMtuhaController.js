(function () {

    'use strict';

    var app = angular.module('authApp');

    app.controller('opdMtuhaController',

        ['$filter','$scope','$http','$rootScope','$uibModal', '$uibModalInstance', 'object',
            function ($filter,$scope,$http,$rootScope,$uibModal,$uibModalInstance,object) {

    	var facility_id =$rootScope.currentUser.facility_id;
            var user_id =$rootScope.currentUser.id;

            $scope.opd_mahudhurio=object.attendanceOpd;
            $scope.opd_mahudhurio_new=object.newAttendanceOpd;
            $scope.opd_mahudhurio_marudio=object.opd_mahudhurio_marudio;
            $scope.opd_diagnosises=object.opd_diagnosises;
            
            $scope.getReportBasedOnthisDate=function (dt_start,dt_end) {
                var reportsOPD={"facility_id":facility_id,"start_date":dt_start,"end_date":dt_end};

                $http.post('/api/getMahudhurioOPD',reportsOPD).then(function(data) {

                    var attendanceOpd = data.data[0][0];
                    var newAttendanceOpd = data.data[1][0];
                    var opd_mahudhurio_marudio = data.data[2][0];
                    var opd_diagnosises = data.data[3];
                    var object = {
                        'opd_diagnosises': opd_diagnosises,
                        'attendanceOpd': attendanceOpd,
                        'newAttendanceOpd': newAttendanceOpd,
                        'opd_mahudhurio_marudio': opd_mahudhurio_marudio
                    };
                    $scope.opd_mahudhurio=object.attendanceOpd;
                    $scope.opd_mahudhurio_new=object.newAttendanceOpd;
                    $scope.opd_mahudhurio_marudio=object.opd_mahudhurio_marudio;
                    $scope.opd_diagnosises=object.opd_diagnosises;
                    $scope.start_date=dt_start;
                    $scope.end_date=dt_end;


                });
                }

			$scope.cancel=function (){
				//console.log('done and cleared');
			$uibModalInstance.dismiss();

			}

                $http.get('/api/getLoginUserDetails/'+user_id ).then(function(data) {
                    $scope.loginUserFacilityDetails=data.data;

                    //console.log($scope.loginUserFacilityDetails);

                });

                $scope.closeAllModals=function (){
				//console.log('done and cleared');
			$uibModalInstance.dismissAll();
			
			}


                $scope.today = function() {
                    $scope.dt_start = new Date();
                    $scope.dt_end = new Date();
                };
                //$scope.today();

                $scope.clear = function() {
                    $scope.dt_start = null;
                    $scope.dt_end = null;
                };

                $scope.inlineOptions = {
                    customClass: getDayClass,
                    minDate: new Date(),
                    showWeeks: true
                };

                $scope.dateOptions = {
                    dateDisabled: disabled,
                    formatYear: 'yy',
                    maxDate: new Date(2020, 5, 22),
                    minDate: new Date(),
                    startingDay: 1
                };

                // Disable weekend selection
                function disabled(data) {
                    var date = data.date,
                        mode = data.mode;
                    return mode === 'day' && (date.getDay() === 0 || date.getDay() === 6);
                }

                $scope.toggleMin = function() {
                    $scope.inlineOptions.minDate = $scope.inlineOptions.minDate ? null : new Date();
                    $scope.dateOptions.minDate = $scope.inlineOptions.minDate;
                };

                $scope.toggleMin();

                $scope.open1 = function() {
                    $scope.popup1.opened = true;
                };

                $scope.open2 = function() {
                    $scope.popup2.opened = true;
                };

                $scope.setDate = function(year, month, day) {
                    $scope.dt_end = new Date(year, month, day);
                };

                $scope.formats = ['dd-MMMM-yyyy', 'yyyy/MM/dd', 'dd.MM.yyyy', 'shortDate'];
                $scope.format = $scope.formats[0];
                $scope.altInputFormats = ['M!/d!/yyyy'];

                $scope.popup1 = {
                    opened: false
                };

                $scope.popup2 = {
                    opened: false
                };

                var tomorrow = new Date();
                tomorrow.setDate(tomorrow.getDate() + 1);
                var afterTomorrow = new Date();
                afterTomorrow.setDate(tomorrow.getDate() + 1);
                $scope.events = [
                    {
                        date: tomorrow,
                        status: 'full'
                    },
                    {
                        date: afterTomorrow,
                        status: 'partially'
                    }
                ];

                function getDayClass(data) {
                    var date = data.date,
                        mode = data.mode;
                    if (mode === 'day') {
                        var dayToCheck = new Date(date).setHours(0,0,0,0);

                        for (var i = 0; i < $scope.events.length; i++) {
                            var currentDay = new Date($scope.events[i].date).setHours(0,0,0,0);

                            if (dayToCheck === currentDay) {
                                return $scope.events[i].status;
                            }
                        }
                    }

                    return '';
                }



        }]);




		
}());