/**
 * Created by japhari on 07/03/2017.
 */

(function() {

    'use strict';

    angular
        .module('authApp')

        .controller('radiologyViewController', radiologyViewController);

    function radiologyViewController($http, $auth, $rootScope,$state,$location,$scope) {

        $scope.isNavCollapsed = false;
        $scope.isCollapsed = true;
        $scope.isCollapsedHorizontal = true;
        var user=$rootScope.currentUser;
        var user_name=$rootScope.currentUser.id;
        var facility_id=$rootScope.currentUser.facility_id;




        //user menu
        $scope.printUserMenu=function (user_id) {

            $http.get('/api/getUsermenu/'+user_id ).then(function(data) {
                $scope.menu=data.data;
                //console.log($scope.menu);

            });

        };

        $http.get('/api/getUserImage/'+user_name).then(function(data) {
            $scope.photo='/uploads/'+data.data[0].photo_path;
            //console.log($scope.photo);

        });

        //Equipments list with status
        $scope.getXrays=function () {

            $http.get('/api/getXrayImage').then(function(data) {
                $scope.Xrays=data.data;

            });


        }
        $scope.getXrays();




        var user_id=$rootScope.currentUser.id;
        var facility=$rootScope.currentUser.facility_id;
        $scope.printUserMenu(user_id);

//radiology patients Queue
        $scope.imageStatus = function(patient_id){


            //console.log(patient_id);

            $http.get('/api/imageStatus/'+patient_id).then(function(data) {

                $scope.patient_orders=data.data;

                //console.log(data.data);
            });
        };

//Verify Radiology Images

        $scope.VerifyXray = function(id){

            //console.log(id);

            $http.get('/api/VerifyXray/'+id).then(function(data) {

                //console.log(data.data);
                swal(
                    'Updated Successfully',
                    'Image  Verified!',
                    'success'
                )
                $scope.getXrays();

            });
        };

















    }

})();