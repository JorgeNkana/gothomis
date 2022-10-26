/**
 * Created by Mazigo Jr on 2017-10-02.
 */
(function () {
    'use strict';
    angular.module('authApp').controller('inventoryClientController',inventoryClientController);
    function inventoryClientController($scope,$rootScope,$http,$mdDialog) {
        var user_id = $rootScope.currentUser.id;
        var facility_id = $rootScope.currentUser.facility_id;
        angular.element(document).ready(function () {
            $http.post('api/getItems',{facility_id:facility_id}).then(function (data) {
                $scope.items = data.data;
            });
            $http.get('api/getUserDepartments').then(function (data) {
                $scope.departments = data.data;
            });
        });
        $scope.refreshItems = function () {
            $http.post('api/getItems',{facility_id:facility_id}).then(function (data) {
               $scope.items = data.data;
            });
            $http.get('api/getUserDepartments').then(function (data) {
               $scope.departments = data.data;
            });
        }
        $scope.removeFromSelection = function(item, objectdata) {

            var indexremoveobject = objectdata.indexOf(item);

            objectdata.splice(indexremoveobject, 1);

        }
        $scope.selectedOrders = [];
        $scope.addItems = function (dept,item,qty) {
            for (var i = 0; i < $scope.selectedOrders.length; i++)
                if ($scope.selectedOrders[i].item_id == item.id) {
                    swal(item.item_name + " already in your order list!", "", "info");
                    return;
                }
            $scope.selectedOrders.push({
                "facility_id": facility_id,
                "quantity": qty.quantity,
                "status": 0,
                "user_id": user_id,
                "item_id": item.id,
                "item_name": item.item_name,
                "department_name": dept.department_name,
                "department_id":dept.id
            });
            $('#item').val('');
            $('#qty').val('');
        }
        $scope.saveRequest = function (item) {
           $http.post('/api/sendInventoryRequests',item).then(function (data) {
               if(data.data.status == 1){
                   swal(data.data.msg,'','success');
               }
           });
            $scope.selectedOrders = [];
        }
    }
})();