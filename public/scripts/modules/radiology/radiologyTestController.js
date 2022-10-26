/**
 * Created by japhari on 07/03/2017.
 */

(function() {

    'use strict';

    angular
        .module('authApp')

        .controller('radiologyTestController', radiologyTestController);

    function radiologyTestController($http, $auth, $rootScope,$state,$location,$scope) {


        //Accordion

        $scope.oneAtATime = true;




        $scope.status = {
            isCustomHeaderOpen: false,
            isFirstOpen: true,
            isFirstDisabled: false
        };
        //Accordion end



        $scope.isNavCollapsed = false;
        $scope.isCollapsed = true;
        $scope.isCollapsedHorizontal = true;
        var user=$rootScope.currentUser;
        var user_name=$rootScope.currentUser.id;
        var facility_id=$rootScope.currentUser.facility_id;




        //user menu
        $scope.printUserMenu=function (user_id) {

            $http.get('/api/getUsermenu/'+user_id ).then(function(data) {
                $scope.menu=data.data;
                //console.log($scope.menu);

            });

        };

        $http.get('/api/getUserImage/'+user_name).then(function(data) {
            $scope.photo='/uploads/'+data.data[0].photo_path;
            //console.log($scope.photo);

        });

        //Equipments list with status
        $scope.getXrays=function () {

            $http.get('/api/getXrayImage').then(function(data) {
                $scope.Xrays=data.data;

            });


        }
        //Service Data
        $scope.getServices=function () {

            $http.get('/api/getServicedata').then(function(data) {
                $scope.servicesData=data.data;

            });


        }

        //Sub-Department list
        $scope.departments_list=function () {
            $http.get('/api/getdepartments').then(function(data) {
                $scope.departments=data.data;

            });


        }

        //Services per Devices
        $scope.device_services=function () {
            $http.get('/api/deviceServices').then(function(data) {
                $scope.ServedDevice=data.data;



                $scope.viewby = 10;
                $scope.ServedDevices = data.data.length;
                $scope.currentPage = 1;
                $scope.itemsPerPage = $scope.viewby;
                $scope.maxSize = 5; //Number of pager buttons to show

                $scope.setPage = function (pageNo) {
                    $scope.currentPage = pageNo;
                };



                $scope.setItemsPerPage = function(num) {
                    $scope.itemsPerPage = num;
                    $scope.currentPage = 1;
                }

            });


        }


        //Device names
        $scope.deviceName=function () {
            $http.get('/api/deviceName').then(function(data) {
                $scope.devicesdata=data.data;

            });


        }
        //Device denied
        $scope.serviceDenied=function () {
            $http.get('/api/deniedDevices').then(function(data) {
                $scope.deniedDevices=data.data;

            });


        }

        $scope.getXrays();
        $scope.getServices();
        $scope.departments_list();
        $scope.deviceName();
        $scope.device_services();
        $scope.serviceDenied();




        var user_id=$rootScope.currentUser.id;
        var facility=$rootScope.currentUser.facility_id;
        $scope.printUserMenu(user_id);

//radiology patients Queue
        $scope.imageStatus = function(patient_id){


            //console.log(patient_id);

            $http.get('/api/imageStatus/'+patient_id).then(function(data) {

                $scope.patient_orders=data.data;

                //console.log(data.data);
            });
        };

//Verify Radiology Images

        $scope.VerifyXray = function(id){

            //console.log(id);

            $http.get('/api/VerifyXray/'+id).then(function(data) {

                //console.log(data.data);
                swal(
                    'Updated Successfully',
                    'Image  Verified!',
                    'success'
                )
                $scope.getXrays();

            });
        };
        //Service registration
        $scope.serviceRegistration = function(devicesdata){
            var serviceData = {
                'sub_department_id':devicesdata.sub_department_name.id,
                'equipment_id':devicesdata.equipment_name.id,
                'item_id':devicesdata.item_name.id,
                'eraser':1
            }

            //console.log(serviceData);
            $http.post('/api/serviceRegistration',serviceData).then(function(data) {

                //console.log(data.data);
                swal(
                    'Registered Successfully',
                    'Service  Registered!',
                    'success'
                )
                devicesdata="";
                $scope.device_services();
            });

        };




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













    }

})();