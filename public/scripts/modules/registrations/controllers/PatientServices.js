(function() {
  'use strict';

  var app = angular.module('authApp');

  app.factory('Patient', ['$resource', function($resource) {
    return $resource('gothomis/api/patient/:id', {}, {
      update  : { method : 'PUT', params  : {id: '@id'}},
    });
  }]);
})();