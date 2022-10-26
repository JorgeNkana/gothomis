/**
 * Created by USER on 2017-02-13.
 */
(function() {

    'use strict';

    angular
        .module('authApp')
        .controller('nutritionController',nutritionController);

    function nutritionController($http, $auth, $rootScope,$state,$location,$scope,$uibModal, $mdDialog) {
        var facility_id =$rootScope.currentUser.facility_id;
        var user_id =$rootScope.currentUser.id;
        $scope.regex=/\s/g;
        $scope.AvailablePrintOuts=[{"id":1,"TemplateName":"Drug Sheet"},{"id":2,"TemplateName":"Input Output Forms"},{"id":3,"TemplateName":"Observation Chart"},{"id":4,"TemplateName":"Turning Chart"}];
        $scope.AvailablePrintOutsTheatre=[{"id":1,"TemplateName":"Anaesthesia"},{"id":2,"TemplateName":"Doctor Report/indication"}];
        $scope.wardStatus=[{"id":1,"TemplateName":"List waiting for Operation"},{"id":2,"TemplateName":"List of Patients Discharged"},{"id":3,"TemplateName":"List of Patients with Drugs Dosage"}];
        $scope.bedStates=[{"id":1,"TemplateName":"Get Dosage status"},{"id":2,"TemplateName":"Transfer to Another Bed"},{"id":3,"TemplateName":"Transfer to Another Ward"},{"id":4,"TemplateName":"Collect Lab Sample"},{"id":5,"TemplateName":"Prepare for Operation"},{"id":6,"TemplateName":"Abscondee from the ward"}];
        $scope.chartsReserved=[{"id":1,"TemplateName":"Observation Chart"},{"id":2,"TemplateName":"Turning Chart"},{"id":3,"TemplateName":"Feeding Chart"}];

     
   $scope.nutritionAssessment=function(){
   $http.get('/api/getNutritionPatients/'+facility_id).then(function(data) {
           $scope.getNutritionPatients=data.data;
                }); 

           };


      $scope.nursingCareOptions=function(templateID,SelectedPatient){
      if(templateID==1){
                 
                    $mdDialog.show({                 
                        controller: function ($scope) {
                                $scope.SelectedPatient=SelectedPatient;
                                $scope.cancel = function () {
                                 $mdDialog.hide();
                            };
                        },
                        templateUrl: '/views/modules/nursing_care/nursing_care_chart.html',
                        parent: angular.element(document.body),
                        clickOutsideToClose: false,
                         fullscreen: $scope.customFullscreen // Only for -xs, -sm breakpoints.
                    });
             }
             else if(templateID==2){
                 var admission_id=SelectedPatient.admission_id;

                   $http.get('/api/getListNursingCare/'+admission_id).then(function(data) {
              

                    $mdDialog.show({                 
                        controller: function ($scope) {
                                $scope.SelectedPatient=SelectedPatient;
                                $scope.nursingCares=data.data;
                             
                                $scope.cancel = function () {
                                 $mdDialog.hide();
                            };
                        },
                        templateUrl: '/views/modules/nursing_care/pre_view_nursing_care_chart.html',
                        parent: angular.element(document.body),
                        clickOutsideToClose: false,
                         fullscreen: $scope.customFullscreen // Only for -xs, -sm breakpoints.
                    });
                   });
                 
             }
      }
       
     }

})();