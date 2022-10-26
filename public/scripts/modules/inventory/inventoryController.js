/**
 * Created by Mazigo Jr on 2017-04-05.
 */
(function () {
    'use strict';
    angular.module('authApp').controller('inventoryController',inventoryController);
    function inventoryController($scope,$rootScope,$http,$mdDialog) {
        var user_id = $rootScope.currentUser.id;
        var facility_id = $rootScope.currentUser.facility_id;
        angular.element(document).ready(function() {
            $scope.availableLedgers();
            $scope.availableItems();
        });
        var user_store=[];
        $scope.SearchUser=function (seachKey) {
            var searchUser={'userKey':seachKey,'facility_id':facility_id};
            $http.post('/api/getUserToSetStoreToAccess',searchUser ).then(function(data) {
                user_store=data.data;
            });
            return user_store;
        }
        $scope.availableLedgers = function () {
            $http.post('api/getLedgers',{facility_id:facility_id}).then(function (data) {
               $scope.ledgers = data.data;
            });
            $scope.availableItems();
        }
        $scope.availableItems = function () {
            $http.post('api/getItems',{facility_id:facility_id}).then(function (data) {
               $scope.items = data.data;
            });

        }
        $scope.getDepartments = function () {
            $http.get('/api/getUserDepartments').then(function (data) {
               $scope.departments = data.data;
            });
        }
        $scope.getDepartmentOrders = function (item) {
            $http.post('/api/getDepartmentItems',{department_id:item.id,facility_id:facility_id}).then(function (data) {
               $scope.departmentItems = data.data;
            });
        }
        $scope.newLedger = function (item) {
            var itemData = {ledger_name:item.ledger_name,ledger_code:item.ledger_code,description:item.description,facility_id:facility_id};
            $http.post('/api/newLedger',itemData).then(function (data) {
            if(data.data.status == 1){
                swal(data.data.msg,'','success');
            }
            });
            $('#new_ledger').val('');
            $('#ledger_code').val('');
            $('#description').val('');
            $scope.availableLedgers();
        }
        $scope.updateLegder = function (item) {
            $http.post('/api/updateLedger',item).then(function (data) {
                if(data.data.status == 1){
                    swal(data.data.msg,'','success');
                }
            });
            $scope.availableLedgers();
        }
        $scope.newItem = function (item) {
            $http.post('/api/postNewItem',item).then(function (data) {
                if(data.data.status == 1){
                    swal(data.data.msg,'','success');
                }
                $('#new_item').val('');
                $('#item_code').val('');
            });
            $scope.availableItems();
        }
        $scope.updateItem = function (item) {
            $http.post('/api/updateItem',item).then(function (data) {
                if(data.data.status == 1){
                    swal(data.data.msg,'','success');
                }
            });
            $scope.availableItems();
        }
    $scope.departmentalOrders = function () {
        $http.post('/api/getDepartmentOrders',{facility_id:facility_id}).then(function (data) {
           $scope.departmentalOrders = data.data;
        });
    }
    $scope.inventoryReports = function (item) {
        var report = {start:item.start,end:item.end,facility_id:facility_id};
        $http.post('/api/inventoryReports',report).then(function (data) {
           $scope.inventoryReports = data.data;
        });
    }
    $scope.inspectOrders = function () {
        $http.post('/api/inspectOrders',{facility_id:facility_id}).then(function (data) {
           $scope.pendingOrders = data.data;
        });
    }
    $scope.getOrderItems = function (item) {
        $mdDialog.show({
            controller:function ($scope) {
                $http.post('/api/getOrderItems',{order_number:item.order_number}).then(function (data) {
                    $scope.orderItems = data.data;
                });
                $scope.orderAction = function (item,status) {
                   var items = {id:item.id,item_name:item.item_name,batch:item.batch,supplier:item.supplier,cost_price:item.cost_price,order_status:status};
                    console.log(items)
                    $http.post('/api/updateOrderItem',items).then(function (data) {
                    if(data.data.status ==1){
                        swal('',data.data.msg,'success')
                    }
                    if(data.data.status ==0){
                        swal('',data.data.msg,'info')
                    }
                    });
                }
                $scope.cancel = function(){
                    $mdDialog.hide();
                };
            },
            templateUrl: '/views/modules/inventory/itemsReceivedModal.html',
            parent: angular.element(document.body),
            clickOutsideToClose: false,
            fullscreen: false,
        });
    }
    $scope.quotationForm = function (item) {
        swal({
            title: 'Proceed?',
            html: 'By clicking YES you confirm that order is valid and order process can continue..Agree?',
            type: 'info',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Yes'
        }).then(function () {
            $mdDialog.show({
                controller:function ($scope) {
                    $scope.quotationData = item;
                    $http.get('/api/getUsermenu/'+user_id ).then(function(data) {
                        $scope.menu=data.data;
                    });
                    $scope.cancel = function(){
                        $mdDialog.hide();
                    };
                    $scope.printQuotationForm = function(item){
                        var inventoryOrder = {item:item,user_id:user_id,facility_id:facility_id};
                        $http.post('/api/postOrderItems',inventoryOrder).then(function (data) {
                           if(data.data.status ==1){
                               swal('',data.data.msg,'success');
                           }
                        });
                        var DocumentContainer = document.getElementById('quotationFormToPrint');
                        var WindowObject = window.open("", "PrintWindow",
                            "width=900,height=700,top=50,left=450,toolbars=no,scrollbars=no,status=no,resizable=yes");
                        WindowObject.document.title = "PRINT QUOTATION FORM: GoT-HoMIS";


                        WindowObject.document.write('<link rel="stylesheet" href="/css/bootstrap.css" type="text/css" />');
                        WindowObject.document.write('<link rel="stylesheet" href="/css/design.css" type="text/css" />');
                        WindowObject.document.write('<link rel="stylesheet" href="/bower_components/material-design-lite/material.css" type="text/css" />');

                        WindowObject.document.write('<link rel="stylesheet" href="/bower_components/angular-material/angular-material.min.css" type="text/css" />');

                        WindowObject.document.write('<link rel="stylesheet" href="/css/datatable_style.css" type="text/css" />');




                        WindowObject.document.writeln(DocumentContainer.innerHTML);

                        WindowObject.document.close();

                        setTimeout(function () {
                            WindowObject.focus();
                            WindowObject.print();
                            WindowObject.close();
                        }, 0);
                    };
                },
                templateUrl: '/views/modules/inventory/quotationModal.html',
                parent: angular.element(document.body),
                clickOutsideToClose: false,
                fullscreen: false,
            });
        }, function(){
           swal('','cancelled','info');
            return;});


    }
    $scope.issueItems = function (item,user,order) {

        var issueData = {facility_id:facility_id,quantity:item.quantity,item_received_id:order.item_received_id,issuing_officer_id:user_id,receiver_id:user.id,
            department_id:order.dept_id,item_id:order.item_id};
        $http.post('/api/issueInventoryItems',issueData).then(function (data) {
            if(data.data.status ==1){
                swal('',data.data.msg,'success');
            }
        });
    }


    }
})();