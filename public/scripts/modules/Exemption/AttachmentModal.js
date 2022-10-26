/**
 * Created by USER on 2017-07-09.
 */
/**
 * Created by USER on 2017-06-26.
 */
(function () {

    'use strict';

    var app = angular
        .module('authApp')

    app.controller('AttachmentModal',['$state','$scope', '$http', '$rootScope', '$uibModal', '$mdDialog',
        function ($state,$scope, $http, $rootScope, $uibModal, $mdDialog) {
            $scope.cancel = function() {
                $mdDialog.cancel();
                $state.reload();
            };
           // var Attachments= $scope.Attachments;
            var facility_id =$rootScope.currentUser.facility_id;
            var user_id =$rootScope.currentUser.id;






        }]);





}());