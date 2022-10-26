(function() {
    'use strict';

    angular.module('authApp').controller('AppController', AppController);
    AppController.$inject = ['$scope', '$rootScope', '$mdSidenav','$mdDialog', '$mdMedia', '$filter', '$window', '$http'];

    function AppController($scope, $rootScope, $mdSidenav, $mdDialog, $mdMedia, $filter, $window, $http) {

        $scope.loading = false;
        $scope.leftSidebar = true;
        $scope.showSearch = false;
        $scope.hideToast = false;
        $scope.topDirections = ['left', 'up'];
        $scope.bottomDirections = ['down', 'right'];
        $scope.isOpen = false;
        $scope.availableModes = ['md-fling', 'md-scale'];
        $scope.selectedMode = 'md-fling';
        $scope.selectedDirection = 'up';

        $scope.toggleSidenav = function(menuId) {
            $mdSidenav(menuId).toggle();
        };

        $scope.change_password_dialog = function(ev) {
            $mdDialog.show({
                    controller: DialogController,
                    templateUrl: '/views/change_password.html',
                    parent: angular.element(document.body),
                    targetEvent: ev,
                    clickOutsideToClose: true,
                    fullscreen: $scope.customFullscreen // Only for -xs, -sm breakpoints.
                })
                .then(function(answer) {

                }, function() {

                });
        }

        function DialogController($scope, $mdDialog) {
            $scope.hide = function() {
                $mdDialog.hide();
            };

            $scope.cancel = function() {
                $mdDialog.cancel();
            };

            $scope.answer = function(answer) {
                $mdDialog.hide(answer);
            };
            $scope.change_password = function (item) {
            var user_data = {user:$rootScope.currentUser,details:item};
             $http.post('/api/reset_password',user_data).then(function (data) {
                if(data.data.status == 1){
                    swal(data.data.data, "", "success");
                }
                else
                {
                    swal(data.data.data, "", "error");
                }
             });
            }
        }

        $scope.hide_left_bar = function() {
            $mdSidenav('left').toggle();
        };

    }
})();