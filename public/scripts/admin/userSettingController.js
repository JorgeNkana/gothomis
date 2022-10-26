/**
 * Created by USER on 2017-02-14.
 */

(function() {

    'use strict';

    angular
        .module('authApp')
        .controller('userSettingController', userSettingController);

    function userSettingController($http, $auth, $rootScope,$state,$location,$scope) {

        //loading menu
        var user_id=$rootScope.currentUser.id;
        var facility_id=$rootScope.currentUser.facility_id;
        $http.get('/api/getUsermenu/'+user_id ).then(function(data) {
            $scope.menu=data.data;
            ////console.log($scope.menu);

        });

        // users lis
        $http.get('/api/patientsearch').then(function(data) {

            ////console.log(data)
            $scope.patients=data.data;


        });
		
		$scope.saveSignature = function(patient) {
    //console.log(angular.element('#username').value); // undefined
	if (localStorage.getItem("clientSignature") === null) {
     return sweetAlert("Please let Staff sign before saving!", "", "error");
     }
	const clientSignature = localStorage.getItem('clientSignature');
	var binaryData= "data:image/png;base64," +clientSignature;
	
	var postData={binaryData:binaryData,visitId:"doctor-"+patient.id};
								
	$http.post('/api/client-signature-pad', postData).then(function (response) {         
										 
										localStorage.removeItem('clientSignature');
										return sweetAlert(response.data.Message, "", response.data.status);
									});
};

        $http.get('/api/facility_list').then(function(data) {
            $scope.facilities=data.data;

        });
        $http.get('/api/professional_registration').then(function(data) {
            $scope.professsionals=data.data;
            ////console.log($scope.professsionals)

        });
        $scope.user_list=function () {
            $http.get('/api/user_list/'+facility_id).then(function(data) {
                $scope.users=data.data;
                ////console.log($scope.users)

            });
        }

        $scope.user_list();

        $scope.user_registration=function (user) {

            $http.post('/api/user_registration',user).then(function(data) {

                $scope.user_list();
                var sending=data.data;
                swal(
                    'Feedback..',
                    sending,
                    'success'
                )
            });
        }

        //  update


        $scope.user_update=function (user) {

            swal({
                title: 'Are you sure?',
                text: "  ",
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

                $http.post('/api/user_update', user).then(function (data) {


                    $scope.user_list();
                    var sending=data.data;
                    swal(
                        'Feedback..',
                        'User Details Updated..',
                        'success'
                    )
                })
            }, function (dismiss) {
                // dismiss can be 'cancel', 'overlay',
                // 'close', and 'timer'
                if (dismiss === 'cancel') {
                    swal(
                        {
                            timer:200,
                            title:'Cancelled'
                        }
                    )
                }
            })



        }


//  delete
        $scope.user_delete=function (user,id) {

            swal({
                title: 'Are you sure?',
                text: "  ",
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

                $http.get('/api/user_delete/'+id).then(function(data) {


                    $scope.user_list();
                    var sending=data.data;
                    swal(
                        'Feedback..',
                        'User Deleted....',
                        'error'
                    )
                })

            }, function (dismiss) {
                // dismiss can be 'cancel', 'overlay',
                // 'close', and 'timer'
                if (dismiss === 'cancel') {
                    swal(
                        {
                            timer:200,
                            title:'Cancelled'
                        }
                    )
                }
            })



        }

        $scope.password=function(password){
            var  user_password={'password':password,'user_id':user_id};
            $http.post('/api/check_password',user_password).then(function(data) {
                $scope.passwordCheck=data.data;
            })
        }

        $scope.resset_password=function(resset){

            var new_password=resset.new_password;
            var re_password=resset.re_password;
            var  user_password={'password':resset.new_password,'user_id':user_id};
            if(new_password==re_password){
                $http.post('/api/reset_password',user_password).then(function(data) {
                    var reset_password=data.data;

                    swal(
                        '',
                        reset_password,
                        'success'
                    )

                })
            }
            else{
                swal(
                    '',
                    'Password Mismatch',
                    'error'
                )
            }
            $scope.resset={};
            $scope.resset="";
        }

        $scope.Admin_resset_password=function (user) {

            swal({
                title: 'Are you sure you want To Reset Password of  '+user.name+'  ?',
                text: "  ",
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

                var new_password='12345678';
                var  user_password={'password':new_password,'id':user.id};
                $http.post('/api/check_password',user_password).then(function(data) {
                    var reset_password=data.data.msg;

                    swal(
                        'OK...!',
                        reset_password,
                        'success'
                    )

                })


            }, function (dismiss) {
                // dismiss can be 'cancel', 'overlay',
                // 'close', and 'timer'
                if (dismiss === 'cancel') {
                    swal(
                        {
                            timer:200,
                            title:'Cancelled'
                        }
                    )
                }
            })



        }

        $scope.Admin_resset_password_logout=function (user) {

            swal({
                title: 'Are you sure you want To Logout  '+user.name+'  ?',
                text: "  ",
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


                var  user_password={'id':user.id};
                $http.post('/api/check_password_logout',user_password).then(function(data) {
                    var reset_password=data.data.msg;

                    swal(
                        'OK...!',
                        reset_password,
                        'success'
                    )

                })


            }, function (dismiss) {
                // dismiss can be 'cancel', 'overlay',
                // 'close', and 'timer'
                if (dismiss === 'cancel') {
                    swal(
                        {
                            timer:200,
                            title:'Cancelled'
                        }
                    )
                }
            })



        }


    }

})();