/**
 * Created by Mazigo Jr on 2017-07-20.
 */
(function () {
    'use strict';
    angular
        .module('authApp')
        .controller('shopController',shopController);
    function shopController($scope,$state,$http,$rootScope,$uibModal,$mdDialog,toastr,Helper,$timeout,$stateParams) {

        var facility_id = $rootScope.currentUser.facility_id;
        var user_id=$rootScope.currentUser.id;
		$scope.paymentMethod = '';
		$scope.regex = /\B(?=(\d{3})+(?!\d))/g;
		$scope.list_travessing = false;
		
		//gepg codes
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
						$http.post('/gepg/new/resendBill', {facility_id:facility_id,BillId: bill.BillId}).then(function(response){
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
				$http.post('/gepg/new/printBill', {facility_id:facility_id, BillId: bill.BillId}).then(function(response){
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
					if(!$('#amount').val() || !$('#date').val()){
						swal('You must specify amount and date','','info');
						return;
					}
					
					var bill = {
						facility_id: facility_id,
						UserId:$rootScope.currentUser.id,
						UserName:$rootScope.currentUser.name,
						InvoiceId:0,
						InvoiceLine: {
							BillDescription:$('#transaction').val(),
							BillAmount:$('#amount').val().replace(/,/g, ""),
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
									setTimeout(checking, 120000);//2mins
								}
							}, function(error, status){ Helper.overlay(false); swal({title: 'Temporary Error!', html: Helper.genericError('GePG Payments Reconcilliation'),type: 'error', allowOutsideClick: false});});
						};
				
			Process();
		}
		
		$scope.gepgPostBill = function(invoice_id){
			var bill = {
					facility_id: facility_id,
					UserId:$rootScope.currentUser.id,
					UserName:$rootScope.currentUser.name,
					InvoiceId:invoice_id,
					InvoiceLine: {
						BillDescription:'Hospital Bill',
						BillAmount:$scope.getTotal(),
						CashDeposit:0,
						PayerName:$scope.selectedPatient.name.split('#')[0],
						PayerId:$scope.selectedPatient.name.split('#')[1],
						PayerPhone:$scope.selectedPatient.mobile_number ? $scope.selectedPatient.mobile_number.toString().replace(/\+\s/g,'') : '',
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
															$http.post('/gepg/new/rollback', {invoice_id: invoice_id}).then(function(response){
																//TODO
															}, function(error, status){
																//TODO
															});
														};
										Rollback(invoice_id);
									}else{
										$scope.itemData = [];
										$scope.toto = $scope.getTotal();
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
							$http.post('/gepg/new/cancel_bill',{facility_id: facility_id, BillId:bill.BillId, CashDeposit: parseInt(bill.CashDeposit), InvoiceId: bill.InvoiceId, user_id: user_id, reason:reason}).then(function(response){
								Helper.overlay(false);
								swal({title:'CANCEL BILL REQUEST',
									html:response.data.generic+(response.data.success==0 ? '<br /><span style="color:red">'+response.data.real+'</span>' : ''),
									type:'info',
									customClass: 'swal-wide',
									allowOutsideClick:false
								});
								if(response.data.success==1)
									$scope.pendingGePGConfirmations.splice(index,1);
							},function(error, status){Helper.overlay(false); swal({title: 'Temporary Error!', html: Helper.genericError('Cancelling GePG Bill'),type: 'error'});});
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
			if($state.current.name == 'payments' || $state.current.name == 'point_of_sale' ||  $state.current.name == 'shop'){
				Helper.overlay(true);
				$http.post('/gepg/new/pending_bills', {facility_id: facility_id}).then(function (response) {
					Helper.overlay(false);
					if(response.data.success != undefined && response.data.success==0 && response.data.account==-1)
						return;//no gepg account detected
					if(response.data.constructor === Array && response.data.length != 0){
						$scope.pendingGePGConfirmations = response.data;
					}
					
					if(!$scope.running)
						$scope.running = true;
				}, function(error, status){Helper.overlay(false);});
			}else
				return;
		}
		
		$scope.GePGReceipt = function(bill){
			if(bill.Paid == 0 || bill.PspReceiptNumber == null)
				return;
			
			Helper.overlay(true);
			$http.post('/gepg/new/getGePGPaidBill',{gepg_receipt:bill.PspReceiptNumber})
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
		
		$scope.Processed = function(bill,index){
			if(bill.Paid == 0 || bill.PspReceiptNumber == null)
				return;
			
			var Process = function(bill, index){
								Helper.overlay(true);
								$http.post('/gepg/new/mark_processed_bill',{facility_id: facility_id, BillId:bill.BillId, CashDeposit: parseInt(bill.CashDeposit), InvoiceId: bill.InvoiceId, user_id: user_id}).then(function (response) {
									Helper.overlay(false);
									if(response.data.success == 1 && $scope.pendingGePGConfirmations.constructor === Array)
										$scope.pendingGePGConfirmations.splice(index,1)
								},function(error, status){Helper.overlay(false); swal({title: 'Temporary Error!', html: Helper.genericError('Carrying out the request.'),type: 'warning'})}); 
							};
					
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
		
		
		
		$scope.startup = function(){
			if($stateParams.reload){
				$scope.checkGePG();
			}
		}
		
		$scope.startup();
		//end gepg codes
		
        $http.get('/api/getUsermenu/'+user_id ).then(function(data) {
            $scope.menu=data.data;
        });
        //Patients to PoS
        var resdata =[];
        $scope.showSearch = function(searchKey) {
            $http.post('/api/patientsToPoS',{"search":searchKey,"facility_id":facility_id}).then(function(data) {
                resdata = data.data;
            });
            return resdata;
        }
        var PoSata =[];
        $scope.searchItems = function(searchKey,patient) {
			 
			if(patient.sub_category_name=="NHIF"){
				var category_id =patient.patient_category_id;
			}
			else{
				var category_id =10;

			}

            $http.post('/api/itemsToShop',{"search":searchKey,"patient_category_id":category_id,"facility_id":facility_id}).then(function(data) {
                PoSata = data.data;
            });
            return PoSata;
        }

        $scope.itemData = [];
        var balance = [];
        $scope.addItem = function(item,x,quantity,selectedBatch){
        	$scope.item_issue.selectedBatch="";
			if(item.sub_category_name=="NHIF")
			{
			var	payment_filter=item.patient_category_id;
				 var payment_status = 1;
				 var pay_cat_id =item.patient_category_id;
			}
			else{
			var	payment_filter=10
				 var payment_status = 2;
				 var pay_cat_id = 10;
			}

           // var payment_filter = item.patient_category_id;
            //var main_category_id = item.main_category_id;

            if(item == null || x==null || quantity ==null){
                swal("Please search Patient and Item the click 'Add' Button"); return;
            }
            for(var i=0;i<$scope.itemData.length;i++)
                if($scope.itemData[i].item_id == x.item_id && $scope.itemData[i].batch_no== selectedBatch.batch_no){
                    swal(x.item_name+" already in your order list!","","info");
                    $scope.selectedItem= "";
                    $scope.quantity= "";
                    return;}
            var sub_total = x.price * quantity;

                    if(selectedBatch.batch_no == undefined){
                        swal(x.item_name +' is not available in store.','Contact store manager','info');
                        $scope.selectedItem= "";
                        $scope.quantity= "";
                        $scope.item_issue.selectedBatch= "";
                        return;
                    }
                    else if(selectedBatch.batch_no !=undefined){
                        $scope.itemData.push({hospital_shop_posting:true,"dept_id":x.dept_id,"item_id":x.item_id,"item_name":x.item_name,"sub_total":sub_total,"receipt_number":"","item_type_id":x.item_type_id,"quantity":quantity,"price":x.price,"item_price_id":x.price_id,
							"chf_toto": 0,
                            "chf_use":0,
                            "user_id":user_id,"patient_id":item.patient_id,"medical_record_number":item.medical_record_number,"account_number":item.account_number,"account_number_id":item.account_id,"first_name":item.first_name,"middle_name":item.middle_name,"last_name":item.last_name,
                            "status_id":payment_status,"sub_category_name":item.sub_category_name,"payment_filter":payment_filter,"facility_id":facility_id,"discount":0,"discount_by":user_id
							,
							batch_no:selectedBatch.batch_no,order_id:selectedBatch.id,balance_available:selectedBatch.quantity,
			received_from_id:selectedBatch.received_from_id,store_id:100
							});
                        $scope.toto = $scope.getTotal();
                        $scope.selectedItem= "";
                        $scope.quantity= "";
                        $scope.item_issue.selectedBatch= "";
                        return;
                    }
                    else if(selectedBatch.batch_no ==undefined){
                        swal('Quantity for '+ x.item_name +' is not sufficient in store.','info');
                        $scope.selectedItem= "";
                        $scope.quantity= "";
                        $scope.item_issue.selectedBatch= "";
                        return;
                    }





        }
		
			
		//--------------------------balancing stock in hospital_shop_-------------------------------------
		//loading item batches from function ShowItem above
        $scope.loadBatch=function (item_id) {

            $http.get('/api/batchdispensing_list/'+item_id+','+user_id).then(function(data) {
                $scope.batches=data.data;
                ////////////console.log(data.data);

            });
        }
		
		
		$scope.batchesbalances="";
        //loading item batches balance from function loadBatchabove
         $scope.loadBatchBalance=function (batch_number,store_id,$item_id) {

            $http.get('/api/loaddispensingBatchBalance/'+batch_number+','+store_id+','+$item_id).then(function(data) {
                $scope.batchesbalances=data.data;
                ////////console.log(data.data)
            });
        }
//-----------------------------balancing stock in hospital_shop_----------------------------------
	
		
        $scope.removeItem = function(item){

            var indexofItem = $scope.itemData.indexOf(item);
            $scope.itemData.splice(indexofItem,1);
            $scope.toto = $scope.getTotal();

        }

        $scope.getTotal = function () {
            var  total = 0;
            for(var i = 0; i < $scope.itemData.length ; i++) {
                total += ($scope.itemData[i].sub_total);
            }
            return total;
        }
        $scope.processSales = function (paymentMethod) {
            var x= $scope.getTotal();
			
			if($scope.itemData[0].sub_category_name != "NHIF" && paymentMethod != 'gepg' && $scope.configuredOption == 'gepg' && x !==0){
				swal('Bills must be paid by GePG','','info');
				return;
			}
			
            swal({
                    title: 'Are you sure you want to complete this transaction with a sum of '+x+' Tshs?',
					type: 'warning',
					showCancelButton: true,
					confirmButtonColor: '#3085d6',
					cancelButtonColor: '#d33',
					confirmButtonText: 'Yes'
            }).then(function () {
                var receiptNumber="";
				//gepg codes snippet
				if(paymentMethod == 'gepg' && x == 0){//send to gepg only valid bills
					swal('GePG bills must be payable','','info');
					return;
				}else if(paymentMethod == 'gepg'){//turn all bills unpaid in order to post to gepg and wait for payment
					for(var i=0; i < $scope.itemData.length; i++){
						$scope.itemData[i].status_id = 1;
                        $scope.itemData[i]['payment_method_id'] = 2;
                    }
				}
				//end gepg code snippet
				else if(paymentMethod == 'cash'){//append payment method id and method name for the receipt
					for(var i=0; i < $scope.itemData.length; i++)
						$scope.itemData[i]['payment_method_id'] = 1;
				}
				$http.post('/api/saveFromPoS',$scope.itemData).then(function (data) {
					if(data.data.status==0){
						swal(data.data.data,'','error');
					}
					else {
						receiptNumber=data.data;
						if(paymentMethod == 'cash'){//load receipt for cash payments
							for(var i=0;i<$scope.itemData.length;i++){
								$scope.itemData[i]["receipt_number"] = receiptNumber;
							}
							var object = $scope.itemData;
							var modalInstance = $uibModal.open({
								templateUrl: '/views/modules/payments/posReceipts.html',
								size: 'lg',
								animation: true,
								controller: 'posReceipts',
								resolve:{
									object: function () {
										return object;
									}
								}

							});
							$scope.itemData = [];
							$scope.toto = $scope.getTotal();
						}else{//gepg codes snippet
							$scope.gepgPostBill(receiptNumber);
						}
					}
				});
            }, function (dismiss) {});

        }

		//pharmacy sub store _report types

		$scope.reports=[{'id':1,'balance':"Store Balance"},{'id':2,'balance':"Detailed Report"},{'id':3,'balance':"Dispensed Items"}];

		$scope.pharmacy_report=function (report_type,date) {
			if(date==undefined){
				var	dataaa={
					start_date:'0000-00-00',
					end_date:'0000-00-00',
				};
				var rec={facility_id:facility_id,user_id:user_id,report_type:report_type,datee:dataaa};
			}
			else{
				var rec={facility_id:facility_id,user_id:user_id,report_type:report_type,datee:date};
			}

			$http.post('/api/item_balances_list_in_dispensing',rec).then(function (data) {
				if (data.data.length > 0) {
					$scope.items = data.data;
					$scope.xs = [];
					$scope.ys = [];
				}
				for(var i=0;i< $scope.items.length; i++){
					$scope.xs.push($scope.items[i].item_name);
					$scope.ys.push($scope.items[i].quantity_received);
				}

				// $scope.labels=$scope.xs ;
				// $scope.data =  $scope.ys;


				$scope.onClick = function (points, evt) {
					console.log(points, evt);
				};
				$scope.datasetOverride = [{ yAxisID: 'y-axis-1' }, { yAxisID: 'y-axis-2' }];
				$scope.options = {
					scales: {
						yAxes: [
							{
								id: 'y-axis-1',
								type: 'linear',
								display: true,
								position: 'left'
							},
							{
								id: 'y-axis-2',
								type: 'linear',
								display: true,
								position: 'right'
							}
						]
					}
				};

			});
		}

        $http.get('/api/Dispensing_stores_List/'+user_id).then(function(data) {
            $scope.dispensing_stores=data.data;

        });

        $http.get('/api/Sub_main_stores_List/' + user_id).then(function (data) {
            $scope.Sub_Main_stores = data.data;

        });
//codes to display items in pharmacy stores...received items.
        $scope.showSearchItem = function(searchKey) {


            $http.post('/api/searchItemReceived',{searchKey:searchKey}).then(function(data) {
                resdata = data.data;
            });
            return resdata;

        }

        $scope.dispensing_array_requisitions=[];
        $scope.verification_array_requisitions=[];
        $scope.dispensing_item_list_requisition=function (order) {

            if (order.selectedItem == undefined) {
                swal(
                    'Error',
                    'Choose Item First..',
                    'error'
                )
            } else if (order.selectedItem.item_id == undefined) {
                swal(
                    'Error',
                    'Choose Item First..',
                    'error'
                )
            }
            else if (order.selectedStoreRequestReceiver == undefined) {
                swal(
                    'Error',
                    'Choose Store Name you want To send this order request',
                    'error'
                )
            } else if (order.selectedStoreRequestSender == undefined) {
                swal(
                    'Error',
                    'Choose Store Name where order request is Coming from',
                    'error'
                )
            }
            else if ( order.quantity == undefined) {
                swal(
                    'Error',
                    'Enter Quantity you want to order',
                    'error'
                )
            }
            else if (order.selectedStoreRequestSender.id==order.selectedStoreRequestReceiver.id) {
                swal(
                    'Error',
                    'You can not Request item From similar Store',
                    'error'
                )
            }
            else if ( order.quantity<0) {
                swal(
                    'Error',
                    'Quantity Can not be a negative value .. Please enter a Possitive number',
                    'error'
                )
            }
            else{
                for(var i=0;i<$scope.dispensing_array_requisitions.length;i++){


                    if(
                        $scope.dispensing_array_requisitions[i].item_id == order.selectedItem.item_id &&
                        $scope.dispensing_array_requisitions[i].request_receiver == order.selectedStoreRequestReceiver.id
                    )
                    { swal(order.selectedItem.item_name+" already in your order list","","info"); return;}
                }

                $scope.dispensing_array_requisitions.push({
                    'item_id': order.selectedItem.item_id,
                    'item_name': order.selectedItem.item_name,
                    'quantity': order.quantity,
                    'user_id':user_id,
                    'facility_id':facility_id,
                    'request_sender': order.selectedStoreRequestSender.id,
                    'from': order.selectedStoreRequestSender.store_name,
                    'request_receiver': order.selectedStoreRequestReceiver.id,
                    'receiver_name': order.selectedStoreRequestReceiver.store_name,
                    'store_name': order.selectedStoreRequestReceiver.store_name,
                    'request_receiver_type': order.selectedStoreRequestReceiver.store_type_id,
                    'request_sender_type': order.selectedStoreRequestSender.store_type_id,

                });
            }
            $('#itm3').val('');
            $('#itm4').val('');

        }

        $scope.removeItemArray_requisition = function(x){

            $scope.dispensing_array_requisitions.splice(x,1);


        }


//dispensing store ordering items from several stores
        $scope.dispensing_item_ordering = function () {


            $http.post('/api/dispensing_item_ordering',$scope.dispensing_array_requisitions).then(function (data) {
                $scope.dispensing_array_requisitions=[];
                var sending = data.data.msg;
                var statusee = data.data.status;
                if (statusee == 0) {
                    swal(
                        'Error',
                        sending,
                        'error'
                    )
                }
                else {
                    swal(
                        'Feedback..',
                        sending,
                        'success'
                    )
                }


            });
        }

        $scope.getPatientReport = function(item) {
			if(item.sub_category_name=="NHIF"){
				$scope.paymentMethod = 'cash';
			}else
				$scope.paymentMethod = $scope.configuredOption;
			
            $http.post('/api/getPastMedicine', {

                "patient_id": item.patient_id,

                "visit_date_id": item.account_id

            }).then(function(data) {

                $scope.prevMedicines = data.data;

            });


            $http.post('/api/getPastProcedures', {

                "patient_id": item.patient_id,

                "visit_date_id": item.account_id

            }).then(function(data) {

                $scope.pastProcedures = data.data;

            });
        }
        //paid insurance
		//reports based on patient category
		$http.get('/api/payment_sub_category_to_set_price').then(function(data) {
			$scope.patientCategories=data.data;
		});

		$scope.getInsurance = function (item) {
			var report_generated_on = new Date() + "";
			var items ={category:item.category,start:item.start,end:item.end,facility_id:facility_id};
			$http.post('api/categoriesReport',items).then(function (data) {
				if(data.data.status==0){
					swal('',data.data.msg,'info');
				}else {
					$scope.categoriesReports=data.data;
					$scope.insuGrandTotal=$scope.insuPesa();
					$scope.insu_report_generated_on = report_generated_on.substring(0, 24);
				}
			});
		}
		//paid insurance
		$scope.getPaidInsurance = function (item) {
			var report_generated_on = new Date() + "";
			var items ={category:10,start:item.start,end:item.end,facility_id:facility_id};
			$http.post('api/paidInsuranceReports',items).then(function (data) {
				if(data.data.status==0){
					swal('',data.data.msg,'info');
				}else {
					$scope.paidCategoriesReports=data.data;
					$scope.insuPaidGrandTotal=$scope.insuPaidPesa();
					$scope.insu_report_generated_on = report_generated_on.substring(0, 24);
				}
			});
		}
		$scope.insuPesa = function () {
			var total = 0;
			for (var i = 0; i < $scope.categoriesReports.length; i++) {
				total -= -($scope.categoriesReports[i].quantity*$scope.categoriesReports[i].price);
			}
			return total;
		}
		$scope.insuPaidPesa = function () {
			var total = 0;
			for (var i = 0; i < $scope.paidCategoriesReports.length; i++) {
				total -= -($scope.paidCategoriesReports[i].quantity*$scope.paidCategoriesReports[i].price);
			}
			return total;
		}
		$scope.printInsuReport = function () {
//location.reload();
			var DocumentContainer = document.getElementById('insutoprint');
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

		};
		$scope.printInsuPaidReport = function () {
//location.reload();
			var DocumentContainer = document.getElementById('insupaidtoprint');
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

		};
         $scope.cashiersBalance = function (item) {
             var cashierData = {facility_id:facility_id,user_id:user_id,start:item.start,end:item.end};
             $http.post('/api/getCashierTransactions',cashierData).then(function (data) {
                 $scope.transactions = data.data[0];
                 $scope.transactionsGePG = data.data[1];
                 $scope.detailedData = data.data[2];
                 $scope.detailedDataGePG = data.data[3];
                 $scope.selIdx= -1;
                // $scope.detailedTotal = $scope.sum();
                // $scope.detailedTotalGePG = $scope.sumGePG();
                 $scope.selData=function(d,idx){
                     $scope.selectedData=d;
                     $scope.selIdx=idx;
                 }
                 $scope.isSelData=function(d){
                     return $scope.selectedData===d;
                 }
             });
         }

        $scope.printCashiersReport = function () {
//location.reload();
            var DocumentContainer = document.getElementById('employeedivtoprint');
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

        };


        $scope.getCashiers = function (item) {
            $http.post('/api/getHospitalShopCashierReports', {
                "start": item.start,
                "end": item.end,
				'category':10,
                "facility_id": facility_id
            }).then(function (data) {
                $scope.cashiers = data.data;

                var report_generated_on = new Date() + "";
                $scope.employee_report_generated_on = report_generated_on.substring(0, 24);

                $scope.csGrandTotal = $scope.cashierTtl();

            });

        }

        $scope.getDate = function (item) {
            $http.post('/api/getDetailedReportsHospitalShop', {
                "start": item.start,
                "end": item.end,
                'category':10,
                "facility_id": facility_id
            }).then(function (data) {
                $scope.detailedData = data.data[0];
                $scope.detailedDataGePG = data.data[1];
                $scope.selIdx = -1;
                $scope.detailedTotal = $scope.sum();

                $scope.selData = function (d, idx) {
                    $scope.selectedData = d;
                    $scope.selIdx = idx;
                };
                var report_generated_on = new Date() + "";
                $scope.report_generated_on = report_generated_on.substring(0, 24);


                $scope.isSelData = function (d) {
                    return $scope.selectedData === d;
                }
            });
        };
        $scope.getDetailedData = function (item, dates) {
            $http.post('/api/detailedDataHospitalShop', {
                "receipt_number": item.receipt_number,
                "start": dates.start,
                "end": dates.end
            }).then(function (data) {
                $scope.getDetailedReports = data.data;
            });
        }
        $scope.cashierTtl = function () {
            var total = 0;
            for (var i = 0; i < $scope.cashiers.length; i++) {
                total -= -($scope.cashiers[i].sub_total);
            }
            return total;
        }

        $scope.sum = function () {
            var total = 0;
            for (var i = 0; i < $scope.detailedData.length; i++) {
                total -= -($scope.detailedData[i].sub_total);
            }
            return total;
        }

        $scope.getReceiptCopy = function (item,category) {
            $http.post('/api/getReceiptData',{"receipt_number":item.receipt_number,"payment_method_id":category}).then(function (data) {
                $scope.getDetailedReports = data.data;
                var modalInstance = $uibModal.open({
                    templateUrl: '/views/modules/payments/receiptCopy.html',
                    size: 'lg',
                    animation: true,
                    controller: 'printReceipt',
                    resolve: {
                        object: function () {

                            return $scope.getDetailedReports;
                        }
                    }
                });
            });
        }



    }

})();