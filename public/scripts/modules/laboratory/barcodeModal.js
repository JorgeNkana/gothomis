(function() {

    'use strict';

    var app = angular.module('authApp');

    app.controller('barcodeModal',

        ['$scope', '$http', '$rootScope', '$uibModal', '$uibModalInstance', 'object',
            function($scope, $http, $rootScope, $uibModal, $uibModalInstance, object) {
                var facility_id = $rootScope.currentUser.facility_id;
                var user_id = $rootScope.currentUser.id;
                $scope.sample_number = object.sample_number;
                $scope.image_code = object.image_code;
                $scope.last_name = object.last_name;
                $scope.sub_department_name = object.sub_department_name;

                $scope.closeModal = function() {
                    $uibModalInstance.dismiss();
                }
            }
        ]);

}());