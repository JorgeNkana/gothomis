/**
 * Created by USER on 2017-02-13.
 */
(function() {

    'use strict';

    angular
        .module('authApp')
        .controller('regionController', regionController);

    function regionController($http, $auth, $rootScope,$state,$location,$scope,$timeout) {
        //loading menu
        var user_id=$rootScope.currentUser.id;
        $http.get('/api/getUsermenu/'+user_id ).then(function(data) {
            $scope.menu=data.data;
            ////console.log($scope.menu);

        });
   //Region Registrations
        $scope.region_registration=function (region) {
           ////console.log(region);
            $http.post('/api/region_registration',region).then(function(data) {
               // region.region.region_name ==' ';

                $scope.region_list();
                var sending=data.data;
                swal(
                    'Feedback..',
                    sending,
                    'success'
                )

            });

        }
        // $http.get('/api/user_rights/'+$rootScope.currentUser.id).then(function(data) {
        //     $scope.roles=data.data;
        //    //////console.log( $scope.roles);
        //
        // });
        //displaying region list when function clicked
        $scope.region_list=function () {

            $http.get('/api/region_registration').then(function(data) {
                $scope.regions=data.data;

            });
        }

        //displaying region list when browser loading
        $http.get('/api/region_registration').then(function(data) {
            $scope.regions=data.data;

        });


   //region update


        $scope.update=function (region) {
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


                $http.post('/api/update_region', region).then(function (data) {

$scope.e="";
                    //console.log($scope.e);
                    $scope.region_list();
                    swal(
                        'Feedback..',
                        'Updates Success...',
                        'success'
                    )
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


//regions delete
        $scope.delete=function (region,id) {
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

                $http.get('/api/delete/'+id).then(function(data) {


                    $scope.region_list();
                    swal(
                        'Feedback..',
                        'Deleted...',
                        'warning'
                    )
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



            //council_type_registration  CRUD


        $scope.council_type_registration=function (council_type) {

            $http.post('/api/council_type_registration',council_type).then(function(data) {

                $scope.council_type_list();
                var sending=data.data;
                swal(
                    'Feedback..',
                    sending,
                    'success'
                )

            });
        }


        $scope.council_type_list=function () {

            $http.get('/api/council_type_list').then(function(data) {
                $scope.council_types=data.data;

            });
        }

//displaying council types list when browser loading
        $http.get('/api/council_type_list').then(function(data) {
            $scope.council_types=data.data;

        });

   //council_type update


        $scope.council_type_update=function (council_type) {

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


                $http.post('/api/council_type_update', council_type).then(function (data) {


                    $scope.council_type_list();
                    swal(
                        'Feedback..',
                        'Updates Success...',
                        'success'
                    )
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


//council_type delete
        $scope.council_type_delete=function (council_type,id) {
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
                $http.get('/api/council_type_delete/'+id).then(function(data) {


                    $scope.council_type_list();
                    swal(
                        'Feedback..',
                        'Deleted...',
                        'warning'
                    )
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


        //councils registration CRUD



        $scope.council_registration=function (council) {

            $http.post('/api/council_registration',council).then(function(data) {

                $scope.council_list();
                var sending=data.data;
                swal(
                    'Feedback..',
                    sending,
                    'success'
                )
            });
        }

//displaying region list when function clicked
        $scope.council_list=function () {

            $http.get('/api/council_list').then(function(data) {
                $scope.councils=data.data;

            });
        }
//displaying region list when browser loading
        $http.get('/api/council_list').then(function(data) {
            $scope.councils=data.data;

        });

        //  update


        $scope.council_update=function (council) {
           ////console.log(council)
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
                $http.post('/api/council_update', council).then(function (data) {


                    $scope.council_list();
                    swal(
                        'Feedback..',
                        'Updates Success...',
                        'success'
                    )
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
        $scope.council_delete=function (council,id) {
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

                $http.get('/api/council_delete/'+id).then(function(data) {


                    $scope.council_list();
                    swal(
                        'Feedback..',
                        'Deleted...',
                        'warning'
                    )
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




        //residence registration CRUD



        $scope.residence_registration=function (residence) {
           ////console.log(residence)
            $http.post('/api/residence_registration',residence).then(function(data) {


                $scope.residence_list();
                var sending=data.data;
                swal(
                    'Feedback..',
                    sending,
                    'success'
                )

            });
        }


        $scope.residence_list=function () {

            $http.get('/api/residence_list').then(function(data) {
                $scope.residences=data.data;

            });

        }

        $scope.residence_list();

        //  update


        $scope.residence_update=function (residence) {
            var updates={'id':residence.residence_id,'residence_name':residence.residence_name,'council_id':residence.council_id};
           ////console.log(updates)
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



                $http.post('/api/residence_update', updates).then(function (data) {


                    $scope.residence_list();
                    swal(
                        'Feedback..',
                        'Updates Success...',
                        'success'
                    )
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
        $scope.residence_delete=function (residence,id) {
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
                $http.get('/api/residence_delete/'+id).then(function(data) {

                    $scope.residence_list();
                    swal(
                        'Feedback..',
                        'Deleted...',
                        'warning'
                    )
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