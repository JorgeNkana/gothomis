/**
 * Created by Mazigo Jr on 2017-02-23.
 */
(function () {
    'use strict';
    angular
        .module('authApp')
        .controller('topupController',topupController);
    function topupController($scope,$state,$http,$rootScope,$uibModal,$mdDialog,toastr,Helper,$timeout) {

        var facility_id = $rootScope.currentUser.facility_id;
        var user_id=$rootScope.currentUser.id;
        $scope.paymentMethod = 'cash';
        $scope.regex = /\B(?=(\d{3})+(?!\d))/g;
        $scope.list_travessing = false;
        $scope.data = {'cb':false};

        //gepg codes
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

                var BillDetails = {
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
                var GePG = {attempt:0, load: function(BillDetails){
                        GePG.attempt++;
                        Helper.overlay(true);
                        $http.post('/gepg/gepg_send_bill', BillDetails).then(function(response){
                            Helper.overlay(false);
                            swal({
                                title:'GEPG',
                                html:response.data.generic+(response.data.success==0 ? '<br /><span style="color:red">'+response.data.real+'</span>' : ''),
                                type:'info',
                                showCancelButton: true,
                                cancelButtonText: 'Print',
                                customClass: 'swal-wide',
                                allowOutsideClick:false
                            }).then(function (){
                                //TODO
                            }, function(){
                                var printer = window.open("", "PAYMENT INFO");
                                printer.document.writeln(response.data.generic);
                                printer.document.close();
                                printer.focus();
                                printer.print();
                                printer.close();
                            });
                        }, function(data){if(GePG.attempt <5){ Helper.overlay(false);GePG.load(BillDetails); } else{ Helper.overlay(false); swal({title: 'Temporary Error!', html: Helper.genericError('Posting Bill to GePG'),type: 'error'});}});
                    }};
                GePG.load(BillDetails);
            }, function(){ return;});
        }

        $scope.reconcile = function(){
            var checking = function(){
                Helper.overlay(true);
                $('#counter').html("Reconcilling...");
                $http.post('/gepg/gepg_reconciled_bills', {facility_id: facility_id}).then(function(response){
                    Helper.overlay(false);
                    toastr.warning(response.data.generic,'', {timeOut: 12000});
                    if(response.data.completed == 1)
                        swal({
                            title:'GEPG RECONCILLIATION',
                            html:'<hr /><span style="font-family:Book Antiqua; font-size:16px; font-weight:bold">'+response.data.generic+'</span>',
                            type:'info',
                            customClass: 'swal-wide',
                            allowOutsideClick:false
                        });
                    else
                        setTimeout(checking, 120000);
                }, function(data){Helper.overlay(false);});
            }

            var GePG = {attempt:0, load: function(){
                    GePG.attempt++;
                    Helper.overlay(true);
                    $http.post('/gepg/gepg_reconcile', {facility_id: facility_id}).then(function(response){
                        Helper.overlay(false);
                        swal({
                            title:'GEPG',
                            html:response.data.generic+(response.data.success==0 ? '<br /><span style="color:red">'+response.data.real+'</span>' : ''),
                            type:'info',
                            customClass: 'swal-wide',
                            allowOutsideClick:false
                        });
                        if(response.data.success == 1){
                            setTimeout(checking, 120000);//2mins
                        }
                    }, function(data){if(GePG.attempt <5){Helper.overlay(false); GePG.load();}else{ Helper.overlay(false);  swal({title: 'Temporary Error!', html: Helper.genericError('GePG Payments Reconcilliation'),type: 'error', allowOutsideClick: false})}});
                }}

            GePG.load();
        }


        $scope.gepgPostBill = function(invoice_id){
            var BillDetails = {
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

            var GePG = {attempt:0, load: function(BillDetails){
                    GePG.attempt++;
                    Helper.overlay(true);
                    $http.post('/gepg/gepg_send_bill', BillDetails).then(function(response){
                        Helper.overlay(false);
                        swal({
                            title:'GEPG',
                            html:response.data.generic+(response.data.success==0 ? '<br /><span style="color:red">'+response.data.real+'</span>' : ''),
                            type:'info',
                            customClass: 'swal-wide',
                            allowOutsideClick:false
                        });
                        if(response.data.success==0){
                            var Rollback = {attempt:0, load: function(invoice_id){
                                    Rollback.attempt++;
                                    $http.post('/gepg/rollback', {invoice_id: invoice_id}).then(function(response){}, function(data){if(Rollback.attempt <5) Rollback.load(invoice_id);});
                                }};
                            Rollback.load(invoice_id);
                        }else{
                            $scope.itemData = [];
                            $scope.toto = $scope.getTotal();
                        }
                    }, function(data){if(GePG.attempt <5){Helper.overlay(false); GePG.load(BillDetails);}else{ Helper.overlay(false);  swal({title: 'Temporary Error!', html: Helper.genericError('Posting Bill to GePG'),type: 'error'})}});
                }}

            GePG.load(BillDetails);
        }

        $scope.cancelBill = function(bill, index){
            if(bill.paid == 1)
                return;

            swal({
                title: 'Proceed?',
                html: 'The Bill for the client will be removed from GePG. It may take a moment before is actually removed from GePG bills queue.',
                type: 'info',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Yes',
                customClass: 'swal-wide',
                allowOutsideClick:false
            }).then(function () {
                $http.post('/gepg/gepg_cancel_bill',{facility_id: facility_id,bill:bill}).then(function(response){
                    swal({title:'GEPG',
                        html:response.data.generic+(response.data.success==0 ? '<br /><span style="color:red">'+response.data.real+'</span>' : ''),
                        type:'info',
                        customClass: 'swal-wide',
                        allowOutsideClick:false
                    });
                    if(response.data.success==1)
                        $scope.pendingGePGConfirmations.splice(index,1);
                },function(error, status){swal({title: 'Temporary Error!', html: Helper.genericError('Cancelling GePG Bill'),type: 'error'})});
            }, function(){ return;});
        }


        $scope.checkGePG = function(){
            if($state.current.name == 'payments' || $state.current.name == 'point_of_sale'){
                if($scope.list_travessing){
                    setTimeout($scope.checkGePG, 5000);//5 secs
                    return;
                }
                $http.post('/gepg/gepg_check_pending_bills', {facility_id: facility_id}).then(function (response) {
                    $scope.pendingGePGConfirmations = [];
                    if(response.data.success != undefined && response.data.success==0 && response.data.account==-1)
                        return;//no gepg account detected
                    if(response.data.constructor === Array && response.data.length != 0){
                        $scope.pendingGePGConfirmations = response.data;
                        $http.post('/gepg/gepg_check_paid_bills', {facility_id:facility_id}).then(function (response) {});
                    }
                    setTimeout($scope.checkGePG, 30000);//30 secs
                    if(!$scope.running)
                        $scope.running = true;
                }, function(data){setTimeout($scope.checkGePG, 30000);});
            }else
                return;
        }

        $scope.GePGReceipt = function(gepg){
            if(gepg.paid == 0 || !gepg.gepg_receipt)
                return;

            var Receipt = {attempt:0, load: function(gepg){
                    Receipt.attempt++;
                    $http.post('/gepg/getGePGPaidBill',{PspReceiptNumber:gepg.gepg_receipt}).then(function (response){
                        for(var i = 0; i < response.data.length; i++){
                            response.data[i].receipt_number = response.data[i].gepg_receipt;
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
                    }, function(data){if(Receipt.attempt <5) Receipt.load(gepg); else  swal({title: 'Temporary Error!', html: Helper.genericError('Loading Customer\'s Bill Items.','<i>Please, re-select the customer</i>'),type: 'warning'})});
                }}

            Receipt.load(gepg);
        }

        $scope.Processed = function(bill,index, auto=false){
            if(bill.paid == 0)
                return;

            var Process = {attempt:0, load: function(BillId){
                    Process.attempt++;
                    $http.post('/gepg/gepg_mark_processed_bills',{facility_id: facility_id,BillId:BillId}).then(function (response) {
                        if(response.data.success == undefined && $scope.pendingGePGConfirmations.constructor === Array)
                            $scope.pendingGePGConfirmations.splice(index,1)
                    },function(data){if(Process.attempt <5) Process.load(BillId); else swal({title: 'Temporary Error!', html: Helper.genericError('Carrying out the request.'),type: 'warning'})});
                }}
            if(auto)
                Process.load(bill.BillId);
            else
                swal({
                    title: 'Proceed?',
                    html: 'Your action will permanently remove the item from your screen.',
                    type: 'info',
                    showCancelButton: true,
                    confirmButtonColor: '#3085d6',
                    cancelButtonColor: '#d33',
                    confirmButtonText: 'Yes',
                    customClass: 'swal-wide',
                    allowOutsideClick:false
                }).then(function () {
                    Process.load(bill.BillId);
                }, function(){ return;});
        }

        $scope.autoListReducer = function(){
            $scope.list_travessing = true;
            if($scope.pendingGePGConfirmations != undefined && $scope.pendingGePGConfirmations.constructor === Array){
                var index = 0;
                $scope.pendingGePGConfirmations.forEach(function(bill){
                    if(parseInt(bill.since_payment) >= 5)
                        $scope.Processed(bill,index,true);
                });
            }
            setTimeout($scope.autoListReducer, 300000);//5 mins
            $scope.list_travessing = false;
        }

        $scope.startup = function(){
            if(!$scope.running){
                $scope.checkGePG();
                $scope.autoListReducer();
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
        $scope.LoadCHFTOPUPDETAILS = function(item) {
            $http.post('api/chfCheckBills',{patient_id:item.patient_id,account_id: item.account_id}).then(function (data) {
                $scope.totalCHFBils = data.data[0];
                $scope.CHFITEM= data.data[1][0];
                $scope.chf_ceiling = parseInt(data.data[2].original.chf_ceiling);

            });
        }
        var PoSata =[];

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

        $scope.addItem = function(item,chf){

            console.log(item,chf);

if (item==undefined){swal("Choose Client","","error");return;}
if (chf.dept_id==undefined){swal("Choose Department for This  CHF Top up Bill ","","error");return;}
if (chf.quantity==undefined){swal("Enter CHF Top up Bill Amount ","","error");return;}
if (chf.billmode==undefined){swal("Choose Transaction Mode","","error");return;}
            for(var i=0;i<$scope.itemData.length;i++){
                if($scope.itemData[i].dept_id == chf.dept_id && $scope.itemData[i].quantity == chf.quantity ){

                    return;
                }
            }


                $scope.itemData.push({hospital_shop_posting:false,"dept_id":dept_id,"item_id":$scope.CHFITEM.item_id,"item_name":$scope.CHFITEM.item_name,
                    "chf_toto":$scope.CHFITEM.price*chf.quantity,"chf_use":0,"sub_total":$scope.CHFITEM.price*chf.quantity,"receipt_number":"","item_type_id":$scope.CHFITEM.item_type_id,
                    "quantity":chf.quantity,"price":$scope.CHFITEM.price,"item_price_id":$scope.CHFITEM.item_price_id,
                    "user_id":user_id,"patient_id":item.patient_id,"medical_record_number":item.medical_record_number,
                    "account_number":item.account_number,"account_number_id":item.account_id,
                    "first_name":item.first_name,"middle_name":item.middle_name,"last_name":item.last_name,
                    "status_id":chf.billmode,"sub_category_name":item.sub_category_name,"payment_filter":item.patient_category_id,
                    "facility_id":facility_id,"discount":0,"discount_by":user_id
                });

           $scope.toto= $scope.getTotal();


        }

        $scope.Create_bill = function () {

            swal({
                title: 'Are you sure You Want To Create This Bill ?',

                text: "This can not be Reversed",
                type: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Yes!  ',
                cancelButtonText: 'No, cancel!',
                confirmButtonClass: 'btn btn-success',
                cancelButtonClass: 'btn btn-danger',
                buttonsStyling: false
            }).then(function () {
                for(var i=0; i < $scope.itemData.length; i++)
                    $scope.itemData[i]['payment_method_id'] = 1;

                $http.post('/api/saveFromPoS',$scope.itemData).then(function (data) {
                    $scope.itemData=[];
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
                            "Bill has Generated and will be visible to Bills Payment Module for payment",
                            'success'
                        )
                    }


                })


            }, function (dismiss) {
                // dismiss can be 'cancel', 'overlay',
                // 'close', and 'timer'
                if (dismiss === 'cancel') {

                }
            })
        }

        $scope.getQuantity = function (amount) {

            console.log(amount);
            if (amount==undefined) {
                return;
            }
            else{
                var amounted=   $("#item").val().replace(',','')
                $scope.chf.quantity=(amounted/1000);
            }

        }
        $http.get('/api/department_list').then(function(data) {
            $scope.departments=data.data;

        });
        $scope.getDiff_CHFBill = function () {

            return  $scope.itemData[$scope.itemData.length-1].chf_toto;

        }

        $scope.removeItem = function(item){

            var indexofItem = $scope.itemData.indexOf(item);
            $scope.itemData.splice(indexofItem,1);
            $scope.toto = $scope.getTotal();

        }

        $scope.getTotal = function () {
            console.log($scope.itemData);
            var  total = 0;
            for(var i = 0; i < $scope.itemData.length ; i++) {
                total += ($scope.itemData[i].sub_total);
            }
            console.log(total);
            return total;
        }


        $scope.processSales = function (paymentMethod,patient) {

                var x= $scope.getTotal();


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
                if(paymentMethod == 'gepg' && $scope.getTotal() ==0){//send to gepg only valid bills
                    swal('GePG bills must be payable','','info');
                    return;
                }else if(paymentMethod == 'gepg'){//turn all bills unpaid in order to post to gepg and wait for payment
                    for(var i=0; i < $scope.itemData.length; i++){
                        $scope.itemData[i].status_id = 1;
                        $scope.itemData[i]['payment_method_id'] = 2;
                    }
                }
                //end gepg code snippet
                if(paymentMethod == 'cash'){//append payment method id and method name for the receipt
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