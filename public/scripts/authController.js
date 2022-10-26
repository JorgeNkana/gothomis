(function() {

  'use strict';

  angular
    .module('authApp')
    .controller('AuthController', AuthController);

  function AuthController($auth, $state, $http, $rootScope,$scope,toastr,$mdToast,$mdDialog,$mdBottomSheet) {
	    angular.element(document).ready(function () {
			
			$http.post('/api/installSystem').then(function(data) {
						    if(data.data.status ==1){
                        $mdDialog.show({
                     controller: function ($scope) {
                        	
                       
                      
                         $scope.cancel = function () {
                             
                            $mdDialog.hide();
                        }; 
						
						$scope.migrateDb = function () {
							 $scope.dataLoading = true;
                        $http.post('/api/createSchema').then(function(response) {
							 if(response.data.status ==1){
								  $mdToast.show(
              $mdToast.simple()
                  .textContent('CONGRATULATION,NOW LOGIN.')
                  .hideDelay(5000)
          );
								 $scope.cancel();
								 
							 }else if(response.data.status ==0){
								 
								  $mdToast.show(
              $mdToast.simple()
                  .textContent(response.data.data)
                  .hideDelay(5000)
          );
							 }
							 
							 
						},
                function (data) {
                    // Handle error here
                    toastr.error('', 'Failed to make setup');
                }).finally(function () {
                $scope.dataLoading = false;
            });
						
						
						
                        };
                    },
                    templateUrl: '/views/modules/admin/installerModal.html',
                    parent: angular.element(document.body),
                    clickOutsideToClose: false,
                    fullscreen: $scope.customFullscreen // Only for -xs, -sm breakpoints.
                });
							}
				
			});
                          });
	  

    var vm = this;

    vm.loginError = false;

    vm.logout = function() {
        //console.log("logging out")
    }

    vm.login = function() {
        vm.Loads = true;

      var credentials = {
        email: vm.email,
        password: vm.password
      };

      $auth.login(credentials).then(function() {
         vm.Loads = true;

        // Return an $http request for the now authenticated
        // user so that we can flatten the promise chain
        return $http.get('/api/authenticate/user');

        // Handle errors
      }, function(error) {
        vm.loginError = true;
        $scope.error=error.data.error;
        vm.loginErrorText =error.data.error;
        toastr.error('',vm.loginErrorText);

        // Because we returned the $http.get request in the $auth.login
        // promise, we can chain the next promise to the end here
      }).then(function(response) {

	  if(angular.isDefined(response)==false){
		  return;
	  }
	  
        // Stringify the returned data to prepare it
        // to go into local storage
        var user = JSON.stringify(response.data.user);

        // Set the stringified user data into local storage
        localStorage.setItem('user', user);

        // The user's authenticated state gets flipped to
        // true so we can now show parts of the UI that rely
        // on the user being logged in
        $rootScope.authenticated = true;

        // Putting the user's data on $rootScope allows
        // us to access it anywhere across the app
        $rootScope.currentUser = response.data.user;

        var login_name=$rootScope.currentUser.name;
          $mdToast.show(
              $mdToast.simple()
                  .textContent('WELCOME  '+login_name)
                  .hideDelay(5000)
          );

        // Everything worked out so we can now redirect to
        // the users state to view the data
        $state.go('dashboard');
      });
    };
  }
})();