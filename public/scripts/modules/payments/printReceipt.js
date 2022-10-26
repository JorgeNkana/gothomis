/**
 * Created by Mazigo Jr on 2017-03-25.
 */
(function () {
    'use strict';
    var app = angular.module('authApp');
    app.controller('printReceipt',['$scope','$state','$http','$rootScope','$uibModal' , '$mdDialog','object','$uibModalInstance',
            function ($scope,$state,$http,$rootScope,$uibModal, $mdDialog,object,$uibModalInstance) {
                $scope.date = new Date();
                var user_id = $rootScope.currentUser.id;
                var facility_id = $rootScope.currentUser.facility_id;
                $http.get('/api/getUsermenu/'+user_id ).then(function(data) {
                    $scope.menu=data.data;
                });
            $scope.bill = object;
                $scope.getTotal = function () {
                    var  total = 0;
                    for(var i = 0; i < $scope.bill.length ; i++) {
                        total -= -($scope.bill[i].price*$scope.bill[i].quantity-$scope.bill[i].discount);
                    }
				
					if((total % 50) > 0){
						total = (total -  (total % 50)) + 50;
					}
                    return total;
                }
              $scope.printInvoice = function printInvoice()
                {
                    var DocumentContainer = document.getElementById('contentToPrint');
                    var WindowObject = window.open("", "PrintWindow",
                        "width=750,height=650,top=50,left=50,toolbars=no,scrollbars=yes,status=no,resizable=yes");
                    WindowObject.document.title = "Print Receipt: GoT-HOMIS";
                    WindowObject.document.writeln(DocumentContainer.innerHTML);
                    WindowObject.document.close();

                    setTimeout(function(){
                        WindowObject.focus();
                        WindowObject.print();
                        WindowObject.close();
                    },0);
					$state.reload();
                    $uibModalInstance.dismiss();
                }
                $scope.closeReceipt = function () {
                    $state.reload();
                    $uibModalInstance.dismiss();
                }



            }]);
}());