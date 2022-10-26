(function() {
  'use strict';

  angular.module('authApp').directive('hamburger', [ function() {
    return {
      restrict: 'E',
      scope: false,
      replace: true,
      template: '<md-button ng-if="showHamburger()" ng-click="showLeft()" class="hamburger">'+
        '<ng-md-icon icon="menu" size="48"></ng-md-icon>'+
        '</md-button>'
    };
  }]);

  angular.module('authApp').directive('appHeader', [ function() {
    return {
      restrict: 'E',
      scope: false,
      replace: true,
      templateUrl: '/views/app-header.html'
    };
  }]);
})();