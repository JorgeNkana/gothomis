/**
 * Created by jeph on 05/06/2017.
 */

(function () {
    angular.module('authApp').controller('emergencyQueueController',emergencyQueueController);
    function emergencyQueueController($scope,$state,$http,$rootScope,$uibModal) {
        var user_id = $rootScope.currentUser.id;
        var facility_id = $rootScope.currentUser.facility_id;
        $scope.setTab = function(newTab){
            $scope.tab = newTab;
        };
        $scope.isSet = function(tabNum){
            return $scope.tab === tabNum;
        }


            $http.post('/api/getOpdPatients',{"facility_id":facility_id}).then(function(data) {
                $scope.patientData = data.data;
            });
            $http.post('/api/investigationList',{"facility_id":facility_id}).then(function(data) {
                $scope.patientInvData = data.data;
            });

        var patientOpdPatients =[];
        $scope.showSearch = function(searchKey) {
            $http.post('/api/getAllOpdPatients',{"searchKey":searchKey,"facility_id":facility_id}).then(function(data) {
                patientOpdPatients = data.data;
            });
            return patientOpdPatients;
        }
        var patientInvPatients =[];
        $scope.showSearch2 = function(searchKey) {
            //console.log(searchKey)
            $http.post('/api/getAllInvPatients',{"searchKey":searchKey,"facility_id":facility_id}).then(function(data) {
                patientInvPatients = data.data;
            });
            return patientInvPatients;
        }

        $scope.getConsultationModal = function (item) {
            var object = item;
            var modalInstance = $uibModal.open({
                templateUrl: '/views/modules/clinicalServices/consultationModal.html',
                size: 'lg',
                animation: true,
                controller: 'opdController',
                windowClass: 'app-modal-window',
                resolve:{
                    object: function () {
                        return object;
                    }
                }
            });
        }
        $scope.getTreatmentModal = function (item) {
            var object = item;
            var modalInstance = $uibModal.open({
                templateUrl: '/views/modules/clinicalServices/treatmentModal.html',
                size: 'lg',
                animation: true,
                controller: 'opdController',
                windowClass: 'app-modal-window',
                resolve:{
                    object: function () {
                        return object;
                    }
                }
            });
        }
        $scope.billsCancellation = function () {
            $http.post('/api/getBillList',{"facility_id":facility_id}).then(function (data) {
                $scope.patientBill=data.data;
            });
        }
        $scope.getBillModal = function (item) {
            $http.post('/api/cancelPatientBill',{"patient_id":item.patient_id,"facility_id":facility_id}).then(function (data) {
                var object = data.data;
                var modalInstance = $uibModal.open({
                    templateUrl:'/views/modules/clinicalServices/billCancellationModal.html',
                    size: 'lg',
                    animation:true,
                    controller:'opdController',
                    windowClass: 'app-modal-window',
                    resolve :{
                        object: function () {
                            return object;
                        }
                    }
                });
            });
        }
    }
})();