/**
 * Created by Japhari Jr on 2017-04-09.
 */
(function() {
    'use strict';
    var app = angular.module('authApp');
    app.controller('urgencyModal', ['$scope', '$http', '$state', '$rootScope', '$mdDialog',
        function($scope, $http, $state, $rootScope, $mdDialog) {

            var object = $scope.item;
            var information = $scope.infos;


            $scope.patient = object;
            $scope.infos = information;
            var user_id = $rootScope.currentUser.id;
            var facility_id = $rootScope.currentUser.facility_id;
            $scope.showFirstForm = function() {
                $scope.firstFormShow = true;
                $scope.secondFormShow = false;
            }
            $scope.patient_edit = function (patient) {
                console.log(patient);

            }

        }
    ]);
})();