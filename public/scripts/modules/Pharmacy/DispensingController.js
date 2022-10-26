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
        .controller('DispensingController', DispensingController);

    function DispensingController($http, $auth, $rootScope,$state,$location,$scope,Excel,$timeout,$interval,$uibModal,$mdDialog,Helper) {

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
        $scope.dispensing_array_requisitions=[];
        $scope.verification_array_requisitions=[];
        //patients list to dispense
        $scope.Dispensing_queue=function () {
            $http.get('/api/Dispensing_queue/'+facility_id).then(function(data) {
                $scope.patients=data.data;
                $scope.patientsTodispense="";
               // //////////console.log($scope.patients);

            });
        }
        $scope.Dispensing_queue();

 $scope.Dispensing_prescription_vefiry_queue=function () {
            $http.get('/api/Dispensing_prescription_vefiry_queue/'+facility_id).then(function(data) {
                $scope.patient_to_verify=data.data;

               // //////////console.log($scope.patients);

            });
        }
        $scope.Dispensing_prescription_vefiry_queue();

        $scope.patient_to_verify_prescription=function (visit_id) {
            $http.get('/api/patient_to_verify/'+visit_id).then(function(data) {
                $scope.patientsToVerify=data.data;
                //////console.log($scope.patientsTodispense);
                //$scope.prescription_receipt=[];
            });
        }

		$scope.patient_to_dispense=function (visit_id) {
            $http.get('/api/patient_to_dispense/'+visit_id).then(function(data) {
                $scope.patientsTodispense=data.data;
                //////console.log($scope.patientsTodispense);
                $scope.prescription_receipt=[];
            });
        }
$scope.balanceCheck=function (dawa,$index) {
    $http.post('/api/balanceCheck', {

        "main_category_id": dawa.main_category_id,

        "item_id": dawa.item_id,

        "facility_id": facility_id,
        "user_id": user_id

    }).then(function(data) {
        $scope.salio=data.data[0].balance;
$scope.indexess=$index;
            });
        }


        $scope.Item_to_dispense=function (orderview) {
            //////console.log(orderview)

            var item_id=orderview.item_id;
            var quantity=orderview.quantity;
            $scope.Vieworders=orderview;
            $scope.patientsTodispense="";
            $http.get('/api/batch_patient_dispensing_list/'+item_id+','+user_id+','+quantity).then(function(data) {
                $scope.batches=data.data;
               // //////////console.log(data.data);

            });


        }

        $scope.Viewprescriptions=function (orderview) {
            $scope.Viewprescription=orderview;

        }

        $scope.Vieworder=function (orderview) {

            var item_id=orderview.item_id;
            var quantity=orderview.quantity;
            $scope.Vieworders=orderview;
            $http.get('/api/batchdispensing_list/'+item_id+','+user_id).then(function(data) {
                $scope.batches=data.data;
               ////////console.log(data.data);

            });
        }

        //codes to display patients from database to dispense.
        $scope.searchPatientTodispense = function(searchKey) {


            $http.post('/api/searchPatientTodispense',{searchKey:searchKey}).then(function(data) {
                resdata = data.data;
            });
            return resdata;

        }
        $scope.searchPatientToverifyPrescription = function(searchKey) {

            $http.post('/api/searchPatientToverifyPrescription',{searchKey:searchKey}).then(function(data) {
                resdata = data.data;
            });
            return resdata;

        }


        $scope.batchesbalances="";
        //loading item batches balance from function loadBatchabove
         $scope.loadBatchBalance=function (batch_number,store_id,item_id) {

            $http.get('/api/loaddispensingBatchBalance/'+batch_number+','+store_id+','+item_id).then(function(data) {
                $scope.batchesbalances=data.data;
                ////////console.log(data.data)
            });
        }

//loading specific patient data from db to dispense
        $scope.LoadPatientTodispenseFromDB=function (mrn) {
            $http.post('/api/LoadPatientTodispenseFromDB',{mrn:mrn}).then(function(data) {
                $scope.patients=data.data;
                $scope.patientsTodispense="";
               // //////////console.log($scope.patients);

            });
        }



        //loading specific patient data from db to verify prescriptions
        $scope.LoadPatientTodispenseFromDBverifyprescriptions=function (mrn) {
            $http.post('/api/LoadPatientTodispenseFromDBverifyprescriptions',{mrn:mrn}).then(function(data) {
                $scope.patient_to_verify=data.data;
                $scope.patientsTodispense="";
               // //////////console.log($scope.patients);

            });
        }

        $scope.prescription_receipt=[];
        $scope.issue_to_patient=function (item1,item2) {

             //////console.log(item1,item2);
            if(item2.out_of_stock=='OS'){
                var received_from_id=undefined;
                var identifier=undefined;
                var store_id=undefined;
                var batch_no=undefined;
                var quantity_received=undefined;
            }
          else{
                var received_from_id=item1.selectedBatch.received_from_id;;
                var identifier=$scope.batchesbalances[0].id;
                var store_id=$scope.batchesbalances[0].store_id;
                var batch_no=item1.selectedBatch.batch_no;
                var quantity_received=$scope.batchesbalances[0].quantity_received - item2.quantity;
            }
            for(var i=0;i<$scope.prescription_receipt.length;i++)
               // if($scope.prescription_receipt[i].item_id == item2.item_id){ swal("Item already in your order list!"); return;}

            $scope.prescription_receipt.push({'item_id':item2.item_id,'frequency':item2.frequency,'duration':item2.duration,'instruction':item2.instruction,'dose':item2.dose,'os':item2.out_of_stock,'gender':item2.gender,'dob':item2.dob,'middle_name':item2.middle_name,'last_name':item2.last_name,'first_name':item2.first_name,'start_date':item2.start_date,'item_name':item2.item_name,'medical_record_number':item2.medical_record_number,'quantity':item2.quantity,'doctor':item2.name});


            var patient_order={
                'item_id':item2.item_id,
                'os':item2.out_of_stock,
                'quantity_dispensed':item2.quantity,
                'patient_id':item2.patient_id,
                'request_amount':item2.quantity,
                'received_from_id':received_from_id,
                'order_id':item2.id,
                'identifier':identifier,
                'user_id':user_id,
                'dispensing_status_id':2,
                'store_id':store_id,
                'batch_no':batch_no,
                'quantity_received':quantity_received,
            };
             ////////console.log(patient_order);

			 
            $http.post('/api/save_dispensed_item',patient_order).then(function(data) {
                swal('PRESCRIPTION', data.data.msg, data.data.status);
                if(data.data.status == 'success'){
                    $scope.Vieworders="";
                    $http.get('/api/patient_to_dispense/'+item2.visit_id).then(function(data) {
                        $scope.patientsTodispense=data.data;


                        if( $scope.patientsTodispense.length <1)
                        {
                            var itemed=$scope.prescription_receipt;

                            $scope.Dispensing_queue();

                        }
                    });
                }
            });
        }

        $scope.Verification_done=function (item2) {


            // for(var i=0;i<$scope.verification_array_requisitions.length;i++)
            //     if($scope.verification_array_requisitions[i].item_id == item2.item_id){ swal("Item already in your order list!"); return;}


            if(item2.quantity==undefined){
                swal('error','Please Enter Quantity','error')
                return;
            }
            else{

                var patient_precription={
                    'id':item2.id,'item_id':item2.item_id,'quantity':item2.quantity,"facility_id": facility_id,"user_id": user_id,
                    "item_type_id": item2.item_type_id, "item_price_id": item2.price_id,
                    "payment_filter":item2.bill_id, "bill_id":item2.bill_id,
                    "account_number_id": item2.visit_id, "visit_id": item2.visit_id,
                    "patient_id": item2.patient_id,
                };

                $scope.verification_array_requisitions.push({ 'cancellation_reason':0,'id':item2.id,'item_id':item2.item_id,'quantity':item2.quantity, "facility_id": facility_id,
                    "user_id": user_id, "item_type_id": item2.item_type_id,"item_price_id": item2.price_id, "payment_filter":item2.bill_id,
                    "bill_id":item2.bill_id,"account_number_id": item2.visit_id,"visit_id": item2.visit_id,"patient_id": item2.patient_id,
                });
                $('#quantity').val('');
                $scope.salio="";
                $scope.indexess=null;

            swal({
                title: 'Are you sure  You want To Verify This Item ?',

                text: "You won't be able to revert this!",
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

    $http.post('/api/save_verified_item',patient_precription).then(function(data) {
                        var msg=data.data.msg;
                        var status=data.data.status;
                       
                        if(status==1)
                        {
                            swal('',msg,'success')

                        }

                        $http.get('/api/patient_to_verify/'+item2.visit_id).then(function(data) {
                            $scope.patientsToVerify=data.data;

                            if($scope.patientsToVerify.length <1)
                            {
                                $scope.Dispensing_prescription_vefiry_queue();
								
								$http.post('/api/postMedicines_verified',$scope.verification_array_requisitions).then(function(data) {
                                $scope.Dispensing_prescription_vefiry_queue();
                                    $scope.verification_array_requisitions=[];
                                    $scope.Dispensing_queue();
                                if(data.data.status==1){




swal('',data.data.msg,'success')
}
                                else if(data.data.status==0){{

    swal('',data.data.msg,'error')
}
}


                                });
                                


                            }



                        });
                    });


            }, function (dismiss) {
                // dismiss can be 'cancel', 'overlay',
                // 'close', and 'timer'
                if (dismiss === 'cancel') {
                    $scope.verification_array_requisitions=[];
                }
            })


        }
        }

        $scope.save_cancel_prescription=function (item2) {


            // for(var i=0;i<$scope.verification_array_requisitions.length;i++)
            //     if($scope.verification_array_requisitions[i].item_id == item2.item_id){ swal("Item already in your order list!"); return;}


            swal({
                title: 'Are you sure  You want To Reject?',

                text: "You won't be able to revert this Easly!",
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

//please write reasons for this discount

                swal({
                    title: 'Reasons for Rejection',
                    input: 'textarea',

                    showCancelButton: true,
                    inputValidator: function (value) {
                        return new Promise(function (resolve, reject) {
                            if (value) {
                                resolve()
                            } else {
                                reject('You need to write Reasons for This Rejection!')
                            }



                            //--------------------




                            //--------------------
                        })
                    }
                }).then(function (result) {




                var patient_precription={
                    'id':item2.id,
                    'quantity':item2.quantity,
                    'cancellation_reason':result,
                    'user_id':user_id,
                    'item_id':item2.item_id
                };

                $http.post('/api/save_cancel_prescription',patient_precription).then(function(data) {

                    // $scope.verification_array_requisitions.push({'cancellation_reason':result,'id':item2.id,'item_id':item2.item_id,'quantity':item2.quantity});


                    $scope.Vieworders="";
                    $http.get('/api/patient_to_verify/'+item2.visit_id).then(function(data) {
                        $scope.patientsToVerify=data.data;


                        if( $scope.patientsToVerify.length <1)
                        {
                            $scope.Dispensing_prescription_vefiry_queue();
							
							$http.post('/api/postMedicines_verified',$scope.verification_array_requisitions).then(function(data) {
                                $scope.Dispensing_prescription_vefiry_queue();
                                $scope.verification_array_requisitions=[];
                                $scope.Dispensing_queue();
                                if(data.data.status==1){




                                    swal('',data.data.msg,'success')
                                }
                                else if(data.data.status==0){{

                                    swal('',data.data.msg,'error')
                                }
                                }


                            });

                        }



                    });
                });

                })

            }, function (dismiss) {
                // dismiss can be 'cancel', 'overlay',
                // 'close', and 'timer'
                if (dismiss === 'cancel') {
                    $scope.verification_array_requisitions=[];
                }
            })
        }






        $scope.prescription_printer=function (item1,item2) {

            // for(var i=0;i<$scope.prescription_receipt.length;i++)
            // if($scope.prescription_receipt[i].item_id == item2.item_id){ swal("Item already in your order list!"); return;}
            //
            $scope.prescription_receipt.push({'item_id':item2.item_id,'frequency':item2.frequency,'duration':item2.duration,'instruction':item2.instruction,'dose':item2.dose,'os':item2.out_of_stock,'gender':item2.gender,'dob':item2.dob,'middle_name':item2.middle_name,'last_name':item2.last_name,'first_name':item2.first_name,'start_date':item2.start_date,'item_name':item2.item_name,'medical_record_number':item2.medical_record_number,'quantity':item2.quantity,'doctor':item2.name});


        }

$scope.Print_now=function (item) {
    $mdDialog.show({
        controller: function ($scope) {
            $scope.prescriptions = item;
            $scope.date = new Date();
        },
        templateUrl: '/views/Pharmacy/prescriptionModal.html',
        parent: angular.element(document.body),
        clickOutsideToClose: true,
        fullscreen: false,
        });

    //
    //
    // var object =item;
    // var modalInstance = $uibModal.open({
    //     templateUrl: '/views/Pharmacy/prescriptionModal.html',
    //     size: 'lg',
    //     animation: true,
    //     controller: 'PrescriptionController',
    //     resolve:{
    //         object: function () {
    //             return object;
    //         }
    //     }
    // });

}
        $scope.Cancelverification=function () {
           $scope.patientsToVerify="";
            $scope.verification_array_requisitions=[];
            $scope.Dispensing_prescription_vefiry_queue(facility_id);

        }

        $scope.Cancelorders=function () {
           $scope.patientsTodispense="";
            $scope.Dispensing_queue(facility_id);

        }
        $scope.Cancelorder=function () {

            $scope.Vieworders="";

        }




        //codes to display items in pharmacy stores...received items.
        $scope.showSearch = function(searchKey) {


            $http.post('/api/searchItemReceived',{searchKey:searchKey}).then(function(data) {
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

 $scope.TargetedStoreUserToReceive=function (store_id) {
            // console.log(store_id)
            $http.get('/api/TargetedStoreUserToReceive/'+store_id+','+facility_id).then(function(data) {
                $scope.target_store_users=data.data;


            });
        }
        $scope.items_array_issue=[];
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
            }else if(issuing.user_targeted_id==undefined){
                swal(
                    'Error',
                    'Choose Targeted user receiving this item ',
                    'error'
                )
                return;
            }else if(issuing.remarks==undefined){
                swal(
                    'Error',
                    'Please enter remarks ',
                    'error'
                )
                return;
            }

            else if($scope.batchesbalances[0].quantity_received - issuing.quantity <0){
                swal(
                    'Error',
                    'No Enough Quantity from this Store only '+$scope.batchesbalances[0].quantity_received+ ' remained',
                    'error'
                )
                return;
            }



            else {


                for(var i=0;i<$scope.items_array_issue.length;i++){


                    if($scope.items_array_issue[i].item_id == issuing.selectedItem.item_id && $scope.items_array_issue[i].batch_no == $scope.batchesbalances[0].batch_no  ){ swal(issuing.selectedItem.item_name+" with Batch # "+ $scope.batchesbalances[0].batch_no+"  already in your order list ","","info"); return;}
                }



                $scope.items_array_issue.push({'item_id':issuing.selectedItem.item_id,'quantity_issued':issuing.quantity,
                    'user_targeted_id':issuing.user_targeted_id,
                    'item_name':issuing.selectedItem.item_name,
                    'received_from_id':issuing.selectedBatch.received_from_id,
                    'from':issuing.selectedBatch.store_name,
                    'identifier':$scope.batchesbalances[0].id,
                    'transaction_type_id':issuing.transaction_type_id.id,
                    'remarks':issuing.remarks,
                    'issued_store_id':issuing.selectedStore.id,
                    'store_type_id':issuing.selectedStore.store_type_id,
                    'store_name':issuing.selectedStore.store_name,
                    'balance_remained':($scope.batchesbalances[0].quantity_received- issuing.quantity),
                    'previousbalance':$scope.batchesbalances[0].quantity_received,
                    'user_id':user_id,

                    'batch_no':issuing.selectedBatch.batch_no,'facility_id':facility_id});
            }
            $('#itemID').val('');
            $('#qt').val('');
            $('#isi1').val('');

        }

        $scope.removeItemArray_issue = function(x){

            $scope.items_array_issue.splice(x,1);


        }
        $scope.pharmacy_item_returning=function () {
            $http.post('/api/pharmacy_item_returning',$scope.items_array_issue).then(function (data) {

                var sending = data.data.msg;
                var statusee = data.data.status;
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
                    $scope.items_array_issue=[];
                }

            });
        }


        $scope.reconsiliatedBatch = function(item) {
            $http.post('/api/reconsiliatedBatch',{item_id:item,user_id:user_id,sender:3}).then(function (data) {
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
                        "store_type_id":4,
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


    }

})();
/**
 * Created by USER on 2017-03-08.
 */