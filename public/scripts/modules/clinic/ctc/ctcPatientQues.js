(function () {

    'use strict';

    var app = angular.module('authApp');

    app.controller('ctcPatientQues',
                ['$scope','$http','$rootScope','$uibModal', '$uibModalInstance','toastr', 'object',
        function ($scope,$http,$rootScope,$uibModal,$uibModalInstance,toastr,object) {
           $scope.ctcQues=object;
            var facility_id =$rootScope.currentUser.facility_id;
            var user_id =$rootScope.currentUser.id;


			$scope.cancel=function (){
				$uibModalInstance.dismiss();
			
			}

                  var resdata=[];
            $scope.showSearchResidences = function(searchKey) {

                $http.get('/api/searchResidences/'+searchKey).then(function(data) {
                    resdata = data.data;
                });
                ////console.log(resdata);
                return resdata;
            }
            $scope.saveCTCNewAttendance=function (patient,ctcQues) {
				//console.log(ctcQues);
				var patient_id=ctcQues.patient_id;
				var last_name=ctcQues.last_name;
                if (angular.isDefined(patient)==false) {
                    return toastr.error('','ENTER UNIQUE CTC ID NUMBER  FOR '+last_name);
                }
                else if(angular.isDefined(patient.address)==false){
                    return toastr.error('','SELECT STREET/VILLAGE FOR '+last_name);
                }
				var unique_id=patient.unique_id;
				var withrecords=patient.withrecords;
				var ten_cell_leaders=patient.ten_cell_leaders;
				var street=patient.street;
				var on_art=patient.on_art;
				var no_records=patient.no_records;
				var in_care=patient.in_care;
				var head_household=patient.head_household;
				var contact_household=patient.contact_household;
				var address=patient.address.residence_id;

                var postData={"on_off":1,"last_name":last_name,"patient_id":patient_id,"residence_id":address,"contact_house_hold_head":contact_household,"name_head_house_hold":head_household,"in_care":in_care,"no_records":no_records,"on_art":on_art,"street":street,"name_ten_cell_leader":ten_cell_leaders,"facility_id":facility_id,"user_id":user_id,"unique_ctc_number":unique_id,"withrecords":withrecords};
                $http.post('/api/saveCtCRegistration',postData).then(function(data) {
                    if(data.data.status ==0){
                        toastr.error('',data.data.data);
                    }else{
                        toastr.success('',data.data.data);
                        $scope.cancel();
                        var object ={'patient':patient,'ctcQues':ctcQues};
                        var modalInstance = $uibModal.open({
                            templateUrl: '/views/modules/clinic/ctc/ctc_patient_suport.html',
                            size: 'lg',
                            animation: true,
                            windowClass: 'app-modal-window',
                            controller: 'ctcPatientSupport',
                            resolve:{
                                object: function () {
                                    return object;
                                }
                            }
                        });
                    }


                });

            }

			$scope.closeAllModals=function (){
				//console.log('done and cleared');
			$uibModalInstance.dismissAll();
			
			}

        }]);
		
		
		
		
		
}());