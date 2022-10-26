/**
 * Created by Mazigo Jr on 2017-03-27.
 */
(function () {
    'use strict';
    var app = angular.module('authApp');
    app.controller('posReceipts',['$scope','$state','$http','$rootScope','$uibModal' ,'$uibModalInstance', 'object',
        function ($scope,$state,$http,$rootScope,$uibModal, $uibModalInstance, object) {
            $scope.date = new Date();
            $scope.bill = object;
            
			var facility_id = $rootScope.currentUser.facility_id;
            var user_id=$rootScope.currentUser.id;
            $http.get('/api/getUsermenu/'+user_id ).then(function(data) {
                $scope.menu=data.data;
            });
            for(var i=0; i<object.length; i++ ){
                if(object[i].chf_toto){
                    $scope.getTotal = function () {
                        var  total = 0;
                        for(var i = 0; i < object.length ; i++) {
                            total += (object[i].chf_toto);
                        }
					  
						if((total % 50) > 0){
							total = (total -  (total % 50)) + 50;
						}
			
                        return total;
                    }
                }else {
                    $scope.getTotal = function () {
                        var  total = 0;
                        for(var i = 0; i < object.length ; i++) {

                            total += (object[i].sub_total);
                        }
					  
						if((total % 50) > 0){
							total = (total -  (total % 50)) + 50;
						}
                        return total;
                    }
                }
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