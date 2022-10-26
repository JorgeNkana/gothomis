/**
 * Created by USER on 2017-09-17.
 */
/**
 * Created by Mazigo Jr on 2017-05-02.
 */

(function () {
    var app = angular.module('authApp');
    app.controller('General_AppointmentController',['$scope','$http','$rootScope','$state','$uibModal','$window','$mdDialog',
        function ($scope,$http,$rootScope,$state,$uibModal,$window,$mdDialog) {

            var user_id = $rootScope.currentUser.id;
            var facility_id = $rootScope.currentUser.facility_id;
            //searching user for assigning a dept to dispens
            var user_dept=[];
            $scope.SearchUser=function (seachKey) {

                var searchUser={'userKey':seachKey,'facility_id':facility_id};
                $http.post('/api/getUserToSetStoreToAccess',searchUser ).then(function(data) {
                    user_dept=data.data;

                });
                return user_dept;
            }
            $scope.User_dept_populate=[];
            var ckecks;

            $scope.populateInArray=function (selected_user_dept,user) {
                console.log(selected_user_dept,user)

                var checking={'user_id':user.id,'dept_id':selected_user_dept.id};
 
                if(selected_user_dept.value1==false){

                }
                else{
                    $scope.User_dept_populate.push({
                        'user_name':user.name,
                        'user_id':user.id,
                        'dept_id':selected_user_dept.id,
                        'dept_name':selected_user_dept.department_name,
                        'department_name':selected_user_dept.department_name,
                    });

                    

                }


            }



            $scope.dept_user_configure=function () {




                if($scope.User_dept_populate.length<1){
                    swal(
                        'Error',
                        'Nothing to save',
                        'error'
                    )
                }

                else {
                    $http.post('/api/dept_user_configure', $scope.User_dept_populate).then(function (data) {
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
                            $scope.User_dept_populate=[];
                        }
                    });
                }

            }








            $scope.removeItem = function(x){

                $scope.User_dept_populate.splice(x,1);

            }

            $scope.SelectedUserWithDeptAccess = function(user_id){

                $http.get('/api/SelectedUserWithDeptAccess/'+user_id ).then(function(data) {
                    $scope.access_givens=data.data;

                });

            }

            $scope.SelectedUserWithDeptAccess_list = function(){

                $http.get('/api/SelectedUserWithDeptAccess/'+user_id ).then(function(data) {
                    $scope.depts=data.data;

                });

            }
            $scope.SelectedUserWithDeptAccess_list();
            $scope.Remove_user_dept_access = function(id){

                $http.get('/api/Remove_user_dept_access/'+id ).then(function(data) {
                    $scope.removeds=data.data;
                    swal('','Access removed','success')

                });

            }

            $scope.department_list = function () {

                $http.get('/api/department_list').then(function (data) {
                    $scope.departments = data.data;

                });
            }
            var patientData = [];

            $scope.showSearch = function (searchKey) {
                $http.post('/api/getAllOpdPatients', {
                    "facility_id": facility_id,searchKey:searchKey
                }).then(function (data) {
                    patientData = data.data;
                });
                return patientData;
            }

            $scope.department_list(); 
            
//             $scope.appointment_list = function () {
// var vall={facility_id:facility_id};
//                 $http.post('/api/appointment_list',vall).then(function (data) {
//                     $scope.appointments = data.data;
//
//                 });
//             }
            
            $scope.appointment_list = function (item) {
var vall={user_id:user_id,appoint_date:item,facility_id:facility_id};
                $http.post('/api/appointment_list',vall).then(function (data) {
                    $scope.appointments = data.data;

                });
            }

            $scope.today_appointments = function () {
var vall={user_id:user_id,facility_id:facility_id};
                $http.post('/api/today_appointments',vall).then(function (data) {
                    $scope.today_appointment = data.data;

                });
            }
            $scope.today_appointments();

            $scope.appointment_dated = function () {
                var vall={user_id:user_id,facility_id:facility_id};

                $http.post('/api/appointment_dated',vall).then(function (data) {
                    $scope.appointment_date = data.data;

                });
                $scope.today_appointments();
            }
            $scope.appointment_dated();
            $scope.appointment_stages = function (item) {
var vall={
    "start_date": item.start_date,
    "end_date": item.end_date,
    "facility_id": facility_id,user_id:user_id};
                $http.post('/api/appointment_stages',vall).then(function (data) {
                    $scope.stages = data.data;

                });
            }

           // $scope.appointment_list();
            $scope.Save_general_appointments = function (item,patient) {
                if (item == undefined) {
                        swal('Fill all required fileds', '', 'warning');
                        return;
                    }
                    if (item.appoint_date == undefined) {
                        swal('Fill Appointment Date', '', 'warning');
                        return;
                    }
                    var appointDate = item.appoint_date;


                    if (item.appoint_date instanceof Date) {
                        appointDate = item.appoint_date.toISOString();
                    }
                    if (item.appoint_date == undefined) {
                        return;
                    }


                    if (appointDate != '' && ((new Date()).getFullYear() > parseInt(appointDate.substring(0, 4)) ||
                        ((new Date()).getFullYear() == parseInt(appointDate.substring(0, 4)) && ((new Date()).getMonth() + 1)> parseInt(appointDate.substring(appointDate.indexOf("-") + 1, 7))) ||
                        ((new Date()).getFullYear() == parseInt(appointDate.substring(0, 4)) && ((new Date()).getMonth() + 1) == parseInt(appointDate.substring(appointDate.indexOf("-") + 1, 7)) && ((new Date()).getDate()) > parseInt(appointDate.substring(appointDate.lastIndexOf("-") + 1, 10))))) {
                        $scope.app.appoint_date = '';

                        swal('Previous dates Restricted!', '', 'warning');
                        return;
                    }               
 var dataa={patient_id:patient.patient_id,status:0,facility_id:facility_id,user_id:user_id,dept_id:item.dept_id,appoint_date:item.appoint_date,description:item.description}
                $http.post('/api/Save_general_appointments',dataa).then(function (data) {
                    $scope.datas = data.data;
if(data.data.status==1){
    swal('',data.data.msg,'success') ;
    $scope.appointment_dated();
    $scope.today_appointments();
}
                    else{
    swal('',data.data.msg,'error') ;
}
                });
            }
            
            $scope.Update_general_appointment = function (item,id,status) {
 
 if (item == undefined) {
                        swal('Fill all required fileds', '', 'warning');
                        return;
                    }
                    if (item.appoint_date == undefined) {
                        swal('Fill Appointment Date', '', 'warning');
                        return;
                    }
                    var appointDate = item.appoint_date;


                    if (item.appoint_date instanceof Date) {
                        appointDate = item.appoint_date.toISOString();
                    }
                    if (item.appoint_date == undefined) {
                        return;
                    }

if(status==3 && appointDate != '' && ((new Date()).getFullYear() > parseInt(appointDate.substring(0, 4)) ||
                        ((new Date()).getFullYear() == parseInt(appointDate.substring(0, 4)) && ((new Date()).getMonth() + 1)> parseInt(appointDate.substring(appointDate.indexOf("-") + 1, 7))) ||
                        ((new Date()).getFullYear() == parseInt(appointDate.substring(0, 4)) && ((new Date()).getMonth() + 1) == parseInt(appointDate.substring(appointDate.indexOf("-") + 1, 7)) && ((new Date()).getDate()) > parseInt(appointDate.substring(appointDate.lastIndexOf("-") + 1, 10))))) {

                        swal('Previous dates Restricted!', '', 'warning');
                        return;
                    } 
             
                $http.post('/api/Update_general_appointment',{id:id,appoint_date:item.appoint_date,status:status}).then(function (data) {
                    $scope.datas = data.data;
if(data.data.status==1){
    swal('',data.data.msg,'success') ;
   $scope.appointment_dated();
    $scope.today_appointments();
}
                    else{
    swal('',data.data.msg,'error') ;
}
                });
                
            }

        }]);

})();