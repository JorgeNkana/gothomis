/**
 * Created by jeph on 09/06/2017.
 */

(function () {
    'use strict';
    var app = angular.module('authApp');
    app.controller('emergencyClinicsModal',['$scope','$http','$rootScope','$uibModal', '$uibModalInstance', 'object',
        function ($scope,$http,$rootScope,$uibModal,$uibModalInstance,object) {
            $scope.selectedPatient = object;

            var user_id = $rootScope.currentUser.id;
            $scope.urgent = [
                {"urgent": "Urgent"},{"urgent": "Routine"}
            ];
            $scope.referPatient = function (item,patient) {

                if(patient == null){
                    swal("Ooopss Sorry!","Please Select a Patient"); return;
                }
                var referedPatients = {"doctor_requesting_id":user_id,"summary":item.note,"priority":item.priority.urgent,"sender_clinic_id":patient.dept_id,
                    "received":1,"on_off":1,"visit_id":patient.account_id};
                //console.log(referedPatients);

                $http.post('/api/patientToClinic',referedPatients).then(function (data) {

                });
                swal("Patient successfully send  to "+patient.department);
                // $uibModalInstance.dismiss();
            }


        }]);
})();