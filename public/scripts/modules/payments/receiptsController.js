/**
 * Created by Mazigo Jr on 2017-02-23.
 */
(function () {
    'use strict';
   var app= angular.module('authApp');
    app.controller('receiptsController',['$scope','$state','$http','$rootScope','$uibModal','$mdDialog','Helper',
        function ($scope,$state,$http,$rootScope,$uibModal,$mdDialog, Helper) {
            var user_id=$rootScope.currentUser.id;
            var facility_id = $rootScope.currentUser.facility_id;
			$scope.paymentMethod = '';
			$scope.regex = /\B(?=(\d{3})+(?!\d))/g;
			$scope.list_travessing = false;
			$scope.data = {'cb':false};
			
																
			//gepg codes
			$http.get('/gepg/new/getPaymentOption').then(function (data) {
				$scope.paymentMethod = data != null ? (parseInt(data.data[0].BillPayOpt ? data.data[0].BillPayOpt : 1) == 1 ? 'gepg' : 'cash') : '';
				$scope.configuredOption =$scope.paymentMethod;
			});		
																		
			$scope.gepgPostBill = function(bill){
				var phoneRegex = new RegExp("/^255[6-7][1-9]\d{7}$/");
				var BillDetails = {
					facility_id: facility_id,
					UserId:$rootScope.currentUser.id,
					UserName:$rootScope.currentUser.name,
					InvoiceId:$scope.item[0].receipt_number,
					InvoiceLine: {
						BillDescription:'Hospital Bill',
						BillAmount:$scope.toto(),
						CashDeposit:0,
						PayerName:$scope.item[0].name.split('#')[0],
						PayerId:$scope.item[0].name.split('#')[1],
						PayerPhone:$scope.item[0].mobile_number ? $scope.item[0].mobile_number.toString().replace(/\+\s/g,'') : '',
						PayerEmail:'',
					}
				};
					
				var GePG = {attempt:0, load: function(BillDetails){
					GePG.attempt++;
					Helper.overlay(true);
					$http.post('/gepg/new/send_bill', BillDetails).then(function(response){
						Helper.overlay(false);
						swal({
							title:'CONTROL NUMBER REQUEST', 
							html:response.data.generic+(response.data.success==0 ? '<br /><span style="color:red">'+response.data.real+'</span>' : ''),
							type:'info',
							customClass: 'swal-wide',
							allowOutsideClick:false
						});
						
						if(response.data.success == 1){
							bill = null;
							$mdDialog.hide();
						}
					}, function(data){if(GePG.attempt <5){Helper.overlay(false); GePG.load(BillDetails); }else{Helper.overlay(false);  swal({title: 'Temporary Error!', html: Helper.genericError('Posting Bill to GePG'),type: 'error'})}});
				}}
				
				GePG.load(BillDetails);
			}
			//end gepg code
			
			$http.get('/api/getUsermenu/'+user_id ).then(function(data) {
                $scope.menu=data.data;
            });
            $scope.patientBills=$scope.item;
            $scope.cancelBillWindow = function () {
                $mdDialog.cancel();
                $state.reload();
            }
            $scope.toto = function () {
                var  total = 0;
                for(var i = 0; i < $scope.patientBills.length ; i++) {
                    total -= -($scope.patientBills[i].price*$scope.patientBills[i].quantity-$scope.patientBills[i].discount);
                }
				
				if((total % 50) > 0){
					total = (total -  (total % 50)) + 50;
				}
                return total;
            }
            
			$scope.proccessBill = function (bill) {
				if(bill == null){
                    swal("Please choose Patient and payment method to continue!");
					return;
                }
				
				var x= $scope.toto();
                
				swal({
                    title: 'Are you sure you want to complete this transaction with a sum of '+x+' Tshs?',
					type: 'warning',
					showCancelButton: true,
					confirmButtonColor: '#3085d6',
					cancelButtonColor: '#d33',
					confirmButtonText: 'Yes',
					customClass: 'swal-wide',
					allowOutsideClick:false
                }).then(function () {
					//code inserted for gepg
					if(($scope.paymentMethod == 'gepg' || $scope.configuredOption == 'gepg') && x != 0 ){
						var BillUpdate = {attempt:0, load: function(bill){
							BillUpdate.attempt++;
							$http.post('/api/updateGepgUser', {user_id:user_id,bill:bill.invoice_id}).then(function(response){
								$scope.gepgPostBill([bill]);
							}, function(data){if(BillUpdate.attempt <5) BillUpdate.load(bill);});
						}};
						BillUpdate.load(bill);
						return;
					}
					//end code gepg snippet
					else if($scope.paymentMethod == 'cash' || (($scope.paymentMethod == 'gepg' || $scope.configuredOption == 'gepg') && x == 0)){
						var billUpdate={user_id:user_id,bill:bill};
						$http.post('/api/updateBills',billUpdate).then(function (data) {
							$scope.item = bill;
							var object = bill;
							var modalInstance = $uibModal.open({
								templateUrl: '/views/modules/payments/receipts.html',
								size: 'lg',
								animation: true,
								clickOutsideToClose: true,
								controller: 'printReceipt',
								resolve:{
									object: function () {
										return object;
									}
								}
							});
						});
						//end what if
					   $mdDialog.hide();
					}
                }, function (dismiss) {});
            }
        }
        ]);
})();