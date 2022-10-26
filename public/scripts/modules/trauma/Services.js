(function() {
    'use strict';

    var app = angular.module('authApp');
        app.factory('TraumaServices', ['$resource','$http','$mdToast', function($resource,$http,$mdToast) {
        return {
                getTraumaList : function (searchKey = undefined) {
                    return $http.post('/api/get-trauma-list', {searchKey:searchKey})
                        .then(function (response) {
                            return response;
                        });
                },
                getAllPatients : function (item) {
                    return $http.post('/api/surgical-all',item)
                        .then(function (response) {
                            return response;
                        });
                },
                getTraumaConcepts : function () {
                    return $http.post('/api/trauma-concepts')
                        .then(function (response) {
                            return response;
                        });
                },
                getTriageCategories : function () {
                    return $http.post('/api/triage-categories')
                        .then(function (response) {
                            return response;
                        });
                },
            }
    }]);
})();