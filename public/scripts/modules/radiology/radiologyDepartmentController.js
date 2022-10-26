/**
 * Created by japhari on 07/03/2017.
 */

(function () {
    'use strict';
    angular
        .module('authApp')
        .controller('radiologyDepartmentController', radiologyDepartmentController);
    function radiologyDepartmentController($http, $auth, $rootScope, $state, $location, $scope, $window, $uibModal, toastr, $mdDialog) {
        var user = $rootScope.currentUser;
        var user_name = $rootScope.currentUser.id;
        var username = $rootScope.currentUser.name;
        var facility_id = $rootScope.currentUser.facility_id;
        //Return equipment lists
        $scope.equipments_list = function () {
            $http.get('/api/getEquipmentStatus').then(function (data) {
                $scope.equipments = data.data;
            });
        }
        $scope.departments_list=function () {
            $http.get('/api/getdepartments').then(function(data) {
                $scope.departments=data.data;
            });
        }
        $scope.cancel = function () {
            $mdDialog.hide();
        };
        $scope.serviceonnoff = function () {
            $http.get('/api/OnnOffDevices/' + facility_id).then(function (data) {
                $scope.onnoff = data.data;
            });
            $http.get('/api/getdepartments').then(function (data) {
                $scope.departments = data.data;
            });
        }
        var patientData = [];

        $scope.showService = function(searchKey) {
            $http.post('/api/getServicedata', {
                "facility_id": facility_id,
                "search": searchKey
            }).then(function(data) {
                patientData = data.data;
            });
            return patientData;
        }
        $scope.showUsers = function(searchKey) {
            $http.post('/api/getUsers', {
                "facility_id": facility_id,
                "search": searchKey
            }).then(function(data) {
                patientData = data.data;
            });
            return patientData;
        }
        $scope.showDevices = function (device) {
            $mdDialog.show({
                controller: function ($scope) {
                   $scope.device = device;
                    $scope.service = function () {
                        $http.get('/api/OnnOffDevices/' + facility_id).then(function (data) {
                            $scope.onnoff = data.data;
                        });
                        $http.get('/api/getdepartments').then(function (data) {
                            $scope.departments = data.data;
                        });
                    }
                   console.log(device);
                    $scope.equipmentOnOff = function (id, device) {
                        console.log(device);
                        var equip_id = device;
                        var status_named = id.status_name.id;
                        var equipmentonnoff = {
                            'equipment_name': equip_id,
                            'equipment_status': status_named,
                            'user_id': user_name,
                            'facility_id': facility_id
                        };
                        $http.post('/api/equipmentOnOff', equipmentonnoff).then(function (data) {
                        var successfuly= data.data;
                        if(successfuly==1){
                            swal(
                                'Equipment',
                                'Updated Successfully!',
                                'success'
                            )
                            $scope.service();
                        }
                        else   swal(
                            'Error',
                            'Detected',
                            'error'
                        )

                        });
                    };
                    $http.get('/api/getEquipmentStatus').then(function (data) {
                        $scope.equipments = data.data;
                    });
                    $scope.cancel = function () {
                        $mdDialog.hide();
                        $scope.service();
                    };
                },
                templateUrl: '/views/modules/radiology/deviceData.html',
                parent: angular.element(document.body),
                clickOutsideToClose: false,
                fullscreen: $scope.customFullscreen
            })

        };
        $scope.equipment_registration = function (ev) {
            $mdDialog.show({
                controller: function ($scope) {
                    $scope.cancel = function () {
                        $mdDialog.hide();
                    };
                    $http.get('/api/deviceName/' + facility_id).then(function (data) {
                        $scope.devicesdata = data.data;
                    });
                    $scope.getServices = function () {

                        $http.get('/api/getItemCategory').then(function (data) {
                            $scope.category = data.data;
                        });

                    }
                    $http.get('/api/getEquipmentStatus').then(function (data) {
                        $scope.equipments = data.data;
                    });
                },
                templateUrl: '/views/modules/radiology/equipment_registration.html',
                parent: angular.element(document.body),
                clickOutsideToClose: false,
                fullscreen: $scope.customFullscreen
            })
        };
        $scope.radiology = function (ev) {
            $mdDialog.show({
                controller: function ($scope) {
                    $scope.cancel = function () {
                        $mdDialog.hide();
                    };
                    $http.get('/api/getRegistered_departments').then(function (data) {
                        $scope.registered = data.data;
                    });
                },
                templateUrl: '/views/modules/radiology/departments.html',
                parent: angular.element(document.body),
                clickOutsideToClose: false,
                fullscreen: $scope.customFullscreen
            })
        };
        $scope.userRadiology = function (radiology) {
            $mdDialog.show({
                controller: function ($scope) {
                    $scope.UserRadiology = radiology;
                    console.log(radiology);
                    var xray_sub_dept = radiology.id;
                    var permitted = {
                        'section_id': xray_sub_dept,
                        'facility_id': facility_id

                    };

                    $http.post('/api/permittedUsers',permitted).then(function(data) {
                        $scope.imagingUsers=data.data;

                    });
                    $scope.loadUsersDepartment = function () {
                        $http.post('/api/permittedUsers',permitted).then(function(data) {
                            $scope.imagingUsers=data.data;

                        });
                    }
                    $scope.cancel = function () {
                        $mdDialog.hide();
                    };
                    $scope.assignPermision = function (dept,user) {
                        var sub_dept_id = dept.id;
                        var user_id = user.item_name.id;
                        var assignPermission = {
                            'section_id': sub_dept_id,
                            'technologist_id': user_id,
                            'isAllowed': true

                        };
                        $http.post('/api/assignPermission', assignPermission).then(function (data) {
                            var message = data.data.message;
                            var status = data.data.status;
                            console.log(message);
                            if (status == 0) {
                                toastr.error(message);
                            }
                            else {
                                swal(
                                    message,
                                    'SUCCESSFULLY',
                                    'success'
                                )
                            }
                            $scope.loadUsersDepartment();
                        });
                    }
                    $scope.checkXrayPermissions = function (perms,userData,dept_id) {
                       var dept_id = dept_id;
                       console.log(dept_id);
                        var userPermitted = perms.permission;
                        var user_id = userData.id;
                        console.log(user_id);
                        console.log(userPermitted);
                        var permittedUpdates = {
                            'permission': userPermitted,
                            'dept_id': dept_id,
                            'userAccess': user_id

                        };
                        $http.post('/api/userPermittedUpdates', permittedUpdates).then(function (data) {
                            var message = data.data.message;
                            var status = data.data.status;

                            if (status == 0) {
                                toastr.error(message);
                            }
                            else {
                                swal(
                                    message,
                                    'SUCCESSFULLY',
                                    'success'
                                )
                            }
                            // $scope.loadUsersDepartment();
                        });
                    }
                    $http.get('/api/getRegistered_departments').then(function (data) {
                        $scope.registered = data.data;
                    });
                },
                templateUrl: '/views/modules/radiology/UserRadiology.html',
                parent: angular.element(document.body),
                clickOutsideToClose: false,
                fullscreen: $scope.customFullscreen
            })
        };

        $scope.equipmentRegistration = function (equipment) {
            var equipmentData = {
                'equipment_name': equipment.name,
                'description': equipment.description,
                'equipment_status_id': equipment.status_name.id,
                'facility_id': facility_id,
                'eraser': 1,
                'sub_department_id': equipment.sub_department_name.id,
                'user_id': user_name
            };
            $http.post('/api/equipmentRegistration', equipmentData).then(function (data) {
                toastr.success('Successfully Registered', 'Equipment');
                $scope.serviceonnoff();
            });
        };
        //Registered Departments
        $scope.departments_registered = function () {
            $http.get('/api/getRegistered_departments').then(function (data) {
                $scope.registered = data.data;
            });
            $http.get('/api/getdepartments').then(function (data) {
                $scope.departments = data.data;
            });
            $http.get('/api/OnnOffDevices/' + facility_id).then(function (data) {
                $scope.onnoff = data.data;
            });
        }
        //Equipments list
        $scope.registered_list = function () {
            $http.get('/api/getEquipments_list').then(function (data) {
                $scope.getEquipmentStatus = data.data;
            });
        }
        var patientOpdPatients = [];
        $scope.showSearch = function (searchKey) {
            $http.post('/api/getRegisteredServices', {
                "searchKey": searchKey,
                "facility_id": facility_id
            }).then(function (data) {
                patientOpdPatients = data.data;
            });
            return patientOpdPatients;
        }

        $scope.equipmentOnOff = function (id, device) {
            var equip_id = device;
            var status_named = id.status_name.id;

            var equipmentonnoff = {
                'equipment_name': equip_id,
                'equipment_status': status_named,
                'user_id': user_id,
                'facility_id': facility_id
            };
            $http.post('/api/equipmentOnOff', equipmentonnoff).then(function (data) {

                //console.log(data.data);
                swal(
                    'Equipment',
                    'Updated Successfully!',
                    'success'
                )
                $scope.serviceonnoff();
            });
        };
        //Equipment Status
        $scope.getStatus = function () {
            $http.get('/api/getEquipments_status/' + facility_id).then(function (data) {
                $scope.statusEquipment = data.data;
            });
        }
        $scope.sum = function () {
            var total = 0;
            for (var i = 0; i < $scope.rejesta.length; i++) {
                total += ($scope.rejesta[i].sub_total);
            }
            return total;
        }
        $scope.getRejesta = function (item) {
            //console.log(item);
            $http.post('/api/getRejestaReport',
                {
                    "start": item.start,
                    "end": item.end,
                    "facility_id": facility_id
                }).then(function (data) {
                $scope.rejesta = data.data;
                $scope.selIdx = -1;
                $scope.detailedTotal = $scope.sum();
                $scope.selData = function (d, idx) {
                    $scope.selectedData = d;
                    $scope.selIdx = idx;
                }
                $scope.isSelData = function (d) {
                    return $scope.selectedData === d;
                }
            });
        }

        $scope.getrequestedInvestigation = function (item) {
            //console.log(item);
            $http.post('/api/requestedInvestigation',
                {
                    "start": item.start,
                    "end": item.end,
                    "facility_id": facility_id
                }).then(function (data) {
                $scope.investigation = data.data;

            });
        }

        $scope.getSkullInvestigation = function (item) {
            //console.log(item);
            $http.post('/api/skullInvestigation',
                {
                    "start": item.start,
                    "end": item.end,
                    "facility_id": facility_id
                }).then(function (data) {
                $scope.skull = data.data;

            });
        }
        $scope.getChestInvestigation = function (item) {
            //console.log(item);
            $http.post('/api/chestInvestigation',
                {
                    "start": item.start,
                    "end": item.end,
                    "facility_id": facility_id
                }).then(function (data) {
                $scope.chest = data.data;

            });
        }
        $scope.getAbdomenInvestigation = function (item) {
            //console.log(item);
            $http.post('/api/abdomenInvestigation',
                {
                    "start": item.start,
                    "end": item.end,
                    "facility_id": facility_id
                }).then(function (data) {
                $scope.abdomen = data.data;

            });
        }
        $scope.getSpineInvestigation = function (item) {
            //console.log(item);
            $http.post('/api/spineInvestigation',
                {
                    "start": item.start,
                    "end": item.end,
                    "facility_id": facility_id
                }).then(function (data) {
                $scope.spine = data.data;

            });
        }
        $scope.getPelvisInvestigation = function (item) {
            //console.log(item);
            $http.post('/api/pelvisInvestigation',
                {
                    "start": item.start,
                    "end": item.end,
                    "facility_id": facility_id
                }).then(function (data) {
                $scope.pelvis = data.data;

            });
        }
        $scope.getExtremitiesInvestigation = function (item) {
            //console.log(item);
            $http.post('/api/extremitiesInvestigation',
                {
                    "start": item.start,
                    "end": item.end,
                    "facility_id": facility_id
                }).then(function (data) {
                $scope.extremities = data.data;

            });
        }
        $scope.getHSGInvestigation = function (item) {
            //console.log(item);
            $http.post('/api/HSGInvestigation',
                {
                    "start": item.start,
                    "end": item.end,
                    "facility_id": facility_id
                }).then(function (data) {
                $scope.hsg = data.data;

            });
        }
        //Rejesta Report
        $scope.getRejestaReport = function () {

            $http.get('/api/getRejestaReport/' + facility_id).then(function (data) {
                $scope.Rejesta_Report = data.data;
            });
        }
        //Service Data
        $scope.getInvestigationData = function () {
            $http.get('/api/investigationData').then(function (data) {
                $scope.investigationData = data.data;
            });
        }
        $scope.deviceName = function () {
            $http.get('/api/deviceName/' + facility_id).then(function (data) {
                $scope.devicesdata = data.data;
            });
            $scope.pagination = [];
            $scope.device_services = function () {
                $http.get('/api/deviceServices/' + facility_id).then(function (data) {
                    $scope.ServedDevice = data.data;
                    $scope.pagination = data.data;
                    $scope.viewby = 10;
                    $scope.ServedDevices = $scope.pagination.length;
                    $scope.currentPage = 1;
                    $scope.itemsPerPage = $scope.viewby;
                    $scope.maxSize = 2; //Number of pager buttons to show

                    $scope.setPage = function (pageNo) {
                        $scope.currentPage = pageNo;
                    };
                    $scope.setItemsPerPage = function (num) {
                        $scope.itemsPerPage = num;
                        $scope.currentPage = 1;
                    }
                });
            }
        }
        //Device denied
        $scope.serviceDenied = function () {
            $http.get('/api/deniedDevices/' + facility_id).then(function (data) {
                $scope.deniedDevices = data.data;
            });
            $http.get('/api/getItemCategory').then(function (data) {
                $scope.category = data.data;
            });
            $http.get('/api/investigationData').then(function (data) {
                $scope.investigationData = data.data;
            });
        }

        $scope.serviceonnoff();
        $scope.departmentRegistration = function (department) {
            if (department == undefined) {
                swal(
                    'Department is missing',
                    'Register all required field!',
                    'error'
                )
            }
            else if (department.department_name.id == undefined) {
                swal(
                    'Department is missing',
                    'Register Department!',
                    'error'
                )
            } else if (department.name == undefined) {
                swal(
                    'Sub-Department is missing',
                    'Register sub dept!',
                    'error'
                )
            }
            else {
                var departmentData = {
                    'sub_department_name': department.name,
                    'department_id': department.department_name.id,
                    'eraser': 1
                };
                $http.post('/api/departmentRegistration',departmentData).then(function(data) {
                    console.log(data.data);
                    var msg=data.data.msg;
                    var status=data.data.status;
                    if(status==0){
                        swal(
                            'Error',
                            msg,
                            'error'
                        )
                    }
                    else{
                        swal(
                            'Success Registration',
                            msg,
                            'success'
                        )
                    }
                    $scope.departments_list();
                });
            }
        };
        $scope.statusRegistration = function (status) {
            var statusData = {
                'status_name': status.name,
                'eraser': 1
            };
            $http.post('/api/statusRegistration', statusData).then(function (data) {
                var msg = data.data.sent;
                var status = data.data.status;
                if (status == 0) {
                    toastr.error('Status Denied', 'Error');
                }
                else {
                    toastr.success('Successfully', 'Status Registered');
                }
                $scope.equipments_list();
            });
        };
        $scope.InvestigationRegistration = function (investigation) {
            var item_name = investigation.name;
            var InvestigationPart = {
                'item_name': item_name,
                'dept_id': 3
            };
            $http.post('/api/InvestigationRegistration', InvestigationPart).then(function (data) {
                var msg = data.data.msg;
                var status = data.data.status;
                if (status == 0) {
                    toastr.error(msg, 'Error');
                }
                else {
                    toastr.success(msg, 'Success');
                }
            });
        };
        $scope.InvestigationPart = function (investigation) {
            var item_name = investigation.item_name.id;
            var item = investigation.item_name.item_name;
            var sub_item_category = investigation.part.name;
            var item_category = investigation.category.item_category_name;
            var InvestigationType = {
                'item_id': item_name,
                'item_name': item,
                'item_category': item_category,
                'sub_item_category': sub_item_category
            };
            $http.post('/api/InvestigationPart', InvestigationType).then(function (data) {
                var msg = data.data.msg;
                var status = data.data.status;
                if (status == 0) {
                    toastr.error(msg, 'Error');
                }
                else {
                    toastr.success(msg, 'Success');
                }
            });
        };
        $scope.DeactivateUser = function (id) {
            var del_user = user_name;
            var deactiveUser = {
                'id': id,
                'del_user': del_user
            };
            $http.post('/api/DeactivateUser', deactiveUser).then(function (data) {
                toastr.error('Deleted Successfully', 'User');
            });
        };
        $scope.RadiologyUpdate = function (statusEquipments, status, descr, condition) {
            var radiologyData = {
                'statuses_id': status,
                'descriptions': descr,
                'equipment_name': statusEquipments.equipment_name,
                'description_id': statusEquipments.id,
                'condition': statusEquipments.condition,
                'conditions': condition,
                'status_id': statusEquipments.status_id,
                'description': statusEquipments.description,
                'equipment_status_id': statusEquipments.status_name.id,
                'facility_id': facility,
                'user_id': user_id
            };
            $http.post('/api/RadiologyUpdate', radiologyData).then(function (data) {
                toastr.success('Updated Successfully', 'Equipment');
                $scope.device_services();
            });
        };
        $scope.statusUpdate = function (equipment) {
            var updateData = {
                'status_name': equipment.status_name
            };
            $http.post('/api/statusUpdate', equipment).then(function (data) {
                toastr.success('Updated Successfully', 'Status');
                $scope.equipments_list();
            });
        };

        $scope.departmentUpdate = function (department) {
            var id = department.id;
            var sub_department_name = department.sub_department_name;
            var department_id = department.department_id;
            var departmentUpdate = {
                'sub_department_name': sub_department_name,
                'id': id,
                'department_id': department_id
            };
            $http.post('/api/departmentUpdate', departmentUpdate).then(function (data) {
                toastr.success('Successfully', 'Updated Successfully ');
                $scope.departments_list();
            });
        };
        $scope.states = [
            {
                "name": "Skull",
                "id": "1"
            },
            {
                "name": "Chest",
                "id": "2"
            }, {
                "name": "Abdomen",
                "id": "3",
            }, {
                "name": "Spine",
                "id": "4"
            }, {
                "name": "Extremities",
                "id": "5"
            }, {
                "name": "HSG",
                "id": "6"
            }, {
                "name": "Others Specify",
                "id": "7"
            }


        ];

        $scope.departmentDelete = function (id) {
            swal({
                title: 'Are you sure?',
                text: "You won't be able to revert this!",
                type: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Yes, delete it!'
            }).then(function () {

                $http.get('/api/departmentDelete/' + id).then(function (data) {
                    toastr.error('Deleted Successfully', 'Sub-department');
                    $scope.departments_list();
                });
            })
        };
        $scope.statusDelete = function (id) {
            swal({
                title: 'Are you sure?',
                text: "You won't be able to revert this!",
                type: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Yes, delete it!'
            }).then(function () {
                $http.get('/api/statusDelete/' + id).then(function (data) {
                    toastr.error('Deleted Successfuly', ' Status');
                    $scope.equipments_list();
                });
            })
        };
        var _selected;
        $scope.ngModelOptionsSelected = function (value) {
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
        $scope.alertMe = function () {
            setTimeout(function () {
                $window.swal(
                    'Use proper ways to avoid Mistakes',
                    username,
                    'error'
                );
            });
        };
        $scope.model = {
            name: 'Tabs'
        };
        $scope.oneAtATime = true;
        $scope.status = {
            isCustomHeaderOpen: false,
            isFirstOpen: true,
            isFirstDisabled: false
        };
        $scope.device_services = function () {
            $http.get('/api/deviceServices/' + facility_id).then(function (data) {
                $scope.ServedDevice = data.data;
                $scope.viewby = 5;
                $scope.ServedDevices = data.data.length;
                $scope.currentPage = 1;
                $scope.maxSize = 10; //Number of pager buttons to show
                $scope.setPage = function (pageNo) {
                    $scope.currentPage = pageNo;
                };
                $scope.setItemsPerPage = function (num) {
                    $scope.itemsPerPage = num;
                    $scope.currentPage = 1;
                }

            });
        }
        $scope.device_services();
        $scope.serviceRegistration = function (devicesdata) {
            var item_name = devicesdata.item_name.item_id;

            if (devicesdata.item_name.item_id == undefined) {
                swal(username,
                    'Service is missing',
                    'error'
                )
            }
            else if (devicesdata.sub_department_name == undefined) {
                swal(
                    'Sub-Department is missing',
                    'Register all required field!',
                    'error'
                )
            }
            else if (devicesdata.item_name == undefined) {
                swal(
                    'Service is missing',
                    'Register all required field!',
                    'error'
                )
            }
            else if (devicesdata.equipment_name == undefined) {
                swal(
                    username,
                    'Register equipment name!',
                    'error'
                )
            }
            else {
                var serviceData = {
                    'sub_department_id': devicesdata.sub_department_name.id,
                    'equipment_id': devicesdata.equipment_name.equip_id,
                    'item_id': item_name,
                    'eraser': 1

                }
                $http.post('/api/serviceRegistration', serviceData).then(function (data) {
                    var msg = data.data.msg;
                    var status = data.data.status;
                    if (status == 0) {
                        toastr.error(msg, ' Error');
                    }
                    else {
                        toastr.success(msg, ' Success Registration');
                    }

                    $scope.device_services();
                });
            }

        };
        $scope.userRegistration = function (userdepartment) {
            if (userdepartment.name == undefined) {
                swal(username,
                    'Please fill required fields',
                    'error'
                )
            }
            else if (userdepartment.name == undefined) {
                swal(
                    username,
                    'User is missing',
                    'error'
                )
            }
            else if (userdepartment.sub_department_name == undefined) {
                swal(
                    username,
                    'Sub-Department is missing',
                    'error'
                )
            }
            else {
                var userRegister = {
                    'user_id': userdepartment.name.user_id,
                    'subdept_id': userdepartment.sub_department_name.id,
                    'assigned_by': user_name,
                    'grant': 1
                }
                $http.post('/api/userRegistration', userRegister).then(function (data) {
                    var msg = data.data.msg;
                    var status = data.data.status;
                    if (status == 0) {
                        toastr.error(msg, ' Error');
                    }
                    else {
                        toastr.success(msg, ' Registered');
                        swal(
                            'Success Registration',
                            msg,
                            'success'
                        )
                    }
                    $scope.getUsersSubdepartments();

                });
            }
        };
        $scope.OpenDeviceModal = function (device) {
            var object = device;
            var modalInstance = $uibModal.open({
                templateUrl: '/views/modules/radiology/deviceModal.html',
                size: 'lg',
                animation: true,
                controller: 'deviceModal',
                resolve: {
                    object: function () {
                        return object;
                    }
                }
            });
        }
        $scope.VerifyXray = function (id) {
            $http.get('/api/VerifyXray/' + id).then(function (data) {
                swal(
                    'Updated Successfully',
                    'Image  Verified!',
                    'success'
                )
                $scope.getXrays();

            });
        };
        $scope.ServiceDelete = function (id) {
            swal({
                title: 'Are you sure?',
                text: "You won't be able to revert this!",
                type: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Yes, Update it!'
            }).then(function () {
                $http.get('/api/ServiceDelete/' + id).then(function (data) {
                    toastr.error('Out of Stock', 'Service');
                    $scope.device_services();
                });
            })
        };
    }

})();