(function() {
    'use strict';

    var app = angular.module('authApp');

    app.factory('Tribe', ['$resource', function($resource) {
        return $resource('/api/client_tribe/:id', {}, {
            update  : { method : 'PUT', params  : {id: '@id'}},
        });
    }]);
    app.factory('Reception', ['$resource', function($resource) {
        return $resource('/api/client_reception/:id', {}, {
            update  : { method : 'PUT', params  : {id: '@id'}},
        });
    }]);



})();