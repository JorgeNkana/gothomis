/**
 * Created by Mazigo Jr on 2017-04-20.
 */
(function () {
    'use strict';
    var app = angular.module('authApp');
    app.controller('addItemsModal',['$scope','$http','$rootScope','$uibModal' ,'$uibModalInstance',
        function ($scope,$http,$rootScope,$uibModal, $uibModalInstance) {
            var facility_id = $rootScope.currentUser.facility_id;

            $http.get('/api/inventoryItemTypes/'+facility_id).then(function (data) {
               $scope.itemTypes =data.data;
            });
            $scope.saveItem = function (item) {
                if(angular.isDefined(item.item_name)==""){
                swal("Please Type Item name","","error");
                    return;
                }
                else if(angular.isDefined(item.item_type_id)==""){
                swal("Please choose Item type then click save","","error");
                    return;
                }

                var newItem = {"item_name":item.item_name,"item_type_id":item.item_type_id,"facility_id":facility_id};
                $http.post('/api/postNewItem',newItem).then(function (data) {

                });
                $scope.items = null;
                swal("Item saved","","success");
            }

        }]);
}());