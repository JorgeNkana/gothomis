(function () {

    'use strict';

    var app = angular.module('authApp');

    app.controller('LabPanelComponetsController',
                ['$scope','$http','$rootScope','$uibModal', '$uibModalInstance','toastr', 'object',
        function ($scope,$http,$rootScope,$uibModal,$uibModalInstance,toastr,object) {
            var facility_id =$rootScope.currentUser.facility_id;
            var user_id =$rootScope.currentUser.id;
            $scope.results = {
                'sections': []
            };
            $scope.results.sections = object.getPanelComponets;
            $scope.getPanelComponets=object.getPanelComponets;
            $scope.regex=/\s/g;

            $scope.closeModal=function() {
                $uibModalInstance.dismiss();
            }


            $scope.approveComponent = function(getPanelComponet) { //this is called with the submit
              var componentsResults=[];
              var field_id;
              $scope.getPanelComponets.forEach(function (getPanelComponet) {
                       componentsResults.push({
                          'panel_name':getPanelComponet.panel,
                          'component_name':getPanelComponet.panel_compoent_name,
                          'component_id':getPanelComponet.id,
                          'order_id':getPanelComponet.order_id,
                          'item_id':getPanelComponet.item_id,
                          'minimum_limit':getPanelComponet.minimum_limit,
                          'maximum_limit':getPanelComponet.maximum_limit,
                          'si_units':getPanelComponet.si_units,
                          'sample_no':getPanelComponet.sample_no,
                          'user_id':user_id,
                          'component_name_value':getPanelComponet.component_name_value
                      });




              });

                if(componentsResults.length != $scope.getPanelComponets.length){
               return toastr.error('','All fields are required.');
                }

                 $http.post('/api/approveComponentsResults',componentsResults).then(function(data) {
                  if(data.status==0){
                     toastr.error('',data.data.data);
                  }else{
                      toastr.success('','Results Approveds');
                  }
                     $scope.closeModal();
                });
                }

            $scope.saveComponent = function(getPanelComponet) { //this is called with the submit
              var componentsResults=[];
              var field_id;
              $scope.getPanelComponets.forEach(function (getPanelComponet) {
                  field_id = getPanelComponet.panel_compoent_name.replace($scope.regex, '_');
                  if ($('#' + field_id).val() != '') {
                      componentsResults.push({
                          'panel_name': getPanelComponet.panel,
                          'component_name': getPanelComponet.panel_compoent_name,
                          'component_id': getPanelComponet.id,
                          'order_id': getPanelComponet.order_id,
                          'item_id': getPanelComponet.item_id,
                          'minimum_limit': getPanelComponet.minimum_limit,
                          'maximum_limit': getPanelComponet.maximum_limit,
                          'si_units': getPanelComponet.si_units,
                          'sample_no': getPanelComponet.sample_no,
                          'user_id': user_id,
                          'component_name_value':$('#'+field_id).val()
                      });

                  }


              });

                if(componentsResults.length != $scope.getPanelComponets.length){
               return toastr.error('','All fields are required.');
                }

                 $http.post('/api/saveComponentsResults',componentsResults).then(function(data) {
                  if(data.status==0){
                     toastr.error('',data.data.data);
                  }else{
                      toastr.success('','Results Saved ');
                  }
                     $scope.closeModal();
                });
                }


            $scope.cancel=function (){
                //console.log('done and cleared');
                $uibModalInstance.dismiss();

            }

 $scope.confirmRejectResults=function(getTestRequest) {

                var object=getTestRequest;

                var modalInstance = $uibModal.open({
                    templateUrl: '/views/modules/laboratory/confirmRejectResults.html',
                    size: 'lg',
                    animation: true,
                    controller: 'printSampleNumberBarcode',
                    resolve:{
                        object: function () {
                            return object;
                        }
                    }
                });


            }







		              }]);
		
		
		
		
		
}());