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
        .controller('PharmacyItemsController', PharmacyItemsController);

    function PharmacyItemsController($http, $auth, $rootScope,$state,$location,$scope,$timeout,Helper,$mdDialog) {
        $scope.setTab = function(newTab){
            $scope.tab = newTab;
        };
        $scope.isSet = function(tabNum){
            return $scope.tab === tabNum;
        }
        $scope.oneAtATime=true;
        //loading menu
        var user_id=$rootScope.currentUser.id;
        var  facility_id=$rootScope.currentUser.facility_id;
        $http.get('/api/getUsermenu/'+user_id ).then(function(data) {
            $scope.menu=data.data;
            //////console.log($scope.menu);

        });
        $http.get('/api/getLoginUserDetails/' + user_id).then(function (data) {
            $scope.loginUserFacilityDetails = data.data;

        });
        var resdata =[];
        $scope.items_array =[];
        $scope.items_array_issue =[];
        $scope.items_array_requisitions =[];
        $scope.showSearch = function(searchKey) {

                $http.post('/api/searchItem',{searchKey:searchKey}).then(function(data) {
                    resdata = data.data;
                });
                return resdata;

        }

        //codes to display items in pharmacy stores...received items.
        $scope.showItem = function(searchKey) {


                $http.post('/api/searchItemReceived',{searchKey:searchKey}).then(function(data) {
                    resdata = data.data;
                });
                return resdata;

        }

        $scope.LedgerSearch = function(item) {

                        $http.post('/api/ledger',{start_date: item.start_date, end_date: item.end_date, facility_id: facility_id,store_id:item.store_id}).then(function(data) {
                            $scope.ledgers = data.data;
                            
                        });


                
        };

        //loading dispensed items reports
        $scope.dispensed_item_range=function (item) {

            if(item==undefined  ){
                swal(
                    'Warning',
                    'Choose Date Range(Start and End date)',
                    'info'
                )
                return;
            }
            if(item.start_date==undefined  ){
                swal(
                    'Warning',
                    'Choose Date Range(Start and End date)',
                    'info'
                )
                return;
            }
            if(item.end_date==undefined ){
                swal(
                    'Warning',
                    'Choose Date Range(Start and End date)',
                    'info'
                )
                return;
            }

            var records={facility_id:facility_id,user_id:user_id,start_date:item.start_date,end_date:item.end_date}


                $http.post('/api/dispensed_item_range',records).then(function(data) {
                    $scope.dispensed_items=data.data;
                });


        }
        $scope.dispensed_item_range_group=function (item) {

            if(item==undefined  ){
                swal(
                    'Warning',
                    'Choose Date Range(Start and End date)',
                    'info'
                )
                return;
            }
            if(item.start_date==undefined  ){
                swal(
                    'Warning',
                    'Choose Date Range(Start and End date)',
                    'info'
                )
                return;
            }
            if(item.end_date==undefined ){
                swal(
                    'Warning',
                    'Choose Date Range(Start and End date)',
                    'info'
                )
                return;
            }

            var records={facility_id:facility_id,user_id:user_id,start_date:item.start_date,end_date:item.end_date}


                $http.post('/api/dispensed_item_range_group',records).then(function(data) {
                    $scope.dispensed_items_groups=data.data;
                });


        }



 $scope.Receive_issueSearch=function (item) {
            if(item==undefined ){
                swal(
                    'Warning',
                    'Choose Report Type either Receiving or Issuing Report and Date Range',
                    'info'
                )
                return;
            }
            if(item.start_date==undefined || item.end_date==undefined ){
                swal(
                    'Warning',
                    'Choose Date Range(Start and End date)',
                    'info'
                )
                return;
            }
            if(item.receive_r==undefined){
                swal(
                    'Warning',
                    'Choose Report Type either Receiving or Issuing Report',
                    'info'
                )
                return;
            }
            var records={facility_id:facility_id,user_id:user_id,start_date:item.start_date,end_date:item.end_date}
            if(item.receive_r=='rec_r'){
                $http.post('/api/received_voucher',records).then(function(data) {
                    $scope.recs=data.data;
                });
            }
            else if(item.receive_r=='issue_r'){
                $http.post('/api/issue_voucher',records).then(function(data) {
                    $scope.issue_s=data.data;
                });
            }

        }

        //loading item batches from function ShowItem above
        $scope.loadBatch=function (item_id) {
            $http.get('/api/batch_list/'+item_id+','+user_id).then(function(data) {
                $scope.batches=data.data;
                //console.log(data.data);
            });
        }
        $scope.batchesbalances="";
        //loading item batches balance from function loadBatch above
        $scope.loadBatchBalance=function (batch_number,store_id,item_id) {

            $http.get('/api/loadBatchBalance/'+batch_number+','+store_id+','+item_id).then(function(data) {
                $scope.batchesbalances=data.data;
                //console.log(data.data);
            });
        }
// setup data for receiving items
        $http.get('/api/invoice_list/'+facility_id).then(function(data) {
            $scope.invoices=data.data;

        });



        $http.get('/api/pharmacy_transaction_type_list').then(function(data) {
            $scope.transtypes=data.data;

        }); 
        $http.get('/api/pharmacy_transaction_adjustment').then(function(data) {
            $scope.transtypespossitive=data.data;

        });

        $http.get('/api/Main_stores_List/'+user_id).then(function(data) {
            $scope.main_stores=data.data;

        });

        $http.get('/api/store_list/'+user_id).then(function(data) {
            $scope.stores=data.data;

        });


        $scope.store_list=function () {

            $http.get('/api/storesListToAsignAccess/'+facility_id).then(function(data) {
                $scope.storesListToconfigure=data.data;

            });
        }

        $scope.expired=function () {

            $http.get('/api/expired/'+facility_id).then(function(data) {
                $scope.expires=data.data;

            });
        }

        $scope.tracer_medicines_report=function (item) {
            var records={facility_id:facility_id,user_id:user_id,start_date:item.start_date,end_date:item.end_date}
            $http.post('/api/tracer-medicines-report',records).then(function(data) {
                $scope.tracers=data.data;

            });
        }

        $scope.store_list();


        //item_receiving_list queue  CRUD


$scope.item_receiving_list=function (item_receive) {
    if(item_receive==undefined && $scope.items_array.length==0){
        swal(
            'Error',
            'Choose Item first',
            'error'
        )
    }if(item_receive.selectedItem==undefined && $scope.items_array.length==0 ){
        swal(
            'Error',
            'Choose Item first',
            'error'
        )
    }
    else  if(item_receive.selectedItem.item_id==undefined && $scope.items_array.length==0){
        swal(
            'Error',
            'Choose Item first',
            'error'
        )
    }

    else if(item_receive.batch_no==undefined ){
        swal(
            'Error',
            'Choose  or Enter Batch Number',
            'error'
        )
    }
    else if(item_receive.quantity==undefined){
        swal(
            'Error',
            'Choose  or Enter Item Quantity Received',
            'error'
        )
    }
	
    else if(item_receive.expiry_date==undefined){
        swal(
            'Error',
            'Choose  or Enter Item Expiry Date',
            'error'
        )
     }

    else if(item_receive.price==undefined){
        swal(
            'Error',
            'Choose  or Enter Item Cost Price',
            'error'
        )
    }
    else{


    for(var i=0;i<$scope.items_array.length;i++){


        if($scope.items_array[i].batch_no == item_receive.batch_no){ swal("  Batch Number "+item_receive.batch_no+" already in your order list"+$scope.items_array[i].item_name,"","info"); return;}


    }
        if($scope.items_array.length !=0){
            var store_name=$scope.items_array[0].store_name;
            var received_store_id=$scope.items_array[0].received_store_id;
            var invoice_refference=$scope.items_array[0].invoice_refference;
            var received_from_id=$scope.items_array[0].received_from_id;
            var transaction_type_id=$scope.items_array[0].transaction_type_id;
            var transaction_type=$scope.items_array[0].transaction_type;
            var received_date=$scope.items_array[0].received_date;
            var invoice_number=$scope.items_array[0].invoice_number;
            var remarks=$scope.items_array[0].remarks;
        }
        else{
            store_name=item_receive.selectedStore.store_name;
            received_store_id=item_receive.selectedStore.id;
            invoice_refference=item_receive.selectedInvoice.id;
            received_from_id=item_receive.selectedInvoice.vendor_id;
            transaction_type_id=item_receive.transaction_type_id;
            transaction_type=item_receive.transaction_type;
            received_date=item_receive.received_date;
            invoice_number=item_receive.selectedInvoice.invoice_number;
            remarks=item_receive.remarks;
        }
    $scope.items_array.push({'item_id':item_receive.selectedItem.item_id,'item_name':item_receive.selectedItem.item_name,
       'store_name':store_name,
       'received_store_id':received_store_id,
       'invoice_refference':invoice_refference,
       'invoice_number':invoice_number,
       'received_from_id':received_from_id,
       'transaction_type':transaction_type,
       'transaction_type_id':transaction_type_id,
        'received_date':received_date,
        'remarks':remarks,
       'user_id':user_id,'batch_no':item_receive.batch_no,
       'quantity':item_receive.quantity,
       'expiry_date':item_receive.expiry_date,'price':item_receive.price,'facility_id':facility_id,'control':'l'});

    $scope.total_cost=TOTAL_COST($scope.items_array);
}
    $('#btchi').val('');
    $('#qt').val('');
    $('#isi1').val('');
    $('#price').val('');
    $('#expire').val('');

}

        var TOTAL_COST = function(){
            var sum = 0;

            for(var i=0; i<$scope.items_array.length;i++){
                sum += ($scope.items_array[i].price*$scope.items_array[i].quantity);
            }

            return sum;

        }


        $scope.item_receiving_registration=function () {

            $http.post('/api/item_receiving_registration',$scope.items_array).then(function(data) {
                var sending=data.data.msg;
                var statuse=data.data.status;
                $scope.items_array=[];
                if(statuse==0){
                    swal(
                        'Error',
                        sending,
                        'error'
                    )
                }
                else{
                    swal(
                        'Success',
                        sending,
                        'success'
                    )
                }
        
        });

        }

        //pharmacy_report types

        $scope.reports=[{'id':1,'balance':"Store Balance"},{'id':2,'balance':"Detailed Report"},
            {'id':3,'balance':"Reorder Level"},{'id':4,'balance':"Tracer Medicine"}];

         $scope.pharmacy_report=function (report_type) {
             $http.get('/api/item_balances_list_in_mainstore/' + facility_id + ',' + user_id + ',' + report_type).then(function (data) {
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

        $scope.pharmacy_report(1);

        $scope.main_store_incoming_order=function () {

            $http.get('/api/main_store_incoming_order/'+facility_id+','+user_id).then(function(data) {
                $scope.item_orders=data.data;

            });
        }
        $scope.TargetedStoreUserToReceive=function (store_id) {
           // console.log(store_id)
            $http.get('/api/TargetedStoreUserToReceive/'+store_id+','+facility_id).then(function(data) {
                $scope.target_store_users=data.data;
                console.log(data.data)

            });
        }
        $scope.main_store_incoming_order();


        //codes to display order and item specifically for processing
        $scope.Vieworder=function (orderview) {
 // //console.log(orderview)
//// //console.log(orderview.item_id)
            var item_id=orderview.item_id;
            $scope.Vieworders=orderview;
            $http.get('/api/batch_list/'+item_id+','+user_id).then(function(data) {
                $scope.batches=data.data;

            });
        }

        //codes for issuing or processing selected order

        $scope.order_issuing=function (order_issuing,order) {
            //console.log( $scope.batchesbalances);
  //console.log(order_issuing,order)

             if($scope.batchesbalances[0].quantity==undefined){
                swal(
                    'Error',
                    'Choose Batch Number',
                    'error'
                )
            }
            else if(order_issuing.quantity<0){
                swal(
                    'Error',
                    'Issuing Quantity Can not Be Negative value',
                    'error'
                )
            }
            else if(order.item_id==undefined){
                swal(
                    'Error',
                    'Choose Issued Store',
                    'error'
                )
            }
            else if(order_issuing.transaction_type_id==undefined){
                swal(
                    'Error',
                    'Choose Issuing Type of This Transaction',
                    'error'
                )
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
                'received_from_id':order_issuing.selectedBatch.store_id,
                'invoice_refference':order_issuing.selectedBatch.invoice_id,
                'request_id':order.id,
                'identifier':$scope.batchesbalances[0].id,
                'issued_store_id':order.requesting_store_id,
                'vendor':$scope.batchesbalances[0].received_from_id,
                'expiry_date':$scope.batchesbalances[0].expiry_date,

                'store_name':order.requesting_store_name,
                'order_no':order.order_no,
                'requested_store_type_id':order.requested_store_type_id,
                'requesting_store_type_id':order.requesting_store_type_id,

                'store_balance':storeBalance,
                'PreviusBalance':PreviusBalance,
                'batch_no':order_issuing.selectedBatch.batch_no,
                'user_id':user_id,
                'transaction_type_id':order_issuing.transaction_type_id,'facility_id':facility_id};

           // //console.log(issuing_order);
            $http.post('/api/Order_processing',issuing_order).then(function(data) {
                $scope.processed=data.data;
                $scope.main_store_incoming_order();
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


        // $scope.item_receiving_list();

        //  update
$scope.pharmacy_item_list_issue=function (issuing) {

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
                var user_targeted_id = null;
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


    $scope.items_array_issue.push({'item_id':issuing.selectedItem.item_id,'quantity_issued':issuing.quantity,
        'user_targeted_id':user_targeted_id,
        'item_name':issuing.selectedItem.item_name,
        'received_from_id':issuing.selectedBatch.store_id,
        'from':issuing.selectedBatch.store_name,
        'identifier':$scope.batchesbalances[0].id,
        'issued_store_id':issued_store_id,
        'store_type_id':store_type_id,
        'store_name':store_name,
        'invoice_no':issuing.selectedBatch.invoice_number,
        'internal_issuer_id':issuing.selectedBatch.store_id,
        'store_balance':storeBalance,
        'PreviusBalance':PreviusBalance,
        'invoice_number':issuing.selectedBatch.invoice_id,
        'user_id':user_id,
        'vendor_id':issuing.selectedBatch.vendor_id,
        'expiry_date':issuing.selectedBatch.expiry_date,
        'transaction_type_id':issuing.transaction_type_id.id,
        'adjustment':issuing.transaction_type_id.adjustment,
        'batch_no':issuing.selectedBatch.batch_no,'facility_id':facility_id});
}
    $('#btchi').val('');
    $('#qt').val('');
    $('#isi1').val('');
    //console.log( $scope.items_array_issue);
}
$scope.pharmacy_item_issuing=function () {


    $http.post('/api/pharmacy_item_issuing',$scope.items_array_issue).then(function(data) {
        $scope.items_issued=data.data;
        var sending=data.data.msg;
        var statusee=data.data.status;
        $scope.items_array_issue=[];
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




        $scope.pharmacy_item_list_requisition=function (order) {

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
                    { swal(order.selectedItem.item_name+" already in your order list  ","","info"); return;}
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
                $scope.order={};
        }
        }

//main store ordering items from several stores
        $scope.main_store_item_ordering = function () {
               // //console.log(orders);
                $http.post('/api/main_store_item_ordering',$scope.items_array_requisitions).then(function (data) {

                    var sending = data.data.msg;
                    var statusee = data.data.status;
                    $scope.items_array_requisitions=[];
                    if (statusee == 0) {
                        swal(
                            'Error',
                            sending,
                            'error'
                        )
                        return;
                    }
                    else {
                        swal(
                            'Feedback..',
                            sending,
                            'success'
                        )
                        return;
                    }


                });
            }

        $scope.tracers=[];
        
        $scope.tracer_medicine=function (item) {

            $scope.tracers.push({'item_id':item.selectedItem.item_id,'item_name':item.selectedItem.item_name,status:item.status});

        }


        $scope.save_tracer_medicine = function(){


            $http.post('/api/save_tracer_medicine',$scope.tracers).then(function (data) {

                var sending = data.data.msg;
                var statusee = data.data.status;
                $scope.tracers=[];
                if (statusee == 0) {
                    swal(
                        'Info',
                        sending,
                        'info'
                    )

                }
                else {
                    swal(
                        'Response',
                        sending,
                        'success'
                    )
                }


            });

        }

        $scope.removeTracerItem = function(x){

            $scope.tracers.splice(x,1);

        }
        $scope.removeItemArray = function(x){

            $scope.items_array.splice(x,1);

            $scope.total_cost=TOTAL_COST($scope.items_array);
        }

        $scope.removeItemArray_issue = function(x){

            $scope.items_array_issue.splice(x,1);


        }
        $scope.removeItemArray_requisition = function(x){

            $scope.items_array_requisitions.splice(x,1);


        }

        
        






        // stores settting==========================================================================

        //searching user for assigning a store to dispens
        var user_store=[];
        $scope.SearchUser=function (seachKey) {

            var searchUser={'userKey':seachKey,'facility_id':facility_id};
            $http.post('/api/getUserToSetStoreToAccess',searchUser ).then(function(data) {
                user_store=data.data;

            });
            return user_store;
        }
        $scope.User_store_populate=[];
        var ckecks;

        $scope.populateInArray=function (selected_user_store,user) {
console.log(selected_user_store,user)

            var checking={'user_id':user.id,'store_id':selected_user_store.id};

            // $http.post('/api/store_user_checking',checking ).then(function(data) {
            //      ckecks=data.data.counti;
            //
            // });



            if(selected_user_store.value1==false){

            }
            else{
                $scope.User_store_populate.push({
                    'user_name':user.name,
                    'user_id':user.id,
                    'store_id':selected_user_store.id,
                    'store_name':selected_user_store.store_name,
                });

console.log($scope.User_store_populate);

            }


        }



        $scope.store_user_configure=function () {




            if($scope.User_store_populate.length<1){
                swal(
                    'Error',
                    'Nothing to save',
                    'error'
                )
                return;
            }

            else {
                $http.post('/api/store_user_configure', $scope.User_store_populate).then(function (data) {
                    var msg=data.data.msg;
                    var status=data.data.status;
                    if(status==0){
                        swal(
                            'Feedback!',
                            msg,
                            'info'
                        )
                    }
                    else{
                        swal(
                            'Feedback!',
                            msg,
                            'success'
                        )
                    }


                    if(data){
                        $scope.User_store_populate=[];
                    }
                });
            }

        }








        $scope.removeItem = function(x){

            $scope.User_store_populate.splice(x,1);

        }

        $scope.SelectedUserWithStroreAccess = function(user_id){

            $http.get('/api/SelectedUserWithStroreAccess/'+user_id ).then(function(data) {
                $scope.access_givens=data.data;

            });

        }

        $scope.Remove_user_store_access = function(id){

            $http.get('/api/Remove_user_store_access/'+id ).then(function(data) {
                $scope.removeds=data.data;
                swal('','Access removed','success')

            });

        }

        //vendor_registration  CRUD



        $scope.vendor_registration=function (vendor) {
            if(vendor==undefined){
                swal(
                    'Error',
                    'Fill all required fields',
                    'error'
                )
                return;
            }
else{


            var vendor_data={'vendor_name':vendor.vendor_name,'facility_id':facility_id,
                'vendor_address':vendor.vendor_address,'vendor_phone_number':vendor.vendor_phone_number,
                'vendor_contact_person':vendor.vendor_contact_person};

            $http.post('/api/vendor_registration',vendor_data).then(function(data) {
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


                $scope.vendor_list();

            });
        }
        }


        $scope.vendor_list=function () {

            $http.get('/api/vendor_list/'+facility_id).then(function(data) {
                $scope.vendors=data.data;

            });
        }

        $scope.vendor_list();

        //  update


        $scope.vendor_update=function (vendor) {
            swal({
                title: 'Are you sure?',
                text: " ",
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


                $http.post('/api/vendor_update', vendor).then(function (data) {
                    var sending=data.data.msg;
                    swal(
                        'Feedback..',
                        sending,
                        'success'
                    )

                    $scope.vendor_list();

                })




            }, function (dismiss) {
                // dismiss can be 'cancel', 'overlay',
                // 'close', and 'timer'
                if (dismiss === 'cancel') {
                    swal(
                        'Cancelled',
                        ' ',
                        'error'
                    )
                }
            })


        }



//  delete
        $scope.vendor_delete=function (vendor,id) {
            swal({
                title: 'Are you sure?',
                text: " ",
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


                $http.get('/api/vendor_delete/'+id).then(function(data) {

                    var sending=data.data.msg;
                    swal(
                        'Feedback..',
                        sending,
                        'warning'
                    )
                    $scope.vendor_list();

                })

            }, function (dismiss) {
                // dismiss can be 'cancel', 'overlay',
                // 'close', and 'timer'
                if (dismiss === 'cancel') {
                    swal(
                        'Cancelled',
                        ' ',
                        'error'
                    )
                }
            })


        }



        //invoices_registration  CRUD



        $scope.invoice_registration=function (invoice) {

            if(invoice==undefined){
                swal(
                    'Error',
                    'Fill all required fields',
                    'error'
                )
            }
            else{


            var invoice_data={'invoice_number':invoice.invoice_number,'vendor_id':invoice.vendor_id};
            //console.log(invoice_data);
            $http.post('/api/invoice_registration',invoice_data).then(function(data) {
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


                $scope.invoice_list();

            });
        }
        }


        $scope.invoice_list=function () {

            $http.get('/api/invoice_list/'+facility_id).then(function(data) {
                $scope.invoices=data.data;

            });
        }
        $scope.invoice_list();


        //  update


        $scope.invoice_update=function (invoice) {
            swal({
                title: 'Are you sure?',
                text: " ",
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


                $http.post('/api/invoice_update', invoice).then(function (data) {

                    var sending=data.data.msg;
                    swal(
                        'Feedback..',
                        sending,
                        'success'
                    )

                    $scope.invoice_list();

                })




            }, function (dismiss) {
                // dismiss can be 'cancel', 'overlay',
                // 'close', and 'timer'
                if (dismiss === 'cancel') {
                    swal(
                        'Cancelled',
                        ' ',
                        'error'
                    )
                }
            })


        }



//  delete
        $scope.invoice_delete=function (invoice,id) {
            swal({
                title: 'Are you sure?',
                text: " ",
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


                $http.get('/api/invoice_delete/'+id).then(function(data) {

                    var sending=data.data.msg;
                    swal(
                        'Feedback..',
                        sending,
                        'warning'
                    )
                    $scope.invoice_list();

                })

            }, function (dismiss) {
                // dismiss can be 'cancel', 'overlay',
                // 'close', and 'timer'
                if (dismiss === 'cancel') {
                    swal(
                        'Cancelled',
                        ' ',
                        'error'
                    )
                }
            })


        }




        //pharmacy_transaction_type_registration  CRUD



        $scope.pharmacy_transaction_type_registration=function (transtype) {

            var transtype_data={'transaction_type':transtype.transaction_type};
            //console.log(transtype);
            $http.post('/api/pharmacy_transaction_type_registration',transtype).then(function(data) {
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
                $scope.transtype_list();

            });
        }


        $scope.transtype_list=function () {

            $http.get('/api/pharmacy_transaction_type_list').then(function(data) {
                $scope.transtypes=data.data;

            });
        }

        $scope.transtype_list();
        
        $scope.store_type_list=function () {

            $http.get('/api/store_type_list').then(function(data) {
                $scope.storetypes=data.data;

            });
        }

        $scope.store_type_list();


 $scope.daily_dispensed_items=function (item) {


if (item==undefined) {
    var records={};
}
else{

    var records={facility_id:facility_id,user_id:user_id,start_date:item.start_date,end_date:item.end_date}


}

                $http.post('/api/daily_dispensed_items',records).then(function(data) {
                    $scope.daily_dispenses=data.data;
                });


        }
        $scope.daily_dispensed_items();


        //stores_registration  CRUD



        $scope.store_registration=function (store) {
            if(store==undefined){
                swal(
                    'Error',
                    'Fill all required fields',
                    'error'
                )
            }
else{


            var store_data={'store_name':store.store_name,'store_type_id':store.store_type_id,'facility_id':facility_id};
            //console.log(store_data);
            //console.log(store);
            $http.post('/api/store_registration',store_data).then(function(data) {
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

                $scope.store_list();

            });
        }
        }
        $scope.store_update=function (store) {
            swal({
                title: 'Are you sure?',
                text: " ",
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


                $http.post('/api/store_update', store).then(function (data) {

                    var sending=data.data.msg;
                    swal(
                        'Feedback..',
                        sending,
                        'success'
                    )
                    $scope.store_list();

                })




            }, function (dismiss) {
                // dismiss can be 'cancel', 'overlay',
                // 'close', and 'timer'
                if (dismiss === 'cancel') {
                    swal(
                        'Cancelled',
                        ' ',
                        'error'
                    )
                }
            })


        }

        // stores settting==========================================================================
        
        
        //issue voucher start....
        $http.get('/api/issued_store_voucher_list/'+user_id).then(function(data) {
            $scope.voucher_stores=data.data;

        });

        $scope.loadStoreVoucherDates=function (store_id,store_name) {
            
            $scope.issued_store_name=store_name.toUpperCase();
            $http.get('/api/loadStoreVoucherDates/'+store_id).then(function(data) {
                $scope.voucher_dates=data.data;

            });
        }


        $scope.ViewVoucherDetails=function (dated) {
            $mdDialog.show({
                controller: function ($scope) {
                    var store_id=dated.store_id;
                    
                    $http.get('/api/TargetedStoreUserToReceive/'+store_id+','+facility_id).then(function(data) {
                        $scope.target_store_users=data.data;

                    });
                    $scope.store_issued_name = dated.store_name.toUpperCase();
                    $scope.date_ = dated.issued_date;
                    $http.post('/api/ViewVoucherDetails',{dated:dated.issued_date,store_id:dated.store_id}).then(function(data) {
                        $scope.voucher_details=data.data;
                        

                    });
                    $scope.cancel = function () {
                        $mdDialog.hide();

                    };

                    $scope.PrintContent_voucher=function () {

                        //location.reload();
                        var DocumentContainer = document.getElementById('divtoprintvoucher');
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
                },
                templateUrl: '/views/modules/Pharmacy/Issue_voucher.html',
                parent: angular.element(document.body),
                clickOutsideToClose: true,
                fullscreen: false,
            });
        }

        $scope.LoadbinCardData=function(store_id){
            $http.post('/api/LoadbinCardData', {
                store_id: store_id
            }).then(function (data) {
                $scope.bin_cards = data.data;


            });
        }

        $scope.single_item_issue_voucher=function (item,store_id) {
            console.log(item,store_id);
            if (store_id==undefined) {
                swal('Oops!!','Please choose Store To Deal with','info')

            }
            else {


                $mdDialog.show({
                    controller: function ($scope) {

                        $http.get('/api/getLoginUserDetails/' + user_id).then(function (data) {
                            $scope.loginUserFacilityDetails = data.data;

                        });
                        $scope.item_name = item.item_name.toUpperCase();
                        $scope.item_code = item.item_code;

                        $http.post('/api/single_item_issue_voucher', {
                            item_id: item.id,
                            store_id: store_id
                        }).then(function (data) {
                            console.log(data.data);
                            $scope.single_item_vocher = data.data;
$scope.balanced=bincardbalance($scope.single_item_vocher);

                        });

                        var bincardbalance = function () {
                            var totalbincard = 0;

                            for (var i = 0; i < $scope.single_item_vocher.length; i++) {
                                totalbincard -= -($scope.single_item_vocher[i].balanced);
                            }

                            return totalbincard;

                        }
                        $scope.cancel = function () {
                            $mdDialog.hide();

                        };

                        $scope.PrintContent_single_voucher = function () {

                            //location.reload();
                            var DocumentContainer = document.getElementById('single_voucher');
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
                    },
                    templateUrl: '/views/modules/Pharmacy/Single_item_issue_voucher.html',
                    parent: angular.element(document.body),
                    clickOutsideToClose: true,
                    fullscreen: false,
                });
            }
        }

        //issue voucher end....

//amzing typeahead




        var _selected;





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


        $scope.sort = function(keyname){
            $scope.sortKey = keyname;   //set the sortKey to the param passed
            $scope.reverse = !$scope.reverse; //if true make it false and vice versa
        }

        $scope.PrintContent_balance=function () {
            //location.reload();
            var DocumentContainer = document.getElementById('divtoprint1');
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
        $scope.PrintContent_tracer=function () {
           
            //location.reload();
            var DocumentContainer = document.getElementById('tracer');
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
        $scope.PrintContent_expierd=function () {
           
            //location.reload();
            var DocumentContainer = document.getElementById('expierd');
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
        $scope.PrintContent_dispensed_proup=function () {

            //location.reload();
            var DocumentContainer = document.getElementById('dispensed_group');
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
        $scope.PrintContent_dispensed=function () {

            //location.reload();
            var DocumentContainer = document.getElementById('dispensed');
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
        $scope.PrintContent_received=function () {
           
            //location.reload();
            var DocumentContainer = document.getElementById('divtoprint_r');
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
        $scope.PrintContent_issued=function () {
           
            //location.reload();
            var DocumentContainer = document.getElementById('divtoprint_s');
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
$scope.Print_ledger=function () {

            //location.reload();
            var DocumentContainer = document.getElementById('ledger');
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

        $scope.pharmacy_transaction_type_registration=function (transtype) {

            var transtype_data={'transaction_type':transtype.transaction_type};
            //console.log(transtype);
            $http.post('/api/pharmacy_transaction_type_registration',transtype).then(function(data) {
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
                $scope.transtype_list();

            });
        }


        $scope.transtype_list=function () {

            $http.get('/api/pharmacy_transaction_type_list').then(function(data) {
                $scope.transtypes=data.data;

            });
        }

        $scope.transtype_list();

        //  update


        $scope.pharmacy_transaction_type_update=function (transtype) {
            swal({
                title: 'Are you sure?',
                text: " ",
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


                $http.post('/api/pharmacy_transaction_type_update', transtype).then(function (data) {

                    var sending=data.data.msg;
                    swal(
                        'Feedback..',
                        sending,
                        'success'
                    )
                    $scope.transtype_list();

                })




            }, function (dismiss) {
                // dismiss can be 'cancel', 'overlay',
                // 'close', and 'timer'
                if (dismiss === 'cancel') {
                    swal(
                        'Cancelled',
                        ' ',
                        'error'
                    )
                }
            })


        }



//  delete
        $scope.pharmacy_transaction_type_delete=function (id) {
            //console.log(id)
            swal({
                title: 'Are you sure?',
                text: " ",
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


                $http.get('/api/pharmacy_transaction_type_delete/'+id).then(function(data) {

                    var sending=data.data.msg;
                    swal(
                        'Feedback..',
                        sending,
                        'warning'
                    )
                    $scope.transtype_list();

                })

            }, function (dismiss) {
                // dismiss can be 'cancel', 'overlay',
                // 'close', and 'timer'
                if (dismiss === 'cancel') {
                    swal(
                        'Cancelled',
                        ' ',
                        'error'
                    )
                }
            })


        }
       
	   
		$scope.saveTracerStatus = function(){
			for(var i=0; i< $scope.tracer_items.length; i++)
				$scope.tracer_items[i].status = $('#stat_'+$scope.tracer_items[i].id).val();
			
			Helper.overlay(true);
			$http.post('/api/tracerMapping',{save_status:$scope.tracer_items}).then(function(data) {
				Helper.overlay(false);
				swal(data.data.msg,'','info');
			}, function(data){Helper.overlay(false);});
			
		}
		
		
		$scope.addMapping = function(item){
			$scope.mappings.items.push(item);
		}

		$scope.addDispedGroup = function(item){
			$scope.mappings.items.push(item);
		}

		$scope.removeMapping = function(index){
			$scope.mappings.items.splice(index,1);
		}
		$scope.removeDispensedGroup = function(index){
			$scope.mappings.items.splice(index,1);
		}
		
		$scope.saveMapping = function(tracer_id, mappings){
			for(var i=0; i< mappings.length; i++)
				mappings[i]['tracer_medicine_id'] = tracer_id;
			
			Helper.overlay(true);
			$http.post('/api/tracerMapping',{save_mapping:mappings}).then(function(data) {
				Helper.overlay(false);
				$scope.mappings.items = [];
				$scope.mappings.selectedItem = '';
				$scope.loadTracers();
				swal(data.data.msg,'','info');
			}, function(data){Helper.overlay(false);});
		}

		$scope.saveDispensedGroup = function(tracer_id, mappings){
			for(var i=0; i< mappings.length; i++)
				mappings[i]['tracer_medicine_id'] = tracer_id;

			Helper.overlay(true);
			$http.post('/api/saveGrouped',{save_mapping:mappings}).then(function(data) {
				Helper.overlay(false);
				$scope.mappings.items = [];
				$scope.mappings.selectedItem = '';
				$scope.loadgroup_control_list();
				swal(data.data.msg,'','info');
			}, function(data){Helper.overlay(false);});
		}

		$scope.removeFromTracerMapping = function(index,mapping){
			Helper.overlay(true);
			$http.post('/api/tracerMapping',{remove_mapping:mapping}).then(function(data) {
				Helper.overlay(false);
				$scope.loadTracers();
				swal(data.data.msg,'','info');
			}, function(data){Helper.overlay(false);});
		}
$scope.removeFromDispensedGroupMapping = function(index,mapping){
			Helper.overlay(true);
			$http.post('/api/removeFromDispensedGroupMapping',mapping).then(function(data) {
				Helper.overlay(false);
				$scope.loadgroup_control_list();
				swal(data.data.msg,'','info');
			}, function(data){Helper.overlay(false);});
		}


		$scope.loadTracers = function(){
			$http.get('/api/loadTracers').then(function(data) {
				$scope.tracer_items = data.data;
			});
		}

        $scope.loadgroup_control_list = function() {
            $http.get('/api/dispensed_group_control_list').then(function (data) {
                $scope.group_control_lists = data.data;
            });
            $http.get('/api/dispensed_groups').then(function (data) {
                $scope.groups = data.data;
            });
        }

		$scope.mark_pos_dispensing = function(store,mark){
			$http.post('/api/mark_pos_dispensing',{store_id:store.id,status:mark,facility_id:facility_id}).then(function(data) {
                var sending=data.data.msg;
                var status=data.data.status;
                if (status==1) {
                    swal(
                        ' ',
                        sending,
                        'success'
                    )
                }
                else{
                    swal(
                        'Attention Please!',
                        sending,
                        'warning'
                    )
                }



			});
		}
$scope.RnRSearch = function(item) {

            $http.post('/api/RnRSearch',{start_date: item.start_date, end_date: item.end_date, facility_id: facility_id,store_id:item.store_id}).then(function(data) {
                $scope.rnrs = data.data;

            });



        };

 $scope.preparernr = function() {

     swal(
         'Sorry!',
         'eLMIS Link is Down,and This is used for  DEMO Only',
         'warning'
     )


        };

        $scope.PrintRnR=function () {

            //location.reload();
            var DocumentContainer = document.getElementById('rnr_id');
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
		
		$scope.startup = function(){
			$scope.loadTracers();
			$scope.loadgroup_control_list();
			$scope.mappings = {items:[]};
		}

        $scope.PrintContent_daily_dispensed=function () {

            //location.reload();
            var DocumentContainer = document.getElementById('dailydispensed');
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

        $scope.reconsiliatedBatch = function(item) {
            $http.post('/api/reconsiliatedBatch',{item_id:item,user_id:user_id,sender:1}).then(function (data) {
                $scope.item_consiliates = data.data;
            });

        }
        $scope.getStockReconcilliated = function(item) {
            $http.post('/api/getStockReconcilliated',{start_date:item.start_date,end_date:item.end_date}).then(function (data) {
                $scope.reconsiled_records = data.data;
            });

        }
        $scope.returnStockReconcilliated = function(rec,item) {
            $http.post('/api/returnStockReconcilliated',{reconc_id:rec.id,column_id:rec.column_id,quantity:rec.old_quantity,store_type_id:rec.store_type_id,  start_date:item.start_date,end_date:item.end_date}).then(function (data) {
              //  $scope.reconsiled_records = data.data;
                var msg=data.data.msg;
                var status=data.data.status;
                if(status==1){
                    swal(msg,'','success');
                }
                else{
                    swal(msg,'','error');
                }
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
                        "store_type_id":2,
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

           // Helper.overlay(true);
            $http.post('/api/stock_reconsilliation',itemss).then(function(data) {
                $("#"+reason).val('');
               // $scope.item_consiliates = data.data;
                Helper.overlay(false);
                var msg=data.data.msg;
                var status=data.data.status;

                    swal("Reconciliation Successful done",'','success');


            }, function(data){Helper.overlay(false);})


        }
 $scope.reportsums=[{'id':1,'balance':"Store Balance"},{'id':2,'balance':"Detailed Report"},{'id':3,'balance':"Dispensed Items"}];

        $scope.pharmacy_reportsummary=function (report_type,date) {
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
            Helper.overlay(true);
            $http.post('/api/item_balances_list_in_dispensing',rec).then(function (data) {
                Helper.overlay(false);
                if (data.data.length > 0) {
                    $scope.itemdiss = data.data;
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

            }, function(data){Helper.overlay(false);});
        }

        //pharmacy sub store _report types

        $scope.reportsubs=[{'id':1,'balance':"Store Balance"},{'id':2,'balance':"Detailed Report"}];

        $scope.pharmacy_reportsub=function (report_type) {
            Helper.overlay(true);
                $http.get('/api/item_balances_list_in_substore/' + facility_id + ',' + user_id + ',' + report_type).then(function (data) {
                    if (data.data.length > 0) {
                        $scope.items = data.data;
                        $scope.itemsubs = data.data;
                        $scope.xs=[];
                        $scope.ys=[];
                    }

                    Helper.overlay(false);

                    for(var i=0;i< $scope.items.length; i++){
                        $scope.xs.push($scope.items[i].item_name);
                        $scope.ys.push($scope.items[i].quantity);
                    }

                    $scope.labels=$scope.xs ;
                    $scope.data =  $scope.ys;


                }, function(data){Helper.overlay(false);});
            }

$scope.RnRSearch = function(item) {
            if (item==undefined) {
                return;
            }
            else{
                Helper.overlay(true);
                $http.post('/api/RnRSearch',{program:item, facility_id: facility_id}).then(function(data) {
                    Helper.overlay(false);
                    $scope.rnrs = data.data;
                }, function(data){Helper.overlay(false);});

            }


        };

        $scope.RnRProgram=function(program){
            $scope.rnrprogram=program;

        }
        $scope.RnROrderType=function(orderType){
            if (orderType=="true") {
                $scope.orderType=true;
            }
            else{
                $scope.orderType=false;
            }

        }

        $scope.cancelpreparedrnr=function(order_number){


            $http.post('/api/cancelpreparedrnr',{order_number:order_number}).then(function(data) {
                Helper.overlay(false);
                $scope.rnrSavedOrder(order_number);
                var msg= data.data.message;
                var respo=data.data.data;
                swal(msg,'','success')
            }, function(data){Helper.overlay(false);});
        }

        $scope.Initiatepreparedrnr=function(status_,order_number){
            $http.post('/api/Initiatepreparedrnr',{status:status_,order_number:order_number}).then(function(data) {
                Helper.overlay(false);
                $scope.rnrSavedOrder(order_number);
                var msg= data.data.message;
                var respo=data.data.data;
                swal(msg,'','success')
            }, function(data){Helper.overlay(false);});

        }

        $scope.UpdateRnrOrderRowData=function(identity,item){
            //console.log(item.order_number);return;
if (item==undefined) {
    return;
}
if (item.order_status=="INITIATED") {
    swal('RnR is already INITIATED','No Editing allowed for this RnR Stage','info') ;
    return;
}
else{
    $http.post('/api/UpdateRnrOrderRowData',{identity:identity,items:item}).then(function(data) {

        var msg=data.data.massage;
        var status=data.data.status;
        if (status==0) {
            swal(msg,'','error') ;
            return;
        }
        else{
            $scope.edit=false;
        }


    });
}

        }

        $scope.Deletepreparedrnr=function(item){
            swal({
                title: 'sure?',
                text: "You want to delete this? This wouldn't reversed",
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
                $http.post('/api/Deletepreparedrnr',{order_number:item}).then(function (data) {
                    Helper.overlay(false);
                    $scope.rnrSavedOrder(item);
                    var msg=data.data.msg;
                    var status=data.data.status;
                    if (status==200) {
                        swal(msg,'','success') ;
                    }

                }, function(data){Helper.overlay(false);})




            }, function (dismiss) {
                // dismiss can be 'cancel', 'overlay',
                // 'close', and 'timer'
                if (dismiss === 'cancel') {

                }
            })
        }
        $scope.DeleteItemOrderRow=function(item){
            swal({
                title: 'sure?',
                text: "You want to delete this? This wouldn't reversed",
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


                $http.post('/api/DeleteItemOrderRow',{id:item.id}).then(function (data) {
                    $scope.rnrSavedOrder(item.order_number);
                    var msg=data.data.massage;
                    var status=data.data.status;
                    if (status==200) {
                        swal(msg,'','success') ;
                    }

                })




            }, function (dismiss) {
                // dismiss can be 'cancel', 'overlay',
                // 'close', and 'timer'
                if (dismiss === 'cancel') {

                }
            })
        }


        $scope.singleItemUomUpdate=function(item){
if (item==undefined) {
    return;
}
            $http.post('/api/singleItemUomUpdate',item).then(function(data) {

                var msg=data.data.massage;
                var records=data.data.data;
                swal(msg,'','success') ;
            });
        }
        $scope.singleItemMsdProductUpdate=function(item){
            if (item==undefined) {
                return;
            }
            $http.post('/api/singleItemMsdProductUpdate',item).then(function(data) {

                var msg=data.data.massage;
                var records=data.data.data;
                swal(msg,'','success') ;
            });
        }
        $scope.singleItemCodeUpdate=function(item){
            if (item==undefined) {
                return;
            }
            $http.post('/api/singleItemCodeUpdate',item).then(function(data) {

                var msg=data.data.massage;
                var records=data.data.data;
                swal(msg,'','success') ;
            });
        }
        $scope.Updatepreparedrnr=function(status_,order_number){
$scope.edit=false;
            $http.post('/api/Updatepreparedrnr',{status:status_,order_number:order_number}).then(function(data) {
                Helper.overlay(false);
                $scope.rnrSavedOrder(order_number);
                var msg= data.data.message;
                var respo=data.data.data;
                swal(msg,'','success')
            }, function(data){Helper.overlay(false);});
        }

        $scope.LoadRnROrderStatus=function(){
            $http.post('/api/LoadRnROrderStatus',{facility_id:facility_id}).then(function(data) {
                Helper.overlay(false);
                $scope.rnrstaus=data.data;
                var msg= data.data.message;
                var respo=data.data.data;
                swal(msg,'','success')
            }, function(data){Helper.overlay(false);});

        }
$http.get('/api/elmis_transaction_type_list').then(function(data) {
            $scope.elmistranstypes=data.data;

        });
         
 $scope.preparernr = function(rnr) {
if (rnr==undefined){
    swal('Please Choose Program of this Order','','info'); return;
}
if (  $scope.orderType==undefined){
    swal('Please Choose Order Type','','info'); return;
}
     var ordersRnR = [];
     var amountToOrder;
     var sababu;
     $scope.rnrs.forEach(function(category) {

       var skip =  category.skiped;

       // var dataaaaSkipe= {skip:skip,skipedValue:$('#_'+skip).prop('checked')}
       //  console.log(dataaaaSkipe); return;
         amountToOrder = category.item_code.replace(' ', '_');
         sababu = category.item_id.replace(' ', '_');

         if( $('#_'+skip).prop('checked')==false){
             ordersRnR.push({
                 "facility_id":facility_id,
                 "user_id":user_id,
                 "programCode":category.program,
                 "rnr_month":category.rnr_month,
                 "fullSupply":true,
                 "emergency":$scope.orderType,
                 "order_status":rnr,
                 "item_code":category.item_code,
                 "item_name":category.item_name,
                 "beginningBalance":category.beginningBalance,
                 "stockInHand":category.stockInHand,
                 "adjustment":category.adjustment,
                 "amountNeeded":( (((category.beginningBalance-(-category.quantityReceived)
                     -(- category.adjustment))- category.beginningBalance) * 2)
                     - category.beginningBalance),
                 "quantityReceived":category.quantityReceived,
                 "quantityDispensed":category.quantityDispensed,
                 "stockOutDays":category.stockOutDays,
                 "quantityRequested":$("#"+amountToOrder).val().replace(',',''),
                 "reasonForRequestedQuantity":$("#"+sababu).val().replace(',',''),
                  "skiped":$("#"+skip).val()
 });

         }
     });
if (ordersRnR.length>0) {
    Helper.overlay(true);
    $http.post('/api/preparernr',ordersRnR).then(function(data) {
        Helper.overlay(false);
        var errors=data.data.errors;
        var errormsg=data.data.errormsg;
        var info=data.data.info;
        var msg=data.data.massage;
        var records=data.data.data;

        if (errors.length>0){
            swal(msg,errormsg,info) ;
        }
       else{

            swal(msg,records,info) ;

        }
        if (errors.length==0){
            $("#"+amountToOrder).val('');
            $("#"+sababu).val('');
            $("#"+skip).val('');
        }

    }, function(data){Helper.overlay(false);});

}
else{
    swal('Ooops!!!..','.....Nothing to save or to initiate.....','info') ;
}


        };

 $scope.UpdateItemDetails = function(rnr) {

     var itemss = [];
     var field_id;
     var ItemCode;
     var uom;
     $scope.msdItems.forEach(function(category) {

        var msd_product = category.item_code;
         uom = category.item_id;


         if($("#"+field_id).val() != ''){
             itemss.push({
                 "facility_id":facility_id,
                 "user_id":user_id,
                 "item_name":category.item_name,
                 "item_id":category.item_id,
                 "product_code":category.item_code,
                 "item_code":$("#"+ItemCode).val(),
                 "unit_of_measure":$("#"+uom).val(),
                 "msd_product":$("#"+msd_product).val(),
 });
             $("#"+ItemCode).val('');
             $("#"+uom).val('');
         }

     });

     Helper.overlay(true);
     $http.post('/api/UpdateItemDetails',itemss).then(function(data) {
         Helper.overlay(false);
         var msg=data.data.massage;
         var records=data.data.data;
         swal(msg,'','success') ;
     }, function(data){Helper.overlay(false);});
        };

 $scope.PharmacyLists=function(){

     Helper.overlay(true);
     $http.get('/api/PharmacyLists').then(function(data) {
         Helper.overlay(false);
         $scope.msdItems=data.data;
     }, function(data){Helper.overlay(false);});
 }
$scope.regex = /([^a-zA-Z0-9])/g;

 $scope.rnrSavedOrder=function(order_number){

     $http.post('/api/rnrSavedOrder',{order_number:order_number,facility_id:facility_id}).then(function(data) {

         $scope.rnrSavedOrders=data.data;
     } );
 }
        var  itemss=[];
 $scope.elmisProductProgramMapping=function(){
console.log(200);
     var program;

     $scope.msdItems.forEach(function(category) {

         program = category.item_code;
         if($("#"+program).val() != '' && $("#"+program).val() != null ){
             itemss.push({

                 "product_code":category.item_code,
 "item_id":category.item_id,
                 "program_code":$("#"+program).val(),

             });

         }
         else{
console.log(itemss);
             return;
         }
     });
if (itemss.length>0) {


     $http.post('/api/elmisProductProgramMapping',itemss).then(function(data) {
         itemss=[];
         $scope.pp=data.data;
     } );
 }

 }

 $scope.LoadrnrSavedOrder_numbers=function(){
     Helper.overlay(true);
     $http.post('/api/LoadrnrSavedOrder_numbers',{facility_id:facility_id}).then(function(data) {
         Helper.overlay(false);
         $scope.rnrOrderNumbers=data.data;
     }, function(data){Helper.overlay(false);});

 }
        $scope.LoadrnrSavedOrder_numbers();
        $scope.PrintRnRSaved=function () {

            //location.reload();
            var DocumentContainer = document.getElementById('rnr_order_id');
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
        $scope.PrintRnR=function () {

            //location.reload();
            var DocumentContainer = document.getElementById('rnr_id');
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
		
		$scope.startup();

        $scope.submitRnR=function (rnrID) {

            $http.post('/api/submit',{facility_id:facility_id,rnr_id:rnrID}).then(function(data) {

                $scope.pp=data.data;
                    $http.post('/api/Updatepreparedrnr',{message:data.data.message,rnr_status:data.data.status,status:data.data.message,order_number:rnrID,facility_id:facility_id}).then(function(data) {
                        Helper.overlay(false);
                        $scope.rnrSavedOrder(order_number);
                        var msg= data.data.message;
                        var respo=data.data.data;
                        swal(msg,'','success')
                    }, function(data){Helper.overlay(false);});
                    swal("info",data.data.message,"success")


            } ,
                function(response){
                    swal({
                        title: 'RnR DETAILS',
                        html: 'Something seems to have gone wrong. RnR details not saved.<p><b style="color: red">Check Your INTERNET CONNECTION</b>',
                        type: 'warning',
                        showCancelButton: false
                    });
                } );
        }
        $scope.RnRstatus=function (rnrID) {

            $http.post('/api/RnRstatus',{facility_id:facility_id,sourceOrderId:rnrID}).then(function(data) {

                $scope.statusesRnR=data.data;

                    swal("info",data.data.message,"success")

            },

                function(response){
                    swal({
                        title: 'RnR STATUS',
                        html: 'Something seems to have gone wrong.' +
                        '<p><b style="color: red">Check Your INTERNET CONNECTION</b>',
                        type: 'warning',
                        showCancelButton: false
                    });
                } );
        }

        $scope.getDetailedReportsdepartmentally = function (item) {
            $http.post('/api/getDetailedReportsdepartmentally', {
                "start": item.start,
                "end": item.end,
                "dept_id": 4,
                "facility_id": facility_id
            }).then(function (data) {
                $scope.cashdetailedData = data.data[0];
                $scope.insurancedetailedData = data.data[1];
                $scope.exemptiondetailedData = data.data[2];

                $scope.cashdetailedTotal = $scope.cashsum();
                $scope.exemptiondetailedTotal = $scope.exemptionsum();
                $scope.insurancedetailedTotal = $scope.insurancesum();

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
        $scope.cashsum = function () {
            var total = 0;
            for (var i = 0; i < $scope.cashdetailedData.length; i++) {
                total -= -($scope.cashdetailedData[i].sub_total);
            }
            return total;
        }
        $scope.insurancesum = function () {
            var total = 0;
            for (var i = 0; i < $scope.insurancedetailedData.length; i++) {
                total -= -($scope.insurancedetailedData[i].sub_total);
            }
            return total;
        }
        $scope.exemptionsum = function () {
            var total = 0;
            for (var i = 0; i < $scope.exemptiondetailedData.length; i++) {
                total -= -($scope.exemptiondetailedData[i].sub_total);
            }
            return total;
        }


        $scope.pharmacashprint=function () {

            //location.reload();
            var DocumentContainer = document.getElementById('pharmcash_id');
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

    }

})();
/**
 * Created by USER on 2017-03-08.
 */