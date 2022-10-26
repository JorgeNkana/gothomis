/**
 * Created by USER on 2017-03-14.
 */
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

        .factory('Excel',function($window){
            var uri='data:application/vnd.ms-excel;base64,',
                template='<html xmlns:o="urn:schemas-microsoft-com:office:office" xmlns:x="urn:schemas-microsoft-com:office:excel" xmlns="http://www.w3.org/TR/REC-html40"><head><!--[if gte mso 9]><xml><x:ExcelWorkbook><x:ExcelWorksheets><x:ExcelWorksheet><x:Name>{worksheet}</x:Name><x:WorksheetOptions><x:DisplayGridlines/></x:WorksheetOptions></x:ExcelWorksheet></x:ExcelWorksheets></x:ExcelWorkbook></xml><![endif]--></head><body><table>{table}</table></body></html>',
                base64=function(s){return $window.btoa(unescape(encodeURIComponent(s)));},
                format=function(s,c){return s.replace(/{(\w+)}/g,function(m,p){return c[p];})};
            return {
                tableToExcel:function(tableId,worksheetName){
                    var table=$(tableId),
                        ctx={worksheet:worksheetName,table:table.html()},
                        href=uri+base64(format(template,ctx));
                    return href;
                }
            };
        })
        .controller('MediSuplyController', MediSuplyController);

    function MediSuplyController($http, $auth, $rootScope,$state,$location,$scope,Excel,$timeout,$interval,$uibModal,$mdDialog) {

        //loading menu
        var user_id=$rootScope.currentUser.id;
        var  facility_id=$rootScope.currentUser.facility_id;
        $http.get('/api/getUsermenu/'+user_id ).then(function(data) {
            $scope.menu=data.data;
            //////////////console.log($scope.menu);

        });



        $scope.exportToExcel=function(tableId){ // ex: '#my-table'
            var exportHref=Excel.tableToExcel(tableId,'WireWorkbenchDataExport');
            $timeout(function(){location.href=exportHref;},100); // trigger download
        }

        $scope.setTab = function(newTab){
            $scope.tab = newTab;
        };
        $scope.isSet = function(tabNum){
            return $scope.tab === tabNum;
        }
        $scope.oneAtATime=true;


        // var audio=new Audio('/ring-tone/ring11.mp3');
        $http.get('/api/dispensings/'+user_id ).then(function(data) {
            $scope.items_balances=data.data;
            ////////console.log(data.data);

        });
        var resdata =[];
        $scope.items_array_issue =[];


        $scope.Substore_item_list_issue=function (issuing,selectedPatient) {

            var patient_id=null;
            var visit_id=null;
            if(selectedPatient !=undefined){
                 patient_id=selectedPatient.patient_id;
                  visit_id=selectedPatient.account_id;
            }

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

            else if($scope.batchesbalances.quantity - issuing.quantity <0){
                swal(
                    'Error',
                    'No Enough Quantity from this Store only '+$scope.batchesbalances.quantity+ ' remained',
                    'error'
                )
                return;
            }





//                 for(var i=0;i<$scope.items_array_issue.length;i++){
//
//
//                     if($scope.items_array_issue[i].item_id == issuing.selectedItem.item_id){ swal(issuing.selectedItem.item_name+" already in your order list ","","info"); return;}
//
// else{



                var storeBalance=($scope.batchesbalances[0].quantity_received - issuing.quantity);
                var PreviusBalance=$scope.batchesbalances[0].quantity_received;

                $scope.items_array_issue.push({
                    'item_id': issuing.selectedItem.item_id,
                    'quantity_dispensed':issuing.quantity,
                    'quantity_issued':issuing.quantity,
                    'user_id': user_id,
                    'batch_no': $scope.batchesbalances[0].batch_no,
                    'identifier':$scope.batchesbalances[0].id,
                    'quantity_received': storeBalance,
                    'item_name':issuing.selectedItem.item_name,
                    'dispenser_id':$scope.batchesbalances[0].store_id,
                    'store_id':$scope.batchesbalances[0].store_id,
                    'received_from_id':issuing.selectedItem.received_from_id,
                    'dispensing_status_id':1,
                    'order_id':issuing.selectedBatch.id,
                    'patient_id':patient_id,
                    'visit_id': visit_id,

                    'facility_id': facility_id});
            $scope.batchesbalances="";
            $scope.item_issue.selectedBatch='';
                $('#btch').val('');
                $('#itm2').val('');
                $('#isi1').val('');
                $('#item').val('');


        // }
        // }
         }



        $scope.removeItemArray_issue = function(x){

            $scope.items_array_issue.splice(x,1);


        }

        $scope.substore_item_issuing = function () {

            $http.post('/api/save_dispensed_to_users',$scope.items_array_issue).then(function (data) {
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








        $scope.batchesbalances="";
        $scope.loadBatchBalance=function (batch_number) {

            $http.get('/api/loaddispensingBatchBalance/'+batch_number.batch_no+','+batch_number.store_id+','+batch_number.item_id).then(function(data) {
                $scope.batchesbalances=data.data;
                ////////console.log(data.data)
            });
        }


        //codes to display items in pharmacy stores...received items.
        $scope.showSearch = function(searchKey) {


            $http.post('/api/searchItemReceived',{searchKey:searchKey}).then(function(data) {
                resdata = data.data;
            });
            return resdata;

        }

        $scope.showSearchPatient = function(searchKey) {
            $http.post('/api/patientsToPoS',{"search":searchKey,"facility_id":facility_id}).then(function(data) {
                resdata = data.data;
            });
            return resdata;
        }
        //codes to display items in pharmacy stores...received items.
        $scope.showItem = function(searchKey) {


            $http.post('/api/searchItemdispensingReceived',{searchKey:searchKey}).then(function(data) {
                resdata = data.data;
            });
            return resdata;

        }



        //loading item batches from function ShowItem above
        $scope.loadBatch=function (item_id) {

            $http.get('/api/batchdispensing_list/'+item_id+','+user_id).then(function(data) {
                $scope.batches=data.data;
                ////////////console.log(data.data);

            });
        }




        $http.get('/api/pharmacy_transaction_type_list').then(function(data) {
            $scope.transtypes=data.data;

        });

        $http.get('/api/store_list/'+user_id).then(function(data) {
            $scope.stores=data.data;

        });
        $http.get('/api/Dispensing_stores_List/'+user_id).then(function(data) {
            $scope.dispensing_stores=data.data;

        });

        $http.get('/api/Sub_main_stores_List/' + user_id).then(function (data) {
            $scope.Sub_Main_stores = data.data;

        });



        //vendor_registration  CRUD



        $interval( function()
        {
            $scope.callAtInterval();
        }, 1000);

        $scope.callAtInterval = function() {

            // audio.play();

        }

        $scope.dispensing_item_receiving_list=function () {
            $scope.load=0;
            $scope.loading="Please wait while system loading";
            $scope.items="";



            $http.get('/api/dispensing_item_receiving_list/'+facility_id+','+user_id).then(function(data) {

                if(data.data.length<1){
                    swal(
                        'Error',
                        'No data Available',
                        'error'
                    )

                }
                else{
                    $scope.items=data.data;
                    $scope.xs=[];
                    $scope.ys=[];

                    for(var i=0;i< $scope.items.length; i++){
                        $scope.xs.push($scope.items[i].item_name);
                        $scope.ys.push($scope.items[i].quantity_received);
                    }

                    $scope.labels=$scope.xs ;
                    $scope.data =  $scope.ys;
                }


            });
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

        $scope.dispensing_array_requisitions=[];
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




    }

})();
/**
 * Created by USER on 2017-03-08.
 */