(function() {
    'use strict';

    var app = angular.module('authApp');

    app.factory('Sample', ['$resource', function($resource) {
        return $resource('/api/sample_services/:id', {}, {
            update  : { method : 'PUT', params  : {id: '@id'}},
        });
    }]);
    app.factory('Verify', ['$resource', function($resource) {
        return $resource('/api/verify_services/:id', {}, {
            update  : { method : 'PUT', params  : {id: '@id'}},
        });
    }]);
 app.factory('Results', ['$resource', function($resource) {
        return $resource('/api/lab_results/:id', {}, {
            update  : { method : 'PUT', params  : {id: '@id'}},
        });
    }]);


})();