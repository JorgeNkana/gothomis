(function () {

    'use strict';

    var app = angular.module('authApp');

    app.controller('equipmentsInfo',

                ['$scope','$http','$rootScope','$uibModal', '$uibModalInstance', 'object',
        function ($scope,$http,$rootScope,$uibModal,$uibModalInstance,object) {

                   $scope.equipment=object;

            $http.get('/api/getEquipementStatus').then(function(data) {
                $scope.getEquipementStatuses = data.data;


            });




            $scope.saveNewDeviceStatus= function (device,equipment) {
                if (angular.isDefined(device)==false) {
                    return sweetAlert("Select DEVICE Status", "", "error");
                }
                else{

                    var dataPost={"equipment_status_id":device.equip_status,"id":equipment.id};


                    $http.post('/api/saveNewDeviceStatus',dataPost).then(function(data) {
                        if(data.data.status ==0){
                            sweetAlert(data.data.data, "", "error");
                        }
                        else{
                            $http.get('/api/getEquipementList').then(function(data) {
                                $scope.equipementLists=data.data;

                            });
                        	var msg="Working status for "+equipment.equipment_name+" was successfully Changed";
                            $uibModalInstance.dismiss();
                            sweetAlert(msg, "", "success");
                        }});
                }
            }


		              }]);
		
		
		
		
		
}());