/**
 * Created by Mazigo Jr on 2017-02-23.
 */
(function () {
    'use strict';
    angular
        .module('authApp')
        .controller('paymentsController',paymentsController);
    function paymentsController($scope,$state,$http,$rootScope,$uibModal,$mdDialog,toastr,Helper,$timeout,$stateParams) {

        var facility_id = $rootScope.currentUser.facility_id;
        var user_id=$rootScope.currentUser.id;
        $scope.regex = /\B(?=(\d{3})+(?!\d))/g;
		$scope.list_travessing = false;
		$scope.data = {'cb':false};
		$scope.toto = 0;

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
					facility_id: facility_id,
					UserId:$rootScope.currentUser.id,
					UserName:$rootScope.currentUser.name,
					InvoiceId:invoice_id,
					InvoiceLine: {
						BillDescription:'Hospital Bill',
						BillAmount: ($scope.selectedPatient.sub_category_name.toLowerCase() == 'chf' ? $scope.getsumCHFBill() : $scope.getTotal()),
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
								swal({title:'BILL CANCELLATION',
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
		
		$scope.Processed = function(bill,index, auto = false){
			if(bill.Paid == 0 || bill.PspReceiptNumber == null)
				return;
			
			var Process = function(bill, index){
								if(!auto)
									Helper.overlay(true);
								$http.post('/gepg/new/mark_processed_bill',{facility_id: facility_id, BillId:bill.BillId, CashDeposit: parseInt(bill.CashDeposit), InvoiceId: bill.InvoiceId, user_id: user_id}).then(function (response) {
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
            var category_id =patient.patient_category_id;
            if (category_id == null) {
                swal("Please search patient first!", "", "error"); return;
            }
            if(patient.main_category_id == 3){
                category_id = 1;
            }
            $http.post('/api/itemsToPoS',{"search":searchKey, "facility_id":facility_id, "main_category_id":$scope.selectedPatient.main_category_id, "patient_category_id":$scope.selectedPatient.patient_category_id,sub_category_name: $scope.selectedPatient.sub_category_name}).then(function(data) {
                PoSata = data.data;
            });
            return PoSata;
        }
        //get patients' bills
        var allPatientBills=[];
        $scope.searchPatients = function(searchKey){
            $http.post('/api/getAllPatientBills',{"search":searchKey,"facility_id":facility_id}).then(function (data) {
                allPatientBills = data.data;
            });
            return allPatientBills;
        }
        $http.get('/api/getBills/'+facility_id ).then(function(data) {
           $scope.billsData=data.data;
          });
        var patientData=[];
		
        $scope.getBillModal = function (item) {
            $http.post('/api/getPatientBill',{"corpse_id":item.corpse_id,"patient_id":item.patient_id,"receipt_number":item.receipt_number}).then(function(data) {
				if(data.data.length == 0){
					swal('Bill Not found!','','warning');
					return;
				}
                $scope.item=data.data;
				
				$mdDialog.show({
                    controller: 'receiptsController',
                    templateUrl: '/views/modules/payments/billsModal.html',
                    parent: angular.element(document.body),
                    scope: $scope,
                    clickOutsideToClose: true,
                    fullscreen: true,
                });
            });
        }

        $scope.itemData = [];
        var balance = [];

        $scope.addItem = function(item,x,quantity){
            $http.post('/api/checkBilledItem',{account_id:item.account_id,item_id:x.item_id,item_name:x.item_name}).then(function (data) {
				if(data.data.status == 0){
					swal('',data.data.msg,'info'); return;
				}
				var payment_status = 2;
				var sub_category_name = item.sub_category_name.toLowerCase();

				var payment_filter = item.patient_category_id;
				var main_category = item.main_category_id;
				if(item == null || x==null || quantity ==null){
					swal("Please search Patient and Item the click 'Add' Button"); return;
				}
				for(var i=0;i<$scope.itemData.length;i++)
					if($scope.itemData[i].item_id == x.item_id){
						swal(x.item_name+" already in your order list!","","info");
						$scope.selectedItem= "";
						$scope.quantity= "";
						return;
					}
				var sub_total = x.price * quantity;
				
				//not payable conditions
				if(item.main_category_id == 1 && x.onetime == 1){
					sub_total = 0;
				}
				
				if(item.main_category_id == 2 && x.insurance == 1){
					sub_total = 0;
				}
				
				if(item.main_category_id == 3 && x.exemption_status == 0){//zero is used for not payable
					sub_total = 0;
				}
				//end not payable conditions
				
				//payment status remains 1 for all that not payable and not user fee
				if((item.main_category_id == 3  && x.exemption_status == 0) || (item.main_category_id == 2  && x.insurance == 1)){
					payment_status = 1;
				}
				//end payment status remains 1
				
				
				//for all exemptions, main_category is assumed 1 for pricing purposes
				if(item.main_category_id == 3){
					main_category = 1;
				}
				//end for all exemptions

			 if(x.dept_id == 4){
				$http.post('/api/balanceCheck',{"main_category_id":main_category,"item_id":x.item_id,"facility_id":facility_id, "user_id": user_id}).then(function (data) {
						balance = data.data;
					if(balance.length<1){
						 var Pos_os={
                        "item_id":x.item_id,"patient_id":item.patient_id,"visit_id":item.account_id,
                        "prescriber_id":user_id,user_id:user_id,"quantity":quantity,"frequency":'..',"duration":'..',
                        "dose":null,"start_date":null,"instruction":'..',"out_of_stock":'OS',dispensing_status:4,"facility_id":facility_id
                    }
                    $http.post('/api/Save_pos_os',Pos_os).then(function (data) {
                        swal(x.item_name +' is not available in store.','Contact store manager','info');
                    });
						$scope.selectedItem= "";
						$scope.quantity= "";
						return;
					}
					else if(balance.length >0 && balance[0].balance>=quantity) {

						//push for chf top up starts
						if(sub_category_name=='chf'){
							$http.post('api/chfCheckBills',{patient_id:item.patient_id,account_id: item.account_id}).then(function (data) {
								$scope.totalCHFBils = data.data[0];
								$scope.chf_item = data.data[1][0];
								$scope.chf_ceiling = parseInt( data.data[2].original.chf_ceiling);
								var using = data.data[2].original.use_chf_settings;
								console.log("chf_ceiling=",using);
								var billed=$scope.totalCHFBils;
								//demo push starts

								sub_total = x.price * quantity;
								$scope.itemData.push({hospital_shop_posting:false,"dept_id":x.dept_id,"item_id":x.item_id,"item_name":x.item_name,
									"chf_toto":0,"chf_use":using,"sub_total":sub_total,"receipt_number":"","item_type_id":x.item_type_id,
									"quantity":quantity,"price":x.price,"item_price_id":x.price_id,
									"user_id":user_id,"patient_id":item.patient_id,"medical_record_number":item.medical_record_number,
									"account_number":item.account_number,"account_number_id":item.account_id,
									"first_name":item.first_name,"middle_name":item.middle_name,"last_name":item.last_name,
									"status_id":1,"sub_category_name":item.sub_category_name,"payment_filter":payment_filter,
									"facility_id":facility_id,"discount":0,"discount_by":user_id
								});

								//demo push ends
								$scope.BillGenerated =  $scope.getTotal();
								var currentBill = $scope.BillGenerated;
								var ceiling = $scope.chf_ceiling;
								var totalBill = currentBill - (- billed);
								var difference = (totalBill - ceiling);
								var toa_chf_billed=$scope.getsumCHFBill();
								if (difference >0 && using==1){
									var  sub_totalChf = (($scope.chf_item.price)*((difference-toa_chf_billed)/$scope.chf_item.price));
                                    console.log("sub_totalChf=",sub_totalChf);
									sub_total=0;
									$scope.itemData.push({
										hospital_shop_posting:false,
										"dept_id": x.dept_id,
										"item_id": $scope.chf_item.item_id,
										"item_name": $scope.chf_item.item_name,
										"sub_total": sub_total,
										"chf_toto": sub_totalChf,
                                        "chf_use":using,
										"receipt_number": "",
										"item_type_id": $scope.chf_item.item_type_id,
										'quantity':((difference-toa_chf_billed)/$scope.chf_item.price),
										"price": $scope.chf_item.price,
										"item_price_id": $scope.chf_item.item_price_id,
										"user_id": user_id,
										"patient_id": item.patient_id,
										"medical_record_number": item.medical_record_number,
										"account_number":item.account_number,
										"account_number_id": item.account_id,
										"first_name": item.first_name,
										"middle_name": item.middle_name,
										"last_name": item.last_name,
										"status_id": 2,
										"sub_category_name": item.sub_category_name,
										"payment_filter": payment_filter,
										"facility_id": facility_id,
										"discount": 0,
										"discount_by": user_id
									});
									$scope.toto = $scope.getsumCHFBill();
									$scope.chf_top_up =  $scope.getsumCHFBill();
									$scope.selectedItem = "";
									$scope.quantity = "";

								}
								else{
									for(var i=0;i<$scope.itemData.length;i++)
										if($scope.itemData[i].item_id == x.item_id){
											$scope.selectedItem= "";
											$scope.quantity= "";
											return;}
									$scope.itemData.push({
									hospital_shop_posting:false,
										"dept_id":x.dept_id,"item_id":x.item_id,"item_name":x.item_name,
										"sub_total":sub_total,"chf_toto": using,"chf_use":0,"receipt_number":"","item_type_id":x.item_type_id,
										"quantity":quantity,"price":x.price,"item_price_id":x.price_id,
										"user_id":user_id,"patient_id":item.patient_id,"medical_record_number":item.medical_record_number,
										"account_number":item.account_number,"account_number_id":item.account_id,"first_name":item.first_name,
										"middle_name":item.middle_name,"last_name":item.last_name,
										"status_id":1,"sub_category_name":item.sub_category_name,"payment_filter":payment_filter,
										"facility_id":facility_id,"discount":0,"discount_by":user_id
									});
									$scope.toto = $scope.getTotal();
									$scope.selectedItem= "";
									$scope.quantity= "";
								}
							});

						}
						//push for chf top up ends
						else{

						$scope.itemData.push({
							hospital_shop_posting:false,
							"dept_id": x.dept_id,
							"item_id": x.item_id,
							"item_name": x.item_name,
							"sub_total": sub_total,
							"chf_toto": 0,
                            "chf_use":0,
							"receipt_number": "",
							"item_type_id": x.item_type_id,
							"quantity": quantity,
							"price": x.price,
							"item_price_id": x.price_id,
							"user_id": user_id,
							"patient_id": item.patient_id,
							"medical_record_number": item.medical_record_number,
							"account_number": item.account_number,
							"account_number_id": item.account_id,
							"first_name": item.first_name,
							"middle_name": item.middle_name,
							"last_name": item.last_name,
							"status_id": payment_status,
							"sub_category_name": item.sub_category_name,
							"payment_filter": payment_filter,
							"facility_id": facility_id,
							"discount": 0,
							"discount_by": user_id
						});
						$scope.toto = $scope.getTotal();
						$scope.selectedItem = "";
						$scope.quantity = "";


					}
					}
					else if(quantity>balance[0].balance){
						swal('Quantity for '+ x.item_name +' is not sufficient in store.','There are only '+ balance[0].balance+' items remained in store','info');
						$scope.selectedItem= "";
						$scope.quantity= "";

					}
				});
				}
				else {
					//push for chf top up starts
				 if(sub_category_name=='chf'){
						$http.post('api/chfCheckBills',{patient_id:item.patient_id,account_id: item.account_id}).then(function (data) {
							$scope.totalCHFBils = data.data[0];
							$scope.chf_item = data.data[1][0];
                            $scope.chf_ceiling = parseInt(data.data[2].original.chf_ceiling);
                            var using = data.data[2].original.use_chf_settings;
                            console.log("chf_ceiling=",using);
							var billed=$scope.totalCHFBils;
							//demo push starts
                            sub_total = x.price * quantity;
								$scope.itemData.push({hospital_shop_posting:false,"dept_id":x.dept_id,"item_id":x.item_id,"item_name":x.item_name,
									"chf_toto":0,"chf_use":using,"sub_total":sub_total,"receipt_number":"","item_type_id":x.item_type_id,
									"quantity":quantity,"price":x.price,"item_price_id":x.price_id,
									"user_id":user_id,"patient_id":item.patient_id,"medical_record_number":item.medical_record_number,
									"account_number":item.account_number,"account_number_id":item.account_id,
									"first_name":item.first_name,"middle_name":item.middle_name,"last_name":item.last_name,
									"status_id":1,"sub_category_name":item.sub_category_name,"payment_filter":payment_filter,
									"facility_id":facility_id,"discount":0,"discount_by":user_id
								});

							//demo push ends
							$scope.BillGenerated =  $scope.getTotal();
							var currentBill = $scope.BillGenerated;
							var ceiling = $scope.chf_ceiling;
							var totalBill = currentBill - (- billed);
							var difference = totalBill - ceiling;
							var toa_chf_billed=$scope.getsumCHFBill();
							if (difference >0 && using==1){
							  var  sub_totalChf = (($scope.chf_item.price)*((difference-toa_chf_billed)/$scope.chf_item.price));
								sub_total=0;
								$scope.itemData.push({
									hospital_shop_posting:false,
									"dept_id": x.dept_id,
									"item_id": $scope.chf_item.item_id,
									"item_name": $scope.chf_item.item_name,
									"sub_total": sub_total,
									"chf_toto": sub_totalChf,
                                    "chf_use":using,
									"receipt_number": "",
									"item_type_id": $scope.chf_item.item_type_id,
									'quantity':(difference-toa_chf_billed)/$scope.chf_item.price,
									"price": $scope.chf_item.price,
									"item_price_id": $scope.chf_item.item_price_id,
									"user_id": user_id,
									"patient_id": item.patient_id,
									"medical_record_number": item.medical_record_number,
									"account_number":item.account_number,
									"account_number_id": item.account_id,
									"first_name": item.first_name,
									"middle_name": item.middle_name,
									"last_name": item.last_name,
									"status_id": 2,
									"sub_category_name": item.sub_category_name,
									"payment_filter": payment_filter,
									"facility_id": facility_id,
									"discount": 0,
									"discount_by": user_id
								});
								$scope.toto = $scope.getsumCHFBill();
								$scope.chf_top_up =  $scope.getsumCHFBill();
								$scope.selectedItem = "";
								$scope.quantity = "";

							}
							else{
								for(var i=0;i<$scope.itemData.length;i++)
									if($scope.itemData[i].item_id == x.item_id){
										$scope.selectedItem= "";
										$scope.quantity= "";
										return;}
								$scope.itemData.push({
									hospital_shop_posting:false,
									"dept_id":x.dept_id,"item_id":x.item_id,"item_name":x.item_name,
									"sub_total":sub_total,"chf_toto": 0,"chf_use":using,"receipt_number":"","item_type_id":x.item_type_id,
									"quantity":quantity,"price":x.price,"item_price_id":x.price_id,
									"user_id":user_id,"patient_id":item.patient_id,"medical_record_number":item.medical_record_number,
									"account_number":item.account_number,"account_number_id":item.account_id,"first_name":item.first_name,
									"middle_name":item.middle_name,"last_name":item.last_name,
									"status_id":1,"sub_category_name":item.sub_category_name,"payment_filter":payment_filter,
									"facility_id":facility_id,"discount":0,"discount_by":user_id
								});
								$scope.toto = $scope.getTotal();
								$scope.selectedItem= "";
								$scope.quantity= "";
							}
						});

					}
					//push for chf top up ends
				   else{
					$scope.itemData.push({
						hospital_shop_posting:false,
						"dept_id":x.dept_id,"item_id":x.item_id,"item_name":x.item_name,
						"sub_total":sub_total,
						"chf_toto":0,
                        "chf_use":0,
						"receipt_number":"","item_type_id":x.item_type_id,"quantity":quantity,
						"price":x.price,"item_price_id":x.price_id,
						"user_id":user_id,"patient_id":item.patient_id,"medical_record_number":item.medical_record_number,
						"account_number":item.account_number,"account_number_id":item.account_id,"first_name":item.first_name,
						"middle_name":item.middle_name,"last_name":item.last_name,
						"status_id":payment_status,"sub_category_name":item.sub_category_name,"payment_filter":payment_filter,
						"facility_id":facility_id,"discount":0,"discount_by":user_id
					});
					$scope.toto = $scope.getTotal();
					$scope.selectedItem= "";
					$scope.quantity= "";
				}
				}});
           /* }); */
        }

        $scope.getsumCHFBill = function () {
            var  total = 0;
            for(var i = 0; i < $scope.itemData.length ; i++) {
                total +=($scope.itemData[i].chf_toto);
            }
			
			if((total % 50) > 0){
				total = (total -  (total % 50)) + 50;
			}
				
            return total;
        }

        $scope.getDiff_CHFBill = function () {

           return  $scope.itemData[$scope.itemData.length-1].chf_toto;

        }

        $scope.removeItem = function(item){

            var indexofItem = $scope.itemData.indexOf(item);
            $scope.itemData.splice(indexofItem,1);
            $scope.toto = $scope.getTotal();
            $scope.chf_top_up =  $scope.getsumCHFBill();
        }

        $scope.getTotal = function () {
        	console.log($scope.itemData);
            var  total = 0;
            for(var i = 0; i < $scope.itemData.length ; i++) {
                total += ($scope.itemData[i].sub_total);
			}
					  
            if((total % 50) > 0){
				total = (total -  (total % 50)) + 50;
			}
			
            return total;
        }


        $scope.processSales = function (paymentMethod,patient) {
			var sub_category_name = patient.sub_category_name.toLowerCase();
			var x = 0;
			if(sub_category_name =='chf'){
				  x = $scope.getsumCHFBill();
			}else {
				  x= $scope.getTotal();
			}
			
			swal({
					title: 'Are you sure you want to complete this transaction with a sum of '+x+' Tshs?',
					type: 'warning',
					showCancelButton: true,
					confirmButtonColor: '#3085d6',
					cancelButtonColor: '#d33',
					confirmButtonText: 'Yes',
					allowOutsideClick: false
			}).then(function () {
					var receiptNumber="";
                //gepg codes snippet
				if(paymentMethod == 'gepg' && x != 0){//turn all bills unpaid in order to post to gepg and wait for payment
					for(var i=0; i < $scope.itemData.length; i++){
						$scope.itemData[i].status_id = 1;
                        $scope.itemData[i]['payment_method_id'] = 2;
                    }
				}
				//end gepg code snippet
				if(paymentMethod == 'cash' || x == 0){//append payment method id and method name for the receipt
					for(var i=0; i < $scope.itemData.length; i++)
						$scope.itemData[i]['payment_method_id'] = 1;
				}

				$http.post('/api/saveFromPoS',$scope.itemData).then(function (data) {
                    if(data.data.status==0){
                        swal(data.data.data,'','error');
                    }
                   else {
                        receiptNumber=data.data;
                        if(paymentMethod == 'cash' || x == 0){//load receipt for cash payments
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
							$scope.toto = x;
						}else{
							$scope.gepgPostBill(receiptNumber);
						}
                    }
                });
			}, function (dismiss) {});

		}
         $scope.cashiersBalance = function (item) {
             var cashierData = {facility_id:facility_id,user_id:user_id,start:item.start,end:item.end};
             $http.post('/api/getCashierTransactions',cashierData).then(function (data) {
                 $scope.transactions = data.data[0];
                 $scope.transactionsGePG = data.data[1];
                 $scope.detailedData = data.data[2];
                 $scope.detailedDataGePG = data.data[3];
                 $scope.selIdx= -1;
				 //replaced the none existing sum & sumGepg functions
                 $scope.detailedTotal = 0;
                 $scope.detailedTotalGePG = 0;
				 $scope.detailedData.forEach(function(item){$scope.detailedTotal +=item.sub_total;});
				 $scope.detailedDataGePG.forEach(function(item){$scope.detailedTotalGePG +=item.sub_total;});
				 
                 $scope.selData=function(d,idx){
                     $scope.selectedData=d;
                     $scope.selIdx=idx;
                 }
                 $scope.isSelData=function(d){
                     return $scope.selectedData===d;
                 }
             });
         }
	
		$scope.deposit_summary = function (deposit,totalBill) {
            var withdrawing={name:deposit[0].name,detail:deposit,patient_id:deposit[0].patient_id,withdraw:totalBill,action_type:'withdraw',user_id:user_id,facility_id:facility_id};
            $('#amount_').val('');
            $('#patient_').val('');

            swal({
                title: 'SURE YOU WANT To CHECK DEPOSIT BALANCE AND DEDUCT FROM Patient Deposit ACCOUNT?',

                text:  "",
                type: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Yes!  ',
                cancelButtonText: 'No, cancel!',
                confirmButtonClass: 'btn btn-success',
                cancelButtonClass: 'btn btn-danger',
                buttonsStyling: false,
				allowOutsideClick: false
            }).then(function () {

                $http.post('/api/deposit_summary',withdrawing).then(function (data) {
                    var sending = data.data.msg;

                    if (data.data.status == 0) {
                        swal(
                            'Error',
                            sending,
                            'error'
                        )
                    }
                    else {
                        swal(
                            'Success',
                            sending,
                            'success'
                        )
                    }

                });



            }, function (dismiss) {
                // dismiss can be 'cancel', 'overlay',
                // 'close', and 'timer'
                if (dismiss === 'cancel') {

                }
            });


        }
		 	 
		 
    }

})();