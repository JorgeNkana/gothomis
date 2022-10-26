/**
 * Created by jeph on 09/06/2017.
 */

(function () {
    'use strict';
    var app = angular.module('authApp');
    app.controller('emergencyadmissionModal',['$scope','$http','$rootScope','$uibModal', '$uibModalInstance', 'object',
        function ($scope,$http,$rootScope,$uibModal,$uibModalInstance,object) {
            $scope.admissionDetails = object;
            $scope.users = object;
            var user_id = $rootScope.currentUser.id;
            $scope.admitPatient = function (item,notes) {
                if(notes == null){
                    swal("Ooopss Sorry!","Please write admission notes and prescription instructions"); return;
                }
                var admissionData = {"patient_id":item.patient_id,"admission_status_id":1,"facility_id":item.facility_id,"user_id":user_id,
                    "instructions":notes.instructions,"prescriptions":notes.prescriptions,"ward_id":item.ward_id};
                $http.post('/api/admitPatient',admissionData).then(function (data) {

                });
                swal("Patient successfully admitted to "+item.ward_full_name);
                $uibModalInstance.dismiss();
            }
            //admission to specialized clinics
            $scope.internalTransfer = function (clinic,summary) {
                var clinicData = {"sender_clinic_id":clinic.sender_clinic_id,"clinic_id":clinic.clinic_id,"summary":summary.instructions,"priority":summary.priority,"received":0,"visit_id":clinic.visit_id};
                $http.post('/api/postToClinics',clinicData).then(function (data) {

                });
                $scope.notes = null;
                swal(clinic.first_name+' '+clinic.middle_name+' '+clinic.last_name+' transferred to '+clinic.department_name,'','success');
                $uibModalInstance.dismiss();
            }

        }]);
})();