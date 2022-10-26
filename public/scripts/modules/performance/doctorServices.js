(function() {
    'use strict';

    var app = angular.module('authApp');

    app.factory('Performance', ['$resource', function($resource) {
        return $resource('/api/doctor_performance/:id', {}, {
            update  : { method : 'PUT', params  : {id: '@id'}},
        });
    }]);



})();