/**
 * Created by USER on 2017-03-13.
 */
/**
 * Created by USER on 2017-03-09.
 */

(function() {

    'use strict';

    angular
        .module('authApp')
        .controller('SubStoreItemsController', SubStoreItemsController);

    function SubStoreItemsController($http, $auth, $rootScope,$state,$location,$scope,Helper) {

        //loading menu
        var user_id = $rootScope.currentUser.id;
        var facility_id = $rootScope.currentUser.facility_id;
        $http.get('/api/getUsermenu/' + user_id).then(function (data) {
            $scope.menu = data.data;
            ////// //console.log($scope.menu);

        });

        $scope.setTab = function(newTab){
            $scope.tab = newTab;
        };
        $scope.isSet = function(tabNum){
            return $scope.tab === tabNum;
        }
        $scope.oneAtATime=true;

        $scope.sort = function(keyname){
            $scope.sortKey = keyname;   //set the sortKey to the param passed
            $scope.reverse = !$scope.reverse; //if true make it false and vice versa
        }
        var resdata = [];
        $scope.items_array_requisitions =[];
        $scope.items_array_issue =[];

        //codes to display items in pharmacy stores...received items.
        $scope.showSearch = function(searchKey) {


            $http.post('/api/searchItemReceived',{searchKey:searchKey}).then(function(data) {
                resdata = data.data;
            });
            return resdata;

        }
        //codes to display items in pharmacy stores...received items.
        $scope.showItem = function (searchKey) {


            $http.post('/api/searchItemsubstoreReceived', {searchKey: searchKey}).then(function (data) {
                resdata = data.data;
            });
            return resdata;

        }

        //loading item batches from function ShowItem above
        $scope.loadBatch = function (item_id) {

            $http.get('/api/batchsubstore_list/' + item_id + ',' + user_id).then(function (data) {
                $scope.batches = data.data;
                 //console.log(data.data);

            });
        }



        $scope.batchesbalances = "";
        //loading item batches balance from function loadBatch above
        $scope.loadBatchBalance = function (batch_number, store_id) {
// //console.log(batch_number, store_id)
            $http.get('/api/loadsubstoreBatchBalance/' + batch_number + ',' + store_id).then(function (data) {
                $scope.batchesbalances = data.data;
                //console.log(data.data);

            });

        }



        $http.get('/api/pharmacy_transaction_type_list').then(function (data) {
            $scope.transtypes = data.data;

        });

        $http.get('/api/store_list/' + user_id).then(function (data) {
            $scope.stores = data.data;

        });

        $http.get('/api/Sub_stores_List/' + user_id).then(function (data) {
            $scope.Sub_stores = data.data;

        });

        $http.get('/api/Sub_main_stores_List/' + user_id).then(function (data) {
            $scope.Sub_Main_stores = data.data;

        });

        $http.get('/api/Sub_dispensing_stores_List/' + user_id).then(function (data) {
            $scope.Sub_dispensing_stores = data.data;

        });


        //vendor_registration  CRUD

        $scope.TargetedStoreUserToReceive=function (store_id) {
            console.log(store_id)
            $http.get('/api/TargetedStoreUserToReceive/'+store_id+','+facility_id).then(function(data) {
                $scope.target_store_users=data.data;
                console.log(data.data)

            });
        }
        $scope.substore_item_receiving_list = function () {

            $http.get('/api/substore_item_receiving_list/' + facility_id + ',' + user_id).then(function (data) {
                if(data.data.length >0){
                    $scope.items = data.data;
                }
                else{
                    swal(
                        'Info',
                        'Nothing in stores',
                        'info'
                    )
                }


            });
        }


        //pharmacy sub store _report types

        $scope.reports=[{'id':1,'balance':"Store Balance"},{'id':2,'balance':"Detailed Report"}];

        $scope.pharmacy_report=function (report_type) {
            $http.get('/api/item_balances_list_in_substore/' + facility_id + ',' + user_id + ',' + report_type).then(function (data) {
                if (data.data.length > 0) {
                    $scope.items = data.data;
                    $scope.xs=[];
                    $scope.ys=[];
                }



                for(var i=0;i< $scope.items.length; i++){
                    $scope.xs.push($scope.items[i].item_name);
                    $scope.ys.push($scope.items[i].quantity);
                }

                $scope.labels=$scope.xs ;
                $scope.data =  $scope.ys;


            });
        }
        // $scope.item_receiving_list();

        $scope.Substore_item_list_issue=function (issuing) {
            if(issuing==undefined){
                swal(
                    'Error',
                    'Choose Item First..',
                    'error'
                )
                return;
            } else if(issuing.selectedItem==undefined){
                swal(
                    'Error',
                    'Choose Item First..',
                    'error'
                )
                return;
            }
            else if($scope.batchesbalances.length<1){
                swal(
                    'Error',
                    'Choose Batch Number which has enough balance',
                    'error'
                )
                return;
            }


            else if(issuing.selectedBatch==undefined){
                swal(
                    'Error',
                    'Choose Item Batch',
                    'info'
                )
                return;
            }
            else if($scope.batchesbalances.length<1){
                swal(
                    'Error',
                    'No Enough Quantity from this Store',
                    'info'
                )
                return;
            }
            else if(issuing.quantity==undefined){
                swal(
                    'Error',
                    'Fill Issuing Quantity',
                    'error'
                )
                return;
            }
            else if(issuing.quantity<0){
                swal(
                    'Error',
                    'Issuing Quantity Can not Be Negative value',
                    'error'
                )
                return;
            }
            if( issuing.transaction_type_id==undefined){
                swal(
                    'Error',
                    'Choose Issuing Type of This Transaction',
                    'error'
                )
                return;
            }
            else if($scope.batchesbalances[0].quantity - issuing.quantity <0){
                swal(
                    'Error',
                    'No Enough Quantity from this Store only '+$scope.batchesbalances[0].quantity+ ' remained',
                    'error'
                )
                return;
            }



            else {

                for(var i=0;i<$scope.items_array_issue.length;i++){


                    if($scope.items_array_issue[i].item_id == issuing.selectedItem.item_id){ swal(issuing.selectedItem.item_name+" already in your order list ","","info"); return;}
                }


                if($scope.items_array_issue.length !=0 && $scope.items_array_issue[0].adjustment == 'plus'){

                    var  user_targeted_id = $scope.items_array_issue[0].user_targeted_id;
                    var  issued_store_id = $scope.items_array_issue[0].issued_store_id;
                    var  store_type_id = $scope.items_array_issue[0].store_type_id;
                    var  store_name = $scope.items_array_issue[0].store_name;
                    var  identifier = $scope.items_array_issue[0].identifier;
                    var received_from_id=$scope.items_array_issue[0].received_from_id;
                    // var from =$scope.items_array_issue[0].from;
                    var invoice_no=$scope.items_array_issue[0].invoice_no;
                    var internal_issuer_id=$scope.items_array_issue[0].internal_issuer_id;
                    var invoice_number=$scope.items_array_issue[0].invoice_number;
                    var vendor_id=$scope.items_array_issue[0].vendor_id;
                    // var expiry_date=$scope.items_array_issue[0].expiry_date;
                    var transaction_type_id=$scope.items_array_issue[0].transaction_type_id;
                    var adjustment=$scope.items_array_issue[0].adjustment;
                    // var batch_no=$scope.items_array_issue[0].batch_no;
                }
                else if($scope.items_array_issue.length !=0 && $scope.items_array_issue[0].adjustment != 'plus'){

                    var  issued_store_id = $scope.items_array_issue[0].issued_store_id;
                    var  store_type_id = $scope.items_array_issue[0].store_type_id;
                    var  store_name = $scope.items_array_issue[0].store_name;
                    var  identifier = $scope.items_array_issue[0].identifier;
                    var received_from_id=$scope.items_array_issue[0].received_from_id;
                    // var from =$scope.items_array_issue[0].from;
                    var invoice_no=$scope.items_array_issue[0].invoice_no;
                    var internal_issuer_id=$scope.items_array_issue[0].internal_issuer_id;
                    var invoice_number=$scope.items_array_issue[0].invoice_number;
                    var vendor_id=$scope.items_array_issue[0].vendor_id;
                    // var expiry_date=$scope.items_array_issue[0].expiry_date;
                    var transaction_type_id=$scope.items_array_issue[0].transaction_type_id;
                    var adjustment=$scope.items_array_issue[0].adjustment;
                    // var batch_no=$scope.items_array_issue[0].batch_no;
                }


                else{
                    if($scope.items_array_issue.length ==0 && issuing.transaction_type_id==undefined){
                        swal(
                            'Error',
                            'Choose Issuing Type of This Transaction',
                            'error'
                        )
                        return;
                    }
                    else if(issuing.selectedStore==undefined && issuing.transaction_type_id.adjustment=='plus'){
                        swal(
                            'Error',
                            'Choose Issued Store',
                            'error'
                        )
                        return;
                    }
                    else if(issuing.user_targeted_id==undefined && issuing.transaction_type_id.adjustment=='plus'){
                        swal(
                            'Error',
                            'Choose User Targeted To received These Items In  '+issuing.selectedStore.store_name,
                            'error'
                        )
                        return;
                    }
                    else if(issuing.selectedBatch==undefined){
                        swal(
                            'Error',
                            'Choose Item Batch',
                            'info'
                        )
                        return;
                    }
                    else if($scope.items_array_issue.length ==0 && issuing.transaction_type_id.adjustment=='plus'){


                        var  user_targeted_id =issuing.user_targeted_id;
                        var  issued_store_id = issuing.selectedStore.id;
                        var  store_type_id = issuing.selectedStore.store_type_id;
                        var  store_name = issuing.selectedStore.store_name;
                        var  identifier =  $scope.batchesbalances[0].id;
                        var received_from_id=issuing.selectedBatch.store_id;
                        var from =issuing.selectedBatch.store_name;
                        var invoice_no=issuing.selectedBatch.invoice_number;
                        var internal_issuer_id=issuing.selectedBatch.store_id;
                        var invoice_number=issuing.selectedBatch.invoice_id;
                        var vendor_id=issuing.selectedBatch.vendor_id;
                        var expiry_date=issuing.selectedBatch.expiry_date;
                        var transaction_type_id=issuing.transaction_type_id.id;
                        var adjustment=issuing.transaction_type_id.adjustment;
                        var batch_no=issuing.selectedBatch.batch_no;

                    }
                    else if($scope.items_array_issue.length ==0 && issuing.transaction_type_id.adjustment !='plus'){



                        var issued_store_id =issuing.selectedBatch.store_id;
                        var store_type_id =null;
                        var store_name = null;
                        var  store_name = issuing.selectedBatch.store_name;
                        var  identifier =  $scope.batchesbalances[0].id;
                        var received_from_id=issuing.selectedBatch.store_id;
                        var from =issuing.selectedBatch.store_name;
                        var invoice_no=issuing.selectedBatch.invoice_number;
                        var internal_issuer_id=issuing.selectedBatch.store_id;
                        var invoice_number=issuing.selectedBatch.invoice_id;
                        var vendor_id=issuing.selectedBatch.vendor_id;
                        var expiry_date=issuing.selectedBatch.expiry_date;
                        var transaction_type_id=issuing.transaction_type_id.id;
                        var adjustment=issuing.transaction_type_id.adjustment;
                        var batch_no=issuing.selectedBatch.batch_no;

                    }
                }

                // if (issuing.transaction_type_id.adjustment == 'plus'){
                //     issued_store_id = issuing.selectedStore.id;
                //     store_type_id = issuing.selectedStore.store_type_id;
                //     store_name = issuing.selectedStore.store_name;
                // }
                var storeBalance=($scope.batchesbalances[0].quantity - issuing.quantity);
                var PreviusBalance=$scope.batchesbalances[0].quantity;


                $scope.items_array_issue.push({
                    'user_targeted_id':user_targeted_id,
                    'item_name':issuing.selectedItem.item_name,
                    'issuing':issuing,
                    'from':issuing.selectedBatch.store_name,
                    'item_id': issuing.selectedItem.item_id,
                    'quantity_issued': issuing.quantity,
                    'identifier':$scope.batchesbalances[0].id,
                    'received_from_id':received_from_id,
                    'store_sender_id': issuing.selectedBatch.store_id,
                    'store_receiver_id': issued_store_id,
                    'issued_store_id': issued_store_id,
                    'store_type_id': store_type_id,
                    'store_name': store_name,
                    'internal_issuer_id': $scope.batchesbalances[0].store_id,
                    'store_balance': storeBalance,
                    'PreviusBalance': PreviusBalance,
                    'invoice_number': issuing.selectedBatch.invoice_id,
                    'vendor_id': issuing.selectedBatch.vendor_id,
                    'expiry_date': $scope.batchesbalances[0].expiry_date,
                    'user_id': user_id,
                    'transaction_type_id': issuing.transaction_type_id.id,
                    'adjustment':issuing.transaction_type_id.adjustment,
                    'batch_no': $scope.batchesbalances[0].batch_no,
                    'facility_id': facility_id});

            }
            $('#btchi').val('');
            $('#qt').val('');
            $('#isi1').val('');
        }

        
        $scope.removeItemArray_issue = function(x){

            $scope.items_array_issue.splice(x,1);


        }

        $scope.substore_item_issuing = function () {

                $http.post('/api/substore_item_issuing',$scope.items_array_issue).then(function (data) {
                    $scope.items_issued = data.data;
                    $scope.items_array_issue=[];
                    var sending = data.data.msg;
                    var statusee = data.data.status;
                   // $scope.items_array_issue=[];
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
                $scope.batchesbalances = "";
            }





        $scope.SubStore_item_list_requisition=function (order) {

            if (order.selectedItem == undefined) {
                swal(
                    'Error',
                    'Choose Item First..',
                    'error'
                )
                return;
            } else if (order.selectedItem.item_id == undefined) {
                swal(
                    'Error',
                    'Choose Item First..',
                    'error'
                )
                return;
            }
            else if (order.selectedStoreRequestReceiver == undefined) {
                swal(
                    'Error',
                    'Choose Store Name you want To send this order request',
                    'error'
                )
                return;
            } else if (order.selectedStoreRequestSender == undefined) {
                swal(
                    'Error',
                    'Choose Store Name where order request is Coming from',
                    'error'
                )
                return;
            }
            else if ( order.quantity == undefined) {
                swal(
                    'Error',
                    'Enter Quantity you want to order',
                    'error'
                )
                return;
            }
            else if (order.selectedStoreRequestSender.id==order.selectedStoreRequestReceiver.id) {
                swal(
                    'Error',
                    'You can not Request item From similar Store',
                    'error'
                )
                return;
            }
            else if ( order.quantity<0) {
                swal(
                    'Error',
                    'Quantity Can not be a negative value .. Please enter a Possitive number',
                    'error'
                )
                return;
            }
            else{
                for(var i=0;i<$scope.items_array_requisitions.length;i++){


                    if(
                        $scope.items_array_requisitions[i].item_id == order.selectedItem.item_id &&
                        $scope.items_array_requisitions[i].request_receiver == order.selectedStoreRequestReceiver.id
                    )
                    { swal(order.selectedItem.item_name+" already in your order list ","","info"); return;}
                }


                $scope.items_array_requisitions.push({
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

        $scope.removeItemArray = function(x){

            $scope.items_array_requisitions.splice(x,1);
        }


//sub store ordering items from several stores
        $scope.substore_item_ordering = function () {
            $http.post('/api/substore_item_ordering',$scope.items_array_requisitions).then(function (data) {
                $scope.items_array_requisitions=[];
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

//item order processsing codes


        $scope.sub_store_incoming_order=function () {

            $http.get('/api/sub_store_incoming_order/'+facility_id+','+user_id).then(function(data) {
                $scope.item_orders=data.data;

            });
        }
        $scope.sub_store_incoming_order();


        //codes to display order and item specifically for processing
        $scope.Vieworder=function (orderview) {

   //console.log(orderview)

            //codes to display selected item order batch list
            var item_id=orderview.item_id;

            $scope.Vieworders=orderview;
            $http.get('/api/batchsubstore_list/'+item_id+','+user_id).then(function(data) {
                $scope.batches=data.data;
                //console.log(data.data)
            });
        }





        //codes for issuing or processing selected order

        $scope.order_issuing=function (order_issuing,order) {
            // //console.log(order_issuing,order)

            if($scope.batchesbalances[0].quantity==undefined){
                swal(
                    'Error',
                    'Choose Batch Number',
                    'error'
                )
                return;
            }
            else if(order_issuing.quantity<0){
                swal(
                    'Error',
                    'Issuing Quantity Can not Be Negative value',
                    'error'
                )
                return;
            }
            else if(order.item_id==undefined){
                swal(
                    'Error',
                    'Choose Issued Store',
                    'error'
                )
                return;
            }
            else if(order_issuing.transaction_type_id==undefined){
                swal(
                    'Error',
                    'Choose Issuing Type of This Transaction',
                    'error'
                )
                return;
            }
            else if($scope.batchesbalances[0].quantity - order_issuing.quantity <0){
                swal(
                    'Error',
                    'No Enough Quantity from this Store only'+$scope.batchesbalances[0].quantity+ ' remained',
                    'error'
                )
            }
            else{
                var storeBalance=($scope.batchesbalances[0].quantity - order_issuing.quantity);
                var PreviusBalance=$scope.batchesbalances[0].quantity;
                var issuing_order={'item_id':order.item_id,'quantity_issued':order_issuing.quantity,
                    'received_from_id':$scope.batchesbalances[0].received_from_id,
                    'request_id':order.id,
                    'issued_store_id':order.requested_store_id,
                    'identifier':$scope.batchesbalances[0].id,
                    'store_name':order.requesting_store_name,
                    'order_no':order.order_no,
                    'requested_store_type_id':order.requested_store_type_id,
                    'requesting_store_type_id':order.requesting_store_type_id,
                    'store_balance':storeBalance,
                    'PreviusBalance':PreviusBalance,
                    'batch_no':$scope.batchesbalances[0].batch_no,
                    'user_id':user_id,
                    'transaction_type_id':order_issuing.transaction_type_id,'facility_id':facility_id};
                // //console.log( $scope.batchesbalances);
                  //console.log(issuing_order);
                $http.post('/api/sub_store_Order_processing',issuing_order).then(function(data) {
                    $scope.processed=data.data;
                    $scope.sub_store_incoming_order();
                    $scope.Vieworders="";
                    var sending=data.data.msg;
                    var statusee=data.data.status;
                    if(statusee==0){
                        swal(
                            'Error',
                            sending,
                            'error'
                        )
                    }
                    else{
                        swal(
                            'Feedback..',
                            sending,
                            'success'
                        )
                    }

                });
            }
        }



        $scope.Cancelorder=function () {

            $scope.Vieworders="";

        }
        var _selected;


        $scope.reconsiliatedBatch = function(item) {
            $http.post('/api/reconsiliatedBatch',{item_id:item,user_id:user_id,sender:2}).then(function (data) {
                $scope.item_consiliates = data.data;
            });

        }
        $scope.stock_reconsilliation = function(item) {
            $http.post('/api/stock_reconsilliation',item).then(function (data) {
                $scope.reconsiled = data.data;
            });

        }

        $scope.regex=/\s/g;
        $scope.SaveReconsilation=function(reason){

            var itemss = [];
            var field_id;
            if (reason==undefined){
                swal("Enter Reconciliation Reasons",'','info');
                return;
            }
            $scope.item_consiliates.forEach(function(prices) {

                var item_id = prices.id;
                if($("#"+item_id).val() != ''){
                    itemss.push({
                        "column_id":prices.id,
                        "old_quantity":prices.quantity,
                        "store_id":prices.store_id,
                        "store_type_id":3,
                        "batch_no":prices.batch_no,
                        "item_id":prices.item_id,
                        "user_id":user_id,
                        "reason":reason,
                        "facility_id":facility_id,
                        "current_quantity":$("#"+item_id).val().replace(/,/g, '').replace(/[A-Za-z]/g, ''),
                    });
                    $("#"+item_id).val('');


                }

            });
            if (itemss.length==0){

                return;
            }

            Helper.overlay(true);
            $http.post('/api/stock_reconsilliation',itemss).then(function(data) {
                $("#"+reason).val('');
                // $scope.item_consiliates = data.data;
                Helper.overlay(false);
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




        $scope.ngModelOptionsSelected = function(value) {
            if (arguments.length) {
                _selected = value;
            } else {
                return _selected;
            }
        };

        $scope.modelOptions = {
            debounce: {
                default: 500,
                blur: 250
            },
            getterSetter: true
        };


    }

})();
/**
 * Created by USER on 2017-03-08.
 */