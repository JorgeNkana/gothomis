(function () {

    'use strict';

    var app = angular.module('authApp');

    app.controller('mortuaryManagementModal',

                ['$scope','$http','$rootScope','$uibModal', '$uibModalInstance', 'object',
        function ($scope,$http,$rootScope,$uibModal,$uibModalInstance,object) {
                    $scope.mortuary=object;
                    var mortuary_id=$scope.mortuary.id;

            $http.get('/api/getCabinetNumber/'+mortuary_id).then(function(data){
                $scope.getCabinetNumber=data.data;
            });

            $http.get('/api/getCabinets/'+mortuary_id).then(function(data){
                $scope.getCabinets=data.data;
            });

			
			$scope.cancel=function (){
				//console.log('done and cleared');
			$uibModalInstance.dismiss();
			
			}
			
			
			$scope.closeAllModals=function (){
				//console.log('done and cleared');
			$uibModalInstance.dismissAll();
			
			}

        }]);
		
		
		
		
		
}());