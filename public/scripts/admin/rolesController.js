/**
 * Created by USER on 2017-02-13.
 */

(function() {
    'use strict';
    angular
        .module('authApp').directive('ngFiles', ['$parse', function($parse) {

            function fn_link(scope, element, attrs) {
                var onChange = $parse(attrs.ngFiles);
                element.on('change', function(event) {
                    onChange(scope, {
                        $files: event.target.files
                    });
                });
            };

            return {
                link: fn_link
            }
        }]).controller('rolesController', rolesController);

    function rolesController($http, $auth, $rootScope, $state, $location, $scope, toastr) {

        var user = $rootScope.currentUser;
        var user_name = $rootScope.currentUser.id;
        var facility_id = $rootScope.currentUser.facility_id;
		
        $http.get('/api/getRoles').then(function(data) {
            $scope.roles = data.data;

        });

        $http.get('/api/user_list/'+facility_id).then(function(data) {
            $scope.users = data.data;
        });

		$http.get('/api/getPerm').then(function (data) {
                $scope.permission = data.data;
                //console.log($scope.perms);

            });
		
		$scope.chooseUser = function (user) {

            $scope.selectedUser = user;

            $scope.getAssignedPerms(user);

        }
		
		$scope.getFacilities = function () {
            $http.get('/api/facility_list').then(function (data) {
                $scope.facilities = data.data;
            });
        };
		
		$scope.getProffesions = function () {
            $http.get('/api/professional_registration').then(function (data) {
                $scope.professsionals = data.data;
            });
        }
		
		
        $scope.user_registration = function (user) {
            $http.post('/api/user_registration', user).then(function (data) {				
				if (data.data.status == 0) {
                    sweetAlert(data.data.data, "", "error");
                } else {
                    sweetAlert(data.data.data, "", "success");
                }
                $scope.user_list();
            });
        }
		
        $scope.user_list = function () {
            $http.get('/api/user_list/'+facility_id).then(function (data) {
                $scope.users = data.data;
            });
        }
		
        $scope.checkUserPerms = function (permUserVal, item, permUsers) {

            if (permUserVal == true) {

                if (angular.isDefined(permUsers) == false) {
                    toastr.error('PLEASE SELECT THE USER ,FROM SEARCH BOX ABOVE BEFORE PROCEED', '');

                } else {
                    var permission_id = item.id;
                    var user_id = permUsers.id;
                    //console.log(user_id);
                    var perm_users = {'permission_id': permission_id, 'user_id': user_id, 'grant': 1};

                    $http.get('/api/getPermName/' + permission_id).then(function (data) {
                        $scope.perms = data.data;
                        var perm_name = 'Permission ' + $scope.perms + ' was selected and SAVED in the SYSTEM';

                        $http.post('/api/perm_user', perm_users).then(function (data) {
                            var getstatus = data.status;
                            var getdata = data.data.data;


                            if (data.data.status == 0) {
                                toastr.error(getdata, '');
                            } else {
                                $scope.getAssignedPerms(permUsers);
                                toastr.success(getdata, '');
                            }


                        });

                    });


                }
            }
        }
		
		
        $scope.getAssignedPerms = function (permUsers) {

            var selectedUserId = permUsers.id;
            $http.get('/api/getAssignedMenu/' + selectedUserId).then(function (data) {
                $scope.savedPerms = data.data;
            });

        }
    }
})();