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
        .controller('payment_typeController', payment_typeController);

    function payment_typeController($http, $auth, $rootScope,$state,$location,$scope,$timeout) {

        //loading menu
        var user_id=$rootScope.currentUser.id;
        var  facility_id=$rootScope.currentUser.facility_id;
        $http.get('/api/getUsermenu/'+user_id ).then(function(data) {
            $scope.menu=data.data;
            //////console.log($scope.menu);

        });


        
        //payment_type_registration  CRUD

        $http.get('/api/payment_type_list').then(function(data) {
            $scope.payment_types=data.data;

        });

        $scope.payment_type_registration=function (payment_type) {
              //console.log(payment_type);
            var payment_type_data={'payment_type_name':payment_type.payment_type_name,'facility_id':facility_id};
            $http.post('/api/payment_type_registration',payment_type_data).then(function(data) {
                var sending=data.data;
                swal(
                    'Feedback..',
                    sending,
                    'success'
                )

                $scope.payment_type_list();
                
            });
        }


        $scope.payment_type_list=function () {

            $http.get('/api/payment_type_list').then(function(data) {
                $scope.payment_types=data.data;

            });
        }



        //  update


        $scope.payment_type_update=function (payment_type) {
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


                $http.post('/api/payment_type_update', payment_type).then(function (data) {

                    var sending=data.data;
                    swal(
                        'Feedback..',
                        'Updated....',
                        'success'
                    )
                    $scope.payment_type_list();

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
        $scope.payment_type_delete=function (payment_type,id) {
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


                $http.get('/api/payment_type_delete/'+id).then(function(data) {

                    var sending=data.data;
                    swal(
                        'Feedback..',
                        'Deleted..',
                        'warning'
                    )
                    $scope.payment_type_list();

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
        
        
        
        
        
        //payment categories CRUD



        

        $http.get('/api/payment_category_list').then(function(data) {
            $scope.payment_categories=data.data;

        });

        $scope.payment_category_registration=function (payment_category) {
            ////console.log(payment_category);
            var payment_category_data={'category_description':payment_category.category_description,' facility_id': facility_id};
            $http.post('/api/payment_category_registration',payment_category_data).then(function(data) {
                var sending=data.data;
                swal(
                    'Feedback..',
                    sending,
                    'success'
                )

                $scope.payment_category_list();

            });
        }


        $scope.payment_category_list=function () {

            $http.get('/api/payment_category_list').then(function(data) {
                $scope.payment_categories=data.data;

            });
        }



        //  update


        $scope.payment_category_update=function (payment_category) {
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


                $http.post('/api/payment_category_update', payment_category).then(function (data) {

                    var sending=data.data;
                    swal(
                        'Feedback..',
                        'updated....',
                        'success'
                    )
                    $scope.payment_category_list();

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
        $scope.payment_category_delete=function (payment_category,id) {
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


                $http.get('/api/payment_category_delete/'+id).then(function(data) {

                    var sending=data.data;
                    swal(
                        'Feedback..',
                        'Deleted',
                        'warning'
                    )
                    $scope.payment_category_list();

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


        //payment sub categories CRUD





        $http.get('/api/payment_sub_category_list').then(function(data) {
            $scope.payment_sub_categories=data.data;

        });

        $scope.payment_sub_category_registration=function (payment_sub_category) {
            ////console.log(payment_sub_category);
            var payment_sub_category_data={'sub_category_name':payment_sub_category.sub_category_name,'pay_cat_id':payment_sub_category.pay_cat_id,'facility_id':facility_id};
            $http.post('/api/payment_sub_category_registration',payment_sub_category_data).then(function(data) {
                var sending=data.data;
                swal(
                    'Feedback..',
                    sending,
                    'success'
                )

                $scope.payment_sub_category_list();

            });
        }


        $scope.payment_sub_category_list=function () {

            $http.get('/api/payment_sub_category_list').then(function(data) {
                $scope.payment_sub_categories=data.data;

            });
        }



        //  update


        $scope.payment_sub_category_update=function (payment_sub_category) {
            ////console.log(payment_sub_category)
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


                $http.post('/api/payment_sub_category_update', payment_sub_category).then(function (data) {

                    var sending=data.data;
                    swal(
                        'Feedback..',
                        'Updated...',
                        'success'
                    )
                    $scope.payment_sub_category_list();

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
        $scope.payment_sub_category_delete=function (payment_sub_category,id) {
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


                $http.get('/api/payment_sub_category_delete/'+id).then(function(data) {

                    var sending=data.data;
                    swal(
                        'Feedback..',
                        'Deleted..',
                        'warning'
                    )
                    $scope.payment_sub_category_list();

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


        //payment status CRUD





        $http.get('/api/payment_status_list').then(function(data) {
            $scope.payment_status=data.data;

        });

        $scope.payment_status_registration=function (pay_status) {
            //console.log(pay_status);
            var payment_status_data={'payment_status':pay_status.payment_status};
            $http.post('/api/payment_status_registration',pay_status).then(function(data) {
                var sending=data.data;
                swal(
                    'Feedback..',
                    sending,
                    'success'
                )
                $scope.payment_status_list();

            });
        }


        $scope.payment_status_list=function () {

            $http.get('/api/payment_status_list').then(function(data) {
                $scope.payment_status=data.data;

            });
        }



        //  update


        $scope.payment_status_update=function (payment_status) {
            ////console.log(payment_status)
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


                $http.post('/api/payment_status_update', payment_status).then(function (data) {

                    var sending=data.data;
                    swal(
                        'Feedback..',
                        'Updated...',
                        'success'
                    )
                    $scope.payment_status_list();

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
        $scope.payment_status_delete=function (payment_status,id) {
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


                $http.get('/api/payment_status_delete/'+id).then(function(data) {

                    var sending=data.data;
                    swal(
                        'Feedback..',
                        'Deleted...',
                        'warning'
                    )
                    $scope.payment_status_list();

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