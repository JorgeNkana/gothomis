(function() {
    'use strict';

    var app = angular.module('authApp')

    app.factory('Search', ['$http', function($http) {
        return {
            getLabPatients : function (text) {
                return $http.post('/api/search-tribes',{
                    "name": text,
                })
                    .then(function (response) {
                        return response;
                    });
            },
        };
    }]);

})();