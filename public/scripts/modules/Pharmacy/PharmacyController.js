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
        .controller('PharmacyController', PharmacyController);

    function PharmacyController($http, $auth, $rootScope,$state,$location,$scope,$timeout) {
        $scope.setTab = function(newTab){
            $scope.tab = newTab;
        };
        $scope.isSet = function(tabNum){
            return $scope.tab === tabNum;
        }
        //loading menu
        var user_id=$rootScope.currentUser.id;
        var  facility_id=$rootScope.currentUser.facility_id;
        $http.get('/api/getUsermenu/'+user_id ).then(function(data) {
            $scope.menu=data.data;
            //////console.log($scope.menu);

        });

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

        $scope.populateInArray=function (selected_user_store) {


             var checking={'user_id':user_store[0].id,'store_id':selected_user_store.id};

            // $http.post('/api/store_user_checking',checking ).then(function(data) {
            //      ckecks=data.data.counti;
            //
            // });



                if(selected_user_store.value1==false){

                }
                else{
                    $scope.User_store_populate.push({
                        'user_name':user_store[0].name,
                        'user_id':user_store[0].id,
                        'store_id':selected_user_store.id,
                        'store_name':selected_user_store.store_name,
                    });



                }


            }



$scope.store_user_configure=function () {




if($scope.User_store_populate.length<1){
    swal(
        'Error',
        'Nothing to save',
        'error'
    )
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

        //vendor_registration  CRUD
        
        

        $scope.vendor_registration=function (vendor) {
            
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
        $scope.invoice_delete=function (id) {
            //console.log(id);
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
                $scope.transtype_list();
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
        //store type_registration  CRUD



        $scope.store_type_registration=function (storetype) {

            var storetype_data={'store_type_name':storetype.store_type_name};
            //console.log(storetype_data);
            $http.post('/api/store_type_registration',storetype_data).then(function(data) {

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

                $scope.store_type_list();

            });
        }


        $scope.store_type_list=function () {

            $http.get('/api/store_type_list').then(function(data) {
                $scope.storetypes=data.data;

            });
        }

        $scope.store_type_list();

        //  update


        $scope.store_type_update=function (storetype) {
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


                $http.post('/api/store_type_update', storetype).then(function (data) {

                    var sending=data.data.msg;
                    swal(
                        'Feedback..',
                        sending,
                        'success'
                    )
                    $scope.store_type_list();

                })


                $scope.store_type_list();

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
        $scope.store_type_delete=function (id) {
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


                $http.get('/api/store_type_delete/'+id).then(function(data) {

                    var sending=data.data.msg;
                    swal(
                        'Feedback..',
                        sending,
                        'warning'
                    )
                    $scope.store_type_list();

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


        //store requests status_registration  CRUD



        $scope.store_request_status_registration=function (requesstatus) {

            var requesstatus_data={'store_request_status':requesstatus.store_request_status};
            //console.log(requesstatus_data);
            $http.post('/api/store_request_status_registration',requesstatus_data).then(function(data) {
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

                $scope.store_request_status_list();

            });
        }


        $scope.store_request_status_list=function () {

            $http.get('/api/store_request_status_list').then(function(data) {
                $scope.requeststatuses=data.data;

            });
        }

        $scope.store_request_status_list();

        //  update


        $scope.store_request_status_update=function (requeststatus) {

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


                $http.post('/api/store_request_status_update', requeststatus).then(function (data) {

                    var sending=data.data.msg;
                    swal(
                        'Feedback..',
                        sending,
                        'success'
                    )
                    $scope.store_request_status_list();

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
        $scope.store_request_status_delete=function (id) {
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


                $http.get('/api/store_request_status_delete/'+id).then(function(data) {

                    var sending=data.data.msg;
                    swal(
                        'Feedback..',
                        sending,
                        'warning'
                    )
                    $scope.store_request_status_list();

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


        //stores_registration  CRUD



        $scope.store_registration=function (store) {

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


        $scope.store_list=function () {

            $http.get('/api/store_list/'+user_id).then(function(data) {
                $scope.stores=data.data;

            });
        }

        $scope.store_list();

        //  update


        $scope.store_update=function (store) {
            console.log(store)
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



//  delete
        $scope.store_delete=function (store,id) {
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


                $http.get('/api/store_delete/'+id).then(function(data) {

                    var sending=data.data.msg;
                    swal(
                        'Feedback..',
                        sending,
                        'warning'
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




    }

})();
/**
 * Created by USER on 2017-03-08.
 */