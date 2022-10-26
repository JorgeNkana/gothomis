/**
 * Created by USER on 2017-03-09.
 */
/**
 * Created by USER on 2017-02-24.
 */
/**
 * Created by USER on 2017-02-13.
 */
/**
 * Created by USER on 2017-02-13.
 */
(function() {

    'use strict';

    angular
        .module('authApp')
        .controller('DrfController', DrfController);

    function DrfController($http, $auth, $rootScope,$state,$location,$scope,$timeout,Helper,$mdDialog,$stateParams) {
        $scope.setTab = function(newTab){
            $scope.tab = newTab;
        };
        $scope.isSet = function(tabNum){
            return $scope.tab === tabNum;
        }
        $scope.oneAtATime=true;
        //loading menu
        var user_id=$rootScope.currentUser.id;
        var user_name=$rootScope.currentUser.name;
        var  facility_id=$rootScope.currentUser.facility_id;
        
        //gepg codes
		$scope.paymentMethod = '';
		$scope.pendingGePGConfirmations = [];
		$http.get('/gepg/new/getPaymentOption').then(function (data) {
			$scope.paymentMethod = data != null ? (parseInt(data.data[0].BillPayOpt ? data.data[0].BillPayOpt : 1) == 1 ? 'gepg' : 'cash') : '';
			$scope.configuredOption =$scope.paymentMethod;
		});	
		
		$scope.billInfo = function(bill){
			if(bill.PayCntrNum == null){
				swal({
							title:'RESEND BILL',
							html:'Would you like to resend the bill?',
							type:'info',
							showCancelButton: true,
							confirmButtonText: 'Resend',
							customClass: 'swal-wide',
							allowOutsideClick:false
					}).then(function (){
						Helper.overlay(true);
						$http.post('/gepg/new/resendBill', {facility_id:facility_id,BillId: bill.BillId, drf:true}).then(function(response){
							Helper.overlay(false);
							swal({
									title:'CONTROL NUMBER REQUEST',
									html:response.data.generic+(response.data.success==0 ? '<br /><span style="color:red">'+response.data.real+'</span>' : ''),
									type:'info',
									customClass: 'swal-wide',
									allowOutsideClick:false
							});
						}, function(error, status){ Helper.overlay(false); swal({title: 'Temporary Error!', html: Helper.genericError('Posting Bill to GePG'),type: 'error'});});
					}, function(){
						return;
					});
			}else if(bill.Paid == 0){
				Helper.overlay(true);
				$http.post('/gepg/new/printBill', {facility_id:facility_id, BillId: bill.BillId, drf:true}).then(function(response){
					Helper.overlay(false);
					swal({
							title:response.data.title,
							html:response.data.generic+(response.data.success==0 ? '<br /><span style="color:red">'+response.data.real+'</span>' : ''),
							type:'info',
							showCancelButton: true,
							cancelButtonText: 'Print',
							customClass: 'swal-wide',
							allowOutsideClick:false
					}).then(function (){
						//TODO
					}, function(){
						var printer = window.open("", "BILL INFO");
						printer.document.writeln(response.data.generic);
						printer.document.close();
						printer.focus();
						printer.print();
						printer.close();
					});
				}, function(error, status){ Helper.overlay(false); swal({title: 'Temporary Error!', html: Helper.genericError('Posting Bill to GePG'),type: 'error'});});
			}
		}
		
		$scope.cashDeposit = function(){
			var date = new Date(); 
			date = date.getFullYear()+"-"+((date.getMonth()+1).toString().length == 1 ? "0"+(date.getMonth()+1) : (date.getMonth()+1))+"-"+(date.getDate().toString().length == 1 ? "0"+date.getDate() : date.getDate());
			var html = '<form class="form-horizontal" role="form" name="myForm" autocomplete="off" >\
				<br />\
				<div class="row">\
					<div class="form-group">\
						<label class="col-md-3 control-label">Cashier:</label>\
						<div class="col-md-9">\
							<input type="text" disabled class="form-control" value="' +$rootScope.currentUser.name+ '"/>\
						</div>\
					</div>\
					<div class="form-group">\
						<label class="col-md-3 control-label">Payment of:</label>\
						<div class="col-md-9">\
							<select id="transaction" class="form-control">\
								<option value="Onhand Cash Deposit" selected>Onhand Cash Deposit</option>\
							</select>\
						</div>\
					</div>\
					<div class="form-group">\
						<label class="col-md-3 control-label">GSF Code:</label>\
						<div class="col-md-9">\
							<input id = "gfsCode" list="gfs" style = "width:100%">\
							<datalist id="gfs">\
							</datalist> \
						</div>\
					</div>\
					<div class="form-group">\
						<label class="col-md-3 control-label">Amount:</label>\
						<div class="col-md-9">\
							<input type="text" class="form-control" id="amount" onkeyup="money(this, event)"/>\
						</div>\
					</div>\
					<div class="form-group">\
						<label class="col-md-3 control-label" style="">Date:</label>\
						<div class="col-md-9">\
							<input type="date" id="date" class="form-control" value="'+date+'"/>\
								</div>\
							</div>\
						</div>\
						<br /><div class="col-md-12 text-center">Proceed?</div>\
					</form>';
			swal({
					title: 'Fill in the Form Below',
					html: html,
					type: 'info',
					showCancelButton: true,
					confirmButtonColor: '#3085d6',
					cancelButtonColor: '#d33',
					confirmButtonText: 'Yes',
					customClass: 'swal-wide',
					allowOutsideClick:false
				}).then(function () {
					//validate
					if(!$('#amount').val() || !$('#date').val() | !$('#gfsCode').val()){
						swal('You must specify GFS, amount and date','','info');
						return;
					}
					
					var bill = {
                        drf:true,
						facility_id: facility_id,
						UserId:$rootScope.currentUser.id,
						UserName:$rootScope.currentUser.name,
						InvoiceId:0,
						InvoiceLine: {
							BillDescription:$('#transaction').val(),
							BillAmount:$('#amount').val().replace(/,/g, ""),
							GfsCode:$('#gfsCode').val(),
							CashDeposit:1,
							PayerName:$rootScope.currentUser.name,
							PayerId:(new Date()).getTime(),
							PayerPhone:'',
							PayerEmail:'',
						}
					};
					var Process = {Attempt: 0, execute : function(bill){
									Process.Attempt++;
									Helper.overlay(true);
									$http.post('/gepg/new/send_bill', bill).then(function(response){
										Helper.overlay(false);
										swal({
												title:'CONTROL NUMBER REQUEST',
												html:response.data.generic+(response.data.success==0 ? '<br /><span style="color:red">'+response.data.real+'</span>' : ''),
												type:'info',
												customClass: 'swal-wide',
												allowOutsideClick:false
											});
										$scope.pendingGePGConfirmations = response.data.data;
									}, function(error, status){ Helper.overlay(false);  if(Process.Attempt < 3){Process.execute(bill); return;}  swal({title: 'Temporary Error!', html: Helper.genericError('Posting Bill to GePG'),type: 'error'});});
					}};
								
					Process.execute(bill);
				}, function(){ return;});
			
			Helper.overlay(true);
			$http.get('/api/gfs-mappings').then(function(data) {
                $scope.gfs_mappings=data.data;
				var str = '';
				for (var i=0; i < $scope.gfs_mappings.length;++i){
					str += '<option value="'+$scope.gfs_mappings[i].code+'">'+$scope.gfs_mappings[i].description+'</option>'; // Storing options in variable
				}
				var my_list=document.getElementById("gfs");
				my_list.innerHTML = str;
				Helper.overlay(false);
            }, function(error, status){ Helper.overlay(false);  if(Process.Attempt < 3){Process.execute(bill); return;}  swal({title: 'Temporary Error!', html: Helper.genericError('Posting Bill to GePG'),type: 'error'});});
		}
		
		
		$scope.reconcile = function(){
			var checking = function(){
							Helper.overlay(true);
							$('#counter').html("Reconcilling...");
							$http.post('/gepg/new/reconciled_bills', {facility_id: facility_id}).then(function(response){
								Helper.overlay(false);
								toastr.warning(response.data.generic,'', {timeOut: 120000});
								if(response.data.completed == 1){
									swal({
										title:'RECONCILLIATION REQUEST',
										html:'<hr /><span style="font-family:Book Antiqua; font-size:16px; font-weight:bold">'+response.data.generic+'</span>',
										type:'info',
										customClass: 'swal-wide',
										allowOutsideClick:false
									});
								}
							}, function(error, status){Helper.overlay(false);});
						}
						
			var Process = function(){
							Helper.overlay(true);
							$http.post('/gepg/new/reconcile', {facility_id: facility_id}).then(function(response){
								Helper.overlay(false);
								swal({
									title:'RECONCILLIATION REQUEST',
									html:response.data.generic+(response.data.success==0 ? '<br /><span style="color:red">'+response.data.real+'</span>' : ''),
									type:'info',
									customClass: 'swal-wide',
									allowOutsideClick:false
								});
								if(response.data.success == 1){
									setTimeout(checking, 45000);//45 secs
								}
							}, function(error, status){ Helper.overlay(false); swal({title: 'Temporary Error!', html: Helper.genericError('GePG Payments Reconcilliation'),type: 'error', allowOutsideClick: false});});
						};
				
			Process();
		}
		
		$scope.gepgPostBill = function(invoice_id){
			var bill = {
                    drf:true,
					facility_id: facility_id,
					UserId:$rootScope.currentUser.id,
					UserName:$rootScope.currentUser.name,
					InvoiceId:invoice_id,
					InvoiceLine: {
						BillDescription:'Hospital Bill',
						BillAmount: $scope.totalSalesCost,
						CashDeposit:0,
						PayerName:$scope.sales[0].buyer_name,
						PayerId:$scope.sales[0].buyer_name,
						PayerPhone:$scope.sales[0].mobile_number ? $scope.sales[0].mobile_number : '',
						PayerEmail:'',
					}
				};
				
			var Process = {Attempt: 0 , execute : function(bill, invoice_id){
								Process.Attempt++;
								Helper.overlay(true);
								$http.post('/gepg/new/send_bill', bill).then(function(response){
									Helper.overlay(false);
									swal({
											title:'CONTROL NUMBER REQUEST',
											html:response.data.generic+(response.data.success==0 ? '<br /><span style="color:red">'+response.data.real+'</span>' : ''),
											type:'info',
											customClass: 'swal-wide',
											allowOutsideClick:false
									});
									
									if(response.data.success==0){
										var Rollback = function(invoice_id){
															$http.post('/gepg/new/rollback', {invoice_id: invoice_id, drf:true}).then(function(response){
																//TODO
															}, function(error, status){
																//TODO
															});
														};
										Rollback(invoice_id);
									}else{
										$scope.itemData = [];
										$scope.toto = $scope.totalSalesCost;
                                        $scope.sales=[];
									}
									
									$scope.pendingGePGConfirmations = response.data.data;
								}, function(error, status){ Helper.overlay(false);  if(Process.Attempt < 3){Process.execute(bill, invoice_id); return;} swal({title: 'Temporary Error!', html: Helper.genericError('Posting Bill to GePG'),type: 'error'});});
			}};
				
			Process.execute(bill, invoice_id);
		}
			
		$scope.cancelBill = function(bill, index){
			if(bill.Paid == 1)
				return;
			
			var Process = function(bill, index, reason){
							Helper.overlay(true);
                            $http.post('/api/CancelDrfGepgCell',{invoice_number: bill.InvoiceId}).then(function(data) {
                                $http.post('/gepg/new/cancel_bill',{facility_id: facility_id, BillId:bill.BillId, CashDeposit: parseInt(bill.CashDeposit), InvoiceId: bill.InvoiceId, user_id: user_id, reason:reason, drf:true}).then(function(response){
                                    Helper.overlay(false);
                                    swal({title:'BILL CANCELLATION',
                                        html:response.data.generic+(response.data.success==0 ? '<br /><span style="color:red">'+response.data.real+'</span>' : ''),
                                        type:'info',
                                        customClass: 'swal-wide',
                                        allowOutsideClick:false
                                    });
                                    if(response.data.success==1)
                                        $scope.pendingGePGConfirmations.splice(index,1);
                                },function(error, status){Helper.overlay(false); swal({title: 'Temporary Error!', html: Helper.genericError('Cancelling GePG Bill'),type: 'error'});});
                            },function(error, status){Helper.overlay(false); swal({title: 'Temporary Error!', html: Helper.genericError('Cancelling Bill'),type: 'error'});});
						};
			
			var html = '<form class="form-horizontal" role="form" name="myForm" autocomplete="off" >\
				<br />\
				<div class="row">\
					<div class="form-group">\
						<label class="col-md-3 control-label">User:</label>\
						<div class="col-md-9">\
							<input type="text" disabled class="form-control" value="' +$rootScope.currentUser.name+ '"/>\
						</div>\
					</div>\
					<div class="form-group">\
						<label class="col-md-3 control-label">Reason:</label>\
						<div class="col-md-9">\
							<textarea id="reason" class="form-control" rows="4"></textarea>\
						</div>\
					</div>\
				</div>\
				</form>';
			swal({
					title: 'CANCEL THIS BILL?',
					html: html,
					type: 'info',
					showCancelButton: true,
					confirmButtonColor: '#3085d6',
					cancelButtonColor: '#d33',
					confirmButtonText: 'OK',
					customClass: 'swal-wide',
					allowOutsideClick:false
				}).then(function () {
					if($.trim($('textarea#reason').val()) == ''){
						swal('You must provide a reason for your action','','warning');
						return;
					}
					Process(bill, index, $.trim($('textarea#reason').val()));
			}, function(){ return;});			
		}	
		
		$scope.checkGePG = function(){
			Helper.overlay(true);
            $http.post('/gepg/new/pending_bills', {facility_id: facility_id, drf: true}).then(function (response) {
                Helper.overlay(false);
                if(response.data.success != undefined && response.data.success==0 && response.data.account==-1)
                    return;//no gepg account detected
                if(response.data.constructor === Array && response.data.length != 0){
                    $scope.pendingGePGConfirmations = response.data;
                }
                
                if(!$scope.running)
                    $scope.running = true;
            }, function(error, status){Helper.overlay(false);});
		}
		
		$scope.GePGReceipt = function(bill){
			if(bill.Paid == 0 || bill.PspReceiptNumber == null)
				return;
			
			Helper.overlay(true);
			$http.post('/gepg/new/getGePGPaidBill',{gepg_receipt:bill.PspReceiptNumber, drf:true})
				.then(function (response){
					Helper.overlay(false);
					for(var i = 0; i < response.data.length; i++){
						response.data[i].receipt_number = bill.PspReceiptNumber;
					};
					
					var object = response.data;
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
				}, function(error, status){Helper.overlay(false); swal({title: 'Temporary Error!', html: Helper.genericError('Loading Customer\'s Bill Items.','<i>Please, re-select the customer</i>'),type: 'warning'})}); 
		}
		
		$scope.Processed = function(bill,index, auto = false){
			if(bill.Paid == 0 || bill.PspReceiptNumber == null)
				return;
			
			var Process = function(bill, index){
								if(!auto)
									Helper.overlay(true);
								$http.post('/gepg/new/mark_processed_bill',{facility_id: facility_id, BillId:bill.BillId, CashDeposit: parseInt(bill.CashDeposit), InvoiceId: bill.InvoiceId, user_id: user_id, drf:true}).then(function (response) {
									if(!auto)
										Helper.overlay(false);
									if(response.data.success == 1 && $scope.pendingGePGConfirmations.constructor === Array)
										$scope.pendingGePGConfirmations.splice(index,1)
								},function(error, status){if(!auto) Helper.overlay(false); if(!auto) swal({title: 'Temporary Error!', html: Helper.genericError('Carrying out the request.'),type: 'warning'})}); 
							};
			if(auto){
				Process(bill, index);
				return;
			}
			
			swal({
					title: 'Proceed?',
					html: 'Your action will permanently remove the item from your screen. Do you wish to proceed?',
					type: 'info',
					showCancelButton: true,
					confirmButtonColor: '#3085d6',
					cancelButtonColor: '#d33',
					confirmButtonText: 'Yes',
					customClass: 'swal-wide',
					allowOutsideClick:false
				}).then(function () {
					Process(bill, index);
				}, function(){ return;});				
		}
		
		$scope.autoListReducer = function(){
			$scope.list_travessing = true;
			if($scope.pendingGePGConfirmations != undefined && $scope.pendingGePGConfirmations.constructor === Array){
				var index = 0;
				$scope.pendingGePGConfirmations.forEach(function(bill){
					if(parseInt(bill.since_payment) >= 90)
						$scope.Processed(bill,index, true);
					index++;
				});
			}
			setTimeout($scope.autoListReducer, 1800000);//30 mins
			$scope.list_travessing = false;
		}
		
		$scope.startup = function(){
			$scope.autoListReducer();
			if($stateParams.reload){
				$scope.checkGePG();
			}
		}
		
		$scope.startup();
		//end gepg codes
        
        $http.get('/api/getUsermenu/'+user_id ).then(function(data) {
            $scope.menu=data.data;
            ////////console.log($scope.menu);
        });
        $http.get('/api/getLoginUserDetails/' + user_id).then(function (data) {
            $scope.loginUserFacilityDetails = data.data;
        });

        $scope.CreateNewProduct=function () {
            $mdDialog.show({
                controller: function ($scope) {

                    $http.get('/api/facility_address/'+facility_id).then(function(data) {
                        $scope.facility_address=data.data;
                    });

$scope.SaveNewProduct=function(product){
   if (product==undefined) {
       swal('Fill All required Fields','','info');
   }
   else if(product.item_name==undefined){
       swal('Product Name Is Required','','info');
   }
   else if(product.unit_of_measure==undefined){
       swal('Unit Of Measure(UoM) Is Required','','info');
   }
   else {
       Helper.overlay(true);
       $http.post('/api/SaveNewProduct',{item_name:product.item_name.toUpperCase(),
           item_code:product.item_code,item_category:product.item_category,
           item_sub_category:product.item_sub_category,unit_of_measure:product.unit_of_measure
       }).then(function(data) {
           $('#itemName').val('');
           $('#itemCode').val('');
           $('#uom').val('');
           $('#itemSubCategory').val('');
           $('#itemCategory').val('');
           Helper.overlay(false);
           $scope.products=data.data;
           var msg=data.data.msg;
           var status=data.data.status;
           if(status==1){
               swal(msg,'','success');
           }
           else{
               swal('something went wrong','','error');
           }
       }, function(data){Helper.overlay(false);});
   }
}

$scope.cancel = function () {
                        $mdDialog.hide();

                    };
                },
                templateUrl: '/views/modules/Drf/createNewProduct.html',
                parent: angular.element(document.body),
                clickOutsideToClose: true,
                fullscreen: false,
            });
        }
        $scope.CreateCategories=function () {
            $mdDialog.show({
                controller: function ($scope) {

                    $http.get('/api/facility_address/'+facility_id).then(function(data) {
                        $scope.facility_address=data.data;
                    });

$scope.SaveNewCategory=function(product){
   if (product==undefined) {
       swal('Fill All required Fields','','info');
   }

   else {
       Helper.overlay(true);
       $http.post('/api/SaveNewCategory',{category_name:product.category_name.toUpperCase()}).then(function(data) {
           $('#categoryName').val('');
           Helper.overlay(false);
           $scope.products=data.data;
           var msg=data.data.msg;
           var status=data.data.status;
           $scope.LoadCategories();
           if(status==1){
               swal(msg,'','success');
           }
           else{
               swal('something went wrong','','error');
           }
       }, function(data){Helper.overlay(false);});
   }
}

$scope.cancel = function () {
                        $mdDialog.hide();

                    };
                },
                templateUrl: '/views/modules/Drf/createCategories.html',
                parent: angular.element(document.body),
                clickOutsideToClose: true,
                fullscreen: false,
            });
        }

        $scope.EditCategory=function (product) {
            $mdDialog.show({
                controller: function ($scope) {

$scope.category=product;

                    $http.get('/api/facility_address/'+facility_id).then(function(data) {
                        $scope.facility_address=data.data;
                    });

$scope.EditCategory=function(product){
    Helper.overlay(true);
    $http.post('/api/EditCategory',{id:product.id,category_name:product.category_name.toUpperCase()}).then(function(data) {
        Helper.overlay(false);
        $scope.product=data.data;
        var msg=data.data.msg;
        var status=data.data.status;
        if(status==1){
            swal(msg,'','success');
        }
        else{
            swal(msg,'','error');
        }
    }, function(data){Helper.overlay(false);});
}


                    $scope.cancel = function () {
                        $mdDialog.hide();

                    };
                },
                templateUrl: '/views/modules/Drf/editCategories.html',
                parent: angular.element(document.body),
                clickOutsideToClose: true,
                fullscreen: false,
            });
        }
        $scope.EditProduct=function (product) {
            $mdDialog.show({
                controller: function ($scope) {

$scope.product=product;

                    $http.get('/api/facility_address/'+facility_id).then(function(data) {
                        $scope.facility_address=data.data;
                    });

$scope.UpdateProduct=function(product){
    Helper.overlay(true);
    $http.post('/api/SaveProductUpdate',{id:product.id,item_name:product.item_name.toUpperCase(),
        item_code:product.item_code,item_category:product.item_category,
        item_sub_category:product.item_sub_category,unit_of_measure:product.unit_of_measure}).then(function(data) {
        Helper.overlay(false);
        $scope.product=data.data;
        var msg=data.data.msg;
        var status=data.data.status;
        if(status==1){
            swal(msg,'','success');
        }
        else{
            swal(msg,'','error');
        }
    }, function(data){Helper.overlay(false);});
}


                    $scope.cancel = function () {
                        $mdDialog.hide();

                    };
                },
                templateUrl: '/views/modules/Drf/editProduct.html',
                parent: angular.element(document.body),
                clickOutsideToClose: true,
                fullscreen: false,
            });
        }

 $scope.CreatePrice=function () {
            $mdDialog.show({
                controller: function ($scope) {

                    $http.get('/api/facility_address/'+facility_id).then(function(data) {
                        $scope.facility_address=data.data;
                    });
                    $scope.LoadCategories=function(id){
                        Helper.overlay(true);
                        $http.post('/api/LoadCategories').then(function(data) {
                            Helper.overlay(false);
                            $scope.categories=data.data;
                        }, function(data){Helper.overlay(false);});
                    }
                    $scope.LoadCategories();
                    $scope.Products=function(){
                        Helper.overlay(true);
                        $http.post('/api/DrfProductsToPriceSet',{facility_id:facility_id}).then(function(data) {
                            Helper.overlay(false);
                            $scope.products=data.data;
                        }, function(data){Helper.overlay(false);});
                    }
                    $scope.Products();

                    $scope.regex=/\s/g;
$scope.SavePrice=function(product){
    console.log(product);
if (product==undefined){
    swal("","choose category",'error');
    return;
}if (product.category==undefined){
    swal("","choose category",'error');
    return;
}
    var itemss = [];
    var field_id;

    $scope.products.forEach(function(prices) {

        var item_id = prices.id;
          if($("#"+item_id).val() != ''){
            itemss.push({
                "item_code":prices.item_code,
                "item_name":prices.item_name,
                "category":product.category,
                "item_id":prices.id,
                "status":1,
                "item_price":$("#"+item_id).val().replace(',',''),
            });
            $("#"+item_id).val('');

        }

    });

    Helper.overlay(true);
    $http.post('/api/SaveProductPrice',itemss).then(function(data) {
        Helper.overlay(false);
        $scope.product=data.data;
        var msg=data.data.msg;
        var status=data.data.status;
        if(status==1){
            swal(msg,'','success');
        }
        else{
            swal(msg,'','error');
        }
    }, function(data){Helper.overlay(false);})
}

$scope.cancel = function () {
                        $mdDialog.hide();

                    };
                },
                templateUrl: '/views/modules/Drf/createProductPrice.html',
                parent: angular.element(document.body),
                clickOutsideToClose: true,
                fullscreen: false,
            });
        }

        $scope.EditPrice=function (product) {
            $mdDialog.show({
                controller: function ($scope) {

$scope.product=product;

                    $http.get('/api/facility_address/'+facility_id).then(function(data) {
                        $scope.facility_address=data.data;
                    });

$scope.UpdatePrice=function(product){
    Helper.overlay(true);
    $http.post('/api/SaveProductPriceUpdate',product).then(function(data) {
        Helper.overlay(false);
        $scope.product=data.data;
        var msg=data.data.msg;
        var status=data.data.status;
        if(status==1){
            swal(msg,'','success');
        }
        else{
            swal(msg,'','error');
        }
    }, function(data){Helper.overlay(false);});
}


                    $scope.cancel = function () {
                        $mdDialog.hide();

                    };
                },
                templateUrl: '/views/modules/Drf/editProductPrice.html',
                parent: angular.element(document.body),
                clickOutsideToClose: true,
                fullscreen: false,
            });
        }

        var Items=[];
        $scope.showSearch=function(search){
            $http.post('/api/searchDrfProduct',{searchKey:search}).then(function(data) {
                Items=data.data;
            });
            return Items;
        }
        $scope.SalesshowSearch=function(search){
            $http.post('/api/SalesshowSearch',{searchKey:search}).then(function(data) {
                Items=data.data;
            });
            return Items;
        }
$scope.stocks=[];
        $scope.AddNewStock=function(product){

            if (product==undefined) {
                swal('Fill All required Fields','','info');
                return;
            }
            else if(product.selectedItem==undefined){
                swal('Product Name Is Required','','info');
                return;
            }
            else if(product.quantity==undefined){
                swal('Quantity Is Required','','info');
                return;
            }
            else if(product.vendor_name==undefined){
                swal('Vendor Name Is Required','','info');
                return;
            }
            else if(product.invoice_number==undefined){
                swal('Invoice Number Is Required','','info');
                return;
            }
            else if(product.expiry_date==undefined){
                swal('Product Expiry Date Is Required','','info');
                return;
            }
            else if(product.unit_price==undefined){
                swal('Product  Unit Price Is Required','','info');
                return;
            }
            else if(product.batch_number==undefined){
                swal('Batch number Is Required','','info');
                return;
            } else if(product.received_date==undefined){
                swal('Product Received date required','','info');
                return;
            }
            else {
                for(var i=0;i<$scope.stocks.length;i++){

                    if($scope.stocks[i].item_id == product.selectedItem.id){ swal(product.selectedItem.item_name+" already in your order list","","info"); return;}

                }
             $scope.stocks.push({
                 'item_id':product.selectedItem.id,'item_name':product.selectedItem.item_name,'received_date':product.received_date,
                 'item_code':product.selectedItem.item_code,user_id:user_id,user_name:user_name,
                 'vendor_name':product.vendor_name.toUpperCase(),'invoice_number':product.invoice_number,
                 'quantity':product.quantity.replace(/,/g, '').replace(/[A-Za-z]/g, ''),'expiry_date':product.expiry_date,batch_number:product.batch_number,
                 'unit_price':product.unit_price.replace(/,/g, '').replace(/[A-Za-z]/g, ''),'cost_price':(product.unit_price.replace(/,/g, '').replace(/[A-Za-z]/g, '') * product.quantity.replace(/,/g, '').replace(/[A-Za-z]/g, '')),
             });
                $('#quantity').val('');
                $('#unitPrice').val('');
                $('#batchNumber').val('');
                $('#expiryDate').val('');
                $('#itemId').val('');
        }
        }
        $scope.removeStock = function(x){

            $scope.stocks.splice(x,1);

        }

$scope.sales=[];
        $scope.AddNewSale=function(product){
var auth_no="-";
var nhif_id="-";console.log(product.payment_status);
            if (product==undefined) {
                swal('Fill All required Fields','','info');
                return;
            }
            else if(product.payment_status==undefined){
                swal('Payment Method option required','','info');
                return;
            }
            else if(product.selectedItem==undefined){
                swal('Product Name Is Required','','info');
                return;
            }
            else if(product.quantity==undefined){
                swal('Quantity Is Required','','info');
                return;
            } else if(product.buyer_name==undefined){
                swal('Buyer name Is Required','','info');
                return;
            }
            else if($scope.itemPrice==undefined){
                swal('No Price TAG for This Category....','','info');
                return;
            }
else if(($scope.dispensingbalances[0].balance -product.quantity.replace(/,/g, '').replace(/[A-Za-z]/g, ''))<0)
{
   swal('Insuficient Balance....','','info');
                return;  
}
            else {
              if(product.auth_no !=undefined){
               auth_no=product.auth_no;
                }
                if(product.nhif_id !=undefined){
                    nhif_id=product.nhif_id;
                }
                for(var i=0;i<$scope.sales.length;i++){

                    if($scope.sales[i].item_id == product.selectedItem.id )
                        { swal(product.selectedItem.item_name +" already in your order list","","info"); return;}

                }

                if($scope.itemPrice[0].category=="COST SHARING" || $scope.itemPrice[0].category=="WHOLE SALE"){
                    var mult=1;

                    console.log(true)
                }
                else{
                    var mult=0;
                      console.log(false)
                }
             $scope.sales.push({
                'category':$scope.itemPrice[0].category,
                 'nhif_id':nhif_id,'auth_no':auth_no,
                 'item_id':product.selectedItem.item_id,'item_name':product.selectedItem.item_name,
                 'item_code':product.selectedItem.item_code,'user_id':user_id,'seller_name':user_name,
                 'buyer_name':product.buyer_name.toUpperCase(),
                 'mobile_number':product.mobile_number,
                 'quantity':product.quantity.replace(/,/g, '').replace(/[A-Za-z]/g, ''),
                 'balance_remained':($scope.dispensingbalances[0].balance - product.quantity.replace(/,/g, '').replace(/[A-Za-z]/g, '')),
                 'expiry_date':'***',
                 'batch_number':product.selectedItem.batch_number,
                 'payment_status':product.payment_status,
                 'unit_price':$scope.itemPrice[0].item_price.replace(/,/g, '').replace(/[A-Za-z]/g, '') ,'cost_price':($scope.itemPrice[0].item_price.replace(/,/g, '').replace(/[A-Za-z]/g, '') * product.quantity.replace(/,/g, '').replace(/[A-Za-z]/g, ''))* mult,
             });
                $('#item').val('');
                $('#quanti').val('');
              $scope.totalSalesCost=  $scope.totalSalesamount()*mult;
        }
        }
$scope.CheckBalance=function(sale){
    $scope.balanceAvailable={};
            if (sale.quantity==undefined) {
                return;
            }
$scope.balanceAvailable=($scope.dispensingbalances[0].balance -sale.quantity.replace(/,/g, '').replace(/[A-Za-z]/g, ''))
        }
        $scope.removeSales = function(x){

            $scope.sales.splice(x,1);
            $scope.totalSalesCost=  $scope.totalSalesamount();
        }

        $scope.SaveNewSale = function(){
var inputOptions = new Promise(function (resolve) {
setTimeout(function () {
resolve({
'1': 'Cash',
'2': 'GePG', 
'3': 'Insurance'
})
},10);
});

swal({
title: 'PAYMENT TYPE',
input: 'radio',
inputOptions: inputOptions,
inputValidator: function (result) {
        return new Promise(function (resolve, reject) {
        if (result) {
            resolve()
        } else {
reject('Please, Choose Payment Type')
}
   });
  }
   }).then(function (result) {
   



            swal({
                title: 'Are you sure you want to complete this transaction with a sum of '+$scope.totalSalesCost+' Tshs?',
                type: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Yes',
                allowOutsideClick: false
        }).then(function () {
             //sales saving starts

            Helper.overlay(true);
            $http.post('/api/SaveNewSale',{type:result,data:$scope.sales}).then(function(data) {

                    Helper.overlay(false);
                    $scope.salesProcessed=data.data;
                    $scope.invoice_number=data.data.invoice_number;
                    
                    //gepg code
                    if(result == 2){
                        $scope.gepgPostBill($scope.invoice_number);
                        return;
                    }                    
                    //end gepg code
                    $scope.totalSalesCost=  $scope.totalSalesamount();

                 if($scope.sales[0].category=="COST SHARING" || $scope.sales[0].category=="WHOLE SALE"){
                    var mult=1;
                    $scope.mult=1;
                }
                else{
                    var mult=0;
                     $scope.mult=0;
                      console.log(false)
                }
                $scope.totalSalesamount=function () {
                    var sum=0;
                    for (var i=0;i<$scope.sales.length;i++) {
                        sum -=-($scope.sales[i].quantity* $scope.sales[i].unit_price * mult)
                    }
                    //console.log(sum);
                    return sum;
                }

                $scope.saleses=$scope.sales;
                    $scope.reloadInvoices();

                var msg=data.data.msg;
                $scope.statement=data.data.statement;
                var status=data.data.status;
                $scope.date = new Date();
                $scope.user_name=$rootScope.currentUser.name;
                if(status==1){
                    $scope.cancel = function () {
                        $mdDialog.hide();

                    };
                    $mdDialog.show({
                        controller: DrfController,
                        scope: $scope,
                        preserveScope: true,
                        templateUrl: '/views/modules/Drf/printSalesInvoice.html',
                        clickOutsideToClose: false,
                        fullscreen: true
                    });
                }
                else{
                    swal(msg,'','error');
                }
                }, function(data){Helper.overlay(false);}
            );

            //save sales end    
            }, function (dismiss) {});



      });

        }

        $scope.getReceiptsdata = function(invoice_number){

            Helper.overlay(true);
            $http.post('/api/getReceiptDatadrf',{invoice_number:invoice_number}).then(function(data) {

                    Helper.overlay(false);
                    $scope.salesProcessed=data.data;
                    $scope.dataaa=data.data;
                    $scope.sales=data.data;
                    $scope.invoice_number=data.data[0].invoice_number;
if($scope.sales[0].category=="COST SHARING" || $scope.sales[0].category=="WHOLE SALE"){
                    var mult=1;
                    $scope.mult=1;

                    console.log(true)
                }
                else{
                    var mult=1;
                     $scope.mult=1;
                      console.log(false)
                }
                $scope.receiptCopyAmount=function () {
                    var sum=0;
                    for (var i=0;i<$scope.dataaa.length;i++) {

                        sum -=-($scope.dataaa[i].quantity* $scope.dataaa[i].unit_price)
                    }
                    //console.log(sum);
                    return sum;
                }
                $scope.totalSalesCost=  $scope.receiptCopyAmount($scope.dataaa);

                $scope.saleses=$scope.dataaa;


                var msg=data.data.msg;
                $scope.statement=data.data.statement;
                var status=data.data.status;
                $scope.date = new Date();
                $scope.user_name=$rootScope.currentUser.name;

                    $scope.cancel = function () {
                        $mdDialog.hide();

                    };
                    $mdDialog.show({
                        controller: DrfController,
                        scope: $scope,
                        preserveScope: true,
                        templateUrl: '/views/modules/Drf/printSalesInvoice.html',
                        clickOutsideToClose: false,
                        fullscreen: true
                    });

                }, function(data){Helper.overlay(false);}
            );
        }
        $scope.StockBalance = function(){

            Helper.overlay(true);
            $http.post('/api/StockBalance').then(function(data) {

                    Helper.overlay(false);
                    $scope.stockbalance=data.data;
                     $scope.dispensingBalance();

                }
                 
                , function(data){Helper.overlay(false);}
            );
        }

        $scope.dispensingBalance=function(){
            $http.post('/api/DispStockBalance').then(function(data) {

                    
                    $scope.dispstockbalance=data.data;

                });
        }
        $scope.getMedicenes = function(invoice){

            Helper.overlay(true);
            $http.post('/api/getMedicenes',{invoice:invoice}).then(function(data) {

                    Helper.overlay(false);
                    $scope.saleses=data.data;
                $scope.totalSalesCostT=  $scope.totalSalesamountT();

                if( $scope.saleses.length>0){
                    $scope.cancel = function () {
                        $mdDialog.hide();

                    };
                    $mdDialog.show({
                        controller: DrfController,
                        scope: $scope,
                        preserveScope: true,
                        templateUrl: '/views/modules/Drf/printSalesNhifDetailInvoice.html',
                        clickOutsideToClose: false,
                        fullscreen: true
                    });
                }
                }, function(data){Helper.overlay(false);}
            );
        }
  $scope.reloadkbalance = function(){

            Helper.overlay(true);
            $http.post('/api/StockBalance').then(function(data) {

                    Helper.overlay(false);
                    $scope.stockbalance=data.data;
                    $scope.dispensingBalance();

                }, function(data){Helper.overlay(false);}
            );
        }

       $scope.reloadInvoices = function(){

            Helper.overlay(true);
            $http.post('/api/reloadInvoices').then(function(data) {

                    Helper.overlay(false);
                    $scope.invoiceslists=data.data;

                }, function(data){Helper.overlay(false);}
            );
        }
        $scope.ViewInvoice = function(invoice){

            Helper.overlay(true);
            $http.post('/api/ViewInvoice',{invoice_number:invoice}).then(function(data) {

                    Helper.overlay(false);
                    $scope.invoiceDetails=data.data;
                $scope.statement=data.data.statement;
                $scope.date = new Date();
                $scope.user_name=$rootScope.currentUser.name;
                    $scope.totalCost=$scope.totalCostamount();
                $scope.cancel = function () {
                    $mdDialog.hide();
                };
                $mdDialog.show({
                    controller: DrfController,
                    scope: $scope,
                    preserveScope: true,
                    templateUrl: '/views/modules/Drf/ViewInvoiceDetail.html',
                    clickOutsideToClose: false,
                    fullscreen: true
                });

                }, function(data){Helper.overlay(false);}
            );
        }

        $scope.reloadInvoices();
        $scope.StockBalance();
        $scope.LoadStockExpires = function(dated){

            Helper.overlay(true);
            $http.post('/api/LoadStockExpires',dated).then(function(data) {

                    Helper.overlay(false);
                    $scope.expires=data.data;

                }, function(data){Helper.overlay(false);}
            );
        }
        $scope.LoadStockDetails = function(dated){

            Helper.overlay(true);
            $http.post('/api/LoadStockDetails',dated).then(function(data) {

                    Helper.overlay(false);
                    $scope.stockdetails=data.data;

                }, function(data){Helper.overlay(false);}
            );
        }
        $scope.LoadStockDetails();
        $scope.StockIssued = function(dated){

            Helper.overlay(true);
            $http.post('/api/LoadStockIssuedDetails',dated).then(function(data) {
                    Helper.overlay(false);
                    $scope.stockissued=data.data;

                }, function(data){Helper.overlay(false);}
            );
        }
        $scope.LoadFinanceDetails = function(dated,level){
if(dated==undefined){
   var start_date="";
    var end_date="";
}
else{
   var  start_date=dated.start_date;
   var end_date=dated.end_date;
}
            Helper.overlay(true);
            $http.post('/api/LoadFinanceDetails',{user_id:user_id,start_date:start_date,end_date:end_date,level:level}).then(function(data) {
                    Helper.overlay(false);
                    $scope.finances=data.data[0];
                    $scope.graphs=data.data[1];
                    $scope.financesReceipts=data.data[2];
                     $scope.employees=data.data[3];
                $scope.totalEmployee=$scope.totalEmployees();
                $scope.totalFinanceCost=$scope.totalFinanceCostData();

                $scope.xs = [];
                $scope.ys = [];

                for(var i=0;i< $scope.graphs.length; i++){
                    $scope.xs.push($scope.graphs[i].month_value);
                    $scope.ys.push($scope.graphs[i].Amount);
                }

                $scope.labels=$scope.xs ;
                $scope.data =  $scope.ys;

                }, function(data){Helper.overlay(false);}
            );
        }
        $scope.LoadFinanceDebts = function(dated){

            Helper.overlay(true);
            $http.post('/api/LoadFinanceDebts',dated).then(function(data) {
                    Helper.overlay(false);
                    $scope.financesDepts=data.data;
                $scope.totalDebtCost=$scope.totalDebtCostData();


                }, function(data){Helper.overlay(false);}
            );
        }
        $scope.LoadFinanceNHIF = function(dated){

            Helper.overlay(true);
            $http.post('/api/LoadFinanceNHIF',dated).then(function(data) {
                    Helper.overlay(false);
                    $scope.financesNHIF=data.data;
                $scope.totalDebtCost=$scope.totalDebtCostDataNHIF();


                }, function(data){Helper.overlay(false);}
            );
        }
        $scope.StockIssued();
        $scope.SaveNewStock=function(){

 Helper.overlay(true);
            $http.post('/api/SaveNewStock',$scope.stocks).then(function(data) {

                    Helper.overlay(false);
                $scope.stocks=[];
                $scope.StockBalance();

                    var msg=data.data.msg;
                    var status=data.data.status;
                    if(status==1){
                        swal(msg,'','success');
                    }
                    else{
                        swal(msg,'','error');
                    }
                }, function(data){Helper.overlay(false);}
            );

        }


        $scope.Products=function(){
            Helper.overlay(true);
            $http.post('/api/DrfProducts',{facility_id:facility_id}).then(function(data) {
                Helper.overlay(false);
                $scope.products=data.data;
            }, function(data){Helper.overlay(false);});
        }
        $scope.DeleteProduct=function(product){
            Helper.overlay(true);
            $http.post('/api/DeleteProduct',product).then(function(data) {
                Helper.overlay(false);
                $scope.products=data.data;
            }, function(data){Helper.overlay(false);});
        }

        $scope.ProductPrice=function(){
            Helper.overlay(true);
            $http.post('/api/DrfPrices',{facility_id:facility_id}).then(function(data) {
                Helper.overlay(false);
                $scope.prices=data.data;
            }, function(data){Helper.overlay(false);});
        }
        $scope.LoadPriceTag=function(id,category){
            Helper.overlay(true);
            $http.post('/api/LoadPriceTag',{id:id.item_id,category:category}).then(function(data) {
                Helper.overlay(false);
                if(data.data.length==0){
                    swal("error","No Price Tag for this Item For "+"SELECTED"+",   SET PRICE FOR THIS CATEGORY FIRST BEFORE CONTINUING","error");
                    return;
                }
                $scope.itemPrice=data.data;
            }, function(data){Helper.overlay(false);});
        }
        $scope.LoadCategories=function(id){
            Helper.overlay(true);
            $http.post('/api/LoadCategories').then(function(data) {
                Helper.overlay(false);
                $scope.categories=data.data;
            }, function(data){Helper.overlay(false);});
        }
        $scope.LoadCategories();
$scope.LoadBatchbalance=function(id){
            Helper.overlay(true);
            $http.post('/api/LoadBatchbalance',{id:id.item_id}).then(function(data) {
                Helper.overlay(false);
                $scope.batches=data.data;
            }, function(data){Helper.overlay(false);});
        }

$scope.LoadItemDispensingbalance=function(id,category){
            Helper.overlay(true);

            $http.post('/api/LoadItemDispensingbalance',{id:id.item_id, batch_number: id.batch_number}).then(function(data) {
                Helper.overlay(false);

                $scope.dispensingbalances=data.data;
                $scope.LoadPriceTag(id,category);
            }, function(data){Helper.overlay(false);});
        }

        $scope.freezeInvoice=function(inv_number){


            swal({
                title: 'sure?',
                text: "You want to FREEZE invoice # "+inv_number+" ?",
                type: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Yes!',
                cancelButtonText: 'No, cancel!',
                confirmButtonClass: 'btn btn-success',
                cancelButtonClass: 'btn btn-danger',
                buttonsStyling: false
            }).then(function () {

                Helper.overlay(true);
                $http.post('/api/freezeInvoice',{invoice_number:inv_number}).then(function(data) {
                    Helper.overlay(false);
                    $scope.reloadInvoices();
                    var msg=data.data.msg;
                    var status=data.data.status;
                    if(status==1){
                        swal(msg,'','success');
                    }
                    else{
                        swal(msg,'','error');
                    }
                }, function(data){Helper.overlay(false);});



            }, function (dismiss) {
                // dismiss can be 'cancel', 'overlay',
                // 'close', and 'timer'
                if (dismiss === 'cancel') {

                }
            })

        }
        $scope.ClearBilledInvoice=function(inv_number){


            swal({
                title: 'sure?',
                text: "You want to CLEAR BILL of  invoice # "+inv_number+" ?",
                type: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Yes!',
                cancelButtonText: 'No, cancel!',
                confirmButtonClass: 'btn btn-success',
                cancelButtonClass: 'btn btn-danger',
                buttonsStyling: false
            }).then(function () {

                swal({
                    title: 'Payment Details',
                    html:
                    '<input id="swal-input1" class="swal2-input" placeholder="Enter Amount Paid e.g 15,000,00">' +
                    '<input id="swal-input2" class="swal2-input" placeholder="Enter PaySlip Number e.g 18000837">' +
                    '<input id="swal-input3" class="swal2-input" placeholder="Enter Agent Name e.g NMB,CRDB BANK,M-PESA">'+
                    '<input id="swal-input4" class="swal2-input" placeholder="Enter Payer Name e.g GoT-HOMIS HEALTH CENTER">',
                    preConfirm: function () {
                        return new Promise(function (resolve) {
                            resolve([
                                $('#swal-input1').val(),
                                $('#swal-input2').val(),
                                $('#swal-input3').val(),
                                $('#swal-input4').val()
                            ])
                        })
                    },
                    onOpen: function () {
                        $('#swal-input1').focus()
                    }
                }).then(function (result) {
                     var payment =JSON.stringify({cost_amount:result[0],payslip:result[1],payment_agent_name:result[2].toUpperCase(),payer_name:result[3].toUpperCase(),invoice_number:inv_number,cost:$scope.totalCostamount()});
                    Helper.overlay(true);
                    $http.post('/api/ClearBilledInvoice',payment).then(function(data) {
                        Helper.overlay(false);
                        $scope.reloadInvoices();
                        var msg=data.data.msg;
                        var status=data.data.status;
                        $scope.statement=data.data.statement;
                        if(status==1){
                            swal(msg,'','success');
                        }
                        else{
                            swal(msg,'','error');
                        }
                    }, function(data){Helper.overlay(false);});


                }).catch(swal.noop);

            }, function (dismiss) {
                // dismiss can be 'cancel', 'overlay',
                // 'close', and 'timer'
                if (dismiss === 'cancel') {

                }
            })

        }
        $scope.totalCostamount=function () {
         var sum=0;
         for (var i=0;i<$scope.invoiceDetails.length;i++) {
             sum -=-($scope.invoiceDetails[i].quantity* $scope.invoiceDetails[i].unit_price)
         }
         //console.log(sum);
         return sum;
        }

 $scope.totalSalesamount=function () {
         var sum=0;
         for (var i=0;i<$scope.sales.length;i++) {
             sum -=-($scope.sales[i].quantity* $scope.sales[i].unit_price)
         }
         //console.log(sum);
         return sum;
        }
        $scope.totalSalesamountT=function () {
         var sum=0;
         for (var i=0;i<$scope.saleses.length;i++) {
             sum -=-($scope.saleses[i].quantity* $scope.saleses[i].unit_price)
         }
         //console.log(sum);
         return sum;
        }
        $scope.totalFinanceCostData=function () {
         var sum=0;
         for (var i=0;i<$scope.finances.length;i++) {
             sum -=-($scope.finances[i].quantity* $scope.finances[i].unit_price)
         }
         //console.log(sum);
         return sum;
        }

 $scope.totalDebtCostData=function () {
         var sum=0;
         for (var i=0;i<$scope.financesDepts.length;i++) {
             sum -=-($scope.financesDepts[i].cost)
         }
         //console.log(sum);
         return sum;
        }

$scope.totalDebtCostDataNHIF=function () {
         var sum=0;
         for (var i=0;i<$scope.financesNHIF.length;i++) {
             sum -=-($scope.financesNHIF[i].cost)
         }
         //console.log(sum);
         return sum;
        }


        $scope.PrintContent=function () {
            $scope.sales=[];
            //location.reload();
            var DocumentContainer = document.getElementById('invoice_id');
            var WindowObject = window.open("", "PrintWindow",
                "width=900,height=700,top=50,left=450,toolbars=no,scrollbars=no,status=no,resizable=yes");
            WindowObject.document.title = "printout: GoT-HOMIS";
            WindowObject.document.writeln(DocumentContainer.innerHTML);
            WindowObject.document.close();

            setTimeout(function () {
                WindowObject.focus();
                WindowObject.print();
                WindowObject.close();
            });

        }
        $scope.PrintBalance=function () {
            $scope.sales=[];
            //location.reload();
            var DocumentContainer = document.getElementById('balance_id');
            var WindowObject = window.open("", "PrintWindow",
                "width=900,height=700,top=50,left=450,toolbars=no,scrollbars=no,status=no,resizable=yes");
            WindowObject.document.title = "printout: GoT-HOMIS";
            WindowObject.document.writeln(DocumentContainer.innerHTML);
            WindowObject.document.close();

            setTimeout(function () {
                WindowObject.focus();
                WindowObject.print();
                WindowObject.close();
            });

        }
        $scope.PrintDetailedStock=function () {
            $scope.sales=[];
            //location.reload();
            var DocumentContainer = document.getElementById('detail_id');
            var WindowObject = window.open("", "PrintWindow",
                "width=900,height=700,top=50,left=450,toolbars=no,scrollbars=no,status=no,resizable=yes");
            WindowObject.document.title = "printout: GoT-HOMIS";
            WindowObject.document.writeln(DocumentContainer.innerHTML);
            WindowObject.document.close();

            setTimeout(function () {
                WindowObject.focus();
                WindowObject.print();
                WindowObject.close();
            });

        }
        $scope.PrintDetailedexpired=function () {

            //location.reload();
            var DocumentContainer = document.getElementById('expired_id');
            var WindowObject = window.open("", "PrintWindow",
                "width=900,height=700,top=50,left=450,toolbars=no,scrollbars=no,status=no,resizable=yes");
            WindowObject.document.title = "printout: GoT-HOMIS";
            WindowObject.document.writeln(DocumentContainer.innerHTML);
            WindowObject.document.close();

            setTimeout(function () {
                WindowObject.focus();
                WindowObject.print();
                WindowObject.close();
            });

        }
$scope.PrintIssuedStock=function () {
            $scope.sales=[];
            //location.reload();
            var DocumentContainer = document.getElementById('issued_id');
            var WindowObject = window.open("", "PrintWindow",
                "width=900,height=700,top=50,left=450,toolbars=no,scrollbars=no,status=no,resizable=yes");
            WindowObject.document.title = "printout: GoT-HOMIS";
            WindowObject.document.writeln(DocumentContainer.innerHTML);
            WindowObject.document.close();

            setTimeout(function () {
                WindowObject.focus();
                WindowObject.print();
                WindowObject.close();
            });

        }
$scope.PrintFiananceDetail=function () {
            $scope.sales=[];
            //location.reload();
            var DocumentContainer = document.getElementById('financed_id');
            var WindowObject = window.open("", "PrintWindow",
                "width=900,height=700,top=50,left=450,toolbars=no,scrollbars=no,status=no,resizable=yes");
            WindowObject.document.title = "printout: GoT-HOMIS";
            WindowObject.document.writeln(DocumentContainer.innerHTML);
            WindowObject.document.close();

            setTimeout(function () {
                WindowObject.focus();
                WindowObject.print();
                WindowObject.close();
            });

        }
$scope.PrintFiananceDebt=function () {
            $scope.sales=[];
            //location.reload();
            var DocumentContainer = document.getElementById('financedEPT_id');
            var WindowObject = window.open("", "PrintWindow",
                "width=900,height=700,top=50,left=450,toolbars=no,scrollbars=no,status=no,resizable=yes");
            WindowObject.document.title = "printout: GoT-HOMIS";
            WindowObject.document.writeln(DocumentContainer.innerHTML);
            WindowObject.document.close();

            setTimeout(function () {
                WindowObject.focus();
                WindowObject.print();
                WindowObject.close();
            });

        }

$scope.PrintFiananceEmployeeDetail=function () {
            $scope.sales=[];
            //location.reload();
            var DocumentContainer = document.getElementById('employee_id');
            var WindowObject = window.open("", "PrintWindow",
                "width=900,height=700,top=50,left=450,toolbars=no,scrollbars=no,status=no,resizable=yes");
            WindowObject.document.title = "printout: GoT-HOMIS";
            WindowObject.document.writeln(DocumentContainer.innerHTML);
            WindowObject.document.close();

            setTimeout(function () {
                WindowObject.focus();
                WindowObject.print();
                WindowObject.close();
            });

        }
         

        $scope.totalEmployees=function () {
         var sum=0;
         for (var i=0;i<$scope.employees.length;i++) {
             sum -=-($scope.employees[i].total)
         }
         //console.log(sum);
         return sum;
        }


//stockinssuing

$scope.stocksissue=[];
        $scope.DrfIssuingItem=function(product){
 
            if (product==undefined) {
                swal('Fill All required Fields','','info');
                return;
            }
            else if(product.selectedItem==undefined){
                swal('Product Name Is Required','','info');
                return;
            }
            else if(product.selectedBatch==undefined){
                swal('Batch number Is Required','','info');
                return;
            }  
            else if(product.receiver_name==undefined){
                swal('receiver name Is Required','','info');
                return;
            }  
            else if((product.selectedBatch.balance-product.quantity.replace(/,/g, '').replace(/[A-Za-z]/g, ''))<0 ){
                swal('Insuficient Stock Balance','','info');
                 // $('#quantity').val('');
                return;
            }
            else {
                for(var i=0;i<$scope.stocksissue.length;i++){

                    if($scope.stocksissue[i].item_id == product.selectedItem.id && $scope.stocksissue[i].batch_number==product.selectedBatch.batch_number )
                        { swal(product.selectedItem.item_name+" already in your order list","","info");
                         return;
                     }
 
                }
             $scope.stocksissue.push({
                 'item_id':product.selectedItem.id,'item_name':product.selectedItem.item_name, 
                 'item_code':product.selectedItem.item_code,user_id:user_id,user_name:user_name, 
                 'receiver_name':product.receiver_name,
                 'quantity':product.quantity.replace(/,/g, '').replace(/[A-Za-z]/g, ''),
                 'expiry_date':product.selectedBatch.expiry_date,batch_number:product.selectedBatch.batch_number,
                
             });
             console.log($scope.stocksissue);
                $('#quantity').val('');
                $('#batchNumber').val('');
                $('#expiryDate').val('');
                $('#item').val('');
        }
        }
        $scope.removeIssuing = function(x){

            $scope.stocksissue.splice(x,1);

        }

         $scope.DrfIssuing=function(){

 Helper.overlay(true);
            $http.post('/api/DrfIssuing',$scope.stocksissue).then(function(data) {

                    Helper.overlay(false);
                $scope.stocksissue=[];
                $scope.StockBalance();

                    var msg=data.data.msg;
                    var status=data.data.status;
                    if(status==1){
                        swal(msg,'','success');
                    }
                    else{
                        swal(msg,'','error');
                    }
                }, function(data){Helper.overlay(false);}
            );

        }

         $scope.regex=/\s/g;
        $scope.SaveReconsilation=function(reason){

            var itemss = [];
            var field_id;
if (reason==undefined){
    swal("Enter Reconciliation Reasons",'','info');
    return;
}
            $scope.batches.forEach(function(prices) {

                var item_id = prices.id;
                if($("#"+item_id).val() != ''){
                    itemss.push({
                        "column_id":prices.id,
                        "old_balance":prices.balance,
                        "old_quantity":prices.quantity,
                        "batch_number":prices.batch_number,
                        "item_id":prices.item_id,
                        "user_id":user_id,
                        "reason":reason, 
                        "current_quantity":$("#"+item_id).val().replace(/,/g, '').replace(/[A-Za-z]/g, ''),
                    });
                    $("#"+item_id).val('');


                }

            });
if (itemss.length==0){

    return;
}

             Helper.overlay(true);
            $http.post('/api/drf_stock_reconsilliation',itemss).then(function(data) {
                $("#"+reason).val('');
               // $scope.item_consiliates = data.data;
                Helper.overlay(false);
                var msg=data.data.msg;
                var status=data.data.status;
                $scope.getStockReconcilliated();

                    swal("Reconciliation Successful done",'','success');


            }, function(data){Helper.overlay(false);})


        }

$scope.returnStockReconcilliated=function(recon){
     
            Helper.overlay(true);
            $http.post('/api/drfreconcilliationReturn',{recon}).then(function(data) {
                    Helper.overlay(false);
                     $scope.getStockReconcilliated();
                    

                }, function(data){Helper.overlay(false);}
            );
    }

    $scope.getStockReconcilliated=function(dated){
    if(dated==undefined){
   var start_date="";
    var end_date="";
}
else{
   var  start_date=dated.start_date;
   var end_date=dated.end_date;
}
            Helper.overlay(true);
            $http.post('/api/drfreconcilliationReport',{start_date:start_date,end_date:end_date}).then(function(data) {
                    Helper.overlay(false);
                    $scope.reconsiled_records=data.data; 
                }, function(data){Helper.overlay(false);}
            );
    }

    $scope.getGepGPendings=function(){

            Helper.overlay(true);
            $http.post('/api/getGepGPendings').then(function(data) {
                    Helper.overlay(false);
                    $scope.gepgpending=data.data;
                    

                }, function(data){Helper.overlay(false);}
            );
    }

    $scope.CancelGepGPendings=function(invoice_number){
swal({
                title: 'Are you sure you want to CANCEL this transaction',
                type: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Yes',
                allowOutsideClick: false
        }).then(function () {
               Helper.overlay(true);
            $http.post('/api/CancelGepGPendings',{invoice_number:invoice_number}).then(function(data) {
                    Helper.overlay(false);
                  $scope.getGepGPendings();
                    

                }, function(data){Helper.overlay(false);}
            );  
            }, function (dismiss) {});

            
    }

    $scope.getGepGPendings();
}

})();
/**
 * Created by USER on 2017-03-08.
 */