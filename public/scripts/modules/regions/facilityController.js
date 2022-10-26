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
        .controller('facilityController', facilityController);

    function facilityController($http, $auth, $rootScope,$state,$location,$scope,$timeout) {

        //loading menu
        var user_id=$rootScope.currentUser.id;
        $http.get('/api/getUsermenu/'+user_id ).then(function(data) {
            $scope.menu=data.data;
            ////console.log($scope.menu);

        });


        $http.get('/api/region_registration').then(function(data) {
                $scope.regions=data.data;

            });
        $http.get('/api/council_type_list').then(function(data) {
            $scope.council_types=data.data;

        });


        $http.get('/api/council_list').then(function(data) {
            $scope.councils=data.data;

        });
        //facility_type_registration  CRUD

        $http.get('/api/facility_type_list').then(function(data) {
            $scope.facility_types=data.data;

        });

        $scope.facility_type_registration=function (facility_type) {
            ////console.log(facility_type)
            $http.post('/api/facility_type_registration',facility_type).then(function(data) {
                var sending=data.data;
                swal(
                    'Feedback..',
                    sending,
                    'success'
                )

                $scope.facility_type_list();
                $scope.fading();
            });
        }


        $scope.facility_type_list=function () {

            $http.get('/api/facility_type_list').then(function(data) {
                $scope.facility_types=data.data;

            });
        }



        //facility update


        $scope.facility_type_update=function (facility_type) {
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



                $http.post('/api/facility_type_update', facility_type).then(function (data) {
                    $scope.facility_type_list();
                    var sending=data.data;
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
       
       

//facility delete
        $scope.facility_type_delete=function (facility_type,id) {
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



                $http.get('/api/facility_type_delete/'+id).then(function(data) {


                    $scope.facility_type_list();

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


        //facilitys registration CRUD



        $scope.facility_registration=function (facility) {
            ////console.log(facility);
            $http.post('/api/facility_registration',facility).then(function(data) {
                $scope.facility_code="";

                var sending=data.data;
                swal(
                    'Feedback..',
                    sending,
                    'success'
                )


//$scope.facility.facility_name=="";
            });
        }

//displaying facilities when function clicked
        $scope.facility_list=function () {

            $http.get('/api/facility_list').then(function(data) {
                $scope.facilities=data.data;

            });
        }



        //  update


        $scope.facility_update=function (facility) {
            ////console.log(facility)
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



                $http.post('/api/facility_update', facility).then(function (data) {


                    $scope.facility_list();
                    var sending=data.data;
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
        $scope.facility_delete=function (facility,id) {

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


                $http.get('/api/facility_delete/'+id).then(function(data) {


                    $scope.facility_list();
                    var sending=data.data;
                    swal(
                        'Feedback..',
                        'Item Deleted...',
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




        //department registration CRUD



        $scope.department_registration=function (department) {

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


                $http.post('/api/department_registration',department).then(function(data) {


                    var sending=data.data;
                    swal(
                        'Feedback..',
                        sending,
                        'success'
                    )
                    $scope.department.department_name=="";
                    $scope.department_list();


                });

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

//displaying department when function clicked
        $scope.department_list=function () {

            $http.get('/api/department_list').then(function(data) {
                $scope.departments=data.data;

            });
        }

//displaying department when browser loading
        $http.get('/api/department_list').then(function(data) {
            $scope.departments=data.data;

        });

        //  update


        $scope.department_update=function (department) {
            ////console.log(department)

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


                $http.post('/api/department_update', department).then(function (data) {


                    $scope.department_list();
                    var sending=data.data;
                    swal(
                        'Feedback..',
                        'Updates Success...',
                        'success',2000
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
        $scope.department_delete=function (department,id) {


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

                $http.get('/api/department_delete/'+id).then(function(data) {


                    $scope.department_list();
                    var sending=data.data;
                    swal(
                        'Feedback..',
                        'Item Deleted...',
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
return;
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



//Professioals Get Data From the database
        $scope.getprofessional=function () {

            $http.get('/api/professional_registration').then(function(data) {
                $scope.professional=data.data;
            });
        }

        //Professionals Registrations
        $scope.professional_registration=function (prof) {
            var comit=confirm('Are you sure you want to Register '+prof.prof_name);
            if(comit) {
                $http.post('/api/professional_registration',prof).then(function(data) {
                    //$scope.prof.prof_name="";
                    //$scope.region_list();
                    $scope.getprofessional();
                });
            }
        }

        $scope.getprofessional();
        //Professionals Update
        $scope.update_professional=function (professional) {
            var comit=confirm('Are you sure you want to Update '+professional.prof_name);
            if(comit) {
                $http.post('/api/update_professional', professional).then(function (data) {
                    $scope.good = professional.prof_name + " " + 'Successful Updated';
                    $scope.kol = data.status;
                    //$scope.region_list();
                    $scope.getprofessional();
                })
            }
            else{
                return false;
            }
        }

        //Professionals delete
        $scope.deleteprof=function (professional) {
            var comit=confirm('Are you sure you want to delete'+" "+professional.prof_name);
            if(comit){
                $http.get('/api/deleteprof/'+professional.id).then(function(data) {
                    $scope.getprofessional();
                })
            }
            else {
                return false;
            }
        }




    }

})();