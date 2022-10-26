(function () {
    'use strict'
    angular.module('authApp')
        .factory('Department', ['$resource','$http', function($resource, $http) {
        return $resource('/api/departments/:id', {}, {
            update : { method: 'PUT', params: {id: '@id'}},
            getByFacility : function (facility_id) {
                return $http.get('/api/departments/by-facility/facility_id')
                    .then(function (response) {
                        return response;
                    })
            },

        })
    }]);
})();